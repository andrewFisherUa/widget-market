<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \App\User;
use Auth;
use App\UserProfile;
use ZipArchive;
use Illuminate\Support\Facades\Validator;
use App\AllNotification;
use App\Notifications\Payout;
#use App\Notifications\PayoutAction;
use Mail;
class AdvertPayoutController extends Controller
{
	private $WebMoneyWmid="970439860595"; //wmid на который будет выполнен перевод
	private $WebMoneyR="R605854660223"; //кошелек на который будет выполнен перевод
	private $secretKey='5P7KG2uN2viZP9rQBEPkJVh6eoenA1Z1'; //ключ webmoney для x20 интерфейса
	
	public function Requisites($id=0){
		$user=\Auth::user();
		if ($id){
			if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')){
				$user=\App\User::find($id);
			}
			else{
				if ($user->id!=\Auth::user()->id){
					return abort(404);
				}
			}
		}
		$requisite=\App\Requisite::where('user_id', $user->id)->first();
		return view('advertiser.payouts.requisites', ['user'=>$user, 'requisite'=>$requisite]);
	}
	
	public function SaveRequisites(Request $request){
		$id=$request->input('id');
		$form=$request->input('form');
		$user_id=$request->input('user_id');
		#$type_org=$request->input('type_org');
		$chstatus=$request->input('chstatus');
		
		$type_org=$request->input('form');
		#$type_payout=$request->input('type_payout');
		$type_payout=$request->input('nds');
		$position=$request->input('position');
		$name=$request->input('name');
		$firm_name=$request->input('firm_name');
		$legale_male=$request->input('legale_male');
		$fact_male=$request->input('fact_male');

		$series_certificate=$request->input('series_certificate',null);
		$number_certificate=$request->input('number_certificate',null);
		$date_certificate=$request->input('date_certificate',null);
		$inn=$request->input('inn');
		$kpp=$request->input('kpp');
		
		$ogrn=$request->input('ogrn');
		$okved=$request->input('okved');
		$name_bank=$request->input('name_bank');
		$account=$request->input('account');
		$kor_account=$request->input('kor_account');
		$bik=$request->input('bik');
		$dogovor=$request->input('dogovor');
	  
		if ($form==1){
			
			$validator=Validator::make($request->all(),[
				'position' => 'required',
				'form' => 'required',
				'name' => 'required',
                'firm_name' => 'required',
				'legale_male' => 'required',
				'fact_male' =>'required',
				'series_certificate' =>'required',
				'number_certificate' =>'required',
				'date_certificate' =>'required',
				'inn' => 'required|unique:user_entitys',
				#'kpp'=> 'required|unique:user_entitys',
				'ogrn' => 'required|unique:user_entitys',
				'okved' => 'required',
				'name_bank' => 'required',
				'account' => 'required|unique:user_entitys',
				'kor_account' => 'required|unique:user_entitys',
				'bik' => 'required',
			]);
		}
		elseif ($form==2){
			$validator=Validator::make($request->all(),[
				'position' => 'required',
				'form' => 'required',
				'name' => 'required',
                'firm_name' => 'required',
				'legale_male' => 'required',
				'fact_male' =>'required',
				'series_certificate' =>'required',
				'number_certificate' =>'required',
				'date_certificate' =>'required',
				'inn' => 'required|unique:user_entitys',
				'kpp'=> 'required|unique:user_entitys',
				'ogrn' => 'required|unique:user_entitys',
				'okved' => 'required',
				'name_bank' => 'required',
				'account' => 'required|unique:user_entitys',
				'kor_account' => 'required|unique:user_entitys',
				'bik' => 'required',
		    ]);
		}
		
		
		/*
		if ($form==1){
			
			$validator=Validator::make($request->all(),[
				'position' => 'required',
				'form' => 'required',
				'name' => 'required',
				'firm_name' => 'required',
				'legale_male' => 'required',
				'fact_male' => 'required',
				'series_certificate' => 'required',
				'number_certificate' => 'required',
				'date_certificate' => 'required',
				
				'inn' => 'required|unique:user_entitys',
				'ogrn' => 'required|unique:user_entitys',
				'okved' => 'required',
				'name_bank' => 'required',
				'account' => 'required|unique:user_entitys',
				'kor_account' => 'required|unique:user_entitys',
				'bik' => 'required',
			]);
		}
		elseif ($form==2){
			$validator=Validator::make($request->all(),[
				'position' => 'required',
				'form' => 'required',
				'name' => 'required',
				'firm_name' => 'required',
				'legale_male' => 'required',
				'fact_male' => 'required',
				'inn' => 'required|unique:user_entitys',
				'kpp'=> 'required|unique:user_entitys',
				'ogrn' => 'required|unique:user_entitys',
				'okved' => 'required',
				'name_bank' => 'required',
				'account' => 'required|unique:user_entitys',
				'kor_account' => 'required|unique:user_entitys',
				'bik' => 'required',
			]);
		}
		*/
		#die();
		
	   
	   #foreach ($validator->messages()->getMessages() as $field_name => $messages)
       #  {
       # var_dump([$field_name,$messages]); 
	#	echo "<hr>";
    #    } 
        $backUrl=url()->previous();
        if(preg_match('/\#/',$backUrl)){
           $backUrl=preg_replace('/\#.*$/','#payments',$backUrl);
        }else{
         $backUrl.="#payments";
        }
        #return Redirect($backUrl);

		if ($validator->fails()){
			
			return Redirect($backUrl)->withErrors($validator)->withInput();
		}
		$requisite=\App\Requisite::where('user_id', $user_id)->first();
		#if ($id){
		#	$requisite=\App\Requisite::find($id);
			
		#	$requisite=\App\Requisite::findOrFail($id);
		#}else{
        if(!$requisite)
			$requisite=new \App\Requisite;
		#}
		
		
			
		$requisite->user_id=$user_id;
		$requisite->type_payout=$type_payout;
		$requisite->type_org=$type_org;
		$requisite->name=$name;
        $requisite->firm_name=$firm_name;
        $requisite->position=$position;
        $requisite->legale_male=$legale_male;
		$requisite->fact_male=$fact_male;
		$requisite->series_certificate=$series_certificate;
		$requisite->number_certificate=$number_certificate;
		$requisite->date_certificate=$date_certificate;
		$requisite->inn=$inn;
		$requisite->kpp=$kpp;
		$requisite->ogrn=$ogrn;
		$requisite->okved=$okved;
		$requisite->name_bank=$name_bank;
		$requisite->account=$account;
		$requisite->kor_account=$kor_account;
		$requisite->bik=$bik;
		
		if($chstatus){
		$requisite->improved=1;
	    $requisite->improved_name=Auth::user()->name;
		}else{
		$requisite->improved=0;
	    $requisite->improved_name=null;
		}
		$requisite->dogovor=$dogovor;
		
		$requisite->save();
        return Redirect($backUrl)->with('message_success', 'Реквизиты ok');
	    echo "<pre>"; print_r([$user_id,$form,$request->toArray()]); echo "</pre>";  die();	
		return back();
		echo "<pre>"; print_r([$form,$validator->fails(),$request->toArray(),$requisite]); echo "</pre>";  die();
		echo "<pre>"; print_r([$form,$validator->fails(),$request->toArray()]); echo "</pre>";  die();
		
		$requisite->type_org=$type_org;
		$requisite->type_payout=$type_payout;
		$requisite->position=$position;
		$requisite->name=$name;
		$requisite->firm_name=$firm_name;
		$requisite->legale_male=$legale_male;
		$requisite->fact_male=$fact_male;
		$requisite->series_certifiacte=$series_certifiacte;
		$requisite->number_certificate=$number_certificate;
		$requisite->date_certificate=$date_certificate;
		$requisite->inn=$inn;
		$requisite->kpp=$kpp;
		$requisite->ogrn=$ogrn;
		$requisite->okved=$okved;
		$requisite->name_bank=$name_bank;
		$requisite->account=$account;
		$requisite->kor_account=$kor_account;
		$requisite->bik=$bik;
		$requisite->save();
		
		return back();
	}
	
	public function firstStepPayout(Request $request){
		
		/*$docxFile = '/home/www/widget.market-place.su/public/dogovor/ip.docx';
		$params = array(
			'(time)'=>'1234'
		);
		$zip = new ZipArchive();
		if (!$zip->open($docxFile)) {
			die('File not open.');
		}
		$documentXml = $zip->getFromName('word/document.xml');
 
$documentXml = str_replace(array_keys($params), array_values($params), $documentXml);
 
$zip->deleteName('word/document.xml');
$zip->addFromString('word/document.xml', $documentXml);

$zip->close();
		exit;*/
	
		$payout_sistem=$request->input('payout_sistem');
		$userId=$request->input('user_id');
		if ($payout_sistem==1){
			return redirect()->route('advertiser.entity.payout.get', ['user_id'=>$userId]);
		}
		elseif ($payout_sistem==2){
			return view('advertiser.payouts.webmoney');
		}
		else{
			return abort(404);
		}
	}
	
	public function GetentityPayout(Request $request){
		$user_id=$request->input('user_id');
		return view('advertiser.payouts.legal_entity', ['user_id'=>$user_id]);
	}
	
	public function entityPayout(Request $request){
		$user_id=$request->input('user_id');
		$nds=$request->input('nds');
		$form=$request->input('form');
		$position=$request->input('position');
		$name=$request->input('name');
		$firm_name=$request->input('firm_name');
		$legale_male=$request->input('legale_male');
		$fact_male=$request->input('fact_male');
		$inn=$request->input('inn');
		$kpp=$request->input('kpp');
		$ogrn=$request->input('ogrn');
		$okved=$request->input('okved');
		$name_bank=$request->input('name_bank');
		$account=$request->input('account');
		$kor_account=$request->input('kor_account');
		$bik=$request->input('bik');
		if (!$nds){
			return back()->with('message_danger', "Выберите как будет производится оплата.")->withInput();
		}
		if (!$form){
			return back()->with('message_danger', "Выберите форму организации.")->withInput();
		}
		if ($form==1){
			$validator=Validator::make($request->all(),[
				'nds' => 'required',
				'form' => 'required',
				'name' => 'required',
				'firm_name' => 'required',
				'legale_male' => 'required',
				'fact_male' => 'required',
				'inn' => 'required|unique:user_entitys',
				'ogrn' => 'required|unique:user_entitys',
				'okved' => 'required',
				'name_bank' => 'required',
				'account' => 'required|unique:user_entitys',
				'kor_account' => 'required|unique:user_entitys',
				'bik' => 'required',
			]);
		}
		elseif ($form==2){
			$validator=Validator::make($request->all(),[
				'nds' => 'required',
				'form' => 'required',
				'name' => 'required',
				'firm_name' => 'required',
				'legale_male' => 'required',
				'fact_male' => 'required',
				'inn' => 'required|unique:user_entitys',
				'kpp'=> 'required|unique:user_entitys',
				'ogrn' => 'required|unique:user_entitys',
				'okved' => 'required',
				'name_bank' => 'required',
				'account' => 'required|unique:user_entitys',
				'kor_account' => 'required|unique:user_entitys',
				'bik' => 'required',
			]);
		}
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}
		$entity=new \App\UserEntity;
		$entity->type_payout=$nds;
		$entity->user_id=$user_id;
		$entity->position=$position;
		$entity->form=$form;
		$entity->name=$name;
		$entity->firm_name=$firm_name;
		$entity->legale_male=$legale_male;
		$entity->fact_male=$fact_male;
		$entity->inn=$inn;
		if ($form==2){
			$entity->kpp=$kpp;
		}
		$entity->ogrn=$ogrn;
		$entity->okved=$okved;
		$entity->name_bank=$name_bank;
		$entity->account=$account;
		$entity->kor_account=$kor_account;
		$entity->bik=$bik;
		$entity->save();
		return redirect()->route('advertiser.my.entity', ['user_id'=>$user_id]);
	}
	
	public function MyEntity($user_id=0){
		if ($user_id){
			$user=\App\User::find($user_id);
			if (!$user){
				return abort(403);
			}
		}
		else{
			$user=\Auth::user();
			//$user=\App\User::find(1);
		}
		$entity=\App\UserEntity::where('user_id', $user->id)->first();
		return view('advertiser.payouts.my_entity', ['user'=>$user, 'entity'=>$entity]);
	}
	
	public function entityDogovor($id){
		$entity=\App\UserEntity::find($id);
		return view('advertiser.payouts.dogovor', ['entity'=>$entity]);
	}
	
	public function payout(Request $request){
		$payout_sistem=$request->input('payout_sistem');
        $summa=$request->input('summa');
		$userId=$request->input('user_id');
		$ura=\App\User::find($userId);
		  $tilly=" как нибудь";
		 if($payout_sistem==1){
			 $tilly=" через банк";
		 }else{
		 			 $tilly=" через электронный кошелёк";
		 }		
        		$use=\App\User::find(39); 
		$text="Пользователь \"".$ura->name."\" пытается пополнить баланс на ".$summa." руб. $tilly";
		Mail::send('email.payout.payment',['token'=>$text],function($u) use ($use){
					$u->from('support@market-place.su', 'Market-place');
					$u->to($use->email);
					$u->subject('Попытка пополнения баланса');
				});
				$notif_header='Попытка пополнения баланса';
			$notif=new AllNotification;
			$notif->user_id=$use->id;
			$notif->header=$notif_header;
			$notif->body=$text;
			$notif->save();
			\Notification::send(\App\User::find($use->id), (new Payout($notif->id)));
		# var_dump([$text,$payout_sistem,$summa,$ura->name]); die();
		
		
        if($payout_sistem==1){
		 $type=$request->input('invoicetype');
         $link=url()->previous();
         $tmp =parse_url($link);


          if(isset($tmp["path"]) && preg_match('/^\/admin\/([\d]+)\//ui',$tmp["path"],$m)){
           $url='/admin/'.$m[1].'/invoice/create?summa='.$summa.'&type='.$type;
          }else{
          $url='/adv_/invoice/create?summa='.$summa.'&type='.$type;
          }
          return Redirect($url);

        }
       
 
		$userId=$request->input('user_id');
		
		$wmid=trim($request->input('wmid'));
		$user=\App\User::find($userId);
		
		
		//736679757699
		if (!$user){
			return back()->with('message_danger', "Произошла ошибка, повторите попытку снова.");
		}
		
		if (strlen($wmid)!=12){
			return back()->with('message_danger', "Не верно указан wmid.");
		}
		$signer = hash('sha256', $this->WebMoneyWmid.$this->WebMoneyR.time().$wmid.'1'.$this->secretKey);
		//wmid + lmi_payee_purse + lmi_payment_no + lmi_clientnumber + lmi_clientnumber_type + secret_key.
		$data = array(
		"wmid"=>$this->WebMoneyWmid,
		"lmi_payee_purse"=>$this->WebMoneyR,
		"lmi_payment_no"=>time(),
		"lmi_payment_amount"=>$summa,
		"lmi_payment_desc"=>"Оплата за размещение рекламы",
		"lmi_clientnumber"=>$wmid,
		"lmi_clientnumber_type"=> 1,
		"lmi_sms_type"=> 1,
		"sha256"=>$signer,
		"lang"=>"ru-RU"
		);
		$data_string = json_encode($data);
		
		$ch = curl_init('https://merchant.webmoney.ru/conf/xml/XMLTransRequest.asp');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		
		$result=curl_exec($ch);
		
		$res=explode(",", $result);
		
		if (stristr($res[0], 'retval')){
			$error=preg_replace("/[^0-9]/", '', $res[0]);
			$text=$this->WMError($error);
			//var_dump($res[0]); die();
			return back()->with('message_danger', $text);
		}
			
		if (stristr($res[1], 'wminvoiceid') == FALSE){
			return back()->with('message_danger', "Произошла ошибка, повторите попытку снова.");
		}
		$wminvoiceid = preg_replace("/[^0-9]/", '', $res[1]);
		if (stristr($res[3], 'retval') == FALSE){
			return back()->with('message_danger', "Произошла ошибка, повторите попытку снова.");
		}
		$retval = preg_replace("/[^0-9]/", '', $res[3]);
		if ($retval!=0){
			return back()->with('message_danger', "Произошла ошибка, повторите попытку снова.");
		}
		$payouts=new \App\Advertises\AdvertiserPayout;
		$payouts->sistem=$payout_sistem;
		$payouts->user_id=$userId;
		$payouts->summa=$summa;
		$payouts->wmid=$wmid;
		$payouts->wminvoiceid=$wminvoiceid;
		$payouts->save();
		return redirect()->action('AdvertPayoutController@wbSuccessGet', ['wminvoiceid'=>$wminvoiceid]);
	}
	
	public function wbSuccessGet(Request $request){
		$wminvoiceid=$request->input('wminvoiceid');
		if (!$wminvoiceid){
			return abort(404);
		}
		$payouts=\App\Advertises\AdvertiserPayout::where('wminvoiceid', $wminvoiceid)->first();
		if (!$payouts){
			return abort(404);
		}
		
		return view('advertiser.payouts.wbsucces', ['payouts'=>$payouts]);
	}
	
	public function wbSuccess(Request $request){
		$wminvoiceid=$request->input('wminvoiceid');
		$code=$request->input('code');
		
		
		$signer = hash('sha256', $this->WebMoneyWmid.$this->WebMoneyR.$wminvoiceid.$code.$this->secretKey);
		//wmid + lmi_payee_purse +lmi_wminvoiceid+lmi_clientnumber_code+secret_key
		$data = array(
		"wmid"=>$this->WebMoneyWmid,
		"lmi_payee_purse"=>$this->WebMoneyR,
		"lmi_clientnumber_code"=>$code,
		"lmi_wminvoiceid"=>$wminvoiceid,
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
		$res=explode(",", $result);
		if (stristr($res[0], 'retval')){
			$error=preg_replace("/[^0-9]/", '', $res[0]);
			$text=$this->WMError($error);
			return back()->with('message_danger', $text);
		}
		if (stristr($res[8], 'retval') == FALSE){
			return back()->with('message_danger', "Произошла ошибка, повторите попытку снова.");
		}
		$retval = preg_replace("/[^0-9]/", '', $res[8]);
		if ($retval!=0){
			return back()->with('message_danger', "Произошла ошибка, повторите попытку снова.");
		}
		$wmtransid = preg_replace("/[^0-9]/", '', $res[0]);
		$payouts=\App\Advertises\AdvertiserPayout::where('wminvoiceid', $wminvoiceid)->first();
		if (!$payouts){
			return back()->with('message_danger', "В системе нет такого платежа.");
		}
		$payouts->status=1;
		$payouts->wmtransid=$wmtransid;
		$payouts->save();
		$userProfile=\App\UserProfile::where('user_id', $payouts->user_id)->first();
		if (!$userProfile){
			return back()->with('message_danger', "Произошла ошибка с юзреом, обратитесь в службу поддержки.");
		}
		#$userProfile->balance=$userProfile->balance+$payouts->summa;
		#$userProfile->save();
	    $ssu=new \App\Models\Advertises\Payment();
	    $ssu->insertSumma($payouts->user_id,$payouts->user_id,$payouts->summa,'перечисление webmoney','webmoney');
		return redirect('/')->with('message_success', "Пополнение баланса прошло успешно");
		//return redirect()->action('TestHomeController@index');
		
	}
	
	public function WMError($error){
		if ($error==500){
			return "Вы неверно указали код, полученный по SMS для оплаты с вашего WebMoney-чека, возможно это код от другого платежа, дождитесь кода и попробуйте указать его снова.";
		}
		elseif ($error==501 or $error==504 or $error==505 or $error==506 or $error==507 or $error==508 or $error==509 or $error==510){
			return "В данный момент приостановлен прием платежей, попробуйте, пожалуйста, позднее.";
		}
		elseif ($error==502){
			return "Произошла ошибка, повторите все сначала.";
		}
		elseif ($error==503){
			return "В данный момент реализована возможность оплаты только для следующих типов кошельков WMZ, WME, WMR, WMU, WMG, WMB. Вы пытаетесь заплатить на кошелек с типов ВМ-валюты, который пока не поддерживается.";
		}
		elseif ($error==504){
			return "В данный момент приостановлен прием платежей, попробуйте, пожалуйста, позднее.";
		}
		elseif ($error==511){
			return "В данный момоент можно заплатить только введя WM-идентификатор, другие способы пока не поддерживаются.";
		}
		elseif ($error==516){
			return "Указанный WM-идентификатор не найден в системе.";
		}
		elseif ($error==517){
			return "Найден указанный вами WM-идентификатор, но телефон прописанный в нем не проверен, пожалуйста, перейдите на passport.webmoney.ru и там в личных данных проверьте свой номер телефона, получив SMS и введя код проверки.";
		}
		elseif ($error==518){
			return "Найден указанный вами WM-идентификатор, но на его кошельке средств для оплаты недостаточно.";
		}
		elseif ($error==519){
			return "В WM-идентификаторе, который был найден, есть кошелек с достаточным для оплаты количеством средств, но этот кошелек настроен на прием оплаты в merchant.webmoney и заплатить с него нельзя. Пожалуйста, или пополните другой кошелек или зайдите на https://security.webmoney.ru/asp/trustlistmerch.asp и укажите точно какой именно кошелек необходимо использовать для оплаты в этом типе платежей по телефону.";
		}
		elseif ($error==524){
			return "Вы пытаетесь заплатить сумму, которая превышает лимит, установленный по умолчанию или назначенный вами. Отрегулировать лимиты для каждого типа кошельков можно на странице https://security.webmoney.ru/asp/trustlistmerch.asp";
		}
		elseif ($error==525){
			return "Указанный вами WM-идентификатор найден, но в нем не включена опция оплаты по SMS, она включена в другом WM-идентификаторе с тем же телефоном. Либо используйте его, либо включите опцию в этом ВМИД на странице https://security.webmoney.ru/asp/trustlistmerch.asp";
		}
		elseif ($error==526){
			return "По введенным вами данным найден WM-идентификатор, в котором опция оплаты по SMS была выключена владельцем WM-идентификатора. Включить ее опять можно на странице https://security.webmoney.ru/asp/trustlistmerch.asp";
		}
		elseif ($error==527){
			return "По введенным вами данным найден WM-идентификатор, но в нем нет подходящего для оплаты кошелька (или кошелек не настроен), произведите настройку кошелька на сайте https://security.webmoney.ru/asp/trustlistmerch.asp";
		}
		elseif ($error==528 or $error==529 or $error==530){
			return "Сумма платежа превышает назначенный вами (или по умолчанию) дневной лимит для платежей такого типа, регулировка лимитов находится на странице https://security.webmoney.ru/asp/trustlistmerch.asp";
		}
		elseif ($error==531){
			return "Назначенный вами (или по умолчанию) кошелек для платежей по SMS не имеет достаточной суммы.";
		}
		elseif ($error==532){
			return "По введенным вами данным найден WM-идентификатор, но кошелек требуемого типа не настроен на оплату через SMS, обратитесь к странице https://security.webmoney.ru/asp/trustlistmerch.asp";
		}
		elseif ($error==533){
			return "Для найденного WM-идентификатора платежи по SMS невозможны, обратитесь, пожалуйста в тех. поддержку системы WebMoney Transfer";
		}
		elseif ($error==535){
			return "Слишком много SMS (или USSD) не закончившихся оплатой отправлено на Ваш телефон, пожалуйста, подождите и попробуйте позднее.";
		}
		elseif ($error==543){
			return "Несколько минут назад вы уже пытались платить на эту же сумму и с этим же номером платежа, попробуйте пожалуйста позднее.";
		}
		elseif ($error==550 or $error==555){
			return "Что-то идет не так, вы пытаетесь подтвердить платеж, который не зарегистрирован в системе как платеж с WebMoney.Check, обратитесь, пожалуйста, с этой проблемой в службу поддержки.";
		}
		elseif ($error==551 or $error==557){
			return "Вы отказались от оплаты данного платежа с помощью WebMoney.Check, чтобы все же заплатить надо начинать снова.";
		}
		elseif ($error==553){
			return "Данный платеж пока еще не оплачен. Вы оплачиваете его через WebMoney чек, при этом вы не указали код, высланный вам по SMS.";
		}
		elseif ($error==556){
			return "Либо данный платеж пока еще не оплачен (если вы оплачиваете счет по USSD или через какую-либо программу или сайт по управлению WM-кошельками), либо вы указали неверный код, высланный вам по SMS.";
		}
		elseif ($error==558){
			return "SMS не была отправлена по данному платежу.";
		}
		elseif ($error==561){
			return "Подтверждение платежа по USSD пока еще неполучено, возможно вы уже отказались от платежа, в противном случае дождитесь получения подтверждения.";
		}
		elseif ($error==571 or $error==572 or $error==573){
			return "Вы указали номер телефона по которому не зарегистрирован WebMoney.Check, либо на нем нет требуемой для оплаты суммы, если на данном телефоне есть регистрация в WebMoney, то на найденных WMID нет необходимой суммы требуемого типа WM-валюты.";
		}
		elseif ($error=='-22'){
			return "Код не может быть больше 8 цифр";
		}
		elseif ($error==534){
			return "Вы уже платили данным способом (SMS или USSD), но с кошелька другого типа, к сожалению пока мы не можем автоматически включить Вам возможность оплаты для этого типа кошелька, установите для нужного кошелька лимиты на оплату на странице https://security.webmoney.ru/asp/trustlistmerch.asp и попробуйте снова.";
		}
		else{
			return "Произошла ошибка, повторите попытку начав все заново.";
		}
	}
}
