<?php

namespace App\Console\Commands\Obmenneg\Table;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class Obmenneg extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:obmenneg_v2';

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
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_btc (name,buy_cash,sell_cash,buy_bank,sell_bank,buy_wmr,sell_wmr,buy_ym,sell_ym,buy_qiwi,sell_qiwi)
			select ?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_btc WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_btc set buy_cash=?,sell_cash=?,buy_bank=?,sell_bank=?,buy_wmr=?,sell_wmr=?,buy_ym=?,sell_ym=?,buy_qiwi=?,sell_qiwi=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		
		$curl = curl_init('https://obmenneg.ru/request-exportxml.xml');
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
		$xmls=simplexml_load_string($json);
		$buy_cash=0;
		$sell_cash=0;
		$buy_bank=0;
		$sell_bank=0;
		$buy_wmr=0;
		$sell_wmr=0;
		$buy_ym=0;
		$sell_ym=0;
		$buy_qiwi=0;
		$sell_qiwi=0;
		foreach ($xmls as $xml){
			if ($xml->from=="BTC" and $xml->to=="CASHRUB"){
				$buy_cash=$xml->out;
			}
			if ($xml->from=="CASHRUB" and $xml->to=="BTC"){
				$sell_cash=$xml->in;
			}
			if ($xml->from=="BTC" and $xml->to=="SBERRUB"){
				$buy_bank=$xml->out;
			}
			if ($xml->from=="SBERRUB" and $xml->to=="BTC"){
				$sell_bank=$xml->in;
			}
			if ($xml->from=="BTC" and $xml->to=="WMR"){
				$buy_wmr=$xml->out;
			}
			if ($xml->from=="WMR" and $xml->to=="BTC"){
				$sell_wmr=$xml->in;
			}
			if ($xml->from=="BTC" and $xml->to=="YAMRUB"){
				$buy_ym=$xml->out;
			}
			if ($xml->from=="YAMRUB" and $xml->to=="BTC"){
				$sell_ym=$xml->in;
			}
			if ($xml->from=="BTC" and $xml->to=="QWRUB"){
				$buy_qiwi=$xml->out;
			}
			if ($xml->from=="QWRUB" and $xml->to=="BTC"){
				$sell_qiwi=$xml->in;
			}
		}
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'Наш Обменник']);
		$sthInsert->execute(['Наш Обменник',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'Наш Обменник']);
		
		
		$sql="insert into table_eth (name,buy_cash,sell_cash,buy_bank,sell_bank,buy_wmr,sell_wmr,buy_ym,sell_ym,buy_qiwi,sell_qiwi)
			select ?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_eth WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_eth set buy_cash=?,sell_cash=?,buy_bank=?,sell_bank=?,buy_wmr=?,sell_wmr=?,buy_ym=?,sell_ym=?,buy_qiwi=?,sell_qiwi=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		$buy_cash=0;
		$sell_cash=0;
		$buy_bank=0;
		$sell_bank=0;
		$buy_wmr=0;
		$sell_wmr=0;
		$buy_ym=0;
		$sell_ym=0;
		$buy_qiwi=0;
		$sell_qiwi=0;
		foreach ($xmls as $xml){
			if ($xml->from=="ETH" and $xml->to=="CASHRUB"){
				$buy_cash=$xml->out;
			}
			if ($xml->from=="CASHRUB" and $xml->to=="ETH"){
							var_dump($xml);
				$sell_cash=$xml->in;
			}
			if ($xml->from=="ETH" and $xml->to=="SBERRUB"){
				$buy_bank=$xml->out;
			}
			if ($xml->from=="SBERRUB" and $xml->to=="ETH"){
				$sell_bank=$xml->in;
			}
			if ($xml->from=="ETH" and $xml->to=="WMR"){
				$buy_wmr=$xml->out;
			}
			if ($xml->from=="WMR" and $xml->to=="ETH"){
				$sell_wmr=$xml->in;
			}
			if ($xml->from=="ETH" and $xml->to=="YAMRUB"){
				$buy_ym=$xml->out;
			}
			if ($xml->from=="YAMRUB" and $xml->to=="ETH"){
				$sell_ym=$xml->in;
			}
			if ($xml->from=="ETH" and $xml->to=="QWRUB"){
				$buy_qiwi=$xml->out;
			}
			if ($xml->from=="QWRUB" and $xml->to=="ETH"){
				$sell_qiwi=$xml->in;
			}
		}
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'Наш Обменник']);
		$sthInsert->execute(['Наш Обменник',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'Наш Обменник']);
	}
}
