<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
class ViewableStat extends Command
{
use WithoutOverlapping;
private $playEvents=[];
private $groupPidsNoControl=[];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:viewable';

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
		//var_dump(1234);
		//exit;
	    #return; 
		$predpdo = \DB::connection("videotest")->getPdo();
	    $this->cacheEvents=[];
		//$tmp_file="/home/myrobot/data/videostatistic/statistic-video.log";
		$tmp_file="/home/myrobot/data/videostatistic/statistic-video-vie".time()."_.log";
        $cmd ="cp -p /home/myrobot/data/videostatistic/statistic-video-vie.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/statistic-video-vie.log";
		`$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				\App\Videosource\Viewable::getInstance()->getData($tmp);
				
			}
		}
		//exit;
		//var_dump(\App\MPW\Statistic\Video\StatisticPads::getInstance()->group);
		//var_dump(\App\MPW\Statistic\Video\StatisticPadsOnPid::getInstance()->group);
		//var_dump(\App\MPW\Statistic\Video\StatisticBlocks::getInstance()->group);
		//var_dump(\App\MPW\Statistic\Video\StatisticPids::getInstance()->group);
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		$this->insertOnDb();
		/*$newT=new \App\Videosource\CalcViewable(); 
		$newT->StartDay();
		*/exit;
		print "старт калкулат \n";
		//$new=new \App\Videosource\Calculator(); 
		//$new->StartDay();
		$newT=new \App\Videosource\CalculatorT(); 
		$newT->StartDay();
		
		print "старт нгс \n";
		$ngs=new \App\Videosource\NgDetail();
		$ngs->StartDay();
		//$this->dispatchPlayerEvents();
	}
	
	private function insertOnDb(){
					#	print "старт visible \n";
		#\App\Videosource\Visible::getInstance()->RegisterData();
									print "старт реквест \n";
		\App\Videosource\Viewable::getInstance()->RegisterData();
		/*		print "старт линкс \n";
		\App\Videosource\Links::getInstance()->RegisterData();
		print "старт pid pad \n";
		
		\App\Videosource\Pid::getInstance()->RegisterData();
		print "старт сайт \n";
		\App\Videosource\Sites::getInstance()->RegisterData();
				print "старт контролс \n";
		\App\Videosource\Controls::getInstance()->RegisterData();
						print "старт сурс \n";
		\App\Videosource\Sources::getInstance()->RegisterData();*/
		//print "старт фрейм \n";
		//\App\Videosource\UserFrame::getInstance()->RegisterData();
		//print "старт реферер \n";
		//\App\Videosource\UserRef::getInstance()->RegisterData();
		//return;
		
	/*print "старт падс\n";
		\App\MPW\Statistic\Video\StatisticPads::getInstance()->insert(\App\MPW\Statistic\Video\StatisticPads::getInstance()->group);
	print "старт падс он пид\n";		
		\App\MPW\Statistic\Video\StatisticPadsOnPid::getInstance()->insert(\App\MPW\Statistic\Video\StatisticPadsOnPid::getInstance()->group);
		print "старт Блокс \n";
		\App\MPW\Statistic\Video\StatisticBlocks::getInstance()->insert(\App\MPW\Statistic\Video\StatisticBlocks::getInstance()->group);
				print "старт пидс \n";
		\App\MPW\Statistic\Video\StatisticPids::getInstance()->insert(\App\MPW\Statistic\Video\StatisticPids::getInstance()->group);
	*/
		

		
		
	}
	
	private function registerEvent(&$data,$datetime,$userAgent, $country, $region, $ref, $control){
		if (!isset($this->playEvents[$data["page_key"]]))
		$this->playEvents[$data["page_key"]]=[];
		if (!isset($this->playEvents[$data["page_key"]][$data["pid"]]))
		$this->playEvents[$data["page_key"]][$data["pid"]]=[];
		if (!isset($this->playEvents[$data["page_key"]][$data["pid"]][$data['id_src']]))
		$this->playEvents[$data["page_key"]][$data["pid"]][$data['id_src']]=[];
		if (!isset($this->playEvents[$data["page_key"]][$data["pid"]][$data['id_src']][$data['event']]))
		$this->playEvents[$data["page_key"]][$data["pid"]][$data['id_src']][$data['event']]=[];
		array_push($this->playEvents[$data["page_key"]][$data["pid"]][$data["id_src"]][$data["event"]],[$datetime,$country,$region,$userAgent, $ref, $data['block'], $control]);
	}
		
	private function dispatchPlayerEvents(){
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$sql="insert into new_video_event_stat (page_key,datetime,pid,id_src,event,country,region,user_agent,ref, block_id, control) values (?,?,?,?,?,?,?,?,?,?,?) ";
		$sth=$pdo->prepare($sql);
		foreach($this->playEvents as $page_key=>$pids){
			foreach($pids as $pid=>$srcs){
				foreach ($srcs as $src=>$events){
					foreach ($events as $event=>$dops){
						foreach ($dops as $dop){
							$sth->execute([$page_key,$dop[0],$pid,$src,$event,$dop[1],$dop[2],$dop[3],$dop[4],$dop[5],$dop[6]]);
						}
					}
				}
			}
		}
		
	}
}
