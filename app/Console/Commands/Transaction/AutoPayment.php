<?php

namespace App\Console\Commands\Transaction;

use Illuminate\Console\Command;
use App\User;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\Payout;

class AutoPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:auto_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$users=\App\UserProfile::all();
		foreach ($users as $user){
			if ($user->auto_payment!=1){
				continue;
			}
			if ($user->balance<'300'){
				$notif_header="Сбой автозаказа";
				$body="Ваш баланс меньше минимальной суммы выплаты.";
				$notif=new AllNotification;
				$notif->user_id=$user->user_id;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send(\App\User::find($user->user_id), (new Payout($notif->id)));
				continue;
					
			}
			$options=\App\Payments\UserPaymentAuto::where('user_id', $user->user_id)->first();
			if ($options){
				if ($options->day!=date('w')){
					continue;
				}
				if ($options->urgently=='1' and $user->balance<'1000'){
				$notif_header="Сбой автозаказа";
				$body="Для срочного вывода Ваш баланс меньше минимальной суммы.";
				$notif=new AllNotification;
				$notif->user_id=$user->user_id;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send(\App\User::find($user->user_id), (new Payout($notif->id)));
				continue;
				}
				
				$user_id=$user->user_id;
				$payout=floor($user->balance);
				$urgently=$options->urgently;
				$pay_option=$options->payment_id;

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
					continue;
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
			}
		}
	}
}
