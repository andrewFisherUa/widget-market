<?php

namespace App\BrandStat;

//use Illuminate\Database\Eloquent\Model;

class Requests //extends Model
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
	public function getData(&$arr){
			$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			$time = strtotime($arr[0]);
			$dtime=$time-($time%180);

			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$day=date("Y-m-d",$time);
			$ip = $arr[1];
			$country=$arr[2];
			if ($country!="RU"){
				$country="CIS";
			}
			$region=$arr[3];
			$site=preg_replace('/\"/','',trim($arr[6]));
			$agent=preg_replace('/\"/','',trim($arr[8]));
			if(!$site) $site="_";
			if(!$agent) $agent="_";
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
			if (!isset($data["page_key"])){
				return;
			}
			if (!isset($data["fromUrl"])){
				return;
			}
			if (!isset($data["pid"])){
				return;
			}
			if (!isset($data["block"])){
				return;
			}
			if (!isset($data["id_src"])){
				return;
			}
			$page_key=$data["page_key"];
			$this->attribs[$page_key]=["ip"=>$ip,"country"=>$country,"region"=>$region,"url"=>$site,"fromUrl"=>$data["fromUrl"],"pid"=>$data["pid"],"id_block"=>$data["block"],"id_offer"=>$data["id_src"],
			"agent"=>$agent, "day"=>$day, "datetime"=>$datetime, "timegroup"=>$ddatetime];
		
	}
	public function RegisterData(){
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$sql="insert into brand_stat_pages (
			ip,
			page_key,
			url,
			from_url,
			country,
			region,
			pid,
			id_block,
			offer_id,
			day,
			datetime,
			timegroup,
			agent)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?
			WHERE NOT EXISTS (SELECT 1 FROM brand_stat_pages where day=? and page_key=?);";
		$sthInsertPids=$pdo->prepare($sql);
		foreach ($this->attribs as $key=>$d){
			$sthInsertPids->execute([
			$d['ip'],
			$key,
			$d['url'],
			$d['fromUrl'],
			$d['country'],
			$d['region'],
			$d['pid'],
			$d['id_block'],
			$d['id_offer'],
			$d['day'],
			$d['datetime'],
			$d['timegroup'],
			$d['agent'],
			$d['day'],
			$key]);
		}

		$myhour=preg_replace('/^0/','',date("H"));
		if($myhour==9){
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete  from brand_stat_pages where day <'$myday'";
		$pdo->exec($sql);
		
		print " deleted pids untill $myday !!!!\n";
		}
	
    } 	

	
}
