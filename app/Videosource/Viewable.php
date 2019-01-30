<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class Viewable //extends Model
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
	        //var_dump($arr);
			/*$ip = $arr[1];
			$country = $arr[2];
			$src = $arr[6];
			$agent = $arr[7];
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			parse_str($req[1], $dd);
			//var_dump($dd);
			$data=json_decode($dd["data"],true);
			if 
			var_dump($data);
			exit;
			if ($data['viewable']==1){
				var_dump($arr);
			}
			exit;*/
			$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			//var_dump($arr);
			$ip = $arr[1];
			//$region=$arr[3];
			$time = strtotime($arr[0]);
			//$dtime=$time-($time%600);
			//$datetime=date("Y-m-d H:i:s",$time);
			//$ddatetime=date("Y-m-d H:i:s",$dtime);
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
			if(!isset($data["event"])){
				return;
			}
			if(!isset($data["page_key"])){
				return;
			}
			$page_key=$data["page_key"];
			//echo $overtype."\n";
			$this->attribs[$page_key]=["ip"=>$ip,"url"=>$site,"agent"=>$agent];
			$country=trim($arr[2]);
			if($country!='RU')
			$country='CIS';
			$event=$data["event"];		
			if($event=="mytimer"){
			$ahr=[$datetime,$data["id_src"],$data["mytime"]];
			$this->megaSpeed->execute($ahr);
			}
			$id_src=$data["id_src"];
			$pid=$data["pid"];
			if($pid==4) $pid=300;
			if($pid==6) $pid=701;
			$viewable=$data['viewable'];
			$sec=0;
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
			$second=0;
			if ($sec==1 || $second_cheap==1){
				$second=1;
			}
			$viewable_second=0;
			if ($second==1){
				$viewable_second=$data['viewable'];
			}
			$pseudo="";
			switch($event){
				case "request":
					
					break;   
				case "AdVideoFirstQuartile":
					$pseudo="first_quartile";
					break;
				case "AdVideoComplete":
					$pseudo="completed";
					break;
				case "AdVideoThirdQuartile":
					$pseudo="third_quartile";
					break;
				case "AdVideoMidpoint":
					$pseudo="midpoint";
					break;
				case "AdVideoStart":
					$pseudo="videostarts";
					break;
				default:
				//echo $event."\n";
				break;
		}	
	    if(!$pseudo) return;	
		if(!isset($this->days[$day])){
		$this->days[$day]=[];
		}
		if(!isset($this->days[$day])){
		$this->days[$day]=[];
		}
		if(!isset($this->days[$day][$pid])){
		$this->days[$day][$pid]=[];
		}
		if(!isset($this->days[$day][$pid][$id_src][$country])){
		$this->days[$day][$pid][$id_src][$country]=[];
		}
		if(!isset($this->days[$day][$pid][$id_src][$country])){
		$this->days[$day][$pid][$id_src][$country]=[];
		}
		if(!isset($this->days[$day][$pid][$id_src][$country])){
		$this->days[$day][$pid][$id_src][$country]=[];
		}
	    if(!isset($this->days[$day][$pid][$id_src][$country][$pseudo])){
		$this->days[$day][$pid][$id_src][$country][$pseudo]=1;
		}else{
		$this->days[$day][$pid][$id_src][$country][$pseudo]++;
		}
		if(!isset($this->days[$day][$pid][$id_src][$country]['viewable'])){
		$this->days[$day][$pid][$id_src][$country]['viewable']=$viewable;
		}
		else{
		$this->days[$day][$pid][$id_src][$country]['viewable']+=$viewable;
		}
		/*
		if(!isset($this->days[$day][$pid][$id_src][$country]['viewable_second'])){
		$this->days[$day][$pid][$id_src][$country]['viewable_second']=$viewable_second;
		}
		else{
		$this->days[$day][$pid][$id_src][$country]['viewable_second']+=$viewable_second;
		}
		*/
		
		
		
		
	}
	public function RegisterData(){
		$pdo = \DB::connection("videotest")->getPdo();
		$sql=" insert into stat_viewable (
    day,
	pid,
	id_src,
    country ,
    start,
	first_quartile,
	midpoint,
	third_quartile,
	complete,
	viewable,
	viewable_second)
	select ?,?,?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM stat_viewable WHERE day=? and pid=? and country =? and id_src=?)
    ;";
	$sthInsertPids=$pdo->prepare($sql);
	$sql="update stat_viewable 
    set start=start+?,
	first_quartile=first_quartile+?,
	midpoint=midpoint+?,
	third_quartile=third_quartile+?,
	complete=complete+?,
	viewable=viewable+?,
	viewable_second=viewable_second+?
	WHERE day=? and pid=? and country =? and id_src=?
	";
	$sthUpdatePids=$pdo->prepare($sql);
		foreach($this->days as $day=>$pids){
			foreach($pids as $pid=>$id_srcs){
				foreach ($id_srcs as $id_src=>$countries){
					foreach ($countries as $country=>$events){
						$start=0;
						$first_quartile=0;
						$midpoint=0;
						$third_quartile=0;
						$complete=0;
						$viewable=0;
						$viewable_second=0;
						if(isset($events["videostarts"]))
						$start=$events["videostarts"];
						if(isset($events["first_quartile"]))
						$first_quartile=$events["first_quartile"];
						if(isset($events["midpoint"]))
						$midpoint=$events["midpoint"];
						if(isset($events["third_quartile"]))
						$third_quartile=$events["third_quartile"];
						if(isset($events["completed"]))
						$complete=$events["completed"];
						if(isset($events["viewable"]))
						$viewable=$events["viewable"];
						if(isset($events["viewable_second"]))
						$viewable_second=$events["viewable_second"];

						$sthUpdatePids->execute([$start,$first_quartile,$midpoint,$third_quartile,$complete,$viewable,$viewable_second,
						$day,$pid,$country,$id_src]);
								
						$sthInsertPids->execute([$day,$pid,$id_src,$country,$start,$first_quartile,$midpoint,$third_quartile,
						$complete,$viewable,$viewable_second,$day,$pid,$country,$id_src]);
					}
				}
			}
		}
	}
	public function prepareOld(){
		$pdo = \DB::connection("videotest")->getPdo();
		$day=date("Y-m-d");			
		$hour=preg_replace('/^0+/','',date("H"));
		if($hour==10){
			var_dump('зашел и удалил');
			var_dump($day);
			$sql="delete from stat_viewable where day<'$day'";
			$pdo->exec($sql);
		}
	}

	
}
