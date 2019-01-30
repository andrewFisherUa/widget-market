<?php

namespace App\Lbtc\QiwiBuyNew;

use Illuminate\Database\Eloquent\Model;

class QiwiVerif extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function index($qiwitoken, $contact_id){
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		if (!$rob){
			return;
		}
		
		$url='https://edge.qiwi.com/sinap/providers/99/onlineCommission';
		$curl = curl_init($url);
		$nonce=1000*time();
		$array_message=array(
			'account'=>'' . $rob->purse . '',
			'paymentMethod'=>array(
				'type'=>'Account',
				'accountId'=>'643',
			),
			'purchaseTotals'=>array(
				'total'=>array(
					'amount'=>'' . $rob->amount . '',
					'currency'=>'643'
				)
			)
		);
		$message=json_encode($array_message);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-type: application/json',
			'Authorization: Bearer '.$qiwitoken
			),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		//var_dump($result);
		$commission=$result->qwCommission->amount;
		$sum=$rob->amount;
		
		$url='https://edge.qiwi.com/sinap/api/v2/terms/99/payments';
		$curl = curl_init($url);
		$nonce=1000*time();
		$array_message=array(
			'id'=>'' . $nonce . '',
			'sum'=>array(
				'amount'=>'' . $sum . '',
				'currency'=>'643'
			),
			'paymentMethod'=>array(
				'type'=>'Account',
				'accountId'=>'643',
			),
			'fields'=>array(
				'account'=>'' . $rob->purse . ''
			)
		);
		$message=json_encode($array_message);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-type: application/json',
			'Authorization: Bearer '.$qiwitoken
			),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		/*if ($result->message="Недостаточно средств "){
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=200;
			$rob->save();
			return 200;
		}*/
		if ($result->transaction->state->code=='Accepted'){
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=6;
			$rob->save();
			return $rob->status;
		}
		else{
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=4;
			$rob->save();
			\App\Lbtc\QiwiBuyNew\Sms::getInstance()->problem($ads->data->contact_id);
			return 4;
		}
	}
	
	public function balance($lbtckey, $lbtcsecret, $qiwitoken, $contact_id, $summa){
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
			'Authorization: Bearer '.$qiwitoken
			),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		foreach ($result->accounts as $account){
			if ($account->alias!="qw_wallet_rub"){
				continue;
			}
			if ($account->balance->amount<$summa+$summa*0.01){
				$url='/api/contact_message_post/' . $contact_id . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$client_message="
				Извините, я уже исчерпал свой лимит.
				Повторите попытку позже";
				$array_message=array(
					"msg"=>$client_message
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => $message,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
				$result=json_decode($json);
				//var_dump($result);
				$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
				$rob->status=100;
				$rob->save();
				
				$url='/api/contact_cancel/' . $contact_id . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$array_message=array(
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_POST => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_POSTFIELDS => $message,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
				return $rob->status;
			}
		}
	}
	
	public function limite($lbtckey, $lbtcsecret, $contact_id, $summa){
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', '1')->first();
		if ($summa>$limite->buy){
			$url='/api/contact_message_post/' . $contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$client_message="
			Извините, я уже исчерпал свой лимит.
			Повторите попытку позже";
			$array_message=array(
				"msg"=>$client_message
			);
			$message=http_build_query($array_message);
			$apiauth = $nonce.$lbtckey.$url.$message;
			$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
			$curl = curl_init('https://localbitcoins.net'.$url);
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			$result=json_decode($json);
			//var_dump($result);
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=101;
			$rob->save();
			
			$url='/api/contact_cancel/' . $contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$array_message=array(
			);
			$message=http_build_query($array_message);
			$apiauth = $nonce.$lbtckey.$url.$message;
			$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
			$curl = curl_init('https://localbitcoins.net'.$url);
			$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			return $rob->status;
		}	
	}
}
