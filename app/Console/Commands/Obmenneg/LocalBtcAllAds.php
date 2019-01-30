<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class LocalBtcAllAds extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:all_ads';

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
	private $lbtckey='86f5573400559dd136ec784f9960a271'; //ключ localbitcoins all_ads
	private $lbtcsecret='d0336f765978f2110f23516a27eb0ea2b1dac58bd12aff3b3d3a69df97023e25'; //секрет localbitcoins all_ads
    public function handle()
    {
		$url='/api/ads/';
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
		$results=json_decode($json);
		
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into all_ads (id_ads,name,visible,temp_price,valut,provider,trade_type,prosent, min_amount, max_amount)
			select ?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM all_ads WHERE id_ads=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update all_ads set name=?, visible=?
			WHERE id_ads=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($results->data->ad_list as $ads){
			if ($ads->data->bank_name){
				$name=$ads->data->trade_type .' '. $ads->data->bank_name .' '. $ads->data->currency;
			}
			else{
				$name=$ads->data->trade_type .' '. $ads->data->online_provider .' '. $ads->data->currency;
			}
			
			if ($ads->data->visible){
				$status=1;
			}
			else{
				$status=0;
			}
			$price=$ads->data->temp_price;
			$btc=$ads->data->temp_price_usd;
			$formula=$ads->data->price_equation;
			$procent=preg_replace("/[^.0-9]/", '', $formula);
			if ($ads->data->trade_type=='ONLINE_BUY' or $ads->data->trade_type=='LOCAL_BUY'){
				$procent=100-$procent*100;
			}
			else{
				$procent=$procent*100-100;
			}
			$provider=\App\Obmenneg\Lbtc\Provider::getInstance()->selectProvider($ads->data->online_provider);
			$sthUpdate->execute([$name, $status, $ads->data->ad_id]);
			$sthInsert->execute([$ads->data->ad_id, $name, $status, $ads->data->temp_price, $ads->data->currency, $provider, $ads->data->trade_type, $procent, $ads->data->min_amount, $ads->data->max_amount, $ads->data->ad_id]);
		}
	}
}
