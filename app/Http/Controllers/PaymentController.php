<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\AllNotification;
use App\Notifications\Payout;
use App\Notifications\PayoutAction;
use SimpleXMLElement;
use DomDocument;

class PaymentController extends Controller
{
    public function setCommission(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$payment=$request->input('payment');
		if ($payment==0 or !$payment){
			return back();
		}
		else if($payment>0){
			$status=1;
		}
		else if($payment<0){
			$status=2;
		}
		
  
		$commission = new \App\Payments\PaymentCommission;
		$commission->user_id=$user_id;
		$commission->who_add=\Auth::user()->id;
		$commission->commission=$payment;
		$commission->status=$status;
		#$commission->save();
		$user=\App\UserProfile::where('user_id', $user_id)->first();
		$user->balance=$user->balance+$payment;
		$user->save();
		$commission->save();
		$manager=\App\UserProfile::where('user_id', $user->manager)->first();
		if ($manager){
			if ($payment>0){
				$notif_header="Админ начислил баланс $user->name";
				$body="Админ начислил юзеру $user->name $payment руб.";
			}
			else{
				$notif_header="Админ оштрафовал $user->name";
				$body="Админ списал с юзера $user->name $payment руб.";
			}
			$notif=new AllNotification;
			$notif->user_id=$manager->user_id;
			$notif->header=$notif_header;
			$notif->body=$body;
			$notif->save();
			\Notification::send(\App\User::find($manager->user_id), (new Payout($notif->id)));
		}
		
		return back()->with('message_success', "Начисление баланса $user->name успешно проведено.");
	}
	
	public function setCommissionJs(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$payment=$request->input('payment');
		if ($payment==0 or !$payment){
			return back();
		}
		else if($payment>0){
			$status=1;
		}
		else if($payment<0){
			$status=2;
		}
		    

	  
		$commission = new \App\Payments\PaymentCommission;
		$commission->user_id=$user_id;
		$commission->who_add=\Auth::user()->id;
		$commission->commission=$payment;
		$commission->status=$status;
		#$commission->save();
		$user=\App\UserProfile::where('user_id', $user_id)->first();
		$user->balance=$user->balance+$payment;
		$user->save();
		$commission->save();
		$manager=\App\UserProfile::where('user_id', $user->manager)->first();
		if ($manager){
			if ($payment>0){
				$notif_header="Админ начислил баланс $user->name";
				$body="Админ начислил юзеру $user->name $payment руб.";
			}
			else{
				$notif_header="Админ оштрафовал $user->name";
				$body="Админ списал с юзера $user->name $payment руб.";
			}
			$notif=new AllNotification;
			$notif->user_id=$manager->user_id;
			$notif->header=$notif_header;
			$notif->body=$body;
			$notif->save();
			\Notification::send(\App\User::find($manager->user_id), (new Payout($notif->id)));
		}
		return response()->json([
			'ok' => true,
			'message' => 'Начисление баланса '.$user->name.' успешно проведено.'
		]);
	}
	
	public function addPayout(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$payout=$request->input('summa');
		$urgently=$request->input('urgently');
		$pay_option=$request->input('pay_option');
		// костыль для ботов яндекса
		/*if ($user_id=='412' or $user_id=='360' or $user_id=='96'){
			return back();
		}*/
		//Алла петрова
		if ($user_id=='678'){
			return back();
		}
		//Пашин какой то клиент по его просьбе
		if ($user_id=='565'){
			return back();
		}
		//Пашин петушара
		if ($user_id=='677'){
			return back();
		}
		if (\Auth::user()->id!=$user_id and !\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager') and !\Auth::user()->hasRole('manager')){
			return back()->with('message_danger', "Даже не пытайся нас обмануть.");
		}
		if (!$urgently){
			if ($payout<'300'){
				return back()->with('message_danger', "Минимальная сумма 300 руб.");
			}
		}
		else{
			if ($payout<'1000'){
				return back()->with('message_danger', "Минимальная сумма срочной выплаты 1000 руб.");
			}
		}
		$user=\App\UserProfile::where('user_id', $user_id)->first();
		if ($user){
			if ($user->balance<$payout){
				return back()->with('message_danger', "Не достаточно средств.");
			}
			$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
			$payment_admin=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
			$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
			$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');
			$old_tran=0;
		if ($user->user_id=='216'){
			$tr=\App\Payments\UserPayout::find(2);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(3);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(4);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(8);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(22);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='255'){
			$tr=\App\Payments\UserPayout::find(5);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='308'){
			$tr=\App\Payments\UserPayout::find(6);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='415'){
			$tr=\App\Payments\UserPayout::find(7);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(9);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='39'){
			$tr=\App\Payments\UserPayout::find(10);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='257'){
			$tr=\App\Payments\UserPayout::find(11);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='206'){
			$tr=\App\Payments\UserPayout::find(12);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='84'){
			$tr=\App\Payments\UserPayout::find(13);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='43'){
			$tr=\App\Payments\UserPayout::find(14);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='111'){
			$tr=\App\Payments\UserPayout::find(15);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='56'){
			$tr=\App\Payments\UserPayout::find(16);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='310'){
			$tr=\App\Payments\UserPayout::find(17);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='296'){
			$tr=\App\Payments\UserPayout::find(18);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='159'){
			$tr=\App\Payments\UserPayout::find(19);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='6'){
			$tr=\App\Payments\UserPayout::find(20);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(21);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='60'){
			$tr=\App\Payments\UserPayout::find(23);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='88'){
			$tr=\App\Payments\UserPayout::find(24);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='5'){
			$tr=\App\Payments\UserPayout::find(25);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
			$summa=$payment_commission+$payment_admin+$old_balance-$payout_sum+$old_tran;
			if ($summa<$payout){
				return back()->with('message_danger', "Не достаточно средств.");
			}
		}
		else{
			return back()->with('message_danger', "Ошибка с юзером.");
		}
		$create = new \App\Payments\UserPayout;
		$create->time_payout=date('Y-m-d H:i:s');
		$create->user_id=$user_id;
		$create->urgently=$urgently?$urgently:0;
		$create->payout=$payout;
		$create->pay_option=$pay_option;
		if (date("w",strtotime($create->time_payout))==0 and $create->urgently==0){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."5"." DAYS"));
		}
		elseif (date("w",strtotime($create->time_payout))==6 and $create->urgently==0){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."6"." DAYS"));
		}
		elseif ($create->urgently==0){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."7"." DAYS"));
		}
		elseif (date("w",strtotime($create->time_payout))==0 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."1"." DAYS"));
		}
		elseif (date("w",strtotime($create->time_payout))==6 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."2"." DAYS"));
		}
		elseif (date("H",strtotime($create->time_payout))>=18 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."1"." DAYS"));
		}
		elseif (date("H",strtotime($create->time_payout))<18 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout"));
		}
		$create->indicative_payment=$indicative_payment;
		$create->save();
		$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
		$payment_admin=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
		$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
		$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');
		$old_tran=0;
		if ($user->user_id=='216'){
			$tr=\App\Payments\UserPayout::find(2);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(3);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(4);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(8);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(22);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='255'){
			$tr=\App\Payments\UserPayout::find(5);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='308'){
			$tr=\App\Payments\UserPayout::find(6);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='415'){
			$tr=\App\Payments\UserPayout::find(7);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(9);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='39'){
			$tr=\App\Payments\UserPayout::find(10);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='257'){
			$tr=\App\Payments\UserPayout::find(11);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='206'){
			$tr=\App\Payments\UserPayout::find(12);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='84'){
			$tr=\App\Payments\UserPayout::find(13);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='43'){
			$tr=\App\Payments\UserPayout::find(14);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='111'){
			$tr=\App\Payments\UserPayout::find(15);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='56'){
			$tr=\App\Payments\UserPayout::find(16);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='310'){
			$tr=\App\Payments\UserPayout::find(17);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='296'){
			$tr=\App\Payments\UserPayout::find(18);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='159'){
			$tr=\App\Payments\UserPayout::find(19);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='6'){
			$tr=\App\Payments\UserPayout::find(20);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(21);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='60'){
			$tr=\App\Payments\UserPayout::find(23);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='88'){
			$tr=\App\Payments\UserPayout::find(24);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='5'){
			$tr=\App\Payments\UserPayout::find(25);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		$user->balance=$payment_commission+$payment_admin+$old_balance-$payout_sum+$old_tran;
		$user->save();
		$create->balance=$user->balance;
		$create->save();
		
		
		if ($urgently=='1'){
			$notif_header="Заказ срочной выплаты.";
			$body="$create->time_payout юзер $user->name заказал срочную выплату на сумму $payout (без учета комиссии).";			
			$users_notif=\App\User::whereHas('roles', function ($query) {
			$query->where('id', 5)->orWhere('id', 4);
			})->get();
			foreach ($users_notif as $u_n){
				$notif=new AllNotification;
				$notif->user_id=$u_n->id;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send($u_n, (new Payout($notif->id)));	
			}
		}
		
		
		return back()->with('message_success', "Заказана выплата на сумму $payout руб.");
	}
	
	public function addPayoutJs(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$payout=$request->input('summa');
		$urgently=$request->input('urgently');
		$pay_option=$request->input('pay_option');
		// костыль для ботов яндекса
		/*if ($user_id=='412' or $user_id=='360' or $user_id=='96'){
			return back();
		}*/
		//Алла петрова
		if ($user_id=='678'){
			return back();
		}
		//Пашин какой то клиент по его просьбе
		if ($user_id=='565'){
			return back();
		}
		//Пашин петушара
		if ($user_id=='677'){
			return back();
		}
		if (\Auth::user()->id!=$user_id and !\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager') and !\Auth::user()->hasRole('manager')){
			return response()->json([
				'ok' => false,
				'message'=>'Не совпадение юзера.'
			]);
		}
		if (!$urgently){
			if ($payout<'300'){
				return response()->json([
					'ok' => false,
					'message'=>'Минимальная сумма 300 руб.'
				]);
			}
		}
		else{
			if ($payout<'1000'){
				return response()->json([
					'ok' => false,
					'message'=>'Минимальная сумма 1000 руб.'
				]);
			}
		}
		$user=\App\UserProfile::where('user_id', $user_id)->first();
		if ($user){
			if ($user->balance<$payout){
				return response()->json([
					'ok' => false,
					'message'=>'Не достаточно средств.'
				]);
			}
			$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
			$payment_admin=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
			$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
			$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');
			$old_tran=0;
		if ($user->user_id=='216'){
			$tr=\App\Payments\UserPayout::find(2);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(3);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(4);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(8);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(22);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='255'){
			$tr=\App\Payments\UserPayout::find(5);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='308'){
			$tr=\App\Payments\UserPayout::find(6);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='415'){
			$tr=\App\Payments\UserPayout::find(7);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(9);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='39'){
			$tr=\App\Payments\UserPayout::find(10);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='257'){
			$tr=\App\Payments\UserPayout::find(11);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='206'){
			$tr=\App\Payments\UserPayout::find(12);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='84'){
			$tr=\App\Payments\UserPayout::find(13);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='43'){
			$tr=\App\Payments\UserPayout::find(14);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='111'){
			$tr=\App\Payments\UserPayout::find(15);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='56'){
			$tr=\App\Payments\UserPayout::find(16);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='310'){
			$tr=\App\Payments\UserPayout::find(17);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='296'){
			$tr=\App\Payments\UserPayout::find(18);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='159'){
			$tr=\App\Payments\UserPayout::find(19);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='6'){
			$tr=\App\Payments\UserPayout::find(20);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(21);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='60'){
			$tr=\App\Payments\UserPayout::find(23);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='88'){
			$tr=\App\Payments\UserPayout::find(24);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='5'){
			$tr=\App\Payments\UserPayout::find(25);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
			$summa=$payment_commission+$payment_admin+$old_balance-$payout_sum+$old_tran;
			if ($summa<$payout){
				return response()->json([
					'ok' => false,
					'message'=>'Не достаточно средств.'
				]);
			}
		}
		else{
			return response()->json([
				'ok' => false,
				'message'=>'Ошибка с юзером.'
			]);
		}
		$create = new \App\Payments\UserPayout;
		$create->time_payout=date('Y-m-d H:i:s');
		$create->user_id=$user_id;
		$create->urgently=$urgently?$urgently:0;
		$create->payout=$payout;
		$create->pay_option=$pay_option;
		if (date("w",strtotime($create->time_payout))==0 and $create->urgently==0){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."5"." DAYS"));
		}
		elseif (date("w",strtotime($create->time_payout))==6 and $create->urgently==0){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."6"." DAYS"));
		}
		elseif ($create->urgently==0){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."7"." DAYS"));
		}
		elseif (date("w",strtotime($create->time_payout))==0 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."1"." DAYS"));
		}
		elseif (date("w",strtotime($create->time_payout))==6 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."2"." DAYS"));
		}
		elseif (date("H",strtotime($create->time_payout))>=18 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout + "."1"." DAYS"));
		}
		elseif (date("H",strtotime($create->time_payout))<18 and $create->urgently==1){
			$indicative_payment=date("Y-m-d",strtotime("$create->time_payout"));
		}
		$create->indicative_payment=$indicative_payment;
		$create->save();
		$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
		$payment_admin=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
		$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
		$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');
		$old_tran=0;
		if ($user->user_id=='216'){
			$tr=\App\Payments\UserPayout::find(2);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(3);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(4);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(8);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(22);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='255'){
			$tr=\App\Payments\UserPayout::find(5);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='308'){
			$tr=\App\Payments\UserPayout::find(6);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='415'){
			$tr=\App\Payments\UserPayout::find(7);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(9);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='39'){
			$tr=\App\Payments\UserPayout::find(10);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='257'){
			$tr=\App\Payments\UserPayout::find(11);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='206'){
			$tr=\App\Payments\UserPayout::find(12);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='84'){
			$tr=\App\Payments\UserPayout::find(13);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='43'){
			$tr=\App\Payments\UserPayout::find(14);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='111'){
			$tr=\App\Payments\UserPayout::find(15);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='56'){
			$tr=\App\Payments\UserPayout::find(16);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='310'){
			$tr=\App\Payments\UserPayout::find(17);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='296'){
			$tr=\App\Payments\UserPayout::find(18);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='159'){
			$tr=\App\Payments\UserPayout::find(19);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='6'){
			$tr=\App\Payments\UserPayout::find(20);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(21);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='60'){
			$tr=\App\Payments\UserPayout::find(23);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='88'){
			$tr=\App\Payments\UserPayout::find(24);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='5'){
			$tr=\App\Payments\UserPayout::find(25);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		$user->balance=$payment_commission+$payment_admin+$old_balance-$payout_sum+$old_tran;
		$user->save();
		$create->balance=$user->balance;
		$create->save();
		
		
		if ($urgently=='1'){
			$notif_header="Заказ срочной выплаты.";
			$body="$create->time_payout юзер $user->name заказал срочную выплату на сумму $payout (без учета комиссии).";			
			$users_notif=\App\User::whereHas('roles', function ($query) {
			$query->where('id', 5)->orWhere('id', 4);
			})->get();
			foreach ($users_notif as $u_n){
				$notif=new AllNotification;
				$notif->user_id=$u_n->id;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send($u_n, (new Payout($notif->id)));	
			}
		}
		return response()->json([
			'ok' => true,
			'message'=>"Заказана выплата на сумму ".$payout." руб.",
		]);
	}
	
	public function addPayoutAutoJs(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$urgently=$request->input('urgently');
		$auto_pay=$request->input('auto_pay');
		$day=$request->input('day');
		$pay_option=$request->input('pay_option');
		// костыль для ботов яндекса
		/*if ($user_id=='412' or $user_id=='360' or $user_id=='96'){
			return back();
		}*/
		//Алла петрова
		if ($user_id=='678'){
			return back();
		}
		//Пашин какой то клиент по его просьбе
		if ($user_id=='565'){
			return back();
		}
		//Пашин петушара
		if ($user_id=='677'){
			return back();
		}
		if (\Auth::user()->id!=$user_id and !\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager') and !\Auth::user()->hasRole('manager')){
			return response()->json([
				'ok' => false,
				'message'=>"Не совпадение юзера.",
			]);
		}
		$userProfile=\App\UserProfile::where('user_id', $user_id)->first();
		if ($userProfile){
			$userProfile->auto_payment=$auto_pay;
			$userProfile->save();
			if ($auto_pay){
			$auto=\App\Payments\UserPaymentAuto::firstOrNew(['user_id' => $user_id]);
			$auto->payment_id=$pay_option;
			$auto->day=$day;
			$auto->urgently=$urgently;
			$auto->save();
			}
			else{
				$auto=\App\Payments\UserPaymentAuto::where('user_id', $user_id)->delete();
			}
		}
		else{
			return response()->json([
				'ok' => false,
				'message'=>"Юзер не найден.",
			]);
		}
		if ($auto_pay){
			return response()->json([
				'ok' => true,
				'message'=>"Автозаказ выплаты успешно включен.",
			]);
			return back()->with('message_success', "Автозаказ выплаты успешно включен");
		}
		else{
			return response()->json([
				'ok' => true,
				'message'=>"Автозаказ выплаты успешно выключен.",
			]);
		}
	}
	
	public function addPayoutAuto(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$urgently=$request->input('urgently');
		$auto_pay=$request->input('auto_pay');
		$day=$request->input('day');
		$pay_option=$request->input('pay_option');
		// костыль для ботов яндекса
		/*if ($user_id=='412' or $user_id=='360' or $user_id=='96'){
			return back();
		}*/
		//Алла петрова
		if ($user_id=='678'){
			return back();
		}
		//Пашин какой то клиент по его просьбе
		if ($user_id=='565'){
			return back();
		}
		//Пашин петушара
		if ($user_id=='677'){
			return back();
		}
		if (\Auth::user()->id!=$user_id and !\Auth::user()->hasRole('admin') and !\Auth::user()->hasRole('super_manager') and !\Auth::user()->hasRole('manager')){
			return back()->with('message_danger', "Даже не пытайся нас обмануть.");
		}
		$userProfile=\App\UserProfile::where('user_id', $user_id)->first();
		if ($userProfile){
			$userProfile->auto_payment=$auto_pay;
			$userProfile->save();
			if ($auto_pay){
			$auto=\App\Payments\UserPaymentAuto::firstOrNew(['user_id' => $user_id]);
			$auto->payment_id=$pay_option;
			$auto->day=$day;
			$auto->urgently=$urgently;
			$auto->save();
			}
			else{
				$auto=\App\Payments\UserPaymentAuto::where('user_id', $user_id)->delete();
			}
		}
		else{
			return back()->with('message_danger', "Пользователь не найден.");
		}
		if ($auto_pay){
			return back()->with('message_success', "Автозаказ выплаты успешно включен");
		}
		else{
			return back()->with('message_success', "Автозаказ выплаты успешно выключен");
		}
	}
	
	public function allPayouts(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		$cnt=$request->input('cnt')?$request->input('cnt'):20;
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$direct=$request->input('direct');
        $order=$request->input('order');
		$status=$request->input('status');
		if(!$status)
		$order=$order?$order:"time_payout";
		else
        $order=$order?$order:"exit_time_payout";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Оплатить до",'index'=>"indicative_payment","order"=>"",'url'=>""],
			['title'=>"Дата создания заявки",'index'=>"time_payout","order"=>"",'url'=>""],
			['title'=>"Дата закрытия заявки",'index'=>"exit_time_payout","order"=>"",'url'=>""]
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
		
		
		
		if (!$status){
			$payouts=\App\Payments\UserPayout::where('status', 0)->orderBy('urgently', 'desc')->orderBy($order,$direct)->paginate($cnt);
			if(3==1){
			$payouts=\App\Payments\UserPayout::where('status', 0)
			->where('user_id', 999)
			->orderBy('urgently', 'desc')->orderBy($order,$direct)->paginate($cnt);
			echo "<pre>"; var_dump($payouts);		echo "</pre>";
			}
			$status=0;
			
		}
		elseif ($status=='all'){
			$payouts=\App\Payments\UserPayout::orderBy($order,$direct)->paginate($cnt);
		}
		else{
			$payouts=\App\Payments\UserPayout::where('status', $status)->orderBy($order,$direct)->paginate($cnt);
		}
		

		$pay_options=\DB::table('payment_options')->get();
		return view('admin.payouts.payouts', ['order'=>$order, 'direct'=>$direct, 'header'=>$header, 'cnt'=>$cnt, 'from'=>$from, 'to'=>$to, 'payouts'=>$payouts, 'pay_options'=>$pay_options, 'status'=>$status]);
	}
	
	public function actionPayouts(Request $request){
		$id=$request->input('id');
		$status=$request->input('status');
		$pay_option=$request->input('pay_option');
		
		$opti=\DB::table('payment_options')->where('id', $pay_option)->first();
		$payout=\App\Payments\UserPayout::where('id', $id)->first();
		$payout->status=$status;
		$payout->pay_option=$pay_option;
		$payout->exit_time_payout=date("Y-m-d H:i:s");
		if ($request->input('payout_fact')){
			$payout->payouts_fact=$request->input('payout_fact');
		}
		$payout->save();
		
		$user=\App\UserProfile::where('user_id', $payout->user_id)->first();
		$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
		$payment_admin=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
		$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
		$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');
		
		$old_tran=0;
		if ($user->user_id=='216'){
			$tr=\App\Payments\UserPayout::find(2);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(3);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(4);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(8);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(22);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='255'){
			$tr=\App\Payments\UserPayout::find(5);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='308'){
			$tr=\App\Payments\UserPayout::find(6);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='415'){
			$tr=\App\Payments\UserPayout::find(7);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(9);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='39'){
			$tr=\App\Payments\UserPayout::find(10);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='257'){
			$tr=\App\Payments\UserPayout::find(11);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='206'){
			$tr=\App\Payments\UserPayout::find(12);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='84'){
			$tr=\App\Payments\UserPayout::find(13);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='43'){
			$tr=\App\Payments\UserPayout::find(14);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='111'){
			$tr=\App\Payments\UserPayout::find(15);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='56'){
			$tr=\App\Payments\UserPayout::find(16);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='310'){
			$tr=\App\Payments\UserPayout::find(17);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='296'){
			$tr=\App\Payments\UserPayout::find(18);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='159'){
			$tr=\App\Payments\UserPayout::find(19);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='6'){
			$tr=\App\Payments\UserPayout::find(20);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(21);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='60'){
			$tr=\App\Payments\UserPayout::find(23);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='88'){
			$tr=\App\Payments\UserPayout::find(24);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='5'){
			$tr=\App\Payments\UserPayout::find(25);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		
		
		$user->balance=$payment_commission+$payment_admin+$old_balance-$payout_sum+$old_tran;
		$user->save();
		
		if ($status==1){
			$notif_header="Оплаченная заявка";
			if ($opti){
				$body="Ваша заявка на сумму $payout->payout оплачена на кошелек $opti->name.";
			}
			else{
				$body="Ваша заявка на сумму $payout->payout оплачена.";
			}
			$notif=new AllNotification;
			$notif->user_id=$payout->user_id;
			$notif->header=$notif_header;
			$notif->body=$body;
			$notif->save();
			\Notification::send(\App\User::find($payout->user_id), (new PayoutAction($notif->id)));
			return back()->with('message_success', "Выплата отмечена как оплаченная.");
		}
		else if ($status==2){
			$notif_header="Не оплаченная заявка";
			$body="Ваша заявка на сумму $payout->payout отклонена.";
			$notif=new AllNotification;
			$notif->user_id=$payout->user_id;
			$notif->header=$notif_header;
			$notif->body=$body;
			$notif->save();
			\Notification::send(\App\User::find($payout->user_id), (new PayoutAction($notif->id)));
			return back()->with('message_success', "Выплата отмечена как не оплаченная.");
		}
		
	}
	public function actionPayoutsUser(Request $request){
		$id=$request->input('id');
		$status=$request->input('status');
		$payout=\App\Payments\UserPayout::where('id', $id)->first();
		if ($payout->status!=0){
			return back()->with('message_danger', "Нельзя отменить заявку.");
		}
		$payout->status=3;
		$payout->exit_time_payout=date("Y-m-d H:i:s");
		$payout->save();
		
		$user=\App\UserProfile::where('user_id', $payout->user_id)->first();
		$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
		$payment_admin=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
		$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
		$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');
		
		$old_tran=0;
		if ($user->user_id=='216'){
			$tr=\App\Payments\UserPayout::find(2);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(3);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(4);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(8);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
			$tr=\App\Payments\UserPayout::find(22);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='255'){
			$tr=\App\Payments\UserPayout::find(5);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='308'){
			$tr=\App\Payments\UserPayout::find(6);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='415'){
			$tr=\App\Payments\UserPayout::find(7);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(9);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='39'){
			$tr=\App\Payments\UserPayout::find(10);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='257'){
			$tr=\App\Payments\UserPayout::find(11);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='206'){
			$tr=\App\Payments\UserPayout::find(12);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='84'){
			$tr=\App\Payments\UserPayout::find(13);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='43'){
			$tr=\App\Payments\UserPayout::find(14);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='111'){
			$tr=\App\Payments\UserPayout::find(15);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='56'){
			$tr=\App\Payments\UserPayout::find(16);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='310'){
			$tr=\App\Payments\UserPayout::find(17);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='296'){
			$tr=\App\Payments\UserPayout::find(18);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='159'){
			$tr=\App\Payments\UserPayout::find(19);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='6'){
			$tr=\App\Payments\UserPayout::find(20);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='90'){
			$tr=\App\Payments\UserPayout::find(21);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='60'){
			$tr=\App\Payments\UserPayout::find(23);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='88'){
			$tr=\App\Payments\UserPayout::find(24);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}
		if ($user->user_id=='5'){
			$tr=\App\Payments\UserPayout::find(25);
			if ($user->user_id==$tr->user_id){
				if ($tr->status==1 or $tr->status==0){
					$old_tran+=$tr->payout;
				}
			}
		}

		
		$user->balance=$payment_commission+$payment_admin+$old_balance-$payout_sum+$old_tran;
		$user->save();
		return back()->with('message_success', "Заявка отменена.");
	}
	
	public function WebMoney(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if (!\Auth::user()->hasRole('admin')){
			return abort(403);
		}
		$payouts=\App\Payments\UserPayout::where('status', 0)->where('pay_option', 4)->whereBetween('indicative_payment', [$from, $to])->get();
		if (count($payouts)==0){
			return back()->with('message_danger', "Нет не оплаченых заявок в этот день.");
		}
		$site_url = "market-place.su";//уберите лишние пробелы
		$base = '<?xml version="1.0" encoding="UTF-8"?>
            <payments xmlns="http://tempuri.org/ds.xsd">
            </payments>';
		$xmlbase = new SimpleXMLElement($base);
		foreach ($payouts as $payout){
			
			$wmr=\App\UsersPaymentOption::where('user_id', $payout->user_id)->where('payment_id', '4')->first()->value;
			if ($payout->urgently==0){
				$summa=$payout->payout;
			}
			else{
				$summa=$payout->payout*0.94;
			}
			$row = $xmlbase->addChild("payment");
			$row->addChild("Destination",$wmr);
			$row->addChild("Amount",$summa);
			$row->addChild("Description","Перевод по заявке id - $payout->id");
			$row->addChild("Id",$payout->id);
		}
		$name="payment_wmr_".date("Y-m-d_H-i-s").".xml";
		$xmlbase->saveXML($_SERVER["DOCUMENT_ROOT"]."/wmr/".$name);
		return response()->download($_SERVER["DOCUMENT_ROOT"]."/wmr/".$name);
	}
	
	public function allPayoutsReport(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if (!$from or !$to){
			$from=date('Y-m-d',time()-3600*24);
            $to=date('Y-m-d');
		}
		$froms=date("Y-m-d 00:00:00", strtotime($from));
		$tos=date("Y-m-d 23:59:59", strtotime($to));
		$payouts=\App\Payments\UserPayout::select(\DB::raw('sum(case when payouts_fact>0 then payouts_fact else payout end) as summa, pay_option'))->where('status', 1)->whereBetween('exit_time_payout', [$froms, $tos])->groupBy('pay_option')->get();
		$pay_options=\DB::table('payment_options')->get();
		return view ('admin.payouts.payouts_rep', ['pay_options'=>$pay_options, 'payouts'=>$payouts, 'from'=>$from, 'to'=>$to]);
	}
	
	
}
