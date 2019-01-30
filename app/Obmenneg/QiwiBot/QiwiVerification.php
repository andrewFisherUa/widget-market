<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class QiwiVerification extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function verification($qiwitoken){
		$url='https://edge.qiwi.com/payment-history/v1/persons/79281131180/payments?rows=50&operation=IN';
		$curl = curl_init($url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
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
		foreach ($result->data as $payment){
			if ($payment->status=="SUCCESS"){
				$transaction = \App\Obmenneg\QiwiBot\lbtcTransaction::where('secret_key', trim($payment->comment))->where('summa', $payment->total->amount)->where('status', 1)->first();
				if ($transaction){
					$transaction->status=2;
					$transaction->save();
				}
			}
		}
	}
}
