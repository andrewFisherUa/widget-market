<?php

namespace App\Videosource\Mystat;

use Carbon\Carbon;

class Top
{
    private static $instance=null;
	private $caches=[];
	private $pads=[];
	private static $regions =[];
	private static $shops =[];
	private static $ltds=[];
	private static $cats=[];
	private static $usrs=[];
	private $geoRez=[];
	#private $sumsk=[];
	public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	self::$instance->prepareData();
	}
	return self::$instance;
	}
		public function getGeo($country,$region){
		if(isset($this->geoRez[$country][$region])){
			return $this->geoRez[$country][$region];
		}
		if(!$region || $region=='-'){
			print "$country $region\n";
			$region=$country;
			
		}
		$this->geoSelectPrepareSth->execute([$country,$region]);
		$retz=$this->geoSelectPrepareSth->fetch(\PDO::FETCH_ASSOC);
		if($retz){
			//print "$country - $region \n";
			$this->geoRez[$country][$region]=$retz["id"];
			//var_dump($retz);
		}else{
			#print "$country - $region \n";
			return 0;
		}
        return $this->geoRez[$country][$region];
	}
    public function getData(&$arr){


		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		//var_dump($arr);
		$ip = $arr[1];
#                print $ip." ->\n";
		$region=$arr[3];
		$time = strtotime($arr[0]);
		$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		$ddatetime=date("Y-m-d H:i:s",$dtime);
		$day=date("Y-m-d",$time);
		$country=trim($arr[2]);
		$region=trim($arr[3]);
		
		$day=date("Y-m-d",$time);
		$req=preg_split("/\s+/",$arr[5]);
		if(!$req) return;
		$agent=preg_replace('/\"/','',trim($arr[8]));
	    parse_str($req[1], $dd);
		  if(!$dd || !isset($dd["data"])){
		    return;
	       }	
		$data=json_decode($dd["data"],true);
		if(!$data){
		    return;
	    }
		if(!isset($data["wid"])){
			return ;
		}
		if(!isset($data["fromUrl"])){
			return;
		}
		if(!isset($data["id_product"])){
			return;
		}
		$url=urldecode($data["fromUrl"]);
		if(!$url) return;
		$tp=parse_url($url);
		if(!isset($tp["host"]) || !$tp["host"]) return;
		$domain=$tp["host"];
		$this->saveData($day,$domain,$ip,$data["wid"],$data["id_product"],$url,$datetime);
#		print_r($tp);
#		print_r([$ip,$day,$datetime]);
       
		#print_r($data);
	}
	public function saveData($day,$domain,$ip,$wid,$id_product,$url,$datetime){
		return;
		$ipex=["176.213.140.214"=>1];
		$status=1;
		if(isset($ipex[$ip])) 
		$status=0;	
			
		if(isset($this->caches[$day][$domain][$ip])) $status=0;
		$caches[$day][$domain][$ip]=1;
		$this->selectIntSum->execute([$day,$domain,$ip]);
		$data=$this->selectIntSum->fetch(\PDO::FETCH_ASSOC);
		if($data["cnt"]>1) $status=0;
		$this->caches[$day][$domain][$ip]=1;
		 $price =  $this->iptarget($ip,$id_product);
		 if(!$price) return;
		 $id_geo=$price[0];
		 $pprice=$price[1];
		 $cprice=$price[2];
		 $shop_id=$price[3];
		 $id_tree=$price[4];
		 $status=0;
		 print_r([$day,$ip,$data["cnt"],$price,$status]);

	     
		$this->insertIntSum->execute([$day,$domain,$ip,$wid,$id_product,$url,$datetime,$id_geo,$pprice,$cprice,$shop_id,$status,$id_tree]);
	}
	public function prepareData(){
		
		$sql="select id,code,name from iso2_tree where country=? and code = ?";
	   $vcpdo=\DB::connection("advertise")->getPdo();
	$this->geoSelectPrepareSth=$vcpdo->prepare($sql);
		return;
	
	}	
	function iptarget($ip,$id_product){

	}
	private function getUserName($id){
		#var_dump($id);
		if(!$id) return "другой";
		if(isset(self::$usrs[$id])) return self::$usrs[$id];
		
		$this->usrsSth->execute([$id]);
		   
			$r=$this->usrsSth->fetch(\PDO::FETCH_ASSOC);
			if(!$r){
				self::$usrs[$id]="другое";
			}
			else{
	
				self::$usrs[$id]=$r["name"];
		
				
			}
		return self::$usrs[$id];
		
	}	
	private function getCategoryName($id){
		
		#var_dump($id);
		if(!$id) return "другое";
		if(isset(self::$cats[$id])) return self::$cats[$id];
		
			$this->rubrikSth->execute([$id]);
		   
			$r=$this->rubrikSth->fetch(\PDO::FETCH_ASSOC);
			if(!$r){
				self::$cats[$id]="другое";
			}
			else{
	
				self::$cats[$id]=$r["name"];
		
				
			}
		return self::$cats[$id];
		
		
		
			
			if(isset(self::$cats[$id])) return self::$cats[$id];
		
			$this->ycatSth->execute([$id]);
		   
			$r=$this->ycatSth->fetch(\PDO::FETCH_ASSOC);
			if(!$r){
				self::$cats[$id]="неизвестно";
			}
			else{
	
				self::$cats[$id]=$r["uniq_name"];
		
				
			}
				return self::$cats[$id];
		}

	private function getShopname($id){
			
			if(isset(self::$shops[$id])) return self::$shops[$id];
		
			$this->shopSth->execute([$id]);
		   
			$r=$this->shopSth->fetch(\PDO::FETCH_ASSOC);
			if(!$r){
				self::$shops[$id]="неизвестно";
			}
			else{
	
				self::$shops[$id]=$r["name"];
		
				
			}
				return self::$shops[$id];
		}
		private function getLtdname($id){
				if(isset(self::$ltds[$id])) return self::$ltds[$id];
			$this->padSth->execute([$id]);
			$r=$this->padSth->fetch(\PDO::FETCH_ASSOC);
			
			if(!$r){
				self::$ltds[$id]="неизвестно";
			}
			else{
				
				self::$ltds[$id]=$r["ltd"];
				
			}
		return self::$ltds[$id];
			
		}
	private function getRegionname($id){

			if(isset(self::$regions[$id])) return self::$regions[$id];
			$this->regionsSth->execute([$id]);
			$r=$this->regionsSth->fetch(\PDO::FETCH_ASSOC);
		
			if(!$r){
				self::$regions[$id]="неизвестно";
			}
			else{
				
				self::$regions[$id]=$r["name"];
				
			}
				return self::$regions[$id];
				
			
		}
   public function collectIMSData($date=null){
	   	  #$ssu=new \App\Models\Advertises\Payment();
	      #$ssu->correctBalance(585,1);	
		  #$date='2018-04-02';
	    $pgprecluck=\DB::connection()->getPdo();
		$pgpcluck=\DB::connection("cluck")->getPdo();
		$pgadv=\DB::connection("advertise")->getPdo();
		$topAdvertises=[];
		$shopClicks=[];
	    if(!$date){
	    $date=date("Y-m-d");
	    $today = Carbon::now();
		
		if($today->hour<2){
		$date = $today->yesterday()->format('Y-m-d');
		$obnul = $today->format('Y-m-d');
		
		$sql=" select id,day_clicks from advertises where status <>2 "; 
		$advs=\DB::connection("advertise")->select($sql);
		foreach($advs as $add){
			$topAdvertises[$add->id]=["add_clicks"=>$add->day_clicks,"d"=>0];
			//var_dump($ad);
			
		}
		 #die();
	    }else{
			 
		}
		}
	
		
		$sql="select * from iso2_tree where id=?";
		$this->regionsSth=$pgadv->prepare($sql);
		$sql="select * from advertises where id=?";
		$this->shopSth=$pgadv->prepare($sql);
		$sql="select * from widgets where pad=? and ltd is not null";
		$this->padSth=$pgprecluck->prepare($sql);
		$sql="select * from yandex_categories where id=? ";
		$this->ycatSth=$pgpcluck->prepare($sql);
		$pgpdo=\DB::connection("pgstatistic")->getPdo();
	 
	    $sql="select * from myads_rubriks where id=? ";
	    $this->rubrikSth=$pgadv->prepare($sql);
         $sql="select * from users where id=? ";		
       $this->usrsSth=$pgprecluck->prepare($sql);


		$sql="update myadvert_summa_clicks 

    set views =?,
    clicks =?,
    summa =?,
    domain =?,
    region =?,
    category =?,
    shop =? 
WHERE 
    day=? and
    shop_id=? and
    pad=? and
    id_tree=? and
    id_region=?

";
		$sthUpdateAll=$pgpdo->prepare($sql);	
		
		$sql="insert into myadvert_summa_clicks (
    day,
    shop_id ,
    pad ,
    id_tree ,
    id_region ,
    views ,
    clicks ,
    summa ,
    domain ,
    region ,
    category ,
    shop 
) select ?,?,?,?,?,?,?,?,?,?,?,?
WHERE NOT EXISTS (SELECT 1 FROM  myadvert_summa_clicks WHERE 
    day=? and
    shop_id=? and
    pad=? and
    id_tree=? and
    id_region=?
)
";
$sthInsertAll=$pgpdo->prepare($sql);	
		$sql="select day,geo_id,shop_id,pad,id_rubrika as id_tree
		,count(*) as views
		from myadvert_views 
		where day between '$date' and '$date'
		and shop_id>0 and found >0
		group by day,geo_id,shop_id,pad,id_rubrika
		";
		$poloz=[];
		$views=\DB::connection("pgstatistic")->select($sql);
		foreach($views as $v){

			$poloz[$v->day][$v->geo_id][$v->shop_id][$v->pad][$v->id_tree]["clicks"]=0;
			$poloz[$v->day][$v->geo_id][$v->shop_id][$v->pad][$v->id_tree]["summa"]=0;
			$poloz[$v->day][$v->geo_id][$v->shop_id][$v->pad][$v->id_tree]["views"]=$v->views;
		}
		$sql="select day,id_geo,shop_id,pad,id_rubrika as id_tree
		,sum(price) as summa
		,sum(client_price) as client_summa
		,count(*) as clicks 
		from myadvert_clicks where day between '$date' and '$date' and status=1
				group by day,id_geo,shop_id,pad,id_rubrika
		";
	
		$views=\DB::connection("pgstatistic")->select($sql);
		foreach($views as $v){
			if($v->client_summa){
			//var_dump($v->shop_id);
			}
			$poloz[$v->day][$v->id_geo][$v->shop_id][$v->pad][$v->id_tree]["clicks"]=$v->clicks;
			$poloz[$v->day][$v->id_geo][$v->shop_id][$v->pad][$v->id_tree]["summa"]=$v->summa;
			$poloz[$v->day][$v->id_geo][$v->shop_id][$v->pad][$v->id_tree]["client_summa"]=$v->client_summa;
			
		}
		$sql="
		select day
,id_server as pad
,'-1' as id_tree
,id_company as shop_id
,country
,region
,sum(price) as summa
,sum(client_price) as client_summa
,count(*) as clicks 
from all_clicks
where day between '$date' and '$date'
and id_type=1
group by day,id_server,id_category,id_company
,country
,region
";
#$poloz=[];
$contrday=date("Y-m-d");
	
$corpat=[];
//print $sql; die();
	$rviews=\DB::connection("pgstatistic_teaser")->select($sql);		
	#$rviews=[];
	foreach($rviews as $rvw){
		$id_geo_rez=$this->getGeo($rvw->country,$rvw->region);
		if($contrday==$rvw->day){
		if(!isset($corpat[$rvw->shop_id]))
			$corpat[$rvw->shop_id]=0;
			$corpat[$rvw->shop_id]+=$rvw->clicks;
        }  
		if(1==1){
		if(!isset($poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["clicks"])){
			
			$poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["clicks"]=0;
			//var_dump($poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad]);
		}else{
			
			//die();
		}
		if($rvw->clicks){
			
		}
		    $poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["clicks"]+=$rvw->clicks;
		if(!isset($poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["summa"]))
			$poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["summa"]=0;
		    $poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["summa"]+=$rvw->summa;		
		if(!isset($poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["client_summa"]))
			$poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["client_summa"]=0;
		    $poloz[$rvw->day][$id_geo_rez][$rvw->shop_id][$rvw->pad][$rvw->id_tree]["client_summa"]+=$rvw->client_summa;			
		    //$poloz[$v->day][$id_geo_rez][$v->shop_id][$v->pad][$v->id_tree]["clicks"]=$v->clicks;
			//$poloz[$v->day][$id_geo_rez][$v->shop_id][$v->pad][$v->id_tree]["summa"]=$v->summa;
			//$poloz[$v->day][$id_geo_rez][$v->shop_id][$v->pad][$v->id_tree]["client_summa"]=$v->client_summa;
	}else{
		//var_dump($rvw);
		//var_dump($rvw->clicks);	
	}
	    //var_dump($id_geo_rez);	
	}
	//var_dump($poloz);	
	$sql="update advertises set day_clicks=? where id=?";
	$miniMik=$pgadv->prepare($sql);
	foreach($corpat as $shop_id=>$va){
		$miniMik->execute([$va,$shop_id]);
	}
	
		#foreach($poloz as $day=>$regions){
			foreach($poloz as $day=>$regions){
				foreach($regions as $region=>$shops){
					$regionName=$this->getRegionName($region);
					foreach($shops as $shop=>$pads){
						
						$shopName=$this->getShopName($shop);
						#print $shop."/".$shopName."\n";
						foreach($pads as $pad=>$categories){
							
							$domain=$this->getLtdName($pad);
							foreach($categories as $category=>$arr){
								if($shop==39){
								//var_dump($category);
							     }
								 $categoryName=$this->getCategoryName($category);
								 $view=isset($arr["views"])?$arr["views"]:1;
								 $clicks = isset($arr["clicks"])?$arr["clicks"]:0;
								 $summa = $arr["summa"];
								 $sthUpdateAll->execute([
								 $view,$clicks,$summa,$domain,$regionName,$categoryName,$shopName,$day,$shop,$pad,$category,$region
								 ]);
								 $sthInsertAll->execute([
								 $day,$shop,$pad,$category,$region,$view,$clicks,$summa,$domain,$regionName,$categoryName,$shopName,$day,$shop,$pad,$category,$region
								 ]);
								if($shop==39 && $clicks){
								 print "$clicks / $domain / $region / $regionName / $category / $categoryName\n";									
 //print "$day,$shop,$pad,$category,$region,$view,$clicks,$summa,$domain,$regionName,$categoryName,$shopName,$day,$shop,$pad,$category,$region\n";									
								}
					           
						    }
						}
					}
					
				}
			}
		#}
	$sthInsertAl=null;
	$sthUpdateAll=null;	
		$sql="update myadvert_stat_pages
		set views=?,
		clicks=?,
		ipscount=?,
		first_found=?,
		request=?,
		jns=?,
		shop=?, 
		domain=?
        WHERE 
	    day=? and
		pad =? and
		hash=? and
		shop_id=? and
	    url=? 
        ";
		$sthUpdateAll=$pgpdo->prepare($sql);		
		
		$sql="insert into myadvert_stat_pages
		(
		day,
		pad,
		hash,
		shop_id,
		url,
		shop,
		domain,
		views,
		clicks,
		ipscount,
		first_found,
		request,
		jns
		)
    select ?,?,?,?,?,?,?,?,?,?,?,?,?
     WHERE NOT EXISTS (SELECT 1 FROM  myadvert_stat_pages WHERE 
	    day=? and
		pad =? and
		hash=? and
		shop_id=? and
		url=? 
	 )
        ";
		$sthInsertAll=$pgpdo->prepare($sql);	
		
        $sql="select url,pad,hash,day,shop_id,count(*) as shows
		from myadvert_views 
		where day between '$date' and '$date'
		and shop_id>0 and found >0
		group by url,pad,hash,day,shop_id
		order by count(*)
		";
		$sql="select t.url,t.pad,t.hash,t.day,t.shop_id,t.shows
,coalesce(tp.request,'') as request
,coalesce(tp.first_found,'') as first_found
,coalesce(tp.jns,'') as jns
,tp.last_visit
,count(*)
from(
select url,pad,hash,day,shop_id,count(*) as shows
		from myadvert_views 
		where day between '$date' and '$date'
		and shop_id>0 and found >0
		group by url,pad,hash,day,shop_id
		
) t
left join myadvert_pad_request tp
on tp.day=t.day and tp.pad=t.pad and tp.hash=t.hash and tp.request<>''
group by t.url,t.pad,t.hash,t.day,t.shop_id,t.shows,tp.request,tp.first_found,tp.jns,tp.last_visit
order by count(*) desc,t.shows";
		#echo $sql;
		
		$poloz=[];
		$pages=\DB::connection("pgstatistic")->select($sql);		
		foreach($pages as $page){
			//var_dump($page->shop_id);
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["first_found"]=$page->first_found;
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["request"]=$page->request;
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["jns"]=$page->jns;
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["views"]=$page->shows;
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["clicks"]=0;
			#$poloz[$page->day][$page->pad][$page->hash][$page->shop_id]["url"]=$page->url;
		}
		$sql="select url,pad,hash,day,shop_id,count(*) as clicks
		,count(distinct(ip)) as ips
		from myadvert_clicks
		where day between '$date' and '$date' and status=1
		group by url,pad,hash,day,shop_id
		";
		
		#$poloz=[];
		$pages=\DB::connection("pgstatistic")->select($sql);	
		foreach($pages as $page){ 
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["clicks"]=$page->clicks;
			$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["ips"]=$page->ips;
			
		}
$sql="select day
,id_server as pad
,id_company as shop_id
,hash
,url
,count(distinct(ip)) as ips
,count(*) as clicks 
from all_clicks
where day between '$date' and '$date'
and id_type=1
group by day,id_server
,id_company
,hash
,url";	 
	
 

$gpages=\DB::connection("pgstatistic_teaser")->select($sql);		
		foreach($gpages as $gpage){ 	
				#$id_geo_rez=$this->getGeo($gpage->country,$gpage->region);

				
				if(!isset($poloz[$gpage->day][$gpage->pad][$gpage->hash][$gpage->shop_id][$gpage->url]["clicks"]))
					$poloz[$gpage->day][$gpage->pad][$gpage->hash][$gpage->shop_id][$gpage->url]["clicks"]=0;
					$poloz[$gpage->day][$gpage->pad][$gpage->hash][$gpage->shop_id][$gpage->url]["clicks"]+=$gpage->clicks;
				if(!isset($poloz[$gpage->day][$gpage->pad][$gpage->hash][$gpage->shop_id][$gpage->url]["ips"]))
					$poloz[$gpage->day][$gpage->pad][$gpage->hash][$gpage->shop_id][$gpage->url]["ips"]=0;
					$poloz[$gpage->day][$gpage->pad][$gpage->hash][$gpage->shop_id][$gpage->url]["ips"]+=$gpage->ips;
				}

		
		#var_dump($poloz); die();
		foreach($poloz as $day=>$pads){
			foreach($pads as $pad=>$hashes){
				$domain=$this->getLtdName($pad);
				foreach($hashes as $hash=>$shops){
					foreach($shops as $shop=>$urls){
						$shopName=$this->getShopName($shop);
						foreach($urls as $url=>$da){
                                                 $url=mb_substr($url,0,1000);
						$views=isset($da["views"])?$da["views"]:1;
						$clicks=isset($da["clicks"])?$da["clicks"]:0;
						$ips=isset($da["ips"])?$da["ips"]:0;
					    $first_found=isset($da["first_found"])?$da["first_found"]:"";
						$request=isset($da["request"])?$da["request"]:"";
						$jns=isset($da["jns"])?$da["jns"]:"";
						#var_dump($first_found);
						#$url=$da["url"];
						//if($clicks && $shop==9){
							#print "$day,$pad,$hash,$shop,$url,$views,$clicks,$shopName,$domain,$ips\n";
							#print "$clicks,$url\n";
						//}
						
						#print "$day,$pad,$hash,$shop,$url,$views,$clicks,$shopName,$domain,$ips\n";
					    $sthUpdateAll->execute([
						$views,$clicks,$ips
						,$first_found
						,$request
						,$jns,$shopName,$domain,$day,$pad,$hash,$shop,$url
						]);
					    $sthInsertAll->execute([
						$day,$pad,$hash,$shop,$url,$shopName,$domain,$views,$clicks,$ips
						,$first_found
						,$request
						,$jns,$day,$pad,$hash,$shop,$url
						]);
						}
						
					}
					
				}
				
			}
			
		}
		$sql="select page_key
		,id_server
		,id_widget
		,date
		,count(*) as cnt
		from advert_stat_clicks where date  between '$date' and '$date'  
		and ipshow<>'176.213.140.214' and ipclick<>'176.213.140.214'
		group by 
		page_key
		,id_server
		,id_widget
		,date
		";
		#var_dump($sql); die();
		$dtype=[];
		$adcs=\DB::connection("pgstatistic")->select($sql);
		foreach($adcs as $adc){
			$dtype[$adc->date][$adc->page_key][$adc->id_server][$adc->id_widget]=$adc->cnt;
			#var_dump($adc);
		}
	$sql="
	update  advert_stat_relevance 

    set url=?,
    api_url=?,
    request=?,
    requested=?,
    found=?,
    all_my_clicks=?
	WHERE 
	    day=? and
	    pad =? and
		pid =? and
		id_geo=? and 
		hash=? 
	";
		$sthUpdateAi=$pgpdo->prepare($sql);
		$sql="
	insert into  advert_stat_relevance (
	day,
    pad,
	pid,
    id_geo,
    hash,
    url,
    api_url,
    request,
    requested,
    found,
	all_my_clicks
    )
	select ?,?,?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM  advert_stat_relevance WHERE 
	    day=? and
	    pad =? and
		pid =? and
		id_geo=? and 
		hash=? 
	    )
	";
		$sthInsertAi=$pgpdo->prepare($sql);
		
		$sql="select 
		day
		,pad
		,pid
		,id_geo
		,hash
		,url
		,api_url
		,request
		,sum(is_found) as is_found
		,count(*) as requested
		from  advert_stat_tmp_relevance 
		where day between '$date' and '$date' 
		group by 
		day
		,pad
		,pid
		,id_geo
		,hash
		,url
		,api_url
		,request
		";
	
		$kiviews=\DB::connection("pgstatistic")->select($sql);
		
		foreach($kiviews as $v){
		$auarClicks=0;
            if(isset($dtype[$v->day][$v->hash][$v->pad][$v->pid])){
				#$auarClicks=$dtype[$v->day][$v->hash][$v->pad][$v->pid];
				#if($v->pid==816)
				#var_dump(["всё уже стоп",$v]);// die();
			}
		
			$k=[
			$v->day,
			$v->pad,
			$v->pid,
			$v->id_geo,
			$v->hash,
			$v->url,
			$v->api_url,
			$v->request,
			$v->requested,
			$v->is_found,
			$auarClicks,
			$v->day,
			$v->pad,
			$v->pid,
			$v->id_geo,
			$v->hash
			];
			$sthUpdateAi->execute([$v->url,
			$v->api_url,
			$v->request,
			$v->requested,
			$v->is_found,
			$auarClicks,
			$v->day,
			$v->pad,
			$v->pid,
			$v->id_geo,
			$v->hash]);
			
			$sthInsertAi->execute($k);
			 
			
		
			#var_dump(["запр",$v]);
		}
		
		if($topAdvertises){
		$sql="update advertises set day_clicks =? where id=? ";	
		$sthiii=$pgadv->prepare($sql);
		$sql="select shop_id,sum(price) as summa,count(*) as clicks
		from myadvert_clicks where day between '$obnul' and '$obnul' and status =1 
		group by shop_id 
		";
		#var_dump($sql);
		$kiliks=\DB::connection("pgstatistic")->select($sql);
		foreach($kiliks as $v){
			if($v->clicks && isset($topAdvertises[$v->shop_id])){
			    $topAdvertises[$v->shop_id]["d"]=$v->clicks;
				
			}
		}	
		foreach($topAdvertises as $id_shop=>$ark){
			$sthiii->execute([$ark["d"],$id_shop]);
			}
		}
		
		#$sql="select * from  cluck_widget order by datetime desc limit 1";
		#$views=\DB::connection("pgstatistic")->select($sql);
		#var_dump($views);
		
		
		
		$this->collectTSOData($date);
		$myhour=preg_replace('/^0/','',date("H"));
		if($myhour==11){
			
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete  from myadvert_views where day <'$myday'";
		$pgpdo->exec($sql);
		print "deleted myadvert_views untill $myday !!!!\n";
		
		$sql="delete  from advert_stat_tmp_relevance where day <'$myday'";
		$pgpdo->exec($sql);
		print " deleted  advert_stat_tmp_relevance  untill $myday !!!!\n";
		
        $sql="delete  from myadvert_requests where day <'$myday'";
		$pgpdo->exec($sql);
		print " deleted  myadvert_requests  untill $myday !!!!\n";
		
		}
	$this->closeDay($day);
   }
   
    public function collectTSOData($date=null){
		#$poloz=[];
		//$pages=\DB::connection("pgstatistic")->select($sql);	
		//foreach($pages as $page){ 
		//	$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["clicks"]=$page->clicks;
		//	$poloz[$page->day][$page->pad][$page->hash][$page->shop_id][$page->url]["ips"]=$page->ips;
		//	
		//}
	}	
	public function closeDay($date){
	$pdostat=\DB::connection("advertise")->getPdo();
	$sql="
    update advertise_history 
    set user_name=?,
    balance=?,
	expense=?,
	views=?,
	clicks=?,
	offers_cnt=?,
	site_permissions=?,
	status=?,
	offer_limit_click=?,
	shop_name=?
	WHERE day=? and id_company=?
	";
    $sthUpd=$pdostat->prepare($sql);	

	$sql="
    insert into advertise_history (
    id_company,
    user_id,
    user_name,
    balance,
	expense,
    day,
	views,
	clicks,
	offers_cnt,
	site_permissions,
	status,
	offer_limit_click,
	shop_name
    )
	select ?,?,?,?,?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM  advertise_history WHERE day=? and id_company=?)
	";
    $sth=$pdostat->prepare($sql);	
	$rekl=[];
	$usershop=[];
	$sql="select * from advertises
        ";
	$data=\DB::connection("advertise")->select($sql);
	foreach($data as $d){
		$usershop[$d->user_id][$d->id]=1;
		$rekl[$d->id]=["balance"=>0,"expense"=>0,"user_id"=>$d->user_id
		,"views"=>0,"clicks"=>0,"offers_cnt"=>0
		,"site_permissions"=>$d->site_permissions,"status"=>$d->status
		,"offer_limit_click"=>$d->limit_clicks
		];
		#var_dump($d);
	}
	#return;
		$sql="select
                user_id
                ,sum(summa) as summa
            from payment_history
			where day <='$date'
            group by
            user_id";
	$data=\DB::connection("advertise")->select($sql);
	foreach($data as $d){

	        if(isset($usershop[$d->user_id])){

		foreach($usershop[$d->user_id] as $id_company=>$a){
			 if(isset($rekl[$id_company])){
				 $rekl[$id_company]["balance"]=$d->summa;
			 }else{
		
			 }
		}
                }else{
                var_dump(['igogo',$d->user_id]);
                }
		
	}
	if(!$rekl) return;
	$sql="select shop_id,
    sum(price) as price
    from myadvert_clicks
    where day='$date'
    group by shop_id
	";
	$data=\DB::connection("pgstatistic")->select($sql);
	foreach($data as $d){
		    if(isset($rekl[$d->shop_id])){
				 $rekl[$d->shop_id]["expense"]-=$d->price;
			 }else{
		     var_dump($d);		 
			 }
	}
	$sql="select id_company,
    sum(price) as price
    from all_clicks
    where day='$date' and id_type=1
    group by id_company 
	";
	$data=\DB::connection("pgstatistic_teaser")->select($sql);
	foreach($data as $d){
		    if(isset($rekl[$d->id_company])){
				    $rekl[$d->id_company]["expense"]-=$d->price;
			 }else{
		     
			 }
	}
	$sql="
	select 
t1.shop_id
,t1.shop
,sum(t1.clicks) as clicks ,sum(t1.views) as views 
,sum(t1.summa) as summa 
,0 as inv 
from myadvert_summa_clicks t1
where t1.day ='$date' and t1.shop_id in (".implode(",",array_keys($rekl)).")
group by 
t1.shop_id
,t1.shop
	";
	$data=\DB::connection("pgstatistic")->select($sql);
	foreach($data as $d){
		 
				    $rekl[$d->shop_id]["views"]=$d->views;
			        $rekl[$d->shop_id]["clicks"]=$d->clicks;
		   
	}	
$sql="
select 
t1.id_company
,sum(t1.price) as price
,count(*) as clicks
from all_clicks t1
where t1.day ='$date' 
and id_type=1
group by 
t1.id_company
";
$data=\DB::connection("pgstatistic_teaser")->select($sql);
	foreach($data as $d){
		//$rekl[$d->id_company]["clicks"]+=$d->clicks;
	}	
	
	$sql="select * from ads where advert_id>0";
	$data=\DB::connection("pg_product")->select($sql);
	foreach($data as $d){
		    if(isset($rekl[$d->advert_id])){
				$rekl[$d->advert_id]["offers_cnt"]=$d->offers_cnt;
			 }else{
		   
			 } 
				    
		   
	}	
	
	foreach($rekl as $id_company=>$bal){
    $shopName=$this->getShopName($id_company);  
	$userName=$this->getUserName($bal["user_id"]);
	print $userName."\n";
	$ret=[$userName,
	$bal["balance"],
	$bal["expense"],
	$bal["views"],
	$bal["clicks"],
	$bal["offers_cnt"],
	$bal["site_permissions"],
	$bal["status"],
	$bal["offer_limit_click"],
	$shopName,
	$date,
	$id_company
	];
    $sthUpd->execute($ret);
	$ret=[$id_company,
	$bal["user_id"],
	$userName,
	$bal["balance"],
	$bal["expense"],
	$date,
	$bal["views"],
	$bal["clicks"],
	$bal["offers_cnt"],
	$bal["site_permissions"],
	$bal["status"],
	$bal["offer_limit_click"],
	$shopName,
	$date,
	$id_company
	];
	$sth->execute($ret);
	    # if($bal["expense"])
			#var_dump($bal);
	   }
	}

}	