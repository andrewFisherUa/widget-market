<?php

namespace App\MoneyReport;

use Illuminate\Database\Eloquent\Model;
use App\MoneyReport\Account;
use App\MoneyReport\ReportAccount;
class Report extends Model
{
    protected $connection= 'report';
	
	public function summaOpened(){
		$summa=0;
		$course=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		$iid=$this->id-1;
		foreach (Account::all() as $account){
			$reportAccount=ReportAccount::where('report_id', $this->id)->where('account_id', $account->id)->first();
			if (!$reportAccount){
				$reportAccount = new ReportAccount;
				$reportAccount->report_id=$this->id;
				$reportAccount->account_id=$account->id;
				$reportAccount->summa_opened=$account->summa->summa;
				$reportAccount->summa_closed=$account->summa->summa;
				$reportAccount->save();
			}
			if ($account->shortcode=='rub'){
				$summa+=$reportAccount->summa_opened;
			}
			elseif ($account->shortcode=='usd'){
				$summa+=$reportAccount->summa_opened*$course->usd;
			}
			elseif ($account->shortcode=='btc'){
				$summa+=$reportAccount->summa_opened*$course->btc;
			}
			elseif ($account->shortcode=='eth'){
				$summa+=$reportAccount->summa_opened*$course->eth;
			}
			elseif ($account->shortcode=='ltc'){
				$summa+=$reportAccount->summa_opened*$course->ltc;
			}
			elseif ($account->shortcode=='uah'){
				$summa+=$reportAccount->summa_opened*$course->uah;
			}
			elseif ($account->shortcode=='eur'){
				$summa+=$reportAccount->summa_opened*$course->eur;
			}
		}
		$this->summa_opened=$summa;
		$this->save();
		return;
	}
	
	public function summaClosed(){
		$summa=0;
		$course=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		$rAccounts=ReportAccount::where('report_id', $this->id)->get();
		foreach ($rAccounts as $rAccount){
			$rAccount->summa_closed=$rAccount->summa_opened;
			$plus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 1)->sum('summa');
			$rAccount->summa_closed+=$plus;
			$minus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 2)->sum('summa');
			$rAccount->summa_closed-=$minus;
			$rAccount->save();
		}
		foreach (Account::all() as $account){
			$reportAccount=ReportAccount::where('report_id', $this->id)->where('account_id', $account->id)->first();
			if (!$reportAccount){
				continue;
			}
			if ($account->shortcode=='rub'){
				$summa+=$reportAccount->summa_closed;
			}
			elseif ($account->shortcode=='usd'){
				$summa+=$reportAccount->summa_closed*$course->usd;
			}
			elseif ($account->shortcode=='btc'){
				$summa+=$reportAccount->summa_closed*$course->btc;
			}
			elseif ($account->shortcode=='eth'){
				$summa+=$reportAccount->summa_closed*$course->eth;
			}
			elseif ($account->shortcode=='ltc'){
				$summa+=$reportAccount->summa_closed*$course->ltc;
			}
			elseif ($account->shortcode=='uah'){
				$summa+=$reportAccount->summa_closed*$course->uah;
			}
			elseif ($account->shortcode=='eur'){
				$summa+=$reportAccount->summa_closed*$course->eur;
			}
		}
		$this->summa_closed=$summa;
		$this->save();
		return;
	}
	
	public function cources($closed){
		if ($closed){
			$cources=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($this->closed)))->first();
		}
		else{
			$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		}
		return $cources;
	}
	
	public function summaAccount(){
		$rAccounts=ReportAccount::where('report_id', $this->id)->get();
		$iid=$this->id-1;
		var_dump($iid);
		foreach ($rAccounts as $rAccount){
			$qqq=ReportAccount::where('report_id', $iid)->where('account_id', $rAccount->account_id)->first();
			if (!$qqq){
				continue;
			}
			$rAccount->summa_opened=$qqq->summa_closed;
			$rAccount->save();
		}
		$rAccounts=ReportAccount::where('report_id', $this->id)->get();
		foreach ($rAccounts as $rAccount){
			$rAccount->summa_closed=$rAccount->summa_opened;
			$plus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 1)->sum('summa');
			$rAccount->summa_closed+=$plus;
			$minus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 2)->sum('summa');
			$rAccount->summa_closed-=$minus;
			$rAccount->save();
		}
	}
	
	public function oldEdit(){
		$reports=\App\MoneyReport\Report::where('opened', '>=', $this->closed)->orderBy('opened', 'asc')->get();
		$summa=$this->summa_closed;
		foreach ($reports as $report){

			$report->summaAccount();
			$report->summaOpened();
			$report->summaClosed();
			$summa=$report->summa_closed;
			$accountOperations=ReportAccount::where('report_id', $report->id)->get();
				foreach ($accountOperations as $operation){
				$account=Account::where('id', $operation->account_id)->first();
				$account->summa->summa=$operation->summa_closed;
				$account->summa->save();
			}
		}
		return;
	}
}
