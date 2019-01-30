<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class lbtcOpenChat extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function collectionChat($lbtckey, $lbtcsecret, $summa, $chat_id, $id){
		$url='/api/contact_messages/'.$chat_id.'/';
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
			CURLOPT_HTTPGET => 1,
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
		$cnt=0;
		foreach ($result->data->message_list as $list){
			if ($list->sender->username=='Obmenneg'){
				$cnt=1;
			}
		}
		if ($cnt==0){
			$url='/api/contact_message_post/'.$chat_id.'/';
			$string=\App\Obmenneg\QiwiBot\lbtcRand::getInstance()->createString();
			$client_message="Здравствуйте\nСекретная строка ".$string."\nКошелек +79281131180";
			$nonce = time();
			$array_message=array(
				'msg'=>$client_message
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
			$transaction = new \App\Obmenneg\QiwiBot\lbtcTransaction;
			$transaction->id_ad=$id;
			$transaction->id_contact=$chat_id;
			$transaction->summa=$summa;
			$transaction->status=0;
			$transaction->secret_key=$string;
			$transaction->save();
		}
	}
}
