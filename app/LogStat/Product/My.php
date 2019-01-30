<?php
namespace App\LogStat\Product;
class My{
	 private static $instance=null;
	 private $servers=[];
	 public static function getInstance(){
	 if(self::$instance==null){
	 self::$instance=new self;
	 self::$instance->prepareData();
	 }
	 return self::$instance;
	}
  public function getData(&$arr){
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		
		$ip = $arr[1];
		$region=$arr[3];
		$time = strtotime($arr[0]);
		$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		$ddatetime=date("Y-m-d H:i:s",$dtime);
		$day=date("Y-m-d",$time);
		$country=trim($arr[2]);
		$region=trim($arr[3]);
	

		
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
		if(!isset($data["uid"])) return;
		if(!isset($data["tracker_id"])) return;
		if(!isset($data["url"])) return;
		if(!isset($data["referer"])) return;
		if(!isset($data["action"])) return;
		$id_company=0;
		if(isset($data["utm_campaign"])) 
		$id_company=intval($data["utm_campaign"]);
	    $summa=0;
		if($data["action"]=='cart'){
			if(isset($data["shopping"]) && isset($data["shopping"]["summa"])){
				$summa=floatval(preg_replace('/,/','.',$data["shopping"]["summa"]));
			}
		  
		}
		
		
		
		$ret=[$day,
		$datetime,
		$data["uid"],
		$data["tracker_id"],
		$data["url"],
		$data["referer"],
		$country,
		$region,
		$data["action"],
		$id_company,
		$summa
		];
		$this->sth->execute($ret);
		
		//var_dump($data);
		/*
		if(!isset($data["wid"])){
			
			return;
		}
		$clid=0;
		if(isset($data["clid"])){
			$clid=intval($data["clid"]);
			
		}
		if(!isset($this->servers[$data["wid"]])){
			
			return;
		}
		$id_server=$this->servers[$data["wid"]];
		$url=$data["fromUrl"];
		$page_key=$data["page_key"];
		
		
		 $this->sth->execute([$id_server,$url,$page_key,$ip,$ip,$country,$region,$ddatetime,$day,$data["wid"],$clid]);
		 var_dump([$id_server,$url,$page_key,$ip,$country,$region,$ddatetime,0,$data["wid"],1,$clid]);
		 */
	}	
	public function prepareData(){
		
	$pdo = \DB::connection("pgstatistic_new")->getPdo();
	$sql="insert into metrica_tracker(
    day,
    datetime,
	user_id,
    tracker_id,
	url,
    referer,
    country,
    region,
	action,
	id_company,
	summa
    )values(?,?,?,?,?,?,?,?,?,?,?)
	";	
	$this->sth=$pdo->prepare($sql);
	}	
}