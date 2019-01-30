<?php

namespace App\Console\Commands\Obmenneg\Parsers;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class Qiwi extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:qiwi';

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
	//для сделок 609849 и 617372
	private $lbtckey='dbc503c1c4aa29ba039363df1df8f2ee'; //ключ localbitcoins parsers_qiwi
	private $lbtcsecret='0add7916f44eef96de3527f41a9edda37c71ed5d01a448f7bd6c9eb102bdb55a'; //секрет localbitcoins parsers_qiwi
	private $qiwi='46c80b272ffc67f5c0738fc912763a14'; //токен qiwi для кошелька 79281131180 действует до 15.08.2018
	//'2260fafba73431387ed8d5c776a322ae'; //токен qiwi для кошелька 79381148114 действует до 08.08.2018
    public function handle()
    {
		
		$url='https://edge.qiwi.com/funding-sources/v1/accounts/current';
		$curl = curl_init($url);
		$nonce=1000*time();
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-type: application/json',
			'Authorization: Bearer '.$this->qiwi
			),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$qiwibalance=0;
		foreach ($result->accounts as $account){
			if ($account->alias!="qw_wallet_rub"){
				continue;
			}
			$qiwibalance=$account->balance->amount;
		}
		
		
		$limite=\DB::connection('obmenneg')->table('limites')->where('id', '1')->first();
		$new_shell=intval($limite->limite - $qiwibalance);
		if ($new_shell<0){
			$new_shell=0;
		}
		if ($qiwibalance<$limite->limite){
			$new_buy=intval($qiwibalance);
		}
		else{
			$new_buy=intval($limite->limite);
		}
		\DB::connection('obmenneg')->table('limites')->where('id', '1')->update(['shell'=>$new_shell, 'buy'=>$new_buy]);
		if ($new_buy<1000){
			$new_buy=1000;
		}
		if ($new_shell<1000){
			$new_shell=1000;
		}
		$limites['new_buy']=$new_buy;
		$limites['new_shell']=$new_shell;
		
		
	
		$ads=\DB::connection('obmenneg')->table('all_ads')->whereIn('id_ads', ['609849', '617372'])->get();
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into qiwi_offers_tmp (id_ads,username,name,temp_price,my_id_ad,min_amount,max_amount)
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
			if ($ad->id_ads=='617372'){
				$sql="select (sum(course)/count(course))*1.03 as course, sum(course) as cour, sum(remainder) as remainder from balancing where id_ad='609849' and status='0'";
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
			foreach ($result->data->ad_list as $k=>$res){
				if ($i>9){
					continue;
				}
				if ($res->data->profile->username=='Obmenneg'){
					continue;
				}
				if ($ad->parse_status){
					if ($ad->position-1==$i){
						if ($ad->id_ads=='609849'){
							if ($ad->max_amount==$limites['new_buy']){
								\App\Lbtc\Parsers\Qiwi::getInstance()->index($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
							}
							else{
								\App\Lbtc\Parsers\Qiwi::getInstance()->limitesBuy($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price, $limites['new_buy']);
							}
						}
						else{
							if ($ad->max_amount==$limites['new_shell'] and $ad->on_actual==1 and $res->data->temp_price>$actual_sell['course']){
								\App\Lbtc\Parsers\Qiwi::getInstance()->index($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price);
							}
							else{
								\App\Lbtc\Parsers\Qiwi::getInstance()->limitesShell($ad->id_ads, $this->lbtckey, $this->lbtcsecret, $res->data->temp_price, $limites['new_shell']);
							}
						}
					}
				}
				else{
					if ($i==0){
						\App\Lbtc\Parsers\Qiwi::getInstance()->indexOff($ad->id_ads, $this->lbtckey, $this->lbtcsecret);
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
		ALTER TABLE qiwi_offers RENAME TO qiwi_offers_temp;
		ALTER TABLE qiwi_offers_tmp RENAME TO qiwi_offers;
		ALTER TABLE qiwi_offers_temp RENAME TO qiwi_offers_tmp;
		truncate TABLE qiwi_offers_tmp;
		';
		$pdo->exec($sql);
		var_dump('дошел до конца');
	}
}
