<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
class BrandStatisticController extends Controller
{
		
	public function statsPidUrl($id, Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=$to=date('Y-m-d');
        }
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"showed";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Url адрес",'index'=>"url","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"showed","order"=>"",'url'=>""],
			['title'=>"Уникальные показы",'index'=>"unik_showed","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"click","order"=>"",'url'=>""],
			['title'=>"Уникальные клики",'index'=>"unik_click","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"% показов",'index'=>"precent_showed","order"=>"",'url'=>""],
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
		
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="create temp table pid_brand_widget as select t1.pid, t1.url, count(t1.page_key) as showed, count(distinct (t1.ip)) as unik_showed, 
		t2.click, t2.unik_click, case when (count(t1.page_key)>0) then round(t2.click/count(t1.page_key)::numeric,4)*100 else 0 end as ctr, 
		case when (count(t1.page_key)>0) then round(t3.all_showed/count(t1.page_key)::numeric,4)*100 else 0 end as precent from brand_stat_pages 
		t1 left join (select id_widget, url, count(distinct (page_key)) as click, count(distinct (ip)) as unik_click from brand_stat_clicks where 
		id_widget='$id' and date between '$from' and '$to' group by id_widget, url) t2 on t1.pid=t2.id_widget and t1.url=t2.url 
		left join (select pid, count(page_key) as all_showed from brand_stat_pages where pid='$id' and day between '$from' and '$to' 
		group by pid) t3 on t3.pid=t1.pid where t1.pid='$id' and t1.day between '$from' and '$to' group by t1.pid, t1.url, t2.click, 
		t2.unik_click, t3.all_showed";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('pgstatistic')->table('pid_brand_widget')->orderBy($order,$direct)->paginate(30);
		$widget=\App\MPW\Widgets\Widget::where('id', $id)->first();
		return view('statistic.brand.pid_urls_brand_statistic', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'widget'=>$widget, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function statsPid($id, Request $request){
		\Auth::user()->touch();
		$prov_widget=\App\MPW\Widgets\Widget::where('id', $id)->whereIn('type', [4])->first();
		if ($prov_widget){
			if (!\Auth::user()->hasRole("admin") and !\Auth::user()->hasRole("super_manager") and !\Auth::user()->hasRole("manager") and \Auth::user()->id!=$prov_widget->user_id){
				return abort(403);
			}
		}
		else{
			return abort(403);
		}
		$user=\App\User::find($prov_widget->user_id);
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');

        }
		$pdo = \DB::connection('pgstatistic')->getPdo();
		/*$sql="create temp table pid_product_widget as select t1.id_widget, t1.day, count(t1.url) as showed, coalesce(t2.cnt,0) as clicked, case when (count(t1.url)>0) then round(coalesce(t2.cnt)/count(t1.url)::numeric,4)*100 else 0 end as ctr from advert_stat_pages t1 left join (select id_widget, date, count(url) as cnt from advert_stat_clicks where char_length(url)>0 group by id_widget, date) t2 on t1.id_widget=t2.id_widget and t1.day=t2.date where t1.id_widget='$id' and char_length(url)>0 and day between '$from' and '$to' group by day, t1.id_widget, t2.cnt";
		*/
		$sql="create temp table pid_brand_widget as select t1.pid, t1.day, sum(t1.showed) as showed_ru, sum(t1.unik_showed) as unik_showed_ru, 
		sum(t1.click) as click_ru, sum(t1.unik_click) as unik_click_ru, sum(t1.summa) as summa_ru, t2.showed_cis, t2.unik_showed_cis, 
		t2.click_cis, t2.unik_click_cis, t2.summa_cis, 
		case when (sum(t1.showed)>0) then round(sum(t1.click)/sum(t1.showed)::numeric,4)*100 else 0 end as ctr_ru,
		case when (sum(t2.showed_cis)>0) then round(sum(t2.click_cis)/sum(t2.showed_cis)::numeric,4)*100 else 0 end as ctr_cis,
		case when (sum(t1.showed+t2.showed_cis)>0) then round(sum(t1.click+t2.click_cis)/sum(t1.showed+t2.showed_cis)::numeric,4)*100 else 0 end as ctr,
		case when (sum(t1.showed)>0) then round(sum(t1.summa)/sum(t1.showed)::numeric,4)*1000 else 0 end as cpm_ru,
		case when (sum(t2.showed_cis)>0) then round(sum(t2.summa_cis)/sum(t2.showed_cis)::numeric,4)*1000 else 0 end as cpm_cis,
		case when (sum(t1.showed+t2.showed_cis)>0) then round(sum(t1.summa+t2.summa_cis)/sum(t1.showed+t2.showed_cis)::numeric,4)*1000 else 0 end as cpm
		from brand_stat_pid t1 left join (select pid, day, sum(showed) as showed_cis, 
		sum(unik_showed) as unik_showed_cis, sum(click) as click_cis, sum(unik_click) as unik_click_cis, sum(summa) as summa_cis
		from brand_stat_pid 
		where country='CIS' and pid='$id' and day between '$from' and '$to' group by pid, day, country) t2 on t1.pid=t2.pid 
		and t1.day=t2.day where t1.country='RU' and t1.pid='$id' and t1.day between '$from' and '$to' group by t1.pid, t1.day, t2.showed_cis, t2.unik_showed_cis, t2.click_cis, t2.unik_click_cis, 
		t2.summa_cis";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('pgstatistic')->table('pid_brand_widget')->orderBy('day', 'desc')->paginate(30);
		$statsAll=\DB::connection('pgstatistic')->table('pid_brand_widget')->select(\DB::raw('sum(showed_ru+showed_cis) as showed, 
		sum(unik_showed_ru+unik_showed_cis) as unik_showed, sum(click_ru+click_cis) as click, sum(unik_click_ru+unik_click_cis) as unik_cliсk, 
		case when (sum(showed_ru+showed_cis)>0) then round(sum(click_ru+click_cis)/sum(showed_ru+showed_cis)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(showed_ru+showed_cis)>0) then round(sum(summa_ru+summa_cis)/sum(showed_ru+showed_cis)::numeric,4)*1000 else 0 end as cpm,
		sum(summa_ru+summa_cis) as summa'))->first();
		$statsRus=\DB::connection('pgstatistic')->table('pid_brand_widget')->select(\DB::raw('sum(showed_ru) as showed, 
		sum(unik_showed_ru) as unik_showed, sum(click_ru) as click, sum(unik_click_ru) as unik_cliсk, 
		case when (sum(showed_ru)>0) then round(sum(click_ru)/sum(showed_ru)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(showed_ru)>0) then round(sum(summa_ru)/sum(showed_ru)::numeric,4)*1000 else 0 end as cpm,
		sum(summa_ru) as summa'))->first();
		$statsCis=\DB::connection('pgstatistic')->table('pid_brand_widget')->select(\DB::raw('sum(showed_cis) as showed, 
		sum(unik_showed_cis) as unik_showed, sum(click_cis) as click, sum(unik_click_cis) as unik_cliсk, 
		case when (sum(showed_cis)>0) then round(sum(click_cis)/sum(showed_cis)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(showed_cis)>0) then round(sum(summa_cis)/sum(showed_cis)::numeric,4)*1000 else 0 end as cpm,
		sum(summa_cis) as summa'))->first();
		$widget=\App\MPW\Widgets\Widget::where('id', $id)->first();
		return view('statistic.brand.pid_stat_detail', ['user'=>$user, 'statsRus'=>$statsRus, 'statsCis'=>$statsCis, 'statsAll'=>$statsAll, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'widget'=>$widget]);
	}
	
	public function summaryStat(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
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
			['title'=>"Показы",'index'=>"showed","order"=>"",'url'=>""],
			['title'=>"Уникальные показы",'index'=>"unik_showed","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"click","order"=>"",'url'=>""],
			['title'=>"Уникальные клики",'index'=>"unik_click","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpm",'index'=>"cpm","order"=>"",'url'=>""],
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
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="create temp table sum_brand_pids as select day, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, case when 
		(sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, case when (sum(showed)>0) then 
		round(sum(summa)/sum(showed)::numeric,4)*1000 else 0 end as cpm, sum(summa) as summa 
		from brand_stat_pid where day between '$from' and '$to' group by day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('pgstatistic')->table('sum_brand_pids')->orderBy($order,$direct)->paginate($number);
		$allStat=\DB::connection('pgstatistic')->table('sum_brand_pids')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(showed)>0) then round(sum(summa)/sum(showed)::numeric,4)*1000 else 0 end as cpm,
		sum(summa) as summa'))->first();
		$sql="create temp table sum_brand_pids_ru as select day, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, case when 
		(sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, case when (sum(showed)>0) then 
		round(sum(summa)/sum(showed)::numeric,4)*1000 else 0 end as cpm, sum(summa) as summa 
		from brand_stat_pid where country='RU' and day between '$from' and '$to' group by day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$ru_stats=\DB::connection('pgstatistic')->table('sum_brand_pids_ru')->orderBy($order,$direct)->paginate($number);
		$ruStat=\DB::connection('pgstatistic')->table('sum_brand_pids_ru')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(showed)>0) then round(sum(summa)/sum(showed)::numeric,4)*1000 else 0 end as cpm,
		sum(summa) as summa'))->first();
		$sql="create temp table sum_brand_pids_cis as select day, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, case when 
		(sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, case when (sum(showed)>0) then 
		round(sum(summa)/sum(showed)::numeric,4)*1000 else 0 end as cpm, sum(summa) as summa 
		from brand_stat_pid where country<>'RU' and day between '$from' and '$to' group by day";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$cis_stats=\DB::connection('pgstatistic')->table('sum_brand_pids_cis')->orderBy($order,$direct)->paginate($number);
		$cisStat=\DB::connection('pgstatistic')->table('sum_brand_pids_cis')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(showed)>0) then round(sum(summa)/sum(showed)::numeric,4)*1000 else 0 end as cpm,
		sum(summa) as summa'))->first();
		return view('statistic.brand.summary_stat', ['number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 
		'stats'=>$stats, 'allStat'=>$allStat, 'ru_stats'=>$ru_stats, 'ruStat'=>$ruStat, 'cis_stats'=>$cis_stats, 'cisStat'=>$cisStat,
		'from'=>$from, 'to'=>$to]);
	}
	
	public function sourceStat(Request $request){
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
        $order=$order?$order:"showed";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Название",'index'=>"title","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"showed","order"=>"",'url'=>""],
			['title'=>"Уникальные показы",'index'=>"unik_showed","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"click","order"=>"",'url'=>""],
			['title'=>"Уникальные клики",'index'=>"unik_click","order"=>"",'url'=>""],
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
		$sql="create temp table sum_brand_source as select t1.id, t1.title, t2.showed, t2.unik_showed, t2.click, t2.unik_click, 
		case when (t2.showed>0) then round(t2.click/t2.showed::numeric,4)*100 else 0 end as ctr 
		from brand_offers 
		t1 left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select id_offer, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click from brand_stat 
		where day between ''$from'' and ''$to'' group by id_offer') AS p(id_offer int, showed int, unik_showed int, click int, unik_click int)) t2 
		on t1.id=t2.id_offer";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('sum_brand_source')->orderBy($order,$direct)->paginate($number);
		$allStat=\DB::connection()->table('sum_brand_source')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr'))->first();
		$sql="create temp table sum_brand_source_ru as select t1.id, t1.title, t2.showed, t2.unik_showed, t2.click, t2.unik_click, 
		case when (t2.showed>0) then round(t2.click/t2.showed::numeric,4)*100 else 0 end as ctr
		from brand_offers 
		t1 left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select id_offer, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click from brand_stat 
		where country=''RU'' and day between ''$from'' and ''$to'' group by id_offer') AS p(id_offer int, showed int, unik_showed int, click int, unik_click int)) t2 
		on t1.id=t2.id_offer";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$ru_stats=\DB::connection()->table('sum_brand_source_ru')->orderBy($order,$direct)->paginate($number);
		$ruStat=\DB::connection()->table('sum_brand_source_ru')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr'))->first();
		$sql="create temp table sum_brand_source_cis as select t1.id, t1.title, t2.showed, t2.unik_showed, t2.click, t2.unik_click, 
		case when (t2.showed>0) then round(t2.click/t2.showed::numeric,4)*100 else 0 end as ctr 
		from brand_offers 
		t1 left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select id_offer, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click from brand_stat 
		where country<>''RU'' and day between ''$from'' and ''$to'' group by id_offer') AS p(id_offer int, showed int, unik_showed int, click int, unik_click int)) t2 
		on t1.id=t2.id_offer";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$cis_stats=\DB::connection()->table('sum_brand_source_cis')->orderBy($order,$direct)->paginate($number);
		$cisStat=\DB::connection()->table('sum_brand_source_cis')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr'))->first();
		return view('statistic.brand.source_stat', ['number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 
		'stats'=>$stats, 'allStat'=>$allStat, 'ru_stats'=>$ru_stats, 'ruStat'=>$ruStat, 'cis_stats'=>$cis_stats, 'cisStat'=>$cisStat,
		'from'=>$from, 'to'=>$to]);
	}
	
	public function oneSourceStat($id, Request $request){
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
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"showed","order"=>"",'url'=>""],
			['title'=>"Уникальные показы",'index'=>"unik_showed","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"click","order"=>"",'url'=>""],
			['title'=>"Уникальные клики",'index'=>"unik_click","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Редактор",'index'=>"","order"=>"",'url'=>""],
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
		$sql="create temp table sum_brand_source as select t1.id, t1.title, t2.day, t2.showed, t2.unik_showed, t2.click, t2.unik_click, 
		case when (t2.showed>0) then round(t2.click/t2.showed::numeric,4)*100 else 0 end as ctr 
		from brand_offers 
		t1 left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select id_offer, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, day from brand_stat 
		where id_offer=''$id'' and day between ''$from'' and ''$to'' group by id_offer, day') AS p(id_offer int, showed int, unik_showed int, 
		click int, unik_click int, day date)) t2 
		on t1.id=t2.id_offer where id='$id'";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('sum_brand_source')->orderBy($order,$direct)->paginate($number);
		$allStat=\DB::connection()->table('sum_brand_source')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr'))->first();
		$sql="create temp table sum_brand_source_ru as select t1.id, t1.title, t2.day, t2.showed, t2.unik_showed, t2.click, t2.unik_click, 
		case when (t2.showed>0) then round(t2.click/t2.showed::numeric,4)*100 else 0 end as ctr
		from brand_offers 
		t1 left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select id_offer, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, day from brand_stat 
		where id_offer=''$id'' and country=''RU'' and day between ''$from'' and ''$to'' group by id_offer, day') AS p(id_offer int, showed int, 
		unik_showed int, click int, unik_click int, day date)) t2 
		on t1.id=t2.id_offer where id='$id'";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$ru_stats=\DB::connection()->table('sum_brand_source_ru')->orderBy($order,$direct)->paginate($number);
		$ruStat=\DB::connection()->table('sum_brand_source_ru')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr'))->first();
		$sql="create temp table sum_brand_source_cis as select t1.id, t1.title, t2.day, t2.showed, t2.unik_showed, t2.click, t2.unik_click, 
		case when (t2.showed>0) then round(t2.click/t2.showed::numeric,4)*100 else 0 end as ctr 
		from brand_offers 
		t1 left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select id_offer, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, day from brand_stat 
		where id_offer=''$id'' and country<>''RU'' and day between ''$from'' and ''$to'' group by id_offer, day') AS p(id_offer int, showed int, 
		unik_showed int, click int, unik_click int, day date)) t2 
		on t1.id=t2.id_offer where id='$id'";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$cis_stats=\DB::connection()->table('sum_brand_source_cis')->orderBy($order,$direct)->paginate($number);
		$cisStat=\DB::connection()->table('sum_brand_source_cis')->select(\DB::raw('sum(showed) as showed, 
		sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_cliсk, 
		case when (sum(showed)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr'))->first();
		return view('statistic.brand.source_stat_one', ['number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 
		'stats'=>$stats, 'allStat'=>$allStat, 'ru_stats'=>$ru_stats, 'ruStat'=>$ruStat, 'cis_stats'=>$cis_stats, 'cisStat'=>$cisStat,
		'from'=>$from, 'to'=>$to]);
	}
}
