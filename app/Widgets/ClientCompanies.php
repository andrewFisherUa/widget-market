<?php

namespace App\Widgets;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;

class ClientCompanies extends AbstractWidget
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
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		$funk = Route::currentRouteName();
        //
		
        $dopSql="";
		#if(preg_match()){
			
		#}
			//admin.
		$pdostat=\DB::connection("pgstatistic")->getPdo(); 	
		$adv_status=$request->input("adv_status",-1);
		if($adv_status>=0){
			$dopSql.=" and a1.status=$adv_status ";
		}
		$name=$request->input("name");
		if($name){
		$dopSql.="and a1.name ~* '$name' ";
		}
		
	    $from=$request->input("from",date("Y-m-d"));	
		$to=$request->input("to",date("Y-m-d"));	
		
		$sql="
		select a1.* from advertise_statuses a1
		where 1=1 ";
		$sql.=" order by a1.id
		";
		$adv_statuses=[];
		$adv_=\DB::connection("advertise")->select($sql); 
		foreach($adv_ as $aa)
		$adv_statuses[$aa->id]=$aa;
		$id_user=0;
		if(isset($funkargs["id_user"])){
			$id_user=$funkargs["id_user"];
			$user=\App\User::findOrFail($id_user);
		}else{
			$user=Auth::user();
		}
		if(preg_match('/^admin\./',$funk)){
		$this->config["prefix_"]="admin.";	
		$this->config["conf_"]["user_id"]=$user->id;	
		}else{
		$this->config["prefix_"]="advertiser.";
		$this->config["conf_"]=[];
		}

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
		$funkargs["from"]=$from;
		$funkargs["to"]=$to;
		$funkargs["adv_status"]=$adv_status;
		$funkargs["name"]=$name;
		
		$funkargs["sort"]="name";
	    $sorts["name"]='<a href="'.route($funk,$funkargs).'" title ="Название">Название </a>';		
		$funkargs["sort"]="username";
	    $sorts["username"]='<a href="'.route($funk,$funkargs).'" title ="Пользователь">Пользователь </a>';	
		$funkargs["sort"]="status";
	    $sorts["status"]='<a href="'.route($funk,$funkargs).'" title ="Статус">Статус </a>';	
		$funkargs["sort"]="views";
	    $sorts["views"]='<a href="'.route($funk,$funkargs).'" title ="Показы">Показы </a>';		
		$funkargs["sort"]="clicks";
	    $sorts["clicks"]='<a href="'.route($funk,$funkargs).'" title ="Клики">Клики</a>';
		$funkargs["sort"]="expense";
	    $sorts["expense"]='<a href="'.route($funk,$funkargs).'" title ="Затраты">Затраты</a>';
		$funkargs["sort"]="ctr";
	    $sorts["ctr"]='<a href="'.route($funk,$funkargs).'" title ="ЦТР">ЦТР</a>';
		$funkargs["sort"]="balance";
	    $sorts["balance"]='<a href="'.route($funk,$funkargs).'" title ="Баланс">Баланс</a>';
		$funkargs["sort"]="offers_cnt";
	    $sorts["offers_cnt"]='<a href="'.route($funk,$funkargs).'" title ="Количество предложений на сегодня">Предложения</a>';

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
        case "views":
		    $funkargs["sort"]="views";
			$funkargs["order"]=$ride;
	        $sorts["views"]='<a href="'.route($funk,$funkargs).'"   title ="Показы">Показы '.$glip.'</a>';		
			$order="views"; 
		break;	
        case "clicks":
		    $funkargs["sort"]="clicks";
			$funkargs["order"]=$ride;
	        $sorts["clicks"]='<a href="'.route($funk,$funkargs).'"  title ="Клики">Клики '.$glip.'</a>';		
			$order="clicks"; 
		break;	
        case "expense":
		    $funkargs["sort"]="expense";
			$funkargs["order"]=$ride;
	        $sorts["expense"]='<a href="'.route($funk,$funkargs).'"  title ="Затраты">Затраты '.$glip.'</a>';		
			$order="expense"; 
		break;	

        case "ctr":
		    $funkargs["sort"]="ctr";
			$funkargs["order"]=$ride;
	        $sorts["ctr"]='<a href="'.route($funk,$funkargs).'"  title ="ЦТР">ЦТР '.$glip.'</a>';		
			$order="ctr"; 
		break;	
		case "balance":
		    $funkargs["sort"]="balance";
			$funkargs["order"]=$ride;
	        $sorts["balance"]='<a href="'.route($funk,$funkargs).'"  title ="Баланс">Баланс '.$glip.'</a>';		
			$order="balance"; 
		break;	
		case "offers_cnt":
		    $funkargs["sort"]="offers_cnt";
			$funkargs["order"]=$ride;
	        $sorts["offers_cnt"]='<a href="'.route($funk,$funkargs).'"  title ="Количество предложений на сегодня">Предложения '.$glip.'</a>';		
			$order="offers_cnt"; 
		break;	

		
		}
		
		
		
$sql="CREATE TEMP TABLE advrs
as 
select 
*
from 
advertise_history t1
where t1.day =NOW()::date
";
if(!isset($this->config["admin"])){
$sql.="
and t1.user_id=".$user->id."
";
}
$sql.="

";
\DB::connection("advertise")->getPdo()->exec($sql); 

$sql="
select 
a1.id
,t1.user_name as username
,t1.user_id 
,t1.expense
,t1.views
,t1.clicks
,t1.ctr
,t1.offer_limit_click
,a1.status
,t2.balance
,t2.offers_cnt
		,case when a1.status = 0 then ' Oжидает модерации'
		when a1.status = 1 then 'В работе'
		when a1.status = 6 then 'Недостаток средств'
		when a1.status = 4 then 'Приостановлен'
		when a1.status = 5 then 'Удалён'
		when a1.status = 2 then 'Отклонен'
		else 'Неготов'
		end as st_name
,a1.cpa_code
,a1.type
,a1.name
,a1.site_permissions
from 
(advertises a1
left join advrs t2 on a1.id=t2.id_company
)
left join
(
select 
t1.id_company
,t1.user_id 
,t1.user_name
,sum(expense) as expense
,sum(views) as views
,sum(clicks) as clicks
,sum(offer_limit_click) as offer_limit_click
,CASE WHEN (sum(t1.views)>0) then round(sum(t1.clicks)/sum(t1.views)::numeric,4)*100 else 0 end as ctr
from 
advertise_history t1
where t1.day 
between '$from' and '$to' ";
if(!isset($this->config["admin"])){
$sql.="
and t1.user_id=".$user->id."
";
}
$sql.="
group by t1.id_company
,t1.user_id,t1.user_name  
) t1
on t2.id_company=t1.id_company
where 1=1 $dopSql
";
if(!isset($this->config["admin"])){
$sql.="
and a1.user_id=".$user->id."
";
}
$sql.="
order by $order $dire
";
/*
$sql1="
select 
a1.id
,t1.user_name as username
,t1.user_id 
,t1.expense
,t1.views
,t1.clicks
,t1.ctr
,t1.offer_limit_click
,a1.status
,t2.balance
,t2.offers_cnt
		,case when a1.status = 0 then ' Oжидает модерации'
		when a1.status = 1 then 'В работе'
		when a1.status = 6 then 'Недостаток средств'
		when a1.status = 4 then 'Приостановлен'
		when a1.status = 5 then 'Удалён'
		when a1.status = 2 then 'Отклонен'
		else 'Неготов'
		end as st_name
,a1.cpa_code
,a1.type
,a1.name
,a1.site_permissions
from (
select 
t1.id_company
,t1.user_id 
,t1.user_name
,sum(expense) as expense
,sum(views) as views
,sum(clicks) as clicks
,sum(offer_limit_click) as offer_limit_click
,CASE WHEN (sum(t1.views)>0) then round(sum(t1.clicks)/sum(t1.views)::numeric,4)*100 else 0 end as ctr
from 
advertise_history t1
where t1.day 
between '$from' and '$to' ";
if(!isset($this->config["admin"])){
$sql1.="
and t1.user_id=".$user->id."
";
}
$sql1.="
group by t1.id_company
,t1.user_id,t1.user_name  
) t1
inner join (advrs t2
inner join advertises a1 on a1.id=t2.id_company
) on t2.id_company=t1.id_company
where 1=1 $dopSql
order by $order $dire
";
*/
		$vza=\DB::connection("advertise")->select($sql);
		#var_dump($sql);
		$perPage=10;
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $da = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

#$cpd_=\DB::connection("advertise")->select($sql); 
foreach($da as $vp_){
	#var_dump($vp_); echo "<hr>";
}
#echo $sql;


        return view('widgets.client_companies', [
            'config' => $this->config
			,'adv_statuses'=>$adv_statuses
			,"adv_status"=>$adv_status
			,"from"=>$from
			,"to"=>$to
			,"da"=>$da
			,"sorts"=>$sorts
        ]);
    }
}
