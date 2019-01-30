<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class Sms extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
	private $lbtckey='bebe9e036aec7e828b4d8495c14df279'; //ключ localbitcoins sms_verif
	private $lbtcsecret='772ca50f333a30aa2c444240ec8a5936657d6d6fb7491fdb4876c48e38d07595'; //секрет localbitcoins sms_verif
	
	private $lbtckey2='dd871d4e0198d71cf105bf8ebc3980ef'; //ключ localbitcoins sms
	private $lbtcsecret2='c5267dc62ad538158e2299b7593da1587fe71826af7eb0d14514c8b68f9dd4d9'; //секрет localbitcoins sms
    public function handle()
    {
		$smspr=\DB::connection('obmenneg')->table('sms')->where('sms', 'sms')->first();
		if ($smspr->value==0){
			exit;
		}
		$url='/api/notifications/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$array_message=array(
		);
		$message=http_build_query($array_message);
		$apiauth = $nonce.$this->lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_TIMEOUT => 40,
		CURLOPT_FILETIME => TRUE,
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$sms=0;
		foreach ($result->data as $notif){
			$pos = strpos($notif->msg, 'Вы получили новое предложение ');
			$pos2 = strpos($notif->msg, 'You have a new offer ');
			if ($pos === false and $pos2 === false){
				continue;
			}	
			if ($notif->read === true){
				continue;
			}
			$url='/api/contact_info/' . $notif->contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$array_message=array(
			);
			$message=http_build_query($array_message);
			$apiauth = $nonce.$this->lbtckey.$url.$message;
			$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
			$curl = curl_init('https://localbitcoins.net'.$url);
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$this->lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			$result=json_decode($json);
			if ($result->data->advertisement->id=='609305' or $result->data->advertisement->id=='609928' or $result->data->advertisement->id=='609849' or $result->data->advertisement->id=='617372'){
				$url='/api/notifications/mark_as_read/' . $notif->id . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$array_message=array(
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$this->lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_POST => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$this->lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
			}
			else{
				$sms+=1;
				if ($result->data->advertisement->trade_type=="LOCAL_SELL" or $result->data->advertisement->trade_type=="LOCAL_BUY"){
					$predlog='1' . $result->data->advertisement->trade_type . '';
				}
				else{
					$predlog='1' . $result->data->advertisement->trade_type . ' ' . $result->data->advertisement->payment_method . '';
				}
				$url='/api/notifications/mark_as_read/' . $notif->id . '/';
				$time=microtime();
				$int=substr($time,11);
				$flo=substr($time,2,5);
				$nonce=$int.$flo;
				$array_message=array(
				);
				$message=http_build_query($array_message);
				$apiauth = $nonce.$this->lbtckey.$url.$message;
				$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
				$curl = curl_init('https://localbitcoins.net'.$url);
				$options = array(
				CURLOPT_POST => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$this->lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
				);
				curl_setopt_array($curl, $options);
				$json=(curl_exec ($curl));
				curl_close($curl);
			}
		}
		if ($sms==1){
			$message=$predlog;
			$array_message=array(
			'login'=>'obmenneg',
			'psw'=>'Qwer1212',
			'phones'=>'79381148114;79889498095',
			'mes'=>$message,
			'charset'=>'utf-8'
			);
			$curl = curl_init('https://smsc.ru/sys/send.php');
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $array_message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			var_dump($json);
		}
		else if ($sms>1){
			$array_message=array(
			'login'=>'obmenneg',
			'psw'=>'Qwer1212',
			'phones'=>'79381148114;79889498095',
			'mes'=>'1 Есть новые предложения',
			'charset'=>'utf-8'
			);
			$curl = curl_init('https://smsc.ru/sys/send.php');
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $array_message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
		}
		
		
		
		$smspr=\DB::connection('obmenneg')->table('sms')->where('sms', 'sms')->first();
		if ($smspr->value==0){
			exit;
		}
		$url='/api/notifications/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$array_message=array(
		);
		$message=http_build_query($array_message);
		$apiauth = $nonce.$this->lbtckey2.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret2));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_TIMEOUT => 40,
		CURLOPT_FILETIME => TRUE,
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey2,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$sms=0;
		foreach ($result->data as $notif){
			$pos = strpos($notif->msg, 'Вы получили новое предложение ');
			$pos2 = strpos($notif->msg, 'You have a new offer ');
			if ($pos === false and $pos2 === false){
				continue;
			}	
			if ($notif->read === true){
				continue;
			}
			$url='/api/contact_info/' . $notif->contact_id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$array_message=array(
			);
			$message=http_build_query($array_message);
			$apiauth = $nonce.$this->lbtckey2.$url.$message;
			$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret2));
			$curl = curl_init('https://localbitcoins.net'.$url);
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$this->lbtckey2,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			$result=json_decode($json);
			$sms+=1;
			if ($result->data->advertisement->trade_type=="LOCAL_SELL" or $result->data->advertisement->trade_type=="LOCAL_BUY"){
				$predlog='2' . $result->data->advertisement->trade_type . '';
			}
			else{
				$predlog='2' . $result->data->advertisement->trade_type . ' ' . $result->data->advertisement->payment_method . '';
			}
			$url='/api/notifications/mark_as_read/' . $notif->id . '/';
			$time=microtime();
			$int=substr($time,11);
			$flo=substr($time,2,5);
			$nonce=$int.$flo;
			$array_message=array(
			);
			$message=http_build_query($array_message);
			$apiauth = $nonce.$this->lbtckey2.$url.$message;
			$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret2));
			$curl = curl_init('https://localbitcoins.net'.$url);
			$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$this->lbtckey2,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
		}
		if ($sms==1){
			$message=$predlog;
			$array_message=array(
			'login'=>'obmenneg',
			'psw'=>'Qwer1212',
			'phones'=>'79381148114;79889498095',
			'mes'=>$message,
			'charset'=>'utf-8'
			);
			$curl = curl_init('https://smsc.ru/sys/send.php');
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $array_message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			var_dump($json);
		}
		else if ($sms>1){
			$array_message=array(
			'login'=>'obmenneg',
			'psw'=>'Qwer1212',
			'phones'=>'79381148114;79889498095',
			'mes'=>'2 Есть новые предложения',
			'charset'=>'utf-8'
			);
			$curl = curl_init('https://smsc.ru/sys/send.php');
			$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $array_message,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
		}
	}
}
