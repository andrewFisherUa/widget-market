<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
class ProductStatisticController extends Controller
{
     private $dbUser;
     private $dbPass;
    public function __construct()
      {
         $this->dbUser=env('DB_USERNAME');
         $this->dbPass=env('DB_PASSWORD');
        $this->middleware('auth');

     }


	public function summaryStat(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
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
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""]
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
		$sql="create temp table sum_product_widgets as select t4.day, t1.manager, sum(t4.yandex_views) as yandex_views, sum(t4.ta_views) as ta_views, 
		sum(t4.views) as views, 
		sum(t4.yandex_clicks) as yandex_clicks, sum(t4.ta_clicks) as ta_clicks, sum(t4.clicks) as clicks, 
		sum(t4.yandex_summa) as yandex_summa, sum(t4.ta_summa) as ta_summa, sum(t4.summa) as summa
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id left join 
		(select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(yandex_views) as yandex_views, sum(ta_views) as ta_views, 
		sum(yandex_views+ta_views) as views, 
		sum(yandex_clicks) as yandex_clicks, sum(ta_clicks) as ta_clicks, sum(yandex_clicks+ta_clicks) as clicks, 
		sum(yandex_summa) as yandex_summa, sum(ta_summa) as ta_summa, sum(yandex_summa+ta_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, yandex_views int, ta_views int, views int, 
		yandex_clicks int, ta_clicks int, clicks int, yandex_summa numeric(18,4), ta_summa numeric(18,4), summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
		or t5.wid_id=t4.pid
		group by t4.day, t1.manager
		";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		if ($manager){
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->where('manager', $manager)->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->where('manager', $manager)->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->where('manager', $manager)->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->where('manager', $manager)->first();
		}
		else{
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		}
		return view('statistic.product.summary_stat', ['summaryStatsAll'=>$summaryStatsAll, 'yandex_statsAll'=>$yandex_statsAll, 
		'ta_statsAll'=>$ta_statsAll,
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'yandex_stats'=>$yandex_stats, 'ta_stats'=>$ta_stats, 'manager'=>$manager]);
	}
	
	public function graph(Request $request){
		\Auth::user()->touch();
		$period=$request->input('period')?$request->input('period'):12;
		$to=date('Y-m-d H:i:s');
        $from=date('Y-m-d H:i:s',time()-3600*$period);
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select t1.timegroup as timegroup, count(t1.url) as showed, coalesce(t2.clicked,0) as clicked from advert_stat_pages t1 left join (select timegroup, count(url) as clicked from advert_stat_clicks where timegroup between '$from' and '$to' and char_length(url)>0 group by timegroup) t2 on t1.timegroup=t2.timegroup where char_length(t1.url)>0 and t1.timegroup between '$from' and '$to' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) group by t1.timegroup, t2.clicked order by t1.timegroup asc";
		$product_values = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_y=[];
		$product_y2=[];
		$product_x=[];
		foreach ($product_values as $product_value){
			$product_y[]=$product_value['showed'];
			$product_y2[]=$product_value['clicked'];
			$product_x[]=date("H:i",strtotime($product_value['timegroup']));
		}
		$graph=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Показы', $product_y)
		->dataset('Клики', $product_y2)
		->labels($product_x)
		->dimensions(1000,500)
		->colors(['#000', '#ff0000'])
		->height(400)
		->width(1200)
		->responsive(false);
		return view('statistic.product.graph', ['period'=>$period, 'graph'=>$graph]);
	}
	
	public function statsPid($id, Request $request){
		\Auth::user()->touch();
		$prov_widget=\App\MPW\Widgets\Widget::where('id', $id)->whereIn('type', [1,3])->first();
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
       if($_SERVER["REMOTE_ADDR"]=="176.213.140.214"){
       }else{

#       return;
       }
		

		
		if(1==1 || Auth::user()->hasRole(["admin","manager","super_manager"])){


			$sql="
			select
            sum(tviews+yviews) as views			
			,sum(tclicks+yclicks+clicks_plus) as clicks
			,sum(tsumma+ysumma+summa_plus) as summa			
			,CASE WHEN (coalesce(sum(tclicks+yclicks+clicks_plus),0)>0 and coalesce(sum(tviews+yviews),0)>0) 
			then round(sum(tclicks+yclicks+clicks_plus)/sum(tviews+yviews)::numeric,4)*100 else 0::numeric end as ctr
                        ,sum(tviews) as tviews			
			,sum(tclicks) as tclicks
			,sum(tsumma) as tsumma		
			,CASE WHEN (coalesce(sum(tclicks),0)>0 and coalesce(sum(tsumma),0)>0) 
			then round(sum(tsumma)/sum(tclicks)::numeric,4) else 0::numeric end as tcpc
                        ,sum(yviews) as yviews			
			,sum(yclicks) as yclicks
			,sum(ysumma) as ysumma		
			,CASE WHEN (coalesce(sum(yclicks),0)>0 and coalesce(sum(ysumma),0)>0) 
			then round(sum(ysumma)/sum(yclicks)::numeric,4) else 0::numeric end as ycpc
			,sum(mclicks) as mclicks
			,sum(msumma) as msumma		
			,CASE WHEN (coalesce(sum(mclicks),0)>0 and coalesce(sum(msumma),0)>0) 
			then round(sum(msumma)/sum(mclicks)::numeric,4) else 0::numeric end as mcpc
			from myadvert_widgets
			where pid=$id and day between '$from' and '$to'
			";
			$seler=\DB::connection("pgstatistic")->select($sql);
			$verh=null;
			if($seler)
			$verh=$seler[0];
		
			
			$sql="
			select day
            ,tviews+yviews as views			
			,tclicks+yclicks+clicks_plus as clicks
			,tsumma+ysumma+summa_plus as summa			
			,CASE WHEN ((tclicks+yclicks+clicks_plus)>0 and (tviews+yviews)>0) 
			then round((tclicks+yclicks+clicks_plus)/(tviews+yviews)::numeric,4)*100 else 0::numeric end as ctr
			,tviews			
			,tclicks
			,tsumma		
			,CASE WHEN (tclicks>0 and tsumma>0) 
			then round(tsumma/tclicks::numeric,4) else 0::numeric end as tcpc
			,yviews			
			,yclicks
			,ysumma	
			,CASE WHEN (yclicks>0 and ysumma>0) 
			then round(ysumma/yclicks::numeric,4) else 0::numeric end as ycpc
			,mclicks
			,msumma	
			,CASE WHEN (mclicks>0 and msumma>0) 
			then round(msumma/mclicks::numeric,4) else 0::numeric end as mcpc
			from myadvert_widgets
			where pid=$id and day between '$from' and '$to'
			order by day desc
			
			
			";
			$vvdata=[];
			$perPage=30;
			$xata=\DB::connection("pgstatistic")->select($sql);
			$found=count($xata);
            $page = $request->input('page', 1); 
            $offset = ($page * $perPage) - $perPage;
            $vvdata = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
			foreach($xata as $x){
				#var_dump($x); echo "<hr>";
			}
			
			#var_dump($xata)
		}else{
			/*
		$pdo = \DB::connection()->getPdo();

		$sql="create temp table pid_product_widget as select t1.id, t4.day, t1.user_id, t3.domain, t4.views, t4.clicks, t4.yandex_clicks, t4.ta_clicks, 
		t4.summa, t4.our_clicks,
		case when (t4.views>0) then round(t4.clicks/t4.views::numeric,4)*100 else 0 end as ctr, 
		case when (t4.clicks>0) then round(t4.summa/t4.clicks::numeric,4) else 0 end as cpc
		from widgets t1 left join (select id, domain from partner_pads) t3 on t1.pad=t3.id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=market password=Sdf40vcdTmv5', 
		'select pid, day, sum(yandex_views+ta_views+ts_views) as views, sum(yandex_clicks) as yandex_clicks,
		sum(ta_clicks) as ta_clicks,
		sum(yandex_clicks+ta_clicks+ts_clicks) as clicks, sum(yandex_summa+ta_summa+ts_summa) as summa, 
		sum(our_clicks) as our_clicks from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, views int, 
		yandex_clicks int, ta_clicks int, clicks int, 
		summa 
		numeric(18,4), our_clicks int)) t4 on t1.id=t4.pid where t1.id='$id'";
		$product_values = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('pid_product_widget')->orderBy('day', 'desc')->paginate(30);
		$statsAll=\DB::connection()->table('pid_product_widget')->select(\DB::raw('sum(views) as views, sum(clicks) as clicks, sum(summa) as summa, 
		sum(our_clicks) as our_clicks,  sum(yandex_clicks) as yandex_clicks, sum(ta_clicks) as ta_clicks,
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc
		'))->first();
		*/

		}

		$stats=[];
		$statsAll=[];
		//$widget_product=\App\WidgetEditor::where('id', $id)->first();
		$widget=\App\MPW\Widgets\Widget::where('id', $id)->first();
		if(1==1 || Auth::user()->hasRole(["admin","manager","super_manager"])){
		return view('statistic.product.pid_stat_detail', ['user'=>$user, 'statsAll'=>$statsAll, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'widget'=>$widget,'collection'=>$vvdata,'verh'=>$verh]);	
		}else{
		return view('statistic.product.pid_stat_detail_old', ['user'=>$user, 'statsAll'=>$statsAll, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'widget'=>$widget]);
		}
	}
	
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
			['title'=>"Клики (по нашей системе)",'index'=>"clicked","order"=>"",'url'=>""],
			['title'=>"Ctr (по нашей системе)",'index'=>"ctr","order"=>"",'url'=>""],
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
		$sql="create temp table pid_product_widget as select t1.id_widget, t1.url, count(t1.page_key) as showed, coalesce(t2.cnt,0) as clicked, 
		CASE WHEN (t2.cnt>0) then round(t2.cnt/count(t1.page_key)::numeric,4) else 0 end as ctr,
		case when (t3.showed>0) then round(count(t1.page_key)/t3.showed::numeric,4)*100 else 0 end as precent_showed
		from advert_stat_pages t1 left join (select id_widget, url, count(page_key) as cnt from advert_stat_clicks where 
		char_length(url)>0 and date between '$from' and '$to' group by id_widget, url) t2 
		on t1.id_widget=t2.id_widget and t1.url=t2.url left join 
		(select id_widget, count(page_key) as showed from advert_stat_pages where day between '$from' and '$to' and id_widget='$id' group by id_widget) 
		t3 on t1.id_widget=t3.id_widget
		where t1.id_widget='$id' and char_length(t1.url)>0 and day between 
		'$from' and '$to' group by t1.id_widget, t2.cnt, t1.url, t3.showed";
		#echo nl2br($sql); die();
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection('pgstatistic')->table('pid_product_widget')->orderBy($order,$direct)->paginate(30);
		$widget=\App\MPW\Widgets\Widget::where('id', $id)->first();
		return view('statistic.product.pid_urls_product_statistic', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'widget'=>$widget, 'header'=>$header, 'order'=>$order, 'direct'=>$direct]);
	}
	
	public function detailUser(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Имя",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Менеджер",'index'=>"manager","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Подробнее",'index'=>" ","order"=>"",'url'=>""]
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
		$sql="create temp table sum_product_widgets as select t1.manager, t1.user_id, t1.name, sum(t4.yandex_views) as yandex_views, 
		sum(t4.ta_views) as ta_views, sum(t4.views) as views, sum(t4.yandex_clicks) as yandex_clicks, sum(t4.ta_clicks) as ta_clicks, 
		sum(t4.clicks) as clicks, sum(t4.yandex_summa) as yandex_summa, sum(t4.ta_summa) as ta_summa, sum(t4.summa) as summa from user_profiles t1 
		left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id 
		left join (select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id 
		left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(yandex_views) as yandex_views, sum(ta_views) as ta_views, 
		sum(yandex_views+ta_views) as views, 
		sum(yandex_clicks) as yandex_clicks, sum(ta_clicks) as ta_clicks, sum(yandex_clicks+ta_clicks) as clicks, 
		sum(yandex_summa) as yandex_summa, sum(ta_summa) as ta_summa, sum(yandex_summa+ta_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid') AS p(pid int, yandex_views int, ta_views int, views int, 
		yandex_clicks int, ta_clicks int, clicks int, yandex_summa numeric(18,4), ta_summa numeric(18,4), summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
		or t5.wid_id=t4.pid where views>'0' group by t1.manager, t1.user_id, t1.name
		";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		if ($manager){
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->groupBy('user_id', 'name', 'manager')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->groupBy('user_id', 'name', 'manager')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->groupBy('user_id', 'name', 'manager')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->first();
		}
		else{
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->groupBy('user_id', 'name', 'manager')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->groupBy('user_id', 'name', 'manager')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->groupBy('user_id', 'name', 'manager')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->first();
		}
		return view('statistic.product.summary_users', ['summaryStatsAll'=>$summaryStatsAll, 'yandex_statsAll'=>$yandex_statsAll, 
		'ta_statsAll'=>$ta_statsAll,
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'yandex_stats'=>$yandex_stats, 'ta_stats'=>$ta_stats, 'manager'=>$manager]);
	}
	
	public function detailUserOne($id, Request $request){
		\Auth::user()->touch();
		$user=\App\User::where('id', $id)->first();

		if (!$user){
			return abort(404);
		}
		if (\Auth::user()->hasRole('manager') and $user->Profile->manager!=\Auth::user()->id){
			return abort(403);
		}
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		$manager=$request->input('manager');
		if (!$number){
			$number=20;
		}
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
            ['title'=>"День",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
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
		$sql="create temp table sum_product_widgets as select t4.day, t1.manager, sum(t4.yandex_views) as yandex_views, sum(t4.ta_views) as ta_views, 
		sum(t4.views) as views, 
		sum(t4.yandex_clicks) as yandex_clicks, sum(t4.ta_clicks) as ta_clicks, sum(t4.clicks) as clicks, 
		sum(t4.yandex_summa) as yandex_summa, sum(t4.ta_summa) as ta_summa, sum(t4.summa) as summa
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id left join 
		(select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(yandex_views) as yandex_views, sum(ta_views) as ta_views, 
		sum(yandex_views+ta_views) as views, 
		sum(yandex_clicks) as yandex_clicks, sum(ta_clicks) as ta_clicks, sum(yandex_clicks+ta_clicks) as clicks, 
		sum(yandex_summa) as yandex_summa, sum(ta_summa) as ta_summa, sum(yandex_summa+ta_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, yandex_views int, ta_views int, views int, 
		yandex_clicks int, ta_clicks int, clicks int, yandex_summa numeric(18,4), ta_summa numeric(18,4), summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
		or t5.wid_id=t4.pid where t1.user_id='$id'
		group by t4.day, t1.manager
		";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		return view('statistic.product.summary_stat_for_user', ['userP'=>$user, 'summaryStatsAll'=>$summaryStatsAll, 'yandex_statsAll'=>$yandex_statsAll, 
		'ta_statsAll'=>$ta_statsAll,
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'yandex_stats'=>$yandex_stats, 'ta_stats'=>$ta_stats, 'manager'=>$manager]);
	}
	
	public function detailPads(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Домен",'index'=>"domain","order"=>"",'url'=>""],
            ['title'=>"Имя",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Менеджер",'index'=>"manager","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Подробнее",'index'=>" ","order"=>"",'url'=>""]
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
		$sql="create temp table sum_product_widgets as select t1.manager, t1.user_id, t1.name, t6.id as pad_id, t6.domain, sum(t4.yandex_views) as yandex_views, 
		sum(t4.ta_views) as ta_views, sum(t4.views) as views, sum(t4.yandex_clicks) as yandex_clicks, sum(t4.ta_clicks) as ta_clicks, 
		sum(t4.clicks) as clicks, sum(t4.yandex_summa) as yandex_summa, sum(t4.ta_summa) as ta_summa, sum(t4.summa) as summa from user_profiles t1 
		left join (select id, user_id, pad from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id 
		left join (select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id 
		left join (select id, domain from partner_pads) t6 on t2.pad=t6.id
		left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(yandex_views) as yandex_views, sum(ta_views) as ta_views, 
		sum(yandex_views+ta_views) as views, 
		sum(yandex_clicks) as yandex_clicks, sum(ta_clicks) as ta_clicks, sum(yandex_clicks+ta_clicks) as clicks, 
		sum(yandex_summa) as yandex_summa, sum(ta_summa) as ta_summa, sum(yandex_summa+ta_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid') AS p(pid int, yandex_views int, ta_views int, views int, 
		yandex_clicks int, ta_clicks int, clicks int, yandex_summa numeric(18,4), ta_summa numeric(18,4), summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
		or t5.wid_id=t4.pid where views>'0' group by t1.manager, t1.user_id, t1.name, t6.domain, t6.id
		";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		if ($manager){
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, domain, pad_id, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->groupBy('user_id', 'name', 'manager', 'domain', 'pad_id')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, domain, pad_id, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->groupBy('user_id', 'name', 'manager', 'domain', 'pad_id')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, domain, pad_id, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->groupBy('user_id', 'name', 'manager', 'domain', 'pad_id')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->where('manager', $manager)->first();
		}
		else{
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, domain, pad_id, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->groupBy('user_id', 'name', 'manager', 'domain', 'pad_id')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, domain, pad_id, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->groupBy('user_id', 'name', 'manager', 'domain', 'pad_id')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('user_id, name, manager, domain, pad_id, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->groupBy('user_id', 'name', 'manager', 'domain', 'pad_id')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->first();
		}
		return view('statistic.product.summary_pads', ['summaryStatsAll'=>$summaryStatsAll, 'yandex_statsAll'=>$yandex_statsAll, 
		'ta_statsAll'=>$ta_statsAll,
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'yandex_stats'=>$yandex_stats, 'ta_stats'=>$ta_stats, 'manager'=>$manager]);
	}
	
	public function detailPadOne($id, Request $request){
		\Auth::user()->touch();
		$pad=\App\PartnerPad::where('id', $id)->first();
		if (!$pad){
			return abort(404);
		}
		if (\Auth::user()->hasRole('manager') and $pad->userProfile($pad->user_id)->manager!=\Auth::user()->id){
			return abort(403);
		}
		$from=$request->input('from');
		$to=$request->input('to');
		$number=$request->input('number');
		$manager=$request->input('manager');
		if (!$number){
			$number=20;
		}
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
            ['title'=>"День",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
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
		$sql="create temp table sum_product_widgets as select t4.day, t1.manager, t6.id as pad_id, sum(t4.yandex_views) as yandex_views, sum(t4.ta_views) as ta_views, 
		sum(t4.views) as views, 
		sum(t4.yandex_clicks) as yandex_clicks, sum(t4.ta_clicks) as ta_clicks, sum(t4.clicks) as clicks, 
		sum(t4.yandex_summa) as yandex_summa, sum(t4.ta_summa) as ta_summa, sum(t4.summa) as summa
		from user_profiles t1 left join (select id, user_id, pad from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id 
		left join (select id, domain from partner_pads) t6 on t6.id=t2.pad left join 
		(select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(yandex_views) as yandex_views, sum(ta_views) as ta_views, 
		sum(yandex_views+ta_views) as views, 
		sum(yandex_clicks) as yandex_clicks, sum(ta_clicks) as ta_clicks, sum(yandex_clicks+ta_clicks) as clicks, 
		sum(yandex_summa) as yandex_summa, sum(ta_summa) as ta_summa, sum(yandex_summa+ta_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, yandex_views int, ta_views int, views int, 
		yandex_clicks int, ta_clicks int, clicks int, yandex_summa numeric(18,4), ta_summa numeric(18,4), summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
		or t5.wid_id=t4.pid where t6.id='$id'
		group by t4.day, t1.manager, t6.id
		";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$summaryStats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$summaryStatsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(views),0) as views, coalesce(sum(clicks),0) as clicks, 
		coalesce(sum(summa),0) as summa, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		$yandex_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$yandex_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(yandex_views),0) as views, coalesce(sum(yandex_clicks),0) as clicks, 
		coalesce(sum(yandex_summa),0) as summa, 
		case when (sum(yandex_views)>0) then round(sum(yandex_clicks)/sum(yandex_views)::numeric,4)*100 else 0 end as ctr, case when (sum(yandex_clicks)>0) then round(sum(yandex_summa)/sum(yandex_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		$ta_stats=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('day, coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->groupBy('day')->orderBy($order,$direct)->paginate($number);
		$ta_statsAll=\DB::connection()->table('sum_product_widgets')->select(\DB::raw('coalesce(sum(ta_views),0) as views, coalesce(sum(ta_clicks),0) as clicks, 
		coalesce(sum(ta_summa),0) as summa, 
		case when (sum(ta_views)>0) then round(sum(ta_clicks)/sum(ta_views)::numeric,4)*100 else 0 end as ctr, case when (sum(ta_clicks)>0) then round(sum(ta_summa)/sum(ta_clicks)::numeric,4) else 0 end as cpc'))
		->whereNotNull('day')->first();
		return view('statistic.product.summary_stat_for_pad', ['pad'=>$pad, 'summaryStatsAll'=>$summaryStatsAll, 'yandex_statsAll'=>$yandex_statsAll, 
		'ta_statsAll'=>$ta_statsAll,
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'yandex_stats'=>$yandex_stats, 'ta_stats'=>$ta_stats, 'manager'=>$manager]);
	}
}
