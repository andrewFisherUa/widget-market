<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
use domDocument;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use FFMpeg;

class TestController extends Controller
{
    public function index($id_user=0, Request $request){
		\Auth::user()->touch();
		$user=Auth::user();
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
        return view('admin.cabinet.home1', ['user'=>$user]);
	}
	
	public function Profile($id_user=0){
		$user=Auth::user();
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		return response()->json([
			'ok' => true,
			'view' => view('admin.cabinet.user_test', ['user'=>$user])->render()
		]);
	}
	public function Balance($id_user=0){
		$user=Auth::user();
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		return response()->json([
			'ok' => true,
			'view' => view('admin.cabinet.user_test2', ['user'=>$user])->render()
		]);
	}
	public function Notif($id_user=0){
		$user=Auth::user();
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		$user_notif=\App\AllNotification::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
		return response()->json([
			'ok' => true,
			'view' => view('admin.cabinet.user_test3', ['user'=>$user, 'user_notif'=>$user_notif])->render()
		]);
	}
	public function removeNotif($id){
		\Auth::user()->touch();
		$notifs=Auth::user()->unreadNotifications->where('type', '<>', 'App\Notifications\NewNews')->where('data', $id);
		foreach ($notifs as $notif){
			$notif->markAsRead();
		}
		return response()->json([
			'ok' => true
		]);
	}
	
	public function News($id_user=0){
		$user=Auth::user();
		if ($id_user){
			$user=User::findOrFail($id_user);
		}
		if (Auth::user()->hasRole('manager')){
			if (Auth::user()->id!=$user->Profile->manager and Auth::user()->id!=$user->id){
				return abort(403);
			}
		}
		if ($user->hasRole('affiliate')){
			$news_lim=\App\News::where('role', 1)->orderBy('created_at', 'desc')->take(20)->get();
		}
		else if($user->hasRole('advertiser')){
			$news_lim=\App\News::where('role', 2)->orderBy('created_at', 'desc')->take(20)->get();
		}
		else{
			$news_lim=\App\News::orderBy('created_at', 'desc')->take(20)->get();
		}
		return response()->json([
			'ok' => true,
			'view' => view('admin.cabinet.user_test4', ['user'=>$user, 'news_lim'=>$news_lim])->render()
		]);
	}
	
	public function addPayoutAuto(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$urgently=$request->input('urgently');

		$auto_pay=$request->input('auto_pay');

		$day=$request->input('day');
		$pay_option=$request->input('pay_option');
		
		if (\Auth::user()->id!=$user_id){
			return response()->json([
				'ok' => false,
			]);
		}
		$userProfile=\App\UserProfile::where('user_id', $user_id)->first();
		if ($userProfile){
			$userProfile->auto_payment=$auto_pay;
			$userProfile->save();
				if ($auto_pay){
				$auto=\App\Payments\UserPaymentAuto::firstOrNew(['user_id' => $user_id]);
				$auto->payment_id=$pay_option;
				$auto->day=$day;
				$auto->urgently=$urgently;
				$auto->save();
				$text="Авто заказ выплаты успешно включен.";
				}
				else{
					$auto=\App\Payments\UserPaymentAuto::where('user_id', $user_id)->delete();
					$text="Авто заказ выплаты успешно выключен.";
				}
		}
		
		return response()->json([
			'ok' => true,
			'message' => $text
		]);
	}
	
	public function message(Request $request){
		$text=$request->input('text');
		$status=1;
		return response()->json([
			'ok' => true,
			'view' => view('admin.cabinet.user_test5', ['status'=>$status, 'text'=>$text])->render()
		]);
	}
	
	public function obmenneg(Request $request){
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"from";
        $direct=$direct?$direct:"asc";
        $newdirect=($direct=="asc")?"desc":"asc";
		$direct=$newdirect;
		$alls=simplexml_load_file("https://obmenneg.ru/request-exportxml.xml?lang=ru");
		$xmls = array();
		$temp_filename = "/home/www/widget.market-place.su/public/obmenneg/info.zip";

$fp = fopen($temp_filename, "w");
fputs($fp, file_get_contents("http://www.bestchange.ru/bm/info.zip"));
fclose($fp);

$zip = new \ZipArchive;
if (!$zip->open($temp_filename)) exit("error");
$currencies = array();
foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
  $entry = explode(";", $value);
  $currencies[$entry[0]] = $entry[2];
}
ksort($currencies);
$exchangers = array();
foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
  $entry = explode(";", $value);
  $exchangers[$entry[0]] = $entry[1];
}
ksort($exchangers);
$rates = array();
foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
  $entry = explode(";", $value);
  if ((string)$entry[4]>0){
  $rates[$entry[0]][$entry[1]][$entry[2]] = array("rate"=> (string)$entry[3] / (string)$entry[4], "reserve"=>$entry[5]);
  }
  else{
  $rates[$entry[0]][$entry[1]][$entry[2]] = array("rate"=> $entry[3] / 1, "reserve"=>$entry[5]);
  }
}
$zip->close();
unlink($temp_filename);

$from_cy = 52;//WMR
$to_cy = 93;//WMZ

//echo(" урсы по направлению " . $currencies[$from_cy] . "->" . $currencies[$to_cy] . ":<br>");
uasort($rates[$from_cy][$to_cy], function ($a, $b) {
  if ($a["rate"] > $b["rate"]) return 1;
  if ($a["rate"] < $b["rate"]) return -1;
  return(0);
});
$first=reset($rates[$from_cy][$to_cy]);
//echo ($first["rate"] < 1 ? 1 : $first["rate"])."---".($first["rate"] < 1 ? 1 / $first["rate"] : 1);
//var_dump(key($rates[$from_cy][$to_cy]));
//echo "<hr>";
foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
//echo ($entry["rate"] < 1 ? 1 : $entry["rate"]) . "---" . ($entry["rate"] < 1 ? 1 / $entry["rate"] : 1)."\n";
//var_dump($key);
  //echo("<a href=\"https://www.bestchange.ru/info.php?id=" . $key . "\">" . $exchangers[$key] . "</a> - " . ($entry["rate"] < 1 ? 1 : $entry["rate"]) . " " . $currencies[$from_cy] . " на " . ($entry["rate"] < 1 ? 1 / $entry["rate"] : 1) . " " . $currencies[$to_cy] . " - резерв " . $entry["reserve"] . " " . $currencies[$to_cy] . "<br>");
	//echo "<hr>";
  }
		
		$nameBank=array(
			'Внутренний счет'=>'INTERNAL',
			'ВТБ 24 USD'=>'VTB24USD',
			'Perfect Money USD'=>'PMUSD', //Perfect Money USD
			'OKPay USD'=>'OKUSD', //OKPay USD
			'WebMoney RUB'=>'WMR', //WMR
			'Приват 24 UAH'=>'P24UAH', //Приват 24 UAH
			'Яндекс.Деньги RUB'=>'YAMRUB', //Яндекс.Деньги
			'Сбербанк RUB'=>'SBERRUB', //Сбербанк
			'Perfect Money EUR'=>'PMEUR', //Perfect Money EUR
			'Bitcoin (BTC)'=>'BTC', //Bitcoin (BTC)
			'QIWI RUB'=>'QWRUB', //QIWI RUB
			'Ethereum (ETH)'=>'ETH', //Ethereum (ETH)
			'Наличные RUB'=>'CASHRUB', //Наличные RUB
			'Альфа-Банк RUB'=>'ACRUB', //Альфа-Банк
			'Тинькофф RUB'=>'TCSBRUB', //Тинькофф
			'Exmo RUB'=>'EXMRUB', //Exmo RUB
			'Exmo USD'=>'EXMUSD', //Exmo USD
			'ВТБ 24 RUB'=>'VTB24RUB', //ВТБ24
			'Payeer USD'=>'PRUSD', //Payeer USD
			'WEX EUR'=>'WEXEUR', //WEX EUR
			'WEX USD'=>'WEXUSD', //WEX USD
			'WebMoney USD'=>'WMZ', //WMZ
			'WebMoney UAH'=>'WMU' //WMU
		);
		//этого нету у них INTERNAL, VTB24USD
		$codesBestChange=array(
			'40'=>'PMUSD', //Perfect Money USD
			'83'=>'OKUSD', //OKPay USD
			'2'=>'WMR', //WMR
			'56'=>'P24UAH', //Приват 24 UAH
			'6'=>'YAMRUB', //Яндекс.Деньги
			'42'=>'SBERRUB', //Сбербанк
			'41'=>'PMEUR', //Perfect Money EUR
			'93'=>'BTC', //Bitcoin (BTC)
			'63'=>'QWRUB', //QIWI RUB
			'139'=>'ETH', //Ethereum (ETH)
			'91'=>'CASHRUB', //Наличные RUB
			'52'=>'ACRUB', //Альфа-Банк
			'105'=>'TCSBRUB', //Тинькофф
			'130'=>'EXMRUB', //Exmo RUB
			'129'=>'EXMUSD', //Exmo USD
			'51'=>'VTB24RUB', //ВТБ24
			'108'=>'PRUSD', //Payeer USD
			'119'=>'WEXEUR', //WEX EUR
			'104'=>'WEXUSD', //WEX USD
			'1'=>'WMZ', //WMZ
			'20'=>'WMU' //WMU
		);
		//echo "<hr>";
		//var_dump($codesBestChange);
		foreach ($alls as $all){
			if ($all->in<1){
				$all->out=(string)$all->out/(string)$all->in;
				$all->in=1;
			}
			elseif ($all->in>1 and $all->in<2){
				$all->out=(string)$all->out/(string)$all->in;
				$all->in=1;
			}
			elseif ($all->in>=2){
				$all->in=1/(string)$all->out*(string)$all->in;
				$all->out=1;
			}
		}
		foreach ($alls as $all){
			$from_all = array_search($all->from, $codesBestChange);
			$to_all = array_search($all->to, $codesBestChange);
			if ($from_all and $to_all){
			foreach ($rates[$from_all][$to_all] as $key=>$entry) {
				if ($to_all==2 || $to_all==6 || $to_all==42 || $to_all==63 || $to_all==91 || $to_all==52 || $to_all==105 || $to_all==130 || $to_all==51){
					if ($entry["reserve"]<20000){
						unset($rates[$from_all][$to_all][$key]);
					}
				}
				if ($to_all==40 || $to_all==83 || $to_all==129 || $to_all==108 || $to_all==104 || $to_all==1){
					if ($entry["reserve"]<330){
						unset($rates[$from_all][$to_all][$key]);
					}
				}
				if ($to_all==56 || $to_all==20){
					if ($entry["reserve"]<8800){
						unset($rates[$from_all][$to_all][$key]);
					}
				}
				if ($to_all==41 || $to_all==119){
					if ($entry["reserve"]<285){
						unset($rates[$from_all][$to_all][$key]);
					}
				}
				if ($to_all==93 || $to_all==139){
					if ($entry["reserve"]<0.9){
						unset($rates[$from_all][$to_all][$key]);
					}
				}
			}

			$min=reset($rates[$from_all][$to_all]);
			$max=end($rates[$from_all][$to_all]);
				
				if ((($min["rate"] < 1 ? 1 : $min["rate"])==($max["rate"] < 1 ? 1 : $max["rate"])) and (($min["rate"] < 1 ? 1 / $min["rate"] : 1)>($max["rate"] < 1 ? 1 / $max["rate"] : 1))){
					$inBestChange=($min["rate"] < 1 ? 1 : $min["rate"]);
					$outBestChange=($min["rate"] < 1 ? 1 / $min["rate"] : 1);
					$reserve=$min["reserve"];
				}
				elseif ((($min["rate"] < 1 ? 1 : $min["rate"])==($max["rate"] < 1 ? 1 : $max["rate"])) and (($min["rate"] < 1 ? 1 / $min["rate"] : 1)<($max["rate"] < 1 ? 1 / $max["rate"] : 1))){
					$inBestChange=($max["rate"] < 1 ? 1 : $max["rate"]);
					$outBestChange=($max["rate"] < 1 ? 1 / $max["rate"] : 1);
					$reserve=$max["reserve"];
				}
				elseif ((($min["rate"] < 1 ? 1 : $min["rate"])>($max["rate"] < 1 ? 1 : $max["rate"])) and (($min["rate"] < 1 ? 1 / $min["rate"] : 1)==($max["rate"] < 1 ? 1 / $max["rate"] : 1))){
					$inBestChange=($max["rate"] < 1 ? 1 : $max["rate"]);
					$outBestChange=($max["rate"] < 1 ? 1 / $max["rate"] : 1);
					$reserve=$max["reserve"];
				}
				elseif ((($min["rate"] < 1 ? 1 : $min["rate"])<($max["rate"] < 1 ? 1 : $max["rate"])) and (($min["rate"] < 1 ? 1 / $min["rate"] : 1)==($max["rate"] < 1 ? 1 / $max["rate"] : 1))){
					$inBestChange=($min["rate"] < 1 ? 1 : $min["rate"]);
					$outBestChange=($min["rate"] < 1 ? 1 / $min["rate"] : 1);
					$reserve=$min["reserve"];
				}
			
			}
			else{
				$inBestChange=0;
				$outBestChange=0;
				$reserve=0;
			}
			
			$xmls[] = array(
				'from' => array_search($all->from, $nameBank),
				'to' => array_search($all->to, $nameBank),
				'in' => (string)$all->in,
				'out' => (string)$all->out,
				'inBestChange'=>$inBestChange,
				'outBestChange'=>$outBestChange,
				'reserve'=>$reserve
			);
		}
		/*foreach ($xmls as $xml){
			var_dump($xml);
			echo "<hr>";
		}*/
		if ($direct=="asc"){
			$direction=SORT_DESC;
		}
		else{
			$direction=SORT_ASC;
		}
		
		$this->array_sort_by_column ($xmls, $order, $direction);
		return view('obmenneg.index', ['xmls'=>$xmls, 'order'=>$order, 'direct'=>$direct]);
	}
	
	private function array_sort_by_column(&$array, $column, $direction) {
		$reference_array = array();

		foreach($array as $key => $row) {
			$reference_array[$key] = $row[$column];
		}

		array_multisort($reference_array, $direction, $array);
	}
	
	public function stata(){
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select * from pid_summa_full order by pid asc";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		return view('admin.cabinet.testara', ['data'=>$data]);
	}
	
	public function Teaser(Request $request){
		$offers=\DB::connection('advertise')->table('offers_teasers')->get();
		var_dump($offers);
	}
	
	public function videotest(){
		
	}
	
	public function apiTest(Request $request){
		$json=$request->input('json');
		return $json;
	}
}
