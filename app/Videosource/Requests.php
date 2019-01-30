<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class Requests //extends Model
{
    private static $instance=null;
	private $attribs=[];
	private $days=[];

    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	self::$instance->prepareOld();
	}
	return self::$instance;
	}
	 public function getData(&$arr){
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			//var_dump($arr);
			if (!isset($arr[1])){
				var_dump($arr);
				return;
			}
			$ip = $arr[1];
			if (!isset($arr[3])){
				return;
			}
			$region=$arr[3];
			$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$day=date("Y-m-d",$time);
			if (!isset($arr[5])){
				return;
			}
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			parse_str($req[1], $dd);
			if (!isset($arr[6])){
				return;
			}
		    $site=preg_replace('/\"/','',trim($arr[6]));
			if (!isset($arr[8])){
				return;
			}
			$agent=preg_replace('/\"/','',trim($arr[8]));
			if(!$site) $site="_";
			if(!$agent) $agent="_";
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
		   if ($data['pid']==1250){
			var_dump($req);
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
		$second=0;
		//if()
		if (isset($data['second'])){
			if ($data['second']=='1'){
				$second=1;
			}
		}
		$second_cheap=0;
		if (isset($data['second_cheap'])){
			if ($data['second_cheap']=='1'){
				$second_cheap=1;
			}
		}
		$overtype=0;
		if(isset($data["overplay"])){
			//$overtype=$data["overplay"];
			$overtype=1;
		    //var_dump($data);
	    }
		
		$viewable_req=0;
		if (isset($data["viewable_req"])){
			$viewable_req=$data["viewable_req"];
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
		if($is_mobile)
		$is_mobile=1;
		else
		$is_mobile=0;
	$page_key=$data["page_key"];
	//echo $overtype."\n";
	$this->attribs[$page_key]=["ip"=>$ip,"mobie"=>$is_mobile,"url"=>$site,"agent"=>$agent,"datetime"=>$datetime,"region"=>$region];	
  if(!isset($this->attribs[$page_key]["overtype"])){
	  $this->attribs[$page_key]["overtype"]=$overtype;
  }  
   if($overtype==1){
	  # echo "$overtype $site\n";
		 $this->attribs[$page_key]["overtype"]=1;

	}
	$country=trim($arr[2]);
		if($country!='RU')
		$country='CIS';
        $event=$data["event"];		
		if($event=="mytimer"){
			if ($data["mytime"]>'1000000'){
				$data["mytime"]='1000000';
			}
			$ahr=[$datetime,$data["id_src"],$data["mytime"]];
			$this->megaSpeed->execute($ahr);
			
		}
		$block=$data["block"];
        $id_src=$data["id_src"];	
		$pid=$data["pid"];	
		if($pid==4) $pid=300;
        if($pid==6) $pid=701;
		$pseudo="";
		switch($event){
		case "request":
    	$pseudo="requested";
		break;   
		case "AdVideoFirstQuartile":
    	$pseudo="played";
		break;
		
	
		case "AdVideoComplete":
		$pseudo="completed";
		break;
		case "AdVideoThirdQuartile":
		case "AdVideoMidpoint":
		case "AdImpression":
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
		case "AdViewable":
		$pseudo="viewed";
		break;
		
		default:
		//echo $event."\n";
		break;
		}	
	    if(!$pseudo) return;	
		if(!isset($this->days[$day])){
		$this->days[$day]=[];
		}
		if(!isset($this->days[$day][$page_key])){
		$this->days[$day][$page_key]=[];
		}
		if(!isset($this->days[$day][$page_key][$pid])){
		$this->days[$day][$page_key][$pid]=[];
		}
		if(!isset($this->days[$day][$page_key][$pid][$country])){
		$this->days[$day][$page_key][$pid][$country]=[];
		}
	    if(!isset($this->days[$day][$page_key][$pid][$country][$pseudo])){
		$this->days[$day][$page_key][$pid][$country][$pseudo]=1;
		}else{
		$this->days[$day][$page_key][$pid][$country][$pseudo]++;
		}
		if($pseudo=="played"){
			if($id_src==1 || $id_src==2){
				#print $id_src." сурс\n";
			}else{
			    #$this->days[$day][$page_key][$pid][$country]["viewed"]=1;	 
				
			}
			
		}
		if(!isset($this->days[$day][$page_key][$pid][$country]['viewable_req'])){
			$this->days[$day][$page_key][$pid][$country]['viewable_req']=$viewable_req;
		}
		else{
			$this->days[$day][$page_key][$pid][$country]['viewable_req']+=$viewable_req;
		}
		if ($second=="1" and $pseudo=="played"){
			if(!isset($this->days[$day][$page_key][$pid][$country]['second'])){
			$this->days[$day][$page_key][$pid][$country]['second']=1;
			}else{
			$this->days[$day][$page_key][$pid][$country]['second']++;
			}
		}
		if ($second_cheap=="1" and $pseudo=="played"){
			if(!isset($this->days[$day][$page_key][$pid][$country]['second_cheap'])){
			$this->days[$day][$page_key][$pid][$country]['second_cheap']=1;
			}else{
			$this->days[$day][$page_key][$pid][$country]['second_cheap']++;
			}
		}
		
		
		
		
	}
	public function RegisterData(){
	    $pdo = \DB::connection("videotest")->getPdo();
		$sql="create temp table stat_user_pages_temp as (select * from stat_user_pages limit 1);";
		$pdo->exec($sql);
		$sql="truncate table stat_user_pages_temp;";
		$pdo->exec($sql);
		$sql=" insert into stat_user_pages_temp (
    day,
    pid,
    page_key,
    country ,
    requested,
	played,
	clicks,
	start,
	videostart,
	completed,
	mobile,
	ip,
	url,
	agent,
	region,
	datetime,
	second_round,
	second_cheap,
	lease,
	control,
	viewable,
	viewable_req)
	select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
    ;";
	$sthInsertPids=$pdo->prepare($sql);
		
		/*$sql=" insert into stat_user_pages (
    day,
    pid,
    page_key,
    country ,
    requested,
	played,
	clicks,
	start,
	videostart,
	completed,
	mobile,
	ip,
	url,
	agent,
	region,
	datetime,
	second_round,
	second_cheap,
	lease,
	control,
	viewable,
	viewable_req)
	select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM stat_user_pages WHERE day=? and pid=? and page_key =? and country =?)
    ;";
	$sthInsertPids=$pdo->prepare($sql);
	$sql="update stat_user_pages 
    set requested=requested+?,
	played=played+?,
	clicks=clicks+?,
	start=start+?,
	videostart=videostart+?,
	completed=completed+?,
	second_round=second_round+?,
	second_cheap=second_cheap+?,
	viewable=viewable+?,
	viewable_req=viewable_req+?
	WHERE day=? and pid=? and page_key =? and country =?
	";
	$sthUpdatePids=$pdo->prepare($sql);*/
			foreach($this->days as $day=>$pages){
		    foreach($pages as $page=>$pids){
				$overtype=$this->attribs[$page]["overtype"];
				//if($overtype)
						//echo "$overtype --\n";
				
		        foreach($pids as $pid=>$countries){
				    foreach($countries as $country=>$events){
					$requested=0;
					$played=0;
					$click=0;
					$starts=0;
					$videostarts=0;
					$starts=0;
					$completed=0;
					$second_round=0;
					$second_cheap=0;
					$lease=0;
					$control=0;
					$viewed=0;
					$viewable_req=0;
					$videos=\App\WidgetVideo::getInstance($pid);
					if ($videos){
						$user=\App\User::getInstance($videos->widget->user_id);
						if ($user){
							$lease=$user->profile->lease;
						}
						if ($country=='RU'){
							$control=$videos->control_rus;
						}
						else{
							$control=$videos->control_cis;
						}
					}
					if(isset($events["completed"]))
					$completed=$events["completed"];	
					if(isset($events["videostarts"]))
					$videostarts=$events["videostarts"];					
					if(isset($events["starts"]))
					$starts=$events["starts"];
					if(isset($events["requested"]))
					$requested=$events["requested"];
					if(isset($events["played"]))
					$played=$events["played"];
					if(isset($events["click"]))
					$click=$events["click"];
					if (isset($events['second']))
					$second_round=$events['second'];
					if (isset($events['second_cheap']))
					$second_cheap=$events['second_cheap'];
				    if (isset($events['viewed']))
					$viewed=$events['viewed'];
					if (isset($events['viewable_req']))
					$viewable_req=$events['viewable_req'];
					
					
					$ip=$this->attribs[$page]["ip"];
					$mobile=$this->attribs[$page]["mobie"];
					$datetime=$this->attribs[$page]["datetime"];
					$url=$this->attribs[$page]["url"];
					$agent=$this->attribs[$page]["agent"];
					$region=$this->attribs[$page]["region"];
					
					      
					
					//echo $url." : ".mb_substr($url,0,1000)."\n";
		                //echo $day." : ".$page." : $pid  $country  $requested $played  $mobile $ip $url $agent\n";
						//echo $day." : ".$page." : $pid  $country  $requested $played $click $starts $videostarts\n";
					  // $sthUpdatePids->execute([$requested,$played,$click,$starts,$videostarts,$completed,$second_round,$second_cheap,$viewed,$viewable_req,$day,$pid,$page,$country]);
					   $sthInsertPids->execute([$day,$pid,$page,$country,$requested,$played,$click,$starts,$videostarts,$completed,$mobile,$ip,mb_substr($url,0, 1000), mb_substr($agent,0, 1000),$region,$datetime,$second_round,$second_cheap,$lease,$control,$viewed,$viewable_req]);
					}
		        }
		    }
		}
		$sql="update stat_user_pages as t1 set 
			requested=t1.requested+t2.requested
			,played=t1.played+t2.played
			,clicks=t1.clicks+t2.clicks
			,start=t1.start+t2.start
			,videostart=t1.videostart+t2.videostart
			,completed=t1.completed+t2.completed
			,second_round=t1.second_round+t2.second_round
			,second_cheap=t1.second_cheap+t2.second_cheap
			,viewable=t1.viewable+t2.viewable
			,viewable_req=t1.viewable_req+t2.viewable_req
			from stat_user_pages_temp as t2 where t1.day=t2.day and t1.pid=t2.pid and t1.page_key=t2.page_key and t1.country=t2.country";
		$pdo->exec($sql);
		$sql="insert into stat_user_pages(
			day,
			pid,
			page_key,
			country ,
			requested,
			played,
			clicks,
			start,
			videostart,
			completed,
			mobile,
			ip,
			url,
			agent,
			region,
			datetime,
			second_round,
			second_cheap,
			lease,
			control,
			viewable,
			viewable_req)
			select t1.day,t1.pid,t1.page_key,t1.country,t1.requested,t1.played,t1.clicks,t1.start,t1.videostart,t1.completed,t1.mobile,
			t1.ip,t1.url,t1.agent,t1.region,t1.datetime,t1.second_round,t1.second_cheap,t1.lease,t1.control,t1.viewable,t1.viewable_req 
			from stat_user_pages_temp as t1 left join 
			stat_user_pages as t2 on t1.page_key=t2.page_key and t1.pid=t2.pid and t1.day=t2.day and t1.country=t2.country where t2.page_key is null";	
		$pdo->exec($sql);
		$sql="drop table stat_user_pages_temp";
		$pdo->exec($sql);
		
		
					$sql="update  stat_user_pages set pid  = 5
    where pid=10";
	$pdo->exec($sql);
						$sql="update  stat_user_pages set pid  = 11
    where pid=0";
	$pdo->exec($sql);
							$sql="update  stat_user_pages set pid  = 6
    where pid=11";
	$pdo->exec($sql);
		$myhour=preg_replace('/^0/','',date("H"));
		if($myhour==9){
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete  from stat_user_pages where day <'$myday'";
		$pdo->exec($sql);
		
		print " deleted pids untill $myday !!!!\n";
		}
	
    } 	
	public function prepareOld(){
		$sql="
		
insert into src_speed (
    datetime,
    src_id,
    speed
    )values(
	?,?,?
	)
		";
		  $pdo = \DB::connection("videotest")->getPdo();
		  $this->megaSpeed=$pdo->prepare($sql);
		$day=date("Y-m-d H:00:00",time()-3600*2);			
		$hour=preg_replace('/^0+/','',date("H"));		
         if($hour==17)	{	
		 $sql="delete from src_speed where datetime <'$day'; 
		 ";
		 $pdo->exec($sql);
		
		  }
		$tmp="select  
src_id
,count(*) as cnt
,avg(speed) as speed
from 
src_speed
where datetime > (NOW() - INTERVAL '1 hours' )

group by src_id
having avg(speed) <6
order by count(*) desc";
	}

	
}
