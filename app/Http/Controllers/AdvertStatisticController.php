<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
class AdvertStatisticController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
            ['title'=>"Домен",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Цтр",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Цпц",'index'=>"cpc","order"=>"",'url'=>""],
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
		$pads_stats=[];
		
		$pads_stat_all=[];
		
			$sql="
		select
		    t.views			
			,coalesce(t2.views,0) as old_views			 
			,t.views-coalesce(t2.views,0) as views_rozn
			,t.clicks
			,coalesce(t2.clicks,0) as old_clicks
			,t.clicks-coalesce(t2.clicks,0) as clicks_rozn
			,t.summa	
            ,coalesce(t2.summa,0) as old_summa
			,t.summa-coalesce(t2.summa,0) as summa_rozn
			,t.ctr
			,coalesce(t2.ctr,0) as old_ctr
			,t.ctr-coalesce(t2.ctr,0) as ctr_rozn
			,t.cpc
			,coalesce(t2.cpc,0) as old_cpc
			,t.cpc-coalesce(t2.cpc,0) as cpc_rozn
			from(
		select 
		    sum(views+yviews) as views			
			,sum(clicks+yclicks) as clicks
			,sum(summa+ysumma) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0) 
			then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
		from myadvert_sites where day between '$from' and '$to'
		
		) t
		cross join 
		(
		select
		    sum(views+yviews) as views			
			,sum(clicks+yclicks) as clicks
			,sum(summa+ysumma) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0) 
			then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
		from myadvert_sites where day between '$fromOld' and '$toOld'
		
		) t2
	
		order by $order $direct
		";
		$pads_stat_all=\DB::connection('pgstatistic')->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		#var_dump($pads_stat_all); die();
		$sql="
		select t.name
		    ,t.pad
		    ,t.views			
			,coalesce(t2.views,0) as old_views			 
			,t.views-coalesce(t2.views,0) as views_rozn
			,t.clicks
			,coalesce(t2.clicks,0) as old_clicks
			,t.clicks-coalesce(t2.clicks,0) as clicks_rozn
			,t.summa	
            ,coalesce(t2.summa,0) as old_summa
			,t.summa-coalesce(t2.summa,0) as summa_rozn
			,t.ctr
			,coalesce(t2.ctr,0) as old_ctr
			,t.ctr-coalesce(t2.ctr,0) as ctr_rozn
			,t.cpc
			,coalesce(t2.cpc,0) as old_cpc
			,t.cpc-coalesce(t2.cpc,0) as cpc_rozn
			from(
		select name,pad
		    ,sum(views+yviews) as views			
			,sum(clicks+yclicks) as clicks
			,sum(summa+ysumma) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0) 
			then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
		from myadvert_sites where day between '$from' and '$to'
		group by name,pad
		) t
		left join 
		(
		select name,pad
		    ,sum(views+yviews) as views			
			,sum(clicks+yclicks) as clicks
			,sum(summa+ysumma) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0) 
			then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
		from myadvert_sites where day between '$fromOld' and '$toOld'
		group by name,pad
		) t2
		on t2.name=t.name and t2.pad=t.pad 
		order by $order $direct
		";
		
		$pads_stats=\DB::connection('pgstatistic')->select($sql);
		foreach($pads_stats as $d){
			#print "<pre>"; print_r($d); print "</pre>";
			
		}
		
		
		
		
		$perPage=$number;
		$found=count($pads_stats);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $pads_stats = new LengthAwarePaginator(array_slice($pads_stats, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$params=['number'=>$number
		, 'fromOld'=>$fromOld
		, 'toOld'=>$toOld
		, 'category'=>$category
		, 'pads_stat_all'=>$pads_stat_all
		, 'search'=>$search
		, 'header'=>$header
		, 'pads_stats'=>$pads_stats
		, 'from'=>$from
		, 'to'=>$to
		, 'order'=>$order
		, 'direct'=>$direct];
		//var_dump($params);
		//die();
		return view('statistic.advert.pads_stat_comparison', $params);
		
		
		
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
		$pad_stats=[];
		$pad_stat_all=[];
		$pad_stats_ru=[];
		$pad_stats_cis=[];
		$pad_stat_all_cis=[];
		$pad_stat_all_ru=[];
		
		$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"yclicks","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"ysumma","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"ycpc","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"tclicks","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"tsumma","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"tcpc","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"played","order"=>"",'url'=>""],

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
			$sql=" select
            coalesce(sum(views+yviews),0) as views			
			,coalesce(sum(clicks+yclicks),0) as clicks
			,coalesce(sum(summa+ysumma),0) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,coalesce(sum(views),0) as tviews		
			,coalesce(sum(clicks),0) as tclicks
			,coalesce(sum(summa),0)	as tsumma
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0) 
			then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as tcpc
			,coalesce(sum(yviews),0) as yviews			
			,coalesce(sum(yclicks),0) as yclicks
			,coalesce(sum(ysumma),0)	as ysumma
			,CASE WHEN (coalesce(sum(yclicks),0)>0 and coalesce(sum(ysumma),0)>0) 
			then round(sum(ysumma)/sum(yclicks)::numeric,4) else 0::numeric end as ycpc
			from myadvert_sites
			where pad=$id and day between '$from' and '$to'
           
			";
			$pad_stat_all=\DB::connection('pgstatistic')->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
					$sql="
			select day
            ,views+yviews as views			
			,clicks+yclicks as clicks
			,summa+ysumma as summa			
			,CASE WHEN ((clicks+yclicks)>0 and (views+yviews)>0) 
			then round((clicks+yclicks)/(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,views as tviews		
			,clicks as tclicks
			,summa	as tsumma
			,CASE WHEN (clicks>0 and summa>0) 
			then round(summa/clicks::numeric,4) else 0::numeric end as tcpc
			,yviews			
			,yclicks
			,ysumma	
			,CASE WHEN (yclicks>0 and ysumma>0) 
			then round(ysumma/yclicks::numeric,4) else 0::numeric end as ycpc
			from myadvert_sites
			where pad=$id and day between '$from' and '$to'
			order by $order $direct
			";
		$pad_stats=\DB::connection('pgstatistic')->select($sql);
		$perPage=$number;
		$found=count($pad_stats);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $pad_stats = new LengthAwarePaginator(array_slice($pad_stats, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
		
		
		$pad=\App\PartnerPad::where('id', $id)->first();
		
		$params=['number'=>$number
		, 'order'=>$order
		, 'direct'=>$direct
		, 'pad'=>$pad
		, 'header'=>$header
		, 'pad_stat_all_ru'=>$pad_stat_all_ru
		, 'pad_stat_all_cis'=>$pad_stat_all_cis
		, 'pad_stats_cis'=>$pad_stats_cis
		, 'pad_stats_ru'=>$pad_stats_ru
		, 'pad_stat_all'=>$pad_stat_all
		, 'pad_stats'=>$pad_stats
		, 'from'=>$from
		, 'to'=>$to];
		//var_dump($params); die();		
		return view('statistic.advert.detail_pads_stat',$params);

	}
    public function partnerStatComparison(Request $request){	

	
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
            ['title'=>"Партнёр",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Цтр",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Цпц",'index'=>"cpc","order"=>"",'url'=>""],
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
		$pads_stats=[];
		
		$pads_stat_all=[];
		
		$sql="
		select
		    t.views			
			,coalesce(t2.views,0) as old_views			 
			,t.views-coalesce(t2.views,0) as views_rozn
			,t.clicks
			,coalesce(t2.clicks,0) as old_clicks
			,t.clicks-coalesce(t2.clicks,0) as clicks_rozn
			,t.summa	
            ,coalesce(t2.summa,0) as old_summa
			,t.summa-coalesce(t2.summa,0) as summa_rozn
			,t.ctr
			,coalesce(t2.ctr,0) as old_ctr
			,t.ctr-coalesce(t2.ctr,0) as ctr_rozn
			,t.cpc
			,coalesce(t2.cpc,0) as old_cpc
			,t.cpc-coalesce(t2.cpc,0) as cpc_rozn
			from(
		select 
		    sum(views+yviews) as views			
			,sum(clicks+yclicks) as clicks
			,sum(summa+ysumma) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0) 
			then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
		from myadvert_sites where day between '$from' and '$to'
		
		) t
		cross join 
		(
		select
		    sum(views+yviews) as views			
			,sum(clicks+yclicks) as clicks
			,sum(summa+ysumma) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0) 
			then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
		from myadvert_sites where day between '$fromOld' and '$toOld'
		
		) t2
	
		order by $order $direct
		";
		$pads_stat_all=\DB::connection('pgstatistic')->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		#var_dump($pads_stat_all); die();
		
		$sql="
select t.user_name as name,t.user_id as pad
,t.views
,coalesce(t2.views,0) as old_views
,t.views-coalesce(t2.views,0) as views_rozn
,t.clicks
,coalesce(t2.clicks,0) as old_clicks
,t.clicks-coalesce(t2.clicks,0) as clicks_rozn
,t.summa
,coalesce(t2.summa,0) as old_summa
,t.summa-coalesce(t2.summa,0) as summa_rozn
,t.ctr
,coalesce(t2.ctr,0) as old_ctr
,t.ctr-coalesce(t2.ctr,0) as ctr_rozn
,t.cpc
,coalesce(t2.cpc,0) as old_cpc
,t.cpc-coalesce(t2.cpc,0) as cpc_rozn
from(
select user_name,user_id
,sum(views+yviews) as views
,sum(clicks+yclicks) as clicks
,sum(summa+ysumma) as summa
,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0)
then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0)
then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
from myadvert_sites where day between '$from' and '$to'
group by user_name,user_id
) t
left join
(
select user_name,user_id
,sum(views+yviews) as views
,sum(clicks+yclicks) as clicks
,sum(summa+ysumma) as summa
,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0)
then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(summa+ysumma),0)>0)
then round(sum(summa+ysumma)/sum(clicks+yclicks)::numeric,4) else 0::numeric end as cpc
from myadvert_sites where day between '$fromOld' and '$toOld'
group by user_name,user_id
) t2
on t2.user_id=t.user_id and t2.user_name=t.user_name
		order by $order $direct
		";
				$pads_stats=\DB::connection('pgstatistic')->select($sql);
		#foreach($pads_stats as $d){
			#print "<pre>"; print_r($d); print "</pre>";
			
		#}
		
		
		
		
		$perPage=$number;
		$found=count($pads_stats);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $pads_stats = new LengthAwarePaginator(array_slice($pads_stats, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$params=['number'=>$number
		, 'fromOld'=>$fromOld
		, 'toOld'=>$toOld
		, 'category'=>$category
		, 'pads_stat_all'=>$pads_stat_all
		, 'search'=>$search
		, 'header'=>$header
		, 'pads_stats'=>$pads_stats
		, 'from'=>$from
		, 'to'=>$to
		, 'order'=>$order
		, 'direct'=>$direct];
		//var_dump($params);
		//die();
		return view('statistic.advert.partner_stat_comparison', $params);
    }
	public function statsPartner($id, Request $request){
		
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
		$pad_stats=[];
		$pad_stat_all=[];
		$pad_stats_ru=[];
		$pad_stats_cis=[];
		$pad_stat_all_cis=[];
		$pad_stat_all_ru=[];
		//var_dump(33333);
		//die();
		$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"yclicks","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"ysumma","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"ycpc","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"tclicks","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"tsumma","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"tcpc","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"played","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"played","order"=>"",'url'=>""],

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
			$sql=" select
            coalesce(sum(views+yviews),0) as views			
			,coalesce(sum(clicks+yclicks),0) as clicks
			,coalesce(sum(summa+ysumma),0) as summa			
			,CASE WHEN (coalesce(sum(clicks+yclicks),0)>0 and coalesce(sum(views+yviews),0)>0) 
			then round(sum(clicks+yclicks)/sum(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,coalesce(sum(views),0) as tviews		
			,coalesce(sum(clicks),0) as tclicks
			,coalesce(sum(summa),0)	as tsumma
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0) 
			then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as tcpc
			,coalesce(sum(yviews),0) as yviews			
			,coalesce(sum(yclicks),0) as yclicks
			,coalesce(sum(ysumma),0)	as ysumma
			,CASE WHEN (coalesce(sum(yclicks),0)>0 and coalesce(sum(ysumma),0)>0) 
			then round(sum(ysumma)/sum(yclicks)::numeric,4) else 0::numeric end as ycpc
			from myadvert_sites
			where user_id=$id and day between '$from' and '$to'
           
			";
			$pad_stat_all=\DB::connection('pgstatistic')->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
			//var_dump($pad_stat_all); die();
					$sql="
			select day
            ,views+yviews as views			
			,clicks+yclicks as clicks
			,summa+ysumma as summa			
			,CASE WHEN ((clicks+yclicks)>0 and (views+yviews)>0) 
			then round((clicks+yclicks)/(views+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,views as tviews		
			,clicks as tclicks
			,summa	as tsumma
			,CASE WHEN (clicks>0 and summa>0) 
			then round(summa/clicks::numeric,4) else 0::numeric end as tcpc
			,yviews			
			,yclicks
			,ysumma	
			,CASE WHEN (yclicks>0 and ysumma>0) 
			then round(ysumma/yclicks::numeric,4) else 0::numeric end as ycpc
			from myadvert_sites
			where user_id=$id and day between '$from' and '$to'
			order by $order $direct 
			";
			//var_dump(nl2br($sql));
		$pad_stats=\DB::connection('pgstatistic')->select($sql);
		$perPage=$number;
		$found=count($pad_stats);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $pad_stats = new LengthAwarePaginator(array_slice($pad_stats, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
		
		
		$pad=\App\User::where('id', $id)->first();
		
		$params=['number'=>$number
		, 'order'=>$order
		, 'direct'=>$direct
		, 'pad'=>$pad
		, 'header'=>$header
		, 'pad_stat_all_ru'=>$pad_stat_all_ru
		, 'pad_stat_all_cis'=>$pad_stat_all_cis
		, 'pad_stats_cis'=>$pad_stats_cis
		, 'pad_stats_ru'=>$pad_stats_ru
		, 'pad_stat_all'=>$pad_stat_all
		, 'pad_stats'=>$pad_stats
		, 'from'=>$from
		, 'to'=>$to];
		//var_dump($params); die();		
		return view('statistic.advert.detail_partner_stat',$params);

	}	
}