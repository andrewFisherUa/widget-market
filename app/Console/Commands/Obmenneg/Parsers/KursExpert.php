<?php

namespace App\Console\Commands\Obmenneg\Parsers;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use Sunra\PhpSimple\HtmlDomParser;

class KursExpert extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:kurs_expert';

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
		$sql="insert into parse_table (name,buy_cash,sell_cash,buy_bank,sell_bank,buy_wmr,sell_wmr,buy_ym,sell_ym,buy_qiwi,sell_qiwi)
			select ?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM parse_table WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update parse_table set buy_cash=?,sell_cash=?,buy_bank=?,sell_bank=?,buy_wmr=?,sell_wmr=?,buy_ym=?,sell_ym=?,buy_qiwi=?,sell_qiwi=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		$old=\DB::connection('obmenneg')->table('parse_table')->where('name', 'КурсЭксперт')->first();
		
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'bitcoin',
			'buy'=>'nalichnie.rub'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$buy_cash=0;
		$buy_cash=trim($tr->find('td', 2)->innertext);
		
		
		//echo $buy_cash ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'nalichnie.rub',
			'buy'=>'bitcoin'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$sell_cash=0;
		$sell_cash=trim($tr->find('td', 1)->innertext);
		
		//echo $sell_cash ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'bitcoin',
			'buy'=>'sberbank'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$buy_bank=0;
		$buy_bank=trim($tr->find('td', 2)->innertext);
		//echo $buy_bank ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'sberbank',
			'buy'=>'bitcoin'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$sell_bank=0;
		$sell_bank=trim($tr->find('td', 1)->innertext);
		
		//echo $sell_bank ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'bitcoin',
			'buy'=>'wmr'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			if ($tr){
				$name=$tr->find('td', 0)->attr['name'];
			}
		}
		$buy_wmr=0;
		if ($tr){
			$buy_wmr=trim($tr->find('td', 2)->innertext);
		}
		
		//echo $buy_wmr ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'wmr',
			'buy'=>'bitcoin'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		if ($table){
			$tr=$table->find('tr', 1);
			$name=$tr->find('td', 0)->attr['name'];
			if ($name=='Obmenneg.ru'){
				$tr=$table->find('tr', 2);
				$name=$tr->find('td', 0)->attr['name'];
			}
		}
		$sell_wmr=0;
		if ($table){
			$sell_wmr=trim($tr->find('td', 1)->innertext);
		}
		
		//echo $sell_wmr ."\n";
		
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'bitcoin',
			'buy'=>'yandex.money'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$buy_ym=0;
		$buy_ym=trim($tr->find('td', 2)->innertext);
		//echo $buy_ym ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'yandex.money',
			'buy'=>'bitcoin'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$sell_ym=0;
		$sell_ym=trim($tr->find('td', 1)->innertext);
		//echo $sell_ym ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'bitcoin',
			'buy'=>'qiwi.rub'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$buy_qiwi=0;
		$buy_qiwi=trim($tr->find('td', 2)->innertext);
		
		//echo $buy_qiwi ."\n";
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'qiwi.rub',
			'buy'=>'bitcoin'
		);
		$curl = curl_init('https://kurs.expert/ru/dt?r=0.' . $nonce . '' .$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$html = HTMLDomParser::str_get_html($json);
		$table=$html->find('table', 0);
		$tr=$table->find('tr', 1);
		$name=$tr->find('td', 0)->attr['name'];
		if ($name=='Obmenneg.ru'){
			$tr=$table->find('tr', 2);
			$name=$tr->find('td', 0)->attr['name'];
		}
		$sell_qiwi=0;
		$sell_qiwi=trim($tr->find('td', 1)->innertext);
		//echo $sell_qiwi ."\n";
		
		if ($buy_cash==0){
			if ($old){
				$buy_cash=$old->buy_cash;
			}
		}
		if ($sell_cash==0){
			if ($old){
				$sell_cash=$old->sell_cash;
			}
		}
		if ($buy_bank==0){
			if ($old){
				$buy_bank=$old->buy_bank;
			}
		}
		if ($sell_bank==0){
			if ($old){
				$sell_bank=$old->sell_bank;
			}
		}
		if ($buy_wmr==0){
			if ($old){
				$buy_wmr=$old->buy_wmr;
			}
		}
		if ($sell_wmr==0){
			if ($old){
				$sell_wmr=$old->sell_wmr;
			}
		}
		if ($buy_ym==0){
			if ($old){
				$buy_ym=$old->buy_ym;
			}
		}
		if ($sell_ym==0){
			if ($old){
				$sell_ym=$old->sell_ym;
			}
		}
		if ($buy_qiwi==0){
			if ($old){
				$buy_qiwi=$old->buy_qiwi;
			}
		}
		if ($sell_qiwi==0){
			if ($old){
				$sell_qiwi=$old->sell_qiwi;
			}
		}
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'КурсЭксперт']);
		$sthInsert->execute(['КурсЭксперт',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'КурсЭксперт']);

	}
}
