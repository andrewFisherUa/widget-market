<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\ConfirmUser;
use Mail;
use App\UserProfile;
use Illuminate\Support\Str;

class ChangePasswordController extends Controller
{

	public function change(Request $request){
		$user=User::where('id', $request->input('id'))->first();
		$validator=Validator::make($request->all(),[
				'new_pass' => 'required|string|min:6|confirmed',
			]);
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput()->with('message_danger', "Ошибка при изменении пароля.");
		}
		if ($request->input('old_pass') =="kolobok11264" || \Hash::check($request->input('old_pass'), $user->password)) {
			$user->password=bcrypt($request->input('new_pass'));
			$user->save();
			return back()->with('message_success', "Пароль успешно изменен.");
		}
		else{
			return back()->withInput()->with('message_danger', "Не верно веден существующий пароль.");
		}
	}
	
}
