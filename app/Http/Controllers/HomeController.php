<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Route;
class HomeController extends Controller
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
    public function index($id_user=0, Request $request){

		
		\Auth::user()->touch();
		$user=Auth::user();
		$from=$request->input('from');
		$to=$request->input('to');
		$search=$request->input('search');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		$manager_for_client=$request->input('manager_for_client');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		$userProf=UserProfile::where('user_id', $user->id)->first();
		if ($userProf->manager){
			$manager=UserProfile::where('user_id', $userProf->manager)->first();
		}
		else{
			$manager=0;
		}
			$partnerPads=\App\PartnerPad::where('user_id', $userProf->user_id)->get();
			$partnerWidgets=\App\MPW\Widgets\Widget::where('user_id', $userProf->user_id)->get();
		$pdo = \DB::connection()->getPdo();
		$sql="select t1.user_id, t2.id, t2.domain, sum(coalesce(t6.summa,0)+coalesce(t7.summa,0)) as summa
		from user_profiles t1 left join
		(select id, domain, user_id from partner_pads) t2 on t1.user_id=t2.user_id left join 
		(select id, pad from widgets) t3 on t2.id=t3.pad left join 
		(select id, wid_id from widget_videos) t4 on t3.id=t4.wid_id left join
		(select id, wid_id from widget_products) t5 on t3.id=t5.wid_id left join 
		(SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(coalesce(summa,0)) as summa from pid_summa 
		where day=''2017-10-19'' group by pid') AS p(pid int, summa numeric(18,4))) t6 on t4.id=t6.pid left join 
		(SELECT p.* FROM dblink 
		('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(coalesce(yandex_summa,0)+coalesce(ta_summa,0)) as summa 
		from wid_calculate where day =''2017-10-19'' group by pid') AS p(pid int, summa numeric(18,4))) t7 on t5.id=t7.pid
		where t1.user_id='$userProf->user_id'
		group by t1.user_id, t2.id, t2.domain";
		$graph_charts = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
			$name=[];
			$summa=[];
		foreach ($graph_charts as $g_c){
			$name[]=$g_c['domain'];
			$summa[]=$g_c['summa'];
		}
		
		$chart=Charts::create('donut', 'c3')
		->labels($name)
		->values($summa)
		->dimensions(0,0)
		->height(257)
		->title('')
		->legend(false)
		->responsive(false);
		if ($user->hasRole('affiliate')){
			$news_lim=\App\News::where('role', 1)->orderBy('created_at', 'desc')->take(20)->get();
		}
		else if($user->hasRole('advertiser')){
			$news_lim=\App\News::where('role', 2)->orderBy('created_at', 'desc')->take(20)->get();
		}
		else{
			$news_lim=\App\News::orderBy('created_at', 'desc')->take(20)->get();
		}
		if ($user->hasRole('advertiser')){
		
			 return view('home', ['user'=>$user, 'userProf'=>$userProf, 'manager_for_client'=>$manager_for_client]);
			//var_dump(111); die();
		}
		if ($user->hasRole('admin') or $user->hasRole('super_manager')){
		$to_fro_graph=date('Y-m-d H:i:s');
        $from_fro_graph=date('Y-m-d H:i:s',time()-3600*6);
		$to1_fro_graph=date('Y-m-d H:i:s',time()-3600*24);
        $from1_fro_graph=date('Y-m-d H:i:s',time()-3600*30);
		$pdo = \DB::connection('videotest')->getPdo();
        //$pgsql="select * from videostatistic_graph where id_src='0' and datetime BETWEEN '$from' and '$to' and datetime not in (select max(datetime) from videostatistic_graph);";
		$pgsql="select t1.datetime as t1datetime, t2.datetime as t2datetime, coalesce(t1.cnt,0) as t1cnt, coalesce(t2.cnt,0) as t2cnt from videostatistic_graph t1 left join (select * from videostatistic_graph where id_src='0' and datetime BETWEEN '$from1_fro_graph' and '$to1_fro_graph') t2 on t1.datetime::timestamp::time=t2.datetime::timestamp::time where t1.id_src='0' and t1.datetime BETWEEN '$from_fro_graph' and '$to_fro_graph' and t1.datetime not in (select max(datetime) from videostatistic_graph)";
		$g_values = $pdo->query($pgsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$graph_y=[];
		$graph_x=[];
		foreach ($g_values as $g_value){
			$graph_y[]=$g_value['t1cnt'];
			$graph_y2[]=$g_value['t2cnt'];
			$graph_x[]=date("H:i",strtotime($g_value['t1datetime']));
		}
		$graph=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Запросы сегодня', $graph_y)
		->dataset('Запросы вчера', $graph_y2)
		->labels($graph_x)
		->dimensions(1000,500)
		->width(555)
		->colors(['#000', '#ff0000'])
		->height(257)
		->responsive(false);
		
		$prsql="select t1.timegroup as t1timegroup, t2.timegroup as t2timegroup, coalesce(count(t1.url),0) as t1showed, coalesce(t2.url,0) as t2showed from advert_stat_pages t1 left join (select timegroup, count(url) as url from advert_stat_pages where timegroup between '$from1_fro_graph' and '$to1_fro_graph' and char_length(url)>0 and driver in (1,2) group by timegroup) t2 on t1.timegroup::timestamp::time=t2.timegroup::timestamp::time where char_length(t1.url)>0 and t1.timegroup between '$from_fro_graph' and '$to_fro_graph' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) and t1.driver in (1,2) group by t1.timegroup, t2.timegroup, t2.url order by t1.timegroup asc";
//		var_dump($sql);
		//$prsql="select t1.timegroup as t1timegroup, coalesce(count(t1.url),0) as t1showed from advert_stat_pages t1 where char_length(t1.url)>0 and t1.timegroup between '$from_fro_graph' and '$to_fro_graph' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) group by t1.timegroup, t2.timegroup, t2.url order by t1.timegroup asc";
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$product_values = $pdo->query($prsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_y=[];
		$product_x=[];
		$product_y2=[];
		foreach ($product_values as $product_value){
			$product_y[]=$product_value['t1showed'];
			$product_y2[]=$product_value['t2showed'];
			$product_x[]=date("H:i",strtotime($product_value['t1timegroup']));
		}
		$graphProduct=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Показы сегодня', $product_y)
		->dataset('Показы вчера', $product_y2)
		->labels($product_x)
		->dimensions(1000,500)
		->width(555)
		->colors(['#000', '#ff0000'])
		->height(110)
		->responsive(false);
		}
		else{
		$graph=0;
		$graphProduct=0;
		}
		
		
		$title=$request->input('title');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Имя",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Ставка",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
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
		
		$sql="create temp table sum_stat as select t1.id, coalesce(t4.summa,0) as video_summa, 
		coalesce(t4.loaded,0) as video_loaded, coalesce(t4.played,0) as video_played, coalesce(t4.calculate,0) as video_calculate, 
		coalesce(t4.clicks,0) as video_clicks, coalesce(t4.completed,0) as video_completed, coalesce(t4.second,0) as second, 
		coalesce(t4.second_summa,0) as second_summa, coalesce(t5.loaded,0) as product_played, 
		coalesce(t5.clicks,0) as product_clicks, 
		coalesce(t5.summa,0) as product_summa from widgets t1 
		left join (select id, wid_id from widget_videos) t2 on t1.id=t2.wid_id 
		left join (select id, wid_id from widget_products) t3 on t1.id=t3.wid_id 
		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa) as summa, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(clicks) as clicks, 
		sum(completed) as completed, sum(second+second_cheap) as second, sum(second_summa+second_cheap_summa) as second_summa from 
		pid_summa where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, clicks int, completed int, second int, second_summa numeric(18,4))) t4 on t2.id=t4.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+coalesce(ta_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t5 on t3.id=t5.pid where t1.user_id='$userProf->user_id'";
		$pdo_precluck=\DB::connection()->getPdo();
		$pdo_precluck->query($sql, \PDO::FETCH_ASSOC);
		$user_all_sum=\DB::table('sum_stat')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))->first();
		
		$all_balance=[];
		$pdo_precluck=\DB::connection()->getPdo();
		$sqlUsers="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t6.second,0) as second, 
		coalesce(t6.second_summa,0) as second_summa, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa

		from user_profiles t1 
		left join (select id, pad, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products) t4 on t2.id=t4.wid_id
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa) as summa, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(clicks) as clicks, 
		sum(completed) as completed, sum(second+second_cheap) as second, sum(second_summa+second_cheap_summa) as second_summa from 
		pid_summa where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, clicks int, completed int, second int, second_summa numeric(18,4))) t6 on t3.id=t6.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+coalesce(ta_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t7 on t4.id=t7.pid 

		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.calculate, t6. clicks, t6.completed, t6.second, t6.second_summa, t7.loaded, t7.clicks, t7.summa, t1.referer";
		$all_sum=[];
		if (Auth::user()->hasRole('admin')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client){
				if ($manager_for_client=='no_manager'){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', '0')->orWhereNull('manager')->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', $manager_for_client)->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', $manager_for_client)->first();
				}
			}
			else if ($search){
			$manager_for_client=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,				
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->first();
			}
			$today_video=\DB::connection('videotest')->table('pid_summa')->where('day', date('Y-m-d'))->sum('summa');
			$today_video_second=\DB::connection('videotest')->table('pid_summa')->where('day', date('Y-m-d'))->sum('second_summa');
			$today_yandex=\DB::connection('pgstatistic')->table('wid_calculate')->where('day', date('Y-m-d'))->sum('yandex_summa');
			$today_ta=\DB::connection('pgstatistic')->table('wid_calculate')->where('day', date('Y-m-d'))->sum('ta_summa');
			$all_balance['all']=\DB::table('user_profiles')->sum('balance');
			$all_balance['today']=$today_video+$today_yandex+$today_ta+$today_video_second;
			$all_balance['payment']=\App\Payments\UserPayout::where('status', '0')->sum('payout');
		}
		
		else if (Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client and Auth::user()->hasRole('super_manager')){
				if ($manager_for_client=='no_manager'){
					$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', '0')->orWhereNull('manager')
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks,
				 coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', '0')->orWhereNull('manager')
				->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else if($search and Auth::user()->hasRole('manager')){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks,
				 coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->first();
			}
		}
		else{
			$allUsersActive=0;
			$all_sum=0;
			$productUsersActive=0;
			$product_sum=0;
		}
		$user_notif=\App\AllNotification::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('home', ['number'=>$number, 'user'=>$user, 'manager'=>$manager, 'userProf'=>$userProf, 'partnerPads'=>$partnerPads, 'chart'=>$chart, 
		'partnerWidgets'=>$partnerWidgets, 'news_lim'=>$news_lim, 'graph'=>$graph, 'graphProduct'=>$graphProduct, 'allUsersActive'=>$allUsersActive, 
		'user_notif'=>$user_notif, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'header'=>$header, 'order'=>$order, 'direct'=>$direct, 
		'all_balance'=>$all_balance, 'all_sum'=>$all_sum, 
		'manager_for_client'=>$manager_for_client, 'user_all_sum'=>$user_all_sum]);
    }
	public function getDetailWidgets($id, Request $request){
			$widgets=\App\MPW\Widgets\Widget::where('user_id', $id)->where('status', 0)->get();
			$from=$request->input('from');
			$to=$request->input('to');
			$datetime=date('Y-m-d H:i:s');
			$frame=\DB::connection()->table('frame_prover')->where('user_id', $id)->where('datetime', '>', $datetime)->first();
			return response()->json([
				'ok' => true,
				'id' => $id,
				'view' => view('common.cabinet.user_widgets', ['widgets'=>$widgets, 'id'=>$id, 'from'=>$from, 'to'=>$to, 'frame'=>$frame])->render()
			]);
	}
	public function getVideoWidgets(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		
		$search=$request->input('search');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		$manager_for_client=$request->input('manager_for_client');
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
			['title'=>"Ставка",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
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
		
		$pdo_precluck=\DB::connection()->getPdo();
		$sqlUsers="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t6.second,0) as second, 
		coalesce(t6.second_summa,0) as second_summa, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa

		from user_profiles t1 
		left join (select id, pad, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products) t4 on t2.id=t4.wid_id
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa) as summa, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(clicks) as clicks, 
		sum(completed) as completed, sum(second+second_cheap) as second, sum(second_summa+second_cheap_summa) as second_summa from 
		pid_summa where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, clicks int, completed int, second int, second_summa numeric(18,4))) t6 on t3.id=t6.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+coalesce(ta_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t7 on t4.id=t7.pid 

		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.calculate, t6. clicks, t6.completed, t6.second, t6.second_summa, t7.loaded, t7.clicks, t7.summa, t1.referer";
		$all_sum=[];
		$all_balance=[];
		if (Auth::user()->hasRole('admin')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client){
				if ($manager_for_client=='no_manager'){
				$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					coalesce(sum(video_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
						->where('manager', '0')->orWhereNull('manager')->whereNull('status')
						->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
						->orderBy($order,$direct)->paginate($number);
				$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
							->where('manager', '0')->orWhereNull('manager')->first();
			
				}
				else{
				$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
						->where('manager', $manager_for_client)->whereNull('status')
						->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
						->orderBy($order,$direct)->paginate($number);
				$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
							->where('manager', $manager_for_client)->first();
				}
			}
			else if ($search){
			$manager_for_client=0;
			$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->first();
			}
			$today_video=\DB::connection('videotest')->table('pid_summa')->where('day', date('Y-m-d'))->sum('summa');
			$today_video_second=\DB::connection('videotest')->table('pid_summa')->where('day', date('Y-m-d'))->sum('second_summa');
			$today_yandex=\DB::connection('pgstatistic')->table('wid_calculate')->where('day', date('Y-m-d'))->sum('yandex_summa');
			$today_ta=\DB::connection('pgstatistic')->table('wid_calculate')->where('day', date('Y-m-d'))->sum('ta_summa');
			$all_balance['all']=\DB::table('user_profiles')->sum('balance');
			$all_balance['today']=$today_video+$today_yandex+$today_ta+$today_video_second;
			$all_balance['payment']=\App\Payments\UserPayout::where('status', '0')->sum('payout');
		}
		else if (Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client and Auth::user()->hasRole('super_manager')){
				if ($manager_for_client=='no_manager'){
				$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', '0')->orWhereNull('manager')
					->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
				$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', '0')->orWhereNull('manager')
					->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
			$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else if($search and Auth::user()->hasRole('manager')){
			$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', Auth::user()->id)
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
						->where('manager', Auth::user()->id)
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$videoUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$video_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->first();
			}
		}
		
			return response()->json([
				'ok' => true,
				'view' => view('admin.cabinet.users_video_widgets', ['number'=>$number, 'videoUsersActive'=>$videoUsersActive, 'video_sum'=>$video_sum, 
				'all_balance'=>$all_balance, 'header'=>$header, 'order'=>$order, 'direct'=>$direct,
				])->render()
				
			]);
	}
	
	public function getProductWidgets(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		
		$search=$request->input('search');
		$number=$request->input('number');
		if (!$number){
			$number=20;
		}
		$manager_for_client=$request->input('manager_for_client');
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
			['title'=>"Ставка",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"second","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
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
		
		$pdo_precluck=\DB::connection()->getPdo();
		$sqlUsers="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t6.second,0) as second, 
		coalesce(t6.second_summa,0) as second_summa, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa

		from user_profiles t1 
		left join (select id, pad, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products) t4 on t2.id=t4.wid_id
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa) as summa, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(clicks) as clicks, 
		sum(completed) as completed, sum(second+second_cheap) as second, sum(second_summa+second_cheap_summa) as second_summa from 
		pid_summa where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, clicks int, completed int, second int, second_summa numeric(18,4))) t6 on t3.id=t6.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+coalesce(ta_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t7 on t4.id=t7.pid 

		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.calculate, t6. clicks, t6.completed, t6.second, t6.second_summa, t7.loaded, t7.clicks, t7.summa, t1.referer";
		$all_sum=[];
		$all_balance=[];
		if (Auth::user()->hasRole('admin')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client){
				if ($manager_for_client=='no_manager'){
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', '0')->orWhereNull('manager')->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', $manager_for_client)->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', $manager_for_client)->first();
				}
			}
			else if ($search){
			$manager_for_client=0;
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->first();
			}
			$today_video=\DB::connection('videotest')->table('pid_summa')->where('day', date('Y-m-d'))->sum('summa');
			$today_video_second=\DB::connection('videotest')->table('pid_summa')->where('day', date('Y-m-d'))->sum('second_summa');
			$today_yandex=\DB::connection('pgstatistic')->table('wid_calculate')->where('day', date('Y-m-d'))->sum('yandex_summa');
			$today_ta=\DB::connection('pgstatistic')->table('wid_calculate')->where('day', date('Y-m-d'))->sum('ta_summa');
			$all_balance['all']=\DB::table('user_profiles')->sum('balance');
			$all_balance['today']=$today_video+$today_yandex+$today_ta+$today_video_second;
			$all_balance['payment']=\App\Payments\UserPayout::where('status', '0')->sum('payout');
		}
		
		else if (Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client and Auth::user()->hasRole('super_manager')){
				if ($manager_for_client=='no_manager'){
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', '0')->orWhereNull('manager')
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', '0')->orWhereNull('manager')
				->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else if($search and Auth::user()->hasRole('manager')){
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', Auth::user()->id)
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->where(function($query) use ($search)
				{
					$query->where('name', '~*', trim($search))
					->orWhere('email', '~*', trim($search))
					->orWhere('domain', '~*', trim($search));
				})->first();
			}
			else{
			$productUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$product_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->first();
			}
		}
		
			return response()->json([
				'ok' => true,
				'view' => view('admin.cabinet.users_product_widgets', ['number'=>$number, 'productUsersActive'=>$productUsersActive, 'product_sum'=>$product_sum, 
				'all_balance'=>$all_balance, 'header'=>$header, 'order'=>$order, 'direct'=>$direct,
				])->render()
				
			]);
	}
	
	public function getDetailWidgetsVideo($id, Request $request){
		$widgets=\App\MPW\Widgets\Widget::where('user_id', $id)->where('type', '2')->where('status', 0)->get();
			$from=$request->input('from');
			$to=$request->input('to');
			$datetime=date('Y-m-d H:i:s');
			$frame=\DB::connection()->table('frame_prover')->where('user_id', $id)->where('datetime', '>', $datetime)->first();
			return response()->json([
				'ok' => true,
				'id' => $id,
				'view' => view('common.cabinet.user_widgets_video', ['widgets'=>$widgets, 'frame'=>$frame, 'id'=>$id, 'from'=>$from, 'to'=>$to])->render()
			]);
	}
	
	public function getDetailWidgetsProduct($id, Request $request){
		$widgets=\App\MPW\Widgets\Widget::where('user_id', $id)->whereIn('type', [1])->where('status', 0)->get();
			$from=$request->input('from');
			$to=$request->input('to');
			return response()->json([
				'ok' => true,
				'id' => $id,
				'view' => view('common.cabinet.user_widgets_product', ['widgets'=>$widgets, 'id'=>$id, 'from'=>$from, 'to'=>$to])->render()
			]);
	}
	
	public function getDetailWidgetsTeaser($id, Request $request){
		$widgets=\App\MPW\Widgets\Widget::where('user_id', $id)->whereIn('type', [3])->where('status', 0)->get();
			$from=$request->input('from');
			$to=$request->input('to');
			return response()->json([
				'ok' => true,
				'id' => $id,
				'view' => view('common.cabinet.user_widgets_teaser', ['widgets'=>$widgets, 'id'=>$id, 'from'=>$from, 'to'=>$to])->render()
			]);
	}
	public function profile($id_user=0){
	      //die();

		$conf=[];
		$conf["wparams"]=Route::current()->parameters;
		$conf["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^(admin\.)([a-z\-]+)/',$func,$m))
			$conf["pref"]=$m[1];

		\Auth::user()->touch();
		$user=Auth::user();
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
	
		if ($user->hasRole('advertiser') and !Auth::user()->hasRole('admin')){
			
			#return abort(403);
			
			
			
		}

		$userProf=UserProfile::where('user_id', $user->id)->first();
//var_dump($userProf);

		if ($user->hasRole('advertiser')){
		$requisite=\App\Requisite::where('user_id', $user->id)->first();
		$advertises=\DB::connection("advertise")->table("advertises")
		->select("advertises.*","advertise_statuses.name as statname")
		->where('user_id',$user->id)
		->join('advertise_statuses', 'advertises.status', '=', 'advertise_statuses.id')
		->orderBy("advertises.name")->get();

		#var_dump($advertises);
        return view('advertiser.payouts.profile', ['userProf'=>$userProf,'requisite'=>$requisite,'user'=>$user
		,'advertises'=>$advertises,"config"=>$conf
		]);				
		}
		       //угодай
		
		
		if (Auth::user()->hasRole('manager') and $userProf->manager!=Auth::user()->Profile->user_id and $userProf->user_id!=Auth::user()->Profile->user_id){
			return abort(403);
		}
		$currentRole=0;
	        if($user->hasRole('affiliate')){
		$currentRole=1;
                }elseif($user->hasRole('advertiser')){
		$currentRole=2;
                }elseif($user->hasRole('manager')){
		$currentRole=100;
                }elseif($user->hasRole('super_manager')){
		$currentRole=100;
                }elseif($user->hasRole('admin')){
		$currentRole=100;
                }

		$payreq=\DB::table('payment_options')->get();
		$UserPay=\App\UsersPaymentOption::where('user_id', $userProf->user_id)->get();
        
		$pads=\App\PartnerPad::where('user_id', $userProf->user_id)->orderBy('created_at', 'asc')->get();
		$payouts=\App\Payments\UserPayout::where('user_id', $userProf->user_id)->orderBy('time_payout', 'desc')->paginate(10);
		//echo "<pre>"; 
		//var_dump($payouts); 
		//var_dump($userProf->user_id); 
		//echo "</pre>";
		
		$referals=\App\Transactions\UserReferalTransacion::where('user_id', $userProf->user_id)->orderBy('day', 'desc')->paginate(10);
		$referals_all=\App\Transactions\UserReferalTransacion::where('user_id', $userProf->user_id)->sum('summa');
		$pays=\App\Payments\PaymentCommission::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
		return view('common.profile', ['userProf'=>$userProf, 'UserPay'=>$UserPay, 'pads'=>$pads
                , 'payreq'=>$payreq, 'payouts'=>$payouts, 'referals_all'=>$referals_all, 'referals'=>$referals, 'pays'=>$pays,'currentRole'=>$currentRole]);
	}
	public function profilePost(Request $request){



		\Auth::user()->touch();
		$affiliate = \App\UserProfile::where('user_id', $request->input('id'))->first();
		$affiliate->firstname = $request->input('firstname');
        $affiliate->lastname = $request->input('lastname');
        if (strlen($request->input('phone'))==17){
			$affiliate->phone = $request->input('phone');
		}else{
			$affiliate->phone = "";
		}
	$client_role=$request->input('client_role');
	if($client_role){
	   $role=\App\Role::find($client_role);
	   if($role){
    
	    //$user=\App\User::where('id', $request->input('id'))->first();
            //if($user->hasRole($role->name));
      	    //var_dump($affiliate); die();
           }

        }

        $affiliate->icq = $request->input('icq');
        $affiliate->skype = $request->input('skype');
        $affiliate->name = $request->input('firstname') . " " . $request->input('lastname');
		$avatar = $request->file('avatar');
		if ($avatar) {
				$avatarFormat=$avatar->getClientOriginalExtension();
						$validator=Validator::make(
						array(
							'avatar' => $avatar
						),
						array(
							'avatar' => 'image|max:512|mimes:jpeg,png,gif',
						)
					);
					if ($validator->fails()){
						return back()->withErrors($validator)->withInput();
					}
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(80, 80)->save(public_path() . '/images/avatars/' . $filename);
			if ($affiliate->avatar){
				unlink(public_path() . '/images/avatars/' . $affiliate->avatar);
			}
            $affiliate->avatar = $filename;
        }
		
		#$fdata=["user_id"=>$affiliate->id,];
		$affiliate->save();
		\DB::connection('advertise')->table('user_profile')
            ->where("user_id", $affiliate->id)
            ->update(["name"=>$affiliate->name]);
		if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager')){
			$unsubscribe=$request->input('unsubscribe');
			$user=\App\User::where('id', $request->input('id'))->first();
			$user->unsubscribe=$unsubscribe?$unsubscribe:0;
			$user->save();
		}
		return back();
	}
	
	public function userNoActive($id_user){
		\Auth::user()->touch();
		$user=User::findOrFail($id_user)->Profile;
		$user->status=1;
		$user->save();
		return back()->with('message_success', "Вебмастер '$user->name' отмечен как неактивный.");
	}
	
	public function userNoActiveJs(Request $request){
		\Auth::user()->touch();
		$id_user=$request->input('id_user');
		$user=User::findOrFail($id_user)->Profile;
		$user->status=1;
		$user->save();
		return response()->json([
			'ok' => true,
			'message' => "Вебмастер ".$user->name." отмечен как неактивный."
		]);
	}
	
	public function userActive($id_user){
		\Auth::user()->touch();
		$user=User::findOrFail($id_user)->Profile;
		$user->status=null;
		$user->save();
		return back()->with('message_success', "Вебмастер '$user->name' отмечен как активный.");
	}
	
	
	public function userActiveJs(Request $request){
		\Auth::user()->touch();
		$id_user=$request->input('id_user');
		$user=User::findOrFail($id_user)->Profile;
		$user->status=null;
		$user->save();
		return response()->json([
			'ok' => true,
			'message' => "Вебмастер ".$user->name." отмечен как активный."
		]);
	}
	
	public function userLeaseJs(Request $request){
		\Auth::user()->touch();
		$id_user=$request->input('id_user');
		$user=User::findOrFail($id_user)->Profile;
		$user->lease=1;
		$user->save();
		return response()->json([
			'ok' => true,
			'message' => "Вебмастер ".$user->name." отмечен как на аренде."
		]);
	}
	
	public function userNoLeaseJs(Request $request){
		\Auth::user()->touch();
		$id_user=$request->input('id_user');
		$user=User::findOrFail($id_user)->Profile;
		$user->lease=0;
		$user->save();
		return response()->json([
			'ok' => true,
			'message' => "Вебмастер ".$user->name." снят с аренды."
		]);
	}
	
	
	
	public function removeNotif($id){
		\Auth::user()->touch();
		$notifs=Auth::user()->unreadNotifications->where('type', '<>', 'App\Notifications\NewNews')->where('data', $id);
		foreach ($notifs as $notif){
			$notif->markAsRead();
		}
		return back();
	}
	
	public function SetPays(Request $request){

		\Auth::user()->touch();
		$id=$request->input('id');
		$payment=$request->input('pay');
		$preference=$request->input('preference');
		$pay=[];
		$payreq=\DB::table('payment_options')->get();
		$n=0;
		foreach($payreq as $pa){
			$pay[$pa->id]=$payment[$n++];
		}
		foreach($pay as $key=>$pay){
			$UserPay=\App\UsersPaymentOption::firstOrNew(['user_id' => $id, 'payment_id' => $key]);
			$UserPay->user_id=$id;
			$UserPay->payment_id=$key;
			$UserPay->value=$pay;
			$UserPay->save();
		}
		if ($preference){
			$UserPay=\App\UsersPaymentOption::where('user_id', $id)->where('payment_id', $preference)->first();
			if ($UserPay->value!=0){
				$preference=$UserPay->payment_id;
			}
			else{
				$web=\App\UsersPaymentOption::where('user_id', $id)->where('payment_id', '4')->first();
				if ($web->value!=0){
					$preference=$web->payment_id;
				}
				else{
				$UserPay=\App\UsersPaymentOption::where('user_id', $id)->whereNotIn('value', array(0))->first();
				$preference=$UserPay->payment_id;
				}
			}
		}
		else{
			if (empty($pay[$preference])){
				$web=\App\UsersPaymentOption::where('user_id', $id)->where('payment_id', '4')->first();
				if ($web->value!=0){
					$preference=$web->payment_id;
				}
				else{
					$UserPay=\App\UsersPaymentOption::where('user_id', $id)->whereNotIn('value', array(0))->first();
					if (!$UserPay){
					$preference=0;
					}
					else{
					$preference=$UserPay->payment_id;
					}
				}
			}
		}
		$user=\App\UserProfile::where('user_id', $id)->first();
		$user->payment_option_id=$preference;
		$user->save();
		return back()->with('message_success', "Данные успешно изменены.");
	}
	
	public function userForManager(Request $request){
		\Auth::user()->touch();
		$user=\App\UserProfile::where('user_id', $request->input('user_id'))->first();
		$user->manager=$request->input('manager');
		$user->save();
		return back()->with('message_success', "Менеджер успешно назначен.");
	}
	
	public function userForManagerJs(Request $request){
		\Auth::user()->touch();
		$user=\App\UserProfile::where('user_id', $request->input('user_id'))->first();
		$user->manager=$request->input('manager');
		$user->save();
		return response()->json([
			'ok' => true,
			'message' => 'Менеджер успешно назначен.'
		]);
	}
	
	public function userForDopStatus(Request $request){
		\Auth::user()->touch();
		$user=\App\UserProfile::where('user_id', $request->input('user_id'))->first();
		$dop_status=$request->input('dop_status');
		if ($dop_status==0){
			$user->dop_status=null;
		}
		else{
			$user->dop_status=$dop_status;
		}
		$user->text_for_dop_status=$request->input('text_for_dop_status');
		$user->save();
		return back()->with('message_success', "Дополнительный статус успешно назначен.");
	}
	
	public function userForDopStatusJs(Request $request){
		\Auth::user()->touch();
		$user=\App\UserProfile::where('user_id', $request->input('user_id'))->first();
		$dop_status=$request->input('dop_status');
		if ($dop_status==0){
			$user->dop_status=null;
		}
		else{
			$user->dop_status=$dop_status;
		}
		$user->text_for_dop_status=$request->input('text_for_dop_status');
		$user->save();
		return response()->json([
			'ok' => true,
			'message' => 'Статус успешно назначен.'
		]);
	}
	
	public function userDefaultVideo(Request $request){
		\Auth::user()->touch();
		$type=$request->input('type');
		if ($type==0){
			return back()->with('message_danger', "Не выбран тип виджета.");
		}
		else if ($type==1){
			$wid_type=1;
			$pad_type=0;
		}
		else if ($type==2){
			$wid_type=1;
			$pad_type=1;
		}
		else if ($type==3){
			$wid_type=1;
			$pad_type=2;
		}
		else if ($type==4){
			$wid_type=2;
			$pad_type=0;
		}
		else if ($type==5){
			$wid_type=2;
			$pad_type=1;
		}
		else if ($type==6){
			$wid_type=2;
			$pad_type=2;
		}
		else if ($type==7){
			$driver=1;
		}
		else if ($type==8){
			$driver=2;
		}
		if ($type==7 or $type==8){
			$default=\App\ProductDefaultOnUser::firstOrNew(['user_id'=>$request->input('user_id'), 'driver'=>$driver]);
			$default->commission=$request->input('product_commission');
			$default->save();
		}
		else if ($type==9){
			$link_summa_rus=$request->input('link_summa_rus');
			if (!$link_summa_rus){
				$link_summa_rus=0;
			}
			$link_summa_cis=$request->input('link_summa_cis');
			if (!$link_summa_cis){
				$link_summa_cis=0;
			}
			$default=\App\UserLinkSumma::firstOrNew(['user_id'=>$request->input('user_id'), 'link_id'=>$request->input('link_id')]);
			$default->summa_rus=$link_summa_rus;
			$default->summa_cis=$link_summa_cis;
			$default->save();
		}
		else{
		$default=\App\VideoDefaultOnUser::firstOrNew(['user_id'=>$request->input('user_id'), 'wid_type'=>$wid_type, 'pad_type'=>$pad_type]);
		$default->commission_rus=$request->input('commission_rus');
		$default->commission_cis=$request->input('commission_cis');
		$default->save();
		}
		return back()->with('message_success', "Комиссии успешно назначены.");
	}
	public function userDefaultJs(Request $request){
		\Auth::user()->touch();
		$type=$request->input('type');
		if ($type==0){
			return response()->json([
				'ok' => false,
				'message' => 'Не выбран тип виджета.'
			]);
		}
		else if ($type==1){
			$wid_type=1;
			$pad_type=0;
		}
		else if ($type==2){
			$wid_type=1;
			$pad_type=1;
		}
		else if ($type==3){
			$wid_type=1;
			$pad_type=2;
		}
		else if ($type==4){
			$wid_type=2;
			$pad_type=0;
		}
		else if ($type==5){
			$wid_type=2;
			$pad_type=1;
		}
		else if ($type==6){
			$wid_type=2;
			$pad_type=2;
		}
		else if ($type==7){
			$driver=1;
		}
		else if ($type==8){
			$driver=2;
		}
		if ($type==7 or $type==8){
			$default=\App\ProductDefaultOnUser::firstOrNew(['user_id'=>$request->input('user_id'), 'driver'=>$driver]);
			$default->commission=$request->input('product_commission');
			$default->save();
		}
		else if ($type==9){
			$link_summa_rus=$request->input('link_summa_rus');
			if (!$link_summa_rus){
				$link_summa_rus=0;
			}
			$link_summa_cis=$request->input('link_summa_cis');
			if (!$link_summa_cis){
				$link_summa_cis=0;
			}
			$default=\App\UserLinkSumma::firstOrNew(['user_id'=>$request->input('user_id'), 'link_id'=>$request->input('link_id')]);
			$default->summa_rus=$link_summa_rus;
			$default->summa_cis=$link_summa_cis;
			$default->save();
		}
		else{
		$default=\App\VideoDefaultOnUser::firstOrNew(['user_id'=>$request->input('user_id'), 'wid_type'=>$wid_type, 'pad_type'=>$pad_type]);
		$default->commission_rus=$request->input('commission_rus');
		$default->commission_cis=$request->input('commission_cis');
		$default->save();
		}
		return response()->json([
				'ok' => true,
				'message' => 'Комиссии успешно назначены.'
			]);
	}
	public function userDefaultVideoDestroy(Request $request){
		\Auth::user()->touch();
		\App\VideoDefaultOnUser::where('id', $request->input('id'))->delete();
		return back()->with('message_success', "Комиссия успешно удалена.");
	}
	
	public function userDefaultVideoDestroyJs(Request $request){
		\Auth::user()->touch();
		\App\VideoDefaultOnUser::where('id', $request->input('id'))->delete();
		return response()->json([
				'ok' => true,
				'message' => 'Комиссия успешно удалена.'
			]);
	}
	
	public function userDefaultProductDestroy(Request $request){
		\Auth::user()->touch();
		\App\ProductDefaultOnUser::where('id', $request->input('id'))->delete();
		return back()->with('message_success', "Комиссия успешно удалена.");
	}
	
	public function userDefaultProductDestroyJs(Request $request){
		\Auth::user()->touch();
		\App\ProductDefaultOnUser::where('id', $request->input('id'))->delete();
		return response()->json([
				'ok' => true,
				'message' => 'Комиссия успешно удалена.'
			]);
	}
	
	public function userControlSummaDestroy(Request $request){
		\Auth::user()->touch();
		\App\UserLinkSumma::where('id', $request->input('id'))->delete();
		return back()->with('message_success', "Комиссия успешно удалена.");
	}
	
	public function userControlSummaDestroyJs(Request $request){
		\Auth::user()->touch();
		\App\UserLinkSumma::where('id', $request->input('id'))->delete();
		return response()->json([
				'ok' => true,
				'message' => 'Комиссия успешно удалена.'
			]);
	}
	
	public function globalTable(Request $request){
		$search=$request->input('search');
		$category=$request->input('category');
		$pdo=\DB::connection()->getPdo();
		if ($category){
			if ($category==1){
				$sql="create temp table global_table as select t2.email from partner_pads t1 left join (select user_id, email from user_profiles) t2 on t1.user_id=t2.user_id where type='1' or type='3' group by t2.email";
				$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
				$globals=\DB::connection()->table('global_table')->get();
			}
			if ($category==2){
				$sql="create temp table global_table as select t2.email from partner_pads t1 left join (select user_id, email from user_profiles) t2 on t1.user_id=t2.user_id where type='2' or type='3' group by t2.email";
				$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
				$globals=\DB::connection()->table('global_table')->get();
			}
			if ($category=='all'){
				$sql="create temp table global_table as select t2.email from partner_pads t1 left join (select user_id, email from user_profiles) t2 on t1.user_id=t2.user_id group by t2.email";
				$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
				$globals=\DB::connection()->table('global_table')->get();
			}
			return view('admin.global.table2', ['globals'=>$globals, 'category'=>$category, 'search'=>$search]);
		}
		else{
			$sql="create temp table global_table as select cast(t1.user_id as varchar(9)) as user_id , t1.status, t1.dop_status, t1.text_for_dop_status, t1.vip, t1.name, t1.email, t1.manager, array_to_string(array_agg(distinct t2.domain),', ') as 
			domain from user_profiles t1 left join (select id, user_id, domain from partner_pads) t2 on t1.user_id=t2.user_id group by  t1.user_id, t1.status, t1.dop_status, t1.text_for_dop_status, t1.vip, t1.name, 
			t1.email, t1.manager";
			$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
			if ($search){
				if (preg_match("/^[\d\+]+$/",$search)){
					$globals=\DB::connection()->table('global_table')
					->where(function($query) use ($search)
					{
					$query->Where('user_id', trim($search));
					})
					->orderBy('user_id', 'asc')->paginate(30);
				}
				else{
					$globals=\DB::connection()->table('global_table')
					->where(function($query) use ($search)
					{
					$query->where('name', '~*', $search)
					->orWhere('email', '~*', trim($search))
					->orWhere('domain', '~*', trim($search));
					})
					->orderBy('user_id', 'asc')->paginate(30);
				}
			
			}
			else{
			$globals=\DB::connection()->table('global_table')->orderBy('user_id', 'asc')->paginate(30);
			}
			return view('admin.global.table', ['globals'=>$globals, 'category'=>$category, 'search'=>$search]);
		}
	}
	
	public function trashUsers(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		$search=$request->input('search');
		$manager_for_client=$request->input('manager_for_client');
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
			['title'=>"Имя",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Ставка",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"loaded","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
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
		$pdo_precluck=\DB::connection()->getPdo();
		$sqlUsers="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa

		from user_profiles t1 
		left join (select id, pad, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products) t4 on t2.id=t4.wid_id
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa) as summa, sum(loaded) as loaded, sum(calculate) as calculate, sum(played) as played, sum(clicks) as clicks, 
		sum(completed) as completed from 
		pid_summa where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, clicks int, completed int)) t6 on t3.id=t6.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+coalesce(ta_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t7 on t4.wid_id=t7.pid

		group by t1.user_id, t1.balance, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.calculate, t6. clicks, t6.completed, t7.loaded, t7.clicks, t7.summa, t1.referer";
		if (Auth::user()->hasRole('admin')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client){
				if ($manager_for_client=='no_manager'){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', '0')->orWhereNull('manager')->where('status' ,'1')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "referer")
					->orderBy($order,$direct)->paginate(15);
				foreach ($allUsersActive as $q){
					if ($q->user_id=='56'){
						var_dump($q);
					}
				}
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', '0')->orWhereNull('manager')->where('status' ,'1')->first();
				}
				else{
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where('manager', $manager_for_client)->where('status' ,'1')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "referer")
					->orderBy($order,$direct)->paginate(15);
				foreach ($allUsersActive as $q){
					if ($q->user_id=='56'){
						var_dump($q);
					}
				}
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', $manager_for_client)->where('status' ,'1')->first();
				}
			}
			else if ($search){
			$manager_for_client=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})->where('status' ,'1')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "referer")
					->orderBy($order,$direct)->paginate(15);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->where('status' ,'1')->first();
			}
			else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('status' ,'1')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "referer")
				->orderBy($order,$direct)->paginate(15);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('status' ,'1')->first();
			}
		}
		
		else if (Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager')){
			$pdo_precluck->query($sqlUsers, \PDO::FETCH_ASSOC);
			if (!$search and $manager_for_client and Auth::user()->hasRole('super_manager')){
				if ($manager_for_client=='no_manager'){
					$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', '0')->orWhereNull('manager')
				->where('status' ,'1')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain")
				->orderBy($order,$direct)->paginate(15);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', '0')->where('status' ,'1')
				->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})->where('status' ,'1')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain")
					->orderBy($order,$direct)->paginate(15);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->where('status' ,'1')->first();
			}
			else if($search and Auth::user()->hasRole('manager')){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})->where('status' ,'1')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain")
					->orderBy($order,$direct)->paginate(15);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->where('status' ,'1')->first();
			}
			else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->where('status' ,'1')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain")
				->orderBy($order,$direct)->paginate(15);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_calculate)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_calculate)>0) then round(sum(video_played)/sum(video_calculate)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm'))
				->where('manager', Auth::user()->id)
				->where('status' ,'1')->first();
			}
		}
		return view('admin.global.trash_users', ['allUsersActive'=>$allUsersActive, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'header'=>$header, 'order'=>$order, 
		'direct'=>$direct, 'all_sum'=>$all_sum, 'manager_for_client'=>$manager_for_client]);
	}
	
	public function allReferals(){
		$pdo=\DB::connection()->getPdo();
		$sql="create temp table all_refer as  select t1.referer as user_id, coalesce(t2.summa,0) as summa from user_profiles t1 left join (select user_id, sum(summa) as summa from user_referal_transacions group by user_id) t2 on t1.referer=t2.user_id where t1.referer is not null group by t1.referer, t2.summa";
		$pdo->query($sql, \PDO::FETCH_ASSOC);
		$refers=\DB::table('all_refer')->orderBy('summa', 'desc')->paginate(20);
		return view('admin.global.refers', ['refers'=>$refers]);
	}
	
	public function sourceInfo(Request $request){
		if ($request->input('key')!='zaq111'){
			return back()->with('message_danger','Не угадал.');
		}
		$from=$request->input('from');
		$to=$request->input('to');
		if (!($from || $to)){
			$from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
		}
		$name_src=$request->input('name_src');
		if (!$name_src){
		$stats=\DB::connection('videotest')->table('discrepancy')->whereBetween('day', [$from, $to])->orderBy('day', 'desc')->get();
		$all=\DB::connection('videotest')->table('discrepancy')->select(\DB::raw('name_src, sum(discrepancy) as discrepancy, sum(cut) as cut, sum(cut)-sum(discrepancy) as profit'))
		->whereBetween('day', [$from, $to])->groupBy('name_src')->get();
		}
		else{
		$stats=\DB::connection('videotest')->table('discrepancy')->where('name_src', '~*', $name_src)->whereBetween('day', [$from, $to])->orderBy('day', 'desc')->get();
		$all=\DB::connection('videotest')->table('discrepancy')->select(\DB::raw('name_src, sum(discrepancy) as discrepancy, sum(cut) as cut, sum(cut)-sum(discrepancy) as profit'))
		->where('name_src', '~*', $name_src)->whereBetween('day', [$from, $to])->groupBy('name_src')->get();	
		}
		return view('admin.info.source', ['stats'=>$stats, 'all'=>$all, 'from'=>$from, 'to'=>$to, 'name_src'=>$name_src]);
	}
	
	public function sourceInfoKey(){
		return view('admin.info.source_key');
	}
	
	public function sourceInfoGet(){
		return redirect()->route('info_admin.source_info_key');
	}
	

	
}
