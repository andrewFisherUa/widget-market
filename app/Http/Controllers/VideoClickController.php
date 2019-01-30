<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use App\UserProfile;;

class VideoClickController extends Controller
{
     private $dbUser;
     private $dbPass;
    public function __construct()
      {
         $this->dbUser=env('DB_USERNAME');
         $this->dbPass=env('DB_PASSWORD');
        $this->middleware('auth');
    }
	public function index(Request $request)
    {
	\Auth::user()->touch();
	$collection=[];
  // $day=date("Y-m-d",time()-3600*48);
   $day=date("Y-m-d");
//var_dump($day);
	 $sql="
	 create temp table tmp1(
	host varchar(255),
	played int,
	clicked int,
	deep numeric (10,4)
	);
	insert into tmp1(
	host ,
	played ,
	clicked ,
	deep 
	)
   SELECT p.* FROM dblink('dbname=statistic_market_place port=5432 host=localhost
   user=".$this->dbUser." password=".$this->dbPass."',
            'select 
			host
			,played,
			clicked,case when played>0 then ROUND((cast(clicked as double precision)/cast(played as double precision))::numeric,4) else 0 end as deep 
from (
	select host,sum(played) as played,sum(clicked) as clicked from  videostatistic_cmpclicks
	where   day=''".$day."''
	group by host) t
')
       AS p(
       host varchar(255),
	played int,
	clicked int,
	deep numeric (10,4))";
	# where host=''lentateh.ru'' id_src=90 and
    $pdo = \DB::connection()->getPdo();
	$pdo->exec($sql);
	$sth=\DB::table("tmp1")->orderBy("deep","desc");
	$collection = $sth->paginate(30);
	    $params=["collection"=>$collection];
		return view('graph.index',$params);
	}
public function pad($id=0){
\Auth::user()->touch();
$date=date("Y-m-d");
 $c=\App\MPW\Statistic\VideostatisticAll::orderBy("day","desc");
 if($id)
 $c->where("pid",$id);
 $collection = $c->paginate(100);
 foreach($collection as $r){
 print "<pre>"; print_r($r->toArray());  print "</pre>";
 }
 $params=["collection"=>$collection];
			return view('graph.pad',$params);
}
		public function deep(){
		\Auth::user()->touch();
		$date=date("Y-m-d");
		#$this->funcMakeCommission();
		$sql="
		 create temp table tmp1(
         pid int
	   ,country varchar(3)
	   ,pad_id int
      
	    ,plays_all int
        ,fplays_all int
        ,firstplays_all int
        ,midplays_all int
        ,thirdplays_all int
        ,completeplays_all int
		,deep numeric(10,4)
		,util numeric(10,4)
	);
	insert into tmp1(
        pid
		,country
		,pad_id
		
		,plays_all
        ,fplays_all
        ,firstplays_all
        ,midplays_all
        ,thirdplays_all
        ,completeplays_all
		,deep 
		,util 
	)
SELECT p.* FROM dblink('dbname=statistic_market_place port=5432 host=localhost
        user=".$this->dbUser." password=".$this->dbPass."','select pid
		,country
		,pad_id
		
		,sum(plays_all) as plays_all
        ,sum(fplays_all) as fplays_all
        ,sum (firstplays_all)
        ,sum(midplays_all) as midplays_all
        ,sum(thirdplays_all) as thirdplays_all
        ,sum(completeplays_all) as completeplays_all
		,avg(deep) as deep 
		,avg(util) as util 
		from videostatistic_pids_all where day =''".$date."''
		group by pid
		,country
		,pad_id
		')
        as p(pid int
	   ,country varchar(3)
	   ,pad_id int
        ,plays_all int
        ,fplays_all int
        ,firstplays_all int
        ,midplays_all int
        ,thirdplays_all int
        ,completeplays_all int
		,deep numeric(10,4)
		,util numeric(10,4)
         )
		";
		$pdo = \DB::connection()->getPdo();
	$pdo->exec($sql);
	$sth=\DB::table("tmp1")->orderBy("plays_all","desc");
	$collection = $sth->paginate(30);
	$sites=[];
	$pids=[];
	foreach($collection as $p){
	#$p->deep=$this->funcMakeDeep($p);
	#$p->util=$this->funcMakeUtil($p);
	$pids[$p->pad_id]=1;
	}
	if($pids){
	$mypdo = \DB::connection("mysqlapi")->getPdo(); 
    $sql="select id,domain from partner_pads where id  in(".implode(",",array_keys($pids)).") ";
	$result = $mypdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	foreach($result as $r){
	$sites[$r["id"]]=$r["domain"];
	#print "<pre>"; print_r($r); print "</pre>";
	}
	
	}
		    $params=["collection"=>$collection,"sites"=>$sites];
			return view('graph.deep',$params);
	}
	
	  private function funcMakeUtil(&$obj){
	  \Auth::user()->touch();
  if($obj->plays_all){
  $x1=$obj->fplays_all*100/$obj->plays_all;
  //thirdplays_all int
      //  ,completeplays_all int
  }
  else{
  $x1=0;
 
  }
   $x=$x1;
	 return round($x,4);
	  }
  private function funcMakeDeep(&$obj){
  \Auth::user()->touch();

  	   if($obj->fplays_all){
  $x1=$obj->firstplays_all*100/$obj->fplays_all;
  
  $x2=$obj->midplays_all*100/$obj->fplays_all;
  $x3=$obj->thirdplays_all*100/$obj->fplays_all;
  $x4=$obj->completeplays_all*100/$obj->fplays_all;
  //thirdplays_all int
      //  ,completeplays_all int
  }
  else{
  $x1=0;
  $x2=0;
  $x3=0;
  $x4=0;  
  }
  $x=0;
  #$x+=$x1/16+$x2/8+$x3/4+$x4/2;
  $x=$x4;
         #print $obj->fplays_all." от  ".$obj->firstplays_all." = $x1 <hr>";
         
  return round($x,4);
  }  
		
  private function funcMakeCommission(){
  \Auth::user()->touch();
     $from=$to="CURRENT_DATE";
     $pdo = \DB::connection("pgstatistic")->getPdo();
	 $sql="select country,day,pid
	 ,sum(fplays_user) as fplays_user
     ,sum(fplays_all) as fplays_all
	 FROM videostatistic_pids WHERE day BETWEEN $from and $to group by country,day,pid";
	 $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	 foreach($result as $row){
		
		    if($row["pid"]==409)continue;
            //

            $widget=\App\Models\Widgets\VideoWidget::find($row['pid']);
            if(!$widget){
                continue;
            }
			$comgr=$widget->CommissionGroup();
			$fcomgr=$widget->ForeignCommissionGroup();
			 if($row['country']=='RU'){
		    $val=round($comgr->value*$row['fplays_user']/1000,4);
			}else{
            $val=round($fcomgr->value*$row['fplays_user']/1000,4);
			}
		    #print "<pre>"; print_r($comgr->toArray()); print "</pre>";
			if($val){
			print "<pre>"; print_r([$val,$row["fplays_user"],$row["fplays_all"]]); print "</pre>";
			}
	 }
    }	
}
