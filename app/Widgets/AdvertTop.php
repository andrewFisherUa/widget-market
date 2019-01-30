<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class AdvertTop extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run(Request $request)
    {
        //
		$user=Auth::user();
		if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			
		}else{
			return '';
		}
        $m=Route::current()->parameters();
		$shop_id=0;
		if(isset($m["shop_id"]))
        $shop_id=$m["shop_id"];
	    if(isset($m["id_user"]))
			$id_user=$m["id_user"];
			else
		   $id_user=0;
	    $links=[];
			$links[]=[
			"url"=>route("advertiser.add_admin"),
			"title"=>'Все прямые рекламодатели'
			];
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
			$links[]=[
			"url"=>route("admin.home",["id_user"=>$company->user_id]),
			"title"=>'Все магазины от '.$owner->name.''
			];

		
		}elseif($id_user){
			$owner= \App\User::findOrFail($id_user);
			$links[]=[
			"url"=>route("admin.home",["id_user"=>$id_user]),
			"title"=>'Все магазины от  '.$owner->name.''
			];
			
		}
		
        return view('widgets.advert_top', [
            'config' => $this->config,"links"=>$links,"id_user"=>$id_user
        ]);
    }
}
