<?php

namespace App\Console\Commands\Crypto;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class Centrobank extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Crypto:centrobank';

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
return;
		$pdo = \DB::connection("crypto")->getPdo();
		$sql="insert into centrobank (charcode,name,nominal,value,date)
			select ?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM centrobank WHERE charcode=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update centrobank set nominal=?,value=?,date=?
			WHERE charcode=?";
		$sthUpdate=$pdo->prepare($sql);
		$year=date('Y');
		$month=date('m');
		$day=date('d');
		$url='http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $day . '/' . $month . '/' . $year . '';
		$curl = curl_init($url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$xmls=simplexml_load_string($json);
		$date=$xmls->attributes()->Date;
		$date=date("Y-m-d", strtotime($date));
		foreach ($xmls as $xml){
			$value=str_replace(',','.',$xml->Value);
			$nominal=str_replace(',','.',$xml->Nominal);
			$sthUpdate->execute([$nominal,$value,$date,mb_strtolower($xml->CharCode)]);
			$sthInsert->execute([mb_strtolower($xml->CharCode),$xml->Name,$nominal,$value,$date,mb_strtolower($xml->CharCode)]);
			if (mb_strtolower($xml->CharCode)=='usd'){
				$sthUpdate->execute([$nominal,$value,$date,'usdt']);
				$sthInsert->execute(['usdt','Tether usd',$nominal,$value,$date,'usdt']);
			}
		}
	}
}
