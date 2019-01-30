<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Teasernet extends Model
{
    private static $instance=null;
	private $servers=[];
	private $sumsk=[];
	public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	self::$instance->prepareData();
	}
	return self::$instance;
	}
    public function getData(&$arr){
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		//var_dump($arr);
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
		  if(!$dd || !isset($dd["data"])){
		    return;
	       }	
		$data=json_decode($dd["data"],true);
		if(!$data){
		    return;
	    }
		//var_dump($data);
		if(!isset($data["wid"])){
			
			return;
		}
				if(!isset($data["wid"])){
			
			return;
		}
		$clid=0;
		if(isset($data["clid"])){
			$clid=intval($data["clid"]);
			
		}
		if(isset($data["price"])){
			$price=floatval($data["price"]);
			
		}else{
		return;
		}
		if(isset($data["id_d"])){
			$id_d=floatval($data["id_d"]);
			
		}else{
		return;
		}
		if(!isset($this->servers[$data["wid"]])){
			
			return;
		}
		$id_server=$this->servers[$data["wid"]];
		$url=$data["fromUrl"];
		$page_key=$data["page_key"];
		if(!isset($this->sumsk[$day]))
		$this->sumsk[$day]=[];
		if(!isset($this->sumsk[$day][$data["wid"]]))
		$this->sumsk[$day][$data["wid"]]=[];
		if(!isset($this->sumsk[$day][$data["wid"]][$page_key]))
		$this->sumsk[$day][$data["wid"]][$page_key]=[];
		if(!isset($this->sumsk[$day][$data["wid"]][$page_key][$id_d]))
		$this->sumsk[$day][$data["wid"]][$page_key][$id_d]=$price;		
		
		

		#print_r([$id_server,$url,$page_key,$ip,$ip,$country,$region,$ddatetime,$day,$data["wid"],$clid]);
		
		 $this->sth->execute([$id_server,$url,$page_key,$ip,$ip,$country,$region,$ddatetime,$day,$data["wid"],$clid]);

	}
    public function prepareData(){
	    $widgets = \App\MPW\Widgets\Teaser::All();
		foreach($widgets as $wid){
			$this->servers[$wid->id]=$wid->pad;
		 #print_r($wid->toArray());
		//$this->pids[$pid->id]=$pid;
		
			//echo $this->pids[$pid->id]->commission_rus;	
		}
			
		#return;
		$pdo = \DB::connection("pgstatistic")->getPdo(); 
		$sql="
		insert into advert_stat_clicks(id_server
		,url
		,page_key
		,ipshow
		,ipclick
		,offer_id
		,country
		,region
		,timegroup
		,date
		,old_id
		,id_widget
		,clid
		,driver
		) 
		values(?,
		?,
		?,
		?,
		?,
		0,
		?,
		?,
		?,
		?,
		0,
		?,
		?,
		4
		)
		";	
		$this->sth=$pdo->prepare($sql);
    }	
    public function getPrices(){
	$pgpdo = \DB::connection("pgstatistic")->getPdo();
	$sql="
	insert into teasernet_clicks
    (
	day,
    wid,
    id,
    page_key,
    summa
	)
	select ?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM teasernet_clicks WHERE day=? and wid=? and id=? and page_key=?)
	";
	$this->insertIntSum= $pgpdo->prepare($sql);

   
	    foreach($this->sumsk as $day =>$wids){
	        foreach($wids as $wid=>$pages){
		        foreach($pages as $page=>$ids){
				    foreach($ids as $id=>$summa){
					$this->insertIntSum->execute([$day,$wid,$id,$page,$summa,$day,$wid,$id,$page]);
	                    //var_dump([$day,$wid,$id,$page,$summa]);
				    }
		        }
	        }
	    }
	} 	
}
