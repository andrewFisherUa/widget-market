<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


class ManagerController extends Controller
{
    public function index(){
		\Auth::user()->touch();
		$managers=\App\User::whereHas('roles', function ($query) {
			$query->whereIn('id', ['3','4','5']);
			})->orderBy('name', 'asc')->get();
		$commissions=\DB::table('сommission_groups')->where('type', '2')->get();
		return view('admin.managers.index', ['managers'=>$managers, 'commissions'=>$commissions]);
	}
	
	public function setCommission(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$commissiongroupid=$request->input('commissiongroupid');
		$manager=\App\ManagerCommission::firstOrNew(['user_id'=>$user_id]);
		$manager->commissiongroupid=$commissiongroupid;
		$manager->save();
		return back();
	}
	
	public function history($id){
		if (!\Auth::user()->hasRole('admin') and \Auth::user()->id!=$id){
			return abort(403);
		}
		$manager=\App\UserProfile::where('user_id', $id)->first();
		$commissions=\App\Transactions\ManagerCommissionTransacion::where('user_id', $id)->orderBy('day', 'desc')->paginate(20);
		return view('admin.managers.detail', ['commissions'=>$commissions, 'manager'=>$manager]);
	}
	
	public function historyPayout($id){
		if (!\Auth::user()->hasRole('admin') and \Auth::user()->id!=$id){
			return abort(403);
		}
		$manager=\App\UserProfile::where('user_id', $id)->first();
		$payouts=\App\Payments\UserPayout::where('user_id', $manager->id)->orderBy('created_at', 'desc')->paginate(20);
		return view('admin.managers.detail_payout', ['payouts'=>$payouts, 'manager'=>$manager]);
	}
	
	public function clients($id){
		if (!\Auth::user()->hasRole('admin') and \Auth::user()->id!=$id){
			return abort(403);
		}
		$manager=\App\UserProfile::where('user_id', $id)->first();
		$users=\App\UserProfile::where('manager', $manager->id)->orderBy('created_at', 'desc')->get();
		return view('admin.managers.clients', ['users'=>$users, 'manager'=>$manager]);
	}
	
}
