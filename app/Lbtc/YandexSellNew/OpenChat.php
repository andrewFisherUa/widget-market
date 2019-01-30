<?php

namespace App\Lbtc\YandexSellNew;

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
		Ты должен перечислить мне: " . $summa . " руб. на YandexMoney: 410011520925164 
		С указанием комментария: " . $reference . " 
		Оплачивай одной цельной суммой, частями робот не засчитает оплату.
		Не допускай ошибок при оплате и не забудь указать комметарий, иначе я не увижу поступления и не переведу тебе монетки!\n
		После оплаты не забудь нажать кнопку 'Я заплатил' 
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
		Пожалуйста, оплатите заявку или отмените сделку";
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
	
	public function third($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_message_post/' . $contact_id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$client_message="
		Денежные средства не были перечислены! 
		Для получения BTC настоятельно рекомендую произвести оплату или отмените сделку.
		В противном случае я начну 'Спор'
		Возможно Вы не указали комментарий к Вашему переводу или внесли оплату частями
		В этом случае сделку сможет провести только оператор. Рабочие часы (с 9-00 по 22-00 МСК).
		";
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
		$rob->status=4;
		$rob->save();
		return $rob->status;
	}
	
	public function end($lbtckey, $lbtcsecret, $contact_id){
		$url='/api/contact_message_post/' . $contact_id . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$client_message="
		Биткоины переведены. Спасибо за сделку, будем очень благодарны за отзыв!";
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
		$rob=\App\Lbtc\LbtcRobot::where('contact_id', $contact_id)->first();
		$rob->status=9;
		$rob->save();
	}
}
