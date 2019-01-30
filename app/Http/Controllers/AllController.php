<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AllController extends Controller
{
    public function privacy(){
		return view('common.privacy');
	}
	
	public function useragree(){
		return view('common.useragree');
	}
}
