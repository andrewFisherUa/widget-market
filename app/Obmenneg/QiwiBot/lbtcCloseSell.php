<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class lbtcCloseSell extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function closeSell($lbtckey, $lbtcsecret, $chat_id){
		$transaction = \App\Obmenneg\QiwiBot\lbtcTransaction::where('id_contact', $chat_id)->where('status', 2)->first();
		if ($transaction){
			$url='/api/contact_release/'.$chat_id.'/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,6);
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
			if ($result->data->message=="The escrow of contact has been released successfully."){
				$transaction->status=3;
				$transaction->save();
			}
		}
	}
}
