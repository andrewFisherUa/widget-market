<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;

class NewVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:video_new_stat';

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
        $file="/home/myrobot/data/videostatistic/statistic-video1531690022_.log";
		$handle = @fopen($file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				\App\VideoNewStat\PageKey::getInstance()->getData($tmp);
			}
		}
		
		$this->insertTemp();
		$this->insertDB();
    }
	
	public function insertTemp(){
		print "запись во временую stat_user_pages \n";
		\App\VideoNewStat\PageKey::getInstance()->registerTemp();
	}
	
	public function insertDB(){
		print "запись в дейстувующую stat_user_pages \n";
		\App\VideoNewStat\PageKey::getInstance()->registerDB();
	}
}
