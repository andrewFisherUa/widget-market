<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
use App\LogStat\Product\Yandex as Reproduct;
class YandexStat extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:yacstat'; #/usr/bin/php /home/www/widget.market-place.su/artisan statistic:yacstat

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
   return;
        $predpdo = \DB::connection("videotest")->getPdo();
		$file='/home/mystatistic/product/yandex.log';
		$tmp_file="/home/mystatistic/product/yandex_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				echo $l."\n";
				$tmp=preg_split("/\s+\:\s+/",$l);
				Reproduct::getInstance()->getData($tmp);

			}
	    @fclose($handle);		
		}		
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		 return;
		 
        /*
	     $tmp_file="/home/myrobot/data/videostatistic/yac.market-place.su-videostat_".time()."_.log";
         $cmd ="cp -p /home/myrobot/data/videostatistic/yac.market-place.su-videostat.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/yac.market-place.su-videostat.log";
	    `$cmd`;
	   
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				echo $l."\n";
				$tmp=preg_split("/\s+\:\s+/",$l);
				\App\Videosource\Yac::getInstance()->getData($tmp); #Videosource\\Yac
			}	
		}
		@fclose($handle);
	     $cmd ="rm  -f  $tmp_file";
	    `$cmd`;		
		*/
    } 
    
}
