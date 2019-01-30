<?php

namespace App\Widgets;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class AdminHomepanel extends AbstractWidget
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
		$funk=Route::currentRouteName();
		$this->config["pref"]="";
		$this->config["params"]=[];
		$kargs=$m=Route::current()->parameters();
		if(preg_match('/admin\./ui',$funk)){
			$this->config["pref"]="admin.";
			if(isset($kargs["id_user"])){
				$this->config["params"]["id_user"]=$kargs["id_user"];
			}
		}
		$profileName="простой человек";
	    $user_id=0;
		$manager=null;
		
		if($this->config["user"]->hasRole("admin")){
		$profileName="Администратор";
		}elseif($this->config["user"]->hasRole("super_manager")){
		$profileName="Главный менеджер";
		}elseif($this->config["user"]->hasRole("manager")){
		$profileName="Менеджер";
		}elseif($this->config["user"]->hasRole("advertiser")){
			
			$user_id=$this->config["user"]->id;
		    $profileName="Рекламодатель";
			$id_manager=$this->config["user"]->Profile->manager;	
             #var_dump($this->config["user"]->Profile->manager); echo "<hr>";
			if(!$id_manager){
				$id_manager=39;
			$sql="update user_profiles set manager=$id_manager where user_id=$user_id ";
			\DB::connection()->getPdo()->exec($sql);
			}
			//var_dump($id_manager);
			$manager=\App\User::find($id_manager);
			#var_dump($this->config["user"]); echo "<hr>";
			#var_dump($id_manager);
			#var_dump($manager->Profile->skype);
		}
		$superstat=null;
			if($user_id){
				$superstat["balance"]=0;
			$sql="select balance from user_profile
			where user_id=".$user_id."
			";
			$ud=\DB::connection("advertise")->select($sql);
			if($ud){
			$bal=$ud[0]->balance;	
			$superstat["balance"]=$bal;
			}
			
			$dayToday=date("Y-m-d");
			$dayYesturday=date("Y-m-d",(time()-3600*24));
			$dayMweek=date("Y-m-d",(time()-3600*24*7));
			$dayMonth=date("Y-m-d",(time()-3600*24*30));
			$superstat["today"]=0;
			$superstat["yesturday"]=0;
			$superstat["week"]=0;
			$superstat["month"]=0;
			$sql="
 select day,sum(summa) as summa
 from payment_history where user_id=".$user_id."
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
					$sql="
 select sum(summa) as summa
 from payment_history where user_id=".$user_id."
 and type='spisanie' 
 and day between '$dayMonth' and '$dayToday'
 ";
 $ddudm=\DB::connection("advertise")->select($sql);
 if($ddudm && $ddudm[0])
	 $superstat["month"]=$ddudm[0]->summa;
  return view('widgets.admin_homepanel', [
            'config' => $this->config,"profileName"=>$profileName,"balancer"=>$superstat,"manager"=>$manager,"id_user"=>$user_id
        ]);
		}else{
        return view('widgets.topadmin_homepanel', [
            'config' => $this->config
        ]);			
		}
		#var_dump($superstat); die();
#var_dump($this->config["user"]);

    }
}
