<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class Mystat extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:mystat';

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
		 #$tmp_file="/home/myrobot/data/videostatistic/mykz.market-place.su-clicks.log";
	     $my_file="/home/myrobot/data/videostatistic/mykz.market-place.su-clicks.log";
		 $tmp_file="/home/myrobot/data/videostatistic/mykz.market-place.su_".time()."_.log";
		 $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
		 
	    `$cmd`;
						$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				#print_r($tmp);
				//\App\Videosource\Mystat\Top::getInstance()->getData($tmp); #Videosource\\Yac
			}	
		}
		@fclose($handle);
		#return;
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		\App\Videosource\Mystat\Top::getInstance()->collectIMSData();
        #var_dump("стата дата");
    }
	
}
