<?php

namespace App\Widgets;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Arrilot\Widgets\AbstractWidget;
use Route;
use Auth;
class Mystatisticdetail extends AbstractWidget
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
        $funkargs=$m=Route::current()->parameters();
	    $domainSql=" ";
		$this->config["pad"]=0;
		if(isset($m["vid"]) && $m["vid"]){
			$this->config["pad"]=$m["vid"];
			$domainSql=" and t1.pad =".$m["vid"]." ";
		}
		#Route::current()->parameters();
		$funk=Route::currentRouteName();
		//$funk
		$vid=0;
        //var_dump($m);
		$id_user=0;
		$shop_id=0;
		$conback="";
		$detail_args=["id"=>0];
		$user=Auth::user();
		$myAdmin=0;
		if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$myAdmin=1;
		}else{
	    }
		if(isset($m["shop_id"])){
		}
		
		if(isset($m["id_user"]) && $m["id_user"]){
			$id_user=$m["id_user"];
			$sql="select id from advertises where user_id=".$id_user." ";
			$shops=\DB::connection("advertise")->select($sql);
			//var_dump($sql);
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
		$searchSql='';  
		$name=$request->input("name");
		if($name){
			$funkargs["name"]=$name;
			$searchSql=" and (t1.url ~* '".$name."' or t1.shop ~* '".$name."' or t1.request ~* '".$name."' ) ";  
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			  $to = date("Y-m-d");
			  $from = date("Y-m-d",(time()-(3600*24*7)));
		}
		$funkargs["from"]=$from;
		$funkargs["to"]=$to;
		$perPage = 50;
		$sqlDop="";
		$dire=$request->input("order");
		$sort=$request->input("sort");
		$colonca="t1.day,t1.hash,t1.url,t1.domain,t1.shop,t1.request,t1.first_found,t1.jns";
		$group="t1.day,t1.hash,t1.url,t1.domain,t1.shop,t1.request,t1.first_found,t1.jns";
		$order=""; 
		$funkargs["order"]="desc";
	    $funkargs["sort"]="views";
		$sorts["views"]='<a href="'.route($funk,$funkargs).'">Показы</a>';		
		$funkargs["sort"]="clicks";
		$sorts["clicks"]='<a href="'.route($funk,$funkargs).'">Клики</a>';			
		$funkargs["sort"]="ctr";
		$sorts["ctr"]='<a href="'.route($funk,$funkargs).'">CTR</a>';		
		$funkargs["sort"]="url";
		$sorts["url"]='<a href="'.route($funk,$funkargs).'">Страница</a>';		
		$funkargs["sort"]="domain";
		$sorts["domain"]='<a href="'.route($funk,$funkargs).'">Сайт</a>';		
		$funkargs["sort"]="shop";
		$sorts["shop"]='<a href="'.route($funk,$funkargs).'">Магазин</a>';	
		$funkargs["sort"]="ipscount";
		$sorts["ipscount"]='<a href="'.route($funk,$funkargs).'">(clicks / ip)</a>';
        $funkargs["sort"]="request";
		$sorts["request"]='<a href="'.route($funk,$funkargs).'">запрос</a>';			
		
		if(!isset($this->config["mod"]) || !$this->config["mod"])
		$this->config["mod"]="data";
	#var_dump($this->config["mod"]);
		switch($this->config["mod"]){
			case "domain":
			if(!$sort){
				$sort="day";
				$dire="desc";
			}
			break;
			default:
			return '';
			break;
		}
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
	#var_dump($sort);
	switch($sort){
		case "day":
		    #$funkargs["sort"]="day";
			#$funkargs["order"]=$ride;
	        #$sorts["day"]='<a href="'.route($funk,$funkargs).'">Дата '.$glip.'</a>';		
			$order="clicks"; 
		break;
		case "views":
		$funkargs["sort"]="views";
		$funkargs["order"]=$ride;
	    $sorts["views"]='<a href="'.route($funk,$funkargs).'">Показы '.$glip.'</a>';		
		$order="views"; 
		break;
		case "clicks":
		$funkargs["sort"]="clicks";
		$funkargs["order"]=$ride;
	    $sorts["clicks"]='<a href="'.route($funk,$funkargs).'">Клики '.$glip.'</a>';		
		$order="clicks"; 
		break;
		case "ctr":
		$funkargs["sort"]="ctr";
		$funkargs["order"]=$ride;
	    $sorts["ctr"]='<a href="'.route($funk,$funkargs).'">CTR '.$glip.'</a>';		
		$order="ctr"; 
		break;
		case "url":
		$funkargs["sort"]="url";
		$funkargs["order"]=$ride;
	    $sorts["url"]='<a href="'.route($funk,$funkargs).'">Страница '.$glip.'</a>';		
		$order="url"; 
		break;
		case "domain":
		$funkargs["sort"]="domain";
		$funkargs["order"]=$ride;
	    $sorts["domain"]='<a href="'.route($funk,$funkargs).'">Сайт '.$glip.'</a>';		
		$order="domain"; 
		break;
		case "shop":
		$funkargs["sort"]="shop";
		$funkargs["order"]=$ride;
	    $sorts["shop"]='<a href="'.route($funk,$funkargs).'">Магазин '.$glip.'</a>';		
		$order="shop"; 
		break;
		case "ipscount":
		$funkargs["sort"]="ipscount";
		$funkargs["order"]=$ride;
	    $sorts["ipscount"]='<a href="'.route($funk,$funkargs).'">(clicks / ip) '.$glip.'</a>';		
		$order="ipscount"; 
		break;		
		case "request":
		$funkargs["sort"]="request";
		$funkargs["order"]=$ride;
	    $sorts["request"]='<a href="'.route($funk,$funkargs).'">запрос '.$glip.'</a>';		
		$order="request"; 
		break;			
		default:
		return '';
		break;
		
	}
		$sql="

	    select 
        ".$colonca."		
		,sum(t1.clicks) as clicks
        ,sum(t1.views) as views
		,CASE WHEN (sum(t1.ipscount)>0) then round(sum(t1.clicks)/sum(t1.ipscount)::numeric,2) else 0 end as ipscount
        ,sum(t1.ipscount) as ipk		
		,CASE WHEN (sum(t1.views)>0) then round(sum(t1.clicks)/sum(t1.views)::numeric,4)*100 else 0 end as ctr
	   ,0 as inv
		from myadvert_stat_pages t1
        where t1.day between '$from' and '$to' ";
		$sql.=$conback;
		$sql.=$domainSql;
		$sql.=$searchSql;
		$sql.="
		group by ".$group."
		order by $order $dire
		";
		//echo "<pre>$sql</pre>";
		//var_dump($sql); die();
		$xata=\DB::connection("pgstatistic")->select($sql);
		
		$found=count($xata);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
	
	
		

		
        return view('widgets.mystatisticdetail', [
            'config' => $this->config,"collection"=>$data,"sorts"=>$sorts
        ]);
    }
}
