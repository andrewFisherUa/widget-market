<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class LocalAds extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:local_ads';

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
			CURLOPT_TIMEOUT => 50,
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
		if (!$result){
			var_dump('Вернулся пустой ответ');
			exit;
		}
		if (!isset($result->data->ad_list)){
			var_dump('Не нашел сделок');
			exit;
		}
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into local_ads (id_ad,name,provider,trade_type,valut,visible,temp_price)
			select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM local_ads WHERE id_ad=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update local_ads set visible=?
			WHERE id_ad=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($result->data->ad_list as $ad){
			if ($ad->data->ad_id!='609849' and $ad->data->ad_id!='617372' and $ad->data->ad_id!='609928' and $ad->data->ad_id!='609305' 
			and $ad->data->ad_id!='635074' and $ad->data->ad_id!='632297' and $ad->data->ad_id != '666810'){
				continue;
			}
			if ($ad->data->bank_name){
				$name=$ad->data->trade_type .' '. $ad->data->bank_name .' '. $ad->data->currency;
			}
			else{
				$name=$ad->data->trade_type .' '. $ad->data->online_provider .' '. $ad->data->currency;
			}
			
			if ($ad->data->visible){
				$status=1;
			}
			else{
				$status=0;
			}
			$provider=\App\Obmenneg\Lbtc\Provider::getInstance()->selectProvider($ad->data->online_provider);
			$sthUpdate->execute([$status, $ad->data->ad_id]);
			$sthInsert->execute([$ad->data->ad_id, $name, $provider, $ad->data->trade_type, $ad->data->currency, $status, $ad->data->temp_price, $ad->data->ad_id]);
		}
	}
}
