<?php

namespace App\Console\Commands\Obmenneg\Table;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class Birges extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:birges';

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
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_birges (name,fromm,tom,btc,eth,usd,rub)
			select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_birges WHERE name=? and fromm=? and tom=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_birges set btc=?, eth=?, usd=?, rub=?
			WHERE name=? and fromm=? and tom=?";
		$sthUpdate=$pdo->prepare($sql);
		
		$curl = curl_init('http://www.cbr.ru/scripts/XML_daily.asp');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$xmls=simplexml_load_string($json);
		$usd=0;
		foreach ($xmls->Valute as $valute){
			if ($valute->CharCode!="USD"){
				continue;
			}
			$usd=$valute->Value;
		}
		$btc=0;
		$eth=0;
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
		$price_btc_usd=$result->mid;
		$btc=$price*$usd;
		
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
		$price_eth_usd=$result->mid;
		$eth=$price*$usd;
		
		$to_eth=1/($eth/$btc);
		$price_to_eth=$to_eth*$eth;
		
		$to_btc=$eth/$btc;
		$price_to_btc=$to_btc*$eth;

		$sthUpdate->execute(['1',$to_eth,$price_btc_usd,$btc,'Bitfinex','btc','usd']);
		$sthInsert->execute(['Bitfinex','btc','usd','1',$to_eth,$price_btc_usd,$btc,'Bitfinex','btc','usd']);
		
		$sthUpdate->execute([$to_btc,'1',$price_eth_usd,$eth,'Bitfinex','eth','usd']);
		$sthInsert->execute(['Bitfinex','eth','usd',$to_btc,'1',$price_eth_usd,$eth,'Bitfinex','eth','usd']);
		
		$curl = curl_init('https://api.bitfinex.com/v1/pubticker/ethbtc');
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
		$price=$result->mid;
		$eth_btc=$price*$price_btc_usd;
		$eth_btc_rub=$eth_btc*$usd;
		$sthUpdate->execute([$price,'1',$eth_btc,$eth_btc_rub,'Bitfinex','eth','btc']);
		$sthInsert->execute(['Bitfinex','eth','btc',$price,'1',$eth_btc,$eth_btc_rub,'Bitfinex','eth','btc']);
		exit;
		
		$btc=0;
		$eth=0;
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
		$btc=$result[0]->price_rub;
		
		$curl = curl_init('https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=RUB');
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
		$eth=$result[0]->price_rub;
		$sthUpdate->execute([$btc,$eth,'CoinMarketCap']);
		$sthInsert->execute(['CoinMarketCap',$btc,$eth,'CoinMarketCap']);
	}
}
