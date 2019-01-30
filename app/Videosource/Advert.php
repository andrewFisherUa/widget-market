<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
	private $widgets=[];
	private $widgetclicks=[];
	private $clids=[];
	private $nads=[];
	private $pids=[];
	private $wids=[];
	private $views=[];
	private $tidges=[];
	private $tinets=[];	
    public function collectViews($date){

	    #$this->repareStat();

		
		
		$pdow=\DB::connection()->getPdo();
	    $sql="select * from widget_tizers";
		$ww = $pdow->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($ww as $wp){
			$this->tidges[$wp["wid_id"]]=$wp["id"];
			#$this->tidges[$wp["id"]]=$wp["wid_id"];
		}
		
		$pdo=\DB::connection("pgstatistic")->getPdo();

		$pds=\App\WidgetEditor::All();
		foreach($pds as $p){
			#echo $p->id.":".$p->wid_id."\n";
			$this->pids[$p->id]=$p->wid_id;
			$this->wids[$p->wid_id]=$p->id;
			#var_dump($p->id);
		}
		
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_clicks where date= '$date'
		and driver=2
        group by id_widget,id_server,driver,clid 
        ";
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		if($click["clid"]){ 
		$this->clids[$click["clid"]][$click["id_widget"]]=$click["cnt"];
		#var_dump($click);
		#print "\n";
		#if(!isset($this->widgetclicks[$click["id_widget"]][$click["driver"]]))
			#$this->widgetclicks[$click["id_widget"]][$click["driver"]]=0;
		   # $this->widgetclicks[$click["id_widget"]][$click["driver"]]+=$click["cnt"];
		
		}
		}

		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_pages where day= '$date'
		and driver=2
        group by id_widget,id_server,driver,clid 
        ";
		#var_dump($sql); die();
		$clicks= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($clicks as $click){
		if($click["clid"]){ 
		$this->widgets[$click["id_widget"]]["showYa"]=$click["cnt"];
		#var_dump($click);
			}
		}
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_pages where day= '$date'
		and driver in(1,777)
        group by id_widget,id_server,driver,clid 
        ";
		$clicks= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($clicks as $click){
		if($click["id_widget"]=="819"){
			#var_dump($click);
			}
                 if(isset($this->widgets[$click["id_widget"]]["viewsJT"]))
                $this->widgets[$click["id_widget"]]["viewsJT"]+=$click["cnt"];
                else
		$this->widgets[$click["id_widget"]]["viewsJT"]=$click["cnt"];
        
		}
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_clicks where date= '$date'
		and driver=11
        group by id_widget,id_server,driver,clid 
        ";
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		if($click["clid"]){ 
		$this->nads[$click["clid"]][$click["id_widget"]]=$click["cnt"];
		#print $click["clid"]." / ".$click["id_widget"]."\n";
		#if(!isset($this->widgetclicks[$click["id_widget"]][$click["driver"]]))
			#$this->widgetclicks[$click["id_widget"]][$click["driver"]]=0;
		   # $this->widgetclicks[$click["id_widget"]][$click["driver"]]+=$click["cnt"];
		
		}
		}

		
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_pages where day= '$date'
		and driver=11
        group by id_widget,id_server,driver,clid 
        ";
		$clicks= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach($clicks as $click){
		    if($click["clid"]){ 
			#$this->nads[$click["clid"]][$click["id_widget"]]=$click["cnt"];
			$this->widgets[$click["id_widget"]]["showNa"]=$click["cnt"];
		     # var_dump($click);
		    }		
		}
		$this->getTopNadavi($date);
		var_dump("т1");
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_pages where day= '$date'
		and driver=4
        group by id_widget,id_server,driver,clid 
        ";
		
		$clicks= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($clicks as $click){
		
		$this->widgets[$click["id_widget"]]["showTeaserNet"]=$click["cnt"];
		#var_dump($click);
			
		}

		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_clicks where date= '$date'
		and driver=4
        group by id_widget,id_server,driver,clid 
        ";
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		$this->tinets[$click["id_widget"]]=$click["cnt"];	
		#$this->widgets[$click["id_widget"]]["showNa"]=$click["cnt"];
		
		}
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_pages where day= '$date'
		and driver=2000
        group by id_widget,id_server,driver,clid 
        ";

		
		$clicks= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($clicks as $click){
		
		$this->widgets[$click["id_widget"]]["showkokos"]=$click["cnt"];
		#var_dump($click);
			
		}
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_clicks where date= '$date'
		and driver=2000
        group by id_widget,id_server,driver,clid 
        ";
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		$this->tinets[$click["id_widget"]]=$click["cnt"];	
		#$this->widgets[$click["id_widget"]]["showNa"]=$click["cnt"];
		
		}

#var_dump($this->widgets); die();
		$preperstpect = \DB::connection("pgstatistic_next")->getPdo();
		$sql="select day,id_widget,count(*) as cnt from product_views_pages where day= '$date'
group by day,id_widget";
$wdw = $preperstpect->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($wdw as $click){
            if(!isset($this->widgets[$click["id_widget"]]["viewsJT"]))
			$this->widgets[$click["id_widget"]]["viewsJT"]=$click["cnt"];
		    else
            $this->widgets[$click["id_widget"]]["viewsJT"]+=$click["cnt"];
		}	
		

		
		$sql="select wid,sum(price) as sum,count(*) as cnt 
		from myadvert_clicks where day= '$date'
		group by wid
		";
		$mclicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		#var_dump($mclicks); die();
		foreach($mclicks as $kcc){
				$this->widgets[$kcc["wid"]]["clicksMein"]=$kcc["cnt"];
		        $this->widgets[$kcc["wid"]]["summaMein"]=$kcc["sum"];
			    #$this->widgets[$click["id_widget"]]["showNa"]=$click["cnt"];
		}


		
		$this->getTeaserNet($date);
		var_dump("т00");
		#exit();
		$this->getTopYandex($date);
		#var_dump("т2");
		$this->getTopAdvert($date);
		var_dump("т3");
		$this->getTopTeaser($date);
		var_dump("т4");
		$this->getTopKokos($date);
		var_dump("т2000");
		$this->RegStat($date);
		
	}
    private function getTeaserNet($date){
	$pgpdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select wid,count(*) as cnt,sum(summa) as sum from teasernet_clicks
        where day= '$date' group by wid";	
		$states= $pgpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($states as $stat){
		
   		$this->widgets[$stat["wid"]]["clicksTeaser"]=$stat["cnt"];
		$this->widgets[$stat["wid"]]["summaTeasernet"]=$stat["sum"];
		#var_dump($stat);
		}
    }
	private function getTopKokos($date){
	$pgpdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select pid, sum(clicks) as clicks, sum(summa) as sum from kokos_sum
        where day= '$date' group by pid";	
		$states= $pgpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($states as $stat){
		
   		$this->widgets[$stat["pid"]]["clickskokos"]=$stat["clicks"];
		$this->widgets[$stat["pid"]]["summakokos"]=$stat["sum"];
		var_dump($stat);
		}
    }	
	private function getTopNadavi($date){
	$pgpdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select * from nadavi_summa where day= '$date'";	
		$states= $pgpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($states as $stat){
			$stat["summa"]=$stat["clicks"]*3;
			if(isset($this->nads[$stat["pid"]])){
				$summa =array_sum($this->nads[$stat["pid"]]);
			#var_dump($this->nads[$stat["pid"]]);
			     print $summa." --> ухб\n";
				if($summa){
             # var_dump($stat); 
			       foreach($this->nads[$stat["pid"]] as $key => $val){
					     					$x=$val*100/$summa;
					                        $finalSumma=$stat["summa"]/100*$x;
					                        $finaClicks=round($stat["clicks"]/100*$x); 
					if(!isset($this->wids[$key])){
					var_dump(["нечем порадовать",$key]);
					continue;
					}
					$this->widgets[$key]["summaNa"]=$finalSumma;
   				    $this->widgets[$key]["clicksNa"]=$finaClicks;
					print $key." >><<   ||  >><< ".$stat["clicks"]." / $finaClicks / ".$finalSumma." \n";						
				   }
				}
			}
		}
	}
	private function getTopTeaser($date){
		$pdo=\DB::connection("pgstatistic")->getPdo();
		$sql=" select * from teaser_stat_pages where day= '$date'";

	    $pages= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($pages as $p){
			$pidId=$p["id_widget"];
			if(isset($this->tidges[$pidId])){
			
			}elseif(isset($this->wids[$pidId])){
				 
			}else{
				continue;
				 //var_dump([$p]);	
				
			}
			$this->widgets[$pidId]["ViewsTtm"]=$p["views"];
			$this->widgets[$pidId]["clicksTtm"]=$p["clicks"];
			$this->widgets[$pidId]["summaTtm"]=$p["summa"];
			#print $p["summa"]." ->> \n";

		 
		}
		$sql="select day ,id_widget
		,sum(price) as price
		,sum(client_price) as client_price
		,count(*) as cnt 
		from all_clicks where day= '$date'
		and id_type=1
		group by day ,id_widget
		";
		$pages= \DB::connection("pgstatistic_teaser")->getPdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($pages as $p){
			if(isset($this->widgets[$p["id_widget"]]["summaTtm"])){
				$this->widgets[$p["id_widget"]]["summaTtm"]+=$p["price"];
				//echo $this->widgets[$p["id_widget"]]["summaTtm"]." / ".$p["price"]."\n";
				$this->widgets[$p["id_widget"]]["clicksTtm"]+=$p["cnt"];
				
			}else{
				$this->widgets[$p["id_widget"]]["summaTtm"]=$p["price"];
				$this->widgets[$p["id_widget"]]["clicksTtm"]=$p["cnt"];
				//var_dump($p);
				//$this->widgets[$p["id_widget"]]["summaTtm"]=$p["price"];
			}
			
		}
		//exit();
		
	}	
	private function getTopYandex($date){
return;
		$pgpdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select * from advert_stat_yandexclicks where day= '$date'";
			#var_dump($sql);
		$states= $pgpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($states as $stat){
		#var_dump($stat);
			if(isset($this->clids[$stat["clid"]])){
			
			$summa =array_sum($this->clids[$stat["clid"]]);
			if($summa){
             
			    foreach($this->clids[$stat["clid"]] as $key => $val){
					
					$x=$val*100/$summa;
					$finalSumma=$stat["summa"]/100*$x;
					$finaClicks=round($stat["clicks"]/100*$x);
					
					if(!isset($this->wids[$key])){
					var_dump(["нечем порадовать",$key]);
					continue;
					}
					$this->widgets[$key]["summaYa"]=$finalSumma;
   				    $this->widgets[$key]["clicksYa"]=$finaClicks;
					print $key." >><< >><< ".$stat["clid"]." $finaClicks ".$finalSumma." \n";
                   }
			    }
			  }
			}
	}
	private function getTopAdvert($date){
return;
$url="http://service.topadvert.ru/stat_external_pin?feed_id=13910&access_key=3d00ab379ea4b003e322fc3a5e7d4591&date_min=".$date."&date_max=".$date."";   


#var_dump($url); die();
$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec($ch); 
curl_close($ch); 
$pinz=[];
$xml = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
// you can use simplexml_load_file to load a file insted of string
$json = json_encode($xml);

if(!$json) {
	var_dump("ttq1"); exit();
}
$array = json_decode($json,TRUE);
if(!$array)
	{
	var_dump("ttq2"); exit();
}
  $itembroke=[];
foreach($array["item"] as $item){
if(is_array($item["pin"])){
var_dump($item);
continue;
}
           if(preg_match('/^([\d]+)\-[\d]+$/',$item["pin"],$m)){

                  $item["pin"]=$m[1];

#              continue;
           }

	   if(is_array($item["pin"])){
		var_dump($item["pin"]);
		   continue;
	   }
	   switch($item["pin"]){
		   case 1654:
		   case 1648:
		   if(!isset($itembroke[$item["pin"]]))
			$itembroke[$item["pin"]]=["clicks"=>0,"summa"=>0];
			
		    $itembroke[$item["pin"]]["clicks"]++;
			$itembroke[$item["pin"]]["summa"]+=$item["money"];
		   continue 2;
		   break;
		   
	   }

	   $item["pin"]=trim($item["pin"]);
	   if($item["pin"]=="572ba8d726ed1_1"){
		  
		       #continue;          
			$item["pin"]=955;
			

		}
             if($item["pin"]=="661"){
                var_dump($item);

                                        $item["pin"]=955;
             }
		if($item["pin"]=="819"){
			$this->pids[$item["pin"]]=$item["pin"];
			var_dump($this->widgets[$item["pin"]]);
        print $item["pin"];
		print  "\n";
		}
		if(!isset($this->pids[$item["pin"]])){
			
			if($item["pin"]=="661"){

                        }else{
			print $item["pin"]." голыдьба\n";
                                       var_dump($item) ;
			continue;
                        }
		}

		
		$widId=$this->pids[$item["pin"]];

		if($widId==731){

		#echo $widId."-> ".$item["pin"]."\n";
                }
		if(!isset($this->widgets[$widId]["summaJT"]))
			  $this->widgets[$widId]["summaJT"]=0;
		  if(!isset($this->widgets[$widId]["clicksJT"]))
		      $this->widgets[$widId]["clicksJT"]=0;
		  
		      $this->widgets[$widId]["clicksJT"]++;
		      $this->widgets[$widId]["summaJT"]+=$item["money"];
	    }
		$sql="select * from new_tmp_top_summary
        where day='$date'";
		$dima=\DB::connection("pgstatistic_new")->select($sql);
		foreach($dima as $d){
			if(!isset($this->widgets[$d->id_widget]["viewsJT"]))
				$this->widgets[$d->id_widget]["viewsJT"]=0;
			    $this->widgets[$d->id_widget]["viewsJT"]+=$d->views;
				
					 if(isset($itembroke[$d->id_widget])){
					 if(!isset($this->widgets[$d->id_widget]["clicksJT"]))
				     $this->widgets[$d->id_widget]["clicksJT"]=0;
			         $this->widgets[$d->id_widget]["clicksJT"]+=$itembroke[$d->id_widget]["clicks"];		
                     if(!isset($this->widgets[$d->id_widget]["summaJT"]))
				     $this->widgets[$d->id_widget]["summaJT"]=0;					 
				     $this->widgets[$d->id_widget]["summaJT"]+=$itembroke[$d->id_widget]["summa"];		
						 
					
					 }
		}
		$sql="select * from new_tmp_my_summary
        where day='$date'";
		$dima=\DB::connection("pgstatistic_new")->select($sql); 
		foreach($dima as $d){
				if(!isset($this->widgets[$d->id_widget]["viewsJT"]))
				$this->widgets[$d->id_widget]["viewsJT"]=0;
			    $this->widgets[$d->id_widget]["viewsJT"]+=$d->views;
				if(!isset($this->widgets[$d->id_widget]["clicksJT"]))
				$this->widgets[$d->id_widget]["clicksJT"]=0;
			    $this->widgets[$d->id_widget]["clicksJT"]+=$d->clicks;

			//if($this->widgets[$d["id_widget"]]["clicksJT"]){
				
			//}
			//var_dump($d);
		}
#		var_dump($this->widgets[731]); die(); 
		//die();
	}
 public function RegStat($date){	
 $pgpdonew = \DB::connection("pgstatistic_new")->getPdo();
 $sql="
 insert into widget_statistic_day(
    day,
    widget_id,
    server_id,
    views,
    clicks,
    summa,
    oplata,
    myclicks
  )
  select ?,?,?,?,?,?,?,?,?,?
  WHERE NOT EXISTS (SELECT 1 FROM widget_statistic_day WHERE day=? and server_id=? and widget_id=?)
  ";
 $this->insertIntSumNew= $pgpdonew->prepare($sql);
 #var_dump($this->insertIntSumNew);
 $pgpdo = \DB::connection("pgstatistic")->getPdo();
 
 
 
	  
$sql="insert into wid_summa (
    pid,
    day,
	ts_views ,
    ts_clicks ,
    ts_summa,
	na_views,
	na_clicks,
    na_summa,
	kokos_views,
	kokos_clicks,
	kokos_summa,
	wid
)
select ?,?,?,?,?,?,?,?,?,?,?,?
WHERE NOT EXISTS (SELECT 1 FROM wid_summa WHERE pid=? and day=? and wid=?)
";
$this->insertIntSum= $pgpdo->prepare($sql);
	$sql="update wid_summa 
set 
	ts_views =?,
    ts_clicks =?,
    ts_summa=?,
	na_views=?,
	na_clicks=?,
    na_summa=?,
	kokos_views=?,
	kokos_clicks=?,
	kokos_summa=?,
	wid=?
 WHERE pid=? and day=? and wid=?
";
$this->updateIntSum = $pgpdo->prepare($sql);
#if(!isset($this->widgets[716]))
#$this->widgets[716]=[];
 foreach($this->widgets as $z=>$k){
	 $wid=$pid=$z;

	 if(!$pid){

	 }
	 $views=0;
			$cnt=0;
			$sum=0;
			$ycnt=0;
			$ysum=0;
			$yviews=0;
			$tcnt=0;
			$tsum=0;
			$tviews=0;
			$nacnt=0;
			$nasum=0;
			$naviews=0;
			$kviews=0;
			$kcnt=0;
			$ksum=0;
			if(isset($k["showNa"]))
			$naviews=$k["showNa"];
			if(isset($k["clicksNa"]))
			$nacnt=$k["clicksNa"];
		    if(isset($k["summaNa"]))
			$nasum=$k["summaNa"];	
			
			
			if(isset($k["showYa"]))
			$yviews=$k["showYa"];
			if(isset($k["clicksYa"]))
			 $ycnt=$k["clicksYa"];
		    if(isset($k["summaYa"]))
			$ysum=$k["summaYa"];
		    if(isset($k["viewsJT"]))
			$views=$k["viewsJT"];
		    if(isset($k["summaJT"]))
			$sum=$k["summaJT"];
		    if(isset($k["clicksJT"]))
			$cnt=$k["clicksJT"];
			if(isset($k["ViewsTtm"]))
			 $tviews=$k["ViewsTtm"];
             if(isset($k["clicksTtm"]))
             $tcnt=$k["clicksTtm"];
             if(isset($k["summaTtm"]))
		     $tsum=$k["summaTtm"];
			 if(isset($k["showTeaserNet"])){
			  $tviews+=$k["showTeaserNet"];
			  //var_dump($k);
			 }
			 if(isset($k["clicksTeaser"])){
			  $tcnt+=$k["clicksTeaser"];
			  //var_dump($k);
			 }
			 if(isset($k["summaTeasernet"])){
			  $tsum+=$k["summaTeasernet"];
			  //var_dump($k);
			 }
			 if(isset($k["showkokos"])){
			  $kviews+=$k["showkokos"];
			  //var_dump($k);
			 }
			  if(isset($k["clickskokos"])){
			  $kcnt+=$k["clickskokos"];
			  //var_dump($k);
			 }
			  if(isset($k["summakokos"])){
			  $ksum+=$k["summakokos"];
			  //var_dump($k);
			 }
			 
			  if(isset($k["clicksMein"])){
			  $cnt+=$k["clicksMein"];
			  //var_dump($k);
			 }
			 if(isset($k["summaMein"])){
			  $sum+=$k["summaMein"];
			  //var_dump($k);
			 }
			 
			 if($date=="2018-06-19"){
                                    if($pid==661){
				 $views+=102000;
#				 $cnt+=1700;
#				 $sum+=5100;
				   }
                                 if($pid==634){
				 $views=3242;
				 $cnt=30;
				 $sum=53;
				   }
                                 if($pid==731){
				 $views=2135;
				 $cnt+=10;
				 $sum+=21;
				   }
                                 if($pid==757){
				 $views=3014;
				 $cnt+=3;
				 $sum+=8;
				   }
                                 if($pid==955){
				 $views=3755;
				 $cnt+=7;
				 $sum+=20;
				   }
                         }
			 if($date=="2018-06-28"){
                         if($pid==661){
				 $views+=4602;
				 $cnt+=190;
				 $sum+=399;
                                 } 


                         }

			 #921 8890 
			 
              //$this->widgets[$kcc["wid"]]["clicksMein"]=$stat["cnt"];
		      //  $this->widgets[$stat["wid"]]["summaMein"]=$stat["sum"];
			  #if($pid==716 && $date=='2018-01-14'){
				#  $views=4140; $cnt=29; $sum=51;
				#  echo $pid." / ".$views."/".$cnt."/".$sum." : ".$yviews."/".$ycnt."/".$ysum." : $tviews/$tcnt/$tsum   : $naviews/$nacnt/$nasum\n"; 
			  #}else {
				#  continue;
			  #}
		     if(in_array($wid,[634,757,731,661])){
#		     if(1==0 && $wid==661){
			echo $pid." / ".$views."/".$cnt."/".$sum." : ".$yviews."/".$ycnt."/".$ysum." : $tviews/$tcnt/$tsum   : $naviews/$nacnt/$nasum\n"; 
                     }
#		}
#              else{	
            $this->updateIntSum->execute([$tviews,$tcnt,$tsum,$naviews,$nacnt,$nasum,$kviews,$kcnt,$ksum,$wid,$pid,$date,$wid]);
			$this->insertIntSum->execute([$pid,$date,$tviews,$tcnt,$tsum,$naviews,$nacnt,$nasum,$kviews,$kcnt,$ksum,$wid,$pid,$date,$wid]);
#               }
		
        }
	}
}
