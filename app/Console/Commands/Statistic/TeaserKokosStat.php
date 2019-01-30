<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
class TeaserKokosStat extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:teaserkokosstat';

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
	    #$this->cacheEvents=[];
		//$tmp_file="/home/myrobot/data/videostatistic/statistic-video.log";
		$tmp_file="/home/myrobot/data/videostatistic/teaserkokos.market-place.su-teaser_".time()."_.log";
        $cmd ="cp -p /home/myrobot/data/videostatistic/teaserkokos.market-place.su-teaser.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/teaserkokos.market-place.su-teaser.log";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				\App\Videosource\TeaserKokos::getInstance()->getData($tmp);
			}	
		}
	     $cmd ="rm  -f  $tmp_file";
	    `$cmd`;		
    }
}
