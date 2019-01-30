<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Carbon\Carbon;

class NgDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'statistic:ngdetail {date?}';
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
		if($today->hour<1){
			$date = $today->yesterday()->format('Y-m-d');
		}
		//$new=new \App\Videosource\Calculator(); 
		//$new->StartDay($date,$date);
		
		$newT=new \App\Videosource\NgDetail(); 
		$newT->StartDay($date);
    }
}
