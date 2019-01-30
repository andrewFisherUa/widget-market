<?php

namespace App\Widgets;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class Mystatistic extends AbstractWidget
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
		$m=Route::current()->parameters();
        //var_dump($m);
		$id_user=0;
		$shop_id=0;
		$conback="";
		$funk='advertiser.statistic';
		$detailfunc="advertiser.statistic_detail";	
		$funkargs=["id"=>0];
		$detail_args=["id"=>0];
		$user=Auth::user();
		$myAdmin=0;
		if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$myAdmin=1;
		}else{
	    }
		
		
		if(isset($m["shop_id"])){
			$funkargs["id"]=$m["shop_id"];
			$detail_args["id"]=$m["shop_id"];
		}
		
		if(isset($m["id_user"]) && $m["id_user"]){
		$detailfunc="admin.statistic_detail";	
		
		 // 
 		$funk='admin.statistic';
		
       
			$id_user=$m["id_user"];
			$funkargs["id_user"]=$id_user;
			$detail_args["id_user"]=$id_user;
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
		}else{
			//var_dump(111); die();
			
		if($myAdmin){
			
		}else{
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
		
		
		#$pdo=\DB::connection("pgstatistic")-getPdo();
		
		$perPage = 10;
		//$chop_id=isset($m["shop_id"])?$m["shop_id"]:0;
		$sqlDop="";

		
		$dire=$request->input("order");
		$sort=$request->input("sort");

		
		$colonca="t1.day";
		$group="t1.day";

		$order=""; 
		
		
		
	    $funkargs["from"]=$from;
		$funkargs["to"]=$to;
		$detail_args["from"]=$from;
		$detail_args["to"]=$to;
		
		$sorts=[
		"day"=>""
	   ,"clicks"=>""
       ,"views"=>""
       ,"summa"=>""
	   ,"cpc"=>""
	   ,"ctr"=>""
	   ,"inv"=>""
		];
		//var_dump($funkargs);
		
		$funkargs["order"]="desc";
		$funkargs["mod"]=$this->config["mod"];
        $detail_args["mod"]=$this->config["mod"];
		$funkargs["sort"]="day";
		$sorts["day"]='<a href="'.route($funk,$funkargs).'">Дата</a>';
		$funkargs["sort"]="region";
		$sorts["region"]='<a href="'.route($funk,$funkargs).'">Регионы</a>';
		$funkargs["sort"]="category";
		$sorts["category"]='<a href="'.route($funk,$funkargs).'">Категории</a>';
		$funkargs["sort"]="domain";
		$sorts["domain"]='<a href="'.route($funk,$funkargs).'">Домены</a>';
		$funkargs["sort"]="company";
		$sorts["company"]='<a href="'.route($funk,$funkargs).'">Магазины</a>';
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
		
		//var_dump($dire);
		//var_dump($sort);
		$wf="widgets.mystatistic";

		if(!isset($this->config["mod"]) || !$this->config["mod"])
		$this->config["mod"]="data";
		
		switch($this->config["mod"]){
			case "data":
			if(!$sort){
				$sort="clicks";
				$dire="desc";
			}
			$wf="widgets.stat.date";
			break;
			case "region":
			$colonca="t1.id_region,coalesce(t1.region,t1.id_region::varchar) as region";
			$group="t1.id_region,t1.region";
			if(!$sort){
						$sort="clicks";
		                $dire="desc";
				
			}
			$wf="widgets.stat.region";
			break;
			case "category":
			$colonca="t1.id_tree,t1.category";
			$group="t1.id_tree,t1.category";
			if(!$sort){
						$sort="clicks";
		                $dire="desc";
			}
			$wf="widgets.stat.category";
			break;
			case "domain":
			
			if($myAdmin){
			$colonca="t1.pad,t1.domain||' ('||t1.pad::varchar||') ' as domain";
			$group="t1.pad,t1.domain";
			}else{
				$colonca="t1.pad,t1.pad::varchar as domain";
			    $group="t1.pad,t1.pad::varchar";
			}
			$wf="widgets.stat.domain";
			if(!$sort){
						$sort="clicks";
		                $dire="desc";
			}
			break;
			case "company":
				$colonca="t1.shop_id,coalesce(t1.shop,t1.shop_id::varchar) as shop";
				$group="t1.shop_id,t1.shop";
			if(!$sort){
						$sort="clicks";
		                $dire="desc";
			}	
			$wf="widgets.stat.company";
			break;
			default:
			var_dump(["неизвестный модуль",$this->config["mod"]]); exit();
			break;
		}
	if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	}else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	}
	switch($sort){
		case "day":
		    $funkargs["sort"]="day";
			$funkargs["order"]=$ride;
	        $sorts["day"]='<a href="'.route($funk,$funkargs).'">Дата '.$glip.'</a>';		
			$order="t1.day"; 
		break;
		case "region":
		    $funkargs["sort"]="region";
			$funkargs["order"]=$ride;
	        $sorts["region"]='<a href="'.route($funk,$funkargs).'">Регионы '.$glip.'</a>';		
			$order="coalesce(t1.region,t1.id_region::varchar)";
		break;
		case "category":
		    $funkargs["sort"]="category";
			$funkargs["order"]=$ride;
	        $sorts["category"]='<a href="'.route($funk,$funkargs).'">Категори '.$glip.'</a>';		
			$order="t1.category"; 
		break;
		case "domain":
		    $funkargs["sort"]="domain";
			$funkargs["order"]=$ride;
	        $sorts["domain"]='<a href="'.route($funk,$funkargs).'">Домены '.$glip.'</a>';		
			if($myAdmin){
			$order="t1.domain"; 
			}else{
			$order="t1.pad::varchar"; 	
			}
		break;
		case "company":
		    $funkargs["sort"]="company";
			$funkargs["order"]=$ride;
	        $sorts["company"]='<a href="'.route($funk,$funkargs).'">Магазины '.$glip.'</a>';		
			$order="coalesce(t1.shop,t1.shop_id::varchar)"; 
			
			
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
		order by $order $dire
		";
		//echo nl2br($sql);
		/*
		select t1.pad,t1.domain||' ('||t1.pad::varchar||') ' as domain ,sum(t1.clicks) as clicks ,sum(t1.views) as views ,sum(t1.summa) as summa ,CASE WHEN (sum(t1.clicks)>0) then round(sum(t1.summa)/sum(t1.clicks)::numeric,4) else 0 end as cpc ,CASE WHEN (sum(t1.views)>0) then round(sum(t1.clicks)/sum(t1.views)::numeric,4)*100 else 0 end as ctr ,0 as inv from myadvert_summa_clicks t1 where t1.day between '2018-01-29' and '2018-02-05' group by t1.pad,t1.domain order by t1.domain asc
		*/
		#var_dump($sql);
		$nata=\DB::connection("pgstatistic")->select($sql);
		$found=count($nata);
		#var_dump($found);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($nata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
	
		
        return view($wf, [
            'config' => $this->config,"colonca"=>$colonca,"collection"=>$data,"sorts"=>$sorts
			,"func"=>$detailfunc,"args"=>$detail_args,"myadmin"=>$myAdmin
        ]);
    }
}
