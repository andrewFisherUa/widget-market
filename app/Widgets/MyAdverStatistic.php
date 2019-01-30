<?php

namespace App\Widgets;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;
class MyAdverStatistic extends AbstractWidget
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
        $name = $request->input("name");
		$dopSql="";
		//$name = $request->input("name");
		if($name){
			$dopSql=" and t1.name ~* '$name'";
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
	//var_dump(3);
/*
		if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	}else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	}
*/
	if($this->config["driver"]==2){
	    $funk='advertiser.yandex_statistic';
	}else{
		$funk='advertiser.site_statistic';
	}
	$funkargs["from"]=$from;
		$funkargs["to"]=$to;
	$funkargs["sort"]="day";
	$sorts["day"]='<a href="'.route($funk,$funkargs).'" title ="день">день </a>';		
	$funkargs["sort"]="name";
	$sorts["name"]='<a href="'.route($funk,$funkargs).'" title ="название сайта">площадка </a>';		
	$funkargs["sort"]="views";
	$sorts["views"]='<a href="'.route($funk,$funkargs).'" title ="общие показы">показы </a>';		
	$funkargs["sort"]="clicks";
	$sorts["clicks"]='<a href="'.route($funk,$funkargs).'" title ="общие клики">клики </a>';		
	$funkargs["sort"]="sclicks";
	$sorts["sclicks"]='<a href="'.route($funk,$funkargs).'" title ="клики прямых рекламодателей" >~ клики </a>';	
	$funkargs["sort"]="ctr";
	$sorts["ctr"]='<a href="'.route($funk,$funkargs).'" title ="общий цтр">цтр </a>';		
	$funkargs["sort"]="summa";
	$sorts["summa"]='<a href="'.route($funk,$funkargs).'" title ="общая сумма заработка">сумма </a>';		
		$funkargs["sort"]="ssumma";
	$sorts["ssumma"]='<a href="'.route($funk,$funkargs).'" title ="сумма с прямых рекламодателей">~ сумма </a>';		
	$funkargs["sort"]="sctr";
	$sorts["sctr"]='<a href="'.route($funk,$funkargs).'" title ="выплата по прямым рекламодателям">~ выплата </a>';			

	
		
		switch($sort){
		case "day":
		    $funkargs["sort"]="day";
			$funkargs["order"]=$ride;
	        $sorts["day"]='<a href="'.route($funk,$funkargs).'"  title ="день">день '.$glip.'</a>';		
			$order="day"; 
		break;
		case "name":
		    $funkargs["sort"]="name";
			$funkargs["order"]=$ride;
	        $sorts["name"]='<a href="'.route($funk,$funkargs).'" title ="название сайта">площадка '.$glip.'</a>';		
			$order="name"; 
		break;
		case "views":
		    $funkargs["sort"]="views";
			$funkargs["order"]=$ride;
	        $sorts["views"]='<a href="'.route($funk,$funkargs).'" title ="общие показы">показы '.$glip.'</a>';		
			$order="views"; 
		break;
		case "clicks":
		    $funkargs["sort"]="clicks";
			$funkargs["order"]=$ride;
	        $sorts["clicks"]='<a href="'.route($funk,$funkargs).'" title ="общие клики">клики '.$glip.'</a>';		
			$order="clicks"; 
		break;
		case "sclicks":
		    $funkargs["sort"]="sclicks";
			$funkargs["order"]=$ride;
	        $sorts["sclicks"]='<a href="'.route($funk,$funkargs).'" title ="клики прямых рекламодателей">~ клики '.$glip.'</a>';		
			$order="sclicks"; 
		break;
		case "ctr":
		    $funkargs["sort"]="ctr";
			$funkargs["order"]=$ride;
	        $sorts["ctr"]='<a href="'.route($funk,$funkargs).'" title ="общий цтр">цтр '.$glip.'</a>';		
			$order="ctr"; 
		break;		
	    case "summa":
		    $funkargs["sort"]="summa";
			$funkargs["order"]=$ride;
	        $sorts["summa"]='<a href="'.route($funk,$funkargs).'" title ="общая сумма заработка">сумма '.$glip.'</a>';		
			$order="summa"; 
		break;				
		case "ssumma":
		    $funkargs["sort"]="ssumma";
			$funkargs["order"]=$ride;
	        $sorts["ssumma"]='<a href="'.route($funk,$funkargs).'" title ="сумма с прямых рекламодателей">~ сумма '.$glip.'</a>';		
			$order="ssumma"; 
		break;		
		case "sctr":
		    $funkargs["sort"]="sctr";
			$funkargs["order"]=$ride;
	        $sorts["sctr"]='<a href="'.route($funk,$funkargs).'" title ="выплата по прямым рекламодателям">~ выплата '.$glip.'</a>';		
			$order="psumma"; 
		break;				
		}
		if($this->config["driver"]==2){
					$sql="
		select 
		count(pad) as cnt,
		sum(t1.yviews) as views,
		sum(t1.yclicks) as clicks,
		0 as sclicks,
		sum(t1.ysumma) as summa,
		0 as ssumma,
		CASE WHEN (sum(t1.yviews)>0 and sum(t1.yclicks)>0) then round(sum(t1.yclicks)/sum(t1.yviews)::numeric,4)*100 else 0::numeric end as ctr,
		0 as sctr
		from myadvert_sites t1
        where  t1.day between '$from' and '$to' 
        and 		t1.yviews>0
		";
		$sql.=$dopSql;
		$sql.="
        
		";

		}else{
		$sql="
		select 
		count(pad) as cnt,
		sum(t1.views) as views,
		sum(t1.clicks) as clicks,
		sum(t1.sclicks) as sclicks,
		sum(t1.summa) as summa,
		sum(t1.ssumma) as ssumma,
		sum(t1.psumma) as psumma,
		CASE WHEN (sum(t1.views)>0 and sum(t1.clicks)>0) then round(sum(t1.clicks)/sum(t1.views)::numeric,4)*100 else 0::numeric end as ctr,
		0 as sctr
		from myadvert_sites t1
        where  t1.day between '$from' and '$to' 
        and 		t1.views>0
		";
		$sql.=$dopSql;
		$sql.="
        
		";
		
        }
		//var_dump($sql);
		$stata=\DB::connection("pgstatistic")->select($sql);
		if($this->config["driver"]==2){
			$sql="
		select 
		t1.day,
		t1.pad,
		t1.name,
		t1.yviews as views,
		t1.yclicks as clicks,
		t1.clicks as sclicks,
		t1.ysumma as summa,
		t1.summa as ssumma,
		
		CASE WHEN (t1.yviews>0 and t1.yclicks>0) then round(t1.yclicks/t1.yviews::numeric,4)*100 else (t1.yviews*-1)::numeric end as ctr,
		0 as sctr
		from myadvert_sites t1
        where  t1.day between '$from' and '$to'  
		and	(t1.yviews>0)
		";
		$sql.=$dopSql;
		$sql.="
        order by $order $dire
		";	
		}else{
		$sql="
		select 
		t1.day,
		t1.pad,
		t1.name,
		t1.views,
		t1.clicks,
		t1.sclicks,
		t1.summa,
		t1.ssumma,
		t1.psumma as psumma,
		CASE WHEN (t1.views>0 and t1.clicks>0) then round(t1.clicks/t1.views::numeric,4)*100 else (t1.views*-1)::numeric end as ctr,
		0 as sctr
		from myadvert_sites t1
        where  t1.day between '$from' and '$to'  
		and	t1.views>0
		";
		$sql.=$dopSql;
		$sql.="
        order by $order $dire
		";
		}
		$xata=\DB::connection("pgstatistic")->select($sql);
		
		$found=count($xata);
        $page = $request->input('page', 1); 
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
	#	print "<pre>"; print_r($data); print "</pre>";
		
		unset($funkargs["sort"]);
		unset($funkargs["order"]);
		unset($funkargs["name"]);
		#var_dump($stata); die();
        return view('widgets.my_adver_statistic', [
           'config' => $this->config,"collection"=>$data,'sorts'=>$sorts,'funkargs'=>$funkargs,'stata'=>$stata
        ]);
    }
}
