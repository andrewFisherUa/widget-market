<?php

namespace App\Widgets;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class UserCompanies extends AbstractWidget
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
		//die();
  	    $funkargs=$this->config["wparams"]=Route::current()->parameters;
		$funk = Route::currentRouteName();
		
			
      # var_dump($funk); die();
		$some_soobr=0;
		
		if (isset($this->config["admin"])){
			$some_soobr=1;
		}
		$dopSql="";
		$name=$request->input("name");
		$adv_status=$request->input("adv_status");

			
		if($adv_status===null) {
			#if($some_soobr)
			#$adv_status=-1;
		    #else
			$adv_status=-1;
		}
			#var_dump($adv_status); die();
		if($adv_status!=-1)
		{
			$dopSql.=" and a1.status=$adv_status ";
		}
		if(!$some_soobr){
			$dopSql.=" and (a1.user_id =".$this->config["user"]->id.") ";
		}
		if($name){
			$dopSql.=" and (a1.name ~* '".$name."' or a1.url ~* '".$name."' or hupp.name ~* '".$name."' ) ";
		}
		if(Auth::user()->hasRole(['admin','super_manager','manager'])){
			
		}else{
			$dopSql.=" and a1.status <>5 ";
			
		}
		$sql="
		select a1.* from advertise_statuses a1
		where 1=1 ";
		$sql.=" order by a1.id
		";
		$adv_statuses=[];
		$adv_=\DB::connection("advertise")->select($sql);
		foreach($adv_ as $aa)
		$adv_statuses[$aa->id]=$aa;
		
		 $dire=$request->input("order");
		$sort=$request->input("sort");	
		if(!$dire)
	    $dire="asc";	
        if(!$sort)
	    $sort="status";	
	    if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	    }else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	    }
		
		
		
		$funkargs["sort"]="name";
	    $sorts["name"]='<a href="'.route($funk,$funkargs).'" title ="Название">Название </a>';		
		$funkargs["sort"]="username";
	    $sorts["username"]='<a href="'.route($funk,$funkargs).'" title ="Пользователь">Пользователь </a>';	
		$funkargs["sort"]="status";
	    $sorts["status"]='<a href="'.route($funk,$funkargs).'" title ="Статус">Статус </a>';	
		$funkargs["sort"]="balance";
	    $sorts["balance"]='<a href="'.route($funk,$funkargs).'" title ="Баланс">Баланс </a>';		
		$funkargs["sort"]="day_clicks";
	    $sorts["day_clicks"]='<a href="'.route($funk,$funkargs).'" title ="Клики сегодня">Клики сегодня</a>';		

		
		switch($sort){
		case "name":
		    $funkargs["sort"]="name";
			$funkargs["order"]=$ride;
	        $sorts["name"]='<a href="'.route($funk,$funkargs).'" title ="Название">Название '.$glip.'</a>';		
			$order="name"; 
		break;
		case "username":
		    $funkargs["sort"]="username";
			$funkargs["order"]=$ride;
	        $sorts["username"]='<a href="'.route($funk,$funkargs).'" title ="Пользователь">Пользователь '.$glip.'</a>';		
			$order="username"; 
		break;
		case "status":
		    $funkargs["sort"]="status";
			$funkargs["order"]=$ride;
	        $sorts["status"]='<a href="'.route($funk,$funkargs).'" title ="Статус">Статус '.$glip.'</a>';		
			$order="st_name"; 
		break;		
        case "balance":
		    $funkargs["sort"]="balance";
			$funkargs["order"]=$ride;
	        $sorts["balance"]='<a href="'.route($funk,$funkargs).'"  title ="Баланс">Баланс '.$glip.'</a>';		
			$order="balance"; 
		break;	
        case "day_clicks":
		    $funkargs["sort"]="day_clicks";
			$funkargs["order"]=$ride;
	        $sorts["day_clicks"]='<a href="'.route($funk,$funkargs).'"  title ="Клики сегодня">Клики сегодня '.$glip.'</a>';		
			$order="day_clicks"; 
		break;	

		
		}
		
		$sql="select 
		a1.id
		,a1.user_id
		,a1.name
		,a1.type
		,a1.status
		,a1.cpa_code
		,case when a1.status = 0 then ' Oжидает модерации'
		when a1.status = 1 then 'В работе'
		when a1.status = 6 then 'Недостаток средств'
		when a1.status = 4 then 'Приостановлен'
		when a1.status = 5 then 'Удалён'
		when a1.status = 2 then 'Отклонен'
		else 'Неготов'
		end as st_name
		,a1.limit_clicks
		,a1.day_clicks
		,a1.site_permissions
		,coalesce(hupp.balance,0) as balance
		,coalesce(hupp.name,'~~') as username
		from advertises a1
		left join user_profile hupp
		on hupp.user_id=a1.user_id
		where 1=1 ";
        $sql.=$dopSql;
		$sql.=" order by $order $dire
		";
		
		$vza=\DB::connection("advertise")->select($sql);
		#var_dump($sql);
		$perPage=10;
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $da = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		
		$today = Carbon::now()->format('Y-m-d');
		#$today = Carbon::yesterday()->format('Y-m-d');
		if($some_soobr){
		 $companies=\App\Advertise::where("status","<",4)->orderBy("created_at","desc")->get();
		
		}else{
	     $companies=\App\Advertise::where("status","<",4)->where("user_id","=",$this->config["user"]->id)->orderBy("created_at","desc")->get();
		 
		 
		 
        }
		
		
		$stats=[];
		$sql="select shop_id
		,sum(clicks) as clicks
		,sum(views) as views
		,sum(summa) as summa
		from myadvert_summa_clicks where day = '$today' group by shop_id";
		$data=\DB::connection("pgstatistic")->select($sql);
		foreach($data as $d){
			$stats[$d->shop_id]=$d;
			#print_r($d); echo "<hr>";
		}
        return view('widgets.user_companies', [
            'config' => $this->config,"companies"=>$companies,'stats'=>$stats
			,"some_soobr"=>$some_soobr,'adv_statuses'=>$adv_statuses,"adv_status"=>$adv_status
			,"da"=>$da,"sorts"=>$sorts
        ]);
    }
}
