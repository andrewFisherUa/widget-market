<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
class VideoStat extends Command
{
use WithoutOverlapping;
private $playEvents=[];
private $groupPidsNoControl=[];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:videostat';

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
		$predpdo = \DB::connection("videotest")->getPdo();
	    $this->cacheEvents=[];
		//$tmp_file="/home/myrobot/data/videostatistic/statistic-video.log";
		$tmp_file="/home/mp.su/statistic/videostatistic/statistic-video".time()."_.log";
        $cmd ="cp -p /home/mp.su/statistic/videostatistic/statistic-video.log $tmp_file && cat /dev/null >  /home/mp.su/statistic/videostatistic/statistic-video.log";
		
		//$cmd ="cp -p /home/mp.su/statistic/videostatistic/statistic-video1534832957_.log $tmp_file && cat /dev/null >  /home/mp.su/statistic/videostatistic/statistic-video1534832957_.log";
		`$cmd`;
		$pdo = \DB::connection()->getPdo();
		$sql="select t3.id from frame_prover t1 left join (select id,user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select * from widget_videos) t3 on t2.id=t3.wid_id where t1.datetime>now();";
		$ppp=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$userPid=array();
		foreach ($ppp as $pp){
			array_push($userPid, $pp['id']);
		}
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				\App\Videosource\Links::getInstance()->getData($tmp);
				\App\Videosource\Pid::getInstance()->getData($tmp);
				\App\Videosource\Sites::getInstance()->getData($tmp);
				\App\Videosource\Controls::getInstance()->getData($tmp);
				\App\Videosource\Sources::getInstance()->getData($tmp);
				\App\Videosource\Requests::getInstance()->getData($tmp);
				//\App\Videosource\UserFrame::getInstance()->getData($tmp, $userPid);
				//\App\Videosource\Visible::getInstance()->getData($tmp);
				
				try{
					if (!$tmp){
					throw new Exception('Нет tmp');
					}
					if (count($tmp)<>9){
					throw new Exception('Длина не 9');
					}
				} 
				catch (Exception $ex) {
					 echo $ex->getMessage()." --->\n";
					continue;
				}
				$req=preg_split("/\s+/",$tmp[5]);
				parse_str($req[1], $arr);
				try {
					if (!isset($arr['data'])){
					throw new Exception('Нет даты');
					}
				}
				catch (Exception $ex) {
					//echo $ex->getMessage()."\n";
					continue;
				}
				$data=json_decode($arr["data"],true);
				try {
					if ($data["pid"]==0){
					throw new Exception('Событие фрейма');
					}
					if ($data["pid"]==999999){
						//var_dump($data);
					throw new Exception('Наш тест');
					}
					if (!isset($data["page_key"])){
					throw new Exception('Нет волшебного ключа');
					}
					if (!isset($data["event"])){
					throw new Exception('Нет события');
					}
					if (!isset($data["id_src"])){
					throw new Exception('Нет ссылки');
					}
					if (!isset($data["pid"])){
					throw new Exception('Нет пида');
					}
					if (!isset($data["block"])){
					throw new Exception('Нет блока');
					}
				}
				catch (Exception $ex) {
					#echo $ex->getMessage()."\n";
					continue;
				}
				$tmp[0]=preg_replace("/^\[|\]$/","",$tmp[0]);
				$time = strtotime($tmp[0]);
				$datetime=date("Y-m-d H:i:s",$time);
				$ref=$tmp[6];
				$userAgent=$tmp[8];
				$country=$tmp[2];
				$region=$tmp[3];
				
				if (!isset($data['control'])){
					$control=0;
				}
				else{
					$control=$data['control'];
				}
				if(isset($this->cacheEvents[$data["event"]])){
				
				$this->cacheEvents[$data["event"]]=1;
				}
				
				
				if($data["id_src"]==54 || $data["id_src"]==103 || $data["id_src"]==4 || $data["id_src"]==85 || $data["id_src"]==79){
					//var_dump($data);
				}
				//$this->registerEvent($data, $datetime, $userAgent, $country, $region, $ref, $control);
				//\App\MPW\Statistic\Video\StatisticPads::getInstance()->Group($data, $datetime, $userAgent, $country);
				//\App\MPW\Statistic\Video\StatisticPadsOnPid::getInstance()->Group($data, $datetime, $userAgent, $country);
				//\App\MPW\Statistic\Video\StatisticBlocks::getInstance()->Group($data, $datetime);
				//\App\MPW\Statistic\Video\StatisticPids::getInstance()->Group($data, $datetime, $userAgent, $country, $control);	
				
			}
		}
		//var_dump(\App\MPW\Statistic\Video\StatisticPads::getInstance()->group);
		//var_dump(\App\MPW\Statistic\Video\StatisticPadsOnPid::getInstance()->group);
		//var_dump(\App\MPW\Statistic\Video\StatisticBlocks::getInstance()->group);
		//var_dump(\App\MPW\Statistic\Video\StatisticPids::getInstance()->group);
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		$this->insertOnDb();
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
		\App\Videosource\Requests::getInstance()->RegisterData();
				print "старт линкс \n";
		\App\Videosource\Links::getInstance()->RegisterData();
		print "старт pid pad \n";
		
		\App\Videosource\Pid::getInstance()->RegisterData();
		print "старт сайт \n";
		\App\Videosource\Sites::getInstance()->RegisterData();
				print "старт контролс \n";
		\App\Videosource\Controls::getInstance()->RegisterData();
						print "старт сурс \n";
		\App\Videosource\Sources::getInstance()->RegisterData();
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
