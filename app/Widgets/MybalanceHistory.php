<?php

namespace App\Widgets;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class MybalanceHistory extends AbstractWidget
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
       $m=Route::current()->parameters();
        //var_dump($m);
		$perPage=50;
		$id_user=0;
		$shop_id=0;
		$conback="";
		$user=Auth::user();
		$myAdmin=0;
		$funk='advertiser.balance_history';
		$funkargs=["id"=>0];
		if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$myAdmin=1;
		}else{
	    }
		if(isset($m["shop_id"])){
			$shop_id=$funkargs["id"]=$m["shop_id"];
			
		}
		if(isset($m["id_user"]) && $m["id_user"]){
		 // 
 		$funk='admin.balance_history';
			$id_user=$m["id_user"];
			$funkargs["id_user"]=$id_user;
			$sql="select id from advertises where user_id=".$id_user." ";
			$shops=\DB::connection("advertise")->select($sql);
			if($shops){
				$v=[];
				foreach($shops as $sh){
					$v[]=$sh->id;
				}
			$conback =' and t1.shop_id in('.implode(",",$v).')';	
			}else{
			$conback =' and t1.shop_id in(-1)';	
			}
			
			
		}else{  
		 if(isset($m["shop_id"]) && $m["shop_id"]){
         $shop_id=$m["shop_id"];	
		 if($m["shop_id"]){
			# $funkargs["id"]=0;
         $conback =' and t1.shop_id in('.$shop_id.')';			 
		 }
		 if($shop_id){
		 $sql="select user_id from advertises where id=".$shop_id." ";
		 $shp=\DB::connection("advertise")->select($sql);
		 if($shp)
		 $id_user=$shp[0]->user_id;
		 
			}
		 
		}else{
			//var_dump(111); die();
			
		if($myAdmin){

			
		}else{
			$id_user=$user->id;
			$sql="select id from advertises where user_id=".$user->id." ";
			$shops=\DB::connection("advertise")->select($sql);
			if($shops){
				$v=[];
				foreach($shops as $sh){
					$v[]=$sh->id;
				}
			$conback =' and t1.shop_id in('.implode(",",$v).')';	
			}else{
			$conback =' and t1.shop_id in(-1)';	
			}
		}	
			
		}
		
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			  $to = date("Y-m-d");
			  $from = date("Y-m-d",(time()-(3600*24*7)));
		}
	    $dire=$request->input("order");
		$sort=$request->input("sort");	
		if(!$dire)
	    $dire="desc";	
        if(!$sort)
	    $sort="day";	
	 if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	}else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	}	
	$funk=Route::currentRouteName();
    $funkargs["from"]=$from;
	$funkargs["to"]=$to;
	$funkargs["sort"]="day";
	$sorts["day"]='<a href="'.route($funk,$funkargs).'">Дата </a>';			
	$funkargs["sort"]="action";
	$sorts["action"]='<a href="'.route($funk,$funkargs).'">Действие </a>';			
	$funkargs["sort"]="summa";
	$sorts["summa"]='<a href="'.route($funk,$funkargs).'">Сумма </a>';		
    switch($sort){
		case "day":
		    $funkargs["sort"]="day";
			$funkargs["order"]=$ride;
	        $sorts["day"]='<a href="'.route($funk,$funkargs).'">Дата '.$glip.'</a>';		
			$order="day"; 
		break;	
		case "action":
		    $funkargs["sort"]="action";
			$funkargs["order"]=$ride;
	        $sorts["action"]='<a href="'.route($funk,$funkargs).'">Действие '.$glip.'</a>';		
			$order="action"; 
		break;
		case "summa":
		    $funkargs["sort"]="summa";
			$funkargs["order"]=$ride;
	        $sorts["summa"]='<a href="'.route($funk,$funkargs).'">Сумма '.$glip.'</a>';		
			$order="summa"; 
		break;
	}
$found=0;
		$data=[];
		if($id_user){
			$sql="
			select day,
			case when type ='popolnenie' then 'пополнение из личного кабинета' 
			when type='webmoney' then 'Зачисление Webmoney' 
			when type='spisanie' then 'Списание за переходы в магазин' 
			
			else type end
			as action
			,type
			,max(datetime) as datetime ,sum(summa) as summa
            from payment_history where user_id=$id_user
			and day between '$from' and '$to'
            group by
            day,type
			order by $order $dire ,datetime desc
			";
			
			$xata=\DB::connection("advertise")->select($sql);
			$found=count($xata);
			#$found=0;
        $page = $request->input('page', 1); 
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		}
		#var_dump($data); die();
        return view('widgets.mybalance_history', [
            'config' => $this->config,'collection'=>$data,"found"=>$found,"sorts"=>$sorts
        ]);
    }
}
