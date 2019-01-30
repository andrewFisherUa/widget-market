<?php

namespace App\Console\Commands\Obmenneg\Table;

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
    protected $signature = 'LocalBtc:kurs_expert_v2';

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
		
		//Bitcoin -> cash
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_cash=0;
		if ($tr){
			$buy_cash=trim($tr->find('td', 2)->innertext);
		}
		
		//Cash -> Bitcoin
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_cash=0;
		if ($tr){
			$sell_cash=trim($tr->find('td', 1)->innertext);
		}
		//Bitcoin -> Sber
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_bank=0;
		if ($tr){
			$buy_bank=trim($tr->find('td', 2)->innertext);
		}
		
		//Sber -> Bitcoin
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_bank=0;
		if ($tr){
			$sell_bank=trim($tr->find('td', 1)->innertext);
		}
		//Bitcoin -> Wmr;
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_wmr=0;
		if ($tr){
			$buy_wmr=trim($tr->find('td', 2)->innertext);
		}
		
		//Wmr -> Bitcoin;
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_wmr=0;
		if ($tr){
			$sell_wmr=trim($tr->find('td', 1)->innertext);
		}
		
		//Bitcoin -> Yandex
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_ym=0;
		if ($tr){
			$buy_ym=trim($tr->find('td', 2)->innertext);
		}
		
		//Yandex -> Bitcoin;
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_ym=0;
		if ($tr){
			$sell_ym=trim($tr->find('td', 1)->innertext);
		}
		
		//Bitcoin -> qiwi
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_qiwi=0;
		if ($tr){
			$buy_qiwi=trim($tr->find('td', 2)->innertext);
		}
		
		//Qiwi -> Bitcoin
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_qiwi=0;
		if ($tr){
			$sell_qiwi=trim($tr->find('td', 1)->innertext);
		}
		
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'КурсЭксперт']);
		$sthInsert->execute(['КурсЭксперт',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'КурсЭксперт']);
		
		
		
		//Etherium
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_eth (name,buy_cash,sell_cash,buy_bank,sell_bank,buy_wmr,sell_wmr,buy_ym,sell_ym,buy_qiwi,sell_qiwi)
			select ?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_eth WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_eth set buy_cash=?,sell_cash=?,buy_bank=?,sell_bank=?,buy_wmr=?,sell_wmr=?,buy_ym=?,sell_ym=?,buy_qiwi=?,sell_qiwi=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		
		//Ethereum -> Cash
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'ethereum',
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_cash=0;
		if ($tr){
			$buy_cash=trim($tr->find('td', 2)->innertext);
		}
		
		//Cash -> Ethereum
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'nalichnie.rub',
			'buy'=>'ethereum'
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_cash=0;
		if ($tr){
			$sell_cash=trim($tr->find('td', 1)->innertext);
		}
		
		//Ethereum -> Sber
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'ethereum',
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_bank=0;
		if ($tr){
			$buy_bank=trim($tr->find('td', 2)->innertext);
		}
		
		//Sber -> Ethereum
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'sberbank',
			'buy'=>'ethereum'
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_bank=0;
		if ($tr){
			$sell_bank=trim($tr->find('td', 1)->innertext);
		}
		
		//Ethereum -> Wmr;
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'ethereum',
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_wmr=0;
		if ($tr){
			$buy_wmr=trim($tr->find('td', 2)->innertext);
		}
		
		//Wmr -> Ethereum;
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'wmr',
			'buy'=>'ethereum'
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_wmr=0;
		if ($tr){
			$sell_wmr=trim($tr->find('td', 1)->innertext);
		}
		
		//Ethereum -> Yandex
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'ethereum',
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_ym=0;
		if ($tr){
			$buy_ym=trim($tr->find('td', 2)->innertext);
		}
		
		//Yandex -> Ethereum;
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'yandex.money',
			'buy'=>'ethereum'
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_ym=0;
		if ($tr){
			$sell_ym=trim($tr->find('td', 1)->innertext);
		}
		
		//Ethereum -> qiwi
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'ethereum',
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$buy_qiwi=0;
		if ($tr){
			$buy_qiwi=trim($tr->find('td', 2)->innertext);
		}
		
		//Qiwi -> Ethereum
		$time=microtime();
		$int=substr($time,11);
		$flo=substr($time,2,7);
		$nonce=$flo.$int;
		$url='/';
		$message=array(
			'sell'=>'qiwi.rub',
			'buy'=>'ethereum'
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
		$table=0;
		$tr=0;
		$table=$html->find('table', 0);
		if ($table){
			foreach($table->find('tr') as $tr) {
				if (strpos($tr->class, 'eLine') === false){
					continue;
				}
				if (strpos($tr->class, 'NotSchedule') !== false){
					continue;
				}
				if ($tr){
					$name=$tr->find('td', 0)->attr['name'];
				}
				if ($name=='Obmenneg.ru'){
					continue;
				}
				break;
			}
		}
		$sell_qiwi=0;
		if ($tr){
			$sell_qiwi=trim($tr->find('td', 1)->innertext);
		}
		
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'КурсЭксперт']);
		$sthInsert->execute(['КурсЭксперт',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'КурсЭксперт']);
		var_dump('doshel');
	}
}
