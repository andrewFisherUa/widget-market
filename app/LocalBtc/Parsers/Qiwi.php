<?php

namespace App\LocalBtc\Parsers;

use Illuminate\Database\Eloquent\Model;

class Qiwi extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	
	public function limites($lbtckey, $lbtcsecret, $limite){
		$url='/api/dashboard/';
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
		if (!isset($result->data->contact_count)){
			var_dump('не нашел сделки');
			return $limite;
		}
		if ($result->data->contact_count>0){
			foreach ($result->data->contact_list as $ads){
				if (!isset($ads->data->advertisement->payment_method)){
					continue;
				}
				if ($ads->data->advertisement->payment_method=="QIWI" and $ads->data->advertisement->trade_type=="ONLINE_BUY" and $ads->data->currency=="RUB"){
					$limite['new_buy']=$limite['new_buy']-$ads->data->amount;
				}
				if ($ads->data->advertisement->payment_method=="QIWI" and $ads->data->advertisement->trade_type=="ONLINE_SELL" and $ads->data->currency=="RUB"){
					$limite['new_shell']=$limite['new_shell']-$ads->data->amount;
				}
			}
		}
		return $limite;
	}
	
	public function indexOff($id_ad, $lbtckey, $lbtcsecret)
    {	
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update local_ads set temp_price=?, prosent=?, min_amount=?, max_amount=?, visible=?
			WHERE id_ad=?";
		$sthUpdate=$pdo->prepare($sql);
		$ad=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', $id_ad)->first();
		if (!$ad){
			return;
		}

		$url='/api/ad-get/' . $ad->id_ad . '/';
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
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$new_procent=preg_replace("/[^.0-9]/", '', $formula);
		if ($result->data->ad_list[0]->data->trade_type=='ONLINE_BUY'){
			$new_procent=1-($ad->min/100);
			$procent=100-$new_procent*100;
		}
		else{
			$new_procent=1+($ad->min/100);
			$procent=$new_procent*100-100;
		}
		
		if ($result->data->ad_list[0]->data->visible){
			$status=1;
		}
		else{
			$status=0;
		}
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->min_amount, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		$url='/api/ad-equation/' . $ad->id_ad . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$array_message=array(
				'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .''
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
		return;
	}
}
