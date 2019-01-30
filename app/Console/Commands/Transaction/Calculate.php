<?php

namespace App\Console\Commands\Transaction;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;

class Calculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:calculate {date?} {user_id?}';

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
		$date= $this->argument('date');
        $user_id=$this->argument('user_id');
		if ($date>=date('Y-m-d')){
			print "Воу воу не гони лошадей, еще рано считать этот день"."\n";
			exit;
		}
		if(!$date){
			$date=date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS"));
		}
		if ($date<=date('2017-10-18')){
			print "Воу воу не гони лошадей, еще рано считать этот день"."\n";
			exit;
		}
		print $date."\n";
		$calculate = new \App\Transactions\Transaction;
		
		$calculate->commissionVideoWidgets($date);
		print 'Записал суммы по видео'."\n";
		
		$calculate->commissionProductWidgets($date);
		print 'Записал суммы по товарке'."\n";
		
		$calculate->commissionTeaserWidgets($date);
		print 'Записал суммы по тизерке'."\n";
		
		$calculate->commissionManagers($date);
		print 'Записал суммы менеджерам'."\n";
		
		$calculate->commissionReferal($date);
		print 'Записал суммы с рефералов'."\n";
		
		//$calculate->testVideo($date);		//это были мои 0.5%
		
		
		$calculate->transactionOnBalance($date);
		print 'Зачислил на балансы'."\n";
		
		
		
	}
}
