<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;

class TeasernetStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:teasernet';

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

	
	        $predpdo = \DB::connection("videotest")->getPdo();
		   #$tmp_file="/home/myrobot/data/videostatistic/teasernetshow.market-place.su-teasershow.log";
	     $tmp_file="/home/myrobot/data/videostatistic/teasernetshow.market-place.su-teasershow_".time()."_.log";
         $cmd ="cp -p /home/myrobot/data/videostatistic/teasernetshow.market-place.su-teasershow.log $tmp_file && cat /dev/null > /home/myrobot/data/videostatistic/teasernetshow.market-place.su-teasershow.log";
	    `$cmd`;
				$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				#print_r($tmp);
				\App\Videosource\Teasernetshow::getInstance()->getData($tmp);

			}	
		}
			
		@fclose($handle);	
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		#return;
		   #$tmp_file="/home/myrobot/data/videostatistic/teasernet.market-place.su-teaser.log";
	     $tmp_file="/home/myrobot/data/videostatistic/teasernet.market-place.su-videostat_".time()."_.log";
         $cmd ="cp -p /home/myrobot/data/videostatistic/teasernet.market-place.su-teaser.log $tmp_file && cat /dev/null > /home/myrobot/data/videostatistic/teasernet.market-place.su-teaser.log";
	    `$cmd`;
				$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				//print_r($tmp);
				\App\Videosource\Teasernet::getInstance()->getData($tmp); #Videosource\\Yac
			}	
		}
		@fclose($handle);
		 \App\Videosource\Teasernet::getInstance()->getPrices()  ;
	     $cmd ="rm  -f  $tmp_file";
	    `$cmd`;		
#             \Rekrut\Product\Models\Marketgitparser::getInstance()->gdeAll();
      
    }
}
