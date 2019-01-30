<?php

namespace App\Lbtc\CabinetParse;

use Illuminate\Database\Eloquent\Model;

class Wmz extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
    public function index($id_ads, $lbtckey, $lbtcsecret)
    {	
		$ad=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', $id_ads)->first();
		if (!$ad){
			return 0;
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into wmz_offers_tmp (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
			select ?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
		$sql="update all_ads set temp_price=?, prosent=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		$url='/buy-bitcoins-online/' . $ad->valut . '/' . $ad->provider . '/.json';
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
		$i=0;
		foreach ($result->data->ad_list as $k=>$res){
			if ($i>9){
				continue;
			}
			if ($res->data->profile->username=='Obmenneg'){
				continue;
			}
			if ($ad->parse_status){
				if ($ad->position-1==$i){
					\App\Lbtc\Parsers\Wmz::getInstance()->index($ad->id_ads, $lbtckey, $lbtcsecret, $res->data->temp_price);
				}
			}
			else{
				if ($i==0){
					\App\Lbtc\Parsers\Wmz::getInstance()->indexOff($ad->id_ads, $lbtckey, $lbtcsecret);
				}
			}
			
			if ($res->data->bank_name){
				$name=$res->data->trade_type .' '. $res->data->bank_name .' '. $res->data->currency;
			}
			else{
				$name=$res->data->trade_type .' '. $res->data->online_provider .' '. $res->data->currency;
			}
			if ($i<5){
			$sthInsert->execute([$res->data->ad_id, $res->data->profile->username, $name, $res->data->temp_price, $ad->id_ads, $res->data->min_amount, $res->data->max_amount]);
			}
			$i++;
		}
		$sql='
		ALTER TABLE wmz_offers RENAME TO wmz_offers_temp;
		ALTER TABLE wmz_offers_tmp RENAME TO wmz_offers;
		ALTER TABLE wmz_offers_temp RENAME TO wmz_offers_tmp;
		truncate TABLE wmz_offers_tmp;
		';
		$pdo->exec($sql);
		return 1;
	}
}
