<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
class HelpController extends Controller
{

    public function reference(){
		if(\Auth::user()->hasRole('advertiser')){
			\Auth::user()->touch();
		     return view('helper.advertiser.reference');
			//abort(403);
		}
		\Auth::user()->touch();
		return view('helper.reference');
	}
	
	public function kBase(){
				if(\Auth::user()->hasRole('advertiser')){
			abort(403);
		}

		\Auth::user()->touch();
		return view('helper.k_base');
	}
	
	public function instructions(){
				if(\Auth::user()->hasRole('advertiser')){
			abort(403);
		}

		\Auth::user()->touch();
		return view('helper.instructions');
	}
	
}
