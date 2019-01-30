<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Carbon\Carbon;
use Illuminated\Console\WithoutOverlapping;
class BrandStat extends Command
{
use WithoutOverlapping;
private $playEvents=[];
private $groupPidsNoControl=[];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:brandstat {date?}';

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
	    #return; 
		$date= $this->argument('date');
		if(!$date){
			$date=date("Y-m-d");
		}
		$today = Carbon::now();
		if($today->hour<1){
			$date = $today->yesterday()->format('Y-m-d');
		}
	    $this->cacheEvents=[];
		//$tmp_file="/home/myrobot/data/videostatistic/statistic-video.log";
		//$tmp_file="/home/myrobot/data/videostatistic/market-place.su-brandshow.log";
		$tmp_file="/home/myrobot/data/videostatistic/market-place.su-brandshow".time()."_.log";
        $cmd ="cp -p /home/myrobot/data/videostatistic/market-place.su-brandshow.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/market-place.su-brandshow.log";
		`$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				\App\BrandStat\Requests::getInstance()->getData($tmp);
			}
		}
		\App\BrandStat\Requests::getInstance()->RegisterData();
		
		//стата по пидам и сслыка
		\App\BrandStat\Stat::getInstance()->getData($date);
		\App\BrandStat\Calculator::getInstance()->insertCalc($date);
		
		//\App\BrandStat\Pid::getInstance()->RegisterData();
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
	}
}
