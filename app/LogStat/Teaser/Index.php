<?php
namespace App\LogStat\Teaser;
use App\MPW\Sources\Urlparser;
class Index{
	 private static $instance=null;
	 private $servers=[];
	 private $geoRez=[];
	 
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
			
		$region=$country;
		
		}
			return 0;
		$this->geoSelectPrepareSth->execute([$country,$region]);
		$retz=$this->geoSelectPrepareSth->fetch(\PDO::FETCH_ASSOC);
		if($retz){
			$this->geoRez[$country][$region]=$retz["id"];
		}else{
			return 0;
		}
        return $this->geoRez[$country][$region];
	}
	public function getData(&$arr){
    	$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		$ip = $arr[1];
		$region=$arr[3];
		$time = strtotime($arr[0]);
		$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		$ddatetime=date("Y-m-d H:i:s",$dtime);
		$country=trim($arr[2]);
		$region=trim($arr[3]);
		$id_geo_rez=$this->getGeo($country,$region);
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
			return;
		}
		
		if(!isset($this->servers[$data["wid"]])){
			return;
		}
		if($data["wid"]=="1501"){
		}
		$id_server=$this->servers[$data["wid"]];
		$url=urldecode($data["fromUrl"]);
		$u_query= Urlparser::getQuery($url);
		$url=Urlparser::getFullUrl($data["fromUrl"]);
		$hash=md5($u_query);
		$page_key=$data["page_key"];
//var_dump([$id_server,$url,$page_key,$ip,$country,$region,$ddatetime,0,$data["wid"],0,1]);
		$this->sth->execute([$id_server,$url,$page_key,$ip,$country,$region,$ddatetime,0,$data["wid"],0,1]);
		$founded=0;
		if(isset($data["found"]))
		$founded=count($data["found"]);	
	    $ret=[$day,$datetime,$id_server,$data["wid"],$hash,$url,$country,$region,$founded];
		if($data["wid"]==1596){
			
		}
		#var_dump($ret);
		#print"\n";
		$this->sthAllViews->execute($ret);
		$varCompanies=[];
		if($founded){
			$typses=[];
			foreach($data["found"] as $k=>$via){
			
			$typ=$via[0];
			$id_comnpany=0;
			$id_prod=0;
			$typses[$typ]=1;
			if($typ == 1){
				$id_comnpany=intval(preg_replace('/^[^\d]+/','',$via[1]));
				$id_prod=intval(preg_replace('/^[^\d]+/','',$via[2]));
				$varCompanies[$id_comnpany][$id_prod]=1;
				#print $country." : ".$region.":$id_geo_rez $url\n";
			}else{
			}
			}
		foreach($varCompanies as $id_comnpany=>$aerr){
			$cnt_b=count($aerr)	;
			$geo_id=$id_geo_rez;
			$id_tree=0;
			$id_rubrika=0;
			$regs=[$day,$id_geo_rez,$id_comnpany,$id_server,$data["wid"],$id_tree,$hash,$url,$cnt_b,$datetime,$ip,0];
			$this->sthMyAdvertViews->execute($regs);
			}
		foreach($typses as $tdp=>$o){
		   $bert=$ret;	
           $bert[]=$tdp;
		   $this->sthDetailViews->execute($bert);
		}	
		
		}
		
		
	}
	
	public function getClicks(&$arr){
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		$ip = $arr[1];
		$region=$arr[3];
		$time = strtotime($arr[0]);
		$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		$ddatetime=date("Y-m-d H:i:s",$dtime);
		$country=trim($arr[2]);
		$day=date("Y-m-d",$time);
		$req=preg_split("/\s+/",$arr[5]);
		if(!$req) return;
		
		$agent=preg_replace('/\"/','',trim($arr[8]));
		
		$id_geo_rez=$this->getGeo($country,$region);
	    parse_str($req[1], $dd);
		
		  if(!$dd || !isset($dd["data"])){
		    return;
	       }	
		
		$data=json_decode($dd["data"],true);
		if(!$data){
		    return;
	    }
        if(!isset($data["wid"])){
			return;
		}
		
		if(!isset($this->servers[$data["wid"]])){
			
			return;
		}
		$id_server=$this->servers[$data["wid"]];
		$url=urldecode($data["fromUrl"]);
		$u_query= Urlparser::getQuery($url);
		$hash=md5($u_query);
		$page_key=$data["page_key"];
		$founded=0;
		$pprice=0;
		$cprice=0;
		
		$id_company=0;
		
		if(isset($data["found"]))
		 $founded=count($data["found"]);	
	     $id_type=2;
	     if(isset($data['type']))
	     $id_type=$data['type'];
	      switch($id_type){
			case 1:
			  if(isset($data['id_company'])){
				  $id_company=$data['id_company'];
	  
				  $company=\App\Advertise::find($data['id_company']);
				  
				  if(!$company) continue;
				  if(!$company->myteaser_perclick) continue;
				  	$persent=75;
	                $sql="select t2.dop_status from widgets t1
                    inner join user_profiles t2
                    on t2.id=t1.user_id
                    where t1.id=".$data["wid"]."";
                        $tdsf=\DB::connection()->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
                    if($tdsf && isset($tdsf["dop_status"])){ 
	                switch($tdsf["dop_status"]){
		            case 2:
		                $persent=65;
		            break;
		            case 3:
		                $persent=50;
		            break;
	                }
					}
	                $pprice=$company->myteaser_perclick;
	                $cprice=$pprice*$persent/100;
	                $mprice=$pprice*-1;
					//var_dump($pprice);
					
					if($pprice){
							$sql="insert into payment_history (
                            type,
                            user_id,
                            summa,
                            description,
                            author_id,
                            author_name
                            )
                            values (
                            'spisanie',
                            ".$company->user_id.",
                            $mprice,
                            'переход по ссылке',
                            0,
                            'система'
                            ) RETURNING id;
                           ";
						   $stt=\DB::connection("advertise")->getPdo()->prepare($sql);
                           $stt->execute();
                           $idr=$stt->fetch(\PDO::FETCH_ASSOC);
                           if($idr && $idr["id"]){
                           $id_transaction=$idr["id"];
                           }
                           $sql="update user_profiles
                           set balance = balance+($mprice)
                           where user_id=".$company->user_id."
                           ";
                           \DB::connection()->getPdo()->exec($sql);
                           $sql="update user_profile
                           set balance = balance+($mprice)
                           where user_id=".$company->user_id."
                           ";
                           \DB::connection("advertise")->getPdo()->exec($sql);
						   
						   print "->>> $id_company->>>\n";
						   $this->sthMyAdvertselecting->execute([$day,$id_company]);
						   $vgtrk=$this->sthMyAdvertselecting->fetch(\PDO::FETCH_ASSOC);
						   var_dump($vgtrk);
						   if($vgtrk){
								$this->sthMyAdvertiseAddClick->execute([$id_company]); 
						   }else{
							   	$this->sthMyAdvertiseSetClick->execute([$id_company]); 
						   }
						   
	                    }
					}
			break;
		  }
	     
	    $ret=[$day,$datetime,$id_server,$data["wid"],$ip,$hash,$url,$country,$region,$id_type,$pprice,$cprice,$id_company];
		#var_dump($ret);
		$this->sthAllClicks->execute($ret);
		
		//var_dump($data);

	}
	public function prepareData(){
		$widgets = \App\MPW\Widgets\Teaser::All();
		foreach($widgets as $wid){
		$this->servers[$wid->id]=$wid->pad;

		}
		$pdo = \DB::connection("pgstatistic")->getPdo();
	    $sql="
		insert into advert_stat_pages(id_server,url,page_key,ip4,country,region,timegroup,old_id,id_widget,rg_,found,driver) 
		values(?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		?,
		1000
		)
		";	
		$this->sth=$pdo->prepare($sql);
		$pdo_new = \DB::connection("pgstatistic_teaser")->getPdo();
		$sql="
	     insert into  all_views (
    day,
    daytime,
    id_server,
    id_widget,
    hash,
    url,
	country,
	region,
    found
	)values(?,?,?,?,?,?,?,?,?)
	";
	$this->sthAllViews=$pdo_new->prepare($sql);
    $sql="
	insert into detail_views (
    day,
    daytime,
    id_server,
    id_widget,
    hash,
    url,
    country,
    region,
    found,
	id_type
    )values(?,?,?,?,?,?,?,?,?,?);
	";
	$this->sthDetailViews=$pdo_new->prepare($sql);	
	$sql="insert into myadvert_views (
    day,
    geo_id,
    shop_id,
    pad,
    wid,
    id_tree,
    hash,
    url,
    found,
    datetime,
    ip,
    id_rubrika
    )values (?,?,?,?,?,?,?,?,?,?,?,?)
	
	";
	$this->sthMyAdvertViews=$pdo->prepare($sql);	
	
	$sql="
	select 
    id_company from 
    all_clicks
    where 
    day=?
	and id_type=1
    and id_company=?
    limit 1
	";
	$this->sthMyAdvertselecting=$pdo_new->prepare($sql);
	
        $v=\DB::connection("advertise")->getPdo();
		$sql="update advertises set day_clicks =1 where id=? ";
        $this->sthMyAdvertiseSetClick=$v->prepare($sql);
		$sql="update advertises set day_clicks = day_clicks +1 where id=?";
        $this->sthMyAdvertiseAddClick=$v->prepare($sql);

	
	
	$sql="
	insert into  all_clicks (
    day,
    daytime,
    id_server,
    id_widget,
	ip,
    hash,
    url,
	country,
	region,
	id_type,
	price,
	client_price,
	id_company
	)values(?,?,?,?,?,?,?,?,?,?,?,?,?)
	";
	$this->sthAllClicks=$pdo_new->prepare($sql);
	
	#$sql="select id,code,name from iso2_regions where country='".$idCountry."' and region = '".$idRegion."'";
	
	$sql="select id,code,name from iso2_tree where country=? and code = ?";
	
	$vcpdo=\DB::connection("advertise")->getPdo();
	
	$this->geoSelectPrepareSth=$vcpdo->prepare($sql);
	
	}
}	