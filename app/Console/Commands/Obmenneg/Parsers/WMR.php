<?php

namespace App\Console\Commands\Obmenneg\Parsers;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class WMR extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:wmr';

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
	//для сделок 635074 и 632297
	private $lbtckey='af94813262d92d70b766c17733375740'; //ключ localbitcoins parsers_wmr
	private $lbtcsecret='02bbc55bfc69bef3f0ceaf9a13318c1be7613ac0224bb63f1a5e873fbcafa4a5'; //секрет localbitcoins parsers_wmr
    public function handle()
    {
	
		$ads=\DB::connection('obmenneg')->table('all_ads')->whereIn('id_ads', ['635074', '632297'])->get();
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into wmr_offers_tmp (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
			select ?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
		$sql="update all_ads set temp_price=?, prosent=?
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
						\App\Lbtc\Parsers\Wmr::getInstance()->index($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
					}
				}
				else{
					if ($i==0){
						\App\Lbtc\Parsers\Wmr::getInstance()->indexOff($ad->id_ads, $this->lbtckey, $this->lbtcsecret);
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
		ALTER TABLE wmr_offers RENAME TO wmr_offers_temp;
		ALTER TABLE wmr_offers_tmp RENAME TO wmr_offers;
		ALTER TABLE wmr_offers_temp RENAME TO wmr_offers_tmp;
		truncate TABLE wmr_offers_tmp;
		';
		$pdo->exec($sql);
	}
}
