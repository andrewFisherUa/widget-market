<?php

namespace App\Videosource\Mystat;

use Illuminate\Database\Eloquent\Model;

class AdvertSumma extends Model
{
	    private static $Pads=[];
		private static $Servers=[];
		private static $UserPads=[];
		
		private function getPad($id){
            
			if(isset(self::$Pads[$id])) return self::$Pads[$id];
			$this->padSth->execute([$id]);
			$r=$this->padSth->fetch(\PDO::FETCH_ASSOC);
		
			if(!$r){
				self::$Pads[$id]=null;
			}
			else{
				if(!isset(self::$UserPads[$r["pad"]])){
				    $this->userpadSth->execute([$r["pad"]]);
			        $rkk=$this->userpadSth->fetch(\PDO::FETCH_ASSOC);
					self::$UserPads[$r["pad"]]=$rkk;
				}
				
				
				self::$Servers[$r["pad"]]=$r["ltd"];
				self::$Pads[$id]=$r;
				
			}
		
			return self::$Pads[$id];
				
			
		}
private function getServer($id){

			if(isset(self::$Servers[$id])) return self::$Servers[$id];
			$this->setverSth->execute([$id]);
			$r=$this->setverSth->fetch(\PDO::FETCH_ASSOC);
		
			if(!$r){
				self::$Servers[$id]=null;
			}
			else{
				print_r([$id,$r["domain"]]);
				
				self::$Servers[$id]=$r["domain"];
				
			}
		
			return self::$Servers[$id];
				
			
		}		
    public function Calculate($date=null){
		
		$pdo=\DB::connection("pgstatistic")->getPdo();
		$pdoprecluck=\DB::connection()->getPdo();
		
				$sql="
		
  update myadvert_widgets
    set 
    tviews=?,
    tclicks=?,
	tsumma=?,
	yviews=?,
    yclicks=?,
	ysumma=?,
	mviews=?,
    mclicks=?,
	msumma=?,
    clicks_plus=?,
    summa_plus=?
	WHERE day=? and pid=?
	";
    $w_UpdStat=$pdo->prepare($sql);
		
		$sql="
		
insert into myadvert_widgets (
    day,
    pid,
    tviews,
    tclicks,
	tsumma,
	yviews,
    yclicks,
	ysumma,
	mviews,
    mclicks,
	msumma
    ) select ?,?,?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM myadvert_widgets WHERE day=? and pid=?)
	";
   $w_InsStat=$pdo->prepare($sql);		
		
		$sql="
	update myadvert_sites 
    set name=?,
	yviews=?,
    yclicks=?, 	
	ysumma=?, 	
    views=?,
    clicks=?, 	
	sclicks=?,
    summa=?,
	ssumma=?, 	
	psumma=?, 
	user_name=?,
	user_id=?
	WHERE day=? and pad=?
	";
   $this->UpdStat=$pdo->prepare($sql);		
	
		$sql="
	insert into myadvert_sites (
    day,
    pad,
    name,
	yviews,
    yclicks, 	
	ysumma,
    views,
    clicks,
    sclicks,
    summa,
	ssumma,
	psumma,
	user_name,
	user_id
    )select ?,?,?,?,?,?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM myadvert_sites WHERE day=? and pad=?)
	";
   $this->InsStat=$pdo->prepare($sql);	
  
		#die();
		$sql="select pad,ltd from widgets w 
         where w.id  = ?";
		$this->padSth=$pdoprecluck->prepare($sql);
		$sql="select domain,user_id from partner_pads w 
        where w.id  = ?";
		$this->setverSth=$pdoprecluck->prepare($sql);		
		$sql="select pp.user_id,uu.name from partner_pads  pp
        inner join users  uu
        on uu.id=pp.user_id
        where pp.id=?";
		$this->userpadSth=$pdoprecluck->prepare($sql);
		$mypad=[];
	    $sql="select pad
		  ,day
          ,sum(views) as views
          ,sum(clicks) as clicks
          ,sum(summa) as summa
          from myadvert_summa_clicks
          where day between  '$date' and '$date'	
          group by pad,day
       	";
		$data0=\DB::connection("pgstatistic")->select($sql);
		foreach($data0 as $d){
			$mypad[$d->day][$d->pad]=$d;
			#var_dump($d);
		}
		#return;
		$sql="select day,wid
		,count(*) as clicks
,sum(client_price) as summa
 from myadvert_clicks
where status =1
and day between  '$date' and '$date'	
group by day,wid 
";
$myppid=[];
		$data4=\DB::connection("pgstatistic")->select($sql);
		foreach($data4 as $d){
			$myppid[$d->day][$d->wid]=$d;
			#var_dump($d);
		}	
		$sql="select pid,day
,coalesce(yandex_views,0) as yandex_views
,coalesce(ta_views,0) as ta_views
,coalesce(yandex_clicks,0) as yandex_clicks
,coalesce(ta_clicks,0) as ta_clicks
,coalesce(yandex_summa,0) as yandex_summa
,coalesce(ta_summa,0) as ta_summa
,coalesce(yandex_views,0)+coalesce(ta_views,0) as views
,coalesce(yandex_clicks,0)+coalesce(ta_clicks,0) as clicks
,coalesce(yandex_summa,0)+coalesce(ta_summa,0) as summa
 from wid_calculate
 where day between  '$date' and '$date'";
		#$sql="select * from wid_summa where day between '$date' and '$date'";
		
		$data=\DB::connection("pgstatistic")->select($sql);
		$pading=[];
		
	
		
		foreach($data as $d){
			$pad=$this->getPad($d->pid);
			if(!$pad){ 
			continue;
			}
			$user_id=0;
			$user_name='';
			#var_dump($pad);
			//var_dump(self::$UserPads);
			if(!isset(self::$UserPads[$pad["pad"]])){
				print "полная неудача во всём\n";
			}else{
				$user_id=self::$UserPads[$pad["pad"]]["user_id"];
				$user_name=self::$UserPads[$pad["pad"]]["name"];
				#var_dump(self::$UserPads[$pad["pad"]]);
			}
			//if($d->pid==661){
			//var_dump([$pad,$d]);
			if($d->pid==661){
                       # var_dump($d);       
                        }
 
		    //}
			$myclicks=0;
			$mysumma=0;
			if(isset($myppid[$d->day][$d->pid])){
				//var_dump(["псевдопосев",$myppid[$d->day][$d->pid]]);
				$myclicks=$myppid[$d->day][$d->pid]->clicks;
				$mysumma=$myppid[$d->day][$d->pid]->summa;
			}

			
if($d->pid==661){
#var_dump($d); die();
}
if(11==1 && $d->pid==661){
$clicks_plus=$myclicks;
$summa_plus=$mysumma;
$veisumma=$d->ta_summa-$mysumma;
$veiclicks=$d->ta_clicks-$myclicks;
}else{
$veisumma=$d->ta_summa-$mysumma;
$veiclicks=$d->ta_clicks-$myclicks;
$clicks_plus=0;
$summa_plus=0;

}

			$gk=[$d->ta_views
			,$veiclicks
			,$veisumma
			,$d->yandex_views
			,$d->yandex_clicks
			,$d->yandex_summa
			,0
			,$myclicks
			,$mysumma
            ,$clicks_plus
            ,$summa_plus
			,$d->day
			,$d->pid
			];
			$w_UpdStat->execute($gk);	
			
			$gk=[$d->day
			,$d->pid
			,$d->ta_views
			,$veiclicks
			,$veisumma
			,$d->yandex_views
			,$d->yandex_clicks
			,$d->yandex_summa
			,0
			,$myclicks
			,$mysumma
			,$d->day
			,$d->pid
			];
			$w_InsStat->execute($gk);		
			if($d->pid==661){
                        #var_dump($gk);       die();
                        }

			
			
			$sclicks=0;
			$ssumma=0;
			
			if(isset($pading[$d->day][$pad["pad"]])){
			#var_dump("ахтыблять !!".$pad["ltd"]);
			$pading[$d->day][$pad["pad"]]["yviews"]+=$d->yandex_views;					
			$pading[$d->day][$pad["pad"]]["yclicks"]+=$d->yandex_clicks;	
			$pading[$d->day][$pad["pad"]]["ysumma"]+=$d->yandex_summa;
			$pading[$d->day][$pad["pad"]]["views"]+=$d->ta_views;	
			$pading[$d->day][$pad["pad"]]["clicks"]+=$d->ta_clicks;	
			$pading[$d->day][$pad["pad"]]["sclicks"]+=$sclicks;
			$pading[$d->day][$pad["pad"]]["summa"]+=$d->ta_summa;	
			$pading[$d->day][$pad["pad"]]["ssumma"]+=$ssumma;
            $pading[$d->day][$pad["pad"]]["psumma"]+=$mysumma;				
			}else{
			$pading[$d->day][$pad["pad"]]["user_id"]=$user_id;	
			$pading[$d->day][$pad["pad"]]["user_name"]=$user_name;	
            $pading[$d->day][$pad["pad"]]["yviews"]=$d->yandex_views;					
			$pading[$d->day][$pad["pad"]]["yclicks"]=$d->yandex_clicks;	
			$pading[$d->day][$pad["pad"]]["ysumma"]=$d->yandex_summa;
			$pading[$d->day][$pad["pad"]]["ltd"]=$pad["ltd"];	
			$pading[$d->day][$pad["pad"]]["views"]=$d->ta_views;	
			$pading[$d->day][$pad["pad"]]["clicks"]=$d->ta_clicks;	
			$pading[$d->day][$pad["pad"]]["sclicks"]=$sclicks;
			$pading[$d->day][$pad["pad"]]["summa"]=$d->ta_summa;	
			$pading[$d->day][$pad["pad"]]["ssumma"]=$ssumma;	
			$pading[$d->day][$pad["pad"]]["psumma"]=$mysumma;	
			}
		}
		#return;
		foreach($pading as $day=>$pads){
			
			foreach($pads as $pad=>$dtt){
			if($dtt["ysumma"]>0){
				
			}	
			
			if($pad==61){
				//var_dump($dtt); die();
			}
				if(isset($mypad[$day][$pad])){
					#var_dump($mypad[$day][$pad]);
					$dtt["ssumma"]=$mypad[$day][$pad]->summa;
					$dtt["sclicks"]=$mypad[$day][$pad]->clicks;
				}
				//var_dump($dtt["psumma"]);
			$k=[
			$dtt["ltd"],
            $dtt["yviews"],
			$dtt["yclicks"],
			$dtt["ysumma"],			
			$dtt["views"],
			$dtt["clicks"],
			$dtt["sclicks"],
			$dtt["summa"],
			$dtt["ssumma"],
			$dtt["psumma"],
			$dtt["user_name"],
			$dtt["user_id"],
			$day,
			$pad
			];
			$this->UpdStat->execute($k);
			$k=[
			$day,
			$pad,
			$dtt["ltd"],
			$dtt["yviews"],
			$dtt["yclicks"],
			$dtt["ysumma"],	
			$dtt["views"],
			$dtt["clicks"],
			$dtt["sclicks"],
			$dtt["summa"],
			$dtt["ssumma"],
			$dtt["psumma"],
			$dtt["user_name"],
			$dtt["user_id"],
			$day,
			$pad
			];
			$this->InsStat->execute($k);
			if($dtt["summa"]>0){
		
			}
			}
		}
		    #$pading[$d->day][$pad["pad"]]["ltd"]=$pad["ltd"];	
			#$pading[$d->day][$pad["pad"]]["views"]=$d->views;	
			#$pading[$d->day][$pad["pad"]]["clicks"]=$d->clicks;	
			#$pading[$d->day][$pad["pad"]]["sclicks"]=$sclicks;
			#$pading[$d->day][$pad["pad"]]["summa"]=$d->summa;	
			#$pading[$d->day][$pad["pad"]]["summa"]=$ssumma;	
		
		 $clicks=[];
		$sql="select date,id_server,page_key,count(*) as cnt from advert_stat_clicks
		where date between  '$date' and '$date'
		group by date,id_server,page_key
		";
		$cdata=\DB::connection("pgstatistic")->select($sql);
		foreach($cdata as $cd){
			$clicks[$cd->date][$cd->id_server][$cd->page_key]=$cd->cnt;
			#var_dump($cd);
			
		}
		
		
		$sql="
		update myadvert_pad_request 
        set name=?,
        request=?,
		first_found=?,
		cnt=?,
		jns=?,
		clicks=?,
		last_visit=?
	   WHERE day=? and pad=? and hash=?
		";
		$this->UpdRStat=$pdo->prepare($sql);	
		$sql="
		insert into myadvert_pad_request (
        day,
        pad,
		hash,
        name,
        request,
		url,
		first_found,
		cnt,
		jns,
		clicks,
		last_visit
        )
	   select ?,?,?,?,?,?,?,?,?,?,?
	   WHERE NOT EXISTS (SELECT 1 FROM myadvert_pad_request WHERE day=? and pad=? and hash=?)
		";
		$this->InsRStat=$pdo->prepare($sql);	
		/*
		$sql="select id_server,day,hash,request,first_find,url,jns
		,count(*) as cnt
		from myadvert_requests where day between  '$date' and '$date'
		group by id_server,day,hash,request,first_find,url,jns
		order by count(*) asc
		";
		*/
		$sql="select 
t1.id_server
,t1.day
,t1.hash
,t1.url
,t1.d
,t1.cnt
,t2.request
,t2.first_find
,t2.url
,t2.jns
from (
select 
t1.id_server
,t1.day
,t1.hash
,t1.url
,max(t1.datetime) as d
,count(t1.*) as cnt
from myadvert_requests t1
where t1.day between  '$date' and '$date'
group by 
t1.id_server
,t1.day
,t1.hash
,t1.url
) t1
inner join myadvert_requests t2
on t2.id_server=t1.id_server
and t2.day=t1.day
and t2.hash=t1.hash
and t2.url=t1.url
and t2.datetime=t1.d
order by t1.cnt desc";
		
		$data=\DB::connection("pgstatistic")->select($sql);
		
		foreach($data as $d){
			$pad=$this->getServer($d->id_server);
			if(!$pad) exit();
			if(preg_match('/^https?\:\/\/([a-z]+\.)market\-place\.su/um',$d->url)){
				continue;
			}
		    
			if($d->jns){
			}
			if(mb_strlen($d->url)>1000){
#				var_dump($d->url);
				continue;
				
			}
			 $cli=0;
			
	        if(isset($clicks[$d->day][$d->id_server][$d->hash])){
				$cli=$clicks[$d->day][$d->id_server][$d->hash];
			#var_dump($clicks[$d->day][$d->id_server][$d->hash]);
			}	   
			
			$k=[$pad,
			$d->request,
			
			$d->first_find,
			$d->cnt,
			$d->jns,
			$cli,
			$d->d,
			$d->day,
			$d->id_server,
			$d->hash
			];
		
			$this->UpdRStat->execute($k);
			
			$k=[$d->day,
			$d->id_server,
			$d->hash,
			$pad,
			$d->request,
			$d->url,
			$d->first_find,
			$d->cnt,
			$d->jns,
			$cli,
			$d->d,
			$d->day,
			$d->id_server,
			$d->hash
			];
			$this->InsRStat->execute($k);
			#var_dump($k);
			
		}
		
	$sql="select date,id_server,id_widget,page_key,count(*) as cnt from advert_stat_clicks
		where date between  '$date' and '$date'
		group by date,id_server,id_widget,page_key
		";
		$cdata=\DB::connection("pgstatistic")->select($sql);
		foreach($cdata as $cd){
			$glilicks[$cd->date][$cd->id_server][$cd->id_widget][$cd->page_key]=$cd->cnt;
			#var_dump($cd);
			
		}	
		#return ;
$sql="
		update myadvert_pid_request 
        set name=?,
        request=?,
		first_found=?,
		cnt=?,
		jns=?,
		clicks=?,
		last_visit=?
	   WHERE day=? and pad=? and pid=? and hash=?
		";
		$_UpdRStat=$pdo->prepare($sql);	
		$sql="
		insert into myadvert_pid_request (
        day,
        pad,
		pid,
		hash,
        name,
        request,
		url,
		first_found,
		cnt,
		jns,
		clicks,
		last_visit
        )
	   select ?,?,?,?,?,?,?,?,?,?,?,?
	   WHERE NOT EXISTS (SELECT 1 FROM myadvert_pid_request WHERE day=? and pad=? and pid=? and hash=?)
		";
		$_InsRStat=$pdo->prepare($sql);			
			$sql="select 
t1.id_server
,t1.id_widget
,t1.day
,t1.hash
,t1.url
,t1.d
,t1.cnt
,t2.request
,t2.first_find
,t2.url
,t2.jns
from (
select 
t1.id_server
,t1.id_widget
,t1.day
,t1.hash
,t1.url
,max(t1.datetime) as d
,count(t1.*) as cnt
from myadvert_requests t1
where t1.day between  '$date' and '$date'
group by 
t1.id_server
,t1.id_widget
,t1.day
,t1.hash
,t1.url
) t1
inner join myadvert_requests t2
on t2.id_server=t1.id_server
and t2.id_widget=t1.id_widget
and t2.day=t1.day
and t2.hash=t1.hash
and t2.url=t1.url
and t2.datetime=t1.d
order by t1.cnt desc";
$data=\DB::connection("pgstatistic")->select($sql);
		
		foreach($data as $d){
			$pad=$this->getServer($d->id_server);
			if(!$pad) exit();
			if(preg_match('/^https?\:\/\/([a-z]+\.)market\-place\.su/um',$d->url)){
				continue;
			}
			if(mb_strlen($d->url)>1000){
#				var_dump($d->url);
				continue;
				
			}
			 $cli=0;
			
	        if(isset($glilicks[$d->day][$d->id_server][$d->id_widget][$d->hash])){
		    		$cli=$glilicks[$d->day][$d->id_server][$d->id_widget][$d->hash];
			#var_dump($glilicks[$d->day][$d->id_server][$d->id_widget][$d->hash]);
			}	 
			$k=[$pad,
			$d->request,
			$d->first_find,
			$d->cnt,
			$d->jns,
			$cli,
			$d->d,
			$d->day,
			$d->id_server,
			$d->id_widget,
			$d->hash
			];
		
			$_UpdRStat->execute($k);			
			$k=[$d->day,
			$d->id_server,
			$d->id_widget,
			$d->hash,
			$pad,
			$d->request,
			$d->url,
			$d->first_find,
			$d->cnt,
			$d->jns,
			$cli,
			$d->d,
			$d->day,
			$d->id_server,
			$d->id_widget,
			$d->hash
			];
			$_InsRStat->execute($k);
			#var_dump($d);
		}
		#select * from ads_yml where status = 1
        $this->ti2ze9R($date);
        #and id_marka=57
	}
	public function ti2ze9R($date){
		$pdo=\DB::connection("pgstatistic")->getPdo();
	$sql="insert into myteaser_sites (
    day,
    pad,
    name,
    views,
    clicks,
    summa,
    user_id,
    user_name
     ) select ?,?,?,?,?,?,?,?
    WHERE NOT EXISTS (SELECT 1 FROM myteaser_sites WHERE day=? and pad=?)
	";	
   $j_InsStat=$pdo->prepare($sql);		
   $sql="update myteaser_sites 
    set name=?,
    views=?,
    clicks=?,
    summa=?,
    user_id=?,
    user_name=?
    WHERE day=? and pad=?
	";	
   $j_UpdStat=$pdo->prepare($sql);		
		
				$sql="select pid,day
,coalesce(ts_views,0) as views
,coalesce(ts_clicks,0) as clicks
,coalesce(ts_summa,0) as summa
 from wid_calculate
 where day between  '$date' and '$date'";
		#$sql="select * from wid_summa where day between '$date' and '$date'";
		
		$data=\DB::connection("pgstatistic")->select($sql);
		$pading=[];
		print "дата тиезка ура ! $date \n";
		foreach($data as $d){
		    $pad=$this->getPad($d->pid);
			if(!$pad){ 
			continue;
			}
			$user_id=0;
			$user_name='';
			#var_dump($pad);
			//var_dump(self::$UserPads);
			if(!isset(self::$UserPads[$pad["pad"]])){
				print "полная неудача во всём\n";
			}else{
				$user_id=self::$UserPads[$pad["pad"]]["user_id"];
				$user_name=self::$UserPads[$pad["pad"]]["name"];
				#var_dump(self::$UserPads[$pad["pad"]]);
			}	
		if(isset($pading[$d->day][$pad["pad"]])){
			$pading[$d->day][$pad["pad"]]["views"]+=$d->views;	
			$pading[$d->day][$pad["pad"]]["clicks"]+=$d->clicks;	
			$pading[$d->day][$pad["pad"]]["summa"]+=$d->summa;	
			
			#var_dump("ахтыблять !!".$pad["ltd"]);
		}else{
				
			$pading[$d->day][$pad["pad"]]["user_id"]=$user_id;	
			$pading[$d->day][$pad["pad"]]["user_name"]=$user_name;	
			$pading[$d->day][$pad["pad"]]["ltd"]=$pad["ltd"];	
			$pading[$d->day][$pad["pad"]]["views"]=$d->views;	
			$pading[$d->day][$pad["pad"]]["clicks"]=$d->clicks;	
			$pading[$d->day][$pad["pad"]]["summa"]=$d->summa;	
		}			
		}
			foreach($pading as $day=>$pads){
				foreach($pads as $pad=>$dtt){
			var_dump(["падинг 2",$dtt["ltd"]]);		
			            $j_UpdStat->execute([	
						$dtt["ltd"],
						$dtt["views"],
						$dtt["clicks"],
						$dtt["summa"],
						$dtt["user_id"],
						$dtt["user_name"],
						$day,
			            $pad
						]);
						$j_InsStat->execute([
			            $day,
			            $pad,
			            $dtt["ltd"],
						$dtt["views"],
						$dtt["clicks"],
						$dtt["summa"],
						$dtt["user_id"],
						$dtt["user_name"],
						$day,
			            $pad
			            ]);
			
				}
			}			
	}
}
