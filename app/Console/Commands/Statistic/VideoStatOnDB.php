<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class VideoStatOnDB extends Command
{
use WithoutOverlapping;
private $padsEvent=[];
private $pads=[];
private $pids=[];
private $pidsControl=[];
private $padsOnPid=[];
private $pidsSum=[];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:new_video';

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
	$pdo = \DB::connection("pgstatistic")->getPdo();
	$from=date('Y-m-d');
	//$from=date('2017-09-13');
	$to=date('Y-m-d',time()+3600*24);
	//$to=date("Y-m-d",strtotime('2017-09-13 + 1 days'));
	echo $from."-".$to."\n";
	$sql="select * from new_video_event_stat where datetime between '$from' and '$to'";
	$datas=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	//общая группировка
	$this->groupOnPads($datas, $from);
	var_dump('я сгруппировал все ключи');
	//группировка для статы по падам
	$this->groupPads($this->padsEvent);
	var_dump('я сгруппировал ключи по ссылкам');
	//группировка для статы по пидам
	$this->groupPids($this->padsEvent);
	var_dump('я сгруппировал ключи по пидам');
	//группировка для статы по пидам суммарная
	$this->groupPidsSum($this->padsEvent);
	var_dump('я сгруппировал ключи по пидам для суммарной статы');
	//группировка для статы по ссылкам и пидам
	$this->groupPidsOnPads($this->padsEvent);
	var_dump('я сгруппировал ключи по ссылка и пидам');
	//запись статы по падам
	$this->insertPads($this->pads, $from);
	var_dump('я записал стату по ссылкам');;
	//запись статы по пидам
	$this->insertPids($this->pids, $this->pidsControl, $from);
	var_dump('я записал стату по пидам');
	//запись статы по ссылкам и пидам
	$this->insertPadsOnPid($this->padsOnPid, $from);
	var_dump('я записал стату по ссылкам и пидам');
	var_dump($this->pidsSum);
	}

	public function groupOnPads($datas, $from){
		foreach ($datas as $data){
			if (!isset($this->padsEvent[$data['page_key']]))
			$this->padsEvent[$data['page_key']]=[];
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]))
			$this->padsEvent[$data['page_key']][$data['id_src']]=[];
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['AdStarted']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['AdStarted']=0;
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['started']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['started']=0;
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['played']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['played']=0;
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['midpoint']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['midpoint']=0;
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['third']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['third']=0;
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['complete']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['complete']=0;
			if (!isset($this->padsEvent[$data['page_key']][$data['id_src']]['click']))
			$this->padsEvent[$data['page_key']][$data['id_src']]['click']=0;
			if ($data['event']=='AdVideoStart' or $data['event']=='AdVideoStart ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['started']++;
			}
			if ($data['event']=='AdStarted' or $data['event']=='AdStarted ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['AdStarted']++;
			}
			if ($data['event']=='AdVideoFirstQuartile' or $data['event']=='AdVideoFirstQuartile ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['played']++;
			}
			if ($data['event']=='AdVideoMidpoint' or $data['event']=='AdVideoMidpoint ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['midpoint']++;
			}
			if ($data['event']=='AdVideoThirdQuartile' or $data['event']=='AdVideoThirdQuartile ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['third']++;
			}
			if ($data['event']=='AdVideoComplete' or $data['event']=='AdVideoComplete ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['complete']++;
			}
			if ($data['event']=='AdClickThru' or $data['event']=='AdClickThru ))'){
				$this->padsEvent[$data['page_key']][$data['id_src']]['click']++;
			}
			if (!isset($this->padsEvent[$data['page_key']]['dop_params']))
			$this->padsEvent[$data['page_key']]['dop_params']=[];
			array_push($this->padsEvent[$data['page_key']]['dop_params'],[$from,$data['pid'],$data['country'],$data['region'],$data['user_agent'],$data['ref'],$data['block_id'],$data['control']]);
		}
	}
	
	public function groupPads($data){
		foreach ($data as $pads){
			if ($pads['dop_params'][0][2]=='RU'){
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
					if (!isset($this->pads['RU']['mobil']))
					$this->pads['RU']['mobil']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->pads['RU']['mobil'][$pad]))
							$this->pads['RU']['mobil'][$pad]=[];
							if (!isset($this->pads['RU']['mobil'][$pad]['AdStarted']))
							$this->pads['RU']['mobil'][$pad]['AdStarted']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['started']))
							$this->pads['RU']['mobil'][$pad]['started']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['played']))
							$this->pads['RU']['mobil'][$pad]['played']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['midpoint']))
							$this->pads['RU']['mobil'][$pad]['midpoint']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['third']))
							$this->pads['RU']['mobil'][$pad]['third']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['complete']))
							$this->pads['RU']['mobil'][$pad]['complete']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['click']))
							$this->pads['RU']['mobil'][$pad]['click']=0;
							if (!isset($this->pads['RU']['mobil'][$pad]['request']))
							$this->pads['RU']['mobil'][$pad]['request']=1;
							else
							$this->pads['RU']['mobil'][$pad]['request']++;
							foreach ($events as $event=>$cnt){
								$this->pads['RU']['mobil'][$pad][$event]+=$cnt;
							}
						}
					}	
				}
				else{
					if (!isset($this->pads['RU']['desctop']))
					$this->pads['RU']['desctop']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->pads['RU']['desctop'][$pad]))
							$this->pads['RU']['desctop'][$pad]=[];
							if (!isset($this->pads['RU']['desctop'][$pad]['AdStarted']))
							$this->pads['RU']['desctop'][$pad]['AdStarted']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['started']))
							$this->pads['RU']['desctop'][$pad]['started']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['played']))
							$this->pads['RU']['desctop'][$pad]['played']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['midpoint']))
							$this->pads['RU']['desctop'][$pad]['midpoint']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['third']))
							$this->pads['RU']['desctop'][$pad]['third']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['complete']))
							$this->pads['RU']['desctop'][$pad]['complete']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['click']))
							$this->pads['RU']['desctop'][$pad]['click']=0;
							if (!isset($this->pads['RU']['desctop'][$pad]['request']))
							$this->pads['RU']['desctop'][$pad]['request']=1;
							else
							$this->pads['RU']['desctop'][$pad]['request']++;
							foreach ($events as $event=>$cnt){
								$this->pads['RU']['desctop'][$pad][$event]+=$cnt;
							}
						}
					}
				}
			}
			else{
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
					if (!isset($this->pads['CIS']['mobil']))
					$this->pads['CIS']['mobil']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->pads['CIS']['mobil'][$pad]))
							$this->pads['CIS']['mobil'][$pad]=[];
							if (!isset($this->pads['CIS']['mobil'][$pad]['AdStarted']))
							$this->pads['CIS']['mobil'][$pad]['AdStarted']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['started']))
							$this->pads['CIS']['mobil'][$pad]['started']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['played']))
							$this->pads['CIS']['mobil'][$pad]['played']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['midpoint']))
							$this->pads['CIS']['mobil'][$pad]['midpoint']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['third']))
							$this->pads['CIS']['mobil'][$pad]['third']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['complete']))
							$this->pads['CIS']['mobil'][$pad]['complete']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['click']))
							$this->pads['CIS']['mobil'][$pad]['click']=0;
							if (!isset($this->pads['CIS']['mobil'][$pad]['request']))
							$this->pads['CIS']['mobil'][$pad]['request']=1;
							else
							$this->pads['CIS']['mobil'][$pad]['request']++;
							foreach ($events as $event=>$cnt){
								$this->pads['CIS']['mobil'][$pad][$event]+=$cnt;
							}
						}
					}	
				}
				else{
					if (!isset($this->pads['CIS']['desctop']))
					$this->pads['CIS']['desctop']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->pads['CIS']['desctop'][$pad]))
							$this->pads['CIS']['desctop'][$pad]=[];
							if (!isset($this->pads['CIS']['desctop'][$pad]['AdStarted']))
							$this->pads['CIS']['desctop'][$pad]['AdStarted']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['started']))
							$this->pads['CIS']['desctop'][$pad]['started']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['played']))
							$this->pads['CIS']['desctop'][$pad]['played']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['midpoint']))
							$this->pads['CIS']['desctop'][$pad]['midpoint']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['third']))
							$this->pads['CIS']['desctop'][$pad]['third']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['complete']))
							$this->pads['CIS']['desctop'][$pad]['complete']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['click']))
							$this->pads['CIS']['desctop'][$pad]['click']=0;
							if (!isset($this->pads['CIS']['desctop'][$pad]['request']))
							$this->pads['CIS']['desctop'][$pad]['request']=1;
							else
							$this->pads['CIS']['desctop'][$pad]['request']++;
							foreach ($events as $event=>$cnt){
								$this->pads['CIS']['desctop'][$pad][$event]+=$cnt;
							}
						}
					}
				}
			}
		}
	}
	
	public function groupPidsOnPads($data){
		foreach ($data as $pads){
			if ($pads['dop_params'][0][2]=='RU'){
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
					if (!isset($this->padsOnPid['RU']['mobil']))
					$this->padsOnPid['RU']['mobil']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->padsOnPid['RU']['mobil'][$pad]))
							$this->padsOnPid['RU']['mobil'][$pad]=[];
							if (!isset($this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]))
							$this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]=[];
							if (!isset($this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]['played']))
							$this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]['played']=0;
							if (!isset($this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]['click']))
							$this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]['click']=0;
							foreach ($events as $event=>$cnt){
								if ($event=='played'){
									$this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]['played']+=$cnt;
								}
								if ($event=='click'){
									$this->padsOnPid['RU']['mobil'][$pad][$pads['dop_params'][0][1]]['click']+=$cnt;
								}
							}
						}
					}
				}
				else{
					if (!isset($this->padsOnPid['RU']['desctop']))
					$this->padsOnPid['RU']['desctop']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->padsOnPid['RU']['desctop'][$pad]))
							$this->padsOnPid['RU']['desctop'][$pad]=[];
							if (!isset($this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]))
							$this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]=[];
							if (!isset($this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]['played']))
							$this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]['played']=0;
							if (!isset($this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]['click']))
							$this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]['click']=0;
							foreach ($events as $event=>$cnt){
								if ($event=='played'){
									$this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]['played']+=$cnt;
								}
								if ($event=='click'){
									$this->padsOnPid['RU']['desctop'][$pad][$pads['dop_params'][0][1]]['click']+=$cnt;
								}
							}
						}
					}
				}
			}
			else{
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
					if (!isset($this->padsOnPid['CIS']['mobil']))
					$this->padsOnPid['CIS']['mobil']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->padsOnPid['CIS']['mobil'][$pad]))
							$this->padsOnPid['CIS']['mobil'][$pad]=[];
							if (!isset($this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]))
							$this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]=[];
							if (!isset($this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]['played']))
							$this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]['played']=0;
							if (!isset($this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]['click']))
							$this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]['click']=0;
							foreach ($events as $event=>$cnt){
								if ($event=='played'){
									$this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]['played']+=$cnt;
								}
								if ($event=='click'){
									$this->padsOnPid['CIS']['mobil'][$pad][$pads['dop_params'][0][1]]['click']+=$cnt;
								}
							}
						}
					}
				}
				else{
					if (!isset($this->padsOnPid['CIS']['desctop']))
					$this->padsOnPid['CIS']['desctop']=[];
					foreach ($pads as $pad=>$events){
						if ($pad!='dop_params'){
							if (!isset($this->padsOnPid['CIS']['desctop'][$pad]))
							$this->padsOnPid['CIS']['desctop'][$pad]=[];
							if (!isset($this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]))
							$this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]=[];
							if (!isset($this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]['played']))
							$this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]['played']=0;
							if (!isset($this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]['click']))
							$this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]['click']=0;
							foreach ($events as $event=>$cnt){
								if ($event=='played'){
									$this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]['played']+=$cnt;
								}
								if ($event=='click'){
									$this->padsOnPid['CIS']['desctop'][$pad][$pads['dop_params'][0][1]]['click']+=$cnt;
								}
							}
						}
					}
				}
			}
		}
	}
	
	public function groupPidsSum($data){
		foreach ($data as $pads){
			
		}
	}
	
	public function groupPids($data){
		foreach ($data as $pads){
			if ($pads['dop_params'][0][7]==1){
				if ($pads['dop_params'][0][2]=='RU'){
					if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
						if (!isset($this->pidsControl['RU']['mobil']))
						$this->pidsControl['RU']['mobil']=[];
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['request']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['played']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['started']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['complete']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['complete']=0;
						if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['click']))
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['click']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]=[];
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['request']))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['request']=1;
								else
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['request']++;
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['played']))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['played']=0;
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['click']))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['click']=0;
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['AdStarted']))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['AdStarted']=0;
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['started']))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['started']=0;
								if (!isset($this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['complete']))
								$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['complete']=0;
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['played']+=$cnt;
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['click']+=$cnt;
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['AdStarted']+=$cnt;
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['started']+=$cnt;
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]][$pad]['complete']+=$cnt;
										$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}									
								}
							}
						}
						$this->pidsControl['RU']['mobil'][$pads['dop_params'][0][1]]['played']+=$played;
					}
					else{
						if (!isset($this->pidsControl['RU']['desctop']))
						$this->pidsControl['RU']['desctop']=[];
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['request']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['played']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['started']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['complete']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['complete']=0;
						if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['click']))
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['click']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]=[];
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['request']))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['request']=1;
								else
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['request']++;
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['played']))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['played']=0;
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['click']))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['click']=0;
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['AdStarted']))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['AdStarted']=0;
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['started']))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['started']=0;
								if (!isset($this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['complete']))
								$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['complete']=0;
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['played']+=$cnt;
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['click']+=$cnt;
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['AdStarted']+=$cnt;
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['started']+=$cnt;
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]][$pad]['complete']+=$cnt;
										$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pidsControl['RU']['desctop'][$pads['dop_params'][0][1]]['played']+=$played;
					}
				}
				else{
					if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
						if (!isset($this->pidsControl['CIS']['mobil']))
						$this->pidsControl['CIS']['mobil']=[];
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['request']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['played']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['started']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['complete']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['complete']=0;
						if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['click']))
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['click']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]=[];
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['request']))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['request']=1;
								else
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['request']++;
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['played']))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['played']=0;
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['click']))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['click']=0;
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['AdStarted']))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['AdStarted']=0;
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['started']))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['started']=0;
								if (!isset($this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['complete']))
								$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['complete']=0;
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['played']+=$cnt;
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['click']+=$cnt;
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['AdStarted']+=$cnt;
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['started']+=$cnt;
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]][$pad]['complete']+=$cnt;
										$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pidsControl['CIS']['mobil'][$pads['dop_params'][0][1]]['played']+=$played;
					}
					else{
						if (!isset($this->pidsControl['CIS']['desctop']))
						$this->pidsControl['CIS']['desctop']=[];
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['request']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['played']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['started']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['complete']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['complete']=0;
						if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['click']))
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['click']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]=[];
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['request']))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['request']=1;
								else
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['request']++;
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['played']))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['played']=0;
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['click']))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['click']=0;
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['AdStarted']))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['AdStarted']=0;
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['started']))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['started']=0;
								if (!isset($this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['complete']))
								$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['complete']=0;
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['played']+=$cnt;
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['click']+=$cnt;
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['AdStarted']+=$cnt;
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['started']+=$cnt;
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]][$pad]['complete']+=$cnt;
										$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pidsControl['CIS']['desctop'][$pads['dop_params'][0][1]]['played']+=$played;
					}
				}
			}
			else{
				if ($pads['dop_params'][0][2]=='RU'){
					if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
						if (!isset($this->pids['RU']['mobil']))
						$this->pids['RU']['mobil']=[];
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['request']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['played']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['click']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['click']=0;
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['started']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['complete']))
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['complete']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pids['RU']['mobil'][$pads['dop_params'][0][1]]['played']+=$played;
					}
					else{
						if (!isset($this->pids['RU']['desctop']))
						$this->pids['RU']['desctop']=[];
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['request']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['played']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['click']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['click']=0;
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['started']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['complete']))
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['complete']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pids['RU']['desctop'][$pads['dop_params'][0][1]]['played']+=$played;
					}
				}
				else{
					if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
						if (!isset($this->pids['CIS']['mobil']))
						$this->pids['CIS']['mobil']=[];
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['request']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['played']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['click']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['click']=0;
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['started']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['complete']))
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['complete']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt!=0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pids['CIS']['mobil'][$pads['dop_params'][0][1]]['played']+=$played;
					}
					else{
						if (!isset($this->pids['CIS']['desctop']))
						$this->pids['CIS']['desctop']=[];
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]=[];
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['request']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['request']=1;
						else
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['request']++;
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['calc_played']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['calc_played']=0;
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['played']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['played']=0;
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['click']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['click']=0;
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['AdStarted']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['AdStarted']=0;
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['started']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['started']=0;
						if (!isset($this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['complete']))
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['complete']=0;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt!=0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pids['CIS']['desctop'][$pads['dop_params'][0][1]]['played']+=$played;
					}
				}
			}
			
			if ($pads['dop_params'][0][2]=='RU'){
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $pads['dop_params'][0][4])){
					if (!isset($this->pidsSum['RU']['mobil']))
					$this->pidsSum['RU']['mobil']=[];
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]=[];
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['request']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['request']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['played']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['played']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['click']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['click']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['started']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['started']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['complete']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['complete']=0;
					if ($pads['dop_params'][0][7]!=1){
						$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['request']++;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['complete']+=$cnt;
									}
								}
							}
						}
						$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['played']+=$played;
					}
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']=[];
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['request']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['request']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['calc_played']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['calc_played']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['played']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['played']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['AdStarted']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['AdStarted']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['started']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['started']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['complete']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['complete']=0;
					if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['click']))
					$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['click']=0;
					if ($pads['dop_params'][0][7]==1){
						$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['request']++;
						$played=0;
						foreach ($pads as $pad=>$events){
							if ($pad!='dop_params'){
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]=[];
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['request']))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['request']=1;
								else
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['request']++;
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['played']))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['played']=0;
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['click']))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['click']=0;
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['AdStarted']))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['AdStarted']=0;
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['started']))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['started']=0;
								if (!isset($this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['complete']))
								$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['complete']=0;
								foreach ($events as $event=>$cnt){
									if ($event=='played'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['played']+=$cnt;
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['calc_played']+=$cnt;
										if ($cnt>0){
											$played=1;
										}
									}
									if ($event=='click'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['click']+=$cnt;
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['click']+=$cnt;
									}
									if ($event=='AdStarted'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['AdStarted']+=$cnt;
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['AdStarted']+=$cnt;
									}
									if ($event=='started'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['started']+=$cnt;
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['started']+=$cnt;
									}
									if ($event=='complete'){
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control'][$pad]['complete']+=$cnt;
										$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['complete']+=$cnt;
									}									
								}
							}
						}
						$this->pidsSum['RU']['mobil'][$pads['dop_params'][0][1]]['control']['played']+=$played;
					}
				}
			}
		}
	}
	
	public function insertPads($data, $from){
		$pdo = \DB::connection()->getPdo();
		$sql="insert into new_video_stat_pads (day,country,device,id_src,requested,ad_started,started,played,midpoint,third,completed,clicked,poteri,dosm,util,ctr)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM new_video_stat_pads WHERE day=? and  country=? and device=? and id_src=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update new_video_stat_pads set requested=?, ad_started=?, started=?, played=?, midpoint=?, third=?, completed=?, clicked=?, poteri=?, dosm=?, util=?, ctr=?
		  WHERE day=? and  country=? and device=? and id_src=?";
		$sthUpdate=$pdo->prepare($sql);	
		foreach ($data as $country=>$devices){
			foreach ($devices as $device=>$pads){
				foreach ($pads as $pad=>$events){
					$poteri=$events['AdStarted']?100-($events['started']/$events['AdStarted'])*100:0;
					$dosm=$events['played']?($events['complete']/$events['played'])*100:0;
					$util=$events['request']?($events['played']/$events['request'])*100:0;
					$ctr=$events['played']?($events['click']/$events['played'])*100:0;
					$sthUpdate->execute([$events['request'],$events['AdStarted'],$events['started'],$events['played'],$events['midpoint'],
					$events['third'],$events['complete'],$events['click'],$poteri,$dosm,$util,$ctr,$from,$country,$device,$pad]);
					$sthInsert->execute([$from,$country,$device,$pad,$events['request'],$events['AdStarted'],$events['started'],$events['played'],
					$events['midpoint'],$events['third'],$events['complete'],$events['click'],$poteri,$dosm,$util,$ctr,$from,$country,$device,$pad]);
				}
			}
		}
	}
	
	public function insertPids($data, $data_control, $from){
		$pdo = \DB::connection()->getPdo();
		$sql="insert into new_video_stat_pids_no_control (day,country,device,pid,requested,ad_started,started,played,calc_played,completed,clicked,deep,poteri,dosm,util,ctr,summa)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM new_video_stat_pids_no_control WHERE day=? and  country=? and device=? and pid=?)";
		$noControlInsert=$pdo->prepare($sql);
		$sql="update new_video_stat_pids_no_control set requested=?, ad_started=?, started=?, played=?, calc_played=?, completed=?, clicked=?, deep=?, poteri=?, dosm=?, util=?, ctr=?, summa=?
		  WHERE day=? and  country=? and device=? and pid=?";
		$noControlUpdate=$pdo->prepare($sql);
		
		$pdo = \DB::connection()->getPdo();
		$sql="insert into new_video_stat_pids_control (day,country,device,pid,requested,ad_started,started,played,calc_played,completed,clicked,deep,poteri,dosm,util,ctr,summa_no_control,summa)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM new_video_stat_pids_control WHERE day=? and  country=? and device=? and pid=?)";
		$controlInsert=$pdo->prepare($sql);
		$sql="update new_video_stat_pids_control set requested=?, ad_started=?, started=?, played=?, calc_played=?, completed=?, clicked=?, deep=?, poteri=?, dosm=?, util=?, ctr=?, summa_no_control=?, summa=?
		  WHERE day=? and  country=? and device=? and pid=?";
		$controlUpdate=$pdo->prepare($sql);
		
		$pdo = \DB::connection()->getPdo();
		$sql="insert into new_video_stat_pids_control_on_pads (day,country,device,id_src,pid,requested,ad_started,started,played,completed,clicked,poteri,dosm,util,ctr,summa)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM new_video_stat_pids_control_on_pads WHERE day=? and  country=? and device=? and id_src=? and pid=?)";
		$controlOnPadsInsert=$pdo->prepare($sql);
		$sql="update new_video_stat_pids_control_on_pads set requested=?, ad_started=?, started=?, played=?, completed=?, clicked=?, poteri=?, dosm=?, util=?, ctr=?, summa=?
		  WHERE day=? and  country=? and device=? and id_src=? and pid=?";
		$controlOnPadsUpdate=$pdo->prepare($sql);
		
		
		
		foreach ($data as $country=>$devices){
			foreach ($devices as $device=>$pids){
				foreach ($pids as $pid=>$events){
					$widget=\App\WidgetVideo::getInstance($pid);
					if (!$widget){
						echo $pid." - такого виджета нет \n";
						continue;
					}
					$deep=$events['played']?$events['calc_played']/$events['played']:0;
					$poteri=$events['AdStarted']?100-($events['started']/$events['AdStarted'])*100:0;
					$dosm=$events['calc_played']?($events['complete']/$events['calc_played'])*100:0;
					$util=$events['request']?($events['played']/$events['request'])*100:0;
					$ctr=$events['calc_played']?($events['click']/$events['calc_played'])*100:0;
					if ($country=='RU'){
						$summa=($events['played']*$widget->commission_rus)/1000;
					}
					else{
						$summa=($events['played']*$widget->commission_cis)/1000;
					}
					$noControlUpdate->execute([$events['request'],$events['AdStarted'],$events['started'],$events['played'],$events['calc_played'],
					$events['complete'],$events['click'],$deep,$poteri,$dosm,$util,$ctr,$summa,$from,$country,$device,$pid]);
					$noControlInsert->execute([$from,$country,$device,$pid,$events['request'],$events['AdStarted'],$events['started'],$events['played'],
					$events['calc_played'],$events['complete'],$events['click'],$deep,$poteri,$dosm,$util,$ctr,$summa,$from,$country,$device,$pid]);
				}
			}
		}
		
		foreach ($data_control as $country=>$devices){
			foreach ($devices as $device=>$pids){
				foreach ($pids as $pid=>$events){
					$widget=\App\WidgetVideo::getInstance($pid);
					if (!$widget){
						echo $pid." - такого виджета нет \n";
						continue;
					}
					$deep=$events['played']?$events['calc_played']/$events['played']:0;
					$poteri=$events['AdStarted']?100-($events['started']/$events['AdStarted'])*100:0;
					$dosm=$events['calc_played']?($events['complete']/$events['calc_played'])*100:0;
					$util=$events['request']?($events['played']/$events['request'])*100:0;
					$ctr=$events['calc_played']?($events['click']/$events['calc_played'])*100:0;
					if ($country=='RU'){
						$defaultSumma=($events['played']*$widget->commission_rus)/1000;
					}
					else{
						$defaultSumma=($events['played']*$widget->commission_cis)/1000;
					}
					$summa=0;
					foreach ($events as $pad=>$padEvents){
						if ($pad!='played' and $pad!='calc_played' and $pad!='AdStarted' and $pad!='started' and $pad!='complete' and $pad!='request' and $pad!='click'){
							$source=\App\VideoSource::getInstance($pad);
							if (!$source){
							echo $pad." - такой ссылки не существует";
							continue;
							}
							if ($country=='RU'){
								$sourceSumma=($padEvents['played']*$source->summa_rus)/1000;
								$summa+=$sourceSumma;
							}
							else{
								$sourceSumma=($padEvents['played']*$source->summa_cis)/1000;
								$summa+=$sourceSumma;
							}
							$poteriSource=$padEvents['AdStarted']?100-($padEvents['started']/$padEvents['AdStarted'])*100:0;
							$dosmSource=$padEvents['played']?($padEvents['complete']/$padEvents['played'])*100:0;
							$utilSource=$padEvents['request']?($padEvents['played']/$padEvents['request'])*100:0;
							$ctrSource=$padEvents['played']?($padEvents['click']/$padEvents['played'])*100:0;			
							$controlOnPadsUpdate->execute([$padEvents['request'],$padEvents['AdStarted'],$padEvents['started'],$padEvents['played'],
							$padEvents['complete'],$padEvents['click'],$poteriSource,$dosmSource,$utilSource,$ctrSource,$sourceSumma,$from,$country,
							$device,$pad,$pid]);
							$controlOnPadsInsert->execute([$from,$country,$device,$pad,$pid,$padEvents['request'],$padEvents['AdStarted'],
							$padEvents['started'],$padEvents['played'],$padEvents['complete'],$padEvents['click'],$poteriSource,$dosmSource,$utilSource,
							$ctrSource,$sourceSumma,$from,$country,$device,$pad,$pid]);
						}
					}
					$controlUpdate->execute([$events['request'],$events['AdStarted'],$events['started'],$events['played'],$events['calc_played'],
					$events['complete'],$events['click'],$deep,$poteri,$dosm,$util,$ctr,$defaultSumma,$summa,$from,$country,$device,$pid]);
					$controlInsert->execute([$from,$country,$device,$pid,$events['request'],$events['AdStarted'],$events['started'],$events['played'],
					$events['calc_played'],$events['complete'],$events['click'],$deep,$poteri,$dosm,$util,$ctr,$defaultSumma,$summa,$from,$country,$device,$pid]);
				}
			}
		}
	}
	
	public function insertPadsOnPid($data, $from){
		$pdo = \DB::connection()->getPdo();
		$sql="insert into new_video_stat_pads_on_pid (day,country,device,id_src,pid,played,clicked,ctr)
			select ?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM new_video_stat_pads_on_pid WHERE day=? and  country=? and device=? and id_src=? and pid=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update new_video_stat_pads_on_pid set played=?, clicked=?, ctr=?
		  WHERE day=? and  country=? and device=? and id_src=? and pid=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $country=>$devices){
			foreach ($devices as $device=>$pads){
				foreach ($pads as $pad=>$pids){
					foreach ($pids as $pid=>$events){
						$ctr=$events['played']?($events['click']/$events['played'])*100:0;
						$sthUpdate->execute([$events['played'],$events['click'],$ctr,$from,$country,$device,$pad,$pid]);
						$sthInsert->execute([$from,$country,$device,$pad,$pid,$events['played'],$events['click'],$ctr,$from,$country,$device,$pad,$pid]);
					}
				}
			}
		}
	}
  	
}
