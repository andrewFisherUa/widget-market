<?php

namespace App\LocalBtc\Parsers;

use Illuminate\Database\Eloquent\Model;

class YandexSell extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function limitesActual($id_ad, $lbtckey, $lbtcsecret, $limite, $t_price){
		$ad=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', $id_ad)->first();
		if (!$ad){
			return;
		}
		//var_dump(123);
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update local_ads set temp_price=?, prosent=?, min_amount=?, max_amount=?, visible=?
			WHERE id_ad=?";
		$sthUpdate=$pdo->prepare($sql);
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
		if (!isset($result->data->ad_list[0]->data->temp_price)){
			var_dump('не вернулась наша сделка селл');
			return;
		}
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$procent=preg_replace("/[^.0-9]/", '', $formula);
		$usd=$price/($btc*$procent);
		$t_procent=$t_price/($btc*$usd);
		$new_procent=$t_procent;
		$url='/api/ad/' . $ad->id_ad . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		if ($limite>1000){
			$visible=1;
			$array_message=array(
			'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .'',
			'lat'=>$result->data->ad_list[0]->data->lat,
			'lon'=>$result->data->ad_list[0]->data->lon,
			'city'=>$result->data->ad_list[0]->data->city,
			'location_string'=>$result->data->ad_list[0]->data->location_string,
			'countrycode'=>$result->data->ad_list[0]->data->countrycode,
			'currency'=>$result->data->ad_list[0]->data->currency,
			'account_info'=>'',
			'bank_name'=>$result->data->ad_list[0]->data->bank_name,
			'msg'=>$result->data->ad_list[0]->data->msg,
			'min_amount'=>$result->data->ad_list[0]->data->min_amount,
			'max_amount'=>$limite,
			'visible'=>$visible
			);
		}
		else{
			$visible=0;
			$array_message=array(
			'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .'',
			'lat'=>$result->data->ad_list[0]->data->lat,
			'lon'=>$result->data->ad_list[0]->data->lon,
			'city'=>$result->data->ad_list[0]->data->city,
			'location_string'=>$result->data->ad_list[0]->data->location_string,
			'countrycode'=>$result->data->ad_list[0]->data->countrycode,
			'currency'=>$result->data->ad_list[0]->data->currency,
			'account_info'=>'',
			'bank_name'=>$result->data->ad_list[0]->data->bank_name,
			'msg'=>$result->data->ad_list[0]->data->msg,
			'min_amount'=>$result->data->ad_list[0]->data->min_amount,
			'max_amount'=>$limite,
			'visible'=>$visible
			);
		}
		
		
		$message=http_build_query($array_message);
		$apiauth = $nonce.$lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_POSTFIELDS => $message,
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
		if (!isset($result->data->ad_list[0]->data->trade_type)){
			var_dump('произошла ошибка при попытке переписать наши значения селл');
			return;
		}
		if ($result->data->ad_list[0]->data->trade_type=='ONLINE_BUY'){
			$procent=100-$new_procent*100;
		}
		else{
			$procent=$new_procent*100-100;
		}
		if ($result->data->ad_list[0]->data->visible){
			$status=1;
		}
		else{
			$status=0;
		}
		$sql="update local_ads set temp_price=?, prosent=?, min_amount=?, max_amount=?, visible=?
			WHERE id_ad=?";
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->min_amount, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		return;
	}
	
	public function limitesSell($id_ad, $lbtckey, $lbtcsecret, $limite, $t_price){
		$ad=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', $id_ad)->first();
		if (!$ad){
			return;
		}
		//var_dump(123);
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update local_ads set temp_price=?, prosent=?, min_amount=?, max_amount=?, visible=?
			WHERE id_ad=?";
		$sthUpdate=$pdo->prepare($sql);
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
		if (!isset($result->data->ad_list[0]->data->temp_price)){
			var_dump('не вернулась наша сделка селл');
			return;
		}
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$procent=preg_replace("/[^.0-9]/", '', $formula);
		$usd=$price/($btc*$procent);
		$t_procent=$t_price/($btc*$usd);
		$min=1+($ad->min/100);
		var_dump($min);
		var_dump($t_procent);
		if($min>=$t_procent){
			$new_procent=$min;
		}
		else{
			$new_procent=$t_procent+$ad->step;
		}
		//$new_procent=$t_procent;
		$url='/api/ad/' . $ad->id_ad . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		if ($limite>1000){
			$visible=1;
			$array_message=array(
			'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .'',
			'lat'=>$result->data->ad_list[0]->data->lat,
			'lon'=>$result->data->ad_list[0]->data->lon,
			'city'=>$result->data->ad_list[0]->data->city,
			'location_string'=>$result->data->ad_list[0]->data->location_string,
			'countrycode'=>$result->data->ad_list[0]->data->countrycode,
			'currency'=>$result->data->ad_list[0]->data->currency,
			'account_info'=>'',
			'bank_name'=>$result->data->ad_list[0]->data->bank_name,
			'msg'=>$result->data->ad_list[0]->data->msg,
			'min_amount'=>$result->data->ad_list[0]->data->min_amount,
			'max_amount'=>$limite,
			'visible'=>$visible
			);
		}
		else{
			$visible=1;
			$array_message=array(
			'price_equation'=>'btc_in_usd*USD_in_RUB*' . $new_procent .'',
			'lat'=>$result->data->ad_list[0]->data->lat,
			'lon'=>$result->data->ad_list[0]->data->lon,
			'city'=>$result->data->ad_list[0]->data->city,
			'location_string'=>$result->data->ad_list[0]->data->location_string,
			'countrycode'=>$result->data->ad_list[0]->data->countrycode,
			'currency'=>$result->data->ad_list[0]->data->currency,
			'account_info'=>'',
			'bank_name'=>$result->data->ad_list[0]->data->bank_name,
			'msg'=>$result->data->ad_list[0]->data->msg,
			'min_amount'=>$result->data->ad_list[0]->data->min_amount,
			'max_amount'=>$limite,
			'visible'=>$visible
			);
		}
		
		
		$message=http_build_query($array_message);
		$apiauth = $nonce.$lbtckey.$url.$message;
		$signature = strtoupper(hash_hmac('sha256',$apiauth,$lbtcsecret));
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_POSTFIELDS => $message,
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
		if (!isset($result->data->ad_list[0]->data->trade_type)){
			var_dump('произошла ошибка при попытке переписать наши значения селл');
			return;
		}
		if ($result->data->ad_list[0]->data->trade_type=='ONLINE_BUY'){
			$procent=100-$new_procent*100;
		}
		else{
			$procent=$new_procent*100-100;
		}
		if ($result->data->ad_list[0]->data->visible){
			$status=1;
		}
		else{
			$status=0;
		}
		$sql="update local_ads set temp_price=?, prosent=?, min_amount=?, max_amount=?, visible=?
			WHERE id_ad=?";
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->min_amount, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		return;
	}
}
