<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SponsoredLinksController extends Controller
{
    public function index(){
		\Auth::user()->touch();
		$links=\App\SponsoredLink::orderBy('created_at', 'desc')->get();
		$visits=\DB::table('sponsored_links_visit')->select(\DB::raw('affiliate, count(affiliate) as cnt, count(distinct ip) as uniq'))->groupBy('affiliate')->get();
		$regis=\DB::table('sponsored_links_regis')->select(\DB::raw('affiliate, count(user_id) as cnt'))->groupBy('affiliate')->get();
		return view('admin.sponsored_links.index', ['links'=>$links, 'visits'=>$visits, 'regis'=>$regis]);
	}
	public function add(Request $request){
		\Auth::user()->touch();
		$rand=Str::random(12);
		$link = new \App\SponsoredLink;
		$link->title=$request->input('title');
		$link->src="https://partner.market-place.su/?utm_source=".$rand;
		$link->affiliate=$rand;
		$link->save();
		return back()->with('message_success', "Ссылка успешно создана $link->src .");
	}
}
