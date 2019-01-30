<?php

namespace App\Widgets;

use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class MyPadDeatailStatistic extends AbstractWidget
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
		
		$idpad=$funkargs["pad"];
$name = $request->input("name");
		$dopSql="";
		$name = $request->input("name");
		if($name){
			$dopSql=" and (t1.request ~* '$name' or t1.url ~* '$name')";
#			$dopSql=" and (t1.request ~* '$name' or t1.url = '$name')";
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
		if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	}else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	}
	
	$funk='advertiser.site_statistic_pad';
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
	
	
		$sql="
		select 
		t1.day,
		t1.pad,
		t1.name,
		t1.request,
		t1.first_found,
		t1.url,
		t1.jns,
		t1.cnt,
		t1.clicks,
		t1.last_visit,
		CASE WHEN (t1.cnt>0 and t1.clicks>0) then round(t1.clicks/t1.cnt::numeric,4)*100 else (t1.cnt*-1)::numeric end as ctr
		from myadvert_pad_request t1
        where  t1.day between '$from' and '$to'  
		and pad=$idpad
		";
		$sql.=$dopSql;
		$sql.="
        order by $order $dire
		";
//    echo $sql;
		
		$xata=\DB::connection("pgstatistic")->select($sql);
			$found=count($xata);
			
        $page = $request->input('page', 1); 
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		#print "<pre>"; print_r($data); print "</pre>";
		
		unset($funkargs["sort"]);
		unset($funkargs["order"]);
		unset($funkargs["name"]);
	   
        return view('widgets.my_pad_deatail_statistic', [
             'config' => $this->config,"collection"=>$data,'sorts'=>$sorts,'funkargs'=>$funkargs
        ]);
    }
}
