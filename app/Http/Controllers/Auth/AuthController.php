<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\ConfirmUser;
use Mail;
use App\UserProfile;
use App\RegistrationLog;
use Illuminate\Support\Str;
use Cookie;
#use Illuminate\Foundation\Auth\AuthenticatesUsers;

#use Closure;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
	public function create(Request $request){
		$referer=$request->input('aid');
		$links=$request->input('affiliate');
		$user=\App\UserProfile::where('refer_id', $referer)->first();
		if ($user){
			$aid = Cookie::queue('aid', $referer , 7200);
		}
		if ($links){
			$link = Cookie::queue('link', $links , 7200);
		}
		return redirect()->route('registration');
	}
	
	public function createRegister(){
		$aid=Cookie::get('aid');
		$link=Cookie::get('link');
		return view('auth.register')->withCookie($aid, $link);
	}
	
	public function repeat(){
		return view('auth.repeat');
	}
	
	public function login(){
		return view('auth.login');
	}
	
	public function logout(){
		\Auth::logout();
		return redirect('/login');
	}
	
    public function createSave(Request $request){

		if ($request->ip()=='46.246.38.54' || $request->ip()=='166.70.207.2' || $request->ip()=='104.244.77.49' || $request->ip()=='46.246.35.174' 
			|| $request->ip()=='195.228.45.176' || $request->ip()=='51.15.65.25' || $request->ip()=='171.25.193.77' || $request->ip()=='51.15.43.205' 
			|| $request->ip()=='87.118.92.43' || $request->ip()=='185.220.101.27' || $request->ip()=='104.244.76.13' || $request->ip()=='51.15.88.249' 
			|| $request->ip()=='171.25.193.20' || $request->ip()=='93.115.95.201' || $request->ip()=='192.42.116.17' || $request->ip()=='195.176.3.19' 
			|| $request->ip()=='195.176.3.24' || $request->ip()=='178.162.217.135' || $request->ip()=='178.20.55.18' || $request->ip()=='176.10.99.200' 
			|| $request->ip()=='185.220.101.32' || $request->ip()=='192.42.116.18' || $request->ip()=='185.220.101.30' || $request->ip()=='171.25.193.77' 
			|| $request->ip()=='31.181.93.77' || $request->ip()=='178.162.217.142' || $request->ip()=='176.126.252.12' || $request->ip()=='45.33.48.204' 
			|| $request->ip()=='185.220.101.13' || $request->ip()=='31.185.27.201' || $request->ip()=='185.220.101.13' || $request->ip()=='185.220.101.22' 
			|| $request->ip()=='185.100.87.207' || $request->ip()=='46.246.62.213'){
			return redirect('/login')->with('message_success','Спасибо за регистрацию, на указанный Email адрес выслано письмо с подтверждением.');
		}
		$usersearch=User::where('email','=',mb_strtolower($request->input('email')))->first(); //делаем выборку из базы по введенному email
		if(!empty($usersearch->email)){
			if($usersearch->status==0){
				return back()->with('message_war',"Такой email уже зарегестрирован, но не подтвержден. Проверьте почту или <a href='/register_repeat'>запросите</a> повторное подтверждение email.");
			}
			else{
				return back()->with('message_war',"Такой email уже зарегестрирован.");
			}
		}
		$validator=Validator::make($request->all(),[
				'firstname' => 'required|string|max:255',
				'lastname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users',
				'password' => 'required|string|min:6|confirmed',
			]);
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}

		$firstname=$request->firstname;
		$lastname=$request->lastname;
		$name=$firstname." ".$lastname;
		$email=mb_strtolower($request->email);
		$password=$request->password;
		
		$user=User::create([
			'name' => $name,
			'email' => $email,
			'password' => bcrypt($password),
		]);
		if($user){
			$user_profile=UserProfile::create([
                        'id' => $user->id,
			'user_id' => $user->id,
			'name' => $name,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'email' => $email
			]);
			RegistrationLog::create([
			'user_id' => $user->id,
			'name' => $name,
			'email' => $email,
			'ip' => $request->ip()
			]);
//		var_dump($user);
//		echo "<hr>";
//		var_dump($user_profile);
//                  die();

			$refer_rand=Str::random(13);
			$prov_ref=UserProfile::where('refer_id', $refer_rand)->first();
			if ($prov_ref){
				$user_profile->refer_id=Str::random(13);
			}
			else{
				$user_profile->refer_id=$refer_rand;
			}
			if ($request->input('referer')){
				$userRef=UserProfile::where('refer_id', $request->input('referer'))->first();
				if ($userRef){
					$user_profile->referer=$userRef->user_id;
				}
			}
			$user_profile->save();
			$email=$user->email;
			$token=str_random(32);
			$model=new ConfirmUser;
			$model->email=$email;
			$model->token=$token;
			$model->save();
			if ($request->type==1){
				$default=\App\Role::where('name','=','affiliate')->first();
			        $user->attachRole($default);
				Mail::send('email.register.register_affiliate',['token'=>$token],function($u) use ($user){
					$u->from('support@market-place.su', 'Market-place');
					$u->to($user->email);
					$u->subject('Подтверждение регистрации Вебмастера');
				});
			}
			if ($request->type==2){
				$default=\App\Role::where('name','=','advertiser')->first();
                  		$user->attachRole($default);
				Mail::send('email.register.register_advertiser',['token'=>$token],function($u) use ($user){
					$u->from('support@market-place.su', 'Market-place');
					$u->to($user->email);
					$u->subject('Подтверждение регистрации Рекламодателя');
				});
			}

			if ($request->input('link')){
				\DB::table('sponsored_links_regis')->insert([
					['user_id' => $user->id, 
					'affiliate' => $request->input('link'),
					'ip' => $request->ip(),
					'date' => date('Y-m-d'),
					'created_at' => date('Y-m-d H:i:s')
					],
				]);
			}
			if(!empty($user->id)){
				return redirect('/login?success')->with('message_success','Спасибо за регистрацию, на указанный Email адрес выслано письмо с подтверждением.');
				}
			else{
				return redirect('/register')->with('message_war','Ошибка при регистрации, пожалуйста повторите попытку');
			}
		}
	}
	
	public function loginYan(Request $request){
		
	}
	
	public function repeatPost(Request $request){
		$user=User::where('email','=',mb_strtolower($request->input('email')))->first();
		if(!empty($user->email)){
			if($user->status==0 ){
				$user->touch();
				$confirm=ConfirmUser::where('email','=',mb_strtolower($request->input('email')))->first();
				$confirm->touch();
				$role=\DB::table('role_user')->where('user_id', $user->id)->first();
				if ($role->role_id==1){
					Mail::send('email.register.register_affiliate',['token'=>$confirm->token],function($u) use ($user){
						$u->from('support@market-place.su', 'Market-place');
						$u->to($user->email);
						$u->subject('Подтверждение регистрации Вебмастера');
					});
				}
				if ($role->role_id==2){
					Mail::send('email.register.register_advertiser',['token'=>$confirm->token],function($u) use ($user){
						$u->from('support@market-place.su', 'Market-place');
						$u->to($user->email);
						$u->subject('Подтверждение регистрации Рекламодателя');
					});
				}
				return back()->with('message_success','Письмо для активации успешно выслано на указанный адрес.');
			}
			else{
				return back()->with('message_war','Такой email уже подтвержден.');
			}
		}
		else{
			return back()->with('message_war','Нет пользователя с таким email адресом.');
		}
	}
	
	public function loginPost(Request $request) {
		if ($request->ip()=='31.173.240.64'){
			return abort(403);
		}
		$email=mb_strtolower($request->input('email'));
		if ($email=='kulinap@yandex.ru' || $email=='vestivmire@yandex.ru' || $email=='avtoresot@yandex.ru' || $email=='webreceptu@yandex.ru' || 
		$email=='yarowoy.vad@yandex.ru' || $email=='domaschnij.sergey@yandex.ru' || $email=='ryltseva.olya@yandex.ru' || $email=='darja.fedorovna@yandex.ru' || 
		$email=='ivanovna.marij@yandex.ru' || $email=='fedorowaewa@yandex.ru' || $email=='vagnoe24@yandex.ru' || $email=='serega.smachnoi@yandex.ru' || 
		$email=='eos1000d@ukr.net' || $email=='odaynews@yandex.ru' || $email=='yarowoy.vad@yandex.ru'){
			$log = new \App\AuthBotGroup;
			$log->date=date('Y-m-d');
			$log->email=$request->input('email');
			$log->ip_adress=$request->ip();
			$log->user_agent=$request->header('user-agent');
			$log->save();
		}
		if ($email=='vestivmire@yandex.ru' || $email=='avtoresot@yandex.ru' || $email=='webreceptu@yandex.ru' || 
		$email=='yarowoy.vad@yandex.ru' || $email=='domaschnij.sergey@yandex.ru' || $email=='ryltseva.olya@yandex.ru' || $email=='darja.fedorovna@yandex.ru' || 
		$email=='ivanovna.marij@yandex.ru' || $email=='fedorowaewa@yandex.ru' || $email=='vagnoe24@yandex.ru' || $email=='serega.smachnoi@yandex.ru' || 
		$email=='eos1000d@ukr.net' || $email=='odaynews@yandex.ru' || $email=='yarowoy.vad@yandex.ru'){
			return back()->with('message_war','Ваш аккаунт привязан к Слободяник Татьяна, свяжитесь с вашим менеджером для уточнения подробностей.');
		}
		if ($email=='genus1345@mail.ru' or $email=='kolya.iuspov@mail.ru'){
			return back()->with('message_war','Ваш аккаунт привязан к Aramyan Jivan, свяжитесь с вашим менеджером для уточнения подробностей.');
		}
		if ($email=='almacotest@ya.ru' || $email=='almacotest@yandex.ru'){
			return abort(403);
		}
		if ($email=='nikolaipryadilshikov@mail.ru'){
			return back()->with('message_war','Ваш аккаунт привязан к Михаилу Дмитриеву, свяжитесь с вашим менеджером для уточнения подробностей.');
		}
		if (\Auth::attempt(['email' => $email, 'password' => $request->input('password'),'status'=>'1'])){
//		if (\Auth::attempt(['email' => $email, 'password' => $request->input('password')])){
			$log = new \App\AuthLog;
			$log->date=date('Y-m-d');
			$log->user_id=\Auth::user()->id;
			$log->ip_adress=$request->ip();
			$log->user_agent=$request->header('user-agent');
			$log->save();
                        return redirect()->intended() ;
                        #return redirect('/home'); 
		}
		else{
                        
			return back()->with('message_war','Не верная комбинация логин-пароль или Вы не активировали Вашу учетную запись. Проверьте Вашу почту.');
		}
	}
	
	public function confirm($token){
		$model=ConfirmUser::where('token','=',$token)->firstOrFail();
		$user=User::where('email','=',$model->email)->first();
		$user->status=1;
		$user->save();
		$log=RegistrationLog::where('email','=',$model->email)->first();
		$log->status=1;
		$log->save();
		$model->delete();
		return redirect('/login')->with('message_success','Ваш аккаунт успешно активирован.');
	}
	
}
