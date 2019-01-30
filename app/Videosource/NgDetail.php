<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class NgDetail // extends Model
{
	
	public function StartDay($from=null){
		if(!$from){
			$from=date("Y-m-d");
		}
		$this->prepareData($from);
	}
	
	public function prepareData($from){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql = "select pid, 
		day, 
		country, 
		substring(url from '.*://([^/]*)' ) as host, 
		count(*) as requested, 
		sum(played) as played, 
		sum(case when played>0 then 1 else 0 end) as calc_played, 
		case when sum(played)>0 then round(sum(played)/sum(case when played>0 then 1 else 0 end)::numeric,4) else 0 end as depth, 
		case when sum(played)>0 then round(sum(case when played>0 then 1 else 0 end)/count(*)::numeric,4)*100 else 0 end as util, 
		case when sum(played)>0 then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as completed, sum(clicks) as clicks, 
		case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr, 
		round(count(distinct ip)/coalesce(count(ip),1)::numeric,4) as bot 
		from stat_user_pages where day='$from' and pid in ('1461','1485', '1513', '1517', '1597') group by pid, day, country, substring(url from '.*://([^/]*)' )";
		
		/*$sql = "select pid, 
		day, 
		country, 
		regexp_replace(replace(replace(replace(url,'http://',''),'https://',''),'//',''), '\/.+$', '', 'g') as host, 
		count(*) as requested, 
		sum(played) as played, 
		sum(case when played>0 then 1 else 0 end) as calc_played, 
		case when sum(played)>0 then round(sum(played)/sum(case when played>0 then 1 else 0 end)::numeric,4) else 0 end as depth, 
		case when sum(played)>0 then round(sum(case when played>0 then 1 else 0 end)/count(*)::numeric,4)*100 else 0 end as util, 
		case when sum(played)>0 then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as completed, sum(clicks) as clicks, 
		case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr, 
		round(count(distinct ip)/coalesce(count(ip),1)::numeric,4) as bot 
		from stat_user_pages where day='$from' and pid in ('1461','1485', '1513', '1517') group by pid, day, country, substring(url from '.*://([^/]*)' )";
		*/
		$stats=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		
		$sql="insert into ngs_details (
		pid
		,day
		,country
		,host
		,requested
		,played
		,calc_played
		,depth
		,util
		,completed
		,clicks
		,ctr
		,bot)
		select ?,?,?,?,?,?,?,?,?,?,?,?,? 
		WHERE NOT EXISTS (SELECT 1 FROM ngs_details WHERE pid=? and day =? and country =? and host=?) ";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update ngs_details set 
		requested=? 
		,played=?
		,calc_played=?
		,depth=?
		,util=?
		,completed=?
		,clicks=?
		,ctr=?
		,bot=?
		WHERE pid=? and day=? and country=? and host=?";
		$sthUpdatePids=$pdo->prepare($sql);
		
		foreach ($stats as $stat){
			if (!$stat->host) $stat->host='Не определен';
			if ($stat->completed>100) $stat->completed=100;
			$sthUpdatePids->execute([
			$stat->requested
			,$stat->played
			,$stat->calc_played
			,$stat->depth
			,$stat->util
			,$stat->completed
			,$stat->clicks
			,$stat->ctr
			,$stat->bot
			,$stat->pid
			,$stat->day
			,$stat->country
			,$stat->host
			]);

			$sthInsertPids->execute([
			$stat->pid
			,$stat->day
			,$stat->country
			,$stat->host
			,$stat->requested
			,$stat->played
			,$stat->calc_played
			,$stat->depth
			,$stat->util
			,$stat->completed
			,$stat->clicks
			,$stat->ctr
			,$stat->bot
			,$stat->pid
			,$stat->day
			,$stat->country
			,$stat->host
			]);
		}
	}
}

