<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class UserFrame //extends Model
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
	public function getData(&$arr, $userPid){
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			//var_dump($arr);
			$ip = $arr[1];
			$time = strtotime($arr[0]);
			$datetime=date("Y-m-d H:i:s",$time);
			$day=date("Y-m-d",$time);
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			parse_str($req[1], $dd);
		    $site=preg_replace('/\"/','',trim($arr[6]));
			if(!$site) $site="_";
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
			if(!isset($data["event"])){
			return;
			}
			if ($data["event"]!="loadWidget"){
			return;
			}
			if (!isset($data["referrer"])){
			return;
			}
			if ($data["referrer"]=='0'){
			return;
			}
			$origins=0;
			if (isset($data["origins"])){
			$origins=$data["origins"];
			}
			$page_key=$data["page_key"];
			$this->attribs[$page_key]=["ip"=>$ip,"url"=>$site,"datetime"=>$datetime];
			$pid=$data["pid"];
			if($pid==4) $pid=300;
			if($pid==6) $pid=701;
			if (!in_array($pid, $userPid)) {
				return;
			}
			if(!isset($this->days[$day])){
			$this->days[$day]=[];
			}
			if(!isset($this->days[$day][$page_key])){
			$this->days[$day][$page_key]=[];
			}
			if(!isset($this->days[$day][$page_key][$pid])){
			$this->days[$day][$page_key][$pid]=[];
			}
			if(!isset($this->days[$day][$page_key][$pid][$site])){
			$this->days[$day][$page_key][$pid][$site]=[];
			}
			if(!isset($this->days[$day][$page_key][$pid][$site][$data["referrer"]])){
			$this->days[$day][$page_key][$pid][$site][$data["referrer"]]=$origins;
			}
	}
	public function RegisterData(){
	    $pdo = \DB::connection("videotest")->getPdo();
		$sql=" insert into frame_pid (
    pid,
    date,
    datetime,
    url,
    referrer,
	origins,
	ip,
	page_key)
	select ?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM frame_pid WHERE date=? and pid=? and page_key =?)
    ;";
	$sthInsertPids=$pdo->prepare($sql);
		foreach($this->days as $day=>$pages){
			foreach($pages as $page=>$pids){
				foreach ($pids as $pid=>$urls){
					foreach ($urls as $url=>$referrers){
						foreach ($referrers as $referrer=>$origins){
							$datetime=$this->attribs[$page]["datetime"];
							$ip=$this->attribs[$page]["ip"];
							$sthInsertPids->execute([$pid,$day,$datetime,$url,$referrer,$origins,$ip,$page,$day,$pid,$page]);
						}
					}
				}
			}
		}
		
		$myday=date("Y-m-d",time()-(3600*168));
		$sql="delete from frame_pid where date <'$myday'";
		$pdo->exec($sql);
		
		$pdo = \DB::connection()->getPdo();
		$myday=date("Y-m-d H:i:s");
		$sql="delete from frame_prover where datetime <'$myday'";
		$pdo->exec($sql);
		
    } 	

	
}
