<?php

namespace App\Lbtc\YandexBuyNew;

use Illuminate\Database\Eloquent\Model;

class OpenChat extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function index($lbtckey, $lbtcsecret, $contact_id, $summa, $reference){
		$url='/api/contact_message_post/' . $contact_id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$client_message="
		Доброго времени суток! 
		Я робот Obmenneg! 
		Пришли мне номер кошелька без пробелов и запятых.
		Я должен перечислить тебе: " . $summa . " руб. 
		Робот на вопросы не отвечает, не задавайте мне вопросов, все условия необходимые для перевода есть в описании!";
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
		$result=json_decode($json);
		if($result->data->message="Message sent successfully."){
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$rob->status=1;
			$rob->save();
			return $rob->status;
		}
	}
	
	public function second($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_message_post/' . $contact_id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$client_message="
		Пожалуйста, напишите номер кошелька или отмените сделку";
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
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		$rob->status=2;
		$rob->save();
		return $rob->status;
	}
	
	public function find($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_messages/' . $contact_id . '/';
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
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
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
		$k=0;
		$l=0;
		foreach ($result->data->message_list as $msg){
			if ($msg->sender->username=='Obmenneg'){
				if (stripos($msg->msg, 'Я робот Obmenneg!') !== false){
					$l=1;
				}
				continue;
			}
			if ($msg->msg){
				if ($l>0){
					$k++;
				}
				$str=preg_replace("/[^0-9]/", '', $msg->msg);
				if (strlen($str)>12){
					$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
					$rob->purse=$str;
					$rob->status=3;
					$rob->save();
					$url='/api/contact_message_post/' . $contact_id . '/';
					$time=microtime();
					$int=substr($time,11);
					$flo=substr($time,2,5);
					$nonce=$int.$flo;
					$client_message="
					Проверим еще раз! Твой номер кошелька: " . $rob->purse . ", я должен отправить " . $rob->amount . " руб.
					Если ты согласен отправь 'да' для перевода средств или 'нет' - и я отменю сделку.";
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
					return $rob->status;
				}
			}
		}
		if ($k>0){
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$url='/api/contact_message_post/' . $contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$client_message="
			Не верный формат ввода, пожалуйста введите свой номер кошелька для перевода.";
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
			return $rob->status;
		}
		
	}
	
	public function find_otv($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_messages/' . $contact_id . '/';
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
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
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
		$k=0;
		$l=0;
		foreach ($result->data->message_list as $msg){
			if ($msg->sender->username=='Obmenneg'){
				if (stripos($msg->msg, 'Проверим еще раз!') !== false){
					$l=1;
				}
				continue;
			}
			if ($msg->msg){
				if ($l>0){
					$k++;
				}
				if (mb_strtolower($msg->msg)=="нет"){
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
					$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
					if ($rob){
						$rob->status=4;
						$rob->save();
					}
					return $rob->status;
				}
				if (mb_strtolower($msg->msg)=="да"){
					$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
					if (!$rob){
						break;
					}
					if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->created_at ." + 25 minutes")){
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
						$rob->status=4;
						$rob->save();
						return $rob->status;
					}
					else{
						$rob->status=5;
						$rob->save();
						return $rob->status;
					}
				}
			}
		}
		
		if ($k>0){
			$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
			$url='/api/contact_message_post/' . $contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$client_message="
			Не верный формат ввода, пожалуйста отправьте 'да' для перевода средств или 'нет' - для отмены сделки.";
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
			return $rob->status;
		}
	}
	
	public function otpusk($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_mark_as_paid/' . $contact_id . '/';
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
		
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		$url='/api/contact_message_post/' . $contact_id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$client_message="
		Я перевел тебе " . $rob->amount . " руб. с кошелька: 410011520925164
		Проверь свой кошелек и отпусти BTC из депонирования!";
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
		$rob->status=7;
		$rob->save();
		return $rob->status;
	}
	
	public function ne_prishli($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_message_post/' . $contact_id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$client_message="
		BTC не были перечислены! 
		Прошу отпустить BTC из депонирования
		В противном случае я начну 'Спор'";
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
		$rob->status=8;
		$rob->save();
		return $rob->status;
	}
	
	public function status($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_info/' . $contact_id . '/';
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
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
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
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		if ($result->data->released_at){
			$rob->status=9;
			$rob->save();
			$url='/api/contact_message_post/' . $contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$client_message="
			Спасибо за сделку! Буду очень признателен, если ты оставишь отзыв!";
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
			return $rob->status;
		}
		return $rob->status;
	}
	
	public function statusSpor($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_info/' . $contact_id . '/';
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
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
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
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		if ($result->data->released_at){
			$rob->status=9;
			$rob->save();
		}
		return $rob->status;
	}
}
