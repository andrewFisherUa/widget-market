<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Sources extends Model
{
        //
 private static $instance=null;
	private $sources=[];
	private $days=[];
	private $money=[];
	private $pids=[];
    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	
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
		    case "AdVideoStart":
    	    $pseudo="played";
		    break;   
			}
			if(!$pseudo) return;
		   
		if(!isset($this->days[$day])){
		$this->days[$day]=[];
		}
        if(!isset($this->days[$day][$country])){
		$this->days[$day][$country]=[];
		}
		 if(!isset($this->days[$day][$country][$id_src])){
		$this->days[$day][$country][$id_src]=[];
		}		
		
        if(!isset($this->days[$day][$country][$id_src][$pseudo])){
		$this->days[$day][$country][$id_src][$pseudo]=1;
		}else{
		$this->days[$day][$country][$id_src][$pseudo]++;
		}
			
			
			
		}  
	public function RegisterData(){
	
	    $pdo = \DB::connection("videotest")->getPdo();
		$sql="create temp table stat_sources_temp as (select * from stat_sources limit 1);";
		$pdo->exec($sql);
		$sql="truncate table stat_sources_temp;";
		$pdo->exec($sql);
		$sql="insert into stat_sources_temp (day,id_src,country,requested,played)
		select ?,?,?,?,?";
		$sthInsertPids=$pdo->prepare($sql);
		/*$sql="insert into stat_sources (day,id_src,country,requested,played)
		select ?,?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM stat_sources WHERE day=?  and id_src =? and country =?)
		";
		$sthInsertPids=$pdo->prepare($sql);
				$sql="update stat_sources 
		set requested=requested+?
		,played=played+?
		WHERE day=?  and id_src =? and country =?
		";
		$sthUpdatePids=$pdo->prepare($sql);*/
			foreach($this->days as $day=>$countries){
		        foreach($countries as $country=>$sources){
				    foreach($sources as $id_src=>$events){
					$requested=0;
					$played=0;
					if(isset($events["requested"]))
					$requested=$events["requested"];
					if(isset($events["played"]))
					$played=$events["played"];
					//$sthUpdatePids->execute([$requested,$played,$day,$id_src,$country]);
					$sthInsertPids->execute([$day,$id_src,$country,$requested,$played]);
				       # echo $day." $country $id_src $requested $played\n";
					}
			    }
		    }	
		
		$sql="update stat_sources as t1 set 
			requested=t1.requested+t2.requested
			,played=t1.played+t2.played
			from stat_sources_temp as t2 where t1.day=t2.day and t1.id_src=t2.id_src and t1.country=t2.country";
		$pdo->exec($sql);
		$sql="insert into stat_sources(
			day
			,id_src
			,country
			,requested
			,played)
			select t1.day,t1.id_src,t1.country,t1.requested,t1.played 
			from stat_sources_temp as t1 left join 
			stat_sources as t2 on t1.day=t2.day and t1.id_src=t2.id_src and t1.country=t2.country where t2.id_src is null";	
		$pdo->exec($sql);
		$sql="drop table stat_sources_temp";
		$pdo->exec($sql);
   }	
}
