<?php

namespace App\Console\Commands\Statistic\Analysis;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminated\Console\WithoutOverlapping;
use AdvertStat;

class Advert extends Command
{
  //use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:analysis:advert {date?}';

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
		if(!$date){
			$date=date("Y-m-d");
		}
		$today = Carbon::now();
		if($today->hour<2){
			$date = $today->yesterday()->format('Y-m-d');
		}
		AdvertStat::setDate($date);
		AdvertStat::Calculate();
		//AdvertStat::TeaserCalc();
		
    }
}
