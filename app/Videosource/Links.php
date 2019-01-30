<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Links extends Model
{
    private static $instance=null;
	private $sources=[];
    public static function getInstance(){
	if(self::$instance==null)
	self::$instance=new self;
	return self::$instance;
	
	}
	public function getData(&$arr){
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			if (!isset($arr[5])){
				var_dump($arr);
				return;
			}
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			parse_str($req[1], $dd);
	    $country=trim($arr[2]);
		if($country!='RU')
		$country='CIS';
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
	
        $event=$data["event"];		
		$block=$data["block"];
        $id_src=$data["id_src"];	
		//if($block !=1 && ($id_src==13 || $id_src==14)){
		//echo $country." $id_src\n";
		//}
		$pseudo="";
		switch($event){
		case "AdVideoStart":
		
		$pseudo="played";
		break;
		case "AdStarted":
		$pseudo="videostart";
		break;
		case "request":
		$pseudo="requested";
		break;

		}
		if(!$pseudo) return;
		if(!isset($this->sources[$ddatetime]))
		$this->sources[$ddatetime]=[];
		if(!isset($this->sources[$ddatetime][$id_src]))
		$this->sources[$ddatetime][$id_src]=[];
		if(!isset($this->sources[$ddatetime][$id_src][$block]))
		$this->sources[$ddatetime][$id_src][$block]=[];
		if(!isset($this->sources[$ddatetime][$id_src][$block][$country]))
		$this->sources[$ddatetime][$id_src][$block][$country]=[];
		
		
		if(!isset($this->sources[$ddatetime][$id_src][$block][$country][$pseudo]))
		$this->sources[$ddatetime][$id_src][$block][$country][$pseudo]=1;	
		else{
				$this->sources[$ddatetime][$id_src][$block][$country][$pseudo]++;
		}
	   // var_dump($data);
	    //echo $id_src.">".$datetime.">".$ddatetime."\n";
	
	}
	public function RegisterData(){
	
	$pdo = \DB::connection("videotest")->getPdo();
	$sql="create temp table stat_links_temp as (select * from stat_links limit 1);";
		$pdo->exec($sql);
		$sql="truncate table stat_links_temp;";
		$pdo->exec($sql);
		$sql="insert into stat_links_temp (
    id_src,
    block,
    datetime,
	country,
    requested ,
    videostart,
    played 
    ) select ?,?,?,?,?,?,?";
	$sthInsertPids=$pdo->prepare($sql);
	/*$sql="insert into stat_links (
    id_src,
    block,
    datetime,
	country,
    requested ,
    videostart,
    played 
    ) select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM stat_links WHERE id_src=? and
    block =? and
    datetime =? and 
	country=?) 
    ";
	$sthInsertPids=$pdo->prepare($sql);
	$sql="update stat_links 
    set requested=requested+?,
    videostart=videostart+?,
    played =played+?
    WHERE id_src=? and
    block =? and
    datetime =? and
	country=?
    ";
	$sthUpdatePids=$pdo->prepare($sql);*/
	foreach($this->sources as $datetime=>$srcs){
	foreach($srcs as $id_src=>$blocks){
	foreach($blocks as $block=>$countries){
	foreach($countries as $country=>$events){
	//foreach($events as $event=>$cnt){
	$requested=0;
	$videostart=0;
	$played=0;
	
    if(isset($events["requested"]))
	$requested=$events["requested"];
	if(isset($events["videostart"]))
	$videostart=$events["videostart"];
	if(isset($events["played"]))
	$played=$events["played"];
	
	//$sthUpdatePids->execute([$requested,$videostart,$played,$id_src,$block,$datetime,$country]);
    $sthInsertPids->execute([$id_src,$block,$datetime,$country,$requested,$videostart,$played]);
	}
	}
	}
	}
	/*$sql="insert into stat_links (
    id_src,
    block,
    datetime,
	country,
    requested ,
    videostart,
    played 
    ) select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM stat_links WHERE id_src=? and
    block =? and
    datetime =? and 
	country=?) 
    ";
	$sthInsertPids=$pdo->prepare($sql);
	$sql="update stat_links 
    set requested=requested+?,
    videostart=videostart+?,
    played =played+?
    WHERE id_src=? and
    block =? and
    datetime =? and
	country=?
    ";
	$sthUpdatePids=$pdo->prepare($sql);*/
	$sql="update stat_links as t1 set 
			requested=t1.requested+t2.requested
			,videostart=t1.videostart+t2.videostart
			,played=t1.played+t2.played
			from stat_links_temp as t2 where t1.id_src=t2.id_src and t1.block=t2.block and t1.datetime=t2.datetime and t1.country=t2.country";
		$pdo->exec($sql);
		$sql="insert into stat_links(
			id_src
			,block
			,datetime
			,country
			,requested
			,videostart
			,played)
			select t1.id_src,t1.block,t1.datetime,t1.country,t1.requested,t1.videostart,t1.played
			from stat_links_temp as t1 left join 
			stat_links as t2 on t1.id_src=t2.id_src and t1.block=t2.block and t1.datetime=t2.datetime and t1.country=t2.country 
			where t2.id_src is null";	
		$pdo->exec($sql);
		$sql="drop table stat_links_temp";
		$pdo->exec($sql);
	
	}	
	
}
