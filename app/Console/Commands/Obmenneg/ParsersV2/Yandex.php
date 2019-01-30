<?php

namespace App\Console\Commands\Obmenneg\ParsersV2;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use \YandexMoney\API;

class Yandex extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:yandex_v2';

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
	//для сделок 609928 и 609305
	private $lbtckey='706b244f398b8cc33b83abcdafb2c041'; //ключ localbitcoins parsers_yandex
	private $lbtcsecret='b1ce1a03fa8b55eda27b4d16e20945c55c060e5b30cea6dfb319a98d2d148e23'; //секрет localbitcoins parsers_yandex
	//private $yandex='410011520925164.828062B84FD6997D29F145E6182152FF1C11A2CAC5B004D6FD270A2D72DFE8E38F41AEC4C5D007721C976854CA5AF420ADE572FA69439E8DC7F844A7B4F5F9AB743555D8BB4B5E85B807C8DCB397FB8123595D06F8F06890C257A95CD89B8E2AE887F06F560113F542EF74E219A2806855CD0281D43DC5CD27C1550EB4E33A09'; //токен яндекс деньги
    private $yandex='410011520925164.3EDFC615F1934DC1EEF9DEB06997B3F206E5E35E05B85900DDB02E6A7C0A17BAEAB702437F77AA59FC22FB647573988E6FAE6E75AC2A4E7243CE3259A27B44DCB6095D8AEF0B8C7F062A793F81BBA96915A99284D4185EEE6BEBF330210E13947901DE58B94F69C1976B00FFE9DF13D51C817C7115953295016D74B9EDD15295'; //токен яндекс деньги
	//private $yandex='410015804466025.BA8B13E49187D6C9A0C77E73317FFBE6613BC09673E04448726D510B4EEBEF985781F76631C8A2C1310DA593977C40073F1F53ED93608E4F1640197C5932F58C5609802D0CAB23C3217C4B34AA207EA189CF26B242224A56CFD68082D584C251F88D4BB2349C3EDF34832F8DF3D22E236CE2598FBC45A613F404AF4D48583FC5';
	public function handle()
    {
		
		$api = new API($this->yandex);
		$acount_info = $api->accountInfo();
		$yandexbalance=$acount_info->balance_details->total;
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="update local_ads set balance=?
			WHERE id_ad=?";
		$sthUpdate=$pdo->prepare($sql);
		$sthUpdate->execute([$yandexbalance, '609305']);
		$sthUpdate->execute([$yandexbalance, '609928']);
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', '2')->first();
		$new_shell=intval($limite->limite - $yandexbalance);
		if ($new_shell<0){
			$new_shell=0;
		}
		if ($yandexbalance<$limite->limite){
			$new_buy=intval($yandexbalance);
		}
		else{
			$new_buy=intval($limite->limite);
		}
		\DB::connection('obmenneg')->table('limites')->where('id', '2')->update(['shell'=>$new_shell, 'buy'=>$new_buy]);
		if ($new_buy<1000){
			$new_buy=1000;
		}
		if ($new_shell<1000){
			$new_shell=1000;
		}
		$limites['new_buy']=$new_buy;
		$limites['new_shell']=$new_shell;
				
		$ads=\DB::connection('obmenneg')->table('local_ads')->whereIn('id_ad', ['609305', '609928'])->get();
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into yandex_offers_tmp (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
			select ?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
		$sql="update all_ads set actual_price=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		//var_dump($limites);
		$limites=\App\LocalBtc\Parsers\Yandex::getInstance()->limites($this->lbtckey, $this->lbtcsecret, $limites);
		//var_dump($limites);
		//exit;
		foreach ($ads as $ad){
			if ($ad->trade_type=='ONLINE_SELL'){
				$url='/buy-bitcoins-online/' . $ad->valut . '/' . $ad->provider . '/.json';
			}
			elseif ($ad->trade_type=='ONLINE_BUY'){
				$url='/sell-bitcoins-online/' . $ad->valut . '/' . $ad->provider . '/.json';
			}
			else{
				continue;
			}
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
				CURLOPT_TIMEOUT => 45,
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
			$i=0;
			foreach ($result->data->ad_list as $k=>$res){
				if ($res->data->max_amount < $ad->min_max_amount){
					continue;
				}
				if ($i>9){
					continue;
				}
				if ($res->data->profile->username=='Obmenneg'){
					continue;
				}
				if ($ad->parser==1){
					//это бай яндекс
					if ($ad->id_ad=='609928'){
						if ($ad->position-1==$i){
							if ($ad->actual==1){
								if ($ad->actual_price>0){
									$pr=1-($ad->pr_actual_price/100);
									$kurs=$ad->actual_price*$pr;
									if ($kurs < $res->data->temp_price){
										var_dump('курс меньше');
										\App\LocalBtc\Parsers\YandexBuy::getInstance()->limitesActual($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_buy'], $kurs);
									}
									else{
										var_dump('курс больше');
										\App\LocalBtc\Parsers\YandexBuy::getInstance()->limitesBuy($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_buy'], $res->data->temp_price);
									}
								}
								else{
									var_dump('не установлен актуальный курс');
									\App\LocalBtc\Parsers\YandexBuy::getInstance()->limitesBuy($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_buy'], $res->data->temp_price);
								}
							}
							else{
								var_dump('выключен актуальный курс');
								\App\LocalBtc\Parsers\YandexBuy::getInstance()->limitesBuy($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_buy'], $res->data->temp_price);
							}
						}
					}
					//это селл яндекс
					if ($ad->id_ad=='609305'){
						if ($ad->position-1==$i){
							if ($ad->actual==1){
								if ($ad->actual_price>0){
									$pr=1+($ad->pr_actual_price/100);
									$kurs=$ad->actual_price*$pr;
									if ($kurs>$res->data->temp_price){
										var_dump('курс больше');
										\App\LocalBtc\Parsers\YandexSell::getInstance()->limitesActual($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_shell'], $kurs);
									}
									else{
										var_dump('курс меньше');
										\App\LocalBtc\Parsers\YandexSell::getInstance()->limitesSell($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_shell'], $res->data->temp_price);
									}
								}
								else{
									var_dump('не установлен актуальный курс');
									\App\LocalBtc\Parsers\YandexSell::getInstance()->limitesSell($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_shell'], $res->data->temp_price);
								}
							}
							else{
								var_dump('выключен актуальный курс');
								\App\LocalBtc\Parsers\YandexSell::getInstance()->limitesSell($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $limites['new_shell'], $res->data->temp_price);
							}
						}
					}
				}
				else{
					if ($i==0){
						\App\LocalBtc\Parsers\Yandex::getInstance()->indexOff($ad->id_ad, $this->lbtckey, $this->lbtcsecret);
					}
				}
				
				
				if ($res->data->bank_name){
					$name=$res->data->trade_type .' '. $res->data->bank_name .' '. $res->data->currency;
				}
				else{
					$name=$res->data->trade_type .' '. $res->data->online_provider .' '. $res->data->currency;
				}
				if ($i<5){
					$sthInsert->execute([$res->data->ad_id, $res->data->profile->username, $name, $res->data->temp_price, $ad->id_ad, $res->data->min_amount, $res->data->max_amount]);
				}
				$i++;
			}
		}
		$sql='
		ALTER TABLE yandex_offers RENAME TO yandex_offers_temp;
		ALTER TABLE yandex_offers_tmp RENAME TO yandex_offers;
		ALTER TABLE yandex_offers_temp RENAME TO yandex_offers_tmp;
		truncate TABLE yandex_offers_tmp;
		';
		$pdo->exec($sql);
		var_dump('дошел до конца');
	}
}
