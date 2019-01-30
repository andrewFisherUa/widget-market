<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class ObmennegController extends Controller
{

	public function first(Request $request){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		$d=date('d');
		$month=date('m');
		$year=date('Y');
		$valuts=\App\Obmenneg\Valut::orderBy('title', 'asc')->get();
		$sum=\App\Obmenneg\AccountBalance::sum('account_balance');
		return view('obmenneg.first', ['d'=>$d, 'month'=>$month, 'year'=>$year, 'valuts'=>$valuts, 'sum'=>$sum]);
	}
	
	public function index(Request $request){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		$month=$request->input('month')?$request->input('month'):date('m');
		$year=$request->input('year')?$request->input('year'):date('Y');
		$valuts=\App\Obmenneg\Valut::orderBy('title', 'asc')->get();
		$sum=\App\Obmenneg\AccountBalance::sum('account_balance');
		return view('obmenneg.index', ['month'=>$month, 'year'=>$year, 'valuts'=>$valuts, 'sum'=>$sum]);
	}
	
    public function table(Request $request){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		$valuts=\App\Obmenneg\Valut::orderBy('position', 'asc')->get();
		
		return view('obmenneg.table', ['valuts'=>$valuts]);
	}
	
	public function add(){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		return view('obmenneg.add');
	}
	
	public function add_valut_post(Request $request){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		$valut=$request->input('valut');
		$account_balance=$request->input('account_balance');
		$v=new \App\Obmenneg\Valut;
		$v->title=$valut;
		$v->save();
		$balance= new \App\Obmenneg\AccountBalance;
		$balance->id_valut=$v->id;
		$balance->account_balance=$account_balance?$account_balance:0;
		$balance->save();
		$log=new \App\Obmenneg\AccountBalanceLog;
		$log->id_account_balance=$balance->id;
		$log->old_balance=0;
		$log->new_balance=$balance->account_balance;
		$log->who_action=\Auth::user()->id;
		$log->comment='Первое назначение счета при добавлении валюты.';
		$log->save();

		return back()->with('message_success', "Система/счет успешно добавлена.");
	}
	
	public function editAccountBalance(Request $request){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		$from=$request->input('from');
		if (!$from){
			$from=date('Y-m-d');
		}
		$valuts=\App\Obmenneg\Valut::orderBy('position', 'asc')->get();
		return view('obmenneg.edit', ['valuts'=>$valuts, 'from'=>$from]);
	}
	
	public function editPost(Request $request){
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37){
			return abort(404);
		}
		$id=$request->input('id');
		$date=$request->input('from');
		$plus=$request->input('plus');
		$minus=$request->input('minus');
		$comment=$request->input('comment');
		$old_plus=\App\Obmenneg\Transaction::where('date', $date)->where('id_valut', $id)->sum('plus');
		$old_minus=\App\Obmenneg\Transaction::where('date', $date)->where('id_valut', $id)->sum('minus');
		\DB::connection('obmenneg')->table('transactions')->where('date', $date)->where('id_valut', $id)->delete();
		foreach ($plus as $k=>$p){
			if ($comment[$k]=="обнал"){
				$prov=\App\Obmenneg\CacheOut::where('id_valut', $id)->where('date', $date)->where('summa', $minus[$k])->first();
				if (!$prov){
					$cache=new \App\Obmenneg\CacheOut;
					$cache->id_valut=$id;
					$cache->date=$date;
					$cache->summa=$minus[$k];
					$cache->save();
				}
			}
			
			if ($p==0 and $minus[$k]==0 and $comment[$k]==0) continue;
			\DB::connection('obmenneg')->table('transactions')->insert([
				'id_valut' => $id,
				'date' => $date,
				'plus' => $p,
				'minus' => $minus[$k],
				'comment' => $comment[$k],
				'created_at'=>date('Y-m-d H:i:s')
			]);
		}
		$new_plus=\App\Obmenneg\Transaction::where('date', $date)->where('id_valut', $id)->sum('plus');
		$new_minus=\App\Obmenneg\Transaction::where('date', $date)->where('id_valut', $id)->sum('minus');
		$balance=\App\Obmenneg\AccountBalance::where('id_valut', $id)->first();
		$old_balance=$balance->account_balance;
		$new_sum=$new_plus-$old_plus+$old_minus-$new_minus;
		$balance->account_balance=$balance->account_balance+$new_sum;
		$balance->save();
		$log=new \App\Obmenneg\AccountBalanceLog;
		$log->id_account_balance=$balance->id;
		$log->old_balance=$old_balance;
		$log->new_balance=$balance->account_balance;
		$log->who_action=\Auth::user()->id;
		$comment="Измнение транзакций за ".$date;
		$log->comment=$comment;
		$log->save();
		return back();
	}
	
	public function editAccountBalanceOne($id, $from){
		$valut=\App\Obmenneg\Valut::where('id', $id)->first();
		$transactions=\App\Obmenneg\Transaction::where('id_valut', $id)->where('date', $from)->orderBy('id', 'asc')->get();
		return view('obmenneg.one_edit', ['valut'=>$valut, 'from'=>$from, 'transactions'=>$transactions]);
	}
	
	public function editPosition(Request $request){
		$ids=$request->input('id');
		foreach ($ids as $k=>$id){
			$valut=\App\Obmenneg\Valut::find($id);
			$valut->position=$k;
			$valut->save();
		}
		return back();
	}
	
	public function editBalance(Request $request){
		$id=$request->input('id');
		$action=$request->input('action');
		$balance=\App\Obmenneg\AccountBalance::find($id);
		$old_balance=$balance->account_balance;
		$balance->account_balance=$balance->account_balance+$action;
		$balance->save();
		$log=new \App\Obmenneg\AccountBalanceLog;
		$log->id_account_balance=$balance->id;
		$log->old_balance=$old_balance;
		$log->new_balance=$balance->account_balance;
		$log->who_action=\Auth::user()->id;
		if ($action>0){
			$comment="Ручное начисление баланса.";
		}
		else{
			$comment="Ручное вычитание баланса.";
		}
		$log->comment=$comment;
		$log->save();
		return back();
	}
	
	public function BalanceLog($id){
		$logs=\App\Obmenneg\AccountBalanceLog::where('id_account_balance', $id)->orderBy('created_at', 'desc')->paginate(30);
		return view('obmenneg.log', ['logs'=>$logs, 'id'=>$id]);
	}
	
	public function Bot(){
		$status=0;
		if (file_exists('/home/www/widget.market-place.su/public/obmenneg/bot/bot.txt')){
			$content = file_get_contents('https://widget.market-place.su/obmenneg/bot/bot.txt');
			$word = explode(":", $content);
			if (count($word)==2){
				$status=$word[1];
			}
		}
		return view('obmenneg.bot', ['status'=>$status]);
	}
	
	public function BotPost(Request $request){
		$activate=$request->input('activate');
		$message="activate:".$activate;
		$path = "/home/www/widget.market-place.su/public/obmenneg/bot/bot.txt";
		file_put_contents($path, $message);
		return back();
	}
	
	public function addCache(Request $request){
		$id=$request->input('id');
		$date=$request->input('date');
		$summa=$request->input('summa');
		$cache=new \App\Obmenneg\CacheOut;
		$cache->id_valut=$id;
		$cache->date=$date;
		$cache->summa=$summa;
		$cache->save();
		$balance=\App\Obmenneg\AccountBalance::where('id_valut', $id)->first();
		$old_balance=$balance->account_balance;
		$balance->account_balance=$balance->account_balance-$summa;
		$balance->save();
		$log=new \App\Obmenneg\AccountBalanceLog;
		$log->id_account_balance=$balance->id;
		$log->old_balance=$old_balance;
		$log->new_balance=$balance->account_balance;
		$log->who_action=\Auth::user()->id;
		$comment="обнал";
		$log->comment=$comment;
		$log->save();
		\DB::connection('obmenneg')->table('transactions')->insert([
				'id_valut' => $id,
				'date' => $date,
				'plus' => 0,
				'minus' => $summa,
				'comment' => 'обнал',
				'created_at'=>date('Y-m-d H:i:s')
			]);
		return back();
	}
	
	public function CacheValut($id, Request $request){
		$month=$request->input('month')?$request->input('month'):date('m');
		$year=$request->input('year')?$request->input('year'):date('Y');
		if($month=="01" or $month=="03" or $month=="05" or $month=="07" or $month=="08" or $month=="10" or $month=="12"){
			$max=31;
		}
		elseif($month=="04" or $month=="06" or $month=="09" or $month=="11"){
			$max=30;
		}
		elseif($month=="02" and $year%4==0){
			$max=29;
		}
		else{
		$max=28;
		}
		
		if ($month=='god'){
			$from=$year.'-01-01';
			$to=$year.'-12-31';
		}
		else{
			$from=$year.'-'.$month.'-01';
			$to=$year.'-'.$month.'-'.$max;
		}
		$cache=\App\Obmenneg\CacheOut::where('id_valut', $id)->whereBetween('date', [$from, $to])->orderBy('date', 'desc')->get();
		$valut=\App\Obmenneg\Valut::where('id', $id)->first();
		$sum=\App\Obmenneg\CacheOut::where('id_valut', $id)->whereBetween('date', [$from, $to])->orderBy('date', 'desc')->sum('summa');
		return view('obmenneg.cache', ['month'=>$month, 'year'=>$year, 'cache'=>$cache, 'valut'=>$valut, 'sum'=>$sum]);
	}

}
