<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MoneyReport\Valute;
use App\MoneyReport\Account;
use App\MoneyReport\Report;
use App\MoneyReport\ReportAccount;
use App\MoneyReport\ReportOperation;
use App\MoneyReport\AccountBalancing;
use App\MoneyReport\TypeOperation;
use Auth;

class MoneyReportController extends Controller
{
	
	public function index(){
		$accounts=Account::all();
		//foreach($accounts as $s){
			//var_dump($s); echo "<hr>";
		//}
		//die();
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		$types=TypeOperation::all();
		$spors=\DB::connection('obmenneg')->table('local_robots')->where('status', 10)->get();
		return view('money_report.index', ['accounts'=>$accounts, 'report'=>$report, 'cources'=>$cources, 'types'=>$types, 'spors'=>$spors]);
	}
	
	public function addValute(Request $request){
		$type=new Valute;
		$type->title=strtolower($request->input('title'));
		$type->shortcode=strtolower($request->input('shortcode'));
		$type->save();
		return back()->with('message_success','Валюта успешно добавлена.');
	}
	
	public function addAccounte(Request $request){
		$summa=str_replace(',','.',$request->input('summa'));
		if ($request->input('valut')=='no'){
			return back()->with('message_danger','Не указана валюта для счета.');
		}
		$account=new Account;
		$account->title=$request->input('title');
		$account->shortcode=$request->input('valut');
		$account->card=$request->input('card')?$request->input('card'):0;
		$account->save();
		$balance=new AccountBalancing;
		$balance->account_id=$account->id;
		$balance->summa=$summa;
		$balance->save();
		return back()->with('message_success','Система/счет создана и успешно сохранена.');
	}
	
	public function addOperation(Request $request){
		$report=Report::findOrFail($request->input('reports_id'));
		$account=Account::findOrFail($request->input('accounts_id'));
		$type=$request->input('type');
		$datetime=$request->input('datetime');
		$summa=$request->input('summa');
		$shortcode=$request->input('shortcode');
		$comment=$request->input('comment');
		$obnal=$request->input('obnal');
		$course=\DB::connection('report')->table('courses')->orderBy('datetime','desc')->first();
		foreach ($shortcode as $k=>$sh){
			if ($sh=='no' and $summa[$k]!=0){
				return back()->with('message_danger','Для одной из записей не задан тип.');
			}
		}
		ReportOperation::where('reports_id', $report->id)->where('accounts_id', $account->id)->where('type', $type)->delete();
		foreach ($datetime as $k=>$date){
			if ($summa[$k]==0){
				continue;
			}
			$cource=\DB::connection('report')->table('courses_tmp')->where('datetime', '<=', $date)->orderBy('datetime', 'desc')->first();
			if (!$cource){
				$cource=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($date)))->first();
			}
			$operat=new ReportOperation;
			$operat->reports_id=$report->id;
			$operat->accounts_id=$account->id;
			$operat->type=$type;
			$operat->shortcode=$shortcode[$k];
			$operat->summa=$summa[$k];
			$operat->comment=$comment[$k];
			$operat->datetime=$date;
			$operat->obnal=$obnal[$k]?$obnal[$k]:0;
			if ($account->shortcode=='rub'){
				$operat->cources=1;
			}
			elseif ($account->shortcode=='usd'){
				$operat->cources=$cource->usd;
			}
			elseif ($account->shortcode=='btc'){
				$operat->cources=$cource->btc;
			}
			elseif ($account->shortcode=='eth'){
				$operat->cources=$cource->eth;
			}
			elseif ($account->shortcode=='ltc'){
				$operat->cources=$cource->ltc;
			}
			elseif ($account->shortcode=='uah'){
				$operat->cources=$cource->uah;
			}
			elseif ($account->shortcode=='eur'){
				$operat->cources=$cource->eur;
			}
			$operat->save();
		}
		$rAccounts=ReportAccount::where('report_id', $report->id)->get();
		foreach ($rAccounts as $rAccount){
			$rAccount->summa_closed=$rAccount->summa_opened;
			$plus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 1)->sum('summa');
			$rAccount->summa_closed+=$plus;
			$minus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 2)->sum('summa');
			$rAccount->summa_closed-=$minus;
			$rAccount->save();
		}
		$report->summaClosed();
		$report->save();
		$accountOperations=ReportAccount::where('report_id', $report->id)->get();
		foreach ($accountOperations as $operation){
			$accounts=Account::where('id', $operation->account_id)->first();
			$accounts->summa->summa=$operation->summa_closed;
			$accounts->summa->save();
		}
		return back()->with('message_success',"Данные отчета по {$account->title} успешно измненены.");
	}
	
	public function ReportAccount($report_id, $account_id, Request $request){
		$type=$request->input('type');
		if (!$type){
			$type="12";
		}
		$typee=str_split($type);
		$shortcode=$request->input('shortcode');
		$typesMenu=TypeOperation::select(\DB::raw('title, shortcode'))->groupBy('shortcode','title')->get();
		$shortcodes=array();
		if (!$shortcode){
			foreach ($typesMenu as $tt){
				array_push($shortcodes, $tt->shortcode);
			}
		}
		else{
			array_push($shortcodes, $shortcode);
		}
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$rep=Report::find($report_id);
		if ($rep->closed && $report_id == 111){
			$spec_cources=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($rep->closed)))->first();
		}
		else{
			$spec_cources=$cources;
		}
		$types=TypeOperation::all();
		
		$account=Account::findOrFail($account_id);
		$operations=ReportOperation::where('reports_id', $report_id)->where('accounts_id', $account_id)->whereIn('type', $typee)->whereIn('shortcode', $shortcodes)->orderBy('datetime', 'desc')->get();
		$balance=ReportAccount::where('report_id', $report_id)->where('account_id', $account_id)->first();
		$sum=ReportOperation::select(\DB::raw('shortcode, sum(case when type=1 then summa else -summa end) as summa'))->
		where('reports_id', $report_id)->where('accounts_id', $account_id)->whereIn('type', $typee)->whereIn('shortcode', $shortcodes)->groupBy('shortcode')->first();
		return view('money_report.operations', ['sum'=>$sum, 'operations'=>$operations, 'rep'=>$rep, 'spec_cources'=>$spec_cources, 'balance'=>$balance, 'cources'=>$cources, 'shortcode'=>$shortcode, 'typesMenu'=>$typesMenu, 'type'=>$type, 'report'=>$report, 'account'=>$account, 'types'=>$types]);
	}
	
	public function reports(Request $request){

		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$fromW=date("Y-m-d 23:59:59", strtotime($from));
		$toW=date("Y-m-d 23:59:59", strtotime($to));
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$types=TypeOperation::all();
		$reports=Report::whereBetween('opened', [$fromW, $toW])->orWhereBetween('closed', [$fromW, $toW])->orderBy('opened', 'desc')->get();
		$accounts=Account::all();

#foreach($accounts as $c){
#  var_dump($c->valuts->status);
#  echo "<hr>";
#}
return view('money_report.reports', ['from'=>$from, 'to'=>$to, 'reports'=>$reports, 'report'=>$report, 'cources'=>$cources, 'types'=>$types, 'accounts'=>$accounts]);
	}
	
	public function reportOpertaion($id, Request $request){
		$type=$request->input('type');
		if (!$type){
			$type="12";
		}
		$typee=str_split($type);
		$shortcode=$request->input('shortcode');
		$typesMenu=TypeOperation::select(\DB::raw('title, shortcode'))->groupBy('shortcode','title')->get();
		$shortcodes=array();
		if (!$shortcode){
			foreach ($typesMenu as $tt){
				array_push($shortcodes, $tt->shortcode);
			}
		}
		else{
			array_push($shortcodes, $shortcode);
		}
		if ($shortcode=='rbtobm'){
			array_push($shortcodes, 'rbt');
			array_push($shortcodes, 'obm');
		}
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$rep=Report::find($id);
		if ($rep->closed){
			$date=date("Y-m-d", strtotime($rep->closed));
			$spec_cources=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($rep->closed)))->first();
		}
		else{
			$spec_cources=$cources;
			$date=date("Y-m-d");
		}
		$types=TypeOperation::all();
		
		$pdo = \DB::connection('report')->getPdo();
		if ($shortcode=='rbt' or $shortcode=='obm'){
		$sql="create temp table opeartion as select t1.type, 
			t1.shortcode, sum(case when t2.shortcode='rub' then t1.summa 
			when t2.shortcode='usd' then t1.summa*t3.usd 
			when t2.shortcode='btc' then t1.summa*t3.btc 
			when t2.shortcode='eth' then t1.summa*t3.eth 
			when t2.shortcode='ltc' then t1.summa*t3.ltc 
			when t2.shortcode='uah' then t1.summa*t3.uah
			when t2.shortcode='eur' then t1.summa*t3.eur
			else 0 end) as summa 
			from report_operations t1 
			left join (select * from accounts) t2 on t1.accounts_id=t2.id 
			left join (select * from courses where date='$date' order by datetime desc limit 1) t3 on 1=1
			where t1.reports_id='$id' group by t1.type, t1.shortcode";
		
		}
		else{
			$sql="create temp table opeartion as select t1.type, 
		case when t1.shortcode='rbt' then 'obm' else t1.shortcode end as shortcode, sum(case when t2.shortcode='rub' then t1.summa 
		when t2.shortcode='usd' then t1.summa*t3.usd 
		when t2.shortcode='btc' then t1.summa*t3.btc 
		when t2.shortcode='eth' then t1.summa*t3.eth 
		when t2.shortcode='ltc' then t1.summa*t3.ltc 
		when t2.shortcode='uah' then t1.summa*t3.uah
		when t2.shortcode='eur' then t1.summa*t3.eur
		else 0 end) as summa 
		from report_operations t1 
		left join (select * from accounts) t2 on t1.accounts_id=t2.id 
		left join (select * from courses where date='$date' order by datetime desc limit 1) t3 on 1=1
		where t1.reports_id='$id' group by t1.type, t1.shortcode";
		}
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		

		$operations=\DB::connection('report')->table('opeartion')->select(\DB::raw('type, shortcode, sum(summa) as summa'))->whereIn('type', $typee)->

		whereIn('shortcode', $shortcodes)->groupBy('shortcode','type')->get();
		$sum=\DB::connection('report')->table('opeartion')->select(\DB::raw('sum(case when type=1 then summa else -summa end) as summa'))->whereIn('type', $typee)->
		whereIn('shortcode', $shortcodes)->first();
		
		$sql="create temp table cachs as select t1.type, t4.title, t4.shortcode as valut,
		case when t1.shortcode='rbt' then 'obm' else t1.shortcode end as shortcode, sum(case when t2.shortcode='rub' then t1.summa 
		when t2.shortcode='usd' then t1.summa*t3.usd 
		when t2.shortcode='btc' then t1.summa*t3.btc 
		when t2.shortcode='eth' then t1.summa*t3.eth 
		when t2.shortcode='ltc' then t1.summa*t3.ltc 
		when t2.shortcode='uah' then t1.summa*t3.uah
		when t2.shortcode='eur' then t1.summa*t3.eur
		else 0 end) as summa 
		from report_operations t1 
		left join (select * from accounts) t2 on t1.accounts_id=t2.id 
		left join (select * from courses where date='$date' order by datetime desc limit 1) t3 on 1=1 
		left join (select * from accounts) t4 on t1.accounts_id=t4.id
		where t1.reports_id='$id' group by t1.type, t4.title, t4.shortcode, t1.shortcode";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$cachs=\DB::connection('report')->table('cachs')->select(\DB::raw('type, title, shortcode, valut, sum(summa) as summa'))->
		groupBy('type','title', 'shortcode', 'valut')->get();
		return view('money_report.report_operations', ['sum'=>$sum, 'cachs'=>$cachs,'operations'=>$operations, 'rep'=>$rep, 'spec_cources'=>$spec_cources, 'cources'=>$cources, 'shortcode'=>$shortcode, 'typesMenu'=>$typesMenu, 'type'=>$type, 'report'=>$report, 'types'=>$types]);
	}
	
	public function reportOpertaionAccounts($id, Request $request){
		$type=$request->input('type');
		if (!$type){
			$type="12";
		}
		$typee=str_split($type);
		$shortcode=$request->input('shortcode');
		$typesMenu=TypeOperation::select(\DB::raw('title, shortcode'))->groupBy('shortcode','title')->get();
		$shortcodes=array();
		if (!$shortcode){
			foreach ($typesMenu as $tt){
				array_push($shortcodes, $tt->shortcode);
			}
		}
		else{
			array_push($shortcodes, $shortcode);
		}
		if ($shortcode=='rbtobm'){
			array_push($shortcodes, 'rbt');
			array_push($shortcodes, 'obm');
		}
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$rep=Report::find($id);
		if ($rep->closed){
			$date=date("Y-m-d", strtotime($rep->closed));
			$spec_cources=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($rep->closed)))->first();
		}
		else{
			$spec_cources=$cources;
			$date=date("Y-m-d");
		}
		$types=TypeOperation::all();
		
		$pdo = \DB::connection('report')->getPdo();
		if ($shortcode=='rbt' or $shortcode=='obm'){
		$sql="create temp table opeartion as select t1.type, 
			t1.shortcode, sum(case when t2.shortcode='rub' then t1.summa 
			when t2.shortcode='usd' then t1.summa*t3.usd 
			when t2.shortcode='btc' then t1.summa*t3.btc 
			when t2.shortcode='eth' then t1.summa*t3.eth 
			when t2.shortcode='ltc' then t1.summa*t3.ltc 
			when t2.shortcode='uah' then t1.summa*t3.uah
			when t2.shortcode='eur' then t1.summa*t3.eur
			else 0 end) as summa 
			from report_operations t1 
			left join (select * from accounts) t2 on t1.accounts_id=t2.id 
			left join (select * from courses where date='$date' order by datetime desc limit 1) t3 on 1=1
			where t1.reports_id='$id' group by t1.type, t1.shortcode";
		
		}
		else{
			$sql="create temp table opeartion as select t1.type, 
		case when t1.shortcode='rbt' then 'obm' else t1.shortcode end as shortcode, sum(case when t2.shortcode='rub' then t1.summa 
		when t2.shortcode='usd' then t1.summa*t3.usd 
		when t2.shortcode='btc' then t1.summa*t3.btc 
		when t2.shortcode='eth' then t1.summa*t3.eth 
		when t2.shortcode='ltc' then t1.summa*t3.ltc 
		when t2.shortcode='uah' then t1.summa*t3.uah
		when t2.shortcode='eur' then t1.summa*t3.eur
		else 0 end) as summa 
		from report_operations t1 
		left join (select * from accounts) t2 on t1.accounts_id=t2.id 
		left join (select * from courses where date='$date' order by datetime desc limit 1) t3 on 1=1
		where t1.reports_id='$id' group by t1.type, t1.shortcode";
		}
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$operations=ReportOperation::where('reports_id', $id)->whereIn('type', $typee)->whereIn('shortcode', $shortcodes)->orderBy('datetime', 'desc')->get();

		$sum=\DB::connection('report')->table('opeartion')->select(\DB::raw('sum(case when type=1 then summa else -summa end) as summa'))->whereIn('type', $typee)->
		whereIn('shortcode', $shortcodes)->first();
		return view('money_report.report_operations_accounts', ['sum'=>$sum, 'operations'=>$operations, 'rep'=>$rep, 'spec_cources'=>$spec_cources, 'cources'=>$cources, 'shortcode'=>$shortcode, 'typesMenu'=>$typesMenu, 'type'=>$type, 'report'=>$report, 'types'=>$types]);
	}
	
	public function reportTimeDetail(Request $request){
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$from=$request->input('from');
		$to=$request->input('to');
		if (!$from or !$to){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
		}
		$froms=date("Y-m-d 00:00:00", strtotime($from));
		$tos=date("Y-m-d 23:59:59", strtotime($to));
		$type=$request->input('type');
		if (!$type){
			$type="12";
		}
		$typee=str_split($type);
		$shortcode=$request->input('shortcode');
		$typesMenu=TypeOperation::select(\DB::raw('title, shortcode'))->groupBy('shortcode','title')->get();
		$shortcodes=array();
		if (!$shortcode){
			foreach ($typesMenu as $tt){
				array_push($shortcodes, $tt->shortcode);
			}
		}
		else{
			array_push($shortcodes, $shortcode);
		}
		if ($shortcode=='rbtobm'){
			array_push($shortcodes, 'rbt');
			array_push($shortcodes, 'obm');
		}

		$types=TypeOperation::all();
		$spec_cources=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($to)))->first();
		$operations=ReportOperation::whereBetween('datetime', [$froms, $tos])->whereIn('type', $typee)->whereIn('shortcode', $shortcodes)->orderBy('datetime', 'desc')->get();
		return view('money_report.report_operations_time_detail', ['operations'=>$operations, 'from'=>$from, 'to'=>$to, 'type'=>$type, 'typesMenu'=>$typesMenu, 
		'spec_cources'=>$spec_cources, 'report'=>$report, 'cources'=>$cources, 'shortcode'=>$shortcode, 'types'=>$types]);
	}
	
	public function reportTime(Request $request){
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$from=$request->input('from');
		$to=$request->input('to');
		if (!$from or !$to){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
		}
		$froms=date("Y-m-d 00:00:00", strtotime($from));
		$tos=date("Y-m-d 23:59:59", strtotime($to));
		$type=$request->input('type');
		if (!$type){
			$type="12";
		}
		$typee=str_split($type);
		$shortcode=$request->input('shortcode');
		$typesMenu=TypeOperation::select(\DB::raw('title, shortcode'))->groupBy('shortcode','title')->get();
		$shortcodes=array();
		if (!$shortcode){
			foreach ($typesMenu as $tt){
				array_push($shortcodes, $tt->shortcode);
			}
		}
		else{
			array_push($shortcodes, $shortcode);
		}
		if ($shortcode=='rbtobm'){
			array_push($shortcodes, 'rbt');
			array_push($shortcodes, 'obm');
		}
		$types=TypeOperation::all();
		$spec_cources=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($to)))->first();
		$pdo = \DB::connection('report')->getPdo();
		if ($shortcode=='rbt' or $shortcode=='obm'){
		$sql="create temp table opeartion as select t1.type, 
			t1.shortcode, sum(case when t2.shortcode='rub' then t1.summa*t1.cources 
			when t2.shortcode='usd' then t1.summa*t1.cources
			when t2.shortcode='btc' then t1.summa*t1.cources
			when t2.shortcode='eth' then t1.summa*t1.cources
			when t2.shortcode='ltc' then t1.summa*t1.cources
			when t2.shortcode='uah' then t1.summa*t1.cources
			when t2.shortcode='eur' then t1.summa*t1.cources
			else 0 end) as summa 
			from report_operations t1 
			left join (select * from accounts) t2 on t1.accounts_id=t2.id 
			left join (select * from courses where date='$to' order by datetime desc limit 1) t3 on 1=1
			where t1.datetime between '$froms' and '$tos' group by t1.type, t1.shortcode";
		
		}
		else{
			$sql="create temp table opeartion as select t1.type, 
		case when t1.shortcode='rbt' then 'obm' else t1.shortcode end as shortcode, sum(case when t2.shortcode='rub' then t1.summa 
		when t2.shortcode='usd' then t1.summa*t1.cources
		when t2.shortcode='btc' then t1.summa*t1.cources
		when t2.shortcode='eth' then t1.summa*t1.cources
		when t2.shortcode='ltc' then t1.summa*t1.cources
		when t2.shortcode='uah' then t1.summa*t1.cources
		when t2.shortcode='eur' then t1.summa*t1.cources
		else 0 end) as summa 
		from report_operations t1 
		left join (select * from accounts) t2 on t1.accounts_id=t2.id 
		left join (select * from courses where date='$to' order by datetime desc limit 1) t3 on 1=1
		where t1.datetime between '$froms' and '$tos' group by t1.type, t1.shortcode";
		}
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$operations=\DB::connection('report')->table('opeartion')->select(\DB::raw('type, shortcode, sum(summa) as summa'))->whereIn('type', $typee)->
		whereIn('shortcode', $shortcodes)->groupBy('shortcode','type')->get();
		$sql="create temp table cachs as select t1.type, t4.title, t4.shortcode as valut,
		case when t1.shortcode='rbt' then 'obm' else t1.shortcode end as shortcode, sum(case when t2.shortcode='rub' then t1.summa 
		when t2.shortcode='usd' then t1.summa*t1.cources
		when t2.shortcode='btc' then t1.summa*t1.cources
		when t2.shortcode='eth' then t1.summa*t1.cources
		when t2.shortcode='ltc' then t1.summa*t1.cources
		when t2.shortcode='uah' then t1.summa*t1.cources
		when t2.shortcode='eur' then t1.summa*t1.cources
		else 0 end) as summa 
		from report_operations t1 
		left join (select * from accounts) t2 on t1.accounts_id=t2.id 
		left join (select * from courses where date='$to' order by datetime desc limit 1) t3 on 1=1 
		left join (select * from accounts) t4 on t1.accounts_id=t4.id
		where t1.datetime between '$froms' and '$tos' group by t1.type, t4.title, t4.shortcode, t1.shortcode";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$cachs=\DB::connection('report')->table('cachs')->select(\DB::raw('type, title, shortcode, valut, sum(summa) as summa'))->
		groupBy('type','title', 'shortcode', 'valut')->get();
		$sum=\DB::connection('report')->table('opeartion')->select(\DB::raw('sum(case when type=1 then summa else -summa end) as summa'))->whereIn('type', $typee)->
		whereIn('shortcode', $shortcodes)->first();
		$sql="select t1.opened, t1.summa_opened, t1.summa_opened/t2.btc as opened_btc, t2.datetime as opened_date, t3.razn, (t1.summa_opened+t3.razn) as summa_closed, 
		(t1.summa_opened+t3.razn)/t4.btc as closed_btc, t4.datetime as closed_date from reports t1 left join (select * from courses_tmp where datetime>='$froms' 
		order by datetime asc limit 1) t2 
		on 1=1 left join (select (sum(case when (type=1) then summa*cources end)-sum(case when (type=2) then summa*cources end)) as razn 
		from report_operations where shortcode='$shortcode' and datetime between '$froms' and '$tos') t3 on 1=1 left join 
		(select * from courses_tmp where datetime<='$tos' order by datetime desc limit 1) t4 on 1=1 where t1.opened::date='$from'";
		$launch=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('money_report.report_operations_time', ['cachs'=>$cachs, 'operations'=>$operations, 'from'=>$from, 'to'=>$to, 'type'=>$type, 'typesMenu'=>$typesMenu, 
		'spec_cources'=>$spec_cources, 'report'=>$report, 'cources'=>$cources, 'shortcode'=>$shortcode, 'types'=>$types, 'sum'=>$sum, 'launch'=>$launch]);
	}
	
	public function reportClosed($id){
		$report=Report::find($id);
		if (!$report->closed){
		$report->closed=date("Y-m-d H:i:s");
		$report->save();
		$report->summaClosed();
		}
		else{
			return back()->with('message_danger',"Отчет № {$report->id} был закрыт ранее.");
		}
		$accountOperations=ReportAccount::where('report_id', $report->id)->get();
		foreach ($accountOperations as $operation){
			$account=Account::where('id', $operation->account_id)->first();
			$account->summa->summa=$operation->summa_closed;
			$account->summa->save();
		}
		return back()->with('message_success',"Отчет № {$report->id} успешно закрыт.");
	}
	
	public function reportEdit(Request $request){
		/*if (!\Auth::user()->hasRole('admin')){
			return back()->with('message_danger',"Куда лезешь? Тебе не положено.");
		}*/
		$report=Report::findOrFail($request->input('reports_id'));
		$account=$request->input('account_id');
		$type=$request->input('type');
		$datetime=$request->input('datetime');
		$summa=$request->input('summa');
		$shortcode=$request->input('shortcode');
		$comment=$request->input('comment');
		$obnal=$request->input('obnal');
		$course=\DB::connection('report')->table('courses')->orderBy('datetime','desc')->first();
		foreach ($shortcode as $k=>$sh){
			if ($sh=='no' and $summa[$k]!=0){
				return back()->with('message_danger','Для одной из записей не задан тип.');
			}
		}
		ReportOperation::where('reports_id', $report->id)->where('type', $type)->delete();
		foreach ($datetime as $k=>$date){
			if ($summa[$k]==0){
				continue;
			}
			$cource=\DB::connection('report')->table('courses_tmp')->where('datetime', '<=', $date)->orderBy('datetime', 'desc')->first();
			if (!$cource){
				$cource=\DB::connection('report')->table('courses')->where('date', date("Y-m-d", strtotime($date)))->first();
			}
			$accountee=Account::findOrFail($account[$k]);
			$operat=new ReportOperation;
			$operat->reports_id=$report->id;
			$operat->accounts_id=$account[$k];
			$operat->type=$type;
			$operat->shortcode=$shortcode[$k];
			$operat->summa=$summa[$k];
			$operat->comment=$comment[$k];
			$operat->datetime=$date;
			$operat->obnal=$obnal[$k]?$obnal[$k]:0;
			if ($accountee->shortcode=='rub'){
				$operat->cources=1;
			}
			elseif ($accountee->shortcode=='usd'){
				$operat->cources=$course->usd;
			}
			elseif ($accountee->shortcode=='btc'){
				$operat->cources=$course->btc;
			}
			elseif ($accountee->shortcode=='eth'){
				$operat->cources=$course->eth;
			}
			elseif ($accountee->shortcode=='ltc'){
				$operat->cources=$course->ltc;
			}
			elseif ($accountee->shortcode=='uah'){
				$operat->cources=$course->uah;
			}
			elseif ($accountee->shortcode=='eur'){
				$operat->cources=$course->eur;
			}
			$operat->save();
		}
		$rAccounts=ReportAccount::where('report_id', $report->id)->get();
		foreach ($rAccounts as $rAccount){
			$rAccount->summa_closed=$rAccount->summa_opened;
			$plus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 1)->sum('summa');
			$rAccount->summa_closed+=$plus;
			$minus=ReportOperation::where('reports_id', $rAccount->report_id)->where('accounts_id', $rAccount->account_id)->where('type', 2)->sum('summa');
			$rAccount->summa_closed-=$minus;
			$rAccount->save();
		}
		$report->summaClosed();
		$report->save();
		
		$accountOperations=ReportAccount::where('report_id', $report->id)->get();
		foreach ($accountOperations as $operation){
			$account=Account::where('id', $operation->account_id)->first();
			$account->summa->summa=$operation->summa_closed;
			$account->summa->save();
		}
		$provv=Report::orderBy('opened', 'desc')->first();
		if ($provv->opened!=$report->opened){
			$report->oldEdit();
		}
		return back()->with('message_success',"Данные отчета #{$report->id} успешно измненены.");
	}
	
	public function month($id, Request $request){
		$report= Report::whereNull('closed')->first();
		if (!$report){
			$report=new Report;
			$report->opened=date('Y-m-d H:i:s');
			$report->summa_opened=0;
			$report->save();
		}
		$report->summaOpened();
		$cources=\DB::connection('report')->table('courses')->orderBy('datetime', 'desc')->first();
		
		$month=$request->input('month');
		$year=$request->input('year');
		if (!$month){
			$month=date('m');
		}
		if (!$year){
			$year=date('Y');
		}
		$account=Account::findOrFail($id);
		return view('money_report.month', ['account'=>$account, 'month'=>$month, 'year'=>$year, 'report'=>$report, 'cources'=>$cources]);
	}
}
