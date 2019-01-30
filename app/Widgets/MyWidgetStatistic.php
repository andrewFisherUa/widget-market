<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Route;
use Auth;
class MyWidgetStatistic extends AbstractWidget
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
		$perPage = 50;
        $funkargs=$m=Route::current()->parameters();
		if(isset($funkargs["widget_id"])){
		$pid=$funkargs["widget_id"];
		}else{
			return "";
		}
		$dopSql="";
		$name = $request->input("name");
		if($name){
			$dopSql=" and (t1.request ~* '$name' or t1.url ~* '$name')";
			$funkargs["name"]=$name;
		}	
	    $from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			  $to = date("Y-m-d");
			  $from = date("Y-m-d",(time()-(0)));
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

        $funk='advertiser.widget_statistic';
		$funkargs["from"]=$from;
		$funkargs["to"]=$to;
		
			$funkargs["from"]=$from;
	$funkargs["to"]=$to;
	$funkargs["sort"]="day";
	$sorts["day"]='<a href="'.route($funk,$funkargs).'">день </a>';		
	$funkargs["sort"]="name";
	$sorts["name"]='<a href="'.route($funk,$funkargs).'">площадка </a>';		
	$funkargs["sort"]="request";
	$sorts["request"]='<a href="'.route($funk,$funkargs).'">запрос </a>';		
	$funkargs["sort"]="first_found";
	$sorts["first_found"]='<a href="'.route($funk,$funkargs).'">товар </a>';		
	$funkargs["sort"]="cnt";
	$sorts["cnt"]='<a href="'.route($funk,$funkargs).'">просмотров </a>';		
	$funkargs["sort"]="clicks";
	$sorts["clicks"]='<a href="'.route($funk,$funkargs).'">клики </a>';	
	$funkargs["sort"]="ctr";
	$sorts["ctr"]='<a href="'.route($funk,$funkargs).'">цтр </a>';		
	$funkargs["sort"]="last_visit";
	$sorts["last_visit"]='<a href="'.route($funk,$funkargs).'">последний запрос </a>';	
	
	
		/*
		
	    $funkargs["sort"]="url";
		$sorts["url"]='<a href="'.route($funk,$funkargs).'">url</a>';		
		$funkargs["sort"]="request";
		$sorts["request"]='<a href="'.route($funk,$funkargs).'">строка</a>';			
		$funkargs["sort"]="requested";
		$sorts["requested"]='<a href="'.route($funk,$funkargs).'">запросы</a>';		
		$funkargs["sort"]="found";
		$sorts["found"]='<a href="'.route($funk,$funkargs).'">найдено</a>';		
		$funkargs["sort"]="viewed";
		$sorts["viewed"]='<a href="'.route($funk,$funkargs).'">отношение</a>';		
		$funkargs["sort"]="all_my_clicks";
		$sorts["all_my_clicks"]='<a href="'.route($funk,$funkargs).'">наши клики</a>';		
		$funkargs["sort"]="all_my_ctr";
		$sorts["all_my_ctr"]='<a href="'.route($funk,$funkargs).'">наш цтр</a>';	
		
		*/
		
		
		
		switch($sort){
		case "day":
		    $funkargs["sort"]="day";
			$funkargs["order"]=$ride;
	        $sorts["day"]='<a href="'.route($funk,$funkargs).'">день '.$glip.'</a>';		
			$order="day"; 
		break;
		case "name":
		    $funkargs["sort"]="name";
			$funkargs["order"]=$ride;
	        $sorts["name"]='<a href="'.route($funk,$funkargs).'">площадка '.$glip.'</a>';		
			$order="name"; 
		break;
		case "request":
		    $funkargs["sort"]="request";
			$funkargs["order"]=$ride;
	        $sorts["request"]='<a href="'.route($funk,$funkargs).'">запрос '.$glip.'</a>';		
			$order="request"; 
		break;
		case "first_found":
		    $funkargs["sort"]="first_found";
			$funkargs["order"]=$ride;
	        $sorts["first_found"]='<a href="'.route($funk,$funkargs).'">товар '.$glip.'</a>';		
			$order="first_found"; 
		break;
		case "cnt":
		    $funkargs["sort"]="cnt";
			$funkargs["order"]=$ride;
	        $sorts["cnt"]='<a href="'.route($funk,$funkargs).'">показы '.$glip.'</a>';		
			$order="cnt"; 
		break;		
		case "clicks":
		    $funkargs["sort"]="clicks";
			$funkargs["order"]=$ride;
	        $sorts["clicks"]='<a href="'.route($funk,$funkargs).'">клики '.$glip.'</a>';		
			$order="clicks"; 
		break;	
		case "ctr":
		    $funkargs["sort"]="ctr";
			$funkargs["order"]=$ride;
	        $sorts["ctr"]='<a href="'.route($funk,$funkargs).'">цтр '.$glip.'</a>';		
			$order="ctr"; 
		break;	
		case "last_visit":
		    $funkargs["sort"]="last_visit";
			$funkargs["order"]=$ride;
	        $sorts["last_visit"]='<a href="'.route($funk,$funkargs).'">последний запрос '.$glip.'</a>';		
			$order="last_visit"; 
		break;			
		}
		$stata=[];
		$data=[];
		$sql="
		select 
		t1.day,
		t1.pad,
		t1.pid,
		t1.name,
		t1.request,
		t1.first_found,
		t1.url,
		t1.jns,
		t1.cnt,
		t1.clicks,
		t1.last_visit,
		CASE WHEN (t1.cnt>0 and t1.clicks>0) then round(t1.clicks/t1.cnt::numeric,4)*100 else (t1.cnt*-1)::numeric end as ctr
		from myadvert_pid_request t1
        where  t1.day between '$from' and '$to'  
		and pid=$pid
		";
		$sql.=$dopSql;
		$sql.="
        order by $order $dire
		";
		$xata=\DB::connection("pgstatistic")->select($sql);
		
		#print "<pre>"; print_r($xata); print "</pre>";
        $found=count($xata);
		#print "<pre>"; print_r($found); print "</pre>";
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
        return view('widgets.my_widget_statistic', [
            'config' => $this->config,"collection"=>$data,'sorts'=>$sorts,"stata"=>$stata
        ]);
    }
}
