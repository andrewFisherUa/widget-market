<?php

namespace App\LocalBtc\QiwiSell;

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
	
	public function index($qiwitoken, $comment, $lbtckey, $lbtcsecret, $contact_id){
		$url='https://edge.qiwi.com/payment-history/v1/persons/79381148114/payments?rows=50&operation=IN';
		$curl = curl_init($url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_INTERFACE => "185.60.135.248",
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
		foreach ($result->data as $payment){
			if ($payment->sum->currency!=643 or $payment->total->currency!=643){
				continue;
			}
			if ($payment->status=="SUCCESS"){
				if (trim($payment->comment)==$comment){
					$rob=\App\LocalBtc\LocalRobot::where('referance_code', $comment)->where('amount', $payment->total->amount)->first();
					if ($rob){
						$rob->status=5;
						$rob->save();
						
						$url='/api/contact_message_post/' . $contact_id . '/';
						$time=microtime();
						$int=substr($time,11);
						$flo=substr($time,2,5);
						$nonce=$int.$flo;
						$client_message="
						Сумма переведена. Вывожу биткоины!";
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
						CURLOPT_TIMEOUT => 30,
						CURLOPT_FILETIME => TRUE,
						CURLOPT_INTERFACE => "185.60.135.248",
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
		}
	}
	
	public function limite($lbtckey, $lbtcsecret, $contact_id, $summa){
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', '1')->first();
		if ($summa>$limite->shell){
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
			CURLOPT_INTERFACE => "185.60.135.248",
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
			$rob=\App\LocalBtc\LocalRobot::where('contact_id', $contact_id)->first();
			$rob->status=101;
			$rob->save();
			
			return $rob->status;
		}	
	}
}
