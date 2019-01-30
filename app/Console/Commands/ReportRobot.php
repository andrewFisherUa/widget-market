<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\NoActive;

class ReportRobot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:robot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$to_date=date('Y-m-d H:i:s');
		$from_date=date('Y-m-d H:i:s',time()-3600*24*7);
		$transactions=\DB::connection('obmenneg')->table('local_robots')->whereBetween('created_at', [$from_date, $to_date])->where('status', '9')->get();
		$report=\App\MoneyReport\Report::orderBy('opened', 'desc')->first();
		\App\MoneyReport\ReportOperation::where('reports_id', $report->id)->
				where('shortcode', 'rbt')->delete();
		foreach ($transactions as $transaction){
			$cource=\DB::connection('report')->table('courses_tmp')->where('datetime', '<=', $transaction->created_at)->orderBy('datetime', 'desc')->first();
			if (!$cource){
				$cource=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($transaction->created_at)))->first();
			}
			if ($transaction->updated_at<'2018-05-17 00:00:00'){
				continue;
			}
			if ($transaction->updated_at<$report->opened){
				var_dump($transaction);
				continue;
			}
			if ($transaction->updated_at<'2018-05-21 14:10:00'){
				//var_dump($transaction);
				continue;
			}
			/*if ($transaction->id_ad=='609849'){//это киви бай
				continue;
				$account=\App\MoneyReport\Account::where('id', 20)->first();
				if (!$account){
					continue;
				}
				if (!$report){
					continue;
				}
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=$account->id;
				$operat->type=2;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount*1.01;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->save();
				
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=37;
				$operat->type=1;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount_btc*0.99;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->save();
			}
			
			if ($transaction->id_ad=='617372'){ //это киви селл
				continue;
				$account=\App\MoneyReport\Account::where('id', 20)->first();
				if (!$account){
					continue;
				}
				if (!$report){
					continue;
				}
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=$account->id;
				$operat->type=1;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount*1;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->save();
				
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=37;
				$operat->type=2;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount_btc*1.01;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->save();
			}*/
			
			if ($transaction->id_ad=='609928'){ //это яндекс бай
				$account=\App\MoneyReport\Account::where('id', 57)->first();
				if (!$account){
					continue;
				}
				if (!$report){
					continue;
				}
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=$account->id;
				$operat->type=2;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount*1.005;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->cources=1;
				$operat->save();
				
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=58;
				$operat->type=1;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount_btc*0.99;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->cources=$cource->btc;
				$operat->save();
			}
			
			if ($transaction->id_ad=='609305'){ //это яндекс селл
				$account=\App\MoneyReport\Account::where('id', 57)->first();
				if (!$account){
					continue;
				}
				if (!$report){
					continue;
				}
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=$account->id;
				$operat->type=1;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount*1;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->cources=1;
				$operat->save();
				
				$operat=new \App\MoneyReport\ReportOperation;
				$operat->reports_id=$report->id;
				$operat->accounts_id=58;
				$operat->type=2;
				$operat->shortcode='rbt';
				$operat->summa=$transaction->amount_btc*1.01;
				$operat->comment=$transaction->contact_id;
				$operat->datetime=$transaction->created_at;
				$operat->obnal=0;
				$operat->cources=$cource->btc;
				$operat->save();
			}
		}
		$rAccounts=\App\MoneyReport\ReportAccount::where('report_id', $report->id)->get();
		foreach ($rAccounts as $rAccount){
			$rAccount->summa_closed=$rAccount->summa_opened;
			$plus=\App\MoneyReport\ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 1)->sum('summa');
			$rAccount->summa_closed+=$plus;
			$minus=\App\MoneyReport\ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 2)->sum('summa');
			$rAccount->summa_closed-=$minus;
			$rAccount->save();
		}
		$report->summaClosed();
		$report->save();
		$accountOperations=\App\MoneyReport\ReportAccount::where('report_id', $report->id)->get();
		foreach ($accountOperations as $operation){
			$account=\App\MoneyReport\Account::where('id', $operation->account_id)->first();
			$account->summa->summa=$operation->summa_closed;
			$account->summa->save();
		}
	}
}
