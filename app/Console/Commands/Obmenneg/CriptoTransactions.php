<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class CriptoTransactions extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:cripto_transactions';

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
		$curl = curl_init('https://api.bitfinex.com/v1/tickers');
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
		$time=time()-(time()%300);
		$datetime=date("Y-m-d H:i:s",$time);
		$pdo = \DB::connection("crypto")->getPdo();
		$sql="insert into transactions (market,whence,whereto,volume,volume_to_usd,volume_to_rub,price,price_to_usd,price_to_rub,datetime)
			select ?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM transactions WHERE market=? and whence=? and whereto=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update transactions set volume=?, volume_to_usd=?, volume_to_rub=?, price=?, price_to_usd=?, price_to_rub=?, datetime=?
			WHERE market=? and whence=? and whereto=?";
		$sthUpdate=$pdo->prepare($sql);
		$centrobanks=\DB::connection('crypto')->table('centrobank')->get();
		//bitfinex
		//$curl = curl_init('https://api.bitfinex.com/v1/pubticker/'. $whence . '' . $where . '');
		/*$transactions=['btcusd', 'btceur'];
		foreach ($transactions as $transaction){
			$whence=substr($transaction, 0, 3);
			$whereto=substr($transaction, 3);
			$curl = curl_init('https://api.bitfinex.com/v1/pubticker/' . $transaction . '');
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
			if ($whereto=='usd'){
				foreach ($centrobanks as $centrobank){
					if ($whereto==$centrobank->charcode){
						$rub=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				if (!$rub){
					continue;
				}
				$volume=round($result->mid*$result->volume,8);
				$volume_to_usd=round($result->mid*$result->volume,8);
				$volume_to_rub=round($result->mid*$result->volume*$rub,8);
				$price=round($result->mid,8);
				$price_to_usd=round($result->mid,8);
				$price_to_rub=round($result->mid*$rub,8);
			}
			if ($whereto=='eur'){
				foreach ($centrobanks as $centrobank){
					if ($whereto==$centrobank->charcode){
						$rub=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				foreach ($centrobanks as $centrobank){
					if ($centrobank->charcode=='usd'){
						$usd=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				if (!$rub or !$usd){
					continue;
				}
				$usd=$rub/$usd;
				$volume=round($result->mid*$result->volume,8);
				$volume_to_usd=round($result->mid*$result->volume*$usd,8);
				$volume_to_rub=round($result->mid*$result->volume*$rub,8);
				$price=round($result->mid,8);
				$price_to_usd=round($result->mid*$usd,8);
				$price_to_rub=round($result->mid*$rub,8);
			}
			$sthUpdate->execute([$volume, $volume_to_usd, $volume_to_rub, $price, $price_to_usd, $price_to_rub, $datetime, 'Bitfinex', $whence, $whereto]);
			$sthInsert->execute(['Bitfinex', $whence, $whereto, $volume, $volume_to_usd, $volume_to_rub, $price, $price_to_usd, $price_to_rub, $datetime, 
			'Bitfinex', $whence, $whereto]);
		}
		exit;*/
		//GDAX под вопросам, какая то херня с курсом к юсд
		$curl = curl_init('https://api-public.sandbox.gdax.com/products/stats');
			$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HTTPHEADER => array(
					'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
					'Accept-language:ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
					'User-Agent:Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36'
				),
			);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$results=json_decode($json);
		foreach ($results as $name=>$result){
			$names=explode("-", $name);
			$whence=mb_strtolower($names[0]);
			$whereto=mb_strtolower($names[1]);
			if ($whence!='btc' or $whereto=='usd'){
				continue;
			}
			if ($whereto=='eur'){
				foreach ($centrobanks as $centrobank){
					if ($whereto==$centrobank->charcode){
						$rub=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				foreach ($centrobanks as $centrobank){
					if ($centrobank->charcode=='usd'){
						$usd=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				if (!$rub or !$usd){
					continue;
				}
				$usd=$rub/$usd;
				$price=round($result->stats_24hour->volume,8);
				$price_to_usd=round($result->stats_24hour->volume*$usd,8);
				$price_to_rub=round($result->stats_24hour->volume*$rub,8);
				$volume=round(($result->stats_24hour->high+$result->stats_24hour->low)/2*$result->stats_24hour->volume,8);
				$volume_to_usd=round(($result->stats_24hour->high+$result->stats_24hour->low)/2*$result->stats_24hour->volume*$usd,8);
				$volume_to_rub=round(($result->stats_24hour->high+$result->stats_24hour->low)/2*$result->stats_24hour->volume*$rub,8);
			}
			if ($whereto=='gbp'){
				foreach ($centrobanks as $centrobank){
					if ($whereto==$centrobank->charcode){
						$rub=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				foreach ($centrobanks as $centrobank){
					if ($centrobank->charcode=='usd'){
						$usd=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				if (!$rub or !$usd){
					continue;
				}
				$usd=$rub/$usd;
				$price=round($result->stats_24hour->volume,8);
				$price_to_usd=round($result->stats_24hour->volume*$usd,8);
				$price_to_rub=round($result->stats_24hour->volume*$rub,8);
				$volume=round(($result->stats_24hour->high+$result->stats_24hour->low)/2*$result->stats_24hour->volume,8);
				$volume_to_usd=round(($result->stats_24hour->high+$result->stats_24hour->low)/2*$result->stats_24hour->volume*$usd,8);
				$volume_to_rub=round(($result->stats_24hour->high+$result->stats_24hour->low)/2*$result->stats_24hour->volume*$rub,8);
			}
			$sthUpdate->execute([$volume, $volume_to_usd, $volume_to_rub, $price, $price_to_usd, $price_to_rub, $datetime, 'Gdax', $whence, $whereto]);
			$sthInsert->execute(['Gdax', $whence, $whereto, $volume, $volume_to_usd, $volume_to_rub, $price, $price_to_usd, $price_to_rub, $datetime, 
			'Gdax', $whence, $whereto]);
		}
		
		//kraken
		$transactions=['xxbtzusd'];
		foreach ($transactions as $transaction){
			$curl = curl_init('https://api.kraken.com/0/public/Ticker?pair=' . mb_strtoupper($transaction) . '');
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
			$whence=substr($transaction, 0, 5);
			if ($whence=='xxbtz') $whence='btc';
			$whereto=substr($transaction, 5);
			if ($whereto=='usd'){
				foreach ($centrobanks as $centrobank){
					if ($whereto==$centrobank->charcode){
						$rub=$centrobank->value/$centrobank->nominal;
						break;
					}
				}
				if (!$rub){
					continue;
				}
				$price=round($result->mid,8);
				$price_to_usd=round($result->mid,8);
				$price_to_rub=round($result->mid*$rub,8);
				$volume=round($result->mid*$result->volume,8);
				$volume_to_usd=round($result->mid*$result->volume,8);
				$volume_to_rub=round($result->mid*$result->volume*$rub,8);
			}
			
			$price=round($result->result->XXBTZUSD->p[1],8);
			$volume=round($result->result->XXBTZUSD->p[1]*$result->result->XXBTZUSD->v[1],8);
			$sthUpdate->execute([$volume, $price, $datetime, 'Kraken', $whence, $where]);
			$sthInsert->execute(['Kraken', $whence, $where, $volume, $price, $datetime, 'Kraken', $whence, $where]);
		}
		exit;
		//binance
		$transactions=['btcusdt'];
		foreach ($transactions as $transaction){
			$curl = curl_init('https://api.binance.com/api/v1/ticker/24hr?symbol=' . mb_strtoupper($transaction) . '');
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
			$whence=substr($transaction, 0, 3);
			$where=substr($transaction, 3);
			$volume=round($result->quoteVolume,8);
			$price=round($result->weightedAvgPrice,8);
			$sthUpdate->execute([$volume, $price, $datetime, 'Binance', $whence, $where]);
			$sthInsert->execute(['Binance', $whence, $where, $volume, $price, $datetime, 'Binance', $whence, $where]);
		}
		
		//kraken
		$transactions=['xxbtzusd'];
		foreach ($transactions as $transaction){
			$curl = curl_init('https://api.kraken.com/0/public/Ticker?pair=' . mb_strtoupper($transaction) . '');
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
			$whence=substr($transaction, 0, 5);
			if ($whence=='xxbtz') $whence='btc';
			$where=substr($transaction, 5);
			$price=round($result->result->XXBTZUSD->p[1],8);
			$volume=round($result->result->XXBTZUSD->p[1]*$result->result->XXBTZUSD->v[1],8);
			$sthUpdate->execute([$volume, $price, $datetime, 'Kraken', $whence, $where]);
			$sthInsert->execute(['Kraken', $whence, $where, $volume, $price, $datetime, 'Kraken', $whence, $where]);
		}
		$volume=\DB::connection('crypto')->table('transactions')->sum('volume');
		var_dump($volume);
		$sql="update transactions set amount=?
			WHERE market=? and whence=? and wheres=?";
		$sthUpdate=$pdo->prepare($sql);
		$transactions=\DB::connection('crypto')->table('transactions')->get();
		$btc_sum=0;
		foreach ($transactions as $transaction){
			$amount=round($transaction->volume/$volume * 100,8);
			$btc_sum+=$transaction->volume/$volume*$transaction->price;
			$sthUpdate->execute([$amount, $transaction->market, $transaction->whence, $transaction->wheres]);
		}
		$marketcap=\DB::connection('crypto')->table('btc')->orderBy('datetime', 'desc')->first();
		$sql="insert into btc_cap (shortname,marketcap,price,volume,datetime)
			select ?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM btc_cap WHERE shortname=? and datetime=?)";
		$sthInsert=$pdo->prepare($sql);
		$btc_marketcap=round($marketcap->total*$btc_sum,8);
		$btc_sum=round($btc_sum,8);
		$volume=round($volume,0);
		$sthInsert->execute(['btc', $btc_marketcap, $btc_sum, $volume, $datetime, 'btc', $datetime]);
		exit;
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
