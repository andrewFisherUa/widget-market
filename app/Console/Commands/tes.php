<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use baibaratsky\WebMoney\Signer;
use Illuminate\Support\Str;
class tes extends Command
{
	
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tes';

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
		//$yourKeyData="5P7KG2uN2viZP9rQBEPkJVh6eoenA1Z1";
		/*$signer = hash('sha256', '970439860595'.'R605854660223'.time().'331799884688'.'1'.$yourKeyData);
		//wmid + lmi_payee_purse + lmi_payment_no + lmi_clientnumber + lmi_clientnumber_type + secret_key.
		$data = array(
		"wmid"=>"970439860595",
		"lmi_payee_purse"=>"R605854660223",
		"lmi_payment_no"=>time(),
		"lmi_payment_amount"=>0.01,
		"lmi_payment_desc"=>"Оплата за размещение рекламы",
		"lmi_clientnumber"=>"331799884688",
		"lmi_clientnumber_type"=> 1,
		"lmi_sms_type"=> 1,
		"sha256"=>$signer,
		"lang"=>"ru-RU"

		);
		/*lmi_clientnumber_type = мобильный телефон - 0
		ВМИД - 1
		емайл - 2
		lmi_sms_type = смс - 1
		ussd запрос - 2
		авто -3 
		вм счет -4 
		*/	
		/*$data_string = json_encode($data);                                                                                   
																															 
		$ch = curl_init('https://merchant.webmoney.ru/conf/xml/XMLTransRequest.asp');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
																															 
		$result = curl_exec($ch);
		var_dump($result);
		exit;*/
		//wmid + lmi_payee_purse +lmi_wminvoiceid+lmi_clientnumber_code+secret_key
		/*$signer = hash('sha256', '970439860595'.'R605854660223'.'720951817'.'673845'.$yourKeyData);
		$data = array(
		"wmid"=>"970439860595",
		"lmi_payee_purse"=>"R605854660223",
		"lmi_clientnumber_code"=>673845,
		"lmi_wminvoiceid"=>720951817,
		"sha256"=>$signer,
		"lang"=>"ru-RU"
		);
		$data_string = json_encode($data);                                                                                   
																															 
		$ch = curl_init('https://merchant.webmoney.ru/conf/xml/XMLTransConfirm.asp');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
																															 
		$result = curl_exec($ch);
		
		var_dump($result);
		*/
		/*$postData = file_get_contents('https://localbitcoins.net/api/payment_methods/');
		$data = parse_str($postData);
		$data = json_decode($postData, true);
		var_dump($data);*/
		/*$url="https://localbitcoins.net/api/ads/";
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL,$url); // Устанавливаем URL на который посылать запрос  
		curl_setopt($ch, CURLOPT_HEADER, 1); //  Результат будет содержать заголовки
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // Результат будет возвращём в переменную, а не выведен.
		curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Таймаут после 4 секунд 
		curl_setopt($ch, CURLOPT_POST, 1); // Устанавливаем метод
		curl_setopt($ch, CURLOPT_POSTFIELDS, "256521830b85a3a0f1d29819373e62e8&256521830b85a3a0f1d29819373e62e8&msg=ку"); // посылаемые значения
		$result = curl_exec($ch);  
		curl_close($ch);
		var_dump($result);*/
		
		/*$ch = curl_init('https://localbitcoins.net/api/contact_dispute/3tGaPTGu8UmmT5oyXyfMyn');
 
        $nonce = time();
        //$sig = mb_strtoupper( hash_hmac( 'sha256', $nonce.'256521830b85a3a0f1d29819373e62e8/api/myself/', '256521830b85a3a0f1d29819373e62e8' ) );
		$lbtckey='256521830b85a3a0f1d29819373e62e8';
		$lbtcsecret='5ce35dbacacce2afa66809801a55c0c008a8f83cabbe5cd15ff38d2d93a7da63';
		$data=array(
			'php'=>'3tGaPTGu8UmmT5oyXyfMyn'
		)
		$postfields = http_build_query($data);
		var_dump($postfields);
		$message = $nonce.$lbtckey.'/api/contact_dispute/'.$postfields;
		$signature = strtoupper(hash_hmac('sha256',$message,$lbtcsecret));
        $options = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER =>   array(
                        'Apiauth-Key: 256521830b85a3a0f1d29819373e62e8',
                        'Apiauth-Nonce:'.$nonce,
                        'Apiauth-Signature:'.$signature
                ),
				
        );
       
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
 
        var_dump($result);
		*/
		
		
		
		/*$url='/api/contact_message_post/16555613/';
		$lbtckey='256521830b85a3a0f1d29819373e62e8';
		$lbtcsecret='5ce35dbacacce2afa66809801a55c0c008a8f83cabbe5cd15ff38d2d93a7da63';
				
		$nonce = time();
		$mes=array(
			'msg'=>'ку'
		);
		$m = http_build_query($mes);
		$message = $nonce.$lbtckey.$url.$m;
		var_dump($nonce);
		$signature = strtoupper(hash_hmac('sha256',$message,$lbtcsecret));
		var_dump($signature);
		$ch = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $m,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
		);
		curl_setopt_array($ch, $options);
		$result=(curl_exec ($ch));
		curl_close($ch);
		var_dump($result);
		*/
		
		
		//-------------Сбор всех активных предложений на киви (пока что только)
		/*$url='/api/ads/';
		$lbtckey='256521830b85a3a0f1d29819373e62e8';
		$lbtcsecret='5ce35dbacacce2afa66809801a55c0c008a8f83cabbe5cd15ff38d2d93a7da63';
				
		$nonce = time();
		$mes=array(
			'visible'=>1,
			'trade_type'=>'ONLINE_SELL',
			'currency'=>'RUB'
		);
		$m = http_build_query($mes);
		$message = $nonce.$lbtckey.$url.$m;
		var_dump($nonce);
		$signature = strtoupper(hash_hmac('sha256',$message,$lbtcsecret));
		var_dump($signature);
		$ch = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_POSTFIELDS => $m,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
		);
		curl_setopt_array($ch, $options);
		$result=(curl_exec ($ch));
		curl_close($ch);
		$res=json_decode($result);
		foreach ($res->data->ad_list as $ad){
			if ($ad->data->online_provider=="QIWI"){
				$qiwi=$ad->data->ad_id;
			}
		}
		
		//---------------наход все открытые сделки по id который выше нашли
		$url='/api/dashboard/';
		$lbtckey='256521830b85a3a0f1d29819373e62e8';
		$lbtcsecret='5ce35dbacacce2afa66809801a55c0c008a8f83cabbe5cd15ff38d2d93a7da63';
				
		$nonce = time();
		$mes=array(
		
		);
		$m = http_build_query($mes);
		$message = $nonce.$lbtckey.$url.$m;
		var_dump($nonce);
		$signature = strtoupper(hash_hmac('sha256',$message,$lbtcsecret));
		var_dump($signature);
		$ch = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			//CURLOPT_POSTFIELDS => $m,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
		);
		curl_setopt_array($ch, $options);
		$result=(curl_exec ($ch));
		curl_close($ch);
		$res=json_decode($result);
		foreach ($res->data->contact_list as $id_contact){
			//var_dump($id_contact);
			if ($id_contact->data->advertisement->id==$qiwi){
				$pes=$id_contact->data->contact_id;
			}
		}
		
		
		//---------------------открываем чат с пользователем который открыл сделку которая выше и отправляем ему кошелек
		$url='/api/contact_messages/'.$pes.'/';
		$lbtckey='256521830b85a3a0f1d29819373e62e8';
		$lbtcsecret='5ce35dbacacce2afa66809801a55c0c008a8f83cabbe5cd15ff38d2d93a7da63';
				
		$nonce = time();
		$mes=array(
		
		);
		$m = http_build_query($mes);
		$message = $nonce.$lbtckey.$url.$m;
		var_dump($nonce);
		$signature = strtoupper(hash_hmac('sha256',$message,$lbtcsecret));
		var_dump($signature);
		$ch = curl_init('https://localbitcoins.net'.$url);
		$options = array(
			CURLOPT_HTTPGET => 1,
			//CURLOPT_POSTFIELDS => $m,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Apiauth-Key:'.$lbtckey,
			'Apiauth-Nonce:'.$nonce,
			'Apiauth-Signature:'.$signature
			),
		);
		curl_setopt_array($ch, $options);
		$result=(curl_exec ($ch));
		curl_close($ch);
		$res=json_decode($result);
		$b=0;
		
		foreach ($res->data->message_list as $message){
			//var_dump($message);
			if ($message->sender->username=='Obmenneg'){
				$b=1;
			}
		}
		var_dump($b);
		if ($b==0){
			$url='/api/contact_message_post/'.$pes.'/';
			$lbtckey='256521830b85a3a0f1d29819373e62e8';
			$lbtcsecret='5ce35dbacacce2afa66809801a55c0c008a8f83cabbe5cd15ff38d2d93a7da63';
					
			$nonce = time();
			$mes=array(
				'msg'=>'мой кошеле к я тебе не скажу'
			);
			$m = http_build_query($mes);
			$message = $nonce.$lbtckey.$url.$m;
			var_dump($nonce);
			$signature = strtoupper(hash_hmac('sha256',$message,$lbtcsecret));
			var_dump($signature);
			$ch = curl_init('https://localbitcoins.net'.$url);
			$options = array(
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => $m,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_FILETIME => TRUE,
				CURLOPT_HTTPHEADER => array(
				'Apiauth-Key:'.$lbtckey,
				'Apiauth-Nonce:'.$nonce,
				'Apiauth-Signature:'.$signature
				),
			);
			curl_setopt_array($ch, $options);
			$result=(curl_exec ($ch));
			curl_close($ch);
			var_dump($result);
		}
		*/
		//----------------переходим на киви
		/*$url='https://edge.qiwi.com/payment-history/v1/persons/79281131180/payments?rows=10&operation=IN';
		$token='22b5417f1538c16d7a5f68cc8d4118a6';
		$ch = curl_init($url);
		/*$mes=array(
			'rows'=>50
		);
		//$m = http_build_query($mes);
		$options = array(
			CURLOPT_HTTPGET => 1,
			//CURLOPT_POSTFIELDS => $mes,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Content-type: application/json',
			'Authorization: Bearer '.$token
			),
		);
		curl_setopt_array($ch, $options);
		$result=(curl_exec ($ch));
		curl_close($ch);
		$res=json_decode($result);
		//var_dump($res);
		foreach ($res->data as $q){
			var_dump($q->status);
		}
		exit;*/
	}
}
