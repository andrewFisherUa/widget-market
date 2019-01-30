<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
use \YandexMoney\API;
use SimpleXMLElement;


class Robot extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:robot';

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
    private $qiwitoken='46c80b272ffc67f5c0738fc912763a14'; //токен qiwi для кошелька 79281131180 действует до 15.08.2018
	//'2260fafba73431387ed8d5c776a322ae'; //токен qiwi для кошелька 79381148114 действует до 08.08.2018
	private $WebMoneyWmid="736679757699"; //wmid на который будет выполнен перевод
	private $WebMoneyR="R605854660223"; //кошелек на который будет выполнен перевод
	private $secretKey='5P7KG2uN2viZP9rQBEPkJVh6eoenA1Z1'; //ключ webmoney для x20 интерфейса
	private $yandextokenold='410011520925164.828062B84FD6997D29F145E6182152FF1C11A2CAC5B004D6FD270A2D72DFE8E38F41AEC4C5D007721C976854CA5AF420ADE572FA69439E8DC7F844A7B4F5F9AB743555D8BB4B5E85B807C8DCB397FB8123595D06F8F06890C257A95CD89B8E2AE887F06F560113F542EF74E219A2806855CD0281D43DC5CD27C1550EB4E33A09'; //токен яндекс деньги
	private $yandextoken='410011520925164.3EDFC615F1934DC1EEF9DEB06997B3F206E5E35E05B85900DDB02E6A7C0A17BAEAB702437F77AA59FC22FB647573988E6FAE6E75AC2A4E7243CE3259A27B44DCB6095D8AEF0B8C7F062A793F81BBA96915A99284D4185EEE6BEBF330210E13947901DE58B94F69C1976B00FFE9DF13D51C817C7115953295016D74B9EDD15295'; //токен яндекс деньги
	private $yandextokenKatya='410015804466025.BA8B13E49187D6C9A0C77E73317FFBE6613BC09673E04448726D510B4EEBEF985781F76631C8A2C1310DA593977C40073F1F53ED93608E4F1640197C5932F58C5609802D0CAB23C3217C4B34AA207EA189CF26B242224A56CFD68082D584C251F88D4BB2349C3EDF34832F8DF3D22E236CE2598FBC45A613F404AF4D48583FC5';
	public function handle()
    {
		
		$url='/api/ad-get/666810/';
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
			CURLOPT_POSTFIELDS => $message,
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
		var_dump($result);
		
		exit;
		$url='/api/ads/';
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
		CURLOPT_TIMEOUT => 45,
		CURLOPT_FILETIME => TRUE,
		//CURLOPT_INTERFACE => "185.60.135.248",
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		$result=json_decode($json);
		var_dump($result);
		exit;
		//$cmd='php artisan queue:restart';
		//$cmd='php artisan queue:work &/';
		//`$cmd`;
		exit;
		$api = new API($this->yandextoken);
		var_dump($api->accountInfo());
		exit;
		$request_payment = $api->requestPayment(array(
			"pattern_id" => "p2p",
			"to" => "410015804466025",
			"amount_due" => "1.00",
			"comment" => "перевод",
			"message" => ""
		));
		var_dump($api);
		echo "<hr>";
		var_dump($request_payment);
		$process_payment = $api->processPayment(array(
			"request_id" => $request_payment->request_id,
		));
		var_dump($process_payment);
		exit;
		
		
		$api = new API($this->yandextokennn);
		$acount_info = $api->accountInfo();
		$yandexbalance=$acount_info->balance_details->total;
		var_dump($yandexbalance);
		exit;
		$newT=new \App\Videosource\CalculatorB(); 
		$newT->StartDay();
		exit;
		$pdo = \DB::connection()->getPdo();
		$sql="select t3.id from frame_prover t1 left join (select id,user_id from widgets) t2 on t1.user_id=t2.user_id 
		left join (select * from widget_videos) t3 on t2.id=t3.wid_id where t1.datetime>now();";
		$ppp=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$userPid=array();
		foreach ($ppp as $pp){
			array_push($userPid, $pp['id']);
		}
		var_dump($userPid);
		if (in_array("131", $userPid)) {
			echo "Got Irix";
		}
		exit;
		$url='/api/dashboard/';
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
		CURLOPT_TIMEOUT => 45,
		CURLOPT_FILETIME => TRUE,
		CURLOPT_INTERFACE => "185.60.135.248",
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$this->lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		var_dump(curl_getinfo($curl));
		exit;
		
		;
		curl_close($curl);
		$result=json_decode($json);
		exit;
		$curl = curl_init('https://obmenneg.ru/request-bc_parser.html');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		var_dump($json);
		exit;
		
		$url='/api/contact_messages/19465116/';
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
		$k=0;
		$l=0;
		var_dump($result);
		foreach ($result->data->message_list as $msg){
			if ($msg->sender->username=='Obmenneg'){
				if (stripos($msg->msg, 'Проверим еще раз!') !== false){
					$l=1;
				}
				continue;
			}
			var_dump(trim($msg->msg));
			if ($msg->msg){
				if ($l>0){
					$k++;
				}
				if (mb_strtolower(trim($msg->msg))=="нет"){
					var_dump('нашел нет');
					exit;
				}
				if (mb_strtolower(trim($msg->msg))=="да"){
					var_dump('нашел да');
					exit;
				}
			}
		}
		exit;
		$url='/api/contact_info/19424593/';
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
		$results=json_decode($json);
		var_dump($results);
		exit;
		$api = new API($this->yandextoken);
		$operation_history = $api->operationHistory(array("records"=>10, "details"=>"true"));
		foreach ($operation_history->operations as $operat){
			var_dump($operat);
			echo '<br>';
		}
		$qqq=\DB::connection('obmenneg')->table('lbtc_robots')->where('id', '396')->first();
		var_dump($qqq);
		
		exit;
		$api = new API($this->yandextoken);
		$request_payment = $api->requestPayment(array(
			"pattern_id" => "p2p",
			"to" => "410013107113355",
			"amount_due" => "1",
			"comment" => "перевод",
			"message" => ""
		));
		var_dump($request_payment);
		$process_payment = $api->processPayment(array(
			"request_id" => $request_payment->request_id,
		));
		var_dump($process_payment);
		/*
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select id_ad, sum(amount) as amount, sum(amount*0.99) as cmount_com, sum(amount_btc) as amount_btc, sum(amount)/sum(amount_btc) as kurs,
			sum(amount*0.99)/sum(amount_btc) as kurs_com from lbtc_robots where id_ad='617372' and created_at>'2018-02-14' and status='9' group by id_ad";
		$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select id_ad, sum(amount) as amount, sum(amount*1.02) as cmount_com, sum(amount_btc) as amount_btc, sum(amount)/sum(amount_btc) as kurs,
			sum(amount*1.02)/sum(amount_btc) as kurs_com from lbtc_robots where id_ad='609849' and created_at>'2018-02-14' and status='9' group by id_ad";
		$buy=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		*/
		/*var_dump($sell);
		var_dump($buy);
		var_dump($sell['kurs']-$buy['kurs']);
		$pr1=(($sell['kurs']-$buy['kurs'])/$sell['kurs'])*100;
		var_dump($pr1);
		var_dump($sell['kurs_com']-$buy['kurs_com']);
		$pr2=(($sell['kurs_com']-$buy['kurs_com'])/$sell['kurs_com'])*100;
		var_dump($pr2);
		var_dump(($sell['kurs']-$buy['kurs'])-($sell['kurs_com']-$buy['kurs_com']));
		
		if ($buy['amount_btc']>$sell['amount_btc']){
			var_dump('Продаем, не дешевле чем' . $buy['kurs_com'] );
		}
		if ($buy['amount_btc']<$sell['amount_btc']){
			var_dump('Покупаем');
		}*/
		
		/*$sql="select type, amount as amount, amount_btc, created from test where type='2' order by created desc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($buys as $buy){
				$sql="select type, amount as amount, amount_btc, created from test where type='1' order by created asc";
				$se=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			}
		if (!$buy){
			$created='2018-02-14';
			$a_b='10';
		}
		else{
			$created=$buy['created'];
			$a_b=$buy['amount_btc'];
		}
		$sql="select type, sum(amount) as amount, sum(amount_btc) as amount_btc from test where type='1' and created>'$created' or amount_btc<'' group by type";
		$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		var_dump($buy);
		$kurs=$sell['amount']/$sell['amount_btc'];
		$buy=$kurs*1.01;
		var_dump($sell);
		var_dump($kurs);
		var_dump($buy);*/
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select * from lbtc_robots where id_ad in ('617372', '609849') and status='9' order by created_at asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into balancing (id_ad, contact_id, amount, amount_btc, course, remainder, created)
			select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM balancing WHERE contact_id=?)";
		$sthInsert=$pdo->prepare($sql);
		foreach ($stats as $stat){
			if ($stat['id_ad']=='617372'){
				$amount=$stat['amount']*0.99;
			}
			elseif ($stat['id_ad']=='609849'){
				$amount=$stat['amount']*1.02;
			}
			else{
				$amount=0;
			}
			$course=$amount/$stat['amount_btc'];
			$sthInsert->execute([$stat['id_ad'], $stat['contact_id'], $stat['amount'], $stat['amount_btc'], $course, $stat['amount_btc'], 
			$stat['created_at'], $stat['contact_id']]);
		}
		$sql="select * from balancing where id_ad='609849' and status='0' order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="update balancing set status=?, return_course=?, remainder=?, prosent=?
			WHERE contact_id=?";
		$sthUpdate=$pdo->prepare($sql);
		$sql="update balancing set remainder=?, return_course=?
			WHERE contact_id=?";
		$sthUpdateed=$pdo->prepare($sql);
		foreach ($buys as $buy){
			$i=1;
			$intt=\DB::connection('obmenneg')->table('balancing')->where('id_ad', '617372')->where('status', '0')->get();
			$count=count($intt);
			$remainder_buy=$buy['remainder'];
			while ($i<=$count) {
				$sql="select * from balancing where id_ad='617372' and status='0' order by created asc";
				$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
				$remainder_sell=$sell['remainder'];
				if ($sell['return_course']){
					$sell_course=($sell['return_course']+$buy['course'])/2;
				}
				else{
					$sell_course=$buy['course'];
				}
				if ($buy['return_course']){
					$buy_course=($buy['return_course']+$sell['course'])/2;
				}
				else{
					$buy_course=$sell['course'];
				}
				if ($remainder_buy>$remainder_sell){
					$remainder_buy=$remainder_buy-$remainder_sell;
					$remainder_sell=0;
					$prosent=(($buy_course-$sell_course)/$buy_course)*100;
					$sthUpdate->execute(['1',$sell_course,$remainder_sell,$prosent,$sell['contact_id']]);
					$sthUpdateed->execute([$remainder_buy,$buy_course,$buy['contact_id']]);
				}
				else{
					$remainder_sell=$remainder_sell-$remainder_buy;
					$remainder_buy=0;
					$prosent=(($buy_course-$sell_course)/$buy_course)*100;
					$sthUpdate->execute(['1',$buy_course,$remainder_buy,$prosent,$buy['contact_id']]);
					$sthUpdateed->execute([$remainder_sell,$sell_course,$sell['contact_id']]);
					break;
				}
				$i++;
			}
			continue;
		}
		exit;

		foreach ($buys as $buy){
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
			$sql="select * from test where type='1' and status='0' order by created asc";
			$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($sell['otpusk_kurs']){
				$sell_kurs=($sell['otpusk_kurs']+$buy['kurs'])/2;
			}
			else{
				$sell_kurs=$buy['kurs'];
			}
			if ($buy['otpusk_kurs']){
				$buy_kurs=($buy['otpusk_kurs']+$sell['kurs'])/2;
			}
			else{
				$buy_kurs=$sell['kurs'];
			}
			if ($buy['amount_btc']>$sell['amount_btc']){
				$sthUpdate->execute(['1',$sell_kurs,'1',$sell['created']]);
				$buy['amount_btc']=$buy['amount_btc']-$sell['amount_btc'];
				$sthUpdateed->execute([$buy['amount_btc'],$buy_kurs,'2',$buy['created']]);
			}
			else{
				$sthUpdate->execute(['1',$buy_kurs,'2',$buy['created']]);
				$sell['amount_btc']=$sell['amount_btc']-$buy['amount_btc'];
				$sthUpdateed->execute([$sell['amount_btc'],$sell_kurs,'1',$sell['created']]);
				continue;
			}
			
		}
	}
		
}
