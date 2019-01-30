<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Route;
use Auth;
class UserInvoices extends AbstractWidget
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
    {   $perPage=10;
        
		$this->config["wparams"]=Route::current()->parameters;
        
		$this->config["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^([a-z]+\.)([a-z\-]+)/',$func,$m))
			$this->config["pref"]=$m[1];
         if(isset($this->config["wparams"]["id_user"])){
         $user=\App\User::findOrFail($this->config["wparams"]["id_user"]);
         }else{
         $user=Auth::user();
         }
		 $from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		$sql="select t1.id,t1,
		t1.number,
		t1.payd::date as payd, 
		t1.total, 
		t1.npp, 
		t1.payment_date,
		t1.datetime::date as day,
		
		case when t1.payd is not null then 'Оплачен'
		else 
		case when t1.type =2 then 'Авансовый'
	    else 'Неоплачен'
		end
		end as paymenede
		from user_invoices t1 where t1.user_id =".$user->id."
		and t1.deleted=0
		order by t1.id desc
		";
		$xata=\DB::connection()->select($sql);
			$found=count($xata);
			#$found=0;
        $page = $request->input('page', 1); 
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		//return '';
        //var_dump($user->id);
        return view('widgets.user_invoices', [
            'config' => $this->config,'collection'=>$data,'user'=>$user
        ]);
    }
}
