<?php

namespace App\Console\Commands\Obmenneg\Robots;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class QiwiBuyNewV2 extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:qiwi_buy_new_v2';

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
	private $lbtckey='7da4a206792933b0c6bcb040b0e3268e'; //ключ localbitcoins qiwi_buy_robot
	private $lbtcsecret='7dbd59f81a4de6553d975d91717df3d4d44a80cf601260280462e772f9fc11e9'; //секрет localbitcoins qiwi_buy_robot
	private $qiwitoken='2260fafba73431387ed8d5c776a322ae'; //токен qiwi для кошелька 79381148114 действует до 08.08.2018
	//'46c80b272ffc67f5c0738fc912763a14'; //токен qiwi для кошелька 79281131180 действует до 15.08.2018
	
    public function handle()
    {

		$ad=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', '609849')->first();
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
		CURLOPT_INTERFACE => "185.60.135.248",
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
				if ($ads->data->advertisement->payment_method!="QIWI" or $ads->data->advertisement->trade_type!="ONLINE_BUY" or $ads->data->currency!="RUB"){
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
						$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->NoMoney($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					}
					if ($stat==0){
						$stat=\App\LocalBtc\QiwiBuy\QiwiVerif::getInstance()->balance($this->lbtckey, $this->lbtcsecret, $this->qiwitoken, $ads->data->contact_id, $ads->data->amount);
					}
					if ($stat==0){
						$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->index($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id, $ads->data->amount, $ads->data->reference_code);
					}
				}
				if ($stat==1){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->find($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					if ($stat==1){
						if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->updated_at ." + 10 minutes")){
							$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->second($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
						}
					}
				}
				if ($stat==2){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->find($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
				if ($stat==3){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->find_otv($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
				if ($stat==5){
					$stat=\App\LocalBtc\QiwiBuy\QiwiVerif::getInstance()->index($this->qiwitoken, $ads->data->contact_id);
				}
				if ($stat==6){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->otpusk($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
				if ($stat==7){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					if ($stat==7){
						if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->updated_at ." + 15 minutes")){
							$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->ne_prishli($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
							\App\LocalBtc\QiwiBuy\Sms::getInstance()->index($ads->data->contact_id);
						}
					}
				}
				if ($stat==8){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
					if ($stat==8){
						if (strtotime(date('Y-m-d H:i:s'))>strtotime($rob->created_at ." + 61 minutes")){
							$stat=\App\LocalBtc\QiwiBuy\Spor::getInstance()->index($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
						}
					}
				}
				if ($stat==10){
					$stat=\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $ads->data->contact_id);
				}
			}
		}
		$robs=\App\LocalBtc\LocalRobot::where('id_ad', '609849')->where('status', 10)->get();
		foreach ($robs as $rob){
			\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->statusSpor($this->lbtckey, $this->lbtcsecret, $rob->contact_id);
		}
		$robs=\App\LocalBtc\LocalRobot::where('id_ad', '609849')->whereIn('status', [7, 8])->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') ."-61 minutes")), date('Y-m-d H:i:s')])->get();
		foreach ($robs as $rob){
			\App\LocalBtc\QiwiBuy\OpenChat::getInstance()->status($this->lbtckey, $this->lbtcsecret, $rob->contact_id);
		}
	}
}
