<?php

namespace App\Obmenneg;

use Illuminate\Database\Eloquent\Model;

class Valut extends Model
{
	protected $connection= 'obmenneg';
	public function monthStat($id, $day, $month, $year){
		if ($day<10){
			$from=$year."-".$month."-0".$day;
		}
		else{
			$from=$year."-".$month."-".$day;
		}
		$pdo = \DB::connection('obmenneg')->getPdo();
		$sql="select id_valut, sum(plus) as plus, sum(minus) as minus from transactions where id_valut='$id' and date='$from'
		group by id_valut";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
	
	public function yearStat($id, $month, $year){
		$pdo = \DB::connection('obmenneg')->getPdo();
		$sql="select id_valut,sum(plus) as plus, sum(minus) as minus from transactions where id_valut='$id' and Extract(year from date)='$year' 
		and Extract(month from date)='$month'
		group by id_valut";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
	
	public function transactions($id, $date){
		$stats=\App\Obmenneg\Transaction::where('id_valut', $id)->where('date', $date)->get();
		return $stats;
	}
	
	public function monthAllStat($id, $month, $year){
		if ($month=="01" or $month=="03" or $month=="05" or $month=="07" or $month=="08" or $month=="10" or $month=="12"){
			$day=31;
		}
		elseif ($month=="04" or $month=="06" or $month=="09" or $month=="11"){
			$day=30;
		}
		elseif ($month=="02" and $year%4==0){
			$day=29;
		}
		else{
			$day=28;
		}
		$from=$year."-".$month."-01";
		$to=$year."-".$month."-".$day;
		$pdo = \DB::connection('obmenneg')->getPdo();
		$sql="select id_valut, sum(plus) as plus, sum(minus) as minus from transactions where id_valut='$id' and date between '$from' and '$to'
		group by id_valut";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
}
