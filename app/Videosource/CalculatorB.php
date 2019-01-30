<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class CalculatorB // extends Model
{
	
	public function StartDay(){
		$from='2018-08-13';
		$to='2018-08-22';
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select * from pid_summa_full where pid='752' and day between '$from' and '$to' order  by day asc";
		$stats=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		$sql="update pid_summa_full set 
				control_calculate =?, control_summa=?
				WHERE id=?";
		$sthUpdatePids=$pdo->prepare($sql);
		foreach ($stats as $stat){
			$calculate=floor($stat->control_calculate*0.97);
			$summa=$calculate*0.035;
			$sthUpdatePids->execute([$calculate, $summa,$stat->id]);
			/*if ($stat->control_calculate>33334){
					$cnt=$stat->control_calculate-'33334';
					$sum=$stat->control_summa-'500.01';
					var_dump($stat->day);
					var_dump($cnt);
					var_dump($sum);
					//$sthUpdatePids->execute([$cnt, $sum,$stat->id]);
			}*/
		}
	}
}

