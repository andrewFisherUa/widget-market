<?php

namespace App\Console\Commands\Obmenneg\ParsersV2;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class Bank extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:bank_v2';

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
	private $lbtckey='391ba1098c5c586b5d53384948322db8'; //ключ localbitcoins parser_bank
	private $lbtcsecret='023ddfd860b001b976ca8740e1b7d85e143831677744cbe4b0beab7110d798b5'; //секрет localbitcoins parser_bank
    public function handle()
    {
		$ads=\DB::connection('obmenneg')->table('local_ads')->whereIn('id_ad', ['705600', '705602'])->get();
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into banks_offers_tmp (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
			select ?,?,?,?,?,?,?";
		$sthInsert=$pdo->prepare($sql);
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
					//это бай байн
					if ($ad->id_ad=='705602'){
						if ($ad->position-1==$i){
							\App\LocalBtc\Parsers\BankBuy::getInstance()->index($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
						}
					}
					if ($ad->id_ad=='705600'){
						if ($ad->position-1==$i){
							\App\LocalBtc\Parsers\BankSell::getInstance()->index($ad->id_ad, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
						}
					}
				}
				else{
					if ($i==0){
						\App\LocalBtc\Parsers\Bank::getInstance()->indexOff($ad->id_ad, $this->lbtckey, $this->lbtcsecret);
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
		ALTER TABLE banks_offers RENAME TO banks_offers_temp;
		ALTER TABLE banks_offers_tmp RENAME TO banks_offers;
		ALTER TABLE banks_offers_temp RENAME TO banks_offers_tmp;
		truncate TABLE banks_offers_tmp;
		';
		$pdo->exec($sql);
		var_dump('дошел до конца');
	}
}
