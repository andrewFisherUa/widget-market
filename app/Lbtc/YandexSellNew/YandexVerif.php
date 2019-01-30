<?php

namespace App\Lbtc\YandexSellNew;

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
	
	public function index($yandextoken, $comment, $lbtckey, $lbtcsecret, $contact_id){
		$api = new API($yandextoken);
		$operation_history = $api->operationHistory(array("records"=>30, "details"=>"true"));
		foreach ($operation_history->operations as $operat){
			if (isset($operat->message)){
				if (trim($operat->message)==$comment and $operat->status=="success"){
					$rob=\App\Lbtc\LbtcRobot::where('referance_code', $comment)->where('amount', $operat->amount)->first();
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
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', '2')->first();
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
			
			return $rob->status;
		}	
	}
}
