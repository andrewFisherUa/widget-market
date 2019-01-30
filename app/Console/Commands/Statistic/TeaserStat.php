<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;

use App\LogStat\Teaser\Index as Reproduct;

class TeaserStat extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:teaserstat';

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
        //
		$predpdo = \DB::connection("videotest")->getPdo();
		//$predpdo = \DB::connection("videotest")->getPdo();
		$file='/home/mystatistic/teaser/teaser.log';
		$tmp_file="/home/mystatistic/teaser/teaser_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
                                #echo $l."\n";
				$tmp=preg_split("/\s+\:\s+/",$l);
				Reproduct::getInstance()->getData($tmp);
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;	


		$file='/home/mystatistic/teaser/teaserclicks.log';
		$tmp_file="/home/mystatistic/teaser/teaserclicks_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				#echo $l."\n";
				$tmp=preg_split("/\s+\:\s+/",$l);
				Reproduct::getInstance()->getClicks($tmp);
			}
	    @fclose($handle);		
		}		
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;			
             \Rekrut\Product\Models\Marketgitparser::getInstance()->gdeAll();


		return;
	    #$this->cacheEvents=[];
		//$tmp_file="/home/myrobot/data/videostatistic/statistic-video.log";
		$tmp_file="/home/myrobot/data/videostatistic/mp.market-place.su-teaser_".time()."_.log";
        $cmd ="cp -p /home/myrobot/data/videostatistic/mp.market-place.su-teaser.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/mp.market-place.su-teaser.log";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);

				\App\Videosource\Teaser::getInstance()->getData($tmp);
			}	
		}
		@fclose($handle);
	     $cmd ="rm  -f  $tmp_file";
	    `$cmd`;		
    }
}
