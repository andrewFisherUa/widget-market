<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \YandexMoney\API;
use Charts;

class LocalBtcController extends Controller
{
	private $lbtckey='78f4d04a4d46fa7948e0cef1ba786f79'; //ключ localbitcoins controller
	private $lbtcsecret='724a6cad404ad79523aa998ffb40cdf6c61775a187f6a6131771947a23a4ceb8'; //секрет localbitcoins controller
	private $qiwi='46c80b272ffc67f5c0738fc912763a14'; //токен qiwi для кошелька 79281131180 действует до 15.08.2018
	//'2260fafba73431387ed8d5c776a322ae'; //токен qiwi для кошелька 79381148114 действует до 08.08.2018
	private $yandex='410011520925164.3EDFC615F1934DC1EEF9DEB06997B3F206E5E35E05B85900DDB02E6A7C0A17BAEAB702437F77AA59FC22FB647573988E6FAE6E75AC2A4E7243CE3259A27B44DCB6095D8AEF0B8C7F062A793F81BBA96915A99284D4185EEE6BEBF330210E13947901DE58B94F69C1976B00FFE9DF13D51C817C7115953295016D74B9EDD15295'; //токен яндекс деньги
	//private $yandex='410011520925164.828062B84FD6997D29F145E6182152FF1C11A2CAC5B004D6FD270A2D72DFE8E38F41AEC4C5D007721C976854CA5AF420ADE572FA69439E8DC7F844A7B4F5F9AB743555D8BB4B5E85B807C8DCB397FB8123595D06F8F06890C257A95CD89B8E2AE887F06F560113F542EF74E219A2806855CD0281D43DC5CD27C1550EB4E33A09'; //токен яндекс деньги
	
	
	public function index(){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$ads=\DB::connection('obmenneg')->table('local_ads')->orderBy('provider', 'asc')->orderBy('valut', 'asc')->get();
		return view('local_btc.index', ['ads'=>$ads]);
	}
	
	public function parse($id, Request $request){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$parser=$request->input('parser');
		$actual=$request->input('actual')?$request->input('actual'):0;
		$position=$request->input('position')?$request->input('position'):1;
		$step=$request->input('step');
		$min=$request->input('min');
		$pr_actual_price=$request->input('pr_actual_price')?$request->input('pr_actual_price'):0;
		$robot=$request->input('robot')?$request->input('robot'):0;
		$min_max_amount=$request->input('min_max_amount')?$request->input('min_max_amount'):0;
		\DB::connection('obmenneg')->table('local_ads')->where('id_ad', $id)->update([
			'parser'=>$parser, 'actual'=>$actual, 'position'=>$position, 'step'=>$step, 'min'=>$min, 'robot'=>$robot, 'pr_actual_price'=>$pr_actual_price, 
			'min_max_amount'=>$min_max_amount]);
		return back();
	}
	
	public function Qiwiv3(Request $request){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select date_trunc('day', t1.created_at) as day, t2.amount as amount_buy, t2.amount_btc as amount_btc_buy, t2.course as course_buy, 
		t2.remainder as remainder_buy, t3.amount as amount_sell, t3.amount_btc as amount_btc_sell, t3.course as course_sell, 
		t3.remainder as remainder_sell, t3.return_course as return_course_sell, t3.profit as profit_sell, t2.profit as profit_buy, 
		t3.profit+t2.profit as profit
		 from local_robots t1 
		left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(remainder) as remainder, 
		sum(case when (profit is not null) then profit else 0 end) as profit, 
		date_trunc('day', created) as day from local_balancing where id_ad='609849' and date_trunc('day', created) between '$from' and '$to' 
		group by id_ad, date_trunc('day', created)) 
		t2 on date_trunc('day', t1.created_at)=t2.day 
		left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(remainder) as remainder, 
		avg(return_course) as return_course, sum(case when (profit is not null) then profit else 0 end) as profit,
		date_trunc('day', created) as day from local_balancing where id_ad='617372' and date_trunc('day', created) between '$from' and '$to' 
		group by id_ad, date_trunc('day', created)) 
		t3 on date_trunc('day', t1.created_at)=t3.day 
		where t1.id_ad in ('609849', '617372') and status='9' and date_trunc('day', t1.created_at) between '$from' and '$to' 
		group by date_trunc('day', t1.created_at), t2.amount, t2.amount_btc, t2.course, t2.remainder, t2.profit,
		t3.amount, t3.amount_btc, t3.course, t3.remainder, t3.return_course, t3.profit order by date_trunc('day', t1.created_at) desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(t1.amount) as amount_buy, sum(t1.amount_btc) as amount_btc_buy, avg(t1.course_fact) as course_buy, sum(t1.remainder) as remainder_buy, 
		t2.amount as amount_sell, t2.amount_btc as amount_btc_sell, t2.course as course_sell, t2.remainder as remainder_sell, 
		t2.return_course as return_course_sell, sum(t1.profit) as profit_buy, t2.profit as profit_sell, sum(t1.profit)+t2.profit as profit from local_balancing t1 
		left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(remainder) as remainder, 
		avg(return_course) as return_course, sum(profit) as profit
		from local_balancing where id_ad='617372' and date_trunc('day', created) between '$from' and '$to' 
		group by id_ad) t2 on 1=1
		where t1.id_ad='609849' and date_trunc('day', t1.created) between '$from' and '$to' group by t2.amount, 
		t2.amount_btc, t2.course, t2.remainder, t2.return_course, t2.profit";
		$all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('local_btc.qiwi.transactions_days', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'all'=>$all]);
	}
	
	public function Qiwiv3Detail($date){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select * from local_balancing where id_ad='609849' and date_trunc('day', created)='$date' order by created desc";
		$buy_stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(profit) as profit 
		from local_balancing where id_ad='609849' and date_trunc('day', created)='$date'";
		$buy_stats_all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		
		$sql="select * from local_balancing where id_ad='617372' and date_trunc('day', created)='$date' order by created desc";
		$sell_stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, 
		avg(return_course) as return_course, sum(profit) as profit from local_balancing where id_ad='617372' and date_trunc('day', created)='$date'";
		$sell_stats_all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('local_btc.qiwi.transactions_days_detail', ['date'=>$date, 'buy_stats'=>$buy_stats, 'buy_stats_all'=>$buy_stats_all, 'sell_stats'=>$sell_stats, 'sell_stats_all'=>$sell_stats_all]);
	}
	
	public function Yandexv3(Request $request){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select date_trunc('day', t1.created_at) as day, t2.amount as amount_buy, t2.amount_btc as amount_btc_buy, t2.course as course_buy, 
		t2.remainder as remainder_buy, t3.amount as amount_sell, t3.amount_btc as amount_btc_sell, t3.course as course_sell, 
		t3.remainder as remainder_sell, t3.return_course as return_course_sell, t3.profit as profit_sell, t2.profit as profit_buy, 
		t3.profit+t2.profit as profit
		 from local_robots t1 
		left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(remainder) as remainder, 
		sum(case when (profit is not null) then profit else 0 end) as profit, 
		date_trunc('day', created) as day from local_balancing where id_ad='609928' and date_trunc('day', created) between '$from' and '$to' 
		group by id_ad, date_trunc('day', created)) 
		t2 on date_trunc('day', t1.created_at)=t2.day 
		left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(remainder) as remainder, 
		avg(return_course) as return_course, sum(case when (profit is not null) then profit else 0 end) as profit,
		date_trunc('day', created) as day from local_balancing where id_ad='609305' and date_trunc('day', created) between '$from' and '$to' 
		group by id_ad, date_trunc('day', created)) 
		t3 on date_trunc('day', t1.created_at)=t3.day 
		where t1.id_ad in ('609928', '609305') and status='9' and date_trunc('day', t1.created_at) between '$from' and '$to' 
		group by date_trunc('day', t1.created_at), t2.amount, t2.amount_btc, t2.course, t2.remainder, t2.profit, 
		t3.amount, t3.amount_btc, t3.course, t3.remainder, t3.return_course, t3.profit order by date_trunc('day', t1.created_at) asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(t1.amount) as amount_buy, sum(t1.amount_btc) as amount_btc_buy, avg(t1.course_fact) as course_buy, sum(t1.remainder) as remainder_buy, 
		t2.amount as amount_sell, t2.amount_btc as amount_btc_sell, t2.course as course_sell, t2.remainder as remainder_sell, 
		t2.return_course as return_course_sell, sum(t1.profit) as profit_buy, t2.profit as profit_sell, sum(t1.profit)+t2.profit as profit from local_balancing t1 
		left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(remainder) as remainder, 
		avg(return_course) as return_course, sum(profit) as profit
		from local_balancing where id_ad='609305' and date_trunc('day', created) between '$from' and '$to' 
		group by id_ad) t2 on 1=1
		where t1.id_ad='609928' and date_trunc('day', t1.created) between '$from' and '$to' group by t2.amount, 
		t2.amount_btc, t2.course, t2.remainder, t2.return_course, t2.profit";
		$all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('local_btc.yandex.transactions_days', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'all'=>$all]);
	}
	
	public function Yandexv3Detail($date){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select * from local_balancing where id_ad='609928' and date_trunc('day', created)='$date' order by created desc";
		$buy_stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, sum(profit) as profit 
		from local_balancing where id_ad='609928' and date_trunc('day', created)='$date'";
		$buy_stats_all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		
		$sql="select * from local_balancing where id_ad='609305' and date_trunc('day', created)='$date' order by created desc";
		$sell_stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course_fact) as course, 
		avg(return_course) as return_course, sum(profit) as profit from local_balancing where id_ad='609305' and date_trunc('day', created)='$date'";
		$sell_stats_all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('local_btc.yandex.transactions_days_detail', ['date'=>$date, 'buy_stats'=>$buy_stats, 'buy_stats_all'=>$buy_stats_all, 'sell_stats'=>$sell_stats, 'sell_stats_all'=>$sell_stats_all]);
	}
	
	public function QiwiDisabled(){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$qiwis=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609849')->where('status', '0')->orderBy('created', 'desc')->get();
		return view('local_btc.qiwi.disabled', ['qiwis'=>$qiwis]);
	}
	
	public function QiwiDisabledPost(Request $request){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$statuss=$request->input('status');
		foreach ($statuss as $status){
			\DB::connection('obmenneg')->table('local_balancing')->where('id', $status)->update(['status'=>'2', 'logs'=>\Auth::user()->name]);
		}
		return back();
	}
	
	public function QiwiDisabledInfo(){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$qiwis=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609849')->where('status', '2')->orderBy('created', 'desc')->get();
		return view('local_btc.qiwi.disabled_info', ['qiwis'=>$qiwis]);
	}
	
	public function YandexDisabled(){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$yandexs=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609928')->where('status', '0')->orderBy('created', 'desc')->get();
		return view('local_btc.yandex.disabled', ['yandexs'=>$yandexs]);
	}
	
	public function YandexDisabledPost(Request $request){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$statuss=$request->input('status');
		foreach ($statuss as $status){
			\DB::connection('obmenneg')->table('local_balancing')->where('id', $status)->update(['status'=>'2', 'logs'=>\Auth::user()->name]);
		}
		return back();
	}
	
	public function YandexDisabledInfo(){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		$yandexs=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609928')->where('status', '2')->orderBy('created', 'desc')->get();
		return view('local_btc.yandex.disabled_info', ['yandexs'=>$yandexs]);
	}
	
	public function birges(){
		$btcs=\DB::connection('obmenneg')->table('table_btc')->orderBy('id', 'asc')->get();
		$eths=\DB::connection('obmenneg')->table('table_eth')->orderBy('id', 'asc')->get();
		$birges=\DB::connection('obmenneg')->table('table_birges')->orderBy('id', 'asc')->get();
		return view('local_btc.birges', ['btcs'=>$btcs, 'eths'=>$eths, 'birges'=>$birges]);
	}
	
	public function crypto(){
		if (\Auth::user()->id==933){
			return abort(403);
		}
		/*$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select name,usd,cast(btc as numeric(18,8)) from criptos order by cast(btc as numeric(18,8)) desc";
		$alls=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		$sql="select sum(cast(btc as numeric(18,8))) as sum from criptos";
		$sum=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('local_btc.cripto', ['alls'=>$alls, 'sum'=>$sum]);*/
		$coins=\DB::connection('crypto')->table('coins')->orderBy('id', 'asc')->get();
		return view('local_btc.crypto', ['coins'=>$coins]);
	}
	
	public function Graph(Request $request){
		$period=$request->input('period')?$request->input('period'):12;
		$to=date('Y-m-d H:i:s');
        $from=date('Y-m-d H:i:s',time()-3600*$period);
		$po=$request->input('po')?$request->input('po'):30;
		$pdo = \DB::connection("obmenneg")->getPdo();
		if ($po==5){
			$pp="'0','5','10','15','20','25','30','35','40','45','50','55'";
		}
		if ($po==30){
			$pp="'0','30'";
		}
		//$sql="select name, type, rub, datetime as datetimes from table_graf where datetime between '$from' and '$to' order by datetimes,type";
		$sql="select name, type, rub, datetime from table_graf where datetime between '$from' and '$to' and date_part('minute', datetime) in ($pp) group by name,type,rub,datetime order by datetime,type";
		$graph_values = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$graph_x=[];
		foreach ($graph_values as $g_v){
			$graph_x[date("m-d H:i",strtotime($g_v['datetime']))][$g_v['name']]=$g_v['rub'];
			$graph_y[$g_v['name']][date("m-d H:i",strtotime($g_v['datetime']))]=$g_v['rub'];
		}
		$graph_xx=[];
		$graph=Charts::multi('line', 'morris');
		foreach ($graph_y as $k=>$g){
			$graph->dataset($k, array_values($g));
		}
		$graph->title(" ");
		$graph->height(400);
		$graph->labels(array_keys($graph_x));
		return view('local_btc.graph', ['period'=>$period, 'graph'=>$graph]);
	}
}
