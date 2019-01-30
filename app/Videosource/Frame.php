<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class Frame //extends Model
{
    private static $instance=null;
	private $attribs=[];
	private $days=[];

    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	}
	return self::$instance;
	}
	 public function getData(&$arr){
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			//var_dump($arr);
			$ip = $arr[1];
			$region=$arr[3];
			$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$day=date("Y-m-d",$time);
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			parse_str($req[1], $dd);
		    $site=preg_replace('/\"/','',trim($arr[6]));
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
		$sql=" insert into stat_user_pages (
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
	viewable)
	select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
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
	viewable=viewable+?
	WHERE day=? and pid=? and page_key =? and country =?
	";
	$sthUpdatePids=$pdo->prepare($sql);
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
					
					
					$ip=$this->attribs[$page]["ip"];
					$mobile=$this->attribs[$page]["mobie"];
					$datetime=$this->attribs[$page]["datetime"];
					$url=$this->attribs[$page]["url"];
					$agent=$this->attribs[$page]["agent"];
					$region=$this->attribs[$page]["region"];
					
					      
					
					//echo $url." : ".mb_substr($url,0,1000)."\n";
		                //echo $day." : ".$page." : $pid  $country  $requested $played  $mobile $ip $url $agent\n";
						//echo $day." : ".$page." : $pid  $country  $requested $played $click $starts $videostarts\n";
					   $sthUpdatePids->execute([$requested,$played,$click,$starts,$videostarts,$completed,$second_round,$second_cheap,$viewed,$day,$pid,$page,$country]);
					   $sthInsertPids->execute([$day,$pid,$page,$country,$requested,$played,$click,$starts,$videostarts,$completed,$mobile,$ip,mb_substr($url,0, 1000), mb_substr($agent,0, 1000),$region,$datetime,$second_round,$second_cheap,$lease,$control,$viewed,$day,$pid,$page,$country]);
					}
		        }
		    }
		}
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

	
}
