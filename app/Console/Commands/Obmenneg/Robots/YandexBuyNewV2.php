<?php

namespace App\Console\Commands\Obmenneg\Robots;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class YandexBuyNewV2 extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:yandex_buy_new_v2';

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
	private $lbtckey='7984487833284d5c79c0f86a35684343'; //ключ localbitcoins yandex_buy_robot
	private $lbtcsecret='8f76856d3d0f6da2d4172cf7910f9788375cda9cee13b892bfd9b9a032632b2d'; //секрет localbitcoins yandex_buy_robot
	//private $yandextoken='410011520925164.828062B84FD6997D29F145E6182152FF1C11A2CAC5B004D6FD270A2D72DFE8E38F41AEC4C5D007721C976854CA5AF420ADE572FA69439E8DC7F844A7B4F5F9AB743555D8BB4B5E85B807C8DCB397FB8123595D06F8F06890C257A95CD89B8E2AE887F06F560113F542EF74E219A2806855CD0281D43DC5CD27C1550EB4E33A09'; //токен яндекс деньги
	private $yandextoken='410011520925164.3EDFC615F1934DC1EEF9DEB06997B3F206E5E35E05B85900DDB02E6A7C0A17BAEAB702437F77AA59FC22FB647573988E6FAE6E75AC2A4E7243CE3259A27B44DCB6095D8AEF0B8C7F062A793F81BBA96915A99284D4185EEE6BEBF330210E13947901DE58B94F69C1976B00FFE9DF13D51C817C7115953295016D74B9EDD15295'; //токен яндекс деньги
	//private $yandextoken='410015804466025.BA8B13E49187D6C9A0C77E73317FFBE6613BC09673E04448726D510B4EEBEF985781F76631C8A2C1310DA593977C40073F1F53ED93608E4F1640197C5932F58C5609802D0CAB23C3217C4B34AA207EA189CF26B242224A56CFD68082D584C251F88D4BB2349C3EDF34832F8DF3D22E236CE2598FBC45A613F404AF4D48583FC5';
    public function handle()
    {

		$ad=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', '609928')->first();
		if ($ad->robot==0){
			var_dump('я выключен');
			exit;
		}
		$url='/api/dashboard/';
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
		if (!isset($result->data->contact_count)){
			var_dump('не нашел активные сделки');
			exit;
		}
		if ($result->data->contact_count==0){
			var_dump('нет активных сделок');
		}
		$sum=$ad->max_amount;
		if ($result->data->contact_count>0){
			foreach ($result->data->contact_list as $ads){
				if (!isset($ads->data->advertisement->payment_method)){
					continue;
				}
				if ($ads->data->advertisement->payment_method!="YANDEXMONEY" or $ads->data->advertisement->trade_type!="ONLINE_BUY" or $ads->data->currency!="RUB"){
					continue;
				}
				$rob=\App\LocalBtc\LocalRobot::where('contact_id', $ads->data->contact_id)->first();
				if (!$rob){
					$rob = new \App\LocalBtc\LocalRobot;
					$rob->id_ad=$ads->data->advertisement->id;
					$rob->contact_id=$ads->data->contact_id;
					$rob->username=$ads->data->seller->username;
					$rob->referance_code=$ads->data->reference_code;
					$rob->currency=$ads->data->currency;
					$rob->amount=$ads->data->amount;
					$rob->amount_btc=$ads->data->amount_btc;
					$rob->save();
				}
				$stat=$rob->status;
				if (!$ads->data->disputed_at){
					$sum=$sum-$rob->amount;
				}
				if ($stat==0){
					if ($sum<0){
						$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->NoMoney($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					}
					if ($stat==0){
						$stat=\App\LocalBtc\YandexBuy\YandexVerif::getInstance()->balance($this->lbtckey, $this->lbtcsecret, $this->yandextoken, $ads->data->contact_id, $ads->data->amount);
					}
					if ($stat==0){
						$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->index($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id, $ads->data->amount, $ads->data->reference_code);
					}
				}
				if ($stat==1){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->find($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					if ($stat==1){
						if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->updated_at ." + 10 minutes")){
							$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->second($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
						}
					}
				}
				if ($stat==2){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->find($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
				if ($stat==3){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->find_otv($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
				if ($stat==5){
					$stat=\App\LocalBtc\YandexBuy\YandexVerif::getInstance()->index($this->yandextoken, $ads->data->contact_id);
				}
				if ($stat==6){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->otpusk($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
				if ($stat==7){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					if ($stat==7){
						if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->updated_at ." + 15 minutes")){
							$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->ne_prishli($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
							\App\LocalBtc\YandexBuy\Sms::getInstance()->index($ads->data->contact_id);
						}
					}
				}
				if ($stat==8){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					if ($stat==8){
						if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->created_at ." + 61 minutes")){
							$stat=\App\LocalBtc\YandexBuy\Spor::getInstance()->index($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
						}
					}
				}
				if ($stat==10){
					$stat=\App\LocalBtc\YandexBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
			}
		}
		$robs=\App\LocalBtc\LocalRobot::where('id_ad', '609928')->where('status', 10)->get();
		foreach ($robs as $rob){
			\App\LocalBtc\YandexBuy\OpenChat::getInstance()->statusSpor($this->lbtckey, $this->lbtcsecret, $rob->contact_id);
		}
		$robs=\App\LocalBtc\LocalRobot::where('id_ad', '609928')->whereIn('status', [7, 8])->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') ."-61 minutes")), date('Y-m-d H:i:s')])->get();
		foreach ($robs as $rob){
			\App\LocalBtc\YandexBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $rob->contact_id);
		}
	}
}
