<?php

namespace App\MoneyReport;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection= 'report';
	public function summa()
	{
		return $this->hasOne('App\MoneyReport\AccountBalancing');
	}
	
	public function valuts()
	{
		return $this->hasOne('App\MoneyReport\Valute', 'shortcode', 'shortcode');
	}
	
	public function operations($id, $report)
	{
		$operations=\App\MoneyReport\ReportOperation::where('accounts_id', $id)->where('reports_id', $report)->orderBy('datetime', 'asc')->get();
		return $operations;
	}
	public function monthStat($id, $day, $month, $year){
		if ($day<10){
			$from=$year."-".$month."-0".$day." 00:00:00";
			$to=$year."-".$month."-0".$day." 23:59:59";
		}
		else{
			$from=$year."-".$month."-".$day." 00:00:00";
			$to=$year."-".$month."-".$day." 23:59:59";
		}
		$pdo = \DB::connection('report')->getPdo();
		$report=\App\MoneyReport\Report::whereBetween('opened', [$from, $to])->first();
		if ($report){
		$sql="select sum(case when type=1 then summa end) as plus, sum(case when type<>1 then summa end) as minus, $report->id as report from report_operations where reports_id='$report->id' and accounts_id='$id'";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		}
		else{
			$stat['plus']=0;
			$stat['minus']=0;
			$stat['report']=0;
		}
		return $stat;
	}
	
	public function yearStat($id, $month, $year){
		$pdo = \DB::connection('report')->getPdo();
		$sql="select sum(case when type=1 then summa end) as plus, sum(case when type<>1 then summa end) as minus from report_operations where Extract(year from datetime)='$year' 
		and Extract(month from datetime)='$month' and accounts_id='$id'";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
}
