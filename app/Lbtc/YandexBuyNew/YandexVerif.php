<?php

namespace App\Lbtc\YandexBuyNew;

use Illuminate\Database\Eloquent\Model;
use \YandexMoney\API;

class YandexVerif extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function index($yandextoken, $contact_id){
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		if (!$rob){
			return;
		}
		$api = new API($yandextoken);
		$request_payment = $api->requestPayment(array(
			"pattern_id" => "p2p",
			"to" => "" . $rob->purse . "",
			"amount_due" => "" . $rob->amount . "",
			"comment" => "перевод",
			"message" => ""
		));
		$process_payment = $api->processPayment(array(
			"request_id" => $request_payment->request_id,
		));
		if ($process_payment->status=="success"){
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=6;
			$rob->save();
			return $rob->status;
		}
		else{
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=4;
			$rob->save();
			\App\Lbtc\YandexBuyNew\Sms::getInstance()->problem($ads->data->contact_id);
			return 4;
		}
	}
	
	public function balance($lbtckey, $lbtcsecret, $yandextoken, $contact_id, $summa){
		$api = new API($yandextoken);
		$acount_info = $api->accountInfo();
		if ($acount_info->balance_details->total<$summa+$summa*0.005){
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
	
	public function limite($lbtckey, $lbtcsecret, $contact_id, $summa){
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', '2')->first();
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
