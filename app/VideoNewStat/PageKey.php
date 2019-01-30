<?php

namespace App\VideoNewStat;

class PageKey
{
	private $attribs=[];
	private $key=[];
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
    public function getData(&$arr){
		$ip=$arr[1];
		$region=$arr[3];//????
		$country=trim($arr[2]);
		if($country!='RU')
		$country='CIS';
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		$time = strtotime($arr[0]);
		$datetime=date("Y-m-d H:i:s",$time);
		$day=date("Y-m-d", $time);
		$url=preg_replace('/\"/','',trim($arr[6]));
		if (!$url) $url="_";
		$agent=preg_replace('/\"/','',trim($arr[8]));
		if ($agent) $agent="_";
		$req=preg_split("/\s+/",$arr[5]);
		if(!$req) return;
		parse_str($req[1], $dd);
		if(!$dd || !isset($dd["data"])){
			return;
		}
		$data=json_decode($dd["data"],true);
		if (!$data) return;
		if (!isset($data['page_key']) || !isset($data['event']) || !isset($data['id_src']) || !isset($data['block']) || !isset($data['pid'])) return;
		$second=0;
		if (isset($data['second'])){
			if ($data['second']){
				$second=1;
			}
		}
		$second_cheap=0;
		if (isset($data['second_cheap'])){
			if ($data['second_cheap']){
				$second_cheap=1;
			}
		}
		$viewable=0;
		if (isset($data["viewable_req"])){
			$viewable=$data["viewable_req"];
		}
		$is_mobile=0;
		if(isset($data["is_mobile"])){
		    $is_mobile=$data["is_mobile"];
	    }
		if ($is_mobile) $is_mobile=1;
		else $is_mobile=0;
		$page_key=$data["page_key"];
		$event=$data["event"];
		$id_src=$data["id_src"];
		$block=$data["block"];
		$pid=$data["pid"];
		if($pid==4) $pid=300;
		if($pid==6) $pid=701;
		$this->attribs[$page_key]=["ip"=>$ip,"mobile"=>$is_mobile,"url"=>$url,"agent"=>$agent,"datetime"=>$datetime];
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
			case "click":
			case "AdClickThru":
				$pseudo="click";
				break;
			default:
				break;
			/*
			case "AdVideoThirdQuartile":
			case "AdVideoMidpoint":
			case "AdImpression":
			case "AdStarted":
			case "AdVideoStart":
				break;
			*/
		}
		if (!$pseudo) return;
		if(!isset($this->key[$day])){
			$this->key[$day]=[];
		}
		if(!isset($this->key[$day][$page_key])){
		$this->key[$day][$page_key]=[];
		}
		if(!isset($this->key[$day][$page_key][$pid])){
		$this->key[$day][$page_key][$pid]=[];
		}
		if(!isset($this->key[$day][$page_key][$pid][$country])){
		$this->key[$day][$page_key][$pid][$country]=[];
		}
	    if(!isset($this->key[$day][$page_key][$pid][$country][$pseudo])){
		$this->key[$day][$page_key][$pid][$country][$pseudo]=1;
		}else{
		$this->key[$day][$page_key][$pid][$country][$pseudo]++;
		}
		if(!isset($this->key[$day][$page_key][$pid][$country]['viewable'])){
			$this->key[$day][$page_key][$pid][$country]['viewable']=$viewable;
		}
		else{
			$this->key[$day][$page_key][$pid][$country]['viewable']+=$viewable;
		}
		if ($second=="1" and $pseudo=="played"){
			if(!isset($this->key[$day][$page_key][$pid][$country]['second'])){
			$this->key[$day][$page_key][$pid][$country]['second']=1;
			}else{
			$this->key[$day][$page_key][$pid][$country]['second']++;
			}
		}
		if ($second_cheap=="1" and $pseudo=="played"){
			if(!isset($this->key[$day][$page_key][$pid][$country]['second_cheap'])){
			$this->key[$day][$page_key][$pid][$country]['second_cheap']=1;
			}else{
			$this->key[$day][$page_key][$pid][$country]['second_cheap']++;
			}
		}
	}
	
	public function registerTemp(){
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="create temp table new_key_stat_pages_temp as (select * from new_key_stat_pages limit 1);";
		$pdo->exec($sql);
		$sql="truncate table new_key_stat_pages_temp;";
		$pdo->exec($sql);
		$sql=" insert into new_key_stat_pages_temp (
			day
			,pid
			,page_key
			,country
			,requested
			,viewable
			,played
			,mobile
			,ip
			,url
			,agent
			,datetime
			,clicks
			,completed
			,second_round
			,second_cheap
			,lease
			,control)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
		foreach($this->key as $day=>$pages){
			foreach($pages as $page_key=>$pids){
				foreach($pids as $pid=>$countries){
					foreach($countries as $country=>$events){
						$requested=0;
							if(isset($events["requested"]))
							$requested=$events["requested"];
						$played=0;
							if(isset($events["played"]))
							$played=$events["played"];
						$viewable=0;
							if (isset($events['viewable']))
							$viewable=$events['viewable'];
						$click=0;
							if (isset($events['click']))
							$viewable=$events['click'];
						$completed=0;
							if(isset($events["completed"]))
							$completed=$events["completed"];
						$second_round=0;
							if (isset($events['second']))
							$second_round=$events['second'];
						$second_cheap=0;
							if (isset($events['second_cheap']))
							$second_cheap=$events['second_cheap'];
						$lease=0;
						$control=0;
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
						$ip=$this->attribs[$page_key]["ip"];
						$mobile=$this->attribs[$page_key]["mobile"];
						$datetime=$this->attribs[$page_key]["datetime"];
						$url=$this->attribs[$page_key]["url"];
						$agent=$this->attribs[$page_key]["agent"];
						$sthInsert->execute([$day,$pid,$page_key,$country,$requested,$viewable,$played,$mobile,$ip,mb_substr($url,0, 1000),
						mb_substr($agent,0, 1000),$datetime,$click,$completed,$second_round,$second_cheap,$lease,$control]);
					}
				}
			}
		}
		
		$sql="select count(*) from new_key_stat_pages_temp";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		var_dump($stat);
	}
	
	public function registerDB(){
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="update new_key_stat_pages as t1 set 
			requested=t1.requested+t2.requested
			,played=t1.played+t2.played
			,clicks=t1.clicks+t2.clicks
			,completed=t1.completed+t2.completed
			,second_round=t1.second_round+t2.second_round
			,second_cheap=t1.second_cheap+t2.second_cheap
			from new_key_stat_pages_temp as t2 where t1.day=t2.day and t1.pid=t2.pid and t1.page_key=t2.page_key";
		$pdo->exec($sql);
		$sql="insert into new_key_stat_pages(
			day
			,pid
			,page_key
			,country
			,requested
			,viewable
			,played
			,mobile
			,ip
			,url
			,agent
			,datetime
			,clicks
			,completed
			,second_round
			,second_cheap
			,lease
			,control)
			select 
			t1.day,t1.pid,t1.page_key,t1.country,t1.requested,t1.viewable,t1.played,t1.mobile,t1.ip,t1.url,t1.agent,t1.datetime
			,t1.clicks,t1.completed,t1.second_round,t1.second_cheap,t1.lease,t1.control from new_key_stat_pages_temp as t1 left join 
			new_key_stat_pages as t2 on t1.page_key=t2.page_key and t1.pid=t2.pid and t1.day=t2.day where t2.page_key is null";	
		$pdo->exec($sql);
		$sql="drop table new_key_stat_pages_temp";
		$pdo->exec($sql);
	}
}
