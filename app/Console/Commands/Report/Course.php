<?php

namespace App\Console\Commands\Report;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class Course extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:course';

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
	public function handle()
    {
		$url="https://www.cbr-xml-daily.ru/daily_json.js";
		 
		 #var_dump(file_get_contents($url)); die();
		 $ch = curl_init();  
         curl_setopt($ch, CURLOPT_URL, $url); 
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
         $result = curl_exec($ch); 
         curl_close($ch); 
			var_dump($result);
		exit;
		
		$pdo = \DB::connection("report")->getPdo();
		$sql="insert into courses (date,datetime,usd,btc,eth,ltc,uah,eur)
			select ?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM courses WHERE date=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update courses set datetime=?,usd=?,btc=?,eth=?,ltc=?,uah=?,eur=?
			WHERE date=?";
		$sthUpdate=$pdo->prepare($sql);
		$sql="insert into courses_tmp (datetime,usd,btc,eth,ltc,uah,eur)
			select ?,?,?,?,?,?,?";
		$sthInsertt=$pdo->prepare($sql);
		$date=date('Y-m-d');
		$datetime=date('Y-m-d H:i:s');
		$curl = curl_init('https://www.cbr-xml-daily.ru/daily_json.js');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		foreach ($result->Valute as $valute){
			if ($valute->CharCode=="USD"){
				$usd=$valute->Value;
			}
			if ($valute->CharCode=="UAH"){
				$uah=$valute->Value/$valute->Nominal;
			}
			if ($valute->CharCode=="EUR"){
				$eur=$valute->Value/$valute->Nominal;
			}
		}
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
		
		$curl = curl_init('https://api.bitfinex.com/v1/pubticker/ethusd');
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
		$eth=round($price*$usd,2);
		
		$curl = curl_init('https://api.bitfinex.com/v1/pubticker/ltcusd');
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
		$ltc=round($price*$usd,2);
		
		$sthUpdate->execute([$datetime,$usd,$btc,$eth,$ltc,$uah,$eur,$date]);
		$sthInsert->execute([$date,$datetime,$usd,$btc,$eth,$ltc,$uah,$eur,$date]);
		$sthInsertt->execute([$datetime,$usd,$btc,$eth,$ltc,$uah,$eur]);
	}
}
