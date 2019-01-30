<?php

namespace App\Console\Commands\Report;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use App\MoneyReport\Valute;
use App\MoneyReport\Account;
use App\MoneyReport\Report;
use App\MoneyReport\ReportAccount;
use App\MoneyReport\ReportOperation;
use App\MoneyReport\AccountBalancing;
use App\MoneyReport\TypeOperation;

class Close extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:close';

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
		$report=Report::whereNull('closed')->first();
		if (!$report->closed){
		$report->closed=date("Y-m-d H:i:s");
		$report->summaClosed();
		$report->save();
		}
		else{
			var_dump('уже был закрыт');
		}
		$accountOperations=ReportAccount::where('report_id', $report->id)->get();
		foreach ($accountOperations as $operation){
			$account=Account::where('id', $operation->account_id)->first();
			$account->summa->summa=$operation->summa_closed;
			$account->summa->save();
		}
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
	}
}
