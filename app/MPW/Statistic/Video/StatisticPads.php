<?php

namespace App\MPW\Statistic\Video;

use Illuminate\Database\Eloquent\Model;

class StatisticPads extends Model
{	
	
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance===null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public $group=[];
	
	public function Group($data, $datetime, $userAgent, $country){
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
		if (!isset($this->group[$data["page_key"]][$date][$country]))
		$this->group[$data["page_key"]][$date][$country]=[];
		if (!isset($this->group[$data["page_key"]][$date][$country][$device]))
		$this->group[$data["page_key"]][$date][$country][$device]=[];
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]=[];
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['AdStarted']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['AdStarted']=0;
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['started']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['started']=0;
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['played']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['played']=0;
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['midpoint']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['midpoint']=0;
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['third']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['third']=0;
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['complete']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['complete']=0;
		if (!isset($this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['click']))
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['click']=0;
		if ($data['event']=='AdVideoStart' or $data['event']=='AdVideoStart ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['started']++;
		if ($data['event']=='AdStarted' or $data['event']=='AdStarted ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['AdStarted']++;
		if ($data['event']=='AdVideoFirstQuartile' or $data['event']=='AdVideoFirstQuartile ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['played']++;
		if ($data['event']=='AdVideoMidpoint' or $data['event']=='AdVideoMidpoint ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['midpoint']++;
		if ($data['event']=='AdVideoThirdQuartile' or $data['event']=='AdVideoThirdQuartile ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['third']++;
		if ($data['event']=='AdVideoComplete' or $data['event']=='AdVideoComplete ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['complete']++;
		if ($data['event']=='AdClickThru' or $data['event']=='AdClickThru ))')
		$this->group[$data["page_key"]][$date][$country][$device][$data['id_src']]['click']++;
	}
	
	public function insert($data){
		$pdo = \DB::connection('videostatistic')->getPdo();
		$sql="insert into statistic_pads (page_key,date,country,device,id_src,ad_started,started,played,midpoint,third,complete,click)
			select ?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM statistic_pads WHERE page_key=? and date=? and country=? and device=? and id_src=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update statistic_pads set ad_started=?, started=?, played=?, midpoint=?, third=?, complete=?, click=?
		  WHERE page_key=? and date=? and country=? and device=? and id_src=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $page_key=>$dates){
			foreach ($dates as $date=>$countrys){
				foreach ($countrys as $country=>$devices){
					foreach ($devices as $device=>$id_srcs){
						foreach ($id_srcs as $id_src=>$events){
							$sthUpdate->execute([$events['AdStarted'],$events['started'],$events['played'],$events['midpoint'],$events['third'],
							$events['complete'],$events['click'],$page_key,$date,$country,$device,$id_src]);
							$sthInsert->execute([$page_key,$date,$country,$device,$id_src,$events['AdStarted'],$events['started'],$events['played'],
							$events['midpoint'],$events['third'],$events['complete'],$events['click'],$page_key,$date,$country,$device,$id_src]);
						}
					}
				}
			}
		}
	}
	
	public function isertStat($date){
		$pdo = \DB::connection('videostatistic')->getPdo();
		$sql="SELECT date, country, device, id_src, count(page_key) as requested, sum(ad_started) as ad_started, sum(started) as started, 
		sum(played) as played, sum(complete) as completed, sum(click) as clicked, case when 
		(sum(ad_started)<>0) then 100-(round(sum(started)/sum(ad_started)::numeric,4)*100) else 0 end as poteri, case when (sum(played)<>0) then 
		round(sum(complete)/sum(played)::numeric,4)*100 else 0 end as dosm, case when (count(page_key)<>0) then round(sum(played)/count(page_key)
		::numeric,4)*100 else 0 end as util, case when (sum(played)<>0) then round(sum(click)/count(played)::numeric,4)*100 else 0 end as ctr 
		from statistic_pads where date='$date' group by date,id_src,country,device";
		$data=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$pdo = \DB::connection()->getPdo();
		$sql="insert into video_statistic_pads_new_player (date,country,device,id_src,requested,ad_started,started,played,completed,clicked,poteri,
		dosm,util,ctr) select ?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM video_statistic_pads_new_player WHERE date=? and country=? 
		and device=? and id_src=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update video_statistic_pads_new_player set requested=?, ad_started=?, started=?, played=?, completed=?, clicked=?, poteri=?, dosm=?, 
		util=?, ctr=? WHERE date=? and country=? and device=? and id_src=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $pad){
			$sthUpdate->execute([$pad['requested'],$pad['ad_started'],$pad['started'],$pad['played'],$pad['completed'],$pad['clicked'],$pad['poteri'],
			$pad['dosm'],$pad['util'],$pad['ctr'],$pad['date'],$pad['country'],$pad['device'],$pad['id_src']]);
			$sthInsert->execute([$pad['date'],$pad['country'],$pad['device'],$pad['id_src'],$pad['requested'],$pad['ad_started'],$pad['started'],
			$pad['played'],$pad['completed'],$pad['clicked'],$pad['poteri'],$pad['dosm'],$pad['util'],$pad['ctr'],$pad['date'],$pad['country'],
			$pad['device'],$pad['id_src']]);
		}
	}
	
}
