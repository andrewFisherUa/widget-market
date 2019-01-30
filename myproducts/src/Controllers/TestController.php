<?php namespace Mplacegit\Myproducts\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Route;
class TestController extends Controller{
    public function __construct()
    {
    	$this->middleware(['role:admin|manager|super_manager|advertiser']);

    }
	public function index(Request $request){
		/*
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		$sql="select 
		      t.id_company,
              t.ind,
              t.title,
              t.src,
              t.img,
			  t.descript,
              t.status
		from teasers_offers_new t
				where t.id_company=$id
				order by t.title
		";
		$collection=\DB::connection("advertise")->select($sql);
		$params=['collection'=>$collection];
		return view('mp-teaser::offers',$params);
        */
    }		
}	