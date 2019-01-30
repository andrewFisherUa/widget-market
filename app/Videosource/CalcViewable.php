<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class CalcViewable // extends Model
{
	
	public $viewable=[];
	public function StartDay($from=null,$to=null){
		if(!$from || !$to){
			$from=$to=date("Y-m-d");
		}
		
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select pid, sum(first_quartile) as played, case when (sum(start+first_quartile+midpoint+third_quartile+complete)>0) then 
		round(sum(viewable)/sum(start+first_quartile+midpoint+third_quartile+complete)::numeric,4)*100 else 0 end as viewable 
		from stat_viewable group by country, pid order by viewable desc";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($data as $d){
			if ($d['pid']==0){
				continue;
			}
			$viewable[$from][$d['pid']]['viewable']=$d['viewable'];
		}
		var_dump($viewable);
		exit;
		
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="insert into pid_summa_full (pid
		,day
		,country
		,summa
		,control_summa
		,loaded
		,control_loaded
		,played
		,control_played
		,calculate
		,control_calculate
		,one_played
		,control_one_played
		,completed
		,control_completed
		,clicks
		,control_clicks
		,started
		,control_started
		,second_expensive_all
		,second_expensive
		,second_expensive_summa
		,second_cheap_all
		,second_cheap
		,second_cheap_summa
		,lease_summa,
		coef)
		select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? 
		WHERE NOT EXISTS (SELECT 1 FROM pid_summa_full WHERE pid=? and day =? and country =? ) ";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update pid_summa_full set 
		summa=? 
		,control_summa =?
		,loaded=?
		,control_loaded=?
		,played=?
		,control_played=?
		,calculate=?
		,control_calculate=?
		,one_played=?
		,control_one_played=?
		,completed=?
		,control_completed=?
		,clicks=?
		,control_clicks=?
		,started=?
		,control_started=?
		,second_expensive_all=?
		,second_expensive=?
		,second_expensive_summa=?
		,second_cheap_all=?
		,second_cheap=?
		,second_cheap_summa=?
		,lease_summa=?
		,coef=?
		WHERE pid=? and day =? and country =? ";
		$sthUpdatePids=$pdo->prepare($sql);
		foreach($this->pids as $day=>$pids){
			foreach($pids as $pid=>$countries){
				foreach($countries as $country=>$sum){
					if(!isset($sum["summa"])) $sum["summa"]=0;
					if(!isset($sum["control_summa"])) $sum["control_summa"]=0;
					if(!isset($sum["loaded"])) $sum["loaded"]=0;
					if(!isset($sum["control_loaded"])) $sum["control_loaded"]=0;
					if(!isset($sum["played"])) $sum["played"]=0;
					if(!isset($sum["control_played"])) $sum["control_played"]=0;
					if(!isset($sum["calculate"])) $sum["calculate"]=0;
					if(!isset($sum["control_calculate"])) $sum["control_calculate"]=0;
					if(!isset($sum["one_played"])) $sum["one_played"]=0;
					if(!isset($sum["control_one_played"])) $sum["control_one_played"]=0;
					if(!isset($sum["completed"])) $sum["completed"]=0;
					if(!isset($sum["control_completed"])) $sum["control_completed"]=0;
					if(!isset($sum["clicks"])) $sum["clicks"]=0;
					if(!isset($sum["control_clicks"])) $sum["control_clicks"]=0;
					if(!isset($sum["started"])) $sum["started"]=0;
					if(!isset($sum["control_started"])) $sum["control_started"]=0;
					if(!isset($sum["second_expensive_all"])) $sum["second_expensive_all"]=0;
					if(!isset($sum["second_expensive"])) $sum["second_expensive"]=0;
					if(!isset($sum["second_expensive_summa"])) $sum["second_expensive_summa"]=0;
					if(!isset($sum["second_cheap_all"])) $sum["second_cheap_all"]=0;
					if(!isset($sum["second_cheap"])) $sum["second_cheap"]=0;
					if(!isset($sum["second_cheap_summa"])) $sum["second_cheap_summa"]=0;
					if(!isset($sum["lease_summa"])) $sum["lease_summa"]=0;
					if(!isset($sum["coef"])) $sum["coef"]=0;
					if ($pid=='1271' and $country=='RU'){
						$sum["control_calculate"]=floor($sum["control_summa"]/75*1000);
						var_dump($sum["control_calculate"]);
					}
					if ($pid=='1321' and $country=='RU'){
						$sum["control_calculate"]=floor($sum["control_summa"]/50*1000);
					}
					if ($pid=='1322' and $country=='RU'){
						$sum["control_calculate"]=floor($sum["control_summa"]/50*1000);
					}
					if ($pid=='1323' and $country=='RU'){
						$sum["control_calculate"]=floor($sum["control_summa"]/50*1000);
					}
					if ($pid=='1324' and $country=='RU'){
						$sum["control_calculate"]=floor($sum["control_summa"]/50*1000);
					}
					if ($pid=='1380' and $country=='RU'){
						$sum["control_calculate"]=floor($sum["control_summa"]/50*1000);
					}
					$sthUpdatePids->execute([
					$sum["summa"]
					,$sum["control_summa"]
					,$sum["loaded"]
					,$sum["control_loaded"]
					,$sum["played"]
					,$sum["control_played"]
					,$sum["calculate"]
					,$sum["control_calculate"]
					,$sum["one_played"]
					,$sum["control_one_played"]
					,$sum["completed"]
					,$sum["control_completed"]
					,$sum["clicks"]
					,$sum["control_clicks"]
					,$sum["started"]
					,$sum["control_started"]
					,$sum["second_expensive_all"]
					,$sum["second_expensive"]
					,$sum["second_expensive_summa"]
					,$sum["second_cheap_all"]
					,$sum["second_cheap"]
					,$sum["second_cheap_summa"]
					,$sum["lease_summa"]
					,$sum["coef"]
					,$pid,$day,$country]);
					
					$sthInsertPids->execute([
					$pid
					,$day
					,$country
					,$sum["summa"]
					,$sum["control_summa"]
					,$sum["loaded"]
					,$sum["control_loaded"]
					,$sum["played"]
					,$sum["control_played"]
					,$sum["calculate"]
					,$sum["control_calculate"]
					,$sum["one_played"]
					,$sum["control_one_played"]
					,$sum["completed"]
					,$sum["control_completed"]
					,$sum["clicks"]
					,$sum["control_clicks"]
					,$sum["started"]
					,$sum["control_started"]
					,$sum["second_expensive_all"]
					,$sum["second_expensive"]
					,$sum["second_expensive_summa"]
					,$sum["second_cheap_all"]
					,$sum["second_cheap"]
					,$sum["second_cheap_summa"]
					,$sum["lease_summa"]
					,$sum["coef"]
					,$pid
					,$day
					,$country]);
				}
			}
		}
		$this->makeLinks($from,$to);
	}
	
	public function prepareData(){
		$pda = \DB::connection()->getPdo();
		$sql="select t.id 
			,t.commission_rus 
			,t.commission_cis 
			,k_ru.value as com_ru 
			,k_cis.value as com_cis 
			from widget_videos t 
			
			inner join сommission_groups as k_ru 
			on k_ru.commissiongroupid = t.commission_rus 
			inner join сommission_groups as k_cis 
			on k_cis.commissiongroupid = t.commission_cis";
		$comissian=$pda->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		foreach($comissian as $pid){
			$this->widcomission[$pid->id]=$pid;
		}
		$sql="select t1.user_id, t1.link_id, t1.summa_rus, t1.summa_cis, t3.id as pid from user_link_summas t1 left join 
		(select id,user_id from widgets) t2 on t1.user_id=t2.user_id left join (select id, wid_id from widget_videos) 
		t3 on t2.id=t3.wid_id";
		$pid_links=$pda->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		foreach ($pid_links as $pid_link){
			if (!$pid_link->pid){
				continue;
			}
			$this->pidlinkcontrol[$pid_link->pid][$pid_link->link_id]['summa_rus']=$pid_link->summa_rus;
			$this->pidlinkcontrol[$pid_link->pid][$pid_link->link_id]['summa_cis']=$pid_link->summa_cis;
		}
		
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select * from links";
		$comissionSource=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		foreach($comissionSource as $src){
			$this->linkcomission[$src->id]=$src;
		}
		$this->glubinacommission["rus"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000000')->first();
		$this->glubinacommission["cis"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000001')->first();
		$this->cheapcommission["rus"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000002')->first();
		$this->cheapcommission["cis"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000003')->first();
	}
	
	private function makeCommon($from=null,$to=null){
		$pdo = \DB::connection("videotest")->getPdo();
		if(!$from || !$to){
			$from=$to=$date("Y-m-d");
		}
		$sql="select day,pid,country,
			count(*) as loaded,
			count(CASE WHEN played>0 THEN 1 END) as calculate,
			sum(played) as played,
			sum(completed) as completed,
			sum(clicks) as clicks,
			sum(start) as start,
			count(CASE WHEN second_round>0 THEN 1 END) as second,
			sum(second_round) as second_all,
			count(CASE WHEN second_cheap>0 THEN 1 END) as second_cheap,
			sum(second_cheap) as second_cheap_all,
			case when cast(count(CASE WHEN played>0 THEN 1 END ) as double precision) >0 then  cast(sum(played) as double 
			precision)/cast(count(CASE WHEN played>0 THEN 1 END ) as double precision)else 0 end  as deep,
			case when cast(count(*) as double precision) >0 then  count(CASE WHEN played>0 THEN 1 END)*100/cast(count(*) as double 
			precision) else 0 end as util,
			count(distinct ip) as count_ip,
			lease as lease
			from  stat_user_pages
			where day between '$from' and '$to' and control='0'
			group by day,pid,country,lease";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			if ($d['day']=='2017-11-23' or $d['day']=='2017-11-22' or $d['day']=='2017-11-24'){
				$d['calculate']=floor($d['calculate']*0.7);
			}
			if ($d['day']=='2017-11-25'){
				$d['calculate']=floor($d['calculate']*0.65);
			}
			if ($d['day']=='2017-12-01'){
				$d['calculate']=floor($d['calculate']*0.55);
			}

			if ($d['day']=='2017-12-11'){
				if ($d['pid']!=1034 and $d['pid']!=1043 and $d['pid']!=1052){
					$d['calculate']=floor($d['calculate']*0.8);
				}
			}
			if ($d['day']=='2017-12-12'){
				if ($d['pid']!=1034 and $d['pid']!=1043 and $d['pid']!=1052){
					$d['calculate']=floor($d['calculate']*0.9);
				}
			}
			if ($d['day']=='2017-12-05'){
				if ($d['pid']!=445 and $d['pid']!=446 and $d['pid']!=965 and $d['pid']!=966 and $d['pid']!=762 and $d['pid']!=540 and $d['pid']!=960 and $d['pid']!=503 and $d['pid']!=526){
					$d['calculate']=floor($d['calculate']*0.9);
				}
			}
			if ($d['day']=='2017-12-06'){
				if ($d['pid']!=445 and $d['pid']!=446 and $d['pid']!=965 and $d['pid']!=966 and $d['pid']!=762 and $d['pid']!=540 and $d['pid']!=960 and $d['pid']!=503 and $d['pid']!=526){
					$d['calculate']=floor($d['calculate']*0.75);
				}
			}
			if ($d['day']=='2017-12-07'){
				if ($d['pid']!=445 and $d['pid']!=446 and $d['pid']!=965 and $d['pid']!=966 and $d['pid']!=762 and $d['pid']!=540 and $d['pid']!=960 and $d['pid']!=503 and $d['pid']!=526){
					$d['calculate']=floor($d['calculate']*0.5);
				}
			}
			if ($d['day']=='2017-12-09'){
				if ($d['pid']!=764 and $d['pid']!=1040 and $d['pid']!=748 and $d['pid']!=749 and $d['pid']!=808 and $d['pid']!=702 and $d['pid']!=1028 and $d['pid']!=1029 and $d['pid']!=1035 and $d['pid']!=1025 and $d['pid']!=1026 and $d['pid']!=1064 and $d['pid']!=1059 and $d['pid']!=985 and $d['pid']!=299 and $d['pid']!=300 and $d['pid']!=386 and $d['pid']!=387 and $d['pid']!=952 and $d['pid']!=520 and $d['pid']!=656){
					$d['calculate']=floor($d['calculate']*0.75);
				}
			}
			if ($d['day']=='2017-12-10'){
				if ($d['pid']!=764 and $d['pid']!=1040 and $d['pid']!=748 and $d['pid']!=749 and $d['pid']!=808 and $d['pid']!=702 and $d['pid']!=1028 and $d['pid']!=1029 and $d['pid']!=1035 and $d['pid']!=1025 and $d['pid']!=1026 and $d['pid']!=1064 and $d['pid']!=1059 and $d['pid']!=985 and $d['pid']!=299 and $d['pid']!=300 and $d['pid']!=386 and $d['pid']!=387 and $d['pid']!=952 and $d['pid']!=520 and $d['pid']!=656){
					$d['calculate']=floor($d['calculate']*0.8);
				}
			}
			if ($d['day']=='2017-12-16'){
				if ($d['pid']!=1020 and $d['pid']!=748 and $d['pid']!=749 and $d['pid']!=702 and $d['pid']!=252 and $d['pid']!=272 
				and $d['pid']!=985 and $d['pid']!=299 and $d['pid']!=300 and $d['pid']!=933 and $d['pid']!=455 and $d['pid']!=460 
				and $d['pid']!=1085 and $d['pid']!=1038 and $d['pid']!=1095 and $d['pid']!=376 and $d['pid']!=1065){
					if ($d['pid']==451 or $d['pid']==452 or $d['pid']==453 or $d['pid']==591 or $d['pid']==592 or $d['pid']==593 or $d['pid']==643 or $d['pid']==644 
					or $d['pid']==645 or $d['pid']==646 or $d['pid']==647 or $d['pid']==648 or $d['pid']==649 or $d['pid']==650 or $d['pid']==651 or $d['pid']==652 or $d['pid']==653){
						$d['calculate']=floor($d['calculate']*0.74);
					}
					else{
						$d['calculate']=floor($d['calculate']*0.8);
					}
				}
			}
			if ($d['day']=='2017-12-17'){
				if ($d['pid']!=1020 and $d['pid']!=748 and $d['pid']!=749 and $d['pid']!=702 and $d['pid']!=252 and $d['pid']!=272 
				and $d['pid']!=985 and $d['pid']!=299 and $d['pid']!=300 and $d['pid']!=933 and $d['pid']!=455 and $d['pid']!=460 
				and $d['pid']!=1085 and $d['pid']!=1038 and $d['pid']!=1095 and $d['pid']!=376 and $d['pid']!=1065){
					if ($d['pid']==451 or $d['pid']==452 or $d['pid']==453 or $d['pid']==591 or $d['pid']==592 or $d['pid']==593 or $d['pid']==643 or $d['pid']==644 
					or $d['pid']==645 or $d['pid']==646 or $d['pid']==647 or $d['pid']==648 or $d['pid']==649 or $d['pid']==650 or $d['pid']==651 or $d['pid']==652 or $d['pid']==653){
						$d['calculate']=floor($d['calculate']*0.86);
					}
					else{
						$d['calculate']=floor($d['calculate']*0.91);
					}
				}
			}
			if ($d['day']=='2018-01-07'){
				if ($d['pid']!=1019 and $d['pid']!=376 and $d['pid']!=376 and $d['pid']!=1065 and $d['pid']!=717 and $d['pid']!=193 
				and $d['pid']!=997 and $d['pid']!=967 and $d['pid']!=1128 and $d['pid']!=1103 and $d['pid']!=1101 and $d['pid']!=416 and $d['pid']!=446 
				and $d['pid']!=1024 and $d['pid']!=658 and $d['pid']!=1056 and $d['pid']!=99 and $d['pid']!=445 and $d['pid']!=556 
				and $d['pid']!=555 and $d['pid']!=558 and $d['pid']!=395 and $d['pid']!=580){
				$d['calculate']=floor($d['calculate']*0.7);
				}
			}
			if ($d['day']=='2018-01-08'){
				if ($d['pid']!=1019 and $d['pid']!=376 and $d['pid']!=376 and $d['pid']!=1065 and $d['pid']!=717 and $d['pid']!=193 
				and $d['pid']!=997 and $d['pid']!=967 and $d['pid']!=1128 and $d['pid']!=1103 and $d['pid']!=1101 and $d['pid']!=416 and $d['pid']!=446 
				and $d['pid']!=1024 and $d['pid']!=658 and $d['pid']!=1056 and $d['pid']!=99 and $d['pid']!=445 and $d['pid']!=556 
				and $d['pid']!=555 and $d['pid']!=558 and $d['pid']!=395 and $d['pid']!=580){
				$d['calculate']=floor($d['calculate']*0.6);
				}
			}
			if ($d['day']=='2018-01-07' or $d['day']=='2018-01-08'){
				if ($d['pid']==656){
					$d['calculate']=floor($d['calculate']*0.5);
				}
				if ($d['pid']==1108){
					$d['calculate']=floor($d['calculate']*0.8);
				}
			}
			if ($d['day']>'2018-01-08' and $d['day']<'2018-01-18'){
				$d['calculate']=floor($d['calculate']*0.8);
			}
			//это для цы
			/*if ($d['day']>'2018-03-22'){
				if ($d['pid']==1321 or $d['pid']==1322 or $d['pid']==1323 or $d['pid']==1324){
					$d['calculate']=floor($d['calculate']*0.9);
				}
			}*/
			if(isset($this->widcomission[$d["pid"]])){
				if($d["country"]=="RU")
				$val=round($this->widcomission[$d["pid"]]->com_ru/1000,4)*$d["calculate"];
				else
				$val=round($this->widcomission[$d["pid"]]->com_cis/1000,4)*$d["calculate"]; 
				$d["summa"]=$val;
			}
			else{
				$d["summa"]=0;
			}
			if ($d["country"]=="RU"){
				$d["second_summa"]=round($this->glubinacommission["rus"]->value/1000,4)*$d["second"];
			}
			else{
				$d["second_summa"]=round($this->glubinacommission["cis"]->value/1000,4)*$d["second"];
			}
			if ($d["country"]=="RU"){
				$d["second_cheap_summa"]=round($this->cheapcommission["rus"]->value/1000,4)*$d["second_cheap"];
			}
			else{
				$d["second_cheap_summa"]=round($this->cheapcommission["cis"]->value/1000,4)*$d["second_cheap"];
			}
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["summa"]=$d["summa"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["loaded"]=$d["loaded"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["played"]=$d["played"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["calculate"]=$d["calculate"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["one_played"]=$d["calculate"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["completed"]=$d["completed"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["clicks"]=$d["clicks"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["started"]=$d["start"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_expensive_all"]=$d["second_all"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_expensive"]=$d["second"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_expensive_summa"]=$d["second_summa"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap_all"]=$d["second_cheap_all"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap"]=$d["second_cheap"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap_summa"]=$d["second_cheap_summa"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["coef"]=1-$d["count_ip"]/$d["loaded"];
			if ($d["pid"]==1513 or $d["pid"]==1517){
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_expensive_summa"]=0;
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap_summa"]=0;
			}
			
			
			if ($d["lease"]==1){
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"]=$d["summa"]+$d["second_summa"]+$d["second_cheap_summa"];
			}
			else{
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"]=0;
			}
			
		}
	}
	
	private function makeControl($from=null,$to=null){
		$pdo = \DB::connection("videotest")->getPdo();
		if(!$from || !$to){
			$from=$to=date("Y-m-d");
		}
		$sql="select day,pid,country,id_src,sum(cnt)as cnt from stat_control 
		where day between '$from' and '$to' and control='1' group by day,pid,country,id_src";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			if(!isset($this->linkcomission[$d["id_src"]])){
				$val=0;
			}
			if ($d['day']=='2017-11-23' or $d['day']=='2017-11-22' or $d['day']=='2017-11-24'){
				$d['cnt']=floor($d['cnt']*0.8);
			}
			if ($d['day']=='2017-11-25'){
				$d['cnt']=floor($d['cnt']*0.75);
			}
			if ($d['day']=='2017-12-01'){
				$d['cnt']=floor($d['cnt']*0.65);
			}
			
			
			if ($d['day']=='2017-12-05'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.7);
				}
			}
			if ($d['day']=='2017-12-06'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.55);
				}
			}
			if ($d['day']=='2017-12-07'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.4);
				}
			}
			if ($d['day']=='2017-12-08'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.3);
				}
			}
			if ($d['day']=='2017-12-09'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.5);
				}
			}
			if ($d['day']=='2017-12-10'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.5);
				}
			}
			if ($d['day']>'2017-12-10' and $d['day']<'2018-01-01'){
				if ($d['pid']==974 and $d['id_src']==73){
					var_dump('ютраф');
					$d['cnt']=floor($d['cnt']*0.5);
				}
			}
			if ($d['day']>'2017-12-31'){
				if ($d['pid']==974 and $d['id_src']==73){
					var_dump('ютраф1');
					//var_dump($d['cnt']);
					$d['cnt']=floor($d['cnt']*0.4);
					//var_dump($d['cnt']);
				}
			}
			if ($d['day']>'2018-03-25'){
				if ($d['pid']==1321 or $d['pid']==1322 or $d['pid']==1323 or $d['pid']==1324 or $d['pid']==1380){
					if ($d['id_src']==73){
						$d['cnt']=floor($d['cnt']*0.5);
					}
				}
			}
			if ($d['day']=='2017-12-11'){
				if ($d['id_src']==1 or $d['id_src']==2){
					$d['cnt']=floor($d['cnt']*0.5);
				}
			}
			if ($d['day']=='2018-01-08' and $d['pid']==974){
				$d['cnt']=floor($d['cnt']*0.8);
			}
			if ($d['day']>'2018-01-08' and $d['day']<'2018-01-18'){
				if ($d['pid']!=1143){
				$d['cnt']=floor($d['cnt']*0.9);
				}
			}
			if ($d['pid']==1271){
				$d['cnt']=floor($d['cnt']*0.85);
			}
			if($d["country"]=="RU"){
				if (isset($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]))
				$val=round($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]['summa_rus']/1000,4)*$d['cnt'];
				else
				$val=round($this->linkcomission[$d["id_src"]]->summa_rus/1000,4)*$d["cnt"];
			}
			else{
				if (isset($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]))
				$val=round($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]['summa_cis']/1000,4)*$d['cnt'];
				else
				$val=round($this->linkcomission[$d["id_src"]]->summa_cis/1000,4)*$d["cnt"];
			}
			$d["summa"]=$val;
			if(!isset($this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"])){
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"]=0;
			}
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"]+=$d["summa"];
			
			if(!isset($this->pids[$d["day"]][$d["pid"]][$d["country"]]["play_co"])){
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["play_co"]=0;
			}
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["play_co"]+=$d["cnt"];
		}
		$sql="select day,pid,country,
			count(*) as loaded,
			count(CASE WHEN played>0 THEN 1 END) as calculate,
			sum(played) as played,
			sum(completed) as completed,
			sum(clicks) as clicks,
			sum(start) as start,
			count(CASE WHEN second_round>0 THEN 1 END) as second,
			sum(second_round) as second_all,
			count(CASE WHEN second_cheap>0 THEN 1 END) as second_cheap,
			sum(second_cheap) as second_cheap_all,
			case when cast(count(CASE WHEN played>0 THEN 1 END ) as double precision) >0 then  cast(sum(played) as double 
			precision)/cast(count(CASE WHEN played>0 THEN 1 END ) as double precision)else 0 end  as deep,
			case when cast(count(*) as double precision) >0 then  count(CASE WHEN played>0 THEN 1 END)*100/cast(count(*) as double 
			precision) else 0 end as util,
			count(distinct ip) as count_ip,
			lease as lease
			from  stat_user_pages
			where day between '$from' and '$to' and control='1'
			group by day,pid,country,lease";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			if ($d['pid']==974){
				if (!isset($this->pids[$d["day"]][$d["pid"]][$d["country"]]["play_co"])){
					$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_played"]=0;
					$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_calculate"]=0;
				}
				else{
					$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_played"]=$this->pids[$d["day"]][$d["pid"]][$d["country"]]["play_co"];
					$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_calculate"]=$this->pids[$d["day"]][$d["pid"]][$d["country"]]["play_co"];
				}
				
			}
			else{
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_played"]=$d["played"];
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_calculate"]=$d["played"];
			}
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_loaded"]=$d["loaded"];
			
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_one_played"]=$d["calculate"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_completed"]=$d["completed"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_clicks"]=$d["clicks"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_started"]=$d["start"];
			$this->pids[$d["day"]][$d["pid"]][$d["country"]]["coef"]=1-$d["count_ip"]/$d["loaded"];
			if ($d["lease"]==1){
				if(!isset($this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"])){
					$this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"]=0;
				}
				if(!isset($this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"])){
					$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"]=0;
				}
				$this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"]+=$this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"];
			}
		}
	}
	
	private function makeBlock($from=null,$to=null){
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="insert into block_summa (day,id_block,requested,calculated) select ?,?,?,? 
		WHERE NOT EXISTS (SELECT 1 FROM block_summa WHERE day =? and id_block=?)";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update block_summa set requested =? ,calculated=? WHERE day =? and id_block=?";
		$sthUpdatePids=$pdo->prepare($sql);
		if(!$from || !$to){
			$from=$to=$date("Y-m-d");
		}
		$sql="select day,id_block,count(*) as requested, count(CASE WHEN played>0 THEN 1 END) as calculate 
		from stat_block_pages where day between '$from' and '$to' group by day,id_block ";
		$blocks=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach( $blocks as $block){
			$sthUpdatePids->execute([$block["requested"],$block["calculate"],$block["day"],$block["id_block"]]);
			$sthInsertPids->execute([$block["day"],$block["id_block"],$block["requested"],$block["calculate"],$block["day"],$block["id_block"]]);
		}
	}
	
	private function makeLinks($from=null,$to=null){
		$pdo = \DB::connection("videotest")->getPdo();
		if(!$from || !$to){
			$from=$to=date("Y-m-d");
		}
		$sql="insert into  links_summa (day,id_src,country,requested,played,util) 
		select ?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM links_summa WHERE day=? and id_src =? and country=? )";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update  links_summa set requested=?,played=?,util=? WHERE day=? and id_src =? and country=?";
		$sthUpdatePids=$pdo->prepare($sql);
		$sql="select id_src,
		country,
		day,
		requested,
		played,
		case when requested>0 then cast(played as double precision)*100/cast(requested as double precision)  else 0 end as  util 
		from (select 
		t.id_src
		,country
		,date(t.datetime) as day
		,sum(t.requested) requested
		,sum(t.played) as played
		from stat_links t where t. datetime >= '$from' and country is not null
		group by t.id_src,country,date(t.datetime) )t";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			if($d["id_src"]==13)
			$sthUpdatePids->execute([$d["requested"],$d["played"],$d["util"],$d["day"],$d["id_src"],$d["country"]]);
			$sthInsertPids->execute([$d["day"],$d["id_src"],$d["country"],$d["requested"],$d["played"],$d["util"],$d["day"],$d["id_src"],$d["country"]]);
			$sthUpdatePids->execute([$d["requested"],$d["played"],$d["util"],$d["day"],$d["id_src"],$d["country"]]);
		}
   }
}

