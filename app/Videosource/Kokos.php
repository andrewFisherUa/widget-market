<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class Kokos // extends Model
{
	
	public $wid=[];
	public function StartDay($from=null,$to=null){
		if(!$from || !$to){
			$from=$to=date("Y-m-d");
		}
		$url='https://api.kokos.click/stat-clicks/TESTXXXXXXXXXXXX/?from=' . $from . '&to=' . $to . '';
		$curl = curl_init($url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		var_dump($result);
		foreach ($result->data as $data){
			$ids=$pieces = explode("_-_", trim($data->site));
			$id=0;
			if (count($ids)==2){
				$id=$ids[1];
			}
			if (!isset($this->wid[$from][$id])){
				if ($data->status=="accepted"){
					$this->wid[$from][$id]['clicks']=1;
					$this->wid[$from][$id]['summa']=$data->income;
				}
				else{
					$this->wid[$from][$id]['clicks']=0;
					$this->wid[$from][$id]['summa']=0;
				}
			}
			else{
				if ($data->status=="accepted"){
					$this->wid[$from][$id]['clicks']+=1;
					$this->wid[$from][$id]['summa']+=$data->income;
				}
			}
		}
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$sql="insert into kokos_sum (
		day
		,pid
		,clicks
		,summa)
		select ?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM kokos_sum WHERE pid=? and day =?) ";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update kokos_sum set 
		clicks=? 
		,summa =?
		WHERE pid=? and day =?";
		$sthUpdatePids=$pdo->prepare($sql);
		foreach ($this->wid as $day=>$wid){
			foreach ($wid as $pid=>$stat){
				$sthUpdatePids->execute([$stat['clicks'], $stat['summa'],$pid,$day]);
				$sthInsertPids->execute([$day,$pid,$stat['clicks'], $stat['summa'],$pid,$day]);
			}
		}
	}
}

