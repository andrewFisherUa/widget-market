<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;


class Visible //extends Model
{
    private static $instance=null;
	private $attribs=[];
	private $events=[];
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
		$country=trim($arr[2]);
		if($country!='RU')
		$country='CIS';
        $event=$data["event"];		
		$block=$data["block"];
        $id_src=$data["id_src"];	
		$pid=$data["pid"];	
		if($pid==4) $pid=300;
        if($pid==6) $pid=701;
	   # print  $event."\n";
		$this->events[$event]=1;
		
	}
	public function RegisterData(){
		print_r($this->events);
	
	
    } 	

	
}

