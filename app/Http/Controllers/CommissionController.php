<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
class CommissionController extends Controller
{

    public function addCommission(){
		\Auth::user()->touch();
		return view('admin.commission_group.add');
	}
	
}
