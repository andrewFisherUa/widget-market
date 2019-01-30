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

class TestHomeController extends Controller
{

     private $dbUser;
     private $dbPass;
    public function __construct()
    {
         $this->dbUser=env('DB_USERNAME');
         $this->dbPass=env('DB_PASSWORD');
	
        $this->middleware('auth');
    }
	
    public function index($id_user=0, Request $request){

		\Auth::user()->touch();
		$user=Auth::user();
		if ($id_user){
	$user=\App\User::find($id_user)	;
//var_dump($user);
			$user=User::findOrFail($id_user);


		}
		
		if ($user->hasRole('advertiser')){
		#return view('advertiser.cabinet.home', ['user'=>$user, 'userProf'=>null]);
			return view('advertiser.cabinet.admin', ['user'=>$user, 'userProf'=>null]);
			//var_dump(111); die();
		}

		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
        return view('common.cabinet.home', ['user'=>$user]);
	}
	
	public function Profile($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.profile', ['user'=>$user])->render()
		]);
	}
	
	public function Notif($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		$user_notif=\App\AllNotification::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.notif', ['user'=>$user, 'user_notif'=>$user_notif])->render()
		]);
	}
	
	public function removeNotif($id){
		\Auth::user()->touch();
		$notifs=Auth::user()->unreadNotifications->where('type', '<>', 'App\Notifications\NewNews')->where('data', $id);
		foreach ($notifs as $notif){
			$notif->markAsRead();
		}
		return response()->json([
			'ok' => true
		]);
	}
	
	public function News($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		if ($user->hasRole('affiliate')){
			$news_lim=\App\News::where('role', 1)->orderBy('created_at', 'desc')->take(20)->get();
		}
		else if($user->hasRole('advertiser')){
			$news_lim=\App\News::where('role', 2)->orderBy('created_at', 'desc')->take(20)->get();
		}
		else{
			$news_lim=\App\News::orderBy('created_at', 'desc')->take(20)->get();
		}
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.news', ['user'=>$user, 'news_lim'=>$news_lim])->render()
		]);
	}
	
	public function graphVideo(){
		$to_fro_graph=date('Y-m-d H:i:s');
        $from_fro_graph=date('Y-m-d H:i:s',time()-3600*6);
		$to1_fro_graph=date('Y-m-d H:i:s',time()-3600*24);
        $from1_fro_graph=date('Y-m-d H:i:s',time()-3600*30);
		$pdo = \DB::connection('videotest')->getPdo();
		$pgsql="select t1.datetime as t1datetime, t2.datetime as t2datetime, coalesce(t1.cnt,0) as t1cnt, coalesce(t2.cnt,0) as t2cnt from videostatistic_graph t1 left join (select * from videostatistic_graph where id_src='0' and datetime BETWEEN '$from1_fro_graph' and '$to1_fro_graph') t2 on t1.datetime::timestamp::time=t2.datetime::timestamp::time where t1.id_src='0' and t1.datetime BETWEEN '$from_fro_graph' and '$to_fro_graph' and t1.datetime not in (select max(datetime) from videostatistic_graph) order by t1.datetime asc";
		$g_values = $pdo->query($pgsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$graph_y=[];
		$graph_x=[];
		foreach ($g_values as $g_value){
			$graph_y[]=$g_value['t1cnt'];
			$graph_y2[]=$g_value['t2cnt'];
			$graph_x[]=date("Y-m-d H:i:s",strtotime($g_value['t1datetime']));
		}
		$graph=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Запросы сегодня', $graph_y)
		->dataset('Запросы вчера', $graph_y2)
		->labels($graph_x)
		->dimensions(1000,500)
		->width(530)
		->colors(['#000', '#ff0000'])
		->height(311)
		->responsive(false);
		return view('common.cabinet.graph_video', ['graph'=>$graph])->render();
	}
	
	public function graphProduct(){
		$to_fro_graph=date('Y-m-d H:i:s');
        $from_fro_graph=date('Y-m-d H:i:s',time()-3600*6);
		$to1_fro_graph=date('Y-m-d H:i:s',time()-3600*24);
        $from1_fro_graph=date('Y-m-d H:i:s',time()-3600*30);
		
		$nzsql="
select t
,sum(t1showed) as t1showed
,sum(t2showed) as t2showed
,max(datetime) as datetime
from(
select datetime,
datetime::timestamp::time as t,
views as t1showed,
0 as t2showed
from __tmp_graphics
where datetime between '$from_fro_graph' and '$to_fro_graph'
union 
select 
datetime,
datetime::timestamp::time as t,
0 as t1showed,
views as t2showed
 from __tmp_graphics
where datetime between '$from1_fro_graph' and '$to1_fro_graph'
order by datetime desc
) b
group by t

order by max(datetime)
	";
//	var_dump($nzsql); die();
$pdo = \DB::connection('product_next')->getPdo();
		$product_values = $pdo->query($nzsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_y=[];
		$product_x=[];
		$product_y2=[];		
foreach ($product_values as $product_value){
			$product_y[]=$product_value['t1showed'];
			$product_y2[]=$product_value['t2showed'];
			$product_x[]=date("Y-m-d H:i:s",strtotime($product_value['datetime']));
		}

		$graph=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Показы сегодня', $product_y)
		->dataset('Показы вчера', $product_y2)
		->labels($product_x)
		->dimensions(1000,500)
		->width(530)
		->colors(['#000', '#ff0000'])
		->height(127)
		->responsive(false);
		return view('common.cabinet.graph_product', ['graph'=>$graph])->render();		

		
		$prsql="select t1.timegroup as t1timegroup, t2.timegroup as t2timegroup, coalesce(count(t1.url),0) as t1showed, coalesce(t2.url,0) as t2showed from advert_stat_pages t1 left join (select timegroup, count(url) as url from advert_stat_pages where timegroup between '$from1_fro_graph' and '$to1_fro_graph' and char_length(url)>0 and driver in (1,2) group by timegroup) t2 on t1.timegroup::timestamp::time=t2.timegroup::timestamp::time where char_length(t1.url)>0 and t1.timegroup between '$from_fro_graph' and '$to_fro_graph' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) and t1.driver in (1,2) group by t1.timegroup, t2.timegroup, t2.url order by t1.timegroup asc";
		
		
		//$prsql="select t1.timegroup as t1timegroup, coalesce(count(t1.url),0) as t1showed from advert_stat_pages t1 where char_length(t1.url)>0 and t1.timegroup between '$from_fro_graph' and '$to_fro_graph' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) group by t1.timegroup, t2.timegroup, t2.url order by t1.timegroup asc";
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$product_values = $pdo->query($prsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_y=[];
		$product_x=[];
		$product_y2=[];
		foreach ($product_values as $product_value){
			$product_y[]=$product_value['t1showed'];
			$product_y2[]=$product_value['t2showed'];
			$product_x[]=date("Y-m-d H:i:s",strtotime($product_value['t1timegroup']));
		}
		$graph=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Показы сегодня', $product_y)
		->dataset('Показы вчера', $product_y2)
		->labels($product_x)
		->dimensions(1000,500)
		->width(530)
		->colors(['#000', '#ff0000'])
		->height(127)
		->responsive(false);
		return view('common.cabinet.graph_product', ['graph'=>$graph])->render();
	}
	
	public function graphTeaser(){
		$to_fro_graph=date('Y-m-d H:i:s');
                $from_fro_graph=date('Y-m-d H:i:s',time()-3600*6);
		$to1_fro_graph=date('Y-m-d H:i:s',time()-3600*24);
                $from1_fro_graph=date('Y-m-d H:i:s',time()-3600*30);
		$prsql="select t1.timegroup as t1timegroup, t2.timegroup as t2timegroup, coalesce(count(t1.url),0) as t1showed, coalesce(t2.url,0) as t2showed from advert_stat_pages t1 left join (select timegroup, count(url) as url from advert_stat_pages where timegroup between '$from1_fro_graph' and '$to1_fro_graph' and char_length(url)>0 and driver in (1000) group by timegroup) t2 on t1.timegroup::timestamp::time=t2.timegroup::timestamp::time where char_length(t1.url)>0 and t1.timegroup between '$from_fro_graph' and '$to_fro_graph' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) and t1.driver in (1000) group by t1.timegroup, t2.timegroup, t2.url order by t1.timegroup asc";
		//$prsql="select t1.timegroup as t1timegroup, coalesce(count(t1.url),0) as t1showed from advert_stat_pages t1 where char_length(t1.url)>0 and t1.timegroup between '$from_fro_graph' and '$to_fro_graph' and t1.timegroup not in (select max(timegroup) from advert_stat_pages) group by t1.timegroup, t2.timegroup, t2.url order by t1.timegroup asc";
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$product_values = $pdo->query($prsql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_y=[];
		$product_x=[];
		$product_y2=[];
		foreach ($product_values as $product_value){
			$product_y[]=$product_value['t1showed'];
			$product_y2[]=$product_value['t2showed'];
			$product_x[]=date("Y-m-d H:i:s",strtotime($product_value['t1timegroup']));
		}
		$graph=Charts::multi('line', 'morris')
		->title(' ')
		->dataset('Показы сегодня', $product_y)
		->dataset('Показы вчера', $product_y2)
		->labels($product_x)
		->dimensions(1000,500)
		->width(530)
		->colors(['#000', '#ff0000'])
		->height(127)
		->responsive(false);
		return view('common.cabinet.graph_teaser', ['graph'=>$graph])->render();
	}
	
	public function graphClient($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (\Auth::user()->hasRole('affiliate')){
			if ($id_user!=\Auth::user()->id){
				return abort(403);
			}
		}
		$from=date('Y-m-d');
		$pdo = \DB::connection()->getPdo();
		$sql="select t1.user_id, t2.id, t2.domain, sum(coalesce(t6.summa,0)+coalesce(t7.summa,0)) as summa
		from user_profiles t1 left join
		(select id, domain, user_id from partner_pads) t2 on t1.user_id=t2.user_id left join 
		(select id, pad from widgets) t3 on t2.id=t3.pad left join 
		(select id, wid_id from widget_videos) t4 on t3.id=t4.wid_id left join
		(select id, wid_id from widget_products) t5 on t3.id=t5.wid_id left join 
		(select id, wid_id from widget_tizers) t8 on t3.id=t8.wid_id left join 
		(SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(coalesce(summa,0)+coalesce(control_summa,0)) as summa from pid_summa_full 
		where day=''$from'' group by pid') AS p(pid int, summa numeric(18,4))) t6 on t4.id=t6.pid left join 
		(SELECT p.* FROM dblink 
		('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select pid, sum(coalesce(yandex_summa,0)+coalesce(ta_summa,0)+coalesce(ts_summa,0)+coalesce(na_summa,0)) as summa 
		from wid_calculate where day =''$from'' group by pid') AS p(pid int, summa numeric(18,4))) t7 on t5.wid_id=t7.pid or t8.wid_id=t7.pid
		where t1.user_id='$user->id'
		group by t1.user_id, t2.id, t2.domain";
		$graph_charts = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
			$name=[];
			$summa=[];
		foreach ($graph_charts as $g_c){
			$name[]=$g_c['domain'];
			$summa[]=$g_c['summa'];
		}
		
		$graph=Charts::create('donut', 'c3')
		->labels($name)
		->values($summa)
		->dimensions(0,0)
		->height(230)
		->title('')
		->legend(false)
		->responsive(false);
		return view('common.cabinet.graph_client', ['graph'=>$graph])->render();
	}
	
	public function Contacts($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
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
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.contacts', ['user'=>$user, 'manager'=>$manager])->render()
		]);
	}
	
	public function Pads($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		$partnerPads=\App\PartnerPad::where('user_id', $user->id)->get();
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.pads', ['user'=>$user, 'partnerPads'=>$partnerPads])->render()
		]);
	}
	
	public function Widgets($id_user=0, Request $request){ //die();
		$user=Auth::user();
		$id_user=$request->input('id_user');
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (\Auth::user()->hasRole('affiliate')){
			if ($id_user!=\Auth::user()->id){
				return abort(403);
			}
		}
		$partnerWidgets=\App\MPW\Widgets\Widget::where('user_id', $user->id)->where('status', '0')->get();
		$sql="create temp table sum_stat as select t1.id, coalesce(t4.summa,0) as video_summa, 
		coalesce(t4.loaded,0) as video_loaded, coalesce(t4.played,0) as video_played, coalesce(t4.one_played,0) as video_one_played, 
		coalesce(t4.calculate,0) as video_calculate, 
		coalesce(t4.clicks,0) as video_clicks, coalesce(t4.completed,0) as video_completed, coalesce(t4.second,0) as second, 
		coalesce(t4.second_summa,0) as second_summa, coalesce(t4.lease_summa) as lease_summa,
		coalesce(t4.coef,0) as coef,
		coalesce(t4.ads_requested,0) as ads_requested,
		coalesce(t4.ads_viewable,0) as ads_viewable,
		coalesce(t5.loaded,0) as product_played, 
		coalesce(t5.clicks,0) as product_clicks, 
		coalesce(t5.summa,0) as product_summa from widgets t1 
		left join (select id, wid_id from widget_videos) t2 on t1.id=t2.wid_id 
		left join (select id, wid_id from widget_products) t3 on t1.id=t3.wid_id 
		left join (select id, wid_id from widget_tizers) t6 on t1.id=t6.wid_id 
		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
		sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played,
		sum(clicks+control_clicks) as clicks, 
		sum(completed+control_completed) as completed, sum(second_expensive+second_cheap) as second, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa, 
		sum(lease_summa) as lease_summa, 
		round(avg(case when coef > 0 then coef end)::numeric,4) as coef,
		sum(ads_requested) as ads_requested, 
		sum(ads_viewable) as ads_viewable
		from 
		pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, one_played int, clicks int, completed int, second int, second_summa numeric(18,4), lease_summa numeric(18,4), 
		coef numeric(4,2), ads_requested int, ads_viewable int)) t4 on t2.id=t4.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)+coalesce(ts_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+
		coalesce(ta_clicks, 0)+coalesce(ts_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)+coalesce(ts_summa, 0)+coalesce(na_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t5 on t3.wid_id=t5.pid or t6.wid_id=t5.pid where t1.user_id='$user->id'";

		$pdo_precluck=\DB::connection()->getPdo();
		$pdo_precluck->query($sql, \PDO::FETCH_ASSOC);
		$user_all_sum=\DB::table('sum_stat')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(lease_summa),0) as lease_summa,
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, 
				coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable
				'))->first();

				$partnerPads=\App\PartnerPad::where('user_id', $user->id)->get();
		
	
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.widgets', ['user'=>$user, 'partnerWidgets'=>$partnerWidgets, 'from'=>$from, 'to'=>$to, 'user_all_sum'=>$user_all_sum, 'partnerPads'=>$partnerPads])->render()
		]);
	}
	
	public function Balance($id_user=0, Request $request){
		$user=Auth::user();
		$id_user=$request->input('id_user');
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.balance', ['user'=>$user])->render()
		]);
	}
	
	public function HomeUsers(Request $request){
		
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$search=$request->input('search');
		$number=$request->input('number');
		$sorty=$request->input('sorty');
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.home_users', ['from'=>$from, 'to'=>$to, 'sorty'=>$sorty, 'search'=>$search, 'number'=>$number, 'manager'=>$manager])->render()
		]);
	}
	
	public function HomeAllUsers(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$search=$request->input('search');
		$number=$request->input('number');
		$sorty=$request->input('sorty');
		if ($sorty){
			if ($sorty==1){
				$video='(1)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==2){
				$video='(2)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==3){
				$video='(3)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==4){
				$video='(0)';
				$product='(1)';
				$widget='(1)';
			}
			elseif ($sorty==5){
				$video='(0)';
				$product='(2)';
				$widget='(1)';
			}
			elseif ($sorty==6){
				$video='(0)';
				$product='(0)';
				$widget='(3)';
			}
			elseif ($sorty==7){
				$video='(4)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==8){
				$video='(5)';
				$product='(0)';
				$widget='(0)';
			}
		}
		else{
			$video='(1,2,3,4,5)';
			$product='(1,2,3)';
			$widget='(1,2,3)';
		}
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		
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
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
			['title'=>"Б. за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
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
		
		
		$pdo=\DB::connection()->getPdo();
		$sql="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.lease, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.one_played,0) as video_one_played, 
		coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t6.second,0) as second, 
		coalesce(t6.coef,0) as coef, 
		coalesce(t6.ads_requested,0) as ads_requested,
		coalesce(t6.ads_viewable,0) as ads_viewable,
		coalesce(t6.second_summa,0) as second_summa, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa

		from user_profiles t1 
		left join (select id, pad, user_id from widgets where type in $widget) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos where type in $video) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products where driver in $product) t4 on t2.id=t4.wid_id
		
		left join (select id, wid_id from widget_tizers) t8 on t2.id=t8.wid_id
		
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
		sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played,
		sum(clicks+control_clicks) as clicks, 
		sum(completed+control_completed) as completed, sum(second_expensive+second_cheap) as second, sum(second_expensive_summa+second_cheap_summa) as 
		second_summa, avg(case when coef > 0 then coef end) as coef, 
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from 
		pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, one_played int, clicks int, completed int, second int, second_summa numeric(18,4), coef numeric(4,2), 
		ads_requested int, ads_viewable int)) t6 on t3.id=t6.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)+coalesce(ts_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)+
		coalesce(ta_clicks, 0)+coalesce(ts_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)+coalesce(ts_summa, 0)+coalesce(na_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t7 on t4.wid_id=t7.pid or t8.wid_id=t7.pid 

		group by t1.user_id, t1.balance, t1.vip, t1.lease, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.one_played, t6.calculate, t6. clicks, t6.completed, t6.second, t6.coef, t6.second_summa, t6.ads_requested, t6.ads_viewable, t7.loaded, t7.clicks, t7.summa, t1.referer";
//		var_dump($sql); die();
		if (Auth::user()->hasRole('admin')){
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager){
				if ($manager=='no_manager'){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef,
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', '0')->orWhereNull('manager')->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
					->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', $manager)->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->where('manager', $manager)->first();
				}
			}
			else if ($search){
			$manager=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,				
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
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
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager and Auth::user()->hasRole('super_manager')){
				if ($manager=='no_manager'){
					$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', '0')->orWhereNull('manager')
					->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
					->orderBy($order,$direct)->paginate($number);
					$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks,
					 coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', '0')->orWhereNull('manager')
					->first();
					}
				}
			else if ($search and Auth::user()->hasRole('super_manager')){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
				coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
					->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where(function($query) use ($search)
							{
								$query->where('name', '~*', trim($search))
								->orWhere('email', '~*', trim($search))
								->orWhere('domain', '~*', trim($search));
							})->first();
				}
				else if($search and Auth::user()->hasRole('manager')){
					$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', Auth::user()->id)
					->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})
						->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
						->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', Auth::user()->id)
					->where(function($query) use ($search)
							{
								$query->where('name', '~*', trim($search))
								->orWhere('email', '~*', trim($search))
								->orWhere('domain', '~*', trim($search));
							})->first();
				}
				else{
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, lease, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0)+coalesce(sum(product_played)) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', Auth::user()->id)
					->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "lease", "referer")
					->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0)+coalesce(sum(product_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0)+coalesce(sum(product_played)) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0)+coalesce(sum(product_clicks),0) as clicks,
					 coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0 or sum(product_played)>0) then round((coalesce(sum(video_clicks),0)+
					coalesce(sum(product_clicks),0))/(coalesce(sum(video_played),0)+coalesce(sum(product_played),0))::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', Auth::user()->id)
					->first();
				}
			$all_balance=[];
		}
		//return view('common.cabinet.home_all_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager]);
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.home_all_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager])->render()
		]);
	}
	
	public function HomeVideoUsers(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$search=$request->input('search');
		$number=$request->input('number');
		$sorty=$request->input('sorty');
		if ($sorty){
			if ($sorty==1){
				$video='(1)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==2){
				$video='(2)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==3){
				$video='(3)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==4){
				$video='(0)';
				$product='(1)';
				$widget='(1)';
			}
			elseif ($sorty==5){
				$video='(0)';
				$product='(2)';
				$widget='(1)';
			}
			elseif ($sorty==6){
				$video='(0)';
				$product='(0)';
				$widget='(3)';
			}
			elseif ($sorty==7){
				$video='(4)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==8){
				$video='(5)';
				$product='(0)';
				$widget='(0)';
			}
		}
		else{
			$video='(1,2,3,4,5)';
			$product='(1,2,3)';
			$widget='(1,2,3)';
		}
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		
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
			['title'=>"К. ботности",'index'=>"coef","order"=>"",'url'=>""],
			['title'=>"Б. за глубину",'index'=>"second_summa","order"=>"",'url'=>""],
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
		
		
		$pdo=\DB::connection()->getPdo();
		$sql="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.one_played,0) as video_one_played, 
		coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t6.second,0) as second, 
		coalesce(t6.second_summa,0) as second_summa,
		coalesce(t6.coef,0) as coef,
		coalesce(t6.ads_requested,0) as ads_requested,
		coalesce(t6.ads_viewable,0) as ads_viewable

		from user_profiles t1 
		left join (select id, pad, user_id from widgets where type in $widget) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos where type in $video) t3 on t2.id=t3.wid_id
		
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
		sum(played+control_played) as played,
		sum(one_played+control_one_played) as one_played,		
		sum(clicks+control_clicks) as clicks, 
		sum(completed+control_completed) as completed, sum(second_expensive+second_cheap) as second, sum(second_expensive_summa+second_cheap_summa) as 
		second_summa, avg(case when coef > 0 then coef end) as coef, 
		sum(ads_requested) as ads_requested, sum(ads_viewable) as ads_viewable
		from 
		pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, one_played int, clicks int, completed int, second int, second_summa numeric(18,4), coef numeric(4,2), 
		ads_requested int, ads_viewable int)) t6 on t3.id=t6.pid
		where t6.loaded > '0'
		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.one_played, t6.calculate, t6. clicks, t6.completed, t6.second, t6.second_summa, t6.coef, t6.ads_requested, t6.ads_viewable, t1.referer";
		if (Auth::user()->hasRole('admin')){
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager){
				if ($manager=='no_manager'){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					coalesce(sum(video_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
						->where('manager', '0')->orWhereNull('manager')->whereNull('status')
						->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
						->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
							->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
						->where('manager', $manager)->whereNull('status')
						->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
						->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
							->where('manager', $manager)->first();
				}
			}
			else if ($search){
			$manager=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
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
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager and Auth::user()->hasRole('super_manager')){
				if ($manager=='no_manager'){
				$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
					text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', '0')->orWhereNull('manager')
					->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
				$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
					coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
					coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
					case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
					case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
					case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
					case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
					round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
					case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', '0')->orWhereNull('manager')
					->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
						->where(function($query) use ($search)
						{
							$query->where('name', '~*', trim($search))
							->orWhere('email', '~*', trim($search))
							->orWhere('domain', '~*', trim($search));
						})->first();
			}
			else if($search and Auth::user()->hasRole('manager')){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
					->where('manager', Auth::user()->id)
					->where(function($query) use ($search)
					{
						$query->where('name', '~*', trim($search))
						->orWhere('email', '~*', trim($search))
						->orWhere('domain', '~*', trim($search));
					})
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
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
				text_for_dop_status, domain, coalesce(sum(video_summa),0) as summa, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->where('manager', Auth::user()->id)
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(video_summa),0) as summa, 
				coalesce(sum(video_loaded),0) as loaded, coalesce(sum(video_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(video_calculate),0) as calculate, coalesce(sum(video_clicks),0) as clicks, 
				case when(sum(video_loaded)>0) then round(sum(video_one_played)/sum(video_loaded)::numeric,4)*100 else 0 end as util, 
				case when(sum(video_played)>0) then round(coalesce(sum(video_clicks),0)/coalesce(sum(video_played),0)::numeric,4)*100 else 0 end as ctr, 
				case when(sum(video_one_played)>0) then round(sum(video_played)/sum(video_one_played)::numeric,4) else 0 end as deep, 
				case when(sum(video_played)>0) then round(sum(video_completed)/sum(video_played)::numeric,4)*100 else 0 end as dosm,
				round(avg(case when coef > 0 then coef end)::numeric,4) as coef, 
				case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable'))
				->where('manager', Auth::user()->id)
				->first();
			}
			$all_balance=[];
		}
		//return view('common.cabinet.home_all_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager]);
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.home_video_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager])->render()
		]);
	}
	
	public function HomeProductUsers(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$search=$request->input('search');
		$number=$request->input('number');
		$sorty=$request->input('sorty');
		if ($sorty){
			if ($sorty==1){
				$video='(1)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==2){
				$video='(2)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==3){
				$video='(3)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==4){
				$video='(0)';
				$product='(1)';
				$widget='(1)';
			}
			elseif ($sorty==5){
				$video='(0)';
				$product='(2)';
				$widget='(1)';
			}
			elseif ($sorty==6){
				$video='(0)';
				$product='(0)';
				$widget='(3)';
			}
			elseif ($sorty==7){
				$video='(4)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==8){
				$video='(5)';
				$product='(0)';
				$widget='(0)';
			}
		}
		else{
			$video='(1,2,3,4,5)';
			$product='(1,2,3)';
			$widget='(1,2,3)';
		}
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		
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
			['title'=>"Наши клики",'index'=>"our_clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
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
		
		
		$pdo=\DB::connection()->getPdo();
		$sql="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa,
		coalesce(t7.our_clicks,0) as our_clicks

		from user_profiles t1 
		left join (select id, pad, user_id from widgets where type in $widget) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_products where driver in $product) t4 on t2.id=t4.wid_id
		left join (select id, wid_id from widget_tizers) t8 on t2.id=t8.wid_id
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id
		

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)
		+coalesce(ta_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)+coalesce(na_summa, 0)) as summa,
		pid, sum(coalesce(our_clicks, 0)) as our_clicks from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int, our_clicks int)) t7 on t4.wid_id=t7.pid or t7.pid=t8.wid_id
		where t7.loaded > '0'
		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t7.loaded, t7.clicks, t7.summa, t7.our_clicks, t1.referer";
		if (Auth::user()->hasRole('admin')){
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager){
				if ($manager=='no_manager'){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks,
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', '0')->orWhereNull('manager')->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', $manager)->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', $manager)->first();
				}
			}
			else if ($search){
			$manager=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager and Auth::user()->hasRole('super_manager')){
				if ($manager=='no_manager'){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', '0')->orWhereNull('manager')
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', '0')->orWhereNull('manager')
				->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->first();
			}
			$all_balance=[];
		}
		//return view('common.cabinet.home_all_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager]);
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.home_product_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager])->render()
		]);
	}
	
	public function HomeTeaserUsers(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$search=$request->input('search');
		$number=$request->input('number');
		$sorty=$request->input('sorty');
		if ($sorty){
			if ($sorty==1){
				$video='(1)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==2){
				$video='(2)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==3){
				$video='(3)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==4){
				$video='(0)';
				$product='(1)';
				$widget='(1)';
			}
			elseif ($sorty==5){
				$video='(0)';
				$product='(2)';
				$widget='(1)';
			}
			elseif ($sorty==6){
				$video='(0)';
				$product='(0)';
				$widget='(3)';
			}
			elseif ($sorty==7){
				$video='(4)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==8){
				$video='(5)';
				$product='(0)';
				$widget='(0)';
			}
		}
		else{
			$video='(1,2,3,4,5)';
			$product='(1,2,3)';
			$widget='(1,2,3)';
		}
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		
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
			['title'=>"Наши клики",'index'=>"our_clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"util","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
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
		
		
		$pdo=\DB::connection()->getPdo();
		$sql="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa,
		coalesce(t7.our_clicks,0) as our_clicks

		from user_profiles t1 
		left join (select id, pad, user_id from widgets where type in $widget) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_products where driver in $product) t4 on t2.id=t4.wid_id
		left join (select id, wid_id from widget_tizers) t8 on t2.id=t8.wid_id
		
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(ts_views, 0)) as loaded, sum(coalesce(ts_clicks, 0)) as clicks, 
		sum(coalesce(ts_summa, 0)) as summa,
		pid, sum(coalesce(our_clicks, 0)) as our_clicks from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int, our_clicks int)) t7 on t8.wid_id=t7.pid or t7.pid=t4.wid_id
		where t7.loaded > '0'
		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t7.loaded, t7.clicks, t7.summa, t7.our_clicks, t1.referer";
		if (Auth::user()->hasRole('admin')){
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager){
				if ($manager=='no_manager'){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played,
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks,
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', '0')->orWhereNull('manager')->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', $manager)->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', $manager)->first();
				}
			}
			else if ($search){
			$manager=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager and Auth::user()->hasRole('super_manager')){
				if ($manager=='no_manager'){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', '0')->orWhereNull('manager')
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', '0')->orWhereNull('manager')
				->first();
				}
			}
			else if ($search and Auth::user()->hasRole('super_manager')){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played)) as played, 
				coalesce(sum(product_clicks),0) as clicks, 
				coalesce(sum(our_clicks),0) as our_clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->where('manager', Auth::user()->id)
				->first();
			}
			$all_balance=[];
		}
		//return view('common.cabinet.home_all_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager]);
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.home_teaser_users', ['order'=>$order, 'direct'=>$direct, 'all_balance'=>$all_balance, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager])->render()
		]);
	}
	
	public function Message(Request $request){
		$message=$request->input('message');
		$status=$request->input('status');
		return response()->json([
			'ok' => true,
			'view' => view('common.cabinet.home_message', ['status'=>$status, 'message'=>$message])->render()
		]);
	}
	
	public function AlexPage(Request $request){
		if (!\Auth::user()->hasRole('admin')){
			if (\Auth::user()->id!=16){
				return abort(404);
			}
		}
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$search=$request->input('search');
		$number=$request->input('number');
		$sorty=$request->input('sorty');
		if ($sorty){
			if ($sorty==1){
				$video='(1)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==2){
				$video='(2)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==3){
				$video='(3)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==4){
				$video='(0)';
				$product='(1)';
				$widget='(1)';
			}
			elseif ($sorty==5){
				$video='(0)';
				$product='(2)';
				$widget='(1)';
			}
			elseif ($sorty==6){
				$video='(0)';
				$product='(0)';
				$widget='(3)';
			}
			elseif ($sorty==7){
				$video='(4)';
				$product='(0)';
				$widget='(2)';
			}
			elseif ($sorty==8){
				$video='(5)';
				$product='(0)';
				$widget='(0)';
			}
		}
		else{
			$video='(1,2,3)';
			$product='(1,2,3)';
			$widget='(1,2,3)';
		}
		if (!$number){
			$number=20;
		}
		$manager=$request->input('manager');
		
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"summa";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
			['title'=>"Имя",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Ставка",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"calculate","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Выкуп",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Доход",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Зач. глубина",'index'=>"","order"=>"",'url'=>""],
			['title'=>"Бонус за глубину",'index'=>"","order"=>"",'url'=>""],
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
		
		
		$pdo=\DB::connection()->getPdo();
		$sql="create temp table all_webmaster as select t1.user_id, t1.referer, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, 
		t1.text_for_dop_status, array_to_string(array_agg(distinct t5.domain),', ') as domain, coalesce(t6.summa,0) as video_summa, 
		coalesce(t6.loaded,0) as video_loaded, coalesce(t6.played,0) as video_played, coalesce(t6.one_played,0) as video_one_played, 
		coalesce(t6.calculate,0) as video_calculate, 
		coalesce(t6.clicks,0) as video_clicks, coalesce(t6.completed,0) as video_completed, coalesce(t6.second,0) as second, 
		coalesce(t6.second_summa,0) as second_summa, coalesce(t7.loaded,0) as product_played, 
		coalesce(t7.clicks,0) as product_clicks, 
		coalesce(t7.summa,0) as product_summa

		from user_profiles t1 
		left join (select id, pad, user_id from widgets where type in $widget) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos where type in $video) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products where driver in $product) t4 on t2.id=t4.wid_id
		
		left join (select id, wid_id from widget_tizers) t8 on t2.id=t8.wid_id
		
		left join (select id, domain, user_id from partner_pads) t5 on t1.user_id=t5.user_id

		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select pid, sum(summa+control_summa) as summa, sum(loaded+control_loaded) as loaded, sum(calculate+control_calculate) as calculate, 
		sum(played+control_played) as played, 
		sum(one_played+control_one_played) as one_played,
		sum(clicks+control_clicks) as clicks, 
		sum(completed+control_completed) as completed, sum(second_expensive+second_cheap) as second, sum(second_expensive_summa+second_cheap_summa) as second_summa from 
		pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS p(pid int, summa numeric(18,4), loaded int, calculate int, 
		played int, one_played int, clicks int, completed int, second int, second_summa numeric(18,4))) t6 on t3.id=t6.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)+coalesce(ts_views, 0)) as loaded, sum(coalesce(yandex_clicks, 0)
		+coalesce(ta_clicks, 0)+coalesce(ts_clicks, 0)) as clicks, 
		sum(coalesce(yandex_summa, 0)+coalesce(ta_summa, 0)+coalesce(ts_summa, 0)+coalesce(na_summa, 0)) as summa,
		pid from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(loaded int, clicks int, summa numeric(18,4), pid int)) t7 on t4.wid_id=t7.pid or  t8.wid_id=t7.pid 

		group by t1.user_id, t1.balance, t1.vip, t1.name, t1.email, t1.manager, t1.status, t1.dop_status, t1.text_for_dop_status, 
		t6.summa, t6.loaded, t6.played, t6.one_played, t6.calculate, t6. clicks, t6.completed, t6.second, t6.second_summa, t7.loaded, t7.clicks, t7.summa, t1.referer";
			$pdo->query($sql, \PDO::FETCH_ASSOC);
			if (!$search and $manager){
				if ($manager=='no_manager'){
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', '0')->orWhereNull('manager')->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', '0')->orWhereNull('manager')->first();
				}
				else{
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
					->where('manager', $manager)->whereNull('status')
					->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
					->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))->where('manager', $manager)->first();
				}
			}
			else if ($search){
			$manager=0;
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
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
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
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
			$allUsersActive=\DB::table('all_webmaster')->select(\DB::raw('user_id, vip, referer, balance, name, email, manager, status, dop_status, 
				text_for_dop_status, domain, coalesce(sum(product_summa),0) as summa, 
				coalesce(sum(product_played),0) as played, coalesce(sum(second),0) as second, coalesce(sum(second_summa),0) as second_summa,
				coalesce(sum(product_clicks),0) as clicks, 
				case when(sum(product_played)>0) then round(coalesce(sum(product_clicks),0)/coalesce(sum(product_played),0)::numeric,4)*100 else 0 
				end as ctr'))
				->whereNull('status')
				->groupBy("user_id", "balance", "name", "email", "manager", "status", "dop_status", "text_for_dop_status", "domain", "vip", "referer")
				->orderBy($order,$direct)->paginate($number);
			$all_sum=\DB::table('all_webmaster')->select(\DB::raw('coalesce(sum(product_summa),0) as summa, 
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

		return view('admin.global.secret', ['order'=>$order, 'direct'=>$direct, 'header'=>$header, 'stats'=>$allUsersActive, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'manager'=>$manager]);
	}

}
