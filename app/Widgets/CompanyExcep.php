<?php

namespace App\Widgets;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class CompanyExcep extends AbstractWidget
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
		$id_user=0;
		$shop_id=0;
		$conback="";
		$funk='advertiser.company.exceptions';
		#$detailfunc="advertiser.statistic_detail";	
		$funkargs=["id"=>0];
		$detail_args=["id"=>0];
		$user=Auth::user();
		$myAdmin=0;
		if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$myAdmin=1;
		}else{
	    }
		
		if(isset($m["id"])){
			$funkargs["id"]=$m["id"];
		}else{
			#abort(404);
		}
		
		#if(isset($m["shop_id"])){
		#	$funkargs["id"]=$m["shop_id"];
	    #	$detail_args["id"]=$m["shop_id"];
		#}
		
		if(isset($m["id_user"]) && $m["id_user"]){
			var_dump(112);die();	
		$funk='admin.statistic';
		
		}else{  
	
		if(isset($m["id"]) && $m["id"]){
         $shop_id=$m["id"];	
		 if($m["id"]){
			# $funkargs["id"]=0;
         $conback =' and t1.shop_id in('.$shop_id.')';			 
		 }
		}else{
			var_dump(114);die();	
	    }
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			  $to = date("Y-m-d");
			  $from = date("Y-m-d",(time()-(3600*24*7)));
		}
		$perPage = 10;
		
		$sqlDop="";
		$dire=$request->input("order");
		$sort=$request->input("sort");
		if(!$sort)
	    $sort="domain";
	    if(!$dire)
		$dire="asc";
	if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	}else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	}
	
	        if($myAdmin){
			$gcolonca="t.pad,t.domain";
			$colonca="t1.pad,t1.domain||' ('||t1.pad::varchar||') ' as domain";
			$group="t1.pad,t1.domain";
			$ggroup="t.pad,t.domain";
			}else{
				$gcolonca="t.pad,t.domain";
				$colonca="t1.pad,t1.pad::varchar as domain";
			    $group="t1.pad,t1.pad::varchar";
				$ggroup="t.pad,t.domain";
			}
	
	    $funkargs["from"]=$from;
		$funkargs["to"]=$to;
		$sorts["domain"]='<a href="'.route($funk,$funkargs).'">Домены</a>';
		$funkargs["sort"]="except";
		$sorts["except"]='<a href="'.route($funk,$funkargs).'">Исключить</a>';	
		$funkargs["sort"]="company";
		$funkargs["sort"]="clicks";
		$sorts["clicks"]='<a href="'.route($funk,$funkargs).'">Клики</a>';		
		$funkargs["sort"]="views";
		$sorts["views"]='<a href="'.route($funk,$funkargs).'">Показы</a>';		
		$funkargs["sort"]="summa";
		$sorts["summa"]='<a href="'.route($funk,$funkargs).'">Сумма</a>';		
		$funkargs["sort"]="cpc";
		$sorts["cpc"]='<a href="'.route($funk,$funkargs).'">CPC</a>';		
		$funkargs["sort"]="ctr";
		$sorts["ctr"]='<a href="'.route($funk,$funkargs).'">CTR</a>';		
		$funkargs["sort"]="inv";
		$sorts["inv"]='<a href="'.route($funk,$funkargs).'">Заказы</a>';	
		
        switch($sort){
		case "domain":
		    $funkargs["sort"]="domain";
			$funkargs["order"]=$ride;
	        $sorts["domain"]='<a href="'.route($funk,$funkargs).'">Домены '.$glip.'</a>';		
			if($myAdmin){
			$order="domain"; 
			}else{
			$order="domain"; 	
			}
		break;
		case "except":
		    $funkargs["sort"]="except";
			$funkargs["order"]=$ride;
	        $sorts["except"]='<a href="'.route($funk,$funkargs).'">Исключить '.$glip.'</a>';		
			$order="exc1"; 
		break;	
		case "clicks":
		    $funkargs["sort"]="clicks";
			$funkargs["order"]=$ride;
	        $sorts["clicks"]='<a href="'.route($funk,$funkargs).'">Клики '.$glip.'</a>';		
			$order="clicks"; 
		break;
		case "inv":
		    $funkargs["sort"]="inv";
			$funkargs["order"]=$ride;
	        $sorts["inv"]='<a href="'.route($funk,$funkargs).'">Заказы '.$glip.'</a>';		
			$order="inv"; 
		break;			
		case "views":
		    $funkargs["sort"]="views";
			$funkargs["order"]=$ride;
	        $sorts["views"]='<a href="'.route($funk,$funkargs).'">Показы '.$glip.'</a>';		
			$order="views"; 
		break;		
		case "cpc":
		    $funkargs["sort"]="cpc";
			$funkargs["order"]=$ride;
	        $sorts["cpc"]='<a href="'.route($funk,$funkargs).'">CPC '.$glip.'</a>';		
			$order="cpc"; 
		break;	
		case "ctr":
		    $funkargs["sort"]="ctr";
			$funkargs["order"]=$ride;
	        $sorts["ctr"]='<a href="'.route($funk,$funkargs).'">CTR '.$glip.'</a>';		
			$order="ctr"; 
		break;	
		case "summa":
		    $funkargs["sort"]="summa";
			$funkargs["order"]=$ride;
	        $sorts["summa"]='<a href="'.route($funk,$funkargs).'">Сумма '.$glip.'</a>';		
			$order="summa"; 
		break;	
		}		
		
		$sql="
		select 
		".$gcolonca." 
	   ,t.clicks
       ,t.views
       ,t.summa
	   ,t.cpc
	   ,t.ctr
	   ,t.inv
	   ,case when ap.pad is not null then 1 else 0 end as exc1
		from (
		select 
		".$colonca."
       ,sum(t1.clicks) as clicks
       ,sum(t1.views) as views
       ,sum(t1.summa) as summa
	   ,CASE WHEN (sum(t1.clicks)>0) then round(sum(t1.summa)/sum(t1.clicks)::numeric,4) else 0 end as cpc
	   ,CASE WHEN (sum(t1.views)>0) then round(sum(t1.clicks)/sum(t1.views)::numeric,4)*100 else 0 end as ctr
	   ,0 as inv
       from myadvert_summa_clicks t1 ";
	   $sql.=$sqlDop;
	   $sql.="
        where t1.day between '$from' and '$to' ";
		$sql.=$conback;
		$sql.="
		group by ".$group."
		) t
		left join advertise_pad_except ap 
		on ap.ads_id=$shop_id and ap.pad=t.pad
		order by $order $dire
		";
		//var_dump($sql); die();
		$nata=\DB::connection("pgstatistic")->select($sql);
		$found=count($nata);
		#var_dump($found);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($nata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		return view('widgets.company_excep', [
            'config' => $this->config,"colonca"=>$colonca,"collection"=>$data,"sorts"=>$sorts
			,"myadmin"=>$myAdmin
        ]);
 	//var_dump($data);
		
		$id=$m['id'];
		$sql="select domain, sum(summa) as summa, sum(views) as views, sum(clicks) as clicks from myadver_summa_clicks where shop_id='$id' group by 
		domain";
		$stats=\DB::connection("pgstatistic")->select($sql);
	    return view('widgets.company_excep', ['stats'=>$stats]);
    }
}
