<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class TestCripto extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:cripto';

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
		$url='/';
		$curl = curl_init('https://blockchain.info/q/totalbc');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$satoshi=100000000;
		$btc=$json/$satoshi;
		$time=time()-(time()%300);
		$datetime=date("Y-m-d H:i:s",$time);

		\DB::connection('crypto')->table('btc')->insert([
		  ['datetime' => $datetime, 'total' => $btc]
		]);
		exit;
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into criptos (name,usd,btc)
			select ?,?,? WHERE NOT EXISTS (SELECT 1 FROM criptos WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update criptos set usd=?, btc=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		//bitfinex
		$curl = curl_init('https://api.bitfinex.com/v1/symbols');
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
		$sthUpdate->execute([$result->mid, $result->mid*$result->volume, 'Bitfinex']);
		$sthInsert->execute(['Bitfinex', $result->mid, $result->mid*$result->volume, 'Bitfinex']);
		
		
		//binance
		$curl = curl_init('https://api.binance.com/api/v1/ticker/24hr?symbol=BTCUSDT');
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
		//var_dump($result);
		$sthUpdate->execute([$result->weightedAvgPrice, $result->quoteVolume, 'Binance']);
		$sthInsert->execute(['Binance', $result->weightedAvgPrice, $result->quoteVolume, 'Binance']);
		
		//bithumb
		$curl = curl_init('https://api.bithumb.com/public/ticker/BTC');
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
		//var_dump($result);
		$sthUpdate->execute([$result->data->average_price*0.00094, $result->data->volume_1day*$result->data->average_price*0.00094, 'Bithumb']);
		$sthInsert->execute(['Bithumb', $result->data->average_price*0.00094, $result->data->volume_1day*$result->data->average_price*0.00094, 'Bithumb']);
		
		
		//kraken
		$curl = curl_init('https://api.kraken.com/0/public/Ticker?pair=XXBTZUSD');
		
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
		//var_dump($result);
		
		$sthUpdate->execute([$result->result->XXBTZUSD->p[1], $result->result->XXBTZUSD->p[1]*$result->result->XXBTZUSD->v[1], 'Kraken']);
		$sthInsert->execute(['Kraken', $result->result->XXBTZUSD->p[1], $result->result->XXBTZUSD->p[1]*$result->result->XXBTZUSD->v[1], 'Kraken']);
		
		exit;
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_birges (name,btc,eth)
			select ?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_birges WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_birges set btc=?, eth=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		
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
		$price=$result->bid;
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
		$price=$result->bid;
		$eth=$price*$usd;
		$sthUpdate->execute([$btc,$eth,'Bitfinex']);
		$sthInsert->execute(['Bitfinex',$btc,$eth,'Bitfinex']);
		
		
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
		exit;
	}
}
