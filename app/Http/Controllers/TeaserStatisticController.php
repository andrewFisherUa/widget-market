<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
class TeaserStatisticController extends Controller
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
		$sql="create temp table sum_product_widgets as select t4.day, t1.manager, sum(t4.views) as views, 
		sum(t4.clicks) as clicks, sum(t4.summa) as summa
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id left join 
		(select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(ts_views) as views, 
		sum(ts_clicks) as clicks, sum(ts_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, views int, 
		clicks int, summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
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
		}
		return view('statistic.teaser.summary_stat', ['summaryStatsAll'=>$summaryStatsAll, 
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'manager'=>$manager]);
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
		$pdo = \DB::connection()->getPdo();
		/*$sql="create temp table pid_product_widget as select t1.id_widget, t1.day, count(t1.url) as showed, coalesce(t2.cnt,0) as clicked, case when (count(t1.url)>0) then round(coalesce(t2.cnt)/count(t1.url)::numeric,4)*100 else 0 end as ctr from advert_stat_pages t1 left join (select id_widget, date, count(url) as cnt from advert_stat_clicks where char_length(url)>0 group by id_widget, date) t2 on t1.id_widget=t2.id_widget and t1.day=t2.date where t1.id_widget='$id' and char_length(url)>0 and day between '$from' and '$to' group by day, t1.id_widget, t2.cnt";
		*/
		$sql="create temp table pid_product_widget as select t1.id, t4.day, t1.user_id, t3.domain, t4.views, t4.clicks, t4.summa, t4.our_clicks,
		case when (t4.views>0) then round(t4.clicks/t4.views::numeric,4)*100 else 0 end as ctr, 
		case when (t4.clicks>0) then round(t4.summa/t4.clicks::numeric,4) else 0 end as cpc
		from widgets t1 left join (select id, domain from partner_pads) t3 on t1.pad=t3.id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(yandex_views+ta_views+ts_views) as views, sum(yandex_clicks+ta_clicks+ts_clicks) as clicks, sum(yandex_summa+ta_summa+ts_summa) as summa, 
		sum(our_clicks) as our_clicks from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, views int, clicks int, summa 
		numeric(18,4), our_clicks int)) t4 on t1.id=t4.pid where t1.id='$id'";
		$product_values = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$stats=\DB::connection()->table('pid_product_widget')->orderBy('day', 'desc')->paginate(30);
		$statsAll=\DB::connection()->table('pid_product_widget')->select(\DB::raw('sum(views) as views, sum(clicks) as clicks, sum(summa) as summa, 
		sum(our_clicks) as our_clicks, 
		case when (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr, 
		case when (sum(clicks)>0) then round(sum(summa)/sum(clicks)::numeric,4) else 0 end as cpc
		'))->first();
		//$widget_product=\App\WidgetEditor::where('id', $id)->first();
		$widget=\App\MPW\Widgets\Widget::where('id', $id)->first();
		return view('statistic.teaser.pid_stat_detail', ['user'=>$user, 'statsAll'=>$statsAll, 'stats'=>$stats, 'from'=>$from, 'to'=>$to, 'widget'=>$widget]);
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
		$sql="create temp table sum_product_widgets as select t1.manager, t1.user_id, t1.name, 
		sum(t4.views) as views, sum(t4.clicks) as clicks, sum(t4.summa) as summa from user_profiles t1 
		left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id 
		left join (select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id 
		left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(ts_views) as views, sum(ts_clicks) as clicks, sum(ts_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid') AS p(pid int, views int, clicks int, 
		summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
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
		}
		return view('statistic.teaser.summary_users', ['summaryStatsAll'=>$summaryStatsAll, 
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'manager'=>$manager]);
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
		$sql="create temp table sum_product_widgets as select t4.day, t1.manager, sum(t4.views) as views, 
		sum(t4.clicks) as clicks, sum(t4.summa) as summa
		from user_profiles t1 left join (select id, user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id left join 
		(select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(ts_views) as views, sum(ts_clicks) as clicks, sum(ts_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, views int, 
		clicks int, summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
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
		return view('statistic.teaser.summary_stat_for_user', ['userP'=>$user, 'summaryStatsAll'=>$summaryStatsAll, 
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'manager'=>$manager]);
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
		$sql="create temp table sum_product_widgets as select t1.manager, t1.user_id, t1.name, t6.id as pad_id, t6.domain, 
		sum(t4.views) as views, sum(t4.clicks) as clicks, sum(t4.summa) as summa from user_profiles t1 
		left join (select id, user_id, pad from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id 
		left join (select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id 
		left join (select id, domain from partner_pads) t6 on t2.pad=t6.id
		left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(ts_views) as views, sum(ts_clicks) as clicks, sum(ts_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid') AS p(pid int, views int, 
		clicks int, summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
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
		}
		return view('statistic.teaser.summary_pads', ['summaryStatsAll'=>$summaryStatsAll, 
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'manager'=>$manager]);
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
		$sql="create temp table sum_product_widgets as select t4.day, t1.manager, t6.id as pad_id, 
		sum(t4.views) as views, 
		sum(t4.clicks) as clicks, 
		sum(t4.summa) as summa
		from user_profiles t1 left join (select id, user_id, pad from widgets) t2 on t1.user_id=t2.user_id 
		left join (select id, wid_id, driver from widget_products) t3 on t2.id=t3.wid_id 
		left join (select id, domain from partner_pads) t6 on t6.id=t2.pad left join 
		(select id, wid_id from widget_tizers) t5 on t2.id=t5.wid_id left join 
		(SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, day, sum(ts_views) as views, sum(ts_clicks) as clicks, 
		sum(ts_summa) as summa from 
		wid_calculate where day between ''$from'' and ''$to'' group by pid, day') AS p(pid int, day date, views int, 
		clicks int, summa numeric(18,4))) t4 on t3.wid_id=t4.pid 
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
		return view('statistic.teaser.summary_stat_for_pad', ['pad'=>$pad, 'summaryStatsAll'=>$summaryStatsAll, 
		'number'=>$number, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 'from'=>$from, 'to'=>$to, 'summaryStats'=>$summaryStats, 'manager'=>$manager]);
	}
	public function summaryPadsComparison(Request $request){
		Auth::user()->touch();
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
		    sum(views) as views			
			,sum(clicks) as clicks
			,sum(summa) as summa			
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0) 
			then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0) 
			then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
		from myteaser_sites where day between '$from' and '$to'
		) t
		cross join 
		(
		select
		    sum(views) as views			
			,sum(clicks) as clicks
			,sum(summa) as summa			
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0) 
			then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0) 
			then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
		from myteaser_sites where day between '$fromOld' and '$toOld'
		
		) t2
	
		order by $order $direct
		";
		$pads_stat_all=\DB::connection('pgstatistic')->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		#var_dump($pads_stat_all); die();
		$sql="
select t.name,t.pad
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
,sum(views) as views
,sum(clicks) as clicks
,sum(summa) as summa
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0)
then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0)
then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
from myteaser_sites where day between '$from' and '$to'
group by name,pad
) t
left join
(
select name,pad
,sum(views) as views
,sum(clicks) as clicks
,sum(summa) as summa
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0)
then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0)
then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
from myteaser_sites where day between '$fromOld' and '$toOld'
group by name,pad
) t2
on t2.pad=t.pad and t2.name=t.name
		order by $order $direct
		";		
		
		$pads_stats=\DB::connection('pgstatistic')->select($sql);
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
		return view('statistic.teaser.pads_stat_comparison', $params);		 
		
		#foreach($pads_stats as $d){
			#print "<pre>"; print_r($d); print "</pre>";
			
		#}
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
		    sum(views) as views			
			,sum(clicks) as clicks
			,sum(summa) as summa			
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0) 
			then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0) 
			then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
		from myteaser_sites where day between '$from' and '$to'
		) t
		cross join 
		(
		select
		    sum(views) as views			
			,sum(clicks) as clicks
			,sum(summa) as summa			
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0) 
			then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
			,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0) 
			then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
		from myteaser_sites where day between '$fromOld' and '$toOld'
		
		) t2
	
		order by $order $direct
		";
		$pads_stat_all=\DB::connection('pgstatistic')->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="
select t.name,t.pad
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
select user_name as name,user_id as pad
,sum(views) as views
,sum(clicks) as clicks
,sum(summa) as summa
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0)
then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0)
then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
from myteaser_sites where day between '$from' and '$to'
group by user_name,user_id
) t
left join
(
select user_name as name,user_id as pad
,sum(views) as views
,sum(clicks) as clicks
,sum(summa) as summa
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(views),0)>0)
then round(sum(clicks)/sum(views)::numeric,4)*100 else 0::numeric end as ctr
,CASE WHEN (coalesce(sum(clicks),0)>0 and coalesce(sum(summa),0)>0)
then round(sum(summa)/sum(clicks)::numeric,4) else 0::numeric end as cpc
from myteaser_sites where day between '$fromOld' and '$toOld'
group by user_name,user_id
) t2
on t2.pad=t.pad and t2.name=t.name
		order by $order $direct
		";		
						$pads_stats=\DB::connection('pgstatistic')->select($sql);
		
		
		
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
		return view('statistic.teaser.partner_stat_comparison', $params);
		
	}
	
	
}
