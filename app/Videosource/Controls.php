<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Controls extends Model
{

    //
 private static $instance=null;
	private $sources=[];
	private $days=[];
	private $pages=[];
	private $money=[];
	private $pids=[];
	private $attribs=[];
	private $graphs=[];
	private $loaded=[];
        private $dopDay;
    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	self::$instance->prepareData();
	}
	return self::$instance;
	
	}
  public function getData(&$arr){
   
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$constroltime=$time-($time%180);
			$day=date("Y-m-d",$time);
                        if(!$this->dopDay) $this->dopDay=$day;
			$constroldate=date("Y-m-d H:i:s",$constroltime);
			if (!isset($arr[5])){
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

		   if(!isset($data["pid"])){
		    return;
	       }
		   if(!isset($data["id_src"])){
		    return;
	       }
		   /*if(!isset($data["control"])){
		    return;
	       }*/
		   if(!isset( $data["block"])){
		    var_dump($data);
		    return;
	       }
		  if(!isset( $data["page_key"])){
		    return;
	       } 
		    $block=$data["block"];
		if(isset($data["is_mobile"])){
		    $is_mobile=$data["is_mobile"];
			
	    }	
		else{
		 $is_mobile=0;
		}
		if(!$is_mobile)
 $is_mobile=0;
 else $is_mobile=1;
		$page_key=$data["page_key"];
		  //$pseudo="";
		  $control=0;
		   if(!isset($data["control"])) {
			   print_r($data);
		   }else{
		    $control=$data["control"];
			//if($control)
		    //echo $control." ----- \n";  
		   }
		     $country=trim($arr[2]);
		   
		   if($country!='RU')
		   $country='CIS';
		    $event=$data["event"];		
			/*if ($event=="AdVideoFirstQuartile"){
				var_dump($data);
			}*/
		    $block=$data["block"];
            $id_src=$data["id_src"];	
		    $pid=$data["pid"];	
			if($pid==4) $pid=300;
            if($pid==6) $pid=701;
			$pseudo="";
			$eve="";
		    switch($event){
		    case "AdVideoFirstQuartile":
    	    $pseudo="played";
			$eve="played";
		    break;
            case "AdVideoComplete":
		$eve="completed";
		break;
		
		case "AdStarted":
		$eve="starts";
		break;
		case "AdVideoStart":
		$eve="videostarts";
		break;
		case "click":
		case "AdClickThru":
		$eve="click";
		break;
		case "request":
		$eve="request";
		break;
		#case "AdViewable":
		#$pseudo="viewed";
		#break;
		case "loadWidget":
		//var_dump(["привет из интернета"]);
		if(!isset($this->loaded[$constroldate])){
		$this->loaded[$constroldate]=1;
		}	 
		else{
		$this->loaded[$constroldate]++;
		}
		
		
		
		break;
        default:
		//echo $event."   ->  ";
		break;
		
		     }
			
		
			 if($eve){
		if(!isset($this->pages[$day])){
		$this->pages[$day]=[];
		}
	    if(!isset($this->pages[$day][$country])){
		$this->pages[$day][$country]=[];
		}
		if(!isset($this->pages[$day][$country][$is_mobile])){
		$this->pages[$day][$country][$is_mobile]=[];
		}
		if(!isset($this->pages[$day][$country][$is_mobile])){
		$this->pages[$day][$country][$is_mobile]=[];
		}	
		if(!isset($this->pages[$day][$country][$is_mobile][$id_src])){
		$this->pages[$day][$country][$is_mobile][$id_src]=[];
		}	
	    if(!isset($this->pages[$day][$country][$is_mobile][$id_src][$eve])){
		$this->pages[$day][$country][$is_mobile][$id_src][$eve]=1;
		}else{
		$this->pages[$day][$country][$is_mobile][$id_src][$eve]++;
		}			
	   // echo $eve." $id_src\n";
		}	 
		if(!$pseudo) return;
	
		
		if(!isset($this->graphs[$constroldate])){
		$this->graphs[$constroldate]=[];
		}
		if(!isset($this->graphs[$constroldate][$id_src])){
		$this->graphs[$constroldate][$id_src]=1;
		}else{
		$this->graphs[$constroldate][$id_src]++;
		}
          //$pid=	$data["pid"]	;   
		  //print $pid." $id_src  $country ".$data["control"]."\n";
		   //var_dump($data);
		   $this->attribs[$page_key]=["datetime"=>$datetime];


		
		if(!isset($this->days[$day])){
		$this->days[$day]=[];
		}
		
		
		if(!isset($this->days[$day][$pid])){
		$this->days[$day][$pid]=[];
		}
        if(!isset($this->days[$day][$pid][$country])){
		$this->days[$day][$pid][$country]=[];
		}
			        if(!isset($this->days[$day][$pid][$country][$control][$is_mobile])){
		$this->days[$day][$pid][$country][$control][$is_mobile]=[];
		}	
				
        if(!isset($this->days[$day][$pid][$country][$control][$is_mobile][$id_src])){
		$this->days[$day][$pid][$country][$control][$is_mobile][$id_src]=1;
		}else{
		$this->days[$day][$pid][$country][$control][$is_mobile][$id_src]++;
		}
		
 
		  
	}
public function RegisterData(){
	
	    $pdo = \DB::connection("videotest")->getPdo();
		$sql="create temp table stat_control_temp as (select * from stat_control limit 1);";
		$pdo->exec($sql);
		$sql="truncate table stat_control_temp;";
		$pdo->exec($sql);
		$sql="insert into stat_control_temp (day,pid,id_src,country,control,cnt,mobile)
		select ?,?,?,?,?,?,?";
		$sthInsertPids=$pdo->prepare($sql);
		/*$sql="insert into stat_control (day,pid,id_src,country,control,cnt,mobile)
		select ?,?,?,?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM stat_control WHERE day=? and pid=? and id_src =? and country =? and control=? and mobile=?)
		";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update  stat_control 
		set cnt=cnt+?
		WHERE day=? and pid=? and id_src =? and country =? and control=? and mobile=?
		";
	
		$sthUpdatePids=$pdo->prepare($sql);*/
		foreach($this->days as $day=>$pids){
		    foreach($pids as $pid=>$countries){
			    foreach($countries as $country=>$controls){
					 foreach($controls as $control=>$mobiles){
						foreach ($mobiles as $mobile=>$sources){
							foreach($sources as $id_src=>$cnt){
				        //echo $day."  $pid  $country  $id_src $cnt\n";
						
								//$sthUpdatePids->execute([$cnt,$day,$pid,$id_src,$country,$control,$mobile]);
								$sthInsertPids->execute([$day,$pid,$id_src,$country,$control,$cnt,$mobile]);
							}
						}
					 }
			    }
			}
		}
		$sql="update stat_control as t1 set 
			cnt=t1.cnt+t2.cnt
			from stat_control_temp as t2 where t1.day=t2.day and t1.pid=t2.pid and t1.id_src=t2.id_src and t1.country=t2.country and t1.control=t2.control 
			and t1.mobile=t2.mobile";
		$pdo->exec($sql);
		$sql="insert into stat_control(
			day
			,pid
			,id_src
			,country
			,control
			,cnt
			,mobile)
			select t1.day,t1.pid,t1.id_src,t1.country,t1.control,t1.cnt,t1.mobile 
			from stat_control_temp as t1 left join 
			stat_control as t2 on t1.day=t2.day and t1.pid=t2.pid and t1.id_src=t2.id_src and t1.country=t2.country and t1.control=t2.control 
			and t1.mobile=t2.mobile where t2.id_src is null";	
		$pdo->exec($sql);
		$sql="drop table stat_control_temp";
		$pdo->exec($sql);
		
									$sql="update  stat_control set pid  = 6
                                    where pid=11";
	                                $pdo->exec($sql);
		$sql="create temp table stat_src_pages_temp as (select * from stat_src_pages limit 1);";
		$pdo->exec($sql);
		$sql="truncate table stat_src_pages_temp;";
		$pdo->exec($sql);
		$sql="insert into stat_src_pages_temp (day,id_src,country,mobile,requested,started,played,completed,clicked) 
			select ?,?,?,?,?,?,?,?,?";				
									$sthInsertPids=$pdo->prepare($sql);
					                /*$sql="insert into stat_src_pages (day,id_src,country,mobile,requested,started,played,completed,clicked)  
									select ?,?,?,?,?,?,?,?,?
									WHERE NOT EXISTS (SELECT 1 FROM stat_src_pages WHERE day=? and id_src =? and country =? and mobile =?)
									";				
									$sthInsertPids=$pdo->prepare($sql);
									$sql="update stat_src_pages set 
									requested=requested+?
									,started=started+?
									,played=played+?
									,completed=completed+?
									,clicked=clicked+?
									WHERE day=? and id_src =? and country =? and mobile =?
									";
									$sthUpdatePids=$pdo->prepare($sql);*/
									foreach($this->pages as $day=>$countries){
									    foreach($countries as $country=>$moibils){
										    foreach($moibils as $mobile=>$surces){
											     foreach($surces as $id_src=>$events){
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
												// $sthUpdatePids->execute([$requested,$started,$played,$completed,$clicked,$day,$id_src,$country,$mobile]);
												 $sthInsertPids->execute([$day,$id_src,$country,$mobile,$requested,$started,$played,$completed,$clicked]);
												// echo "$day --  $country -- $mobile -- $id_src \n";
												 }
											
											}
										}

		}
		$sql="update stat_src_pages as t1 set 
			requested=t1.requested+t2.requested
			,started=t1.started+t2.started
			,played=t1.played+t2.played
			,completed=t1.completed+t2.completed
			,clicked=t1.clicked+t2.clicked
			from stat_src_pages_temp as t2 where t1.day=t2.day and t1.id_src=t2.id_src and t1.country=t2.country and t1.mobile=t2.mobile";
		$pdo->exec($sql);
		$sql="insert into stat_src_pages(
			day
			,id_src
			,country
			,mobile
			,requested
			,started
			,played
			,completed
			,clicked)
			select t1.day,t1.id_src,t1.country,t1.mobile,t1.requested,t1.started,t1.played,t1.completed,t1.clicked
			from stat_src_pages_temp as t1 left join 
			stat_src_pages as t2 on t1.day=t2.day and t1.id_src=t2.id_src and t1.country=t2.country and t1.mobile=t2.mobile where t2.id_src is null";	
		$pdo->exec($sql);
		$sql="drop table stat_src_pages_temp";
		$pdo->exec($sql);
		
		
		$sql="create temp table videostatistic_graph_temp as (select * from videostatistic_graph limit 1);";
		$pdo->exec($sql);
		$sql="truncate table videostatistic_graph_temp;";
		$pdo->exec($sql);
		$sql="insert into videostatistic_graph_temp (datetime,id_src,cnt)
		select ?,?,?";
		$sthInsertPids=$pdo->prepare($sql);
		 // $pdos = \DB::connection("pgstatistic")->getPdo();
		
		/*$sql="insert into videostatistic_graph (datetime,id_src,cnt)
		select ?,?,? 
		WHERE NOT EXISTS (SELECT 1 FROM videostatistic_graph WHERE datetime =? and id_src=?)
		";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update videostatistic_graph set cnt=cnt+?
		 WHERE datetime =? and id_src=?
		";	
		$sthUpdatePids=$pdo->prepare($sql);*/
		
		foreach($this->graphs as $date=>$srcs){
		foreach($srcs as $id_src =>$cnt){
		    //print $date." : ".$id_src." : ".$cnt."\n";
			//$sthUpdatePids->execute([$cnt,$date,$id_src]);
			$sthInsertPids->execute([$date,$id_src,$cnt]);
			}
		}




		foreach($this->loaded as $date=>$cnt ){
		$id_src=0;
		
		print $date." : ".$id_src." : ".$cnt."\n";
		    //$sthUpdatePids->execute([$cnt,$date,$id_src]);
			$sthInsertPids->execute([$date,$id_src,$cnt]);
		//$sthUpdatePids->execute([$cnt,$date,$id_src]);
		//$sthInsertPids->execute([$date,$id_src,$cnt,$date,$id_src]);
		}
/* новая статистика*/
// print "start member controbank\n";
// $dadata=\DB::connection("video_")->select($sql);
// foreach($dadata as $dada){
//     $sth->execute([
//     $dada->datetime,
//     $dada->id_src,
//     $dada->cnt
//     ]);






		$sql="  update videostatistic_multy_graph as t1 set 
			cnt=t1.cnt+t2.cnt
			from videostatistic_graph_temp as t2 where t1.datetime=t2.datetime and t1.id_src=t2.id_src";
		$pdo->exec($sql);
		$sql="  insert into videostatistic_multy_graph(
			datetime
			,id_src
			,cnt)
			select t1.datetime,t1.id_src,t1.cnt
			from videostatistic_graph_temp as t1 left join 
			videostatistic_multy_graph as t2 on t1.datetime=t2.datetime and t1.id_src=t2.id_src where t2.id_src is null";	
		$pdo->exec($sql);
		$sql="drop table videostatistic_graph_temp";
		$pdo->exec($sql);
/* новая статистика*/

		$sql="
    CREATE TEMP TABLE _graphic_items_tmp (
    datetime timestamp without time zone,
    id_src integer,
    cnt integer DEFAULT 0
    );
   ";
   \DB::connection("videotest")->getPdo()->exec($sql);
$sql="                                          
    insert into _graphic_items_tmp (
    datetime,
    id_src,
    cnt
    ) 
     values(?
	,?
	,?) 
";
$sth=\DB::connection("videotest")->getPdo()->prepare($sql);	;
$sql="select * from _graphic_items_
where datetime >='".$this->dopDay." 00:00:00' 
";
 print $sql."\n";
 $dadata=\DB::connection("video_")->select($sql);
 foreach($dadata as $dada){
     $sth->execute([
     $dada->datetime,
     $dada->id_src,
     $dada->cnt
     ]);
}
$sql="select datetime,id_src,count(*) as ts ,sum(cnt) as cnt from (
      select datetime,id_src,cnt from videostatistic_multy_graph where datetime >='".$this->dopDay." 00:00:00' 
      union all
      select datetime,id_src,cnt from _graphic_items_tmp  where datetime >='".$this->dopDay." 00:00:00' 
      ) t 
      group by datetime,id_src order by ts
 ";
 $dats=\DB::connection("videotest")->select($sql);
# var_dump($dats);
	$sql="  update videostatistic_graph as t1 set 
			cnt=t2.cnt
			from (select datetime,id_src,sum(cnt) as cnt from (
      select datetime,id_src,cnt from videostatistic_multy_graph where datetime >='".$this->dopDay." 00:00:00' 
      union all
      select datetime,id_src,cnt from _graphic_items_tmp  where datetime >='".$this->dopDay." 00:00:00' 
      ) t 
      group by datetime,id_src) t2 where t1.datetime=t2.datetime and t1.id_src=t2.id_src
      ";
      $pdo->exec($sql);

		$sql="  insert into videostatistic_graph(
			datetime
			,id_src
			,cnt)
			select 
                        t1.datetime,t1.id_src,t1.cnt
			from (
select datetime,id_src,sum(cnt) as cnt from (
      select datetime,id_src,cnt from videostatistic_multy_graph where datetime >='".$this->dopDay." 00:00:00' 
      union all
      select datetime,id_src,cnt from _graphic_items_tmp  where datetime >='".$this->dopDay." 00:00:00' 
      ) t 
      group by datetime,id_src
                        ) as t1 left join 
			videostatistic_graph as t2 on t1.datetime=t2.datetime and t1.id_src=t2.id_src where t2.id_src is null";	

      $pdo->exec($sql);





		
   }	
   public function prepareData(){
	    //$comission = \App\WidgetVideo::All();
		//foreach($comission as $pid){
		//$this->pids[$pid->id]=$pid;
		
			//echo $this->pids[$pid->id]->commission_rus;	
		//}
	}	   
}
