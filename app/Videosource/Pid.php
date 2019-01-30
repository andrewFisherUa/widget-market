<?php
namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Pid extends Model
{
    private static $instance=null;
	private $sources=[];
	private $days=[];
	private $money=[];
	private $pids=[];
	private $blocks=[];
    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	//self::$instance->prepareData();
	}
	return self::$instance;
	
	}
  public function getData(&$arr){
   
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$day=date("Y-m-d",$time);
			if (!isset($arr[5])){
				var_dump($arr);
				return;
			}
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			parse_str($req[1], $dd);
	          
		if(!$dd || !isset($dd["data"])){
		    return;
	    }	  
        $data=json_decode($dd["data"],true);
		if(!$data){
		    return;
	    }
		if(!isset($data["id_src"])){
		    return;
	    }
		if(!isset($data["block"])){
		    return;
	    }
		if(!isset($data["event"])){
		    return;
	    }	
		if(!isset($data["pid"])){
		    return;
	    }	
		if(!isset($data["page_key"])){
		    return;
	    }		
		if(isset($data["is_mobile"])){
		    $is_mobile=$data["is_mobile"];
			//echo $is_mobile." ----- \n";
	    }	
		else{
		 $is_mobile=0;
		}
		$page_key=$data["page_key"];
		
		$country=trim($arr[2]);
		if($country!='RU')
		$country='CIS';
        $event=$data["event"];		
		$block=$data["block"];
        $id_src=$data["id_src"];	
		$pid=$data["pid"];	
		if($pid==4) $pid=300;
        if($pid==6) $pid=701;
		$pseudo="";
		switch($event){
		case "AdVideoFirstQuartile":
    	$pseudo="played";
		break;
        case "AdVideoComplete":
		$eve="completed";
		break;
		case "AdStarted":
		$pseudo="starts";
		break;
		case "AdVideoStart":
		$pseudo="videostarts";
		break;
		case "click":
		case "AdClickThru":
		$pseudo="click";
		break;
		case "request":
		$pseudo="request";
		break;
        default:
		//echo $event."   ->  ";
		break;


		}
		if(!$pseudo) return;
		//echo $page_key."\n";
		

		
		if(!isset($this->blocks[$day])){
		$this->blocks[$day]=[];
		}
		if(!isset($this->blocks[$day][$block])){
		$this->blocks[$day][$block]=[];
		}
		if(!isset($this->blocks[$day][$block][$page_key])){
		$this->blocks[$day][$block][$page_key]=[];
		}
		if(!isset($this->blocks[$day][$block][$page_key][$pseudo])){
		$this->blocks[$day][$block][$page_key][$pseudo]=1;
		}		
		else{
		$this->blocks[$day][$block][$page_key][$pseudo]++;
		}
		
		if(!isset($this->days[$day])){
		$this->days[$day]=[];
		}
		if(!isset($this->days[$day][$pid])){
		$this->days[$day][$pid]=[];
		}
		if(!isset($this->days[$day][$pid][$id_src])){
		$this->days[$day][$pid][$id_src]=[];
		}
		if(!isset($this->days[$day][$pid][$id_src][$pseudo])){
		$this->days[$day][$pid][$id_src][$pseudo]=1;
		}else{
		$this->days[$day][$pid][$id_src][$pseudo]++;
		}
		
		}
		
	public function RegisterData(){
	
	    $pdo = \DB::connection("videotest")->getPdo();
		$sql="create temp table pid_pad_temp as (select * from pid_pad limit 1);";
		$pdo->exec($sql);
		$sql="truncate table pid_pad_temp;";
		$pdo->exec($sql);
		$sql="
		insert into pid_pad_temp(day,pid,id_src,requested,started,played,completed,clicked)
		select ?,?,?,?,?,?,?,?";
		$sthInsertPids=$pdo->prepare($sql);
		/*$sql="
		insert into pid_pad(day,pid,id_src,requested,started,played,completed,clicked)
		select ?,?,?,?,?,?,?,?
	    WHERE NOT EXISTS (SELECT 1 FROM pid_pad WHERE day =? and  pid=? and id_src=?)
		";
	   $sthInsertPids=$pdo->prepare($sql);
	   $sql="
		update  pid_pad
		set 
									requested=requested+?
									,started=started+?
									,played=played+?
									,completed=completed+?
									,clicked=clicked+?
		 WHERE day =? and  pid=? and id_src=?
		";
	   	$sthUpdatePids=$pdo->prepare($sql);*/
		foreach($this->days as $day=>$pids){
		    foreach($pids as $pid=>$sources){
		        foreach($sources as $id_src=>$events){
												$requested=0;
												if(isset($events["request"]))
												$requested=$events["request"];
												$started=0;
												if(isset($events["starts"]))
												$started=$events["starts"];
												$played=0;
												if(isset($events["played"]))
												$played=$events["played"];
												$completed=0;
												if(isset($events["completed"]))
												$completed=$events["completed"];
												$clicked=0;
												if(isset($events["click"]))
												$clicked=$events["click"];				
					//$sthUpdatePids->execute([$requested,$started,$played,$completed,$clicked,$day,$pid,$id_src]);
                    $sthInsertPids->execute([$day,$pid,$id_src,$requested,$started,$played,$completed,$clicked]);            
  			    } 
		   }
		}
		$sql="update pid_pad as t1 set 
			requested=t1.requested+t2.requested
			,started=t1.started+t2.started
			,played=t1.played+t2.played
			,completed=t1.completed+t2.completed
			,clicked=t1.clicked+t2.clicked
			from pid_pad_temp as t2 where t1.day=t2.day and  t1.pid=t2.pid and t1.id_src=t2.id_src";
		$pdo->exec($sql);
		$sql="insert into pid_pad(
			day,pid,id_src,requested,started,played,completed,clicked)
			select t1.day,t1.pid,t1.id_src,t1.requested,t1.started,t1.played,t1.completed,t1.clicked
			from pid_pad_temp as t1 left join 
			pid_pad as t2 on t1.day=t2.day and t1.pid=t2.pid and t1.id_src=t2.id_src where t2.id_src is null";	
		$pdo->exec($sql);
		$sql="drop table pid_pad_temp";
		$pdo->exec($sql);
		//	$this->blocks[$day][$page_key][$pseudo]++;
		
		$sql="create temp table stat_block_pages_temp as (select * from stat_block_pages limit 1);";
		$pdo->exec($sql);
		$sql="truncate table stat_block_pages_temp;";
		$pdo->exec($sql);
		$sql="
		insert into stat_block_pages_temp(day,id_block,page_key,played)
		select ?,?,?,?";
	   $sthInsertPids=$pdo->prepare($sql);
		/*$sql="
		insert into stat_block_pages(day,id_block,page_key,played)
		select ?,?,?,?
	    WHERE NOT EXISTS (SELECT 1 FROM stat_block_pages WHERE day =? and  id_block=? and page_key=?)
		";
	   $sthInsertPids=$pdo->prepare($sql);
		$sql="
		update   stat_block_pages set played=played+?
		
	  WHERE day =? and  id_block=? and page_key=?
		";
	 $sthUpdatePids=$pdo->prepare($sql);*/
		
	 foreach($this->blocks as $day=>$blocks)	{
	  foreach($blocks as $block=>$pages)	{
	  foreach($pages as $page=>$events)	{
	   //foreach($pages as $page=>$events)	{
	   $played=0;
	   if(isset($events["played"]))
	    $played=$events["played"];
		
		//echo "$day,$block,$page \n";
		//$sthUpdatePids->execute([$played,$day,$block,$page]);  
	    $sthInsertPids->execute([$day,$block,$page,$played]);            
	  //}
	 }
	 }
	 }
	 $sql="update stat_block_pages as t1 set 
			played=t1.played+t2.played
			from stat_block_pages_temp as t2 where t1.day=t2.day and t1.id_block=t2.id_block and t1.page_key=t2.page_key";
		$pdo->exec($sql);
		$sql="insert into stat_block_pages(
			day,id_block,page_key,played)
			select t1.day,t1.id_block,t1.page_key,t1.played
			from stat_block_pages_temp as t1 left join 
			stat_block_pages as t2 on t1.day=t2.day and t1.id_block=t2.id_block and t1.page_key=t2.page_key where t2.id_block is null";	
		$pdo->exec($sql);
		$sql="drop table stat_block_pages_temp";
		$pdo->exec($sql);
	 $myhour=preg_replace('/^0/','',date("H"));
		if($myhour==9){
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete  from stat_block_pages where day <'$myday'";
		$pdo->exec($sql);
		print " deleted stat_block untill $myday !!!!\n";
		}
  return;
  /*
		$sql="insert into stat_pages (day,pid,page_key,country,cnt)
		select ?,?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM stat_pages WHERE day=? and pid=? and page_key =? and country =?)
		";
		$sthInsertPids=$pdo->prepare($sql);
			$sql="update stat_pages 
		set cnt=cnt+?
		WHERE day=? and pid=? and page_key =? and country =?
		";
		$sthUpdatePids=$pdo->prepare($sql);
		//echo "\n\n\n\n";
		foreach($this->days as $day=>$pages){
		    foreach($pages as $page=>$pids){
		        foreach($pids as $pid=>$countries){
				    foreach($countries as $country=>$cnt){
		               //echo $day." : ".$page." : $pid  $country  $cnt\n";
					   $sthUpdatePids->execute([$cnt,$day,$pid,$page,$country]);
					   $sthInsertPids->execute([$day,$pid,$page,$country,$cnt,$day,$pid,$page,$country]);
					}
		        }
		    }
		}

		
	$sql="update  stat_pages set pid  = 5
    where pid=10";
	$pdo->exec($sql);
	*/
	}
	public function prepareData(){
	    $comission = \App\WidgetVideo::All();
		foreach($comission as $pid){
		$this->pids[$pid->id]=$pid;
		
			//echo $this->pids[$pid->id]->commission_rus;	
		}
	}	
}