<?php

namespace App\Console\Commands\Obmenneg\Parsers;

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
    protected $signature = 'LocalBtc:yandex';

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
	private $yandex='410011520925164.828062B84FD6997D29F145E6182152FF1C11A2CAC5B004D6FD270A2D72DFE8E38F41AEC4C5D007721C976854CA5AF420ADE572FA69439E8DC7F844A7B4F5F9AB743555D8BB4B5E85B807C8DCB397FB8123595D06F8F06890C257A95CD89B8E2AE887F06F560113F542EF74E219A2806855CD0281D43DC5CD27C1550EB4E33A09'; //токен яндекс деньги
    public function handle()
    {
		
		$api = new API($this->yandex);
		$acount_info = $api->accountInfo();
		$yandexbalance=$acount_info->balance_details->total;
		
		
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
		
		
	
		$ads=\DB::connection('obmenneg')->table('all_ads')->whereIn('id_ads', ['609928', '609305'])->get();
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into yandex_offers_tmp (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
			select ?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
		$sql="update all_ads set actual_price=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
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
			if ($ad->id_ads=='609305'){
				$sql="select (sum(course)/count(course))*1.03 as course, sum(course) as cour, sum(remainder) as remainder from balancing where id_ad='609928' and status='0'";
				$actual_sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
				$sthUpdate->execute([$actual_sell['course'], $ad->id_ads]);
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
			$i=0;
			\DB::connection("obmenneg")->table('ads_offers')->where('my_id_ad', $ad->id_ads)->delete();
			foreach ($result->data->ad_list as $k=>$res){
				if ($i>9){
					continue;
				}
				if ($res->data->profile->username=='Obmenneg'){
					continue;
				}
				if ($ad->parse_status){
					if ($ad->position-1==$i){
						if ($ad->id_ads=='609928'){
							if ($ad->max_amount==$limites['new_buy']){
								\App\Lbtc\Parsers\Yandex::getInstance()->index($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
							}
							else{
								\App\Lbtc\Parsers\Yandex::getInstance()->limitesBuy($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price, $limites['new_buy']);
							}
						}
						else{
							if ($ad->max_amount==$limites['new_shell'] and $res->data->temp_price>$actual_sell['course']){
								\App\Lbtc\Parsers\Yandex::getInstance()->index($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
							}
							else{
								\App\Lbtc\Parsers\Yandex::getInstance()->limitesShell($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price, $limites['new_shell']);
							}
						}
					}
				}
				else{
					if ($i==0){
						\App\Lbtc\Parsers\Yandex::getInstance()->indexOff($ad->id_ads, $this->lbtckey, $this->lbtcsecret);
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
		}
		$sql='
		ALTER TABLE yandex_offers RENAME TO yandex_offers_temp;
		ALTER TABLE yandex_offers_tmp RENAME TO yandex_offers;
		ALTER TABLE yandex_offers_temp RENAME TO yandex_offers_tmp;
		truncate TABLE yandex_offers_tmp;
		';
		$pdo->exec($sql);
	}
}
