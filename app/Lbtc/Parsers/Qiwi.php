<?php

namespace App\Lbtc\Parsers;

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
	
    public function index($id_ads, $lbtckey, $lbtcsecret, $res)
    {	
		$ad=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id_ads)->first();
		if (!$ad){
			return;
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update all_ads set temp_price=?, prosent=?, max_amount=?, visible=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		$url='/api/ad-get/' . $ad->id_ads . '/';
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
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$procent=preg_replace("/[^.0-9]/", '', $formula);
		$usd=$price/($btc*$procent);
						
		$procent2=$res/($btc*$usd);
		if ($ad->trade_type=='ONLINE_BUY'){
			$min=1-($ad->min/100);
		}
		else{
			$min=1+($ad->min/100);
		}
		if ($ad->trade_type=='ONLINE_BUY'){
			if($min<=$procent2){
				$new_procent=$min;
			}
			else{
				$new_procent=$procent2+$ad->step;
			}
		}
		else{
			if ($min>=$procent2){
				$new_procent=$min;
			}
			else{
				$new_procent=$procent2+$ad->step;
			}
		}
		$url='/api/ad-equation/' . $ad->id_ads . '/';
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
						
		$url='/api/ad-get/' . $ad->id_ads . '/';
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
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		return;
	}
	
	
	public function limitesBuy($id_ads, $lbtckey, $lbtcsecret, $res, $limites)
    {
		$ad=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id_ads)->first();
		if (!$ad){
			return;
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update all_ads set temp_price=?, prosent=?, max_amount=?, visible=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		$url='/api/ad-get/' . $id_ads . '/';
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
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$procent=preg_replace("/[^.0-9]/", '', $formula);
		$usd=$price/($btc*$procent);
						
		$procent2=$res/($btc*$usd);
		if ($ad->trade_type=='ONLINE_BUY'){
			$min=1-($ad->min/100);
		}
		else{
			$min=1+($ad->min/100);
		}
		if ($ad->trade_type=='ONLINE_BUY'){
			if($min<=$procent2){
				$new_procent=$min;
			}
			else{
				$new_procent=$procent2+$ad->step;
			}
		}
		else{
			if ($min>=$procent2){
				$new_procent=$min;
			}
			else{
				$new_procent=$procent2+$ad->step;
			}
		}
		$url='/api/ad/' . $id_ads . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		if ($limites>1000){
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
			'max_amount'=>$limites,
			'visible'=>$visible
			);
		}
		else{
			$array_message=array(
			'price_equation'=>$result->data->ad_list[0]->data->price_equation,
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
			'max_amount'=>$limites,
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
		
		$url='/api/ad-get/' . $ad->id_ads . '/';
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
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		return;
	}
	
	public function limitesShell($id_ads, $lbtckey, $lbtcsecret, $res, $limites)
    {
		$ad=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id_ads)->first();
		if (!$ad){
			return;
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update all_ads set temp_price=?, prosent=?, max_amount=?, visible=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		$url='/api/ad-get/' . $id_ads . '/';
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
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$procent=preg_replace("/[^.0-9]/", '', $formula);
		$usd=$price/($btc*$procent);
						
		$procent2=$res/($btc*$usd);
		if ($ad->trade_type=='ONLINE_BUY'){
			$min=1-($ad->min/100);
		}
		else{
			$min=1+($ad->min/100);
		}
		if ($ad->trade_type=='ONLINE_BUY'){
			if($min<=$procent2){
				$new_procent=$min;
			}
			else{
				$new_procent=$procent2+$ad->step;
			}
		}
		else{
			if ($min>=$procent2){
				$new_procent=$min;
			}
			else{
				$new_procent=$procent2+$ad->step;
			}
		}
		$url='/api/ad/' . $id_ads . '/';
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		if ($limites>1000 and $ad->on_actual==1 and $res > $ad->actual_price){
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
			'max_amount'=>$limites,
			'details-phone_number'=>$result->data->ad_list[0]->data->account_details->phone_number,
			'visible'=>$visible
			);
		}
		else{
			if ($limites>1000 and $ad->on_actual!=1){
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
				'max_amount'=>$limites,
				'details-phone_number'=>$result->data->ad_list[0]->data->account_details->phone_number,
				'visible'=>$visible
				);
			}
			else{
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
				'max_amount'=>$limites,
				'details-phone_number'=>$result->data->ad_list[0]->data->account_details->phone_number,
				);
			}
			
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
		
		$url='/api/ad-get/' . $ad->id_ads . '/';
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
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		return;
	}
	
	public function indexOff($id_ads, $lbtckey, $lbtcsecret)
    {	
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update all_ads set temp_price=?, prosent=?, max_amount=?, visible=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		$ad=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id_ads)->first();
		if (!$ad){
			return;
		}

		$url='/api/ad-get/' . $ad->id_ads . '/';
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
		$price=$result->data->ad_list[0]->data->temp_price;
		$btc=$result->data->ad_list[0]->data->temp_price_usd;
		$formula=$result->data->ad_list[0]->data->price_equation;
		$new_procent=preg_replace("/[^.0-9]/", '', $formula);
		
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
		$sthUpdate->execute([$result->data->ad_list[0]->data->temp_price, $procent, $result->data->ad_list[0]->data->max_amount, $status, $result->data->ad_list[0]->data->ad_id]);
		return;
	}
}
