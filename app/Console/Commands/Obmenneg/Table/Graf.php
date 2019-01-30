<?php

namespace App\Console\Commands\Obmenneg\Table;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class Graf extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:graf';

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
	private $lbtckey='1ceedf9fbd71007ddd294f5ad0838913'; //ключ localbitcoins test
	private $lbtcsecret='d9e5cf6a6497a8a4dffdbd80e6b3bc748201bb5cb194fab7ee2142f2d628a994'; //секрет localbitcoins test
	public function handle()
    {
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_graf (name,type,btc,rub,date,datetime)
			select ?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_graf where name=? and datetime=?)";
		$sthInsert=$pdo->prepare($sql);
		
		$time = time();
		$dtime=$time-($time%300);
		$date=date('Y-m-d');
		$datetime=date("Y-m-d H:i:s",$dtime);
		
		$curl = curl_init('http://www.cbr.ru/scripts/XML_daily.asp');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=simplexml_load_string($json);
		$usd=0;
		foreach ($result->Valute as $valute){
			if ($valute->CharCode!="USD"){
				continue;
			}
			$usd=$valute->Value;
		}
		$btc=0;
		$curl = curl_init('https://api.bitfinex.com/v1/pubticker/btcusd');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$price=$result->mid;
		$btc=round($price*$usd,2);
		$sthInsert->execute(['продажа Bitfinex','1','1',$btc,$date,$datetime,'продажа Bitfinex',$datetime]);
		
		$buy_cash=0;
		$url='/sell-bitcoins-online/RUB/transfers-with-specific-bank/.json';
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
		;
		if ($result){
			$buy_cash=$result->data->ad_list[0]->data->temp_price;
		}
		if ($buy_cash==0){
			$sql="select * from table_graf where type='2' order by id desc";
			$buy_cash_old=$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			$buy_cash=$buy_cash_old['rub'];
		}
		$sthInsert->execute(['продажа LocalBtc','2','1',$buy_cash,$date,$datetime,'продажа LocalBtc',$datetime]);
		
		$sell_cash=0;
		$url='/buy-bitcoins-online/RUB/transfers-with-specific-bank/.json';
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
		if ($result){
			$sell_cash=$result->data->ad_list[0]->data->temp_price;
		}
		if ($sell_cash==0){
			$sql="select * from table_graf where type='3' order by id desc";
			$sell_cash_old=$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			$sell_cash=$sell_cash_old['rub'];
		}
		$sthInsert->execute(['покупка LocalBtc','3','1',$sell_cash,$date,$datetime,'покупка LocalBtc',$datetime]);
		
		var_dump('doshel');
	}
}
