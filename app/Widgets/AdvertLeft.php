<?php

namespace App\Widgets;
use Auth;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;


class AdvertLeft extends AbstractWidget
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
		
		
		
		if(!isset($this->config["key"])){
			$this->config["key"]=1;
		
		}
		
		
		$this->config["pref"]="";
		$func=Route::currentRouteName();
        if(preg_match('/^admin\./',$func)){
			$conf["pref"]="admin.";
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			  $to = date("Y-m-d");
			  $from = date("Y-m-d",(time()-(3600*24*7)));
		}
		//$this->config["wparams"]
		$m=Route::current()->parameters();
		$this->config["wparams"]=$m;
		//var_dump($m);
		$invsLink=route('advertiser.invoices_history');
		if(isset($m["id_user"])){
			$id_user=$m["id_user"];
			
		}else{
			
		   $id_user=0;
		}
		//print_r($m);
		
		if(isset($m["mod"])){
			$mod=$m["mod"];
		}else{
			$mod="data";
		}
		$dptitle="";
		$shop_id=0;
		if(isset($m["shop_id"]))
        $shop_id=$m["shop_id"];
        #$user=Auth::user();
		$superstat=[];
		
		if($id_user){
			$dptitle="";
			$sql="select balance from user_profile
			where user_id=$id_user
			";
			$ud=\DB::connection("advertise")->select($sql);
			if($ud){
			$superstat["user_id"]=$id_user;	
			$bal=$ud[0]->balance;	
			$superstat["balance"]=$bal;
			if($bal<=0){
				#$dptitle='<span  style="color:#660000;"> '.$bal.' р </span>';
			}else{
				#$dptitle='<span  style="color:#006600;">  +'.$bal.' р </span>';
			}
			}
		}elseif($shop_id){
		
			$sql="select balance,user_id from user_profile
			where user_id in (select user_id from advertises where id=$shop_id limit 1)
			";
			$ud=\DB::connection("advertise")->select($sql);
			if($ud){
			$bal=$ud[0]->balance;	
			$superstat["balance"]=$bal;
			$superstat["user_id"]=$ud[0]->user_id;
			if($bal<=0){
				#$dptitle='<span  style="color:#660000;"> '.$bal.' р </span>';
			}else{
				#$dptitle='<span  style="color:#006600;">  +'.$bal.' р </span>';
			}
			}
	        }else{
			$user=Auth::user();
		
		if($user->hasRole("advertiser")){
				#var_dump($user);
			$sql="select balance from user_profile
			where user_id=".$user->id."
			";
			$ud=\DB::connection("advertise")->select($sql);
			if($ud){
			$bal=$ud[0]->balance;	
			$superstat["balance"]=$bal;
			$superstat["user_id"]=$user->id;
			if($bal<=0){
				#$dptitle='<span style="color:#660000;"> '.$bal.' р </span>';
			}else{
				#$dptitle='<span style="color:#006600;">  +'.$bal.' р </span>';
			}
			}
			
		}else{

		}
		}
		if(Auth::user()->hasRole(['advertiser'])){}else{
			if(isset($superstat["user_id"]))
			$invsLink=route('admin.invoices_history',['id_user'=>$superstat["user_id"]]);
		    else
				$invsLink="";

		}
		if(isset($superstat["user_id"])){
			$dayToday=date("Y-m-d");
			$dayYesturday=date("Y-m-d",(time()-3600*24));
			$dayMweek=date("Y-m-d",(time()-3600*24*7));
			$superstat["today"]=0;
			$superstat["yesturday"]=0;
			$superstat["week"]=0;
			$superstat["params"]=['user_id'=>$superstat["user_id"],'from'=>$dayMweek,'to'=>$dayToday,"mod"=>"data"
			,"shop_id"=>$shop_id];
			$sql="
 select day,sum(summa) as summa
 from payment_history where user_id=".$superstat["user_id"]."
 and type='spisanie' 
 and day between '$dayMweek' and '$dayToday'
 group by day order by day desc";
 $ddud=\DB::connection("advertise")->select($sql);
        foreach($ddud as $d11){
			if($d11->day==$dayToday){
				$superstat["today"]=$d11->summa;
			}
			if($d11->day==$dayYesturday){
				$superstat["yesturday"]=$d11->summa;
			}
			$superstat["week"]+=$d11->summa;
	        #var_dump($d11); echo "<hr>";
        }
		#    var_dump([$superstat["user_id"],$dayMweek]);
		}
		switch($this->config["key"]){
		case 3:
		 return view('widgets.leftpart.statistic', [
            'config' => $this->config,"id_user"=>$id_user,"mod"=>$mod,"shop_id"=>$shop_id,"from"=>$from,"to"=>$to
			,'dptitle'=>$dptitle,"superstat"=>$superstat,"invsLink"=>$invsLink
        ]);
        break;		
		   case 4: 
		    return view('widgets.leftpart.requisite', [
            'config' => $this->config,"id_user"=>$id_user,"mod"=>$mod,"shop_id"=>$shop_id,"from"=>$from,"to"=>$to
			,'dptitle'=>$dptitle,"superstat"=>$superstat,"invsLink"=>$invsLink
        ]);
		   break;
		   default:
		  return view('widgets.advert_left', [
            'config' => $this->config,"id_user"=>$id_user,"mod"=>$mod,"shop_id"=>$shop_id,"from"=>$from,"to"=>$to
			,'dptitle'=>$dptitle,"superstat"=>$superstat,"invsLink"=>$invsLink
        ]);
		   break;
		   
		}
		
        return view('widgets.advert_left', [
            'config' => $this->config,"id_user"=>$id_user,"mod"=>$mod,"shop_id"=>$shop_id,"from"=>$from,"to"=>$to
			,'dptitle'=>$dptitle,"superstat"=>$superstat,"invsLink"=>$invsLink
        ]);
    }
}
