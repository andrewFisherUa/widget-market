<?php

namespace App\Console\Commands\Obmenneg\Parsers;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class Bitfinex extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:bitfinex';

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
	public function handle(array $req = array())
    {
		
		
		$curl = curl_init('https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=RUB');
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
		var_dump($result);
		exit;
		
		
		
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into parse_table (name,buy_cash)
			select ?,? WHERE NOT EXISTS (SELECT 1 FROM parse_table WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update parse_table set buy_cash=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		$old=\DB::connection('obmenneg')->table('parse_table')->where('name', 'Bitfinex')->first();
		
		$curl = curl_init('https://www.cbr-xml-daily.ru/daily_json.js');
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
		$usd=0;
		foreach ($result->Valute as $valute){
			if ($valute->CharCode!="USD"){
				continue;
			}
			$usd=$valute->Value;
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
		$price=$result->bid;
		$buy_cash=$price*$usd;
		$sthUpdate->execute([$buy_cash,'Bitfinex']);
		$sthInsert->execute(['Bitfinex',$buy_cash,'Bitfinex']);
	}
}
