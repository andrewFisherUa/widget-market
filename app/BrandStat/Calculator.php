<?php

namespace App\BrandStat;

//use Illuminate\Database\Eloquent\Model;

class Calculator //extends Model
{
    private static $instance=null;
	//private $attribs=[];
	//private $days=[];

    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	}
	return self::$instance;
	}
	
	public $linksumma=[];
	public $pids=[];
	public function insertCalc($date){
		$this->prepareData();
		$this->getData($date);
		$this->insert();
	}
	
	public function getData($date){
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select * from brand_stat";
		$stats = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($stats as $stat){
			if ($stat['country']=="RU"){
				$summa=($stat['showed']*$this->linksumma[$stat['id_offer']]['summa_rus'])/1000;
			}
			else{
				$summa=($stat['showed']*$this->linksumma[$stat['id_offer']]['summa_cis'])/1000;
			}
			if(!isset($this->pids[$stat["day"]][$stat["pid"]][$stat["country"]])){
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]=[];
			}
			if(!isset($this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["showed"])){
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["showed"]=$stat["showed"];
			}
			else{
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["showed"]+=$stat["showed"];
			}
			if(!isset($this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["unik_showed"])){
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["unik_showed"]=$stat["unik_showed"];
			}
			else{
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["unik_showed"]+=$stat["unik_showed"];
			}
			if(!isset($this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["click"])){
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["click"]=$stat["click"];
			}
			else{
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["click"]+=$stat["click"];
			}
			if(!isset($this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["unik_click"])){
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["unik_click"]=$stat["unik_click"];
			}
			else{
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["unik_click"]+=$stat["unik_click"];
			}
			if(!isset($this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["summa"])){
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["summa"]=$summa;
			}
			else{
				$this->pids[$stat["day"]][$stat["pid"]][$stat["country"]]["summa"]+=$summa;
			}
		}
	}
	
	public function insert(){
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$sql="insert into brand_stat_pid (pid,day,country,showed,unik_showed,click,unik_click,summa)
			select ?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM brand_stat_pid WHERE pid=? and day=? and country=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update brand_stat_pid set showed=?, unik_showed=?, click=?, unik_click=?, summa=? 
			WHERE pid=? and day=? and country=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($this->pids as $day=>$pids){
			foreach ($pids as $pid=>$countrys){
				foreach ($countrys as $country=>$d){
					$sthUpdate->execute([$d['showed'], $d['unik_showed'], $d['click'], $d['unik_click'], $d['summa'], $pid, $day, $country]);
					$sthInsert->execute([$pid, $day, $country, $d['showed'], $d['unik_showed'], $d['click'], $d['unik_click'], $d['summa'], $pid, $day, $country]);
				}
			}
		}
	}
	
	public function prepareData(){
		$pdo=\DB::connection()->getPdo();
		$sql="select * from brand_offers";
		$links=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		foreach ($links as $link){
			$this->linksumma[$link->id]['summa_rus']=$link->summa_rus;
			$this->linksumma[$link->id]['summa_cis']=$link->summa_cis;
		}
		//var_dump(123);
	}

	
}
