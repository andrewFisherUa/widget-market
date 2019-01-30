<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class lbtcOpenSell extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function collectionSell($lbtckey, $lbtcsecret){
		$url='/api/ads/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,6);
		$nonce=$int.$flo;
		$array_message=array(
			'visible'=>0,
			'trade_type'=>'ONLINE_SELL',
			'currency'=>'RUB'
		);
		$message=http_build_query($array_message);
		$apiauth = $nonce.$lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_RETURNTRANSFER => 1,
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
		$result=json_decode($json);
		$ad_id=[];
		foreach ($result->data->ad_list as $ad){
			if ($ad->data->online_provider=="QIWI"){
				$ad_id[$ad->data->ad_id]=[];
			}
		}
		return $ad_id;
	}
}
