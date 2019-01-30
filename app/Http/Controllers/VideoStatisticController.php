<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
class VideoStatisticController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     private $dbUser;
     private $dbPass;
    public function __construct()
    {
         $this->dbUser=env('DB_USERNAME');
         $this->dbPass=env('DB_PASSWORD');

   
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function graph(Request $request){
		return view('statistic.video.graph');
    }
	public function allStat(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');

        }
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"loaded_ru","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played_ru","order"=>"",'url'=>""],
			['title'=>"Засч. показы",'index'=>"calc_played_ru","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep_ru","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util_ru","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm_ru","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked_ru","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr_ru","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa_ru","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"loaded_cis","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played_cis","order"=>"",'url'=>""],
			['title'=>"Засч. показы",'index'=>"calc_played_cis","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep_cis","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util_cis","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm_cis","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked_cis","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr_cis","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa_cis","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Засч. показы",'index'=>"calc_played","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		
		$pdo = \DB::connection()->getPdo();
		$pgsql="create temp table all_stat as select t1.day as day, t1.loaded as loaded_ru, t1.played as played_ru, t1.calc_played as 
		calc_played_ru, round(t1.deep::numeric,4) as deep_ru, round(t1.util::numeric,4) as util_ru, round(t1.dosm::numeric,4) as dosm_ru, 
		t1.clicked as clicked_ru, round(t1.ctr::numeric, 4) as ctr_ru, round(t1.summa::numeric,2) as summa_ru, t2.loaded as loaded_cis, 
		t2.played as played_cis, t2.calc_played as calc_played_cis, round(t2.deep::numeric,4) as deep_cis, round(t2.util::numeric,4) as 
		util_cis, round(t2.dosm::numeric,4) as dosm_cis, t2.clicked as clicked_cis, round(t2.ctr::numeric, 4) as ctr_cis, round(t2.summa::numeric,2) as 
		summa_cis, (t1.loaded+t2.loaded) as loaded, (t1.played+t2.played) as played, (t1.calc_played+t2.calc_played) as calc_played, 
		round((t1.played+t2.played)/(t1.calc_played+t2.calc_played)::numeric,4) as deep, round((t1.calc_played+t2.calc_played)/(t1.loaded+t2.loaded)
		::numeric,4)*100 as util, round((t1.complited+t2.complited)/(t1.played+t2.played)::numeric,4)*100 as dosm, (t1.clicked+t2.clicked) as clicked, 
		round((t1.clicked+t2.clicked)/(t1.played+t2.played)::numeric,4)*100 as ctr, (t1.summa+t2.summa) as summa from video_sum_stats t1 left 
		join(select * from video_sum_stats) t2 on t1.day=t2.day and t2.country<>'RU' 
		where t1.country='RU' and t1.day between '$from' and '$to' order by day desc";
		$pdo->query($pgsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('all_stat')->orderBy($order,$direct)->paginate(30);
		return view('statistic.video.all_stat', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header]);
	}
	
	public function sourceStat(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');

        }
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"played";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Ссылка",'index'=>"title","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		
		$pdo = \DB::connection()->getPdo();
		$pgsql="create temp table source_stat as select t3.title, t1.id_src, (coalesce(sum(t1.requested),0)+coalesce(sum(t2.requested),0)) as requested, 
		(coalesce(sum(t1.started),0)+coalesce(sum(t2.started),0)) as started, 
		(coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0)) as played, (coalesce(sum(t1.completed),0)+coalesce(sum(t2.completed),0)) as completed, 
		(coalesce(sum(t1.clicked),0)+coalesce(sum(t2.clicked),0)) as clicked, case when (coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0)<>0) then 
		100-(round((coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0))/(coalesce(sum(t1.started),0)+coalesce(sum(t2.started),0))::numeric,4)*100) else 
		0 end as poteri, case when (coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0)<>0) then round((coalesce(sum(t1.completed),0)+coalesce(
		sum(t2.completed),0))/(coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0))::numeric,4)*100 else 0 end as dosm, case when 
		(coalesce(sum(t1.requested),0)+coalesce(sum(t2.requested),0)<>0) then round((coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0))/
		(coalesce(sum(t1.requested),0)+coalesce(sum(t2.requested),0))::numeric,4)*100 else 0 end as util, case when (coalesce(sum(t1.played),0)+
		coalesce(sum(t2.played),0)<>0) then round((coalesce(sum(t1.clicked),0)+coalesce(sum(t2.clicked),0))/(coalesce(sum(t1.played),0)+
		coalesce(sum(t2.played),0))::numeric,4)*100 else 0 end as ctr from video_statistic_pads t1 left join (select * from video_statistic_pads) t2 on 
		t1.id_src=t2.id_src and t1.day=t2.day and t2.country<>'RU' left join (select * from video_sources) t3 on t1.id_src=t3.id where t1.country='RU'
		and t1.day between '$from' and '$to' group by t3.title, t1.id_src order by played desc";
		$pdo->query($pgsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('source_stat')->orderBy($order,$direct)->get();
		return view('statistic.video.source_stat', ['stats'=>$stats, 'header'=>$header, 'from'=>$from, 'to'=>$to]);
	}
	public function sourceStatDetail($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested_ru","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played_ru","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked_ru","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri_ru","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm_ru","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util_ru","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr_ru","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested_cis","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played_cis","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked_cis","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri_cis","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm_cis","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util_cis","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr_cis","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		
		$pdo = \DB::connection()->getPdo();
		$pgsql="create temp table source_stat_detail as select t3.title, t1.id_src, t1.day, coalesce(sum(t1.requested),0) as requested_ru, 
		coalesce(sum(t1.started),0) as started_ru, coalesce(sum(t1.played),0) as played_ru, coalesce(sum(t1.completed),0) as completed_ru, 
		coalesce(sum(t1.clicked),0) as clicked_ru, case when avg(t1.poteri)>0 then round(avg(t1.poteri)::numeric,4) else 0 end as poteri_ru, 
		round(coalesce(avg(t1.dosm),0)::numeric,4) as dosm_ru, round(coalesce(avg(t1.util),0)::numeric,4) as util_ru, 
		round(coalesce(avg(t1.ctr),0)::numeric,4) as ctr_ru, coalesce(sum(t2.requested),0) as requested_cis, coalesce(sum(t2.started),0) as 
		started_cis, coalesce(sum(t2.played),0) as played_cis, coalesce(sum(t2.completed),0) as completed_cis, 	coalesce(sum(t2.clicked),0) as 
		clicked_cis, case when avg(t2.poteri)>0 then round(avg(t2.poteri)::numeric,4) else 0 end as poteri_cis, round(coalesce(avg(t2.dosm),0) ::numeric,4) 
		as dosm_cis, round(coalesce(avg(t2.util),0)::numeric,4) as util_cis, round(coalesce(avg(t2.ctr),0)::numeric,4) as ctr_cis, 
		(coalesce(sum(t1.requested),0)+coalesce(sum(t2.requested),0)) as requested, (coalesce(sum(t1.started),0)+coalesce(sum(t2.started),0)) as started, 
		(coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0)) 
		as played, (coalesce(sum(t1.completed),0)+coalesce(sum(t2.completed),0)) as completed, (coalesce(sum(t1.clicked),0)+coalesce(sum(t2.clicked),0)) 
		as clicked, case when (coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0)<>0) then 100-(round((coalesce(sum(t1.played),0)+coalesce
		(sum(t2.played),0))/(coalesce(sum(t1.started),0)+coalesce(sum(t2.started),0))::numeric,4)*100) else 0 end as poteri, case when 
		(coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0)<>0) then round((coalesce(sum(t1.completed),0)+coalesce(
		sum(t2.completed),0))/(coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0))::numeric,4)*100 else 0 end as dosm, case when 
		(coalesce(sum(t1.requested),0)+coalesce(sum(t2.requested),0)<>0) then round((coalesce(sum(t1.played),0)+coalesce(sum(t2.played),0))/
		(coalesce(sum(t1.requested),0)+coalesce(sum(t2.requested),0))::numeric,4)*100 else 0 end as util, case when (coalesce(sum(t1.played),0)+
		coalesce(sum(t2.played),0)<>0) then round((coalesce(sum(t1.clicked),0)+coalesce(sum(t2.clicked),0))/(coalesce(sum(t1.played),0)+
		coalesce(sum(t2.played),0))::numeric,4)*100 else 0 end as ctr from video_statistic_pads t1 left join (select * from video_statistic_pads where 
		id_src='$id') t2 on t1.id_src=t2.id_src and t1.day=t2.day and t2.country<>'RU' left join (select * from video_sources) t3 on t1.id_src=t3.id 
		where t1.country='RU'and t1.id_src='$id' and t1.day between '$from' and '$to' group by t3.title, t1.id_src, t1.day order by id_src asc";
		$pdo->query($pgsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('source_stat_detail')->orderBy($order,$direct)->paginate(30);
		$st=\DB::connection()->table('source_stat_detail')->first();
		return view('statistic.video.source_stat_detail', ['stats'=>$stats, 'header'=>$header, 'from'=>$from, 'to'=>$to, 'id'=>$id, 'st'=>$st]);
	}
	public function allPids(Request $request){
	\Auth::user()->touch();
	$pdo = \DB::connection('videotest')->getPdo();
	$day=date("Y-m-d H:i:s");
	$sql="select 
	t.day
	,t.pid 
	,pvs.name
	,pvs.video_category
	,pvs.wid_id
    ,t.summa
    ,t.control_summa
    ,coalesce(t_ru.summa,0) as ru_summa
    ,coalesce(t_ru.control_summa,0) as ru_control_summa
    ,coalesce(t_ru.loaded,0) as ru_loaded
    ,coalesce(t_ru.played,0) as ru_played
	,coalesce(t_ru.calculate,0) as ru_calculate
	,coalesce(t_ukr.calculate,0) as ukr_calculate
    ,coalesce(t_ukr.loaded,0) as ukr_loaded
    ,coalesce(t_ukr.played,0) as ukr_played
    ,case when t_ru.loaded  >0 then  round( t_ru.calculate *100/cast(t_ru.loaded as double precision)::numeric,2) else 0 end as ru_util
    ,case when t_ru.calculate >0 then round(t_ru.played /cast(t_ru.calculate as double precision)::numeric,2) else 0 end  as ru_deep
    ,coalesce(t_ukr.summa,0) as ukr_summa
    ,coalesce(t_ukr.control_summa,0) as ukr_control_summa
    ,case when t_ukr.loaded  >0 then  round( t_ukr.calculate *100/cast(t_ukr.loaded as double precision)::numeric,2) else 0 end as ukr_util
    ,case when t_ukr.calculate >0 then round(t_ukr.played /cast(t_ukr.calculate as double precision)::numeric,2) else 0 end  as ukr_deep
	    ,case when t_ru.played >0 then round(t_ru.completed *100/cast(t_ru.played as double precision)::numeric,2) else 0 end  as ru_dosmptr
    ,case when t_ukr.played >0 then round(t_ukr.completed *100/cast(t_ukr.played as double precision)::numeric,2) else 0 end  as ukr_dosmptr
    ,case when t_ru.played >0 then round(t_ru.clicks *100/cast(t_ru.played as double precision)::numeric,2) else 0 end  as ru_ctr
    ,case when t_ukr.played >0 then round(t_ukr.clicks *100/cast(t_ukr.played as double precision)::numeric,2) else 0 end  as ukr_ctr
	,case when t_ru.started >0 then 100-round(t_ru.played *100/cast(t_ru.started as double precision)::numeric,2) else 100 end  as ru_poteri
,case when t_ukr.started >0 then 100-round(t_ukr.played *100/cast(t_ukr.started as double precision)::numeric,2) else 100 end  as ukr_poteri

    from (
    select day,pid,sum(summa) as summa,sum(control_summa) as control_summa
    from pid_summa where day=NOW()::date
    group by day,pid
    ) t
    left join pid_summa t_ru
    on t_ru.day=t.day and  t_ru.pid=t.pid and t_ru.country='RU'
    left join pid_summa t_ukr
    on t_ukr.day=t.day and  t_ukr.pid=t.pid and  t_ukr.country='CIS'
	inner join  pid_video_settings pvs on pvs.pid=t.pid
    order by t.day desc,pvs.name
    ";
	$pids=$pdo->query($sql)->fetchALL(\PDO::FETCH_ASSOC);
   foreach ($pids as $pid){
//var_dump($pid); echo "<hr>";
   }
//die();
	    return view('statistic.video.pids',["pids"=>$pids]);
	}
    public function statisticPid($id,Request $request){

		\Auth::user()->touch();
		$pdo = \DB::connection('videotest')->getPdo();
	$day=date("Y-m-d H:i:s");
	$sql="select 
	t.day
	,t.pid 
	,pvs.name
    ,t.summa
    ,t.control_summa
    ,coalesce(t_ru.summa,0) as ru_summa
    ,coalesce(t_ru.control_summa,0) as ru_control_summa
    ,coalesce(t_ru.loaded,0) as ru_loaded
    ,coalesce(t_ru.played,0) as ru_played
	,coalesce(t_ru.calculate,0) as ru_calculate
	,coalesce(t_ukr.calculate,0) as ukr_calculate
    ,coalesce(t_ukr.loaded,0) as ukr_loaded
    ,coalesce(t_ukr.played,0) as ukr_played
    ,case when t_ru.loaded  >0 then  round( t_ru.calculate *100/cast(t_ru.loaded as double precision)::numeric,2) else 0 end as ru_util
    ,case when t_ru.calculate >0 then round(t_ru.played /cast(t_ru.calculate as double precision)::numeric,2) else 0 end  as ru_deep
    ,coalesce(t_ukr.summa,0) as ukr_summa
    ,coalesce(t_ukr.control_summa,0) as ukr_control_summa
    ,case when t_ukr.loaded  >0 then  round( t_ukr.calculate *100/cast(t_ukr.loaded as double precision)::numeric,2) else 0 end as ukr_util
    ,case when t_ukr.calculate >0 then round(t_ukr.played /cast(t_ukr.calculate as double precision)::numeric,2) else 0 end  as ukr_deep
	,case when t_ru.played >0 then round(t_ru.completed *100/cast(t_ru.played as double precision)::numeric,2) else 0 end  as ru_dosmptr
    ,case when t_ukr.played >0 then round(t_ukr.completed *100/cast(t_ukr.played as double precision)::numeric,2) else 0 end  as ukr_dosmptr
	,case when t_ru.played >0 then round(t_ru.clicks *100/cast(t_ru.played as double precision)::numeric,2) else 0 end  as ru_ctr
    ,case when t_ukr.played >0 then round(t_ukr.clicks *100/cast(t_ukr.played as double precision)::numeric,2) else 0 end  as ukr_ctr
	,case when t_ru.started >0 then 100-round(t_ru.played *100/cast(t_ru.started as double precision)::numeric,2) else 100 end  as ru_poteri
,case when t_ukr.started >0 then 100-round(t_ukr.played *100/cast(t_ukr.started as double precision)::numeric,2) else 100 end  as ukr_poteri

    from (
    select day,pid,sum(summa) as summa,sum(control_summa) as control_summa
    from pid_summa where pid=$id and day>='2017-09-21'
    group by day,pid
    ) t
    left join pid_summa t_ru
    on t_ru.day=t.day and  t_ru.pid=t.pid and t_ru.country='RU'
    left join pid_summa t_ukr
    on t_ukr.day=t.day and  t_ukr.pid=t.pid and  t_ukr.country='CIS'
	inner join  pid_video_settings pvs on pvs.pid=t.pid
    order by t.day desc,t.pid
    ";
	$pids=$pdo->query($sql)->fetchALL(\PDO::FETCH_ASSOC);
   foreach ($pids as $pid){
      return view('statistic.video.pid',["pids"=>$pids]);
   }
	}
	
	public function newSourceStat(Request $request){

		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"played";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Название ссылки",'index'=>"title","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }


        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection('videotest')->getPdo();
		$pdodd = \DB::connection('video_')->getPdo();
		$sql="create temp table source_stat as select t1.id_src, t2.title as title, t2.player as player, sum(t1.requested) as requested, sum(t1.started) 
		as started, sum(t1.played) as played, sum(t1.completed) as completed, sum(t1.clicked) as clicked, case when (sum(t1.started)>0) then 100-(round(
		sum(t1.played)/sum(t1.started)::numeric,4)*100) else 100 end as poteri, case when(sum(t1.played)>0) then round(sum(t1.completed)/sum(t1.played)::
		numeric,4)*100 else 0 end as dosm, case when(sum(t1.requested)>0) then round(sum(t1.played)/sum(t1.requested)::numeric,4)*100 else 0 end as util, 
		case when(sum(t1.played)>0) then round(sum(t1.clicked)/sum(t1.played)::numeric,4)*100 else 0 end as ctr from stat_src_pages t1 left join (select * 
		from links) t2 on t1.id_src=t2.id where t1.day between '$from' and '$to' group by t1.id_src, t2.title, t2.player";
      //||||||| угадайга 2
$sql="
create temp table source_stat as
select 

id_src
,title
,0 as player
,sum(requested) as requested
,sum(started)as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
,case when (sum(started)>0) then 100-(round(sum(played)/sum(started)::numeric,4)*100) else 100 end as poteri
,case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm
,case when(sum(requested)>0) then round(sum(played)/sum(requested)::numeric,4)*100 else 0 end as util
,case when(sum(played)>0) then round(sum(clicked)/sum(played)::numeric,4)*100 else 0 end as ctr 
from _stat_src_pages

where day between '$from' and '$to'
group by 

id_src
,title
";

//echo nl2br($sql); die();
		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
	if (!$title){
		$sql="create temp table sum_source_stat as select sum(requested) as requested, sum(started) as started, sum(played) as played, sum(completed) as completed, sum(clicked) as clicked, 
		case when (sum(started)>0) then 100-(round(sum(played)/sum(started)::numeric,4)*100) else 100 end as poteri, case when(sum(played)>0) then 
		round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(requested)>0) then round(sum(played)/sum(requested)::
		numeric,4)*100 else 0 end as util, case when(sum(played)>0) then round(sum(clicked)/sum(played)::numeric,4)*100 else 0 end as ctr from 
		_stat_src_pages where day between '$from' and '$to'";
	}else{
		$sql="create temp table sum_source_stat as select sum(requested) as requested, sum(started) as started, sum(played) as played, sum(completed) as completed, sum(clicked) as clicked, 
		case when (sum(started)>0) then 100-(round(sum(played)/sum(started)::numeric,4)*100) else 100 end as poteri, case when(sum(played)>0) then 
		round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(requested)>0) then round(sum(played)/sum(requested)::
		numeric,4)*100 else 0 end as util, case when(sum(played)>0) then round(sum(clicked)/sum(played)::numeric,4)*100 else 0 end as ctr from 
		_stat_src_pages where day between '$from' and '$to' and title ~* '$title'";
        }
//                echo nl2br($sql) ; die();
		\DB::connection('video_')->getPdo()->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$players=\DB::connection('videotest')->table('videoplayer')->get();
		$LinkComent=\DB::connection('videotest')->table('links')->get();
		if (!$title){
		$stats=\DB::connection('video_')->table('source_stat')->orderBy($order,$direct)->paginate($number);
		$sum_stat=\DB::connection('video_')->table('sum_source_stat')->first();
		}
		else{

		$stats=\DB::connection('video_')->table('source_stat')->where('title', 'ilike', '%'.$title.'%')->orderBy($order,$direct)->paginate($number);
		$sum_stat=\DB::connection('video_')->table('sum_source_stat')->first();
		}
		return view('statistic.video.new_stat_source', ['order'=>$order, 'direct'=>$direct, 'number'=>$number, 'stats'=>$stats, 'sum_stat'=>$sum_stat, 'title'=>$title, 'players'=>$players, 'header'=>$header, 
		'from'=>$from, 'to'=>$to, 'LinkComent'=>$LinkComent]);
	}
	
	public function newSourceStatComparison(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$fromOld=$request->input('fromOld');
		$toOld=$request->input('toOld');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		if (!($fromOld||$toOld)){
			$fromOld=$toOld=date('Y-m-d',time()-3600*24);
		}
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"played";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Название ссылки",'index'=>"title","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table source_stat as select t1.id_src, t2.title as title, t2.player as player, sum(t1.requested) as requested, sum(t1.started) 
		as started, sum(t1.played) as played, sum(t1.completed) as completed, sum(t1.clicked) as clicked, case when (sum(t1.started)>0) then 100-(round(
		sum(t1.played)/sum(t1.started)::numeric,4)*100) else 100 end as poteri, case when(sum(t1.played)>0) then round(sum(t1.completed)/sum(t1.played)::
		numeric,4)*100 else 0 end as dosm, case when(sum(t1.requested)>0) then round(sum(t1.played)/sum(t1.requested)::numeric,4)*100 else 0 end as util, 
		case when(sum(t1.played)>0) then round(sum(t1.clicked)/sum(t1.played)::numeric,4)*100 else 0 end as ctr, 
		
		t3.requested as old_requested, t3.started as old_started, t3.played as old_played, t3.completed as old_completed, 
		t3.clicked as old_clicked, case when (t3.started>0) then 100-(round(
		t3.played/t3.started::numeric,4)*100) else 100 end as old_poteri, case when(t3.played>0) then round(t3.completed/t3.played::
		numeric,4)*100 else 0 end as old_dosm, case when(t3.requested>0) then round(t3.played/t3.requested::numeric,4)*100 else 0 end as old_util, 
		case when(t3.played>0) then round(t3.clicked/t3.played::numeric,4)*100 else 0 end as old_ctr
		
		from stat_src_pages t1 left join (select * from links) t2 on t1.id_src=t2.id left join 
		
		(select id_src, sum(requested) as requested, sum(started) as started, sum(played) as played, sum(completed) as completed, 
		sum(clicked) as clicked from stat_src_pages where day between '$fromOld' and '$toOld' group by id_src) t3 on t1.id_src=t3.id_src
		
		where t1.day between '$from' and '$to' group by t1.id_src, t2.title, t2.player, t3.requested, t3.started, t3.played, t3.completed, t3.clicked";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table sum_source_stat as select sum(t1.requested) as requested, sum(t1.started) as started, sum(t1.played) as played, sum(t1.completed) as completed, sum(t1.clicked) as clicked, 
		case when (sum(t1.started)>0) then 100-(round(sum(t1.played)/sum(t1.started)::numeric,4)*100) else 100 end as poteri, case when(sum(t1.played)>0) then 
		round(sum(t1.completed)/sum(t1.played)::numeric,4)*100 else 0 end as dosm, case when(sum(t1.requested)>0) then round(sum(t1.played)/sum(t1.requested)::
		numeric,4)*100 else 0 end as util, case when(sum(t1.played)>0) then round(sum(t1.clicked)/sum(t1.played)::numeric,4)*100 else 0 end as ctr,
		
		t2.requested as old_requested, t2.started as old_started, t2.played as old_played, t2.completed as old_completed, t2.clicked as old_clicked, 
		case when (t2.started>0) then 100-(round(t2.played/t2.started::numeric,4)*100) else 100 end as old_poteri, case when(t2.played>0) then 
		round(t2.completed/t2.played::numeric,4)*100 else 0 end as old_dosm, case when(t2.requested>0) then round(t2.played/t2.requested::
		numeric,4)*100 else 0 end as old_util, case when(t2.played>0) then round(t2.clicked/t2.played::numeric,4)*100 else 0 end as old_ctr
		
		from stat_src_pages t1 left join
		
		(select sum(requested) as requested, sum(started) as started, sum(played) as played, sum(completed) as completed, sum(clicked) as clicked 
		from stat_src_pages where day between '$fromOld' and '$toOld') t2 on 1=1
		
		where day between '$from' and '$to' group by t2.requested, t2.started, t2.played, t2.completed, t2.clicked";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$players=\DB::connection('videotest')->table('videoplayer')->get();
		if (!$title){
		$stats=\DB::connection('videotest')->table('source_stat')->orderBy($order,$direct)->paginate($number);
		$sum_stat=\DB::connection('videotest')->table('sum_source_stat')->first();
		}
		else{
		$stats=\DB::connection('videotest')->table('source_stat')->where('title', 'ilike', '%'.$title.'%')->orderBy($order,$direct)->paginate($number);
		$sum_stat=\DB::connection('videotest')->table('sum_source_stat')->first();
		}
		return view('statistic.video.new_stat_source_comparison', ['number'=>$number, 'order'=>$order, 'direct'=>$direct, 'fromOld'=>$fromOld, 'toOld'=>$toOld, 'stats'=>$stats, 'sum_stat'=>$sum_stat, 'title'=>$title, 'players'=>$players, 'header'=>$header, 
		'from'=>$from, 'to'=>$to]);
	}
	
	public function newSourceStatDetail($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Дата",'index'=>"title","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested_ru","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played_ru","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked_ru","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri_ru","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm_ru","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util_ru","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr_ru","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested_cis","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played_cis","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked_cis","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri_cis","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm_cis","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util_cis","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr_cis","order"=>"",'url'=>""],
			
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Потери",'index'=>"poteri","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }


      //||||||| угадайга

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection('videotest')->getPdo();
		$pdodd = \DB::connection('video_')->getPdo();
$roga=0;
$dex=$data=explode("-",$to);
$yy=intval($dex[0]);
if($yy<=2018){
$mu=intval(preg_replace('/^0/i','',$dex[1]));
if($mu<10){
$roga=1;
}
//
//    var_dump($mu); die();
}

if(!$roga){


		$sql="
create temp table source_stat_detail_desc as
select t0.day
,t0.title
,t0.id
,t_ru.requested as requested_ru
,t_ru.started as started_ru
,t_ru.played as played_ru
,t_ru.completed as completed_ru
,t_ru.clicked as clicked_ru
,0 as poteri_ru
,case when t_ru.played>0 then round(t_ru.completed/t_ru.played::numeric,4)*100 else 0 end as dosm_ru 
,case when t_ru.requested >0 then round(t_ru.played/t_ru.requested::numeric,4)*100 else 0 end as util_ru 
,case when t_ru.played>0 then round(t_ru.clicked/t_ru.played::numeric,4)*100 else 0 end as ctr_ru 
,t_cis.requested as requested_cis
,t_cis.started as started_cis
,t_cis.played as played_cis
,t_cis.completed as completed_cis
,t_cis.clicked as clicked_cis
,0 as poteri_cis
,case when t_cis.played>0 then round(t_cis.completed/t_cis.played::numeric,4)*100 else 0 end as dosm_cis 
,case when t_cis.requested >0 then round(t_cis.played/t_cis.requested::numeric,4)*100 else 0 end as util_cis
,case when t_cis.played>0 then round(t_cis.clicked/t_cis.played::numeric,4)*100 else 0 end as ctr_cis 
,t0.requested as requested
,t0.started as started
,t0.played as played
,t0.completed as completed
,t0.clicked as clicked
,0 as poteri
,case when t0.played>0 then round(t0.completed/t0.played::numeric,4)*100 else 0 end as dosm
,case when t0.requested >0 then round(t0.played/t0.requested::numeric,4)*100 else 0 end as util
,case when t0.played>0 then round(t0.clicked/t0.played::numeric,4)*100 else 0 end as ctr
from (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and  day  between '$from' and  '$to'
and mobile=0
group by day,title,id_src
) t0
left join (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='RU'
and  day  between '$from' and  '$to'
and mobile=0
group by day,title,id_src
) t_ru
on t_ru.day=t0.day
and t_ru.id=t0.id
left join (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='CIS'
and mobile=0
and  day  between '$from' and  '$to'
group by day,title,id_src
) t_cis
on t_cis.day=t0.day
and t_cis.id=t0.id

";
		//echo nl2br($sql);
		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
 
		$sql="
create temp table source_stat_detail_all_mobile as
select 
t0.title
,t0.id
,t_ru.requested as requested_ru
,t_ru.started as started_ru
,t_ru.played as played_ru
,t_ru.completed as completed_ru
,t_ru.clicked as clicked_ru
,0 as poteri_ru
,case when t_ru.played>0 then round(t_ru.completed/t_ru.played::numeric,4)*100 else 0 end as dosm_ru 
,case when t_ru.requested >0 then round(t_ru.played/t_ru.requested::numeric,4)*100 else 0 end as util_ru 
,case when t_ru.played>0 then round(t_ru.clicked/t_ru.played::numeric,4)*100 else 0 end as ctr_ru 
,t_cis.requested as requested_cis
,t_cis.started as started_cis
,t_cis.played as played_cis
,t_cis.completed as completed_cis
,t_cis.clicked as clicked_cis
,0 as poteri_cis
,case when t_cis.played>0 then round(t_cis.completed/t_cis.played::numeric,4)*100 else 0 end as dosm_cis 
,case when t_cis.requested >0 then round(t_cis.played/t_cis.requested::numeric,4)*100 else 0 end as util_cis
,case when t_cis.played>0 then round(t_cis.clicked/t_cis.played::numeric,4)*100 else 0 end as ctr_cis 
,t0.requested as requested
,t0.started as started
,t0.played as played
,t0.completed as completed
,t0.clicked as clicked
,0 as poteri
,case when t0.played>0 then round(t0.completed/t0.played::numeric,4)*100 else 0 end as dosm
,case when t0.requested >0 then round(t0.played/t0.requested::numeric,4)*100 else 0 end as util
,case when t0.played>0 then round(t0.clicked/t0.played::numeric,4)*100 else 0 end as ctr
from (
select
title,id_src as id,sum(case when mobile = 1 then  requested else 0 end) as requested
,sum(case when mobile = 1 then started else 0 end) as started
,sum(case when mobile = 1 then played else 0 end) as played
,sum(case when mobile = 1 then  completed else 0 end) as completed
,sum(case when mobile = 1 then  clicked  else 0 end) as clicked
from  _stat_src_pages
where id_src='$id'
and  day  between '$from' and  '$to'
group by title,id_src
) t0
left join (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='RU'
and  day  between '$from' and  '$to'
and mobile=1
group by title,id_src
) t_ru
on t_ru.id=t0.id
left join (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='CIS'
and  day  between '$from' and  '$to'
and mobile=1
group by title,id_src
) t_cis
on 
t_cis.id=t0.id
";

		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);


		$sql="
create temp table source_stat_detail_mobile as 
select  t0.day
,t0.title
,t0.id
,t_ru.requested as requested_ru
,t_ru.started as started_ru
,t_ru.played as played_ru
,t_ru.completed as completed_ru
,t_ru.clicked as clicked_ru
,0 as poteri_ru
,case when t_ru.played>0 then round(t_ru.completed/t_ru.played::numeric,4)*100 else 0 end as dosm_ru 
,case when t_ru.requested >0 then round(t_ru.played/t_ru.requested::numeric,4)*100 else 0 end as util_ru 
,case when t_ru.played>0 then round(t_ru.clicked/t_ru.played::numeric,4)*100 else 0 end as ctr_ru 
,t_cis.requested as requested_cis
,t_cis.started as started_cis
,t_cis.played as played_cis
,t_cis.completed as completed_cis
,t_cis.clicked as clicked_cis
,0 as poteri_cis
,case when t_cis.played>0 then round(t_cis.completed/t_cis.played::numeric,4)*100 else 0 end as dosm_cis 
,case when t_cis.requested >0 then round(t_cis.played/t_cis.requested::numeric,4)*100 else 0 end as util_cis
,case when t_cis.played>0 then round(t_cis.clicked/t_cis.played::numeric,4)*100 else 0 end as ctr_cis 
,t0.requested as requested
,t0.started as started
,t0.played as played
,t0.completed as completed
,t0.clicked as clicked
,0 as poteri
,case when t0.played>0 then round(t0.completed/t0.played::numeric,4)*100 else 0 end as dosm
,case when t0.requested >0 then round(t0.played/t0.requested::numeric,4)*100 else 0 end as util
,case when t0.played>0 then round(t0.clicked/t0.played::numeric,4)*100 else 0 end as ctr
from (
select
day,title,id_src as id,sum(case when mobile = 1 then  requested else 0 end) as requested
,sum(case when mobile = 1 then started else 0 end) as started
,sum(case when mobile = 1 then played else 0 end) as played
,sum(case when mobile = 1 then  completed else 0 end) as completed
,sum(case when mobile = 1 then  clicked  else 0 end) as clicked
from  _stat_src_pages
where id_src='$id'
and  day  between '$from' and  '$to'
group by day,title,id_src
) t0
left join (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='RU'
and  day  between '$from' and  '$to'
and mobile=1
group by day,title,id_src
) t_ru
on t_ru.day=t0.day
and  t_ru.id=t0.id
left join (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='CIS'
and  day  between '$from' and  '$to'
and mobile=1
group by day,title,id_src
) t_cis
on  t_cis.day=t0.day
and t_cis.id=t0.id
";

		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);





		$sql="
create temp table source_stat_detail_all_desc as 
select 
t0.title
,t0.id
,t_ru.requested as requested_ru
,t_ru.started as started_ru
,t_ru.played as played_ru
,t_ru.completed as completed_ru
,t_ru.clicked as clicked_ru
,0 as poteri_ru
,case when t_ru.played>0 then round(t_ru.completed/t_ru.played::numeric,4)*100 else 0 end as dosm_ru 
,case when t_ru.requested >0 then round(t_ru.played/t_ru.requested::numeric,4)*100 else 0 end as util_ru 
,case when t_ru.played>0 then round(t_ru.clicked/t_ru.played::numeric,4)*100 else 0 end as ctr_ru 
,t_cis.requested as requested_cis
,t_cis.started as started_cis
,t_cis.played as played_cis
,t_cis.completed as completed_cis
,t_cis.clicked as clicked_cis
,0 as poteri_cis
,case when t_cis.played>0 then round(t_cis.completed/t_cis.played::numeric,4)*100 else 0 end as dosm_cis 
,case when t_cis.requested >0 then round(t_cis.played/t_cis.requested::numeric,4)*100 else 0 end as util_cis
,case when t_cis.played>0 then round(t_cis.clicked/t_cis.played::numeric,4)*100 else 0 end as ctr_cis 
,t0.requested as requested
,t0.started as started
,t0.played as played
,t0.completed as completed
,t0.clicked as clicked
,0 as poteri
,case when t0.played>0 then round(t0.completed/t0.played::numeric,4)*100 else 0 end as dosm
,case when t0.requested >0 then round(t0.played/t0.requested::numeric,4)*100 else 0 end as util
,case when t0.played>0 then round(t0.clicked/t0.played::numeric,4)*100 else 0 end as ctr
from (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and  day  between '$from' and  '$to'
and mobile=0
group by title,id_src
) t0
left join (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='RU'
and  day  between '$from' and  '$to'
and mobile=0
group by title,id_src
) t_ru
on t_ru.id=t0.id
left join (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='CIS'
and  day  between '$from' and  '$to'
and mobile=0
group by title,id_src
) t_cis
on 
t_cis.id=t0.id

";
		//echo nl2br($sql);
		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$sql="
create temp table source_stat_detail_all as
select t0.day
,t0.title
,t0.id
,t_ru.requested as requested_ru
,t_ru.started as started_ru
,t_ru.played as played_ru
,t_ru.completed as completed_ru
,t_ru.clicked as clicked_ru
,0 as poteri_ru
,case when t_ru.played>0 then round(t_ru.completed/t_ru.played::numeric,4)*100 else 0 end as dosm_ru 
,case when t_ru.requested >0 then round(t_ru.played/t_ru.requested::numeric,4)*100 else 0 end as util_ru 
,case when t_ru.played>0 then round(t_ru.clicked/t_ru.played::numeric,4)*100 else 0 end as ctr_ru 
,t_cis.requested as requested_cis
,t_cis.started as started_cis
,t_cis.played as played_cis
,t_cis.completed as completed_cis
,t_cis.clicked as clicked_cis
,0 as poteri_cis
,case when t_cis.played>0 then round(t_cis.completed/t_cis.played::numeric,4)*100 else 0 end as dosm_cis 
,case when t_cis.requested >0 then round(t_cis.played/t_cis.requested::numeric,4)*100 else 0 end as util_cis
,case when t_cis.played>0 then round(t_cis.clicked/t_cis.played::numeric,4)*100 else 0 end as ctr_cis 
,t0.requested as requested
,t0.started as started
,t0.played as played
,t0.completed as completed
,t0.clicked as clicked
,0 as poteri
,case when t0.played>0 then round(t0.completed/t0.played::numeric,4)*100 else 0 end as dosm
,case when t0.requested >0 then round(t0.played/t0.requested::numeric,4)*100 else 0 end as util
,case when t0.played>0 then round(t0.clicked/t0.played::numeric,4)*100 else 0 end as ctr
from (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and  day  between '$from' and  '$to'

group by day,title,id_src
) t0
left join (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='RU'
and  day  between '$from' and  '$to'

group by day,title,id_src
) t_ru
on t_ru.day=t0.day
and t_ru.id=t0.id
left join (
select
day,title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='CIS'

and  day  between '$from' and  '$to'
group by day,title,id_src
) t_cis
on t_cis.day=t0.day
and t_cis.id=t0.id

";
		//echo nl2br($sql);
		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);



		$sql="
create temp table source_stat_detail_all_sum as 
select 
t0.title
,t0.id
,t_ru.requested as requested_ru
,t_ru.started as started_ru
,t_ru.played as played_ru
,t_ru.completed as completed_ru
,t_ru.clicked as clicked_ru
,0 as poteri_ru
,case when t_ru.played>0 then round(t_ru.completed/t_ru.played::numeric,4)*100 else 0 end as dosm_ru 
,case when t_ru.requested >0 then round(t_ru.played/t_ru.requested::numeric,4)*100 else 0 end as util_ru 
,case when t_ru.played>0 then round(t_ru.clicked/t_ru.played::numeric,4)*100 else 0 end as ctr_ru 
,t_cis.requested as requested_cis
,t_cis.started as started_cis
,t_cis.played as played_cis
,t_cis.completed as completed_cis
,t_cis.clicked as clicked_cis
,0 as poteri_cis
,case when t_cis.played>0 then round(t_cis.completed/t_cis.played::numeric,4)*100 else 0 end as dosm_cis 
,case when t_cis.requested >0 then round(t_cis.played/t_cis.requested::numeric,4)*100 else 0 end as util_cis
,case when t_cis.played>0 then round(t_cis.clicked/t_cis.played::numeric,4)*100 else 0 end as ctr_cis 
,t0.requested as requested
,t0.started as started
,t0.played as played
,t0.completed as completed
,t0.clicked as clicked
,0 as poteri
,case when t0.played>0 then round(t0.completed/t0.played::numeric,4)*100 else 0 end as dosm
,case when t0.requested >0 then round(t0.played/t0.requested::numeric,4)*100 else 0 end as util
,case when t0.played>0 then round(t0.clicked/t0.played::numeric,4)*100 else 0 end as ctr
from (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and  day  between '$from' and  '$to'
group by title,id_src
) t0
left join (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='RU'
and  day  between '$from' and  '$to'

group by title,id_src
) t_ru
on  t_ru.id=t0.id
left join (
select
title,id_src as id,sum(requested) as requested
,sum(started) as started
,sum(played) as played
,sum(completed) as completed
,sum(clicked) as clicked
from  _stat_src_pages
where id_src='$id'
and country='CIS'
and  day  between '$from' and  '$to'
group by title,id_src
) t_cis
on  t_cis.id=t0.id
";


		$pdodd->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
$sumStatDesc=\DB::connection('video_')->table('source_stat_detail_all_desc')->first();
$sumStatMobile=\DB::connection('video_')->table('source_stat_detail_all_mobile')->first();
$sumStatAll=\DB::connection('video_')->table('source_stat_detail_all_sum')->first();
$stats_all=\DB::connection('video_')->table('source_stat_detail_all')->orderBy($order,$direct)->paginate($number);
$stats_desc=\DB::connection('video_')->table('source_stat_detail_desc')->orderBy($order,$direct)->paginate($number);
$stats_mobile=\DB::connection('video_')->table('source_stat_detail_mobile')->orderBy($order,$direct)->paginate($number);
} else{


		$sql="create temp table source_stat_detail_desc as 
		select coalesce(t2.day,t3.day) as day, t1.id, t1.title as title, t1.player as player, sum(coalesce(t2.requested,0)) as requested_ru, sum(coalesce(t2.started,0)) as started_ru, 
		sum(coalesce(t2.played,0)) as played_ru, sum(coalesce(t2.completed,0)) as completed_ru, sum(coalesce(t2.clicked,0)) as clicked_ru, 
		case when (sum(coalesce(t2.started,0))>0) then 100-(round(sum(coalesce(t2.played,0))/sum(coalesce(t2.started,0))::numeric,4)*100) else 100 end as poteri_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.completed,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as dosm_ru, 
		case when(sum(coalesce(t2.requested,0))>0) then round(sum(coalesce(t2.played,0))/sum(coalesce(t2.requested,0))::numeric,4)*100 else 0 end as util_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.clicked,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as ctr_ru, 
		sum(coalesce(t3.requested,0)) as requested_cis, sum(coalesce(t3.started,0)) as started_cis, sum(coalesce(t3.played,0)) as played_cis, 
		sum(coalesce(t3.completed,0)) as completed_cis, sum(coalesce(t3.clicked,0)) as clicked_cis, case when (sum(coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t3.played,0))/sum(coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.completed,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm_cis, 
		case when(sum(coalesce(t3.requested,0))>0) then round(sum(coalesce(t3.played,0))/sum(coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.clicked,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr_cis, 
		sum(coalesce(t2.requested,0)+coalesce(t3.requested,0)) as requested, sum(coalesce(t2.started,0)+coalesce(t3.started,0)) as started, 
		sum(coalesce(t2.played,0)+coalesce(t3.played,0)) as played, sum(coalesce(t2.completed,0)+coalesce(t3.completed,0)) as completed, 
		sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0)) as clicked, case when (sum(coalesce(t2.started,0)+coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.started,0)+coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.completed,0)+coalesce(t3.completed,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm, case when(sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))>0) then round(sum(
		coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr

		from links t1 left join (select * from stat_src_pages where id_src='$id' and country='RU' and mobile='0') t2 on t1.id=t2.id_src 
		left join (select * from stat_src_pages where id_src='$id' and country='CIS' and mobile='0') t3 on t1.id=t3.id_src and t2.day=t3.day  and t2.mobile=t3.mobile
		where t1.id='$id' and coalesce(t2.day,t3.day) between '$from' and 
		'$to' group by coalesce(t2.day,t3.day),t1.id, t1.title, t1.player order by coalesce(t2.day,t3.day) desc";
		//echo nl2br($sql);
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		
		$sql="create temp table source_stat_detail_mobile as 
		select coalesce(t2.day,t3.day) as day, t1.id, t1.title as title, t1.player as player, sum(coalesce(t2.requested,0)) as requested_ru, sum(coalesce(t2.started,0)) as started_ru, 
		sum(coalesce(t2.played,0)) as played_ru, sum(coalesce(t2.completed,0)) as completed_ru, sum(coalesce(t2.clicked,0)) as clicked_ru, 
		case when (sum(coalesce(t2.started,0))>0) then 100-(round(sum(coalesce(t2.played,0))/sum(coalesce(t2.started,0))::numeric,4)*100) else 100 end as poteri_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.completed,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as dosm_ru, 
		case when(sum(coalesce(t2.requested,0))>0) then round(sum(coalesce(t2.played,0))/sum(coalesce(t2.requested,0))::numeric,4)*100 else 0 end as util_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.clicked,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as ctr_ru, 
		sum(coalesce(t3.requested,0)) as requested_cis, sum(coalesce(t3.started,0)) as started_cis, sum(coalesce(t3.played,0)) as played_cis, 
		sum(coalesce(t3.completed,0)) as completed_cis, sum(coalesce(t3.clicked,0)) as clicked_cis, case when (sum(coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t3.played,0))/sum(coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.completed,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm_cis, 
		case when(sum(coalesce(t3.requested,0))>0) then round(sum(coalesce(t3.played,0))/sum(coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.clicked,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr_cis, 
		sum(coalesce(t2.requested,0)+coalesce(t3.requested,0)) as requested, sum(coalesce(t2.started,0)+coalesce(t3.started,0)) as started, 
		sum(coalesce(t2.played,0)+coalesce(t3.played,0)) as played, sum(coalesce(t2.completed,0)+coalesce(t3.completed,0)) as completed, 
		sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0)) as clicked, case when (sum(coalesce(t2.started,0)+coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.started,0)+coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.completed,0)+coalesce(t3.completed,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm, case when(sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))>0) then round(sum(
		coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr

		from links t1 left join (select * from stat_src_pages where id_src='$id' and country='RU' and mobile='1') t2 on t1.id=t2.id_src 
		left join (select * from stat_src_pages where id_src='$id' and country='CIS' and mobile='1') t3 on t1.id=t3.id_src and t2.day=t3.day  and t2.mobile=t3.mobile
		where t1.id='$id' and coalesce(t2.day,t3.day) between '$from' and 
		'$to' group by coalesce(t2.day,t3.day),t1.id, t1.title, t1.player order by coalesce(t2.day,t3.day) desc";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$sql="create temp table source_stat_detail_all_desc as 
		select t1.id, t1.title as title, t1.player as player, sum(coalesce(t2.requested,0)) as requested_ru, sum(coalesce(t2.started,0)) as started_ru, 
		sum(coalesce(t2.played,0)) as played_ru, sum(coalesce(t2.completed,0)) as completed_ru, sum(coalesce(t2.clicked,0)) as clicked_ru, 
		case when (sum(coalesce(t2.started,0))>0) then 100-(round(sum(coalesce(t2.played,0))/sum(coalesce(t2.started,0))::numeric,4)*100) else 100 end as poteri_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.completed,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as dosm_ru, 
		case when(sum(coalesce(t2.requested,0))>0) then round(sum(coalesce(t2.played,0))/sum(coalesce(t2.requested,0))::numeric,4)*100 else 0 end as util_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.clicked,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as ctr_ru, 
		sum(coalesce(t3.requested,0)) as requested_cis, sum(coalesce(t3.started,0)) as started_cis, sum(coalesce(t3.played,0)) as played_cis, 
		sum(coalesce(t3.completed,0)) as completed_cis, sum(coalesce(t3.clicked,0)) as clicked_cis, case when (sum(coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t3.played,0))/sum(coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.completed,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm_cis, 
		case when(sum(coalesce(t3.requested,0))>0) then round(sum(coalesce(t3.played,0))/sum(coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.clicked,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr_cis, 
		sum(coalesce(t2.requested,0)+coalesce(t3.requested,0)) as requested, sum(coalesce(t2.started,0)+coalesce(t3.started,0)) as started, 
		sum(coalesce(t2.played,0)+coalesce(t3.played,0)) as played, sum(coalesce(t2.completed,0)+coalesce(t3.completed,0)) as completed, 
		sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0)) as clicked, case when (sum(coalesce(t2.started,0)+coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.started,0)+coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.completed,0)+coalesce(t3.completed,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm, case when(sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))>0) then round(sum(
		coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr

		from links t1 left join (select * from stat_src_pages where id_src='$id' and country='RU' and mobile='0' and day between '$from' and 
		'$to') t2 on t1.id=t2.id_src 
		left join (select * from stat_src_pages where id_src='$id' and country='CIS' and mobile='0' and day between '$from' and 
		'$to') t3 on t1.id=t3.id_src and t2.day=t3.day  and t2.mobile=t3.mobile
		where t1.id='$id' group by t1.id, t1.title, t1.player";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$sql="create temp table source_stat_detail_all_mobile as 
		select t1.id, t1.title as title, t1.player as player, sum(coalesce(t2.requested,0)) as requested_ru, sum(coalesce(t2.started,0)) as started_ru, 
		sum(coalesce(t2.played,0)) as played_ru, sum(coalesce(t2.completed,0)) as completed_ru, sum(coalesce(t2.clicked,0)) as clicked_ru, 
		case when (sum(coalesce(t2.started,0))>0) then 100-(round(sum(coalesce(t2.played,0))/sum(coalesce(t2.started,0))::numeric,4)*100) else 100 end as poteri_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.completed,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as dosm_ru, 
		case when(sum(coalesce(t2.requested,0))>0) then round(sum(coalesce(t2.played,0))/sum(coalesce(t2.requested,0))::numeric,4)*100 else 0 end as util_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.clicked,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as ctr_ru, 
		sum(coalesce(t3.requested,0)) as requested_cis, sum(coalesce(t3.started,0)) as started_cis, sum(coalesce(t3.played,0)) as played_cis, 
		sum(coalesce(t3.completed,0)) as completed_cis, sum(coalesce(t3.clicked,0)) as clicked_cis, case when (sum(coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t3.played,0))/sum(coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.completed,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm_cis, 
		case when(sum(coalesce(t3.requested,0))>0) then round(sum(coalesce(t3.played,0))/sum(coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.clicked,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr_cis, 
		sum(coalesce(t2.requested,0)+coalesce(t3.requested,0)) as requested, sum(coalesce(t2.started,0)+coalesce(t3.started,0)) as started, 
		sum(coalesce(t2.played,0)+coalesce(t3.played,0)) as played, sum(coalesce(t2.completed,0)+coalesce(t3.completed,0)) as completed, 
		sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0)) as clicked, case when (sum(coalesce(t2.started,0)+coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.started,0)+coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.completed,0)+coalesce(t3.completed,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm, case when(sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))>0) then round(sum(
		coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr

		from links t1 left join (select * from stat_src_pages where id_src='$id' and country='RU' and mobile='1' and day between '$from' and 
		'$to') t2 on t1.id=t2.id_src 
		left join (select * from stat_src_pages where id_src='$id' and country='CIS' and mobile='1' and day between '$from' and 
		'$to') t3 on t1.id=t3.id_src and t2.day=t3.day  and t2.mobile=t3.mobile
		where t1.id='$id' group by t1.id, t1.title, t1.player";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$sql="create temp table source_stat_detail_all as 
		select coalesce(t2.day,t3.day) as day, t1.id, t1.title as title, t1.player as player, sum(coalesce(t2.requested,0)) as requested_ru, sum(coalesce(t2.started,0)) as started_ru, 
		sum(coalesce(t2.played,0)) as played_ru, sum(coalesce(t2.completed,0)) as completed_ru, sum(coalesce(t2.clicked,0)) as clicked_ru, 
		case when (sum(coalesce(t2.started,0))>0) then 100-(round(sum(coalesce(t2.played,0))/sum(coalesce(t2.started,0))::numeric,4)*100) else 100 end as poteri_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.completed,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as dosm_ru, 
		case when(sum(coalesce(t2.requested,0))>0) then round(sum(coalesce(t2.played,0))/sum(coalesce(t2.requested,0))::numeric,4)*100 else 0 end as util_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.clicked,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as ctr_ru, 
		sum(coalesce(t3.requested,0)) as requested_cis, sum(coalesce(t3.started,0)) as started_cis, sum(coalesce(t3.played,0)) as played_cis, 
		sum(coalesce(t3.completed,0)) as completed_cis, sum(coalesce(t3.clicked,0)) as clicked_cis, case when (sum(coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t3.played,0))/sum(coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.completed,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm_cis, 
		case when(sum(coalesce(t3.requested,0))>0) then round(sum(coalesce(t3.played,0))/sum(coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.clicked,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr_cis, 
		sum(coalesce(t2.requested,0)+coalesce(t3.requested,0)) as requested, sum(coalesce(t2.started,0)+coalesce(t3.started,0)) as started, 
		sum(coalesce(t2.played,0)+coalesce(t3.played,0)) as played, sum(coalesce(t2.completed,0)+coalesce(t3.completed,0)) as completed, 
		sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0)) as clicked, case when (sum(coalesce(t2.started,0)+coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.started,0)+coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.completed,0)+coalesce(t3.completed,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm, case when(sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))>0) then round(sum(
		coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr

		from links t1 left join (select * from stat_src_pages where id_src='$id' and country='RU') t2 on t1.id=t2.id_src 
		left join (select * from stat_src_pages where id_src='$id' and country='CIS') t3 on t1.id=t3.id_src and t2.day=t3.day and t2.mobile=t3.mobile
		where t1.id='$id' and coalesce(t2.day,t3.day) between '$from' and 
		'$to' group by coalesce(t2.day,t3.day),t1.id, t1.title, t1.player order by coalesce(t2.day,t3.day) desc";
		
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$sql="create temp table source_stat_detail_all_sum as 
		select t1.id, t1.title as title, t1.player as player, sum(coalesce(t2.requested,0)) as requested_ru, sum(coalesce(t2.started,0)) as started_ru, 
		sum(coalesce(t2.played,0)) as played_ru, sum(coalesce(t2.completed,0)) as completed_ru, sum(coalesce(t2.clicked,0)) as clicked_ru, 
		case when (sum(coalesce(t2.started,0))>0) then 100-(round(sum(coalesce(t2.played,0))/sum(coalesce(t2.started,0))::numeric,4)*100) else 100 end as poteri_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.completed,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as dosm_ru, 
		case when(sum(coalesce(t2.requested,0))>0) then round(sum(coalesce(t2.played,0))/sum(coalesce(t2.requested,0))::numeric,4)*100 else 0 end as util_ru, 
		case when(sum(coalesce(t2.played,0))>0) then round(sum(coalesce(t2.clicked,0))/sum(coalesce(t2.played,0))::numeric,4)*100 else 0 end as ctr_ru, 
		sum(coalesce(t3.requested,0)) as requested_cis, sum(coalesce(t3.started,0)) as started_cis, sum(coalesce(t3.played,0)) as played_cis, 
		sum(coalesce(t3.completed,0)) as completed_cis, sum(coalesce(t3.clicked,0)) as clicked_cis, case when (sum(coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t3.played,0))/sum(coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.completed,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm_cis, 
		case when(sum(coalesce(t3.requested,0))>0) then round(sum(coalesce(t3.played,0))/sum(coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util_cis, 
		case when(sum(coalesce(t3.played,0))>0) then round(sum(coalesce(t3.clicked,0))/sum(coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr_cis, 
		sum(coalesce(t2.requested,0)+coalesce(t3.requested,0)) as requested, sum(coalesce(t2.started,0)+coalesce(t3.started,0)) as started, 
		sum(coalesce(t2.played,0)+coalesce(t3.played,0)) as played, sum(coalesce(t2.completed,0)+coalesce(t3.completed,0)) as completed, 
		sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0)) as clicked, case when (sum(coalesce(t2.started,0)+coalesce(t3.started,0))>0) then 
		100-(round(sum(coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.started,0)+coalesce(t3.started,0))::numeric,4)*100) else 100 end as poteri, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.completed,0)+coalesce(t3.completed,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as dosm, case when(sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))>0) then round(sum(
		coalesce(t2.played,0)+coalesce(t3.played,0))/sum(coalesce(t2.requested,0)+coalesce(t3.requested,0))::numeric,4)*100 else 0 end as util, 
		case when(sum(coalesce(t2.played,0)+coalesce(t3.played,0))>0) then round(sum(coalesce(t2.clicked,0)+coalesce(t3.clicked,0))/sum(coalesce(t2.played,0)+
		coalesce(t3.played,0))::numeric,4)*100 else 0 end as ctr

		from links t1 left join (select * from stat_src_pages where id_src='$id' and country='RU' and day between '$from' and 
		'$to') t2 on t1.id=t2.id_src 
		left join (select * from stat_src_pages where id_src='$id' and country='CIS' and day between '$from' and 
		'$to') t3 on t1.id=t3.id_src and t2.day=t3.day  and t2.mobile=t3.mobile
		where t1.id='$id' group by t1.id, t1.title, t1.player";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);


		
		$sumStatDesc=\DB::connection('videotest')->table('source_stat_detail_all_desc')->first();
                
		$sumStatMobile=\DB::connection('videotest')->table('source_stat_detail_all_mobile')->first();

		$sumStatAll=\DB::connection('videotest')->table('source_stat_detail_all_sum')->first();

		$stats_all=\DB::connection('videotest')->table('source_stat_detail_all')->orderBy($order,$direct)->paginate($number);

		$stats_desc=\DB::connection('videotest')->table('source_stat_detail_desc')->orderBy($order,$direct)->paginate($number);

		$stats_mobile=\DB::connection('videotest')->table('source_stat_detail_mobile')->orderBy($order,$direct)->paginate($number);
} //end roga


		$players=\DB::connection('videotest')->table('videoplayer')->get();
		$videoSource=\DB::connection('videotest')->table('links')->where('id', $id)->first();



		return view('statistic.video.new_stat_source_detail', ['order'=>$order, 'direct'=>$direct
                ,'number'=>$number, 'sumStatDesc'=>$sumStatDesc, 'sumStatMobile'=>$sumStatMobile
                ,'stats_desc'=>$stats_desc
                ,'stats_mobile'=>$stats_mobile
                ,'title'=>$title
                ,'header'=>$header
                ,'from'=>$from
                ,'to'=>$to
                ,'players'=>$players
                ,'videoSource'=>$videoSource
		,'sumStatAll'=>$sumStatAll
                , 'stats_all'=>$stats_all]);
	}
	
	public function newGraph(Request $request){
		\Auth::user()->touch();
		$period=$request->input('period')?$request->input('period'):12;
		$to=date('Y-m-d H:i:s');
        $from=date('Y-m-d H:i:s',time()-3600*$period);
		$id_src=$request->input('id_src');
		$requested=$request->input('requested');
		
		if (!empty($id_src)){
			$srcs=implode(',',$id_src);
		}
		else{
			$srcs=0;
			$requested=0;
		}
		$pdo = \DB::connection('videotest')->getPdo();
		if (isset($requested)){
		$pgsql="select t1.datetime ,t1.id_src ,t1.color ,t1.title ,coalesce(t2.cnt,0) as cnt from (select t1.datetime, t2.id_src,
		coalesce(t2.color,'black') as color, coalesce(t2.title,'запросы') as title from (select datetime from videostatistic_graph where datetime
		between '$from' and '$to' and datetime not in (select max(datetime) from videostatistic_graph) group by datetime) t1 cross join 
		(select t1.id_src, t2.color, t2.title from (select id_src from videostatistic_graph where datetime between '$from' and '$to' group by id_src) 
		t1 left join (SELECT id,coalesce(color,'grey') as color,title from links) t2 
		on t2.id =t1.id_src) t2) t1 left join (select * from videostatistic_graph where datetime between '$from' and '$to') t2 on 
		t2.datetime =t1.datetime and t2.id_src=t1.id_src where t1.id_src in ($srcs, $requested) order by t1.datetime ,t1.id_src";
		}
		else{
		$pgsql="select t1.datetime ,t1.id_src ,t1.color ,t1.title ,coalesce(t2.cnt,0) as cnt from (select t1.datetime, t2.id_src,
		coalesce(t2.color,'black') as color, coalesce(t2.title,'запросы') as title from (select datetime from videostatistic_graph where datetime
		between '$from' and '$to' and datetime not in (select max(datetime) from videostatistic_graph) group by datetime) t1 cross join 
		(select t1.id_src, t2.color, t2.title from (select id_src from videostatistic_graph where datetime between '$from' and '$to' group by id_src) 
		t1 left join (SELECT id,coalesce(color,'grey') as color,title from links) t2 
		on t2.id =t1.id_src) t2) t1 left join (select * from videostatistic_graph where datetime between '$from' and '$to') t2 on 
		t2.datetime =t1.datetime and t2.id_src=t1.id_src where t1.id_src in ($srcs) order by t1.datetime ,t1.id_src";
		}
//	echo $pgsql."\n";	 die();
		
		
        $graph_values = $pdo->query($pgsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$graph_x=[];
		foreach ($graph_values as $g_v){
			$graph_x[date("m-d H:i",strtotime($g_v['datetime']))][$g_v['title']]=$g_v['cnt'];
			$graph_y[$g_v['title']][date("m-d H:i",strtotime($g_v['datetime']))]=$g_v['cnt'];
			$graph_color[]=$g_v['color'];
		}
		$graph_xx=[];
		$graph=Charts::multi('line', 'morris');
		foreach ($graph_y as $k=>$g){
			$graph->dataset($k, array_values($g));
		}
		$graph->title(" ");
		$graph->height(400);
		$graph->colors($graph_color);
		$graph->labels(array_keys($graph_x));
		//$links=\App\MPW\Sources\VideoSource::orderBy('title', 'ASC')->get();
		$sql="select * from links order by title asc";
		$links = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		return view('statistic.video.new_stat_graph', ['period'=>$period, 'links'=>$links, 'graph'=>$graph, 'id_src'=>$id_src, 'requested'=>$requested]);
    }
	
	public function summaryStat(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		$ole=$request->input('ole');
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
			['title'=>"Видимость",'index'=>"viewable","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		if ($ole==1){
		$sql="create temp table summary_ru as select t4.day, t4.mobile, t1.manager, sum(coalesce(t4.loaded,0)) as loaded, 
		sum(coalesce(t4.calculate,0)) as calculate, sum(coalesce(t4.played,0)) as played,
		sum(coalesce(t4.one_played,0)) as one_played,
		sum(coalesce(t4.completed,0)) as completed, sum(coalesce(t4.clicks,0)) as clicks, sum(coalesce(t4.started,0)) as started, 
		sum(coalesce(t4.summa,0)) as summa, 
		sum(coalesce(t4.second,0)) as second, sum(coalesce(t4.second_all,0)) as second_all, sum(coalesce(t4.second_summa,0)) as second_summa,
		round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos where id<>'974') t3 on t2.id=t3.wid_id
		left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, mobile, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef >0 then coef end) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from pid_summa_full where day between ''$from'' and ''$to'' and 
		country=''RU'' group by day, pid, mobile') AS p(day date, mobile int, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t3.id=t4.pid
		where t1.manager is not null and t4.day is not null group by t1.manager, t4.day, t4.mobile";

		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table summary_cis as select t4.day, t4.mobile, t1.manager, sum(coalesce(t4.loaded,0)) as loaded, 
		sum(coalesce(t4.calculate,0)) as calculate, sum(coalesce(t4.played,0)) as played, 
		sum(coalesce(t4.one_played,0)) as one_played, 
		sum(coalesce(t4.completed,0)) as completed, sum(coalesce(t4.clicks,0)) as clicks, sum(coalesce(t4.started,0)) as started, 
		sum(coalesce(t4.summa,0)) as summa,
		sum(coalesce(t4.second,0)) as second, sum(coalesce(t4.second_all,0)) as second_all, sum(coalesce(t4.second_summa,0)) as second_summa,
		round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos where id<>'974') t3 on t2.id=t3.wid_id
		left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, mobile, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef >0 then coef end) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable 
		from pid_summa_full where day between ''$from'' and ''$to'' 
		and country<>''RU'' group by day, pid, mobile') AS p(day date, mobile, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t3.id=t4.pid
		where t1.manager is not null and t4.day is not null group by t1.manager, t4.day, t4.mobile";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table summary as select t4.day, t4.mobile, t1.manager, sum(coalesce(t4.loaded,0)) as loaded, sum(coalesce(t4.calculate,0)) as calculate, 
		sum(coalesce(t4.played,0)) as played, sum(coalesce(t4.one_played,0)) as one_played, 
		sum(coalesce(t4.completed,0)) as completed, sum(coalesce(t4.clicks,0)) as clicks, sum(coalesce(t4.started,0)) as started, 
		sum(coalesce(t4.summa,0)) as summa, 
		sum(coalesce(t4.second,0)) as second, sum(coalesce(t4.second_all,0)) as second_all, sum(coalesce(t4.second_summa,0)) as second_summa, 
		round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos where id<>'974') t3 on t2.id=t3.wid_id
		left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, mobile, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef >0 then coef end) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable 
		from pid_summa_full where day between ''$from'' and ''$to'' 
		group by day, pid, mobile') AS p(day date, mobile int, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t3.id=t4.pid
		where t1.manager is not null and t4.day is not null group by t1.manager, t4.day, t4.mobile";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		}
		else{
		$sql="create temp table summary_ru as select t4.day, t4.mobile, t1.manager, sum(coalesce(t4.loaded,0)) as loaded, 
		sum(coalesce(t4.calculate,0)) as calculate, sum(coalesce(t4.played,0)) as played,
		sum(coalesce(t4.one_played,0)) as one_played,
		sum(coalesce(t4.completed,0)) as completed, sum(coalesce(t4.clicks,0)) as clicks, sum(coalesce(t4.started,0)) as started, 
		sum(coalesce(t4.summa,0)) as summa, 
		sum(coalesce(t4.second,0)) as second, sum(coalesce(t4.second_all,0)) as second_all, sum(coalesce(t4.second_summa,0)) as second_summa, 
		round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, mobile, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef >0 then coef end) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from pid_summa_full where day between ''$from'' and ''$to'' and 
		country=''RU'' group by day, pid, mobile') AS p(day date, mobile int, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t3.id=t4.pid
		where t1.manager is not null and t4.day is not null group by t1.manager, t4.day, t4.mobile";

		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table summary_cis as select t4.day, t4.mobile, t1.manager, sum(coalesce(t4.loaded,0)) as loaded, 
		sum(coalesce(t4.calculate,0)) as calculate, sum(coalesce(t4.played,0)) as played, 
		sum(coalesce(t4.one_played,0)) as one_played, 
		sum(coalesce(t4.completed,0)) as completed, sum(coalesce(t4.clicks,0)) as clicks, sum(coalesce(t4.started,0)) as started, 
		sum(coalesce(t4.summa,0)) as summa,
		sum(coalesce(t4.second,0)) as second, sum(coalesce(t4.second_all,0)) as second_all, sum(coalesce(t4.second_summa,0)) as second_summa, 
		round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, mobile, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef >0 then coef end) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable 
		from pid_summa_full where day between ''$from'' and ''$to'' 
		and country<>''RU'' group by day, pid, mobile') AS p(day date, mobile int, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t3.id=t4.pid
		where t1.manager is not null and t4.day is not null group by t1.manager, t4.day, t4.mobile";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table summary as select t4.day, t4.mobile, t1.manager, sum(coalesce(t4.loaded,0)) as loaded, sum(coalesce(t4.calculate,0)) as calculate, 
		sum(coalesce(t4.played,0)) as played, sum(coalesce(t4.one_played,0)) as one_played, 
		sum(coalesce(t4.completed,0)) as completed, sum(coalesce(t4.clicks,0)) as clicks, sum(coalesce(t4.started,0)) as started, 
		sum(coalesce(t4.summa,0)) as summa, 
		sum(coalesce(t4.second,0)) as second, sum(coalesce(t4.second_all,0)) as second_all, sum(coalesce(t4.second_summa,0)) as second_summa, 
		round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, mobile, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef >0 then coef end) as coef,
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable 
		from pid_summa_full where day between ''$from'' and ''$to'' 
		group by day, pid, mobile') AS p(day date, mobile int, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t3.id=t4.pid
		where t1.manager is not null and t4.day is not null group by t1.manager, t4.day, t4.mobile";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		}
		if (!$manager){
			$ru_summary_all=\DB::connection()->table('summary_ru')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, 
		sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->first();
			$ru_summary_all_pc=\DB::connection()->table('summary_ru')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, 
		sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->first();
			$ru_summary_all_mob=\DB::connection()->table('summary_ru')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, 
		sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->first();
			$cis_summary_all=\DB::connection()->table('summary_cis')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->first();
			$cis_summary_all_pc=\DB::connection()->table('summary_cis')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->first();
			$cis_summary_all_mob=\DB::connection()->table('summary_cis')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->first();
			$summary_all=\DB::connection()->table('summary')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->first();
			$summary_all_pc=\DB::connection()->table('summary')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->first();
			$summary_all_mob=\DB::connection()->table('summary')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->first();
			$summarys=\DB::connection()->table('summary')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$summarys_pc=\DB::connection()->table('summary')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$summarys_mob=\DB::connection()->table('summary')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$ru_summarys=\DB::connection()->table('summary_ru')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$ru_summarys_pc=\DB::connection()->table('summary_ru')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$ru_summarys_mob=\DB::connection()->table('summary_ru')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$cis_summarys=\DB::connection()->table('summary_cis')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$cis_summarys_pc=\DB::connection()->table('summary_cis')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$cis_summarys_mob=\DB::connection()->table('summary_cis')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->groupBy('day')->orderBy($order,$direct)->paginate($number);
		}
		else{
			$ru_summary_all=\DB::connection()->table('summary_ru')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->first();
			$ru_summary_all_pc=\DB::connection()->table('summary_ru')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 0)->first();
			$ru_summary_all_mob=\DB::connection()->table('summary_ru')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 1)->first();
			$cis_summary_all=\DB::connection()->table('summary_cis')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->first();
			$cis_summary_all_pc=\DB::connection()->table('summary_cis')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->where('manager', $manager)->first();
			$cis_summary_all_mob=\DB::connection()->table('summary_cis')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->where('manager', $manager)->first();
			$summary_all=\DB::connection()->table('summary')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->first();
			$summary_all_pc=\DB::connection()->table('summary')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 0)->first();
			$summary_all_mob=\DB::connection()->table('summary')->select(\DB::raw('sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 1)->first();
			$summarys=\DB::connection()->table('summary')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$summarys_pc=\DB::connection()->table('summary')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 0)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$summarys_mob=\DB::connection()->table('summary')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 1)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$ru_summarys=\DB::connection()->table('summary_ru')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$ru_summarys_pc=\DB::connection()->table('summary_ru')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 0)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$ru_summarys_mob=\DB::connection()->table('summary_ru')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->where('mobile', 1)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$cis_summarys=\DB::connection()->table('summary_cis')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$cis_summarys_pc=\DB::connection()->table('summary_cis')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 0)->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
			$cis_summarys_mob=\DB::connection()->table('summary_cis')->select(\DB::raw('day, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(completed) as 
		completed, sum(clicks) as clicks, sum(started) as started, sum(coalesce(second,0)) as second, sum(coalesce(second_all,0)) as second_all, 
		sum(coalesce(second_summa,0)) as second_summa, 
		case when(sum(loaded)>0) then round(sum(one_played)/sum(loaded)::numeric,4)*100 
		else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::numeric,4) else 0 end as deep, 
		case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		sum(summa) as summa, round(avg(case when coef >0 then coef end)::numeric,4) as coef,
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('mobile', 1)->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
		}
		
		return view('statistic.video.summary_stat', ['ole'=>$ole, 'number'=>$number, 'header'=>$header, 'manager'=>$manager, 'order'=>$order, 'direct'=>$direct, 'summary_all'=>$summary_all, 
		'summary_all_pc'=>$summary_all_pc, 'summary_all_mob'=>$summary_all_mob,
		'cis_summary_all'=>$cis_summary_all, 'cis_summary_all_pc'=>$cis_summary_all_pc, 'cis_summary_all_mob'=>$cis_summary_all_mob,
		'ru_summary_all'=>$ru_summary_all, 'ru_summary_all_pc'=>$ru_summary_all_pc, 'ru_summary_all_mob'=>$ru_summary_all_mob, 
		'from'=>$from, 'to'=>$to, 'summarys'=>$summarys,
		'summarys_pc'=>$summarys_pc, 'summarys_mob'=>$summarys_mob,
		'ru_summarys'=>$ru_summarys, 'ru_summarys_pc'=>$ru_summarys_pc, 'ru_summarys_mob'=>$ru_summarys_mob,
		'cis_summarys'=>$cis_summarys, 'cis_summarys_pc'=>$cis_summarys_pc, 'cis_summarys_mob'=>$cis_summarys_mob]);
	}
	
	public function summaryPads(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$search=$request->input('search');
		$sear_category=null;
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		$category=$request->input('category');
		if ($category){
			if ($category=='all'){
				$category=null;
			}
			else if ($category=='white'){
				$sear_category=0;
			}
			else if($category=='adult'){
				$sear_category=1;
			}
			else if ($category=='razv'){
				$sear_category=2;
			}
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Домен",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зач. показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
			['title'=>"Видимость",'index'=>"viewable","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table pads_stats as select t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t2.manager, 
		coalesce(sum(t5.summa),0) as summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, coalesce(sum(t5.completed),0) as completed, 
		coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa, 
		coalesce(sum(t5.started),0) as started, case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr, coalesce(t5.coef,0) as coef, 
		coalesce(sum(t5.ads_requested),0) as ads_requested, coalesce(sum(t5.ads_viewable),0) as ads_viewable,
		case when(sum(t5.ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
		from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef > 0 then coef end) as coef, 
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		
		from pid_summa_full where day between ''$from'' and ''$to'' 
		group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, 
		clicks int, started int, second int, second_all int, second_summa numeric(18,4), coef numeric (4,2), ads_requested int, ads_viewable int)) 
		t5 on t4.id=t5.pid 
		group by t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t2.manager, t5.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		if ($search){
			if (\Auth::user()->hasRole('manager')){
				$category=null;
				$pads_stats=\DB::connection()->table('pads_stats')->where('manager', \Auth::user()->id)->
					where(function($query) use ($search) {
					$query->where('name', '~*', trim($search))
					->orWhere('email', '~*', trim($search))
					->orWhere('domain', '~*', trim($search));
					})->
					orderBy($order,$direct)->paginate($number);
				$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
				coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
				coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
				coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
				case when(sum(loaded)>0) 
				then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then 
				round(sum(played)/sum(one_played)::
				numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
				when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
				round(avg(case when coef > 0 then coef end)::numeric,2) as coef,
				case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', \Auth::user()->id)->
				where(function($query) use ($search) {
					$query->where('name', '~*', trim($search))
					->orWhere('email', '~*', trim($search))
					->orWhere('domain', '~*', trim($search));
					})->first();
			}
			else{
				if (\Auth::user()->hasRole('manager')){
					$category=null;
					$pads_stats=\DB::connection()->table('pads_stats')->where('manager', \Auth::user()->id)->
						where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->
						orderBy($order,$direct)->paginate($number);
					$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
					coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
					coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
					coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(loaded)>0) 
					then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then 
					round(sum(played)/sum(one_played)::
					numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
					when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
					round(avg(case when coef > 0 then coef end)::numeric,2) as coef, 
					case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', \Auth::user()->id)->
					where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->first();
				}
				else{
					$category=null;
					$pads_stats=\DB::connection()->table('pads_stats')->
						where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->
						orderBy($order,$direct)->paginate($number);
					$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
					coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
					coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
					coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(loaded)>0) 
					then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then 
					round(sum(played)/sum(one_played)::
					numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
					when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
					round(avg(case when coef > 0 then coef end)::numeric,2) as coef, 
					case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->
					where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->first();
				}
			}
		}
		else{
			if (\Auth::user()->hasRole('manager')){
			$pads_stats=\DB::connection()->table('pads_stats')->where('manager', \Auth::user()->id)->orderBy($order,$direct)->paginate($number);
			$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
			coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then 
			round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
			round(avg(case when coef > 0 then coef end)::numeric,2) as coef, 
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->where('manager', \Auth::user()->id)->first();
			}
			else{
			$pads_stats=\DB::connection()->table('pads_stats')->orderBy($order,$direct)->paginate($number);
			$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
			coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then 
			round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
			round(avg(case when coef > 0 then coef end)::numeric,2) as coef, 
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->first();
			}
		}
		if ($category){
			$pads_stats=\DB::connection()->table('pads_stats')->where('video_categories', $sear_category)->orderBy($order,$direct)->paginate($number);
		$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		round(avg(case when coef > 0 then coef end)::numeric,2) as coef, 
		case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))->
		where('video_categories', $sear_category)->first();
		}
		return view('statistic.video.pads_stat', ['number'=>$number, 'category'=>$category, 'pads_stat_all'=>$pads_stat_all, 'search'=>$search, 'header'=>$header, 'pads_stats'=>$pads_stats, 'from'=>$from, 'to'=>$to, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function summaryPadsComparison(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$fromOld=$request->input('fromOld');
		$toOld=$request->input('toOld');
		$search=$request->input('search');
		$sear_category=null;
		$category=$request->input('category');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if ($category){
			if ($category=='all'){
				$category=null;
			}
			else if ($category=='white'){
				$sear_category=0;
			}
			else if($category=='adult'){
				$sear_category=1;
			}
			else if ($category=='razv'){
				$sear_category=2;
			}
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		if(!($fromOld||$toOld)){
			$fromOld=$toOld=date('Y-m-d',time()-3600*24);
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Домен",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table pads_stats as select t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t2.manager, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa, 
		coalesce(sum(t5.started),0) as started, case when(sum(t5.loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(t5.completed)/sum(t5.played)::numeric,4)*100 else 0 end as dosm, case when(sum(t5.played)>0) then round(sum(t5.clicks)/sum(t5.played)::
		numeric,4)*100 else 0 end as ctr,
		coalesce(sum(t6.summa),0) as 
		old_summa, coalesce(sum(t6.loaded),0) as old_loaded, coalesce(sum(t6.calculate),0) as old_calculate, 
		coalesce(sum(t6.played),0) as old_played, coalesce(sum(t6.one_played),0) as old_one_played, 
		coalesce(sum(t6.completed),0) as old_completed, coalesce(sum(t6.clicks),0) as old_clicks, 
		coalesce(sum(t6.second),0) as old_second, coalesce(sum(t6.second_all),0) as old_second_all, coalesce(sum(t6.second_summa),0) as old_second_summa, 
		coalesce(sum(t6.started),0) as old_started, case when(sum(t6.loaded)>0) then round(sum(t6.one_played)/sum(t6.loaded)::numeric,4)*100 else 0 end as 
		old_util, case when(sum(t6.one_played)>0) then round(sum(t6.played)/sum(t6.one_played)::numeric,4) else 0 end as old_deep, case when(sum(t6.played)>0) 
		then round(sum(t6.completed)/sum(t6.played)::numeric,4)*100 else 0 end as old_dosm, case when(sum(t6.played)>0) then round(sum(t6.clicks)/sum(t6.played)::
		numeric,4)*100 else 0 end as old_ctr
		from partner_pads t1 left join 
		(select * from user_profiles) t2 on t1.user_id=t2.user_id left join 
		(select * from widgets) t3 on t1.id=t3.pad left join 
		(select * from widget_videos) t4 on t3.id=t4.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) 
		as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
		sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, 
		sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full where day between ''$from'' and ''$to'' group by pid') 
		AS p(pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4))) t5 on t4.id=t5.pid left join
		(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) 
		as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
		sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, 
		sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full where day between ''$fromOld'' and ''$toOld'' group by pid') 
		AS p(pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4))) t6 on t4.id=t6.pid
		where t1.type=2 or t1.type=3 group by t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t2.manager";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		if ($search){
			if (\Auth::user()->hasRole('manager')){
				$category=null;
				$pads_stats=\DB::connection()->table('pads_stats')->where('manager', \Auth::user()->id)->
					where(function($query) use ($search) {
					$query->where('name', '~*', trim($search))
					->orWhere('email', '~*', trim($search))
					->orWhere('domain', '~*', trim($search));
					})->
					orderBy($order,$direct)->paginate($number);
				$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
				coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
				coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
				case when(sum(loaded)>0) 
				then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
				numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
				when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
				coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, coalesce(sum(old_played),0) as old_played, 
				coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
				coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa,
				case when(sum(old_loaded)>0) 
				then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
				numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
				when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
				'))->where('manager', \Auth::user()->id)->
				where(function($query) use ($search) {
					$query->where('name', '~*', trim($search))
					->orWhere('email', '~*', trim($search))
					->orWhere('domain', '~*', trim($search));
					})->first();
			}
			else{
				if (\Auth::user()->hasRole('manager')){
					$category=null;
					$pads_stats=\DB::connection()->table('pads_stats')->where('manager', \Auth::user()->id)->
						where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->
						orderBy($order,$direct)->paginate($number);
					$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
					coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
					coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(loaded)>0) 
					then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
					numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
					when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
					coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, coalesce(sum(old_played),0) as old_played, 
					coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
					coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa, 
					case when(sum(old_loaded)>0) 
					then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
					numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
					when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
					'))->where('manager', \Auth::user()->id)->
					where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->first();
				}
				else{
					$category=null;
					$pads_stats=\DB::connection()->table('pads_stats')->
						where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->
						orderBy($order,$direct)->paginate($number);
					$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
					coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
					coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(loaded)>0) 
					then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
					numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
					when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
					coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, coalesce(sum(old_played),0) as old_played, 
					coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
					coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa, 
					case when(sum(old_loaded)>0) 
					then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
					numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
					when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
					'))->
					where(function($query) use ($search) {
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
						})->first();
				}
			}
		}
		else{
			if (\Auth::user()->hasRole('manager')){
			$pads_stats=\DB::connection()->table('pads_stats')->where('manager', \Auth::user()->id)->orderBy($order,$direct)->paginate($number);
			$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
			coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, coalesce(sum(old_played),0) as old_played, 
			coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
			coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa, 
			case when(sum(old_loaded)>0) 
			then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
			numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
			when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
			'))->where('manager', \Auth::user()->id)->first();
			}
			else{
			$pads_stats=\DB::connection()->table('pads_stats')->orderBy($order,$direct)->paginate($number);
			$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
			coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, coalesce(sum(old_played),0) as old_played, 
			coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
			coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa, 
			case when(sum(old_loaded)>0) 
			then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
			numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
			when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
			'))->first();
			}
		}
		if ($category){
			$pads_stats=\DB::connection()->table('pads_stats')->where('video_categories', $sear_category)->orderBy($order,$direct)->paginate($number);
		$pads_stat_all=\DB::connection()->table('pads_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, coalesce(sum(old_played),0) as old_played, 
		coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
		coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa, 
		case when(sum(old_loaded)>0) 
		then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
		numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as ctr
		'))->
		where('video_categories', $sear_category)->first();
		}
		return view('statistic.video.pads_stat_comparison', ['number'=>$number, 'fromOld'=>$fromOld, 'toOld'=>$toOld, 'category'=>$category, 'pads_stat_all'=>$pads_stat_all, 'search'=>$search, 'header'=>$header, 'pads_stats'=>$pads_stats, 'from'=>$from, 'to'=>$to, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function statsPad($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table pad_stats as select t5.day, t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, 
		coalesce(sum(t5.summa),0) as summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full 
		where day between ''$from'' and ''$to'' group by day, pid') AS p(day date, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, second int, second_all int, 
		second_summa numeric(18,4))) t5 on t4.id=t5.pid 
		where t1.id=$id and t5.day is not null group by t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t5.day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table pad_stats_ru as select t5.day, t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full 
		where day between ''$from'' and ''$to'' and country=''RU'' group by day, pid') AS p(day date, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, second int, second_all int, second_summa numeric(18,4))) t5 on t4.id=t5.pid 
		where t1.id=$id and t5.day is not null group by t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t5.day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table pad_stats_cis as select t5.day, t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, 
		coalesce(sum(t5.summa),0) as summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, coalesce(sum(t5.completed),0) as completed, 
		coalesce(sum(t5.clicks),0) as clicks, coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full 
		where day between ''$from'' and ''$to'' and country<>''RU'' group by day, pid') AS p(day date, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, second int, 
		second_all int, second_summa numeric(18,4))) t5 on t4.id=t5.pid 
		where t1.id=$id and t5.day is not null group by t1.id, t1.domain, t1.video_categories, t2.name, t2.email, t2.user_id, t5.day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$pad_stat_all=\DB::connection()->table('pad_stats')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr'))->first();
		$pad_stat_all_ru=\DB::connection()->table('pad_stats_ru')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr'))->first();
		$pad_stat_all_cis=\DB::connection()->table('pad_stats_cis')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr'))->first();
		$pad_stats=\DB::connection()->table('pad_stats')->orderBy($order,$direct)->paginate($number);
		$pad_stats_ru=\DB::connection()->table('pad_stats_ru')->orderBy($order,$direct)->paginate($number);
		$pad_stats_cis=\DB::connection()->table('pad_stats_cis')->orderBy($order,$direct)->paginate($number);
		$pad=\App\PartnerPad::where('id', $id)->first();
		return view('statistic.video.detail_pads_stat', ['number'=>$number, 'order'=>$order, 'direct'=>$direct, 'pad'=>$pad, 'header'=>$header, 'pad_stat_all_ru'=>$pad_stat_all_ru, 'pad_stat_all_cis'=>$pad_stat_all_cis, 'pad_stats_cis'=>$pad_stats_cis, 'pad_stats_ru'=>$pad_stats_ru, 'pad_stat_all'=>$pad_stat_all, 'pad_stats'=>$pad_stats, 'from'=>$from, 'to'=>$to]);
	}
	
	public function partnerVideoStat(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$search=$request->input('search');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$manager=$request->input('manager');
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Домен",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		
		$sql="create temp table partner_video_stat as select array_to_string(array_agg(distinct t1.domain),', ') as domain, t2.name, t2.manager, t2.email, t2.user_id, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr, round(avg(t5.coef)::numeric,2) as coef from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, avg(case when coef > 0 then coef end) as coef from pid_summa_full where day between ''$from'' and ''$to'' 
		group by pid') AS p(pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4), coef numeric(4,2))) t5 on t4.id=t5.pid 
		where t1.type=2 or t1.type=3 or t1.type='-1' or t1.type=6 group by t2.name, t2.manager, t2.email, t2.user_id";
		//echo nl2br($sql);
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		if ($search){
		if (\Auth::user()->hasRole('manager')){
			$partner_stats=\DB::connection()->table('partner_video_stat')->where('manager', \Auth::user()->id)->
			where(function($query) use ($search) {
			$query->where('name', '~*', trim($search))
			->orWhere('email', '~*', trim($search))
			->orWhere('domain', '~*', trim($search));
			})->
			orderBy($order,$direct)->paginate($number);
			$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
			coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr,round(avg(case when coef > 0 then coef end)::numeric,2) as coef'))->where('manager', \Auth::user()->id)->
			where(function($query) use ($search) {
				$query->where('name', '~*', trim($search))
				->orWhere('email', '~*', trim($search))
				->orWhere('domain', '~*', trim($search));
				})->first();
		}
		else{
		$partner_stats=\DB::connection()->table('partner_video_stat')->
			where(function($query) use ($search) {
			$query->where('name', '~*', trim($search))
			->orWhere('email', '~*', trim($search))
			->orWhere('domain', '~*', trim($search));
			})->
			orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr,round(avg(case when coef > 0 then coef end)::numeric,2) as coef'))->
		where(function($query) use ($search) {
			$query->where('name', '~*', trim($search))
			->orWhere('email', '~*', trim($search))
			->orWhere('domain', '~*', trim($search));
			})->first();
		}
		}
		else{
		if (\Auth::user()->hasRole('manager')){
		$partner_stats=\DB::connection()->table('partner_video_stat')->where('manager', \Auth::user()->id)->orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr,round(avg(case when coef > 0 then coef end)::numeric,2) as coef'))->where('manager', \Auth::user()->id)->first();
		}
		else{
		$partner_stats=\DB::connection()->table('partner_video_stat')->orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr,round(avg(case when coef > 0 then coef end)::numeric,2) as coef'))->first();
		}
		}
		if ($manager and $manager!=0 and !\Auth::user()->hasRole('manager')){
			$partner_stats=\DB::connection()->table('partner_video_stat')->
			where(function($query) use ($manager) {
			$query->where('manager', $manager);
			})->
			orderBy($order,$direct)->paginate($number);
			$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
			coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr,round(avg(case when coef > 0 then coef end)::numeric,2) as coef'))->
			where(function($query) use ($manager) {
			$query->where('manager', $manager);
			})->first();
		}
		return view('statistic.video.partner_stat', ['number'=>$number, 'order'=>$order, 'direct'=>$direct, 'manager'=>$manager, 'partner_all_stat'=>$partner_all_stat, 'partner_stats'=>$partner_stats, 'header'=>$header, 'from'=>$from, 'to'=>$to, 'search'=>$search]);
	}
	
	public function partnerVideoStatComparison(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$fromOld=$request->input('fromOld');
		$toOld=$request->input('toOld');
		$search=$request->input('search');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		if(!($fromOld||$toOld)){
			$fromOld=$toOld=date('Y-m-d', time()-3600*24);
        }
		$manager=$request->input('manager');
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Домен",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		
		$sql="create temp table partner_video_stat as select array_to_string(array_agg(distinct t1.domain),', ') as domain, t2.name, t2.manager, t2.email, t2.user_id, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(t5.loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(t5.completed)/sum(t5.played)::numeric,4)*100 else 0 end as dosm, case when(sum(t5.played)>0) then round(sum(t5.clicks)/sum(t5.played)::
		numeric,4)*100 else 0 end as ctr, 
		
		coalesce(sum(t6.summa),0) as old_summa, coalesce(sum(t6.loaded),0) as old_loaded, 
		coalesce(sum(t6.calculate),0) as old_calculate, coalesce(sum(t6.played),0) as old_played, 
		coalesce(sum(t6.one_played),0) as old_one_played, coalesce(sum(t6.completed),0) as old_completed, 
		coalesce(sum(t6.clicks),0) as old_clicks, coalesce(sum(t6.started),0) as old_started, coalesce(sum(t6.second),0) as old_second, 
		coalesce(sum(t6.second_all),0) as old_second_all, coalesce(sum(t6.second_summa),0) as old_second_summa,
		case when(sum(t6.loaded)>0) then round(sum(t6.one_played)/sum(t6.loaded)::numeric,4)*100 else 0 end as 
		old_util, case when(sum(t6.one_played)>0) then round(sum(t6.played)/sum(t6.one_played)::numeric,4) else 0 end as old_deep, case 
		when(sum(t6.played)>0) 
		then round(sum(t6.completed)/sum(t6.played)::numeric,4)*100 else 0 end as old_dosm, case when(sum(t6.played)>0) then 
		round(sum(t6.clicks)/sum(t6.played)::numeric,4)*100 else 0 end as old_ctr
		
		from partner_pads t1 left join 
		(select * from user_profiles) t2 on t1.user_id=t2.user_id left join 
		(select * from widgets) t3 on t1.id=t3.pad left join 
		(select * from widget_videos) t4 on t3.id=t4.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, 
		sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa 
		from pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4))) t5 on t4.id=t5.pid left join 
		(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa 
		from pid_summa_full where day between ''$fromOld'' and ''$toOld'' group by pid') AS p(pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4))) t6 on t4.id=t6.pid
		where t1.type=2 or t1.type=3 group by t2.name, t2.manager, t2.email, t2.user_id";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		if ($search){
		if (\Auth::user()->hasRole('manager')){
			$partner_stats=\DB::connection()->table('partner_video_stat')->where('manager', \Auth::user()->id)->
			where(function($query) use ($search) {
			$query->where('name', '~*', trim($search))
			->orWhere('email', '~*', trim($search))
			->orWhere('domain', '~*', trim($search));
			})->
			orderBy($order,$direct)->paginate($number);
			$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
			coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
			coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, 
			coalesce(sum(old_played),0) as old_played, 
			coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
			coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa,
			case when(sum(old_loaded)>0) 
			then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
			numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
			when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
			'))->where('manager', \Auth::user()->id)->
			where(function($query) use ($search) {
				$query->where('name', '~*', trim($search))
				->orWhere('email', '~*', trim($search))
				->orWhere('domain', '~*', trim($search));
				})->first();
		}
		else{
		$partner_stats=\DB::connection()->table('partner_video_stat')->
			where(function($query) use ($search) {
			$query->where('name', '~*', trim($search))
			->orWhere('email', '~*', trim($search))
			->orWhere('domain', '~*', trim($search));
			})->
			orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, 
		coalesce(sum(old_played),0) as old_played, 
		coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
		coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa,
		case when(sum(old_loaded)>0) 
		then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
		numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
		when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
		'))->
		where(function($query) use ($search) {
			$query->where('name', '~*', trim($search))
			->orWhere('email', '~*', trim($search))
			->orWhere('domain', '~*', trim($search));
			})->first();
		}
		}
		else{
		if (\Auth::user()->hasRole('manager')){
		$partner_stats=\DB::connection()->table('partner_video_stat')->where('manager', \Auth::user()->id)->orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(one_played),0) as one_played,
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, 
		coalesce(sum(old_played),0) as old_played, 
		coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
		coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa,
		case when(sum(old_loaded)>0) 
		then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
		numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
		when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
		'))->where('manager', \Auth::user()->id)->first();
		}
		else{
		$partner_stats=\DB::connection()->table('partner_video_stat')->orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
		coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, 
		coalesce(sum(old_played),0) as old_played, 
		coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
		coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa,
		case when(sum(old_loaded)>0) 
		then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
		numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
		when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
		'))->first();
		}
		}
		if ($manager and $manager!=0 and !\Auth::user()->hasRole('manager')){
			$partner_stats=\DB::connection()->table('partner_video_stat')->
			where(function($query) use ($manager) {
			$query->where('manager', $manager);
			})->
			orderBy($order,$direct)->paginate($number);
			$partner_all_stat=\DB::connection()->table('partner_video_stat')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
			coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
			coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
			coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa,
			case when(sum(loaded)>0) 
			then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
			numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
			when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr, 
			coalesce(sum(old_summa),0) as old_summa, coalesce(sum(old_loaded),0) as old_loaded, coalesce(sum(old_calculate),0) as old_calculate, 
			coalesce(sum(old_played),0) as old_played, 
			coalesce(sum(old_completed),0) as old_completed, coalesce(sum(old_clicks),0) as old_clicks, coalesce(sum(old_started),0) as old_started, 
			coalesce(sum(old_second),0) as old_second, coalesce(sum(old_second_all),0) as old_second_all, coalesce(sum(old_second_summa),0) as old_second_summa,
			case when(sum(old_loaded)>0) 
			then round(sum(old_one_played)/sum(old_loaded)::numeric,4)*100 else 0 end as old_util, case when(sum(old_one_played)>0) then round(sum(old_played)/sum(old_one_played)::
			numeric,4) else 0 end as old_deep, case when(sum(old_played)>0) then round(sum(old_completed)/sum(old_played)::numeric,4)*100 else 0 end as old_dosm, case 
			when(sum(old_played)>0) then round(sum(old_clicks)/sum(old_played)::numeric,4)*100 else 0 end as old_ctr
			'))->
			where(function($query) use ($manager) {
			$query->where('manager', $manager);
			})->first();
		}
		return view('statistic.video.partner_stat_comparison', ['number'=>$number, 'order'=>$order, 'direct'=>$direct, 'fromOld'=>$fromOld, 'toOld'=>$toOld, 'manager'=>$manager, 'partner_all_stat'=>$partner_all_stat, 'partner_stats'=>$partner_stats, 'header'=>$header, 'from'=>$from, 'to'=>$to, 'search'=>$search]);
	}
	
	public function partnerDetailVideoStat($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"День",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table partner_video_stat_all as select t5.day, t2.name, t2.email, t2.user_id, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full where day between ''$from'' and ''$to'' group by pid, 
		day') AS p(day date, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4))) t5 on t4.id=t5.pid 
		where t2.user_id='$id' and t5.day is not null group by t2.name, t2.email, t2.user_id, t5.day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table partner_video_stat_ru as select t5.day, t2.name, t2.email, t2.user_id, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, 
		coalesce(sum(t5.completed),0) as completed, coalesce(sum(t5.clicks),0) as clicks, 
		coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full where day between ''$from'' and ''$to'' 
		and country=''RU'' group by pid, day') AS p(day date, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4))) t5 on t4.id=t5.pid 
		where t2.user_id='$id' and t5.day is not null group by t2.name, t2.email, t2.user_id, t5.day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="create temp table partner_video_stat_cis as select t5.day, t2.name, t2.email, t2.user_id, coalesce(sum(t5.summa),0) as 
		summa, coalesce(sum(t5.loaded),0) as loaded, coalesce(sum(t5.calculate),0) as calculate, 
		coalesce(sum(t5.played),0) as played, coalesce(sum(t5.one_played),0) as one_played, coalesce(sum(t5.completed),0) as 
		completed, coalesce(sum(t5.clicks),0) as clicks, coalesce(sum(t5.started),0) as started, 
		coalesce(sum(t5.second),0) as second, coalesce(sum(t5.second_all),0) as second_all, coalesce(sum(t5.second_summa),0) as second_summa,
		case when(sum(loaded)>0) then round(sum(t5.one_played)/sum(t5.loaded)::numeric,4)*100 else 0 end as 
		util, case when(sum(t5.one_played)>0) then round(sum(t5.played)/sum(t5.one_played)::numeric,4) else 0 end as deep, case when(sum(t5.played)>0) 
		then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case when(sum(played)>0) then round(sum(clicks)/sum(played)::
		numeric,4)*100 else 0 end as ctr from partner_pads t1 left join (select * from user_profiles) t2 on t1.user_id=t2.user_id left join (select * 
		from widgets) t3 on t1.id=t3.pad left join (select * from widget_videos) t4 on t3.id=t4.wid_id left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
		sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as 
		second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa from pid_summa_full where day between ''$from'' and ''$to'' 
		and country<>''RU'' group by pid, day') AS p(day date, pid int, summa numeric(18,4), 
		loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
		second int, second_all int, second_summa numeric(18,4)
		)) t5 on t4.id=t5.pid 
		where t2.user_id='$id' and t5.day is not null group by t2.name, t2.email, t2.user_id, t5.day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$partner_stats=\DB::connection()->table('partner_video_stat_all')->orderBy($order,$direct)->paginate($number);
		$partner_all_stat=\DB::connection()->table('partner_video_stat_all')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr'))->first();
		$partner_ru_stats=\DB::connection()->table('partner_video_stat_ru')->orderBy($order,$direct)->paginate($number);
		$partner_ru_all_stats=\DB::connection()->table('partner_video_stat_ru')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr'))->first();
		$partner_cis_stats=\DB::connection()->table('partner_video_stat_cis')->orderBy($order,$direct)->paginate($number);
		$partner_cis_all_stats=\DB::connection()->table('partner_video_stat_cis')->select(\DB::raw('coalesce(sum(summa),0) as summa, 
		coalesce(sum(loaded),0) as loaded, coalesce(sum(calculate),0) as calculate, coalesce(sum(played),0) as played, 
		coalesce(sum(completed),0) as completed, coalesce(sum(clicks),0) as clicks, coalesce(sum(started),0) as started, 
		coalesce(sum(second),0) as second, coalesce(sum(second_all),0) as second_all, coalesce(sum(second_summa),0) as second_summa, 
		case when(sum(loaded)>0) 
		then round(sum(one_played)/sum(loaded)::numeric,4)*100 else 0 end as util, case when(sum(one_played)>0) then round(sum(played)/sum(one_played)::
		numeric,4) else 0 end as deep, case when(sum(played)>0) then round(sum(completed)/sum(played)::numeric,4)*100 else 0 end as dosm, case 
		when(sum(played)>0) then round(sum(clicks)/sum(played)::numeric,4)*100 else 0 end as ctr'))->first();
		$userProf=\App\UserProfile::where('user_id', $id)->first();
		return view('statistic.video.partner_stat_detail', ['number'=>$number, 'order'=>$order, 'direct'=>$direct, 'userProf'=>$userProf, 'partner_cis_stats'=>$partner_cis_stats, 'partner_cis_all_stats'=>$partner_cis_all_stats, 'partner_ru_stats'=>$partner_ru_stats, 'partner_ru_all_stats'=>$partner_ru_all_stats,'partner_all_stat'=>$partner_all_stat, 'partner_stats'=>$partner_stats, 'header'=>$header, 'from'=>$from, 'to'=>$to]);
	}
	
	public function statsPid($id, Request $request){
		\Auth::user()->touch();
		$prov_video=\App\WidgetVideo::where('id', $id)->first();
			if ($prov_video){
				$prov_widget=\App\MPW\Widgets\Widget::where('id', $prov_video->wid_id)->first();
				if ($prov_widget){
					if (!\Auth::user()->hasRole("admin") and !\Auth::user()->hasRole("super_manager") and !\Auth::user()->hasRole("manager") and \Auth::user()->id!=$prov_widget->user_id){
						return abort(403);
					}
				}
				else{
					return abort(403);
				}
			}
			else{
				return abort(403);
			}
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		
		$user=\App\User::find($prov_widget->user_id);
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"День",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"deep","order"=>"",'url'=>""],
			['title'=>"Утиль",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"dosm","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Все повторы",'index'=>"second_all","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
			['title'=>"Видимость",'index'=>"viewable","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table pid_statistic_all as select t1.id, t3.domain, t4.day, coalesce(sum(t4.summa),0) as summa,  
			coalesce(sum(t4.loaded),0) as loaded, coalesce(sum(t4.calculate),0) as calculate, coalesce(sum(t4.played),0) as played, 
			coalesce(sum(t4.one_played),0) as one_played,
			coalesce(sum(t4.completed),0) as completed, coalesce(sum(t4.clicks),0) as clicks, coalesce(sum(t4.started),0) as started,
			coalesce(sum(t4.second),0) as second, coalesce(sum(t4.second_all),0) as second_all, coalesce(sum(t4.second_summa),0) as second_summa, 
			coalesce(sum(t4.lease_summa),0) as lease_summa, 
			case when(sum(t4.loaded)>0) then round(sum(t4.one_played)/sum(t4.loaded)::numeric,4)*100 else 0 end as util, 
			case when(sum(t4.one_played)>0) then round(sum(t4.played)/sum(t4.one_played)::numeric,4) else 0 end as deep, 
			case when(sum(t4.played)>0) then round(sum(t4.completed)/sum(t4.played)::numeric,4)*100 else 0 end as dosm, 
			case when(sum(t4.played)>0) then round(sum(t4.clicks)/sum(t4.played)::numeric,4)*100 else 0 end as ctr,
			coalesce(t4.coef,0) as coef,
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
			from widget_videos t1 left join (select * from widgets) t2 on t1.wid_id=t2.id left join (select * from partner_pads) t3 on t2.pad=t3.id left join 
			(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, sum(summa+control_summa) as summa, 
			sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
			sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
			sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, 
			sum(second_expensive_all+second_cheap_all) as second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, 
			sum(lease_summa) as lease_summa, avg(case when coef > 0 then coef end) as coef, 
			sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
			from pid_summa_full where day between ''$from'' and ''$to'' group by pid, day') AS 
			p(day date, pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
			second int, second_all int, second_summa numeric(18,4), lease_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) 
			t4 on t1.id=t4.pid where t1.id='$id'  group by t1.id, t4.day, t3.domain, t4.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$sql="create temp table pid_statistic_ru as select t1.id, t3.domain, t4.day, coalesce(sum(t4.summa),0) as summa, 
			coalesce(sum(t4.loaded),0) as loaded, coalesce(sum(t4.calculate),0) as calculate, coalesce(sum(t4.played),0) as played, 
			coalesce(sum(t4.one_played),0) as one_played,
			coalesce(sum(t4.completed),0) as completed, coalesce(sum(t4.clicks),0) as clicks, coalesce(sum(t4.started),0) as started, 
			coalesce(sum(t4.second),0) as second, coalesce(sum(t4.second_all),0) as second_all, coalesce(sum(t4.second_summa),0) as second_summa, 
			coalesce(sum(t4.lease_summa),0) as lease_summa,
			case when(sum(t4.loaded)>0) then round(sum(t4.one_played)/sum(t4.loaded)::numeric,4)*100 else 0 end as util, 
			case when(sum(t4.one_played)>0) then round(sum(t4.played)/sum(t4.one_played)::numeric,4) else 0 end as deep, 
			case when(sum(t4.played)>0) then round(sum(t4.completed)/sum(t4.played)::numeric,4)*100 else 0 end as dosm, 
			case when(sum(t4.played)>0) then round(sum(t4.clicks)/sum(t4.played)::numeric,4)*100 else 0 end as ctr,
			coalesce(t4.coef,0) as coef,
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
			from widget_videos t1 left join (select * from widgets) t2 on t1.wid_id=t2.id left join (select * from partner_pads) t3 on t2.pad=t3.id left join 
			(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, 
			sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
			sum(played+control_played) as played, sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
			sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, 
			sum(second_expensive_all+second_cheap_all) as second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, 
			sum(lease_summa) as lease_summa, avg(case when coef > 0 then coef end) as coef, 
			sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
			from pid_summa_full where country=''RU'' and day between ''$from'' and ''$to'' group by pid, day') AS 
			p(day date, pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int,
			second int, second_all int, second_summa numeric(18,4), lease_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) 
			t4 on t1.id=t4.pid where t1.id='$id'  group by t1.id, t4.day, t3.domain, t4.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$sql="create temp table pid_statistic_cis as select t1.id, t3.domain, t4.day, coalesce(sum(t4.summa),0) as summa, 
			coalesce(sum(t4.loaded),0) as loaded, coalesce(sum(t4.calculate),0) as calculate, coalesce(sum(t4.played),0) as played, 
			coalesce(sum(t4.one_played),0) as one_played,
			coalesce(sum(t4.completed),0) as completed, coalesce(sum(t4.clicks),0) as clicks, coalesce(sum(t4.started),0) as started, 
			coalesce(sum(t4.second),0) as second, coalesce(sum(t4.second_all),0) as second_all, coalesce(sum(t4.second_summa),0) as second_summa, 
			coalesce(sum(t4.lease_summa),0) as lease_summa,
			case when(sum(t4.loaded)>0) then round(sum(t4.one_played)/sum(t4.loaded)::numeric,4)*100 else 0 end as util, 
			case when(sum(t4.one_played)>0) then round(sum(t4.played)/sum(t4.one_played)::numeric,4) else 0 end as deep, 
			case when(sum(t4.played)>0) then round(sum(t4.completed)/sum(t4.played)::numeric,4)*100 else 0 end as dosm, 
			case when(sum(t4.played)>0) then round(sum(t4.clicks)/sum(t4.played)::numeric,4)*100 else 0 end as ctr,
			coalesce(t4.coef,0) as coef,
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
			from widget_videos t1 left join (select * from widgets) t2 on t1.wid_id=t2.id left join (select * from partner_pads) t3 on t2.pad=t3.id 
			left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select day, pid, 
			sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
			sum(played+control_played) as played, sum(one_played+control_one_played) as one_played, sum(completed) as completed, 
			sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, 
			sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
			sum(second_expensive_summa+second_cheap_summa) as second_summa, 
			sum(lease_summa) as lease_summa, avg(case when coef > 0 then coef end) as coef, 
			sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
			from pid_summa_full where country<>''RU'' and day between ''$from'' and ''$to'' group by pid, day') AS 
			p(day date, pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
			second int, second_all int, second_summa numeric(18,4), lease_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) 
			t4 on t1.id=t4.pid where t1.id='$id'  group by t1.id, t4.day, t3.domain, t4.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$sql="create temp table pid_statistic_sum_all as select t1.id, t3.domain, coalesce(sum(t4.summa),0) as summa, 
			coalesce(sum(t4.loaded),0) as loaded, coalesce(sum(t4.calculate),0) as calculate, coalesce(sum(t4.played),0) as played, 
			coalesce(sum(t4.one_played),0) as one_played,
			coalesce(sum(t4.completed),0) as completed, coalesce(sum(t4.clicks),0) as clicks, coalesce(sum(t4.started),0) as started, 
			coalesce(sum(t4.second),0) as second, coalesce(sum(t4.second_all),0) as second_all, coalesce(sum(t4.second_summa),0) as second_summa, 
			coalesce(sum(t4.lease_summa),0) as lease_summa,
			case when(sum(t4.loaded)>0) then round(sum(t4.one_played)/sum(t4.loaded)::numeric,4)*100 else 0 end as util, 
			case when(sum(t4.one_played)>0) then round(sum(t4.played)/sum(t4.one_played)::numeric,4) else 0 end as deep, 
			case when(sum(t4.played)>0) then round(sum(t4.completed)/sum(t4.played)::numeric,4)*100 else 0 end as dosm, 
			case when(sum(t4.played)>0) then round(sum(t4.clicks)/sum(t4.played)::numeric,4)*100 else 0 end as ctr,
			coalesce(t4.coef,0) as coef,
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
			from widget_videos t1 left join (select * from widgets) t2 on t1.wid_id=t2.id left join (select * from partner_pads) t3 on t2.pad=t3.id left join 
			(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) 
			as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
			sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
			sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, 
			sum(second_expensive_all+second_cheap_all) as second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, 
			sum(lease_summa) as lease_summa, avg(case when coef > 0 then coef end) as coef, 
			sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
			from pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS 
			p(pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
			second int, second_all int, second_summa numeric(18,4), lease_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) 
			t4 on t1.id=t4.pid where t1.id='$id'  group by t1.id, t3.domain, t4.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$sql="create temp table pid_statistic_sum_ru as select t1.id, t3.domain, coalesce(sum(t4.summa),0) as summa,  
			coalesce(sum(t4.loaded),0) as loaded, coalesce(sum(t4.calculate),0) as calculate, coalesce(sum(t4.played),0) as played, 
			coalesce(sum(t4.one_played),0) as one_played,
			coalesce(sum(t4.completed),0) as completed, coalesce(sum(t4.clicks),0) as clicks, coalesce(sum(t4.started),0) as started, 
			coalesce(sum(t4.second),0) as second, coalesce(sum(t4.second_all),0) as second_all, coalesce(sum(t4.second_summa),0) as second_summa, 
			coalesce(sum(t4.lease_summa),0) as lease_summa, 
			case when(sum(t4.loaded)>0) then round(sum(t4.one_played)/sum(t4.loaded)::numeric,4)*100 else 0 end as util, 
			case when(sum(t4.one_played)>0) then round(sum(t4.played)/sum(t4.one_played)::numeric,4) else 0 end as deep, 
			case when(sum(t4.played)>0) then round(sum(t4.completed)/sum(t4.played)::numeric,4)*100 else 0 end as dosm, 
			case when(sum(t4.played)>0) then round(sum(t4.clicks)/sum(t4.played)::numeric,4)*100 else 0 end as ctr,
			coalesce(t4.coef,0) as coef,
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
			from widget_videos t1 left join (select * from widgets) t2 on t1.wid_id=t2.id left join (select * from partner_pads) t3 on t2.pad=t3.id left join 
			(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(summa+control_summa) 
			as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, sum(played+control_played) as played, 
			sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
			sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, sum(second_expensive_all+second_cheap_all) as second_all, 
			sum(second_expensive_summa+second_cheap_summa) as second_summa, 
			sum(lease_summa) as lease_summa, avg(case when coef > 0 then coef end) as coef, 
			sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
			from pid_summa_full where country=''RU'' and day between ''$from'' and ''$to'' group by pid') AS 
			p(pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
			second int, second_all int, second_summa numeric(18,4), lease_summa numeric(18,4), coef numeric(4,2), ads_requested int, ads_viewable int)) 
			t4 on t1.id=t4.pid where t1.id='$id'  group by t1.id, t3.domain, t4.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$sql="create temp table pid_statistic_sum_cis as select t1.id, t3.domain, coalesce(sum(t4.summa),0) as summa, 
			coalesce(sum(t4.loaded),0) as loaded, coalesce(sum(t4.calculate),0) as calculate, coalesce(sum(t4.played),0) as played, 
			coalesce(sum(t4.one_played),0) as one_played, 
			coalesce(sum(t4.completed),0) as completed, coalesce(sum(t4.clicks),0) as clicks, coalesce(sum(t4.started),0) as started, 
			coalesce(sum(t4.second),0) as second, coalesce(sum(t4.second_all),0) as second_all, coalesce(sum(t4.second_summa),0) as second_summa, 
			coalesce(sum(t4.lease_summa),0) as lease_summa, 
			case when(sum(t4.loaded)>0) then round(sum(t4.one_played)/sum(t4.loaded)::numeric,4)*100 else 0 end as util, 
			case when(sum(t4.one_played)>0) then round(sum(t4.played)/sum(t4.one_played)::numeric,4) else 0 end as deep, 
			case when(sum(t4.played)>0) then round(sum(t4.completed)/sum(t4.played)::numeric,4)*100 else 0 end as dosm, 
			case when(sum(t4.played)>0) then round(sum(t4.clicks)/sum(t4.played)::numeric,4)*100 else 0 end as ctr,
			coalesce(t4.coef,0) as coef,
			case when(sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
			from widget_videos t1 left join (select * from widgets) t2 on t1.wid_id=t2.id left join (select * from partner_pads) t3 on t2.pad=t3.id left join 
			(SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, 
			sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
			sum(played+control_played) as played, sum(one_played+control_one_played) as one_played, sum(completed+control_completed) as completed, 
			sum(clicks+control_clicks) as clicks, sum(started+control_started) as started, sum(second_expensive+second_cheap) as second, 
			sum(second_expensive_all+second_cheap_all) as second_all, sum(second_expensive_summa+second_cheap_summa) as second_summa, 
			sum(lease_summa) as lease_summa, avg(case when coef > 0 then coef end) as coef, 
			sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
			from pid_summa_full where country<>''RU'' and day between ''$from'' and ''$to'' group by pid') AS 
			p(pid int, summa numeric(18,4), loaded int, calculate int, played int, one_played int, completed int, clicks int, started int, 
			second int, second_all int, second_summa numeric(18,4), lease_summa numeric(18,4), coef numeric (4,2), ads_requested int, ads_viewable int)) 
			t4 on t1.id=t4.pid where t1.id='$id'  group by t1.id, t3.domain, t4.coef";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		
		$pid_stat_sum_all=\DB::table('pid_statistic_sum_all')->first();
		$pid_stat_sum_ru=\DB::table('pid_statistic_sum_ru')->first();
		$pid_stat_sum_cis=\DB::table('pid_statistic_sum_cis')->first();
		$pid_stat_alls=\DB::table('pid_statistic_all')->orderBy('day', 'desc')->paginate(20);
		$pid_stat_rus=\DB::table('pid_statistic_ru')->orderBy('day', 'desc')->paginate(20);
		$pid_stat_ciss=\DB::table('pid_statistic_cis')->orderBy('day', 'desc')->paginate(20);
		return view('statistic.video.pid_stat_detail', ['user'=>$user, 'pid_stat_sum_all'=>$pid_stat_sum_all, 'pid_stat_sum_ru'=>$pid_stat_sum_ru, 'pid_stat_sum_cis'=>$pid_stat_sum_cis, 
		'pid_stat_alls'=>$pid_stat_alls, 'pid_stat_rus'=>$pid_stat_rus, 'pid_stat_ciss'=>$pid_stat_ciss, 'header'=>$header, 'from'=>$from, 'to'=>$to]);
	}
	
	public function statsPidPad($id, Request $request){
		\Auth::user()->touch();
		$prov_video=\App\WidgetVideo::where('id', $id)->first();
		if ($prov_video){
			$prov_widget=\App\MPW\Widgets\Widget::where('id', $prov_video->wid_id)->first();
			if (!$prov_widget){
				return abort(403);
			}
		}
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		
		$user=\App\User::find($prov_widget->user_id);
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"played";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Id",'index'=>"id_src","order"=>"",'url'=>""],
			['title'=>"Название",'index'=>"title","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicked","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
	$pdo = \DB::connection('video_')->getPdo();
		
		$sql="  create temp table pid_pad_temp as
select 
t1.pid 
, t1.id_src 
, t1.title
, '' as player
, sum(t1.requested) as requested
, sum(t1.started) as started
, sum(t1.played) as played
, sum(t1.completed) as completed
, sum(t1.clicked) as clicked 
from _pid_pad t1
where t1.pid='$id' and t1.day between '$from' and '$to'
group by
t1.pid
, t1.id_src
, t1.title
";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$pid_pads=\DB::connection('video_')->table('pid_pad_temp')->orderBy($order,$direct)->get();
		$pid_pad_all=\DB::connection('video_')->table('pid_pad_temp')->select(\DB::raw('coalesce(sum(requested),0) as requested, 
		coalesce(sum(started),0) as started, coalesce(sum(played),0) as played, coalesce(sum(completed),0) as completed, 
		coalesce(sum(clicked),0) as clicked'))->first();
		return view('statistic.video.pid_pad', ['id'=>$id, 'user'=>$user, 'pid_pads'=>$pid_pads, 'pid_pad_all'=>$pid_pad_all, 'header'=>$header, 'from'=>$from, 'to'=>$to]);

return


		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table pid_pad_temp as select t1.pid as pid, t1.id_src as id_src, t2.title as title, t3.title as player, sum(t1.requested) as requested, sum(t1.started) 
		as started, sum(t1.played) as played, sum(t1.completed) as completed, sum(t1.clicked) as clicked from pid_pad t1 left join (select id, 
		title, player from links) t2 on t1.id_src=t2.id left join (select id, title from videoplayer) t3 on t2.player=t3.id where t1.pid='$id' and t1.day between '$from' and '$to' group by t1.pid, t1.id_src, 
		t2.title, t3.title";
		
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$pid_pads=\DB::connection('videotest')->table('pid_pad_temp')->orderBy($order,$direct)->get();
		$pid_pad_all=\DB::connection('videotest')->table('pid_pad_temp')->select(\DB::raw('coalesce(sum(requested),0) as requested, 
		coalesce(sum(started),0) as started, coalesce(sum(played),0) as played, coalesce(sum(completed),0) as completed, 
		coalesce(sum(clicked),0) as clicked'))->first();
		return view('statistic.video.pid_pad', ['id'=>$id, 'user'=>$user, 'pid_pads'=>$pid_pads, 'pid_pad_all'=>$pid_pad_all, 'header'=>$header, 'from'=>$from, 'to'=>$to]);
	}
	
	public function pidUrlsStatistic($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d H:i',time()-3600*12);
			$to=date('Y-m-d H:i');
        }
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"percent_played";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Url адрес",'index'=>"url","order"=>"",'url'=>""],
			['title'=>"Загрузки",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачитанные показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"% загрузок",'index'=>"percent_requested","order"=>"",'url'=>""],
			['title'=>"% показов",'index'=>"percent_played","order"=>"",'url'=>""],
			['title'=>"% засчитанных показов",'index'=>"percent_calculate","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		
        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$sql="create temp table urls_stats as select t1.pid, t1.url, count(t1.page_key) as requested, sum(t1.played) as played, 
		count(CASE WHEN t1.played>0 THEN 1 END) as calculate,
		case when (t2.requested>0) then round(count(t1.page_key)/t2.requested::numeric,4)*100 else 0 end as percent_requested, 
		case when (t2.played>0) then round(sum(t1.played)/t2.played::numeric,4)*100 else 0 end as percent_played,
		case when (t2.calculate>0) then round(count(CASE WHEN t1.played>0 THEN 1 END)/t2.calculate::numeric,4)*100 else 0 end as percent_calculate 
		from stat_user_pages t1 
		left join (select pid, count(page_key) as requested, sum(played) as played, count(case when played>0 then 1 end) as calculate 
		from stat_user_pages where datetime between '$from' and '$to' group by pid, day) t2 on t1.pid=t2.pid where t1.pid='$id' and 
		t1.datetime between '$from' and '$to' group by t1.pid, t1.url, t2.requested, t2.played, t2.calculate";
		$pdo = \DB::connection('videotest')->getPdo();
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('videotest')->table('urls_stats')->orderBy($order,$direct)->paginate(30);
		$widget=\App\WidgetVideo::where('id', $id)->first();
		return view('statistic.video.pid_urls_video_statistic', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'widget'=>$widget]);
	}
	
	public function frame(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');
        }
		$number=$request->input('number')?$request->input('number'):20;
		$search=$request->input('search');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"id";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Id",'index'=>"id","order"=>"",'url'=>""],
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"День",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Url (который мы зачли)",'index'=>"url","order"=>"",'url'=>""],
			['title'=>"Родитель",'index'=>"refer","order"=>"",'url'=>""],
			['title'=>"Количество загрузок",'index'=>"cnt","order"=>"",'url'=>""],
			['title'=>"Цепочка",'index'=>"","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		if ($search){
			$stats=\DB::connection('videotest')->table('frame_refer')
			->where(function($query) use ($search)
						{
							$query->where('url', '~*', trim($search))
							->orWhere('refer', '~*', trim($search))
							->orWhere('origin', '~*', trim($search));
						})
			->
			whereBetween('day', [$from, $to])
			->orderBy($order,$direct)->paginate($number);
		}
		else{
			$stats=\DB::connection('videotest')->table('frame_refer')->whereBetween('day', [$from, $to])->orderBy($order,$direct)->paginate($number);
		}
		return view('statistic.video.frame_all', ['number'=>$number, 'search'=>$search, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function frameUser(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');
        }
		$number=$request->input('number')?$request->input('number'):20;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"cnt";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"Юзер",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Количество загрузок",'index'=>"cnt","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table frame_us as select pid, sum(cnt) as cnt from frame_refer where day between '$from' and '$to' group by pid";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('videotest')->table('frame_us')->orderBy($order,$direct)->get();
		return view('statistic.video.frame_user', ['number'=>$number, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function frameDetail($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');
        }
		$number=$request->input('number')?$request->input('number'):20;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"cnt";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Id",'index'=>"id","order"=>"",'url'=>""],
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"День",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Url (который мы зачли)",'index'=>"url","order"=>"",'url'=>""],
			['title'=>"Родитель",'index'=>"refer","order"=>"",'url'=>""],
			['title'=>"Количество загрузок",'index'=>"cnt","order"=>"",'url'=>""],
			['title'=>"Цепочка",'index'=>"","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$stats=\DB::connection('videotest')->table('frame_refer')->where('pid', $id)->whereBetween('day', [$from, $to])->orderBy($order,$direct)->paginate($number);
		return view('statistic.video.frame_detail', ['number'=>$number, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function hourStat(Request $request){
		$date1=$request->input('date1');
		$date2=$request->input('date2');
		$hour=$request->input('hour');

		if(!($date1||$date2||$hour)){
            $date1=date('Y-m-d',time()-3600*24);
			$date2=date('Y-m-d');
			$hour=date('H',time()-3600);
        }
		$diha1=$date1." $hour:00:00";
		$diha2=$date2." $hour:00:00";
		if(mb_strlen($hour)==1){
		$diha1=$date1." 0$hour:00:00";
		$diha2=$date2." 0$hour:00:00";

                }
		$pdo = \DB::connection("video_")->getPdo();
$sql="
create temp table video_hour
as 
select 
id
,user_id
,user_name as name
,domain
,sum (request_1) as request_1
,sum(request_2) as request_2
,sum(request_2)-sum (request_1)  as razn


from (

select 
id
,user_id
,user_name
,domain
,request_1
,0 as request_2
from(
select 
t1.pid as id
,t1.user_id
,t1.user_name
,t1.domain
,t1.day as day_1
,sum(t1.request) as request_1
from hour_ t1 where day='$date1'
and hour ='$diha1'
group by 
t1.pid 
,t1.user_id
,t1.user_name
,t1.domain
,t1.day
) t1
union  all
select 
id
,user_id
,user_name
,domain

,0 as request_1
,request_2
from(
select 
t1.pid as id
,t1.user_id
,t1.user_name
,t1.domain
,t1.day as day_1
,sum(t1.request) as request_2
from hour_ t1 where day='$date2'
and hour ='$diha2'
group by 
t1.pid 
,t1.user_id
,t1.user_name
,t1.domain
,t1.day 
) t1
)
z
group by 
id
,user_id
,user_name
,domain
";

//var_dump($sql);
		$pdo->query($sql, \PDO::FETCH_ASSOC);
		$stats=\DB::connection("video_")->table('video_hour')->where('razn', '<>', '0')->orderBy('razn','desc')->get();
		return view('statistic.video.hour', ['stats'=>$stats, 'date1'=>$date1, 'date2'=>$date2, 'hour'=>$hour]);



		$pdo = \DB::connection()->getPdo();
		$sql="create temp table video_hour 

as select t1.id, t2.user_id, t3.name, t4.domain, t5.day as day_1, coalesce(t5.request,0) as request_1, t6.day as day_2, 
			coalesce(t6.request,0) as request_2, coalesce(t6.request,0)-coalesce(t5.request,0) as razn
			from widget_videos t1 
			left join (select id, user_id, pad from widgets) t2 on t1.wid_id=t2.id 
			left join (select id, name from users) t3 on t2.user_id=t3.id 
			left join (select id, domain from partner_pads) t4 on t2.pad=t4.id 
			left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
			'select pid, count(*) as request, day, EXTRACT(Hour FROM datetime) as hour from stat_user_pages where 
			day=''$date1'' and EXTRACT(Hour FROM datetime)=''$hour'' group by pid, EXTRACT(Hour FROM datetime), day') AS p(pid int, request int, day date, hour int)) t5 on t1.id=t5.pid 
			left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
			'select pid, count(*) as request, day, EXTRACT(Hour FROM datetime) as hour from stat_user_pages where 
			day=''$date2'' and EXTRACT(Hour FROM datetime)=''$hour'' group by pid, EXTRACT(Hour FROM datetime), day') AS p(pid int, request int, day date, hour int)) t6 on t1.id=t6.pid and 
			t5.hour=t6.hour";
		$pdo->query($sql, \PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('video_hour')->where('razn', '<>', '0')->orderBy('razn','desc')->get();
		return view('statistic.video.hour', ['stats'=>$stats, 'date1'=>$date1, 'date2'=>$date2, 'hour'=>$hour]);

	}
	
	public function groupIp($id,Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');
        }
		$number=$request->input('number')?$request->input('number'):100;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"requested";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"Ip адресс",'index'=>"ip","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Запросы к ссылкам",'index'=>"src_requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table video_ip as select count(*) as requested, sum(requested) as src_requested, sum(played) as played, 
		sum(clicks) as clicks, pid, day, ip from stat_user_pages where pid='$id' and day between '$from' and '$to' group by pid, day,ip";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('videotest')->table('video_ip')->orderBy($order,$direct)->paginate($number);
		$widget=\App\WidgetVideo::where('id', $id)->first();
		return view('statistic.video.video_ip', ['widget'=>$widget, 'number'=>$number, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function groupIpAll(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');
        }
		$number=$request->input('number')?$request->input('number'):100;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"requested";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Ip адресс",'index'=>"ip","order"=>"",'url'=>""],
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"Имя",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Домен",'index'=>"domain","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Запросы к ссылкам",'index'=>"src_requested","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table video_ip_all as select t1.id, t3.id as pid, t1.user_id, t2.name, t1.pad, t4.domain, t5.requested, 
		t5.src_requested, t5.played, t5.clicks, t5.ip from widgets t1 left join (select name, id from users) t2 on t1.user_id=t2.id 
		left join (select id, wid_id from widget_videos) t3 on t1.id=t3.wid_id left join (select id, domain from partner_pads) t4 on 
		t1.pad=t4.id left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select count(*) as requested, sum(requested) as src_requested, sum(played) as played, sum(clicks) as clicks, pid, ip from 
		stat_user_pages where day between ''$from'' and ''$to'' group by pid, day, ip') 
		AS p(requested int, src_requested int, played int, clicks int, pid int, ip varchar)) t5 on t3.id=t5.pid where t1.type='2' 
		and t5.requested<>'0'";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('video_ip_all')->orderBy($order,$direct)->paginate($number);
		return view('statistic.video.video_ip_all', ['number'=>$number, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function frameUserId($id){
		$date=date('Y-m-d H:i:s',time()+3600*24);
		$pdo = \DB::connection()->getPdo();
		$sql="insert into frame_prover (user_id,datetime)
			select ?,? WHERE NOT EXISTS (SELECT 1 FROM frame_prover WHERE user_id=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update frame_prover set datetime=?
			WHERE user_id=?";
		$sthUpdate=$pdo->prepare($sql);
		$sthUpdate->execute([$date, $id]);
		$sthInsert->execute([$id, $date, $id]);
		return back();
	}
	
	public function frameStatUser($id, Request $request){
		$user=\App\User::find($id);
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $to=date('Y-m-d');
			$from=date('Y-m-d',time()-3600*168);
        }
		$number=$request->input('number')?$request->input('number'):100;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"cnt";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"Url",'index'=>"url","order"=>"",'url'=>""],
			['title'=>"Количество загрузок",'index'=>"cnt","order"=>"",'url'=>""],
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
			['title'=>"Детально",'index'=>"","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		
		$pdo = \DB::connection()->getPdo();
		$sql="select t3.id from users t1 left join (select id,user_id from widgets) t2 on t1.id=t2.user_id 
		left join (select * from widget_videos) t3 on t2.id=t3.wid_id where t1.id='$id';";
		$ppp=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$userPid=array();
		foreach ($ppp as $pp){
			if ($pp['id']){
				array_push($userPid, $pp['id']);
			}
		}
		$userPid=implode(",", $userPid);
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table uss_frame as select t1.pid, t1.url, sum(t1.cnt) as cnt, round(1-t2.count_d/t2.count::numeric,2) as coef 
		from frame_refer t1 
		left join (select substring(referrer from '://((?:(?!://).)+?)/') as url, count(ip) as count, count(distinct(ip)) as 
		count_d from frame_pid where date between '$from' and '$to' group by substring(referrer from '://((?:(?!://).)+?)/')) t2 on t1.url=t2.url
		where t1.pid in ($userPid) and t1.date between '$from' and '$to' group by t1.pid,t1.url,t2.count,t2.count_d order by cnt desc";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('videotest')->table('uss_frame')->orderBy($order,$direct)->paginate($number);
		$sum=\DB::connection('videotest')->table('uss_frame')->orderBy($order,$direct)->sum('cnt');
		return view('statistic.video.user_frame', ['user'=>$user, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'sum'=>$sum, 'header'=>$header, 'number'=>$number, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function frameStatUserDetail($id, Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $to=date('Y-m-d');
			$from=date('Y-m-d',time()-3600*168);
        }
		$number=$request->input('number')?$request->input('number'):100;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"datetime";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Id виджета",'index'=>"pid","order"=>"",'url'=>""],
			['title'=>"Дата",'index'=>"date","order"=>"",'url'=>""],
			['title'=>"Фрейм",'index'=>"url","order"=>"",'url'=>""],
			['title'=>"Url",'index'=>"referrer","order"=>"",'url'=>""],
			['title'=>"Цепочка",'index'=>"","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table uss_frame_detail as select * from frame_pid where pid='$id';";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('videotest')->table('uss_frame_detail')->orderBy($order,$direct)->paginate($number);
		return view('statistic.video.user_frame_detail', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'header'=>$header, 'number'=>$number, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function DomainStatDetal($id, Request $request){
//угода
    if($_SERVER["REMOTE_ADDR"]!="176.213.140.214"){
//    exit();
    }


		\Auth::user()->touch();
		if ($id!=1671 and $id!=1461 and $id!=1485 and $id!=1513 and $id!=1517 and $id!=1597 and $id!=752  and $id!=1664  and $id!=1665 ){
                        if(!\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager')){
                                    abort(403);
                        }


#                       if($_SERVER["REMOTE_ADDR"]!="176.213.140.214"){
#			#return abort(403);
#			}
		}
		
		/*
		if (!\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager') and !\Auth::user()->hasRole('manager')){
			return abort(403);
		}
		*/
		
		$tpid = \App\WidgetVideo::where('id', $id)->first();  //получаем всю запись по пиду из таьблици виджитс
		$gvid = \App\MPW\Widgets\Widget::where('id', $tpid->wid_id)->first(); //из таблицы виджитс
		
		if(!\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager') and !\Auth::user()->hasRole('manager')){
			if(\Auth::user()->id!=$gvid->user_id){
				return abort(403);
			}
		}
		/*if (){
			return abort(403);
		}
		
		\App\WidgetVideo::where('id', $id)->first();
		\App\MPW\Widgets\Widget::where('id', $prov_video->wid_id)->first();
		
		user_id=803
		user_id=4420
		*/
		
		
		
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$number=$request->input('number')?$request->input('number'):100;
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"requested";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Домен",'index'=>"host","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Всего показов",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Зачтенные показы",'index'=>"calc_played","order"=>"",'url'=>""],
			['title'=>"Глубина",'index'=>"depth","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Досмотры",'index'=>"completed","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"К. ботности",'index'=>"bot","order"=>"",'url'=>""],
			['title'=>"сумма",'index'=>"summa","order"=>"",'summa'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }




		$pdo = \DB::connection('video_')->getPdo();
		$sql="create temp table ngs_sss as select 
		pid,  
		host, 
		sum(requested) as requested,
		sum(played) as played, 
		sum(calc_played) as calc_played,
		case when sum(calc_played)>0 then round(sum(played)/sum(calc_played)::numeric,4) else 0 end as depth, 
		case when sum(requested)>0 then round(sum(calc_played)/sum(requested)::numeric,4)*100 else 0 end as util, 
		round(avg(completed)::numeric,4) as completed, 
		sum(clicks) as clicks, 
		case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr,
		round(avg(bot)::numeric,4) as bot,
                sum(summa) as summa
		 from _ngs_details where pid='$id' and day between '$from' and '$to' group by pid, host;";
                 //echo nl2br($sql);
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$statsAll=\DB::connection('video_')->table('ngs_sss')->orderBy($order,$direct)->paginate($number);
		
		$sql="create temp table ngs_sssru as select 
		pid,  
		host, 
		sum(requested) as requested,
		sum(played) as played, 
		sum(calc_played) as calc_played,
		case when sum(calc_played)>0 then round(sum(played)/sum(calc_played)::numeric,4) else 0 end as depth, 
		case when sum(requested)>0 then round(sum(calc_played)/sum(requested)::numeric,4)*100 else 0 end as util, 
		round(avg(completed)::numeric,4) as completed, 
		sum(clicks) as clicks, 
		case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr,
		round(avg(bot)::numeric,4) as bot,
                sum(summa) as summa
		 from _ngs_details where pid='$id' and day between '$from' and '$to' and country='RU' group by pid, host;";

//       echo nl2br($sql); die();

		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$statsRus=\DB::connection('video_')->table('ngs_sssru')->orderBy($order,$direct)->paginate($number);
		
		$sql="create temp table ngs_ssscis as select 
		pid,  
		host, 
		sum(requested) as requested,
		sum(played) as played, 
		sum(calc_played) as calc_played,
		case when sum(calc_played)>0 then round(sum(played)/sum(calc_played)::numeric,4) else 0 end as depth, 
		case when sum(requested)>0 then round(sum(calc_played)/sum(requested)::numeric,4)*100 else 0 end as util, 
		round(avg (completed)::numeric,4) as completed, 
		sum(clicks) as clicks, 
		case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr,
		round(avg(bot)::numeric,4) as bot,
                sum(summa) as summa
		 from _ngs_details where pid='$id' and day between '$from' and '$to' and country<>'RU' group by pid, host;";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$statsCis=\DB::connection('video_')->table('ngs_ssscis')->orderBy($order,$direct)->paginate($number);
		return view('statistic.video.ngs_stats', ['id'=>$id, 'number'=>$number, 'order'=>$order, 'direct'=>$direct, 'header'=>$header, 'from'=>$from, 'to'=>$to, 'statsAll'=>$statsAll, 'statsRus'=>$statsRus, 'statsCis'=>$statsCis]);
	}
	
	public function DomainStatDetalExcel(Request $request){

	       //угодага

		$id=$request->input('id');
		$from=$request->input('from');
		$to=$request->input('to');
	$pdo = \DB::connection('video_')->getPdo();

$sql="
create temp table ngs_cis as
select
day,
pid,
host,
sum(requested) as requested,
sum(played) as played,
sum(summa) as summa,
sum(calc_played) as calc_played,
case when sum(calc_played)>0 then round(sum(played)/sum(calc_played)::numeric,4) else 0 end as depth,
case when sum(requested)>0 then round(sum(calc_played)/sum(requested)::numeric,4)*100 else 0 end as util,
round(avg(completed)::numeric,4) as completed,
sum(clicks) as clicks,
case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr,
round(avg(bot)::numeric,4) as bot
from _ngs_details 
where pid='$id' and day between '$from' and '$to' and country<>'RU' 
and country<>'RU' group by pid, host,day;
";
$pdo->exec($sql);

$sql="
create temp table ngs_rus as
select
day,
pid,
host,
sum(requested) as requested,
sum(played) as played,
sum(summa) as summa,
sum(calc_played) as calc_played,
case when sum(calc_played)>0 then round(sum(played)/sum(calc_played)::numeric,4) else 0 end as depth,
case when sum(requested)>0 then round(sum(calc_played)/sum(requested)::numeric,4)*100 else 0 end as util,
round(avg(completed)::numeric,4) as completed,
sum(clicks) as clicks,
case when sum(played)>0 then round(sum(clicks)/coalesce(sum(played),1)::numeric,4)*100 else 0 end as ctr,
round(avg(bot)::numeric,4) as bot
from _ngs_details 
where pid='$id' and day between '$from' and '$to' and country='RU' 
group by pid, host,day;
";
$pdo->exec($sql);

$sql="select t1.day, t1.host, sum(coalesce(t1.requested,0)+coalesce(t2.requested,0)) as requested, sum(coalesce(t1.calc_played,0)+coalesce(t2.calc_played,0)) as calc_played, 
	sum(coalesce(t1.summa,0)+coalesce(t2.summa,0)) as summa from ngs_rus t1 left join 
	(select * from ngs_cis) t2 on t1.day=t2.day and t1.host=t2.host group by t1.day, t1.host order by t1.day, t1.host desc";
$sqps=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);

		$file = '/home/mp.su/widget.market-place.su/public/ngs/' . $id . '_' . $from . '_' . $to  .'.csv';
		$cmd ="rm  -f  $file";
	        `$cmd`;
		$tofile = "";
		$tofile .= "Дата\tДомен\tЗагрузки\tПоказы\tСумма\n";
		foreach ($sqps as $sqp){
			$day=$sqp['day'];
			$host=$sqp['host'];
			$requested=$sqp['requested'];
			$played=$sqp['calc_played'];
			$summa=str_replace('.',',',$sqp['summa']);
			$tofile .= "$day\t$host\t$requested\t$played\t$summa\n";
//                        echo nl2br("$day\t$host\t$requested\t$played\t$summa\n");
			//@file_put_contents($file, $bom . $tofile . file_get_contents($file));
		}
		
//die();
		//$tofile = "Дата;Домен;Загрузки;Показы;Сумма\n";
		$bom = "\xEF\xBB\xBF";
		@file_put_contents($file, $bom . $tofile . file_get_contents($file));
		return redirect('//widget.market-place.su/ngs/' . $id . '_' . $from . '_' . $to  .'.csv');





die();



		$pdo = \DB::connection('videotest')->getPdo();
		$sql="create temp table ngs_cis as select t1.day, t1.host, sum(t1.requested) as requested, sum(t1.calc_played) as calc_played, 
			round(sum(t1.calc_played)*t3.value/1000::numeric,4) as summa from ngs_details t1 left join 
			(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
			'select id, commission_cis from widget_videos where id=''$id''') AS p(id int, commission_cis varchar)) t2 on t1.pid=t2.id left join 
			(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
			'select commissiongroupid, value from сommission_groups') AS p(commissiongroupid varchar, value numeric(10,4))) t3 on t2.commission_cis=t3.commissiongroupid 
			where t1.pid='$id' and t1.day between '$from' and '$to' and t1.country<>'RU' group by t1.pid, t1.host, t1.day, t3.value order by t1.day, t1.host";
//		echo nl2br($sql)	; die();

		$pdo->exec($sql);
		$sql="create temp table ngs_rus as select t1.day, t1.host, sum(t1.requested) as requested, sum(t1.calc_played) as calc_played, 
			case when (t1.day<'2018-08-03') then round(sum(t1.calc_played)*85/1000::numeric,4) else round(sum(t1.calc_played)*t3.value/1000::numeric,4) end as summa from ngs_details t1 left join 
			(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
			'select id, commission_rus from widget_videos where id=''$id''') AS p(id int, commission_rus varchar)) t2 on t1.pid=t2.id left join 
			(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
			'select commissiongroupid, value from сommission_groups') AS p(commissiongroupid varchar, value numeric(10,4))) t3 on t2.commission_rus=t3.commissiongroupid 
			where t1.pid='$id' and t1.day between '$from' and '$to' and t1.country='RU' group by t1.pid, t1.host, t1.day, t3.value order by t1.day, t1.host";
		$pdo->exec($sql);
		
		$sql="select t1.day, t1.host, sum(coalesce(t1.requested,0)+coalesce(t2.requested,0)) as requested, sum(coalesce(t1.calc_played,0)+coalesce(t2.calc_played,0)) as calc_played, 
		sum(coalesce(t1.summa,0)+coalesce(t2.summa,0)) as summa from ngs_rus t1 left join 
		(select * from ngs_cis) t2 on t1.day=t2.day and t1.host=t2.host group by t1.day, t1.host order by t1.day, t1.host desc";
		$sqps=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		//var_dump($sqp);
		$file = '/home/mp.su/widget.market-place.su/public/ngs/' . $id . '_' . $from . '_' . $to  .'.csv';
		$cmd ="rm  -f  $file";
	    `$cmd`;
		$tofile = "";
		$tofile .= "Дата\tДомен\tЗагрузки\tПоказы\tСумма\n";
		foreach ($sqps as $sqp){
			$day=$sqp['day'];
			$host=$sqp['host'];
			$requested=$sqp['requested'];
			$played=$sqp['calc_played'];
			$summa=str_replace('.',',',$sqp['summa']);
			$tofile .= "$day\t$host\t$requested\t$played\t$summa\n";
			//@file_put_contents($file, $bom . $tofile . file_get_contents($file));
		}
		
		//$tofile = "Дата;Домен;Загрузки;Показы;Сумма\n";
		$bom = "\xEF\xBB\xBF";
		@file_put_contents($file, $bom . $tofile . file_get_contents($file));
		return redirect('//widget.market-place.su/ngs/' . $id . '_' . $from . '_' . $to  .'.csv');
	}
}
