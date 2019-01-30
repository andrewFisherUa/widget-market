<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \YandexMoney\API;

class LbtcController extends Controller
{
	private $lbtckey='78f4d04a4d46fa7948e0cef1ba786f79'; //ключ localbitcoins controller
	private $lbtcsecret='724a6cad404ad79523aa998ffb40cdf6c61775a187f6a6131771947a23a4ceb8'; //секрет localbitcoins controller
	private $qiwi='46c80b272ffc67f5c0738fc912763a14'; //токен qiwi для кошелька 79281131180 действует до 15.08.2018
	//'2260fafba73431387ed8d5c776a322ae'; //токен qiwi для кошелька 79381148114 действует до 08.08.2018
	private $yandex='410011520925164.828062B84FD6997D29F145E6182152FF1C11A2CAC5B004D6FD270A2D72DFE8E38F41AEC4C5D007721C976854CA5AF420ADE572FA69439E8DC7F844A7B4F5F9AB743555D8BB4B5E85B807C8DCB397FB8123595D06F8F06890C257A95CD89B8E2AE887F06F560113F542EF74E219A2806855CD0281D43DC5CD27C1550EB4E33A09'; //токен яндекс деньги
	public function index(){
		$ads=\DB::connection('obmenneg')->table('all_ads')->where('trade_type', 'ONLINE_SELL')->orWhere('trade_type', 'ONLINE_BUY')->orderBy('provider', 'asc')->orderBy('valut', 'asc')->get();
		$locales=\DB::connection('obmenneg')->table('all_ads')->where('trade_type', 'LOCAL_SELL')->orWhere('trade_type', 'LOCAL_BUY')->orderBy('id', 'asc')->get();
		$offers=\DB::connection('obmenneg')->table('ads_offers')->get();
		$sms=\DB::connection('obmenneg')->table('sms')->where('sms', 'sms')->first();
		$url='https://edge.qiwi.com/funding-sources/v1/accounts/current';
		$curl = curl_init($url);
		$nonce=1000*time();
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-type: application/json',
			'Authorization: Bearer '.$this->qiwi
			),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$qiwibalance=0;
		/*foreach ($result->accounts as $account){
			if ($account->alias!="qw_wallet_rub"){
				continue;
			}
			if ($account->balance->amount){
				$qiwibalance=$account->balance->amount;
			}
		}*/
		$api = new API($this->yandex);
		$acount_info = $api->accountInfo();
		$yandexbalance=0;
		if ($acount_info->balance_details->total){
			$yandexbalance=$acount_info->balance_details->total;
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select sum(course)*0.98 as course, sum(remainder) as remainder from balancing where id_ad='617372' and status='0'";
		$actual_buy_qiwi=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select (sum(course)/count(course))*1.03 as course, sum(course) as cour, sum(remainder) as remainder from balancing where id_ad='609849' and status='0'";
		$actual_sell_qiwi=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		
		$sql="select sum(course)*0.985 as course, sum(remainder) as remainder from balancing where id_ad='609305' and status='0'";
		$actual_buy_yandex=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select (sum(course)/count(course))*1.03 as course, sum(course) as cour, sum(remainder) as remainder from balancing where id_ad='609928' and status='0'";
		$actual_sell_yandex=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		
		return view('lbtc.index', ['actual_buy_yandex'=>$actual_buy_yandex, 'actual_sell_yandex'=>$actual_sell_yandex, 'actual_buy_qiwi'=>$actual_buy_qiwi, 'actual_sell_qiwi'=>$actual_sell_qiwi, 'ads'=>$ads, 'offers'=>$offers, 'locales'=>$locales, 'sms'=>$sms, 'qiwibalance'=>$qiwibalance, 'yandexbalance'=>$yandexbalance]);
	}
	
	public function indexIndex(){
		$ads=\DB::connection('obmenneg')->table('all_ads')->where('trade_type', 'ONLINE_SELL')->orWhere('trade_type', 'ONLINE_BUY')->orderBy('provider', 'asc')->orderBy('valut', 'asc')->get();
		return view('lbtc.index_index', ['ads'=>$ads]);
	}
	
	public function newParse($id, Request $request){
		$position=$request->input('position');
		$step=$request->input('step');
		$min=$request->input('min');
		$robot=$request->input('robot')?$request->input('robot'):0;
		$parse_status=$request->input('parse_status');
		$limite=$request->input('limite')?$request->input('limite'):0;
		$on_actual=$request->input('on_actual')?$request->input('on_actual'):0;
		\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id)->update(['position'=>$position, 'step'=>$step, 
		'min'=>$min, 'parse_status'=>$parse_status, 'robot'=>$robot, 'on_actual'=>$on_actual]);
		return response()->json([
			'ok' => true
		]);
	}
	
	public function birges(){
		$birges=\DB::connection('obmenneg')->table('parse_table')->orderBy('id', 'asc')->get();
		return view('lbtc.birges', ['birges'=>$birges]);
	}
	
	public function parse($id, Request $request){
		$position=$request->input('position');
		$step=$request->input('step');
		$min=$request->input('min');
		$robot=$request->input('robot')?$request->input('robot'):0;
		$parse_status=$request->input('parse_status');
		$limite=$request->input('limite')?$request->input('limite'):0;
		$on_actual=$request->input('on_actual')?$request->input('on_actual'):0;
		\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id)->update(['position'=>$position, 'step'=>$step, 
		'min'=>$min, 'parse_status'=>$parse_status, 'robot'=>$robot, 'on_actual'=>$on_actual]);
		
		
		
		/*if ($parse_status==1){
			$this->parseOn($id);
		}*/
		return back();
	}
	
    public function parseOn($id){
		$ads=\DB::connection('obmenneg')->table('all_ads')->get();
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into ads_offers (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
			select ?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
		$sql="update all_ads set temp_price=?, prosent=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		$ad=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id)->first();
		if (!$ad){
			return 0;
		}
		if ($ad->trade_type=='ONLINE_SELL'){
			$url='/buy-bitcoins-online/' . $ad->valut . '/' . $ad->provider . '/.json';
		}
		elseif ($ad->trade_type=='ONLINE_BUY'){
			$url='/sell-bitcoins-online/' . $ad->valut . '/' . $ad->provider . '/.json';
		}
		else{
			return;
		}
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$array_message=array(
		);
		$message=http_build_query($array_message);
		$apiauth = $nonce.$this->lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_TIMEOUT => 40,
		CURLOPT_FILETIME => TRUE,
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$i=0;
		\DB::connection("obmenneg")->table('ads_offers')->where('my_id_ad', $ad->id_ads)->delete();
		foreach ($result->data->ad_list as $k=>$res){
			if ($i>9){
				continue;
			}
			if ($res->data->profile->username=='Obmenneg'){
				continue;
			}
			if ($ad->parse_status and ($ad->position-1==$i)){
				$url='/api/ad-get/' . $ad->id_ads . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$array_message=array(
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$this->lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => $message,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$this->lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
				$result=json_decode($json);
				$price=$result->data->ad_list[0]->data->temp_price;
				$btc=$result->data->ad_list[0]->data->temp_price_usd;
				$formula=$result->data->ad_list[0]->data->price_equation;
				$procent=preg_replace("/[^.0-9]/", '', $formula);
				$usd=$price/($btc*$procent);
				
				$url='/api/ad-get/' . $res->data->ad_id . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$array_message=array(
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$this->lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => $message,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$this->lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
				$result=json_decode($json);
				$procent2=$result->data->ad_list[0]->data->temp_price/($btc*$usd);
				if ($ad->trade_type=='ONLINE_BUY' or $ad->trade_type=='LOCAL_BUY'){
					$min=1-($ad->min/100);
				}
				else{
					$min=1+($ad->min/100);
				}
				if ($ad->trade_type=='ONLINE_BUY' or $ad->trade_type=='LOCAL_BUY'){
					if($min<=$procent2){
						$new_procent=$min;
					}
					else{
						$new_procent=$procent2+$ad->step;
					}
				}
				else{
					if ($min>=$procent2){
						$new_procent=$min;
					}
					else{
						$new_procent=$procent2+$ad->step;
					}
				}

				$url='/api/ad-equation/' . $ad->id_ads . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				//btc_in_usd*USD_in_RUB*1.1400000000000001
				if ($ad->valut=='RUB'){
					$array_message=array(
					//'price_equation'=>'btc_in_usd*USD_in_RUB*1.1400000000000001=670000'
					'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .''
					);
				}
				else{
					$array_message=array(
					//'price_equation'=>'btc_in_usd*USD_in_RUB*1.1400000000000001=670000'
					'price_equation'=>'btc_in_usd*' . $new_procent .''
					);
				}
				$message=http_build_query($array_message);
				$apiauth = $nonce.$this->lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => $message,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$this->lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
				$result=json_decode($json);
				
				$url='/api/ad-get/' . $ad->id_ads . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$array_message=array(
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$this->lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => $message,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$this->lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
				$result=json_decode($json);
				if ($result->data->ad_list[0]->data->trade_type=='ONLINE_BUY' or $result->data->ad_list[0]->data->trade_type=='LOCAL_BUY'){
					$procent=100-$new_procent*100;
				}
				else{
					$procent=$new_procent*100-100;
				}
				$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->ad_id]);
			}
			if ($res->data->bank_name){
				$name=$res->data->trade_type .' '. $res->data->bank_name .' '. $res->data->currency;
			}
			else{
				$name=$res->data->trade_type .' '. $res->data->online_provider .' '. $res->data->currency;
			}
			if ($i<4){
			$sthInsert->execute([$res->data->ad_id, $res->data->profile->username, $name, $res->data->temp_price, $ad->id_ads, $res->data->min_amount, $res->data->max_amount]);
			}
			$i++;
		}
		return;
	}
	
	public function prosent($id, Request $request){
		$prosent=$request->input('prosent');
		if (!$prosent){
			return back();
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update all_ads set temp_price=?, prosent=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		//$new_procent=1+($prosent/100);
		$ads=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id)->first();
		if ($ads->trade_type=='ONLINE_BUY' or $ads->trade_type=='LOCAL_BUY'){
			$new_procent=1-($prosent/100);
		}
		else{
			$new_procent=1+($prosent/100);
		}
		$url='/api/ad-equation/' . $id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		//btc_in_usd*USD_in_RUB*1.1400000000000001
		if ($ads->valut=='RUB'){
			$array_message=array(
			//'price_equation'=>'btc_in_usd*USD_in_RUB*1.1400000000000001=670000'
			'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .''
			);
		}
		else{
			$array_message=array(
			//'price_equation'=>'btc_in_usd*USD_in_RUB*1.1400000000000001=670000'
			'price_equation'=>'btc_in_usd*' . $new_procent .''
			);
		}
		
		$message=http_build_query($array_message);
		$apiauth = $nonce.$this->lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POSTFIELDS => $message,
		CURLOPT_TIMEOUT => 40,
		CURLOPT_FILETIME => TRUE,
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		
		$url='/api/ad-get/' . $id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$array_message=array(
		);
		$message=http_build_query($array_message);
		$apiauth = $nonce.$this->lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POSTFIELDS => $message,
		CURLOPT_TIMEOUT => 40,
		CURLOPT_FILETIME => TRUE,
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $prosent, $result->data->ad_list[0]->data->ad_id]);
		return back();
	}
	
	public function sms(Request $request){
		$sms=$request->input('sms');
		\DB::connection('obmenneg')->table('sms')->where('sms', 'sms')->update(['value' => $sms]);
		return back();
	}
	
	public function table(){
		$d=date('d');
		$month=date('m');
		$year=date('Y');
		$valuts=\App\Obmenneg\Valut::orderBy('title', 'asc')->get();
		$from=date('Y-m-d');
		$usd=\App\Obmenneg\Valut::where('title', 'like', '%USD%')->leftJoin('account_balances', 'valuts.id', '=', 'account_balances.id_valut')->sum('account_balance');
		$rub=\App\Obmenneg\Valut::where('title', 'not like', '%USD%')->leftJoin('account_balances', 'valuts.id', '=', 'account_balances.id_valut')->sum('account_balance');
		return view('lbtc.table', ['valuts'=>$valuts, 'd'=>$d, 'month'=>$month, 'year'=>$year, 'from'=>$from, 'usd'=>$usd, 'rub'=>$rub]);
	}
	
	public function month($id, Request $request){
		$month=$request->input('month');
		$year=$request->input('year');
		if (!$month){
			$month=date('m');
		}
		if (!$year){
			$year=date('Y');
		}
		$valut=\App\Obmenneg\Valut::where('id', $id)->first();
		return view('lbtc.month', ['valut'=>$valut, 'month'=>$month, 'year'=>$year]);
	}
	
	public function day($id, $date, Request $request){
		$stats=\App\Obmenneg\Transaction::where('id_valut', $id)->where('date', $date)->orderBy('created_at', 'desc')->get();
		$valut=\App\Obmenneg\Valut::where('id', $id)->first();
		return view('lbtc.day', ['stats'=>$stats, 'from'=>$date, 'valut'=>$valut]);
	}
	
	public function BalanceLog($id){
		$logs=\App\Obmenneg\AccountBalanceLog::where('id_account_balance', $id)->orderBy('created_at', 'desc')->paginate(30);
		return view('lbtc.log', ['logs'=>$logs, 'id'=>$id]);
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
		$obnal=$request->input('obnal');
		$old_plus=\App\Obmenneg\Transaction::where('date', $date)->where('id_valut', $id)->sum('plus');
		$old_minus=\App\Obmenneg\Transaction::where('date', $date)->where('id_valut', $id)->sum('minus');
		\DB::connection('obmenneg')->table('transactions')->where('date', $date)->where('id_valut', $id)->delete();
		foreach ($plus as $k=>$p){	
			if ($p==0 and $minus[$k]==0 and $comment[$k]==0) continue;
			\DB::connection('obmenneg')->table('transactions')->insert([
				'id_valut' => $id,
				'date' => $date,
				'plus' => $p,
				'minus' => $minus[$k],
				'comment' => $comment[$k],
				'obnal' => $obnal[$k],
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
	
	public function limite(Request $request){
		$id=$request->input('id');
		$limite=$request->input('limite');
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', $id)->update(['shell'=>$limite, 'buy'=>'0', 'limite'=>$limite, 'updated'=>'now()']);
		return back();
	}
	
	public function Qiwi(Request $request){
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql = "select date_trunc('day', t1.created_at) as day, t2.amount_btc_sell, t2.amount_sell, t2.curs_sell, 
			t3.amount_btc_buy, t3.amount_buy, t3.curs_buy from lbtc_robots t1 
			left join (select sum(amount_btc) as amount_btc_sell, sum(amount) as amount_sell, sum(amount)/sum(amount_btc) as curs_sell, 
			date_trunc('day', created_at) as day from lbtc_robots where id_ad='617372' and status='9' group by day) t2 on 
			date_trunc('day', t1.created_at)=t2.day
			left join (select sum(amount_btc) as amount_btc_buy, sum(amount) as amount_buy, sum(amount)/sum(amount_btc) as curs_buy, 
			date_trunc('day', created_at) as day from lbtc_robots where id_ad='609849' and status='9' group by day) t3 on 
			date_trunc('day', t1.created_at)=t3.day

			where t1.id_ad in (609849, 617372) and status=9 group by date_trunc('day', t1.created_at), t2.amount_btc_sell, t2.amount_sell, t2.curs_sell, 
			t3.amount_btc_buy, t3.amount_buy, t3.curs_buy
			order by day desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="select sum(amount_btc) as amount_btc_sell, sum(amount) as amount_sell, sum(amount)/sum(amount_btc) as curs_sell, 
			t2.amount_btc_buy, t2.amount_buy, t2.curs_buy 
			from lbtc_robots t1 
			left join (select sum(amount_btc) as amount_btc_buy, sum(amount) as amount_buy, sum(amount)/sum(amount_btc) as curs_buy 
			from lbtc_robots where id_ad='609849' and status='9') t2 on 1=1 

			 where t1.id_ad='617372' and t1.status='9' group by t2.amount_btc_buy, t2.amount_buy, t2. curs_buy";
		$all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.qiwi.transactions', ['stats'=>$stats, 'all'=>$all]);
	}
	
	public function Yandex(Request $request){
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql = "select date_trunc('day', t1.created_at) as day, t2.amount_btc_sell, t2.amount_sell, t2.curs_sell, 
			t3.amount_btc_buy, t3.amount_buy, t3.curs_buy from lbtc_robots t1 
			left join (select sum(amount_btc) as amount_btc_sell, sum(amount) as amount_sell, sum(amount)/sum(amount_btc) as curs_sell, 
			date_trunc('day', created_at) as day from lbtc_robots where id_ad='609305' and status='9' group by day) t2 on 
			date_trunc('day', t1.created_at)=t2.day
			left join (select sum(amount_btc) as amount_btc_buy, sum(amount) as amount_buy, sum(amount)/sum(amount_btc) as curs_buy, 
			date_trunc('day', created_at) as day from lbtc_robots where id_ad='	609928' and status='9' group by day) t3 on 
			date_trunc('day', t1.created_at)=t3.day

			where t1.id_ad in (609305, 	609928) and status=9 group by date_trunc('day', t1.created_at), t2.amount_btc_sell, t2.amount_sell, t2.curs_sell, 
			t3.amount_btc_buy, t3.amount_buy, t3.curs_buy
			order by day desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="select sum(amount_btc) as amount_btc_sell, sum(amount) as amount_sell, sum(amount)/sum(amount_btc) as curs_sell, 
			t2.amount_btc_buy, t2.amount_buy, t2.curs_buy 
			from lbtc_robots t1 
			left join (select sum(amount_btc) as amount_btc_buy, sum(amount) as amount_buy, sum(amount)/sum(amount_btc) as curs_buy 
			from lbtc_robots where id_ad='609928' and status='9') t2 on 1=1 

			 where t1.id_ad='609305' and t1.status='9' group by t2.amount_btc_buy, t2.amount_buy, t2. curs_buy";
		$all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.yandex.transactions', ['stats'=>$stats, 'all'=>$all]);
	}
	
	public function Qiwiv2(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select date_trunc('day', t1.created_at) as day, coalesce(t2.amount,0) as amount_buy, coalesce(t2.amount_btc,0) as amount_btc_buy, 
			coalesce(t2.course,0) as course_buy, coalesce(t2.return_course,0) as return_course_buy, coalesce(t2.remainder,0) as remainder_buy, 
			case when (coalesce(t2.return_course,0)>0 and coalesce(t2.course,0)>0) 
			then (coalesce(t2.return_course,0)-coalesce(t2.course,0))/coalesce(t2.return_course,0)*100 end as prosent_buy,
			coalesce(t3.amount,0) as amount_sell, coalesce(t3.amount_btc,0) as amount_btc_sell, 
			coalesce(t3.course,0) as course_sell, coalesce(t3.return_course,0) as return_course_sell, 
			case when (coalesce(t3.return_course,0)>0 and coalesce(t3.course,0)>0) 
			then (coalesce(t3.course,0)-coalesce(t3.return_course,0))/coalesce(t3.course,0)*100 end as prosent_sell,
			coalesce(t3.remainder,0) as remainder_sell 
			from lbtc_robots t1 
			left join (select id_ad, sum(amount) as amount, sum(amount_btc) as amount_btc, sum(course)/count(course) as course, 
			sum(return_course)/count(return_course) as return_course, sum(remainder) as remainder, date_trunc('day', created) as 
			day from balancing where id_ad='609849' and date_trunc('day', created) between '$from' and '$to' 
			group by id_ad, date_trunc('day', created) order by date_trunc('day', created) asc) 
			t2 on date_trunc('day', t1.created_at)=t2.day 
			left join (select id_ad, sum(amount) as amount, sum(amount_btc) as amount_btc, sum(course)/count(course) as course, 
			sum(return_course)/count(return_course) as return_course, sum(remainder) as remainder, date_trunc('day', created) as 
			day from balancing where id_ad='617372' and date_trunc('day', created) between '$from' and '$to' 
			group by id_ad, date_trunc('day', created) order by date_trunc('day', created) asc) 
			t3 on date_trunc('day', t1.created_at)=t3.day
			where t1.id_ad in (609849, 	617372) and status=9 and date_trunc('day', t1.created_at) between '$from' and '$to'
			group by date_trunc('day', t1.created_at), t2.amount, t2.amount_btc, t2.course, 
			t2.return_course, t2.remainder, t3.amount, t3.amount_btc, t3.course, t3.return_course, t3.remainder order by date_trunc('day', t1.created_at) desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(course)*0.98/count(course) as course, sum(remainder) as remainder from balancing where id_ad='617372' and status='0'";
		$actual_buy=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select (sum(course)/count(course))*1.03 as course, sum(course) as cour, sum(remainder) as remainder from balancing where id_ad='609849' and status='0'";
		$actual_sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.qiwi.transactions_days', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'actual_buy'=>$actual_buy, 'actual_sell'=>$actual_sell]);
	}
	
	public function Qiwiv3(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select date_trunc('day', t1.created_at) as day, coalesce(t2.amount,0) as amount_buy, coalesce(t2.amount_btc,0) as amount_btc_buy, 
			coalesce(t2.course,0) as course_buy, coalesce(t2.return_course,0) as return_course_buy, 
			coalesce(t3.amount,0) as amount_sell, coalesce(t3.amount_btc,0) as amount_btc_sell, 
			coalesce(t3.course,0) as course_sell, coalesce(t3.return_course,0) as return_course_sell, 
			coalesce(t3.profit,0) as profit, t3.details 
			from lbtc_robots t1 
			left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course) as course, 
			avg(return_course) as return_course, date_trunc('day', created) as 
			day from balancing where id_ad='609849' and date_trunc('day', created) between '$from' and '$to' group by id_ad, date_trunc('day', created)) t2 on 
			date_trunc('day', t1.created_at)=t2.day 
			left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course) as course, 
			avg(return_course) as return_course, sum(profit) as profit, array_agg(details) as details, date_trunc('day', created) as 
			day from balancing where id_ad='617372' and date_trunc('day', created) between '$from' and '$to' group by id_ad, date_trunc('day', created)) t3 on 
			date_trunc('day', t1.created_at)=t3.day 

			where t1.id_ad in ('609849', '617372') and status='9' and date_trunc('day', t1.created_at) between '$from' and '$to' group by 
			date_trunc('day', t1.created_at), t2.amount, t2.amount_btc, t2.course, t2.return_course, 
			t3.amount, t3.amount_btc, t3.course, t3.return_course, t3.profit, t3.details order by date_trunc('day', t1.created_at) desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(t1.amount) as amount_buy, sum(t1.amount_btc) as amount_btc_buy, t2.amount as amount_sell, 
t2.amount_btc as amount_btc_sell, t2.profit
from balancing t1 left join (select sum(amount) as amount, sum(amount_btc) as 
amount_btc, 
sum(profit) as profit
from balancing where id_ad='617372' and date_trunc('day', created) between '$from' and '$to' group by id_ad) t2 on 1=1 
where t1.id_ad='609849' and date_trunc('day', t1.created) between '$from' and '$to' group by t2.amount, 
t2.amount_btc, t2.profit";
		$all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.qiwi.transactions_days_v3', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'all'=>$all]);
	}
	
	public function Qiwiv3Detail($date){
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select * from balancing where id_ad='609849' and date_trunc('day', created)='$date' order by created desc";
		$buy_stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(amount) as amount, sum(amount_btc) as amount_btc, case when (count(course)>0) then sum(course)/count(course) end as course 
		from balancing where id_ad='609849' and date_trunc('day', created)='$date'";
		$buy_stats_all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		
		$sql="select * from balancing where id_ad='617372' and date_trunc('day', created)='$date' order by created desc";
		$sell_stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(amount) as amount, sum(amount_btc) as amount_btc, case when (count(course)>0) then sum(course)/count(course) end as course, 
		case when (count(return_course)>0) then sum(return_course)/count(return_course) end as return_course, sum(profit) as profit from balancing where id_ad='617372' and date_trunc('day', created)='$date'";
		$sell_stats_all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.qiwi.transactions_days_v3_detail', ['buy_stats'=>$buy_stats, 'buy_stats_all'=>$buy_stats_all, 'sell_stats'=>$sell_stats, 'sell_stats_all'=>$sell_stats_all]);
	}
	
	public function Yandexv3(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select date_trunc('day', t1.created_at) as day, coalesce(t2.amount,0) as amount_buy, coalesce(t2.amount_btc,0) as amount_btc_buy, 
			coalesce(t2.course,0) as course_buy, coalesce(t2.return_course,0) as return_course_buy, 
			coalesce(t3.amount,0) as amount_sell, coalesce(t3.amount_btc,0) as amount_btc_sell, 
			coalesce(t3.course,0) as course_sell, coalesce(t3.return_course,0) as return_course_sell, 
			coalesce(t3.profit,0) as profit, t3.details 
			from lbtc_robots t1 
			left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course) as course, 
			avg(return_course) as return_course, date_trunc('day', created) as 
			day from balancing where id_ad='609928' and date_trunc('day', created) between '$from' and '$to' group by id_ad, date_trunc('day', created)) t2 on 
			date_trunc('day', t1.created_at)=t2.day 
			left join (select sum(amount) as amount, sum(amount_btc) as amount_btc, avg(course) as course, 
			avg(return_course) as return_course, sum(profit) as profit, array_agg(details) as details, date_trunc('day', created) as 
			day from balancing where id_ad='609305' and date_trunc('day', created) between '$from' and '$to' group by id_ad, date_trunc('day', created)) t3 on 
			date_trunc('day', t1.created_at)=t3.day 

			where t1.id_ad in ('609928', '609305') and status='9' and date_trunc('day', t1.created_at) between '$from' and '$to' group by 
			date_trunc('day', t1.created_at), t2.amount, t2.amount_btc, t2.course, t2.return_course, 
			t3.amount, t3.amount_btc, t3.course, t3.return_course, t3.profit, t3.details order by date_trunc('day', t1.created_at) desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(t1.amount) as amount_buy, sum(t1.amount_btc) as amount_btc_buy, t2.amount as amount_sell, 
t2.amount_btc as amount_btc_sell, t2.profit
from balancing t1 left join (select sum(amount) as amount, sum(amount_btc) as 
amount_btc, 
sum(profit) as profit
from balancing where id_ad='609305' and date_trunc('day', created) between '$from' and '$to' group by id_ad) t2 on 1=1 
where t1.id_ad='609928' and date_trunc('day', t1.created) between '$from' and '$to' group by t2.amount, 
t2.amount_btc, t2.profit";
		$all=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.yandex.transactions_days_v3', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'all'=>$all]);
	}
	
	public function Yandexv2(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select date_trunc('day', t1.created_at) as day, coalesce(t2.amount,0) as amount_buy, coalesce(t2.amount_btc,0) as amount_btc_buy, 
			coalesce(t2.course,0) as course_buy, coalesce(t2.return_course,0) as return_course_buy, coalesce(t2.remainder,0) as remainder_buy, 
			case when (coalesce(t2.return_course,0)>0 and coalesce(t2.course,0)>0) 
			then (coalesce(t2.return_course,0)-coalesce(t2.course,0))/coalesce(t2.return_course,0)*100 end as prosent_buy,
			coalesce(t3.amount,0) as amount_sell, coalesce(t3.amount_btc,0) as amount_btc_sell, 
			coalesce(t3.course,0) as course_sell, coalesce(t3.return_course,0) as return_course_sell, 
			case when (coalesce(t3.return_course,0)>0 and coalesce(t3.course,0)>0) 
			then (coalesce(t3.course,0)-coalesce(t3.return_course,0))/coalesce(t3.course,0)*100 end as prosent_sell,
			coalesce(t3.remainder,0) as remainder_sell 
			from lbtc_robots t1 
			left join (select id_ad, sum(amount) as amount, sum(amount_btc) as amount_btc, sum(course)/count(course) as course, 
			sum(return_course)/count(return_course) as return_course, sum(remainder) as remainder, date_trunc('day', created) as 
			day from balancing where id_ad='609928' and date_trunc('day', created) between '$from' and '$to' 
			group by id_ad, date_trunc('day', created) order by date_trunc('day', created) asc) 
			t2 on date_trunc('day', t1.created_at)=t2.day 
			left join (select id_ad, sum(amount) as amount, sum(amount_btc) as amount_btc, sum(course)/count(course) as course, 
			sum(return_course)/count(return_course) as return_course, sum(remainder) as remainder, date_trunc('day', created) as 
			day from balancing where id_ad='609305' and date_trunc('day', created) between '$from' and '$to' 
			group by id_ad, date_trunc('day', created) order by date_trunc('day', created) asc) 
			t3 on date_trunc('day', t1.created_at)=t3.day
			where t1.id_ad in (609928, 	609305) and status=9 and date_trunc('day', t1.created_at) between '$from' and '$to'
			group by date_trunc('day', t1.created_at), t2.amount, t2.amount_btc, t2.course, 
			t2.return_course, t2.remainder, t3.amount, t3.amount_btc, t3.course, t3.return_course, t3.remainder order by date_trunc('day', t1.created_at) desc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="select sum(course)*0.985/count(course) as course, sum(remainder) as remainder from balancing where id_ad='609305' and status='0'";
		$actual_buy=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select (sum(course)/count(course))*1.03 as course, sum(course) as cour, sum(remainder) as remainder from balancing where id_ad='609928' and status='0'";
		$actual_sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		return view('lbtc.yandex.transactions_days', ['stats'=>$stats, 'from'=>$from, 'to'=>$to, 'actual_buy'=>$actual_buy, 'actual_sell'=>$actual_sell]);
	}
	
}
