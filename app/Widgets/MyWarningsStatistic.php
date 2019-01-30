<?php

namespace App\Widgets;
use Illuminate\Pagination\LengthAwarePaginator;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
use Auth;


class MyWarningsStatistic extends AbstractWidget
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
			$dopSql=" and (ltd ~* '$name' or url ~* '$name' or message ~* '$name')";
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
	    $sort="new";	
        if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	    }else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	    }
		$funk=Route::currentRouteName();
		$funkargs["from"]=$from;
		$funkargs["to"]=$to;
		
		$funkargs["sort"]="datetime";
	    $sorts["datetime"]='<a href="'.route($funk,$funkargs).'" title ="время последнего сообщения">дата </a>';		
		$funkargs["sort"]="ltd";
	    $sorts["ltd"]='<a href="'.route($funk,$funkargs).'" title ="сайт">сайт	</a>';		
		$funkargs["sort"]="message";
	    $sorts["message"]='<a href="'.route($funk,$funkargs).'" title ="сообщение">сообщение </a>';	
		$funkargs["sort"]="cnt";
	    $sorts["cnt"]='<a href="'.route($funk,$funkargs).'" title ="количество событий">кол. событий	</a>';	
		$funkargs["sort"]="ip";
	    $sorts["ip"]='<a href="'.route($funk,$funkargs).'" title ="ip">ip	</a>';	
		$funkargs["sort"]="new";
	    $sorts["new"]='<a href="'.route($funk,$funkargs).'" title ="новое">новое	</a>';	
		
		switch($sort){
		case "datetime":
		    $funkargs["sort"]="datetime";
			$funkargs["order"]=$ride;
	        $sorts["datetime"]='<a href="'.route($funk,$funkargs).'"  title ="время последнего сообщения">дата '.$glip.'</a>';		
			$order="datetime"; 
		break;
case "ltd":
		    $funkargs["sort"]="ltd";
			$funkargs["order"]=$ride;
	        $sorts["ltd"]='<a href="'.route($funk,$funkargs).'"  title ="сайт">сайт '.$glip.'</a>';		
			$order="ltd"; 
		break;
case "message":
		    $funkargs["sort"]="message";
			$funkargs["order"]=$ride;
	        $sorts["message"]='<a href="'.route($funk,$funkargs).'"  title ="сообщение">сообщение '.$glip.'</a>';		
			$order="message"; 
		break;
case "cnt":
		    $funkargs["sort"]="cnt";
			$funkargs["order"]=$ride;
	        $sorts["cnt"]='<a href="'.route($funk,$funkargs).'"  title ="количество событий">кол. событий '.$glip.'</a>';		
			$order="cnt"; 
		break;
case "ip":
		    $funkargs["sort"]="ip";
			$funkargs["order"]=$ride;
	        $sorts["ip"]='<a href="'.route($funk,$funkargs).'"  title ="ip">ip '.$glip.'</a>';		
			$order="ip"; 
		break;		
case "new":
		    $funkargs["sort"]="new";
			$funkargs["order"]=$ride;
	        $sorts["new"]='<a href="'.route($funk,$funkargs).'"  title ="новое">новое '.$glip.'</a>';		
			$order="new"; 
		break;			
		}
		$sql="select 
		id
		,pad
		,pid
		,new
		,datetime
		,message
		,url
		,ltd
		,cnt
		,ip
		from myadvert_warnings
		where datetime::date between '$from' and '$to'
        ";
		$sql.=$dopSql;
		$sql.="
        order by $order $dire
		";
		
		$xata=\DB::connection("pgstatistic")->select($sql);
		$found=count($xata);
		$page = $request->input('page', 1); 
        $offset = ($page * $perPage) - $perPage;
        $data = new LengthAwarePaginator(array_slice($xata, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$idm=[];
		foreach($data as $d){
		if($d->new) {
		$idm[]=$d->id;	
		}
		}
		if($idm){
			$sql="update myadvert_warnings set new =0 where id in(".implode(",",$idm).")";
			\DB::connection("pgstatistic")->getPdo()->exec($sql);
			
		}
		
		
		unset($funkargs["sort"]);
		unset($funkargs["order"]);
		unset($funkargs["name"]);
		
		
        return view('widgets.my_warnings_statistic', [
            'config' => $this->config,"collection"=>$data,'sorts'=>$sorts,'funkargs'=>$funkargs
        ]);
    }
}
