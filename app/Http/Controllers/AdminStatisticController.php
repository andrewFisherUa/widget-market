<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\RegistrationLog;
class AdminStatisticController extends Controller
{

    public function index(Request $request){
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=date('Y-m-d',time()-3600*24*30);
			$to=date('Y-m-d');
        }
		$sums=\DB::table('user_transactions')->select(\DB::raw('day, sum(video_commission) as video_commission, sum(product_commission) as product_commission, sum(teaser_commission) as teaser_commission, sum(referal_commission) as referal_commission, sum(manager_commission) as manager_commission'))
		->whereBetween('day', array($from, $to))->groupBy('day')->orderBy('day', 'desc')->paginate(30);
		$all_sum=\DB::table('user_transactions')->select(\DB::raw('sum(video_commission) as video_commission, sum(product_commission) as product_commission, sum(teaser_commission) as teaser_commission, sum(referal_commission) as referal_commission, sum(manager_commission) as manager_commission'))
		->whereBetween('day', array($from, $to))->first();
		return view('admin.global.all_stat', ['sums'=>$sums, 'all_sum'=>$all_sum, 'from'=>$from, 'to'=>$to]);
	}
	
	public function RegistrLog(){
		$logs=RegistrationLog::orderBy('created_at', 'desc')->paginate(20);
		return view('admin.global.register_log', ['logs'=>$logs]);
	}
}
