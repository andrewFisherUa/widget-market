<?php

namespace App\MPW\Statistic\Video;

use Illuminate\Database\Eloquent\Model;

class StatisticPids extends Model
{	
	
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance===null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public $group=[];
	
	public function Group($data, $datetime, $userAgent, $country, $control){
		if ($country=="RU"){
			$country="RU";
		}
		else{
			$country="CIS";
		}
		
		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))|(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)){
			$device="mobile";
		}
		else{
			$device="desctop";
		}
		$date=date("Y-m-d", strtotime($datetime));
		
		if (!isset($this->group[$data["page_key"]]))
		$this->group[$data["page_key"]]=[];
		if (!isset($this->group[$data["page_key"]][$date]))
		$this->group[$data["page_key"]][$date]=[];
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']]))
		$this->group[$data["page_key"]][$date][$data['pid']]=[];
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country]))
		$this->group[$data["page_key"]][$date][$data['pid']][$country]=[];
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device]))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device]=[];
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]=[];
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['AdStarted']))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['AdStarted']=0;
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['started']))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['started']=0;
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['played']))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['played']=0;
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['complete']))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['complete']=0;
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['click']))
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['click']=0;
		
		if (!isset($this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['control']))
		if ($control==1)
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['control']=1;
		else
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['control']=0;

		if ($data['event']=='AdStarted' or $data['event']=='AdStarted ))')
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['AdStarted']++;
		if ($data['event']=='AdVideoStart' or $data['event']=='AdVideoStart ))')
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['started']++;
		if ($data['event']=='AdVideoFirstQuartile' or $data['event']=='AdVideoFirstQuartile ))')
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['played']++;
		if ($data['event']=='AdVideoComplete' or $data['event']=='AdVideoComplete ))')
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['complete']++;
		if ($data['event']=='AdClickThru' or $data['event']=='AdClickThru ))')
		$this->group[$data["page_key"]][$date][$data['pid']][$country][$device][$data['id_src']]['click']++;
	}
	
	public function insert($data){
		$pdo = \DB::connection('videostatistic')->getPdo();
		$sql="insert into statistic_pids (page_key,date,pid,country,device,id_src,ad_started,started,played,complete,click,control) 
		select ?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM statistic_pids WHERE page_key=? and date=? and pid=? and country=? and device=? and id_src=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update statistic_pids set ad_started=?, started=?, played=?, complete=?, click=?, control=? 
		WHERE page_key=? and date=? and pid=? and country=? and device=? and id_src=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $page_key=>$dates){
			foreach ($dates as $date=>$pids){
				foreach ($pids as $pid=>$countrys){
					foreach ($countrys as $country=>$devices){
						foreach ($devices as $device=>$id_srcs){
							foreach ($id_srcs as $id_src=>$events){
								$sthUpdate->execute([$events['AdStarted'],$events['started'],$events['played'],$events['complete'],$events['click'],
								$events['control'],$page_key,$date,$pid,$country,$device,$id_src]);
								$sthInsert->execute([$page_key,$date,$pid,$country,$device,$id_src,$events['AdStarted'],$events['started'],
								$events['played'],$events['complete'],$events['click'],$events['control'],$page_key,$date,$pid,$country,$device,$id_src]);
							}
						}
					}
				}
			}
		}
	}
	
	public function isertStat($date){
		/*то что закоменчено это на без контрольных, открытая для всех*/
		$pdo = \DB::connection('videostatistic')->getPdo();
		/*$sql="create temp table tmp_pids_stat as SELECT date, country, page_key, device, pid, sum(ad_started) as ad_started, sum(started) as started, 
		sum(played) as calc_played, case when (sum(played)>0) then 1 else 0 end as played, sum(complete) as completed, sum(click) as clicked from 
		statistic_pids where date='$date' and control='0' group by page_key,date,country,device,pid";*/
		$sql="create temp table tmp_pids_stat as SELECT date, country, page_key, device, pid, sum(ad_started) as ad_started, sum(started) as started, 
		sum(played) as calc_played, case when (sum(played)>0) then 1 else 0 end as played, sum(complete) as completed, sum(click) as clicked from 
		statistic_pids where date='$date' group by page_key,date,country,device,pid";
		$pdo->exec($sql);
		$sql="select date,country, device, pid, count(page_key) as requested, sum(ad_started) as ad_started, sum(started) as started, sum(calc_played) as 
		calc_played, sum(played) as played, sum(completed) as completed, sum(clicked) as clicked, case when (sum(played)<>0) then 
		round(sum(calc_played)/sum(played)::numeric,4) else 0 end as deep, case when (sum(ad_started)<>0) then 100-(round(sum(started)/sum(ad_started)
		::numeric,4)*100) else 0 end as poteri, case when (sum(calc_played)<>0) then round(sum(completed)/sum(calc_played)::numeric,4)*100 else 0 end 
		as dosm, case when (count(page_key)<>0) then round(sum(played)/count(page_key)::numeric,4)*100 else 0 end as util, case when 
		(sum(calc_played)<>0) then round(sum(clicked)/sum(calc_played)::numeric,4)*100 else 0 end as ctr from tmp_pids_stat where date='$date' 
		group by date,country,device,pid;";
		$data=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$pdo = \DB::connection()->getPdo();
		$sql="insert into video_statistic_pids_new_player (date,country,device,pid,requested,ad_started,started,calc_played,played,completed,clicked,deep,
		poteri,dosm,util,ctr,summa) select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM video_statistic_pids_new_player WHERE date=? 
		and country=? and device=? and pid=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update video_statistic_pids_new_player set requested=?, ad_started=?, started=?, calc_played=?, played=?, completed=?, clicked=?, deep=?, poteri=?,
		dosm=?,	util=?, ctr=?, summa=? WHERE date=? and country=? and device=? and pid=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $pad){
			$widget=\App\WidgetVideo::getInstance($pad['pid']);
			if (!$widget){
				echo $pad['pid']." - такого виджета нет \n";
				continue;
			}
			if ($pad['country']=='RU'){
				$summa=($pad['played']*\App\WidgetVideo::getInstance($pad['pid'])->commission_rus)/1000;
				}
			else{
				$summa=($pad['played']*\App\WidgetVideo::getInstance($pad['pid'])->commission_cis)/1000;
			}
			
			$sthUpdate->execute([$pad['requested'],$pad['ad_started'],$pad['started'],$pad['calc_played'],$pad['played'],$pad['completed'],$pad['clicked'],$pad['deep'],
			$pad['poteri'],$pad['dosm'],$pad['util'],$pad['ctr'],$summa,$pad['date'],$pad['country'],$pad['device'],$pad['pid']]);
			$sthInsert->execute([$pad['date'],$pad['country'],$pad['device'],$pad['pid'],$pad['requested'],$pad['ad_started'],$pad['started'],
			$pad['calc_played'],$pad['played'],$pad['completed'],$pad['clicked'],$pad['deep'],$pad['poteri'],$pad['dosm'],$pad['util'],$pad['ctr'],$summa,
			$pad['date'],$pad['country'],$pad['device'],$pad['pid']]);
		}
	}
	
}
