<?php

namespace App\Console\Commands\Transaction;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;

class CalculateToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:calculate_today';

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
		$date=date("Y-m-d");
		
		
		print $date."\n";
		$calculate = new \App\Transactions\Transaction;
		
		
		
		$calculate->commissionVideoWidgetsToday($date);
		print 'Записал суммы по видео'."\n";
		$calculate->commissionProductWidgetsToday($date);
		print 'Записал суммы по товарке'."\n";
		$calculate->commissionTeaserWidgetsToday($date);
		print 'Записал суммы по тизерке'."\n";
		$calculate->commissionManagersToday($date);
		print 'Записал суммы менеджерам'."\n";
		
	}
}
