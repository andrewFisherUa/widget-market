<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class TeaserKokos extends Model
{
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
		//var_dump($arr);
		$ip = $arr[1];
		$region=$arr[3];
		$time = strtotime($arr[0]);
		$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		$ddatetime=date("Y-m-d H:i:s",$dtime);
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
		
		if(!isset($this->servers[$data["wid"]])){
			
			return;
		}
		$id_server=$this->servers[$data["wid"]];
		$url=$data["fromUrl"];
		$page_key=$data["page_key"];
		
		var_dump([$id_server,$url,$page_key,$ip,$country,$region,$ddatetime,0,$data["wid"],1]);
		$this->sth->execute([$id_server,$url,$page_key,$ip,$country,$region,$ddatetime,0,$data["wid"],0,1]);
	}
	public function prepareData(){
	    $widgets = \App\MPW\Widgets\Teaser::All();
		foreach($widgets as $wid){
			$this->servers[$wid->id]=$wid->pad;
		 #print_r($wid->toArray());
		//$this->pids[$pid->id]=$pid;
		
			//echo $this->pids[$pid->id]->commission_rus;	
		}
		$pdo = \DB::connection("pgstatistic")->getPdo();
	$sql="
		insert into advert_stat_pages(id_server,url,page_key,ip4,country,region,timegroup,old_id,id_widget,rg_,found,driver) 
		values(?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		2000
		)
		";	
		$this->sth=$pdo->prepare($sql);
	}	
}
