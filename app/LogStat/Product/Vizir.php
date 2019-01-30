<?php
namespace App\LogStat\Product;
class Vizir{
	 private static $instance=null;
	 private $servers=[];
	 public static function getInstance(){
	 if(self::$instance==null){
	 self::$instance=new self;
	 #self::$instance->prepareData();
	 }
	 return self::$instance;
	}
  public function getData(&$arr){
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		var_dump($arr);
		$ip = $arr[1];
		$region=$arr[3];
		$time = strtotime($arr[0]);
		$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		$ddatetime=date("Y-m-d H:i:s",$dtime);
		$day=date("Y-m-d",$time);
		$country=trim($arr[2]);
		$region=trim($arr[3]);
		$regionName=trim($arr[4]);
		$day=date("Y-m-d",$time);
		$req=preg_split("/\s+/",$arr[5]);
		if(!$req) return;
		$agent=preg_replace('/\"/','',trim($arr[8]));
		
	    parse_str($req[1], $dd);
		//var_dump($dd);
		  if(!$dd || !isset($dd["data"])){
		    return;
	       }	
		$data=json_decode($dd["data"],true);
		if(!$data){
		    return;
	    }
		if(!isset($data["params"])){
			return;
		}
		if(!isset($data["params"]["count"])){
			return;
		}
		$found=$data["params"]["count"];
		var_dump($data["params"]);
		if($found)
		echo $datetime." / ".$country." / ".$region." $regionName ($found)\n";
    }		
}	