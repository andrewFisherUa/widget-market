<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class lbtcOpenBoard extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function collectionBoard($lbtckey, $lbtcsecret, $ad_id){
		$url='/api/dashboard/';
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
		foreach ($result->data->contact_list as $id_contact){
			if ($id_contact->data->advertisement->payment_method=="QIWI"){
				foreach ($ad_id as $key=>$ad){
					if ($key==$id_contact->data->advertisement->id){
						$ad_id[$key]['data'][$id_contact->data->contact_id]['summa']=$id_contact->data->amount;
						$ad_id[$key]['data'][$id_contact->data->contact_id]['payment_completed_at']=$id_contact->data->payment_completed_at;
					}
				}
			}
		}
		return $ad_id;
	}
}
