<?php

namespace App\Console\Commands\Statistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class Partner extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:partner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function ApiMessages(){
		
		  #$tmp_file="/home/myrobot/data/videostatistic/warning.market-place.su.log";
	      $my_file="/home/myrobot/data/videostatistic/warning.market-place.su.log";
		  $tmp_file="/home/myrobot/data/videostatistic/warning.market-place_".time()."_.log";
		  $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	      `$cmd`;
		  $handle = @fopen($tmp_file, "r");
		  if ($handle) {
		  while (($buffer = fgets($handle, 4096)) !== false) {
				#print $buffer;
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\|\|/",$l);
				$url=rawurldecode($tmp[1]);
				$message=rawurldecode($tmp[5]);
				$widget_id=rawurldecode($tmp[6]);
				$ltd=rawurldecode($tmp[7]);
				$time=$tmp[0];
				$idServer=$tmp[2];
				$ip=$tmp[3];
				$type=$tmp[4];
				$md=explode(" ",$time);
				$day=trim($md[0]);
				$k=[
				$time
				,$url
				,$message
				,$ip
				,$ltd
                ,$day
				,$idServer
				,$widget_id
				,$type
				];
				
				$this->updateWarnins->execute($k);
				$k=[
				$day
				,$idServer
				,$widget_id
				,$type
				,$time
				,$url
				,$message
				,$ip
				,$ltd
                ,$day
				,$idServer
				,$widget_id
				,$type
				];
				$this->insertWarnins->execute($k);
				//var_dump($k); 
				#var_dump($tmp); 
				
		    }
		  }
		  @fclose($handle); 
		   $cmd ="rm  -f  $tmp_file";
	      `$cmd`;	
	}	 
	
    public function handle()
    {
		#returns;
		
		$pdo=\DB::connection("pgstatistic")->getPdo();
		$pdoadv=\DB::connection("advertise")->getPdo();
		$pdocluck=\DB::connection("cluck")->getPdo();
		$pdoprecluck=\DB::connection()->getPdo();
		#$this->insertViewsSth=$pdo->prepare($sql);
		
		
		
		
		
		
		
			$sql="
		update myadvert_warnings 

    set datetime=?,
    url=?,
    message=?,
	ip=?,
	ltd=?,
	new=1,
	cnt=cnt+1
	WHERE day=? and pad=? and pid=? and type=?
	";
		$this->updateWarnins=$pdo->prepare($sql);	
		$sql="
		insert into myadvert_warnings (
    day,
    pad,
    pid,
    type,
    datetime,
    url,
    message,
	ip,
	ltd
    )
    select ?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM myadvert_warnings WHERE day=? and pad=? and pid=? and type=?)
	";
		$this->insertWarnins=$pdo->prepare($sql);
		
	$pgpdonew = \DB::connection("pgstatistic_new")->getPdo();	
	$sql="
		insert into advert_requests (
    id_server,
	id_widget,
    day,
    datetime,
    hash,
    ip,
	url,
	request,
    first_find,
    jns,
	nosearch,
	found
    )values(?,?,?,?,?,?,?,?,?,?,?,?)
		";
		$insertAdvRequests=$pgpdonew->prepare($sql);
		
		$sql="
		insert into myadvert_requests (
    id_server,
	id_widget,
    day,
    datetime,
    hash,
    ip,
	url,
	request,
    first_find,
    jns
    )values(?,?,?,?,?,?,?,?,?,?)
		";
		$insertRequests=$pdo->prepare($sql);
		$this->ApiMessages();
		#return;
		  #$tmp_file="/home/myrobot/data/videostatistic/wrequest.market-place.su.log";
	      $my_file="/home/myrobot/data/videostatistic/wrequest.market-place.su.log";
		  $tmp_file="/home/myrobot/data/videostatistic/wrequest.market-place.su_".time()."_.log";
		  $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	      `$cmd`;
		  $handle = @fopen($tmp_file, "r");
		  if ($handle) {
		  while (($buffer = fgets($handle, 4096)) !== false) {
				#print "|".$buffer."|\n";
				
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\|\|\|/",$l);
				#var_dump(["inde",$tmp]);
				if(!isset($tmp[5]))continue;
				if(isset($tmp[10])){
				$u=rawurldecode($tmp[10]);	
				}else{
				$u="";	
				}
				if(isset($tmp[11])){
				$jns=$tmp[11];	
				}else{
				$jns=null;
				}
				
				if(isset($tmp[8])){
				$ff=rawurldecode($tmp[8]);	
				}else{
				$ff="";	
				}
                $id_server=$tmp[0];
				$id_widget=0;
				$tss=explode("_",$id_server);
				if(isset($tss[1])){ 
					//var_dump(['это пидпад',$tss]);
					$id_server=intval($tss[0]);
					$id_widget=intval($tss[1]);
				}else{
					$id_server=intval($tmp[0]);
				}
				#if($id_server==49){
				#var_dump($tmp)	;
				#}
				
				
				$nosearch=0;
				if(isset($tmp[12])){
					$nosearch=intval($tmp[12]);
				}
				$found=0;
				if(isset($tmp[9])){
					$found=$tmp[9];
				}
				#$tmp[7]=rawurldecode($tmp[7]);
				if(!isset($tmp[7]) || !$tmp[7])
					$tmp[7]=0;
				$tmp[7]=rawurldecode($tmp[7]);
			if($id_server=="49"){
				$tmp[7]=rawurldecode($tmp[7]);
				#if($tmp[3]=="6666cd76f96956469e7be39d750cc7d9"){
				var_dump(["колебанья",$u,$tmp[7],$tmp[8]]);	
				#}
				}
				#$tmp[7]=rawurldecode($tmp[7]);	
				$k=[$id_server,
				$id_widget,
				$tmp[1],
				$tmp[2],
				$tmp[3],
				$tmp[5],
				$u,
				$tmp[7],
				$ff,
				$jns
				];
#if($id_server=="49"){
				#var_dump($k);	
				#}
				
				$insertRequests->execute($k);
				$k=[$id_server,
				$id_widget,
				$tmp[1],
				$tmp[2],
				$tmp[3],
				$tmp[5],
				$u,
				$tmp[7],
				$ff,
				$jns,
				$nosearch,
				$found
				];
				if($id_widget=="1463"){
					#print $buffer."\n";
				#print_r($k)	;
				}
				$insertAdvRequests->execute($k);
		    }		
		  }		
		  @fclose($handle);
		  $cmd ="rm  -f  $tmp_file";
	      `$cmd`;	
		 #$pgpdonew = \DB::connection("pgstatistic_new")->getPdo();
		#$pdonew=
		$sql="insert into myadvert_views (
        day,
        geo_id,
        shop_id,
	    pad,
	    wid,
	    id_tree,
		id_rubrika,
	    hash,
        url,
	    found,
	    datetime,
		ip
        ) values (?,?,?,?,?,?,?,?,?,?,?,?)
		";
		$this->insertViewsNewSth=$pgpdonew->prepare($sql);
        $sql="insert into topadvert_views (
        day,
        geo_id,
        shop_id,
	    pad,
	    wid,
	    id_tree,
		id_rubrika,
	    hash,
        url,
	    found,
	    datetime,
		ip
        ) values (?,?,?,?,?,?,?,?,?,?,?,?)
		";
		$this->insertViewsTopNewSth=$pgpdonew->prepare($sql);		
        $sql="insert into topadvert_requests (
     id_server,
	id_widget,
    day,
    datetime,
    hash,
    ip,
	url,
	request,
    first_find,
    jns,
	nosearch,
	found
    )values(?,?,?,?,?,?,?,?,?,?,?,?)
		"; //новый реквест
		$this->insertRequestTopNewSth=$pgpdonew->prepare($sql);			
	        $sql="insert into my_requests (
     id_server,
	id_widget,
    day,
    datetime,
    hash,
    ip,
	url,
	request,
    first_find,
    jns,
	nosearch,
	found
    )values(?,?,?,?,?,?,?,?,?,?,?,?)
		"; //новый реквест
		$this->insertRequestMyNewSth=$pgpdonew->prepare($sql);		
	        $sql="insert into yandex_requests (
     id_server,
	id_widget,
    day,
    datetime,
    hash,
    ip,
	url,
	request,
    first_find,
    jns,
	nosearch,
	found
    )values(?,?,?,?,?,?,?,?,?,?,?,?)
		"; //новый реквест
		$this->insertRequestYandexNewSth=$pgpdonew->prepare($sql);			
		
		$sql="
		insert into advert_stat_pages(id_server
		,url
		,page_key
		,ip4
		,country
		,region
		,timegroup
		,datetime
		,old_id
		,id_widget
		,driver
		,rg_
		,found
		,myfound
		,clid
		";
		$sql.="
		) 
		values(
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
		?,
		?,
		?,
		?,
		?
		";
		$sql.="
		)
		";
		//$pdo
		$this->insertCommonSth=$pdo->prepare($sql);
		
		$sql="insert into myadvert_views (
        day,
        geo_id,
        shop_id,
	    pad,
	    wid,
	    id_tree,
		id_rubrika,
	    hash,
        url,
	    found,
	    datetime,
		ip
        ) values (?,?,?,?,?,?,?,?,?,?,?,?)
		";
		$this->insertViewsSth=$pdo->prepare($sql);
		 #$tmp_file="/home/myrobot/data/videostatistic/mykz.market-place.su-views.log";
	     $my_file="/home/myrobot/data/videostatistic/mykz.market-place.su-views.log";
		 $tmp_file="/home/myrobot/data/videostatistic/mykz_views.market-place.su_".time()."_.log";
		 $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	     `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			$i=-1;
			$nvb=[];
			while (($buffer = fgets($handle, 4096)) !== false) {
				#print $buffer;
				$l = str_replace("\n", "", $buffer);

				$tmp=preg_split("/\|\|/",$l);
				if(preg_match('/^2[0-9]{3,3}\-[0-9]{2,2}\-[0-9]{2,2}\s[0-9]{2,2}\:[0-9]{2,2}\:[0-9]{2,2}$/',$tmp[0])){
				   
					if($i>=0){
					$this->registerTmp($nvb[$i]);
					
					
					}
					$i++;
					#var_dump($tmp);
					$nvb[$i]["day"]=$tmp[1];
					$nvb[$i]["ip"]=$tmp[2];
					$nvb[$i]["geo"]=$tmp[3];
					$nvb[$i]["pad"]=$tmp[7];
					$nvb[$i]["wid"]=$tmp[6];
					$nvb[$i]["hash"]=$tmp[4];
					$nvb[$i]["url"]=rawurldecode($tmp[5]);
					$nvb[$i]["datetime"]=$tmp[0];
					
					$nvb[$i]["signal"]="";
					if(isset($tmp[8])){
						$nvb[$i]["signal"]=$tmp[8];
						if(isset($tmp[9]))
						$nvb[$i]["country"]=$tmp[9];
						if(isset($tmp[10]))
						$nvb[$i]["region"]=$tmp[10];
					if(isset($tmp[11])){
						$nvb[$i]["clid"]=$tmp[11];
						
					}
					}else{
						
					}
					
				}else{
					#var_dump($tmp);
					if(isset($tmp[2]))
					$nvb[$i]["found"][$tmp[0]][$tmp[2]][$tmp[1]]=2;
				    else{
						var_dump(["нарушение",$tmp]);
					}
					
					
				}
				
				
				#\App\Videosource\Mystat\Top::getInstance()->getData($tmp); #Videosource\\Yac
			}
                    if(isset($nvb[$i])){  

					$this->registerTmp($nvb[$i]);
					
                    }					
		}
		 @fclose($handle);
		 $cmd ="rm  -f  $tmp_file";
	     `$cmd`;	
        //$this->servStat();
    }
	public function registerCommon(&$data,$driver=1)
    {

                                        #var_dump($data);

		$cnt=0;
		if(isset($data["found"]) && $data["found"])
			$cnt=count($data["found"]);
		$clid=null;
		if(isset($data["clid"])){
			$clid=$data["clid"];
		}
		$time=strtotime($data["datetime"]);
		$time=$time-($time%180);
		$timegroup=date("Y-m-d H:i:s",$time);
		$r=[
		$data["pad"],
		$data["url"],
		$data["hash"],
		$data["ip"],
		$data["country"],
		$data["region"],
		$timegroup,
		$data["datetime"],
		0,
		$data["wid"],
		$driver,
		'776',
		$cnt,
		0,
		$clid
		];
		$this->insertCommonSth->execute($r);
		if($driver==2){
		
		#var_dump(["может быть очень может быть",$r]);
		}else{
			#var_dump(["может быть очень может быть",$r]);
		}
		
		#var_dump(["может быть очень может быть",$r]);
	}
	public function prezentMyStatNew(&$data)
    {
		$fou=0;
	    if(isset($data["found"]) && $data["found"]){
			$fou=count($data["found"]);
			#var_dump($data);
		foreach($data["found"] as $shop=>$categories){
			foreach($categories	as $category=>$prices){
				$id_rubrika=0;
		        $vcc=explode("_",$category);
				if(isset($vcc[1])){
				$category=intval($vcc[0]);
				$id_rubrika=intval($vcc[1]);
				}
				if(1==0){
				var_dump([
				$data["day"],
				$data["geo"],
				$shop,
				$data["pad"],
				$data["wid"],
				$category,
				$id_rubrika,
				$data["hash"],
				$data["url"],
				count($prices),
				$data["datetime"],
				$data["ip"]
				]);
				}
				
				$this->insertViewsNewSth->execute([
				$data["day"],
				$data["geo"],
				$shop,
				$data["pad"],
				$data["wid"],
				$category,
				$id_rubrika,
				$data["hash"],
				$data["url"],
				count($prices),
				$data["datetime"],
				$data["ip"]
				]);
			
			}
		}	
	    }else{
			#print "нет никого для \n";
		}
		
		$z=[$data["pad"],
	$data["wid"],
	$data["day"],
	$data["datetime"],
	$data["hash"],
	$data["ip"],
	$data["url"],
	'',
	'',
	'',
	0,
	$fou
	];
	#var_dump($data);
		$this->insertRequestMyNewSth->execute($z);			
		
		
	}
	public function prezentTopStatNew(&$data)
    {
		$fou=0;
		#print_r($data); return;
	    if(isset($data["found"]) && $data["found"]){
			$fou=count($data["found"]);
		foreach($data["found"] as $shop=>$categories){
			foreach($categories	as $category=>$prices){
				#var_dump($category);
				$id_rubrika=0;
		        $vcc=explode("_",$category);
				if(isset($vcc[1])){
				$category=intval($vcc[0]);
				$id_rubrika=intval($vcc[1]);
				}
				if(!$category) $category=0;
				if(1==0){
				var_dump([
				$data["day"],
				$data["geo"],
				$shop,
				$data["pad"],
				$data["wid"],
				$category,
				$id_rubrika,
				$data["hash"],
				$data["url"],
				count($prices),
				$data["datetime"],
				$data["ip"]
				]);
				}
				if(1==1){
				$this->insertViewsTopNewSth->execute([
				$data["day"],
				$data["geo"],
				$shop,
				$data["pad"],
				$data["wid"],
				$category,
				$id_rubrika,
				$data["hash"],
				$data["url"],
				count($prices),
				$data["datetime"],
				$data["ip"]
				]);
			    }
			}
		}

		
	    }else{
			#print "нет никого для \n";
		}
	$z=[$data["pad"],
	$data["wid"],
	$data["day"],
	$data["datetime"],
	$data["hash"],
	$data["ip"],
	$data["url"],
	'',
	'',
	'',
	0,
	$fou
	];
	#var_dump($data);
		$this->insertRequestTopNewSth->execute($z);
		
	}	
	public function prezentYandStatNew(&$data)
    {
		$fou=0;
		#print_r($data); return;
	    if(isset($data["found"]) && $data["found"]){
			$fou=count($data["found"]);


		
	    }else{
			#print "нет никого для \n";
		}
	$z=[$data["pad"],
	$data["wid"],
	$data["day"],
	$data["datetime"],
	$data["hash"],
	$data["ip"],
	$data["url"],
	'',
	'',
	'',
	0,
	$fou
	];
	#var_dump($data);
		$this->insertRequestYandexNewSth->execute($z);
		
	}	
	public function registerTmp(&$data)
    {
		#return;
		if($data["ip"]=="176.213.140.214"){
                var_dump($data);
                } 
		
		if($data["signal"]){
			if($data["signal"]=="yandex"){
			#var_dump($data); 
			$this->prezentYandStatNew($data,1);
			$this->registerCommon($data,2);
			return;
			}elseif($data["signal"]=="topadvert"){
			$this->prezentTopStatNew($data,1);
			$this->registerCommon($data,1);
			return;
			}elseif($data["signal"]=="myt"){
			$this->prezentMyStatNew($data,1);
			#return;
			//var_dump($data);
			}
		}
	if(isset($data["found"]) && $data["found"]){
		foreach($data["found"] as $shop=>$categories){
			foreach($categories	as $category=>$prices){
				$id_rubrika=0;
		        $vcc=explode("_",$category);
				if(isset($vcc[1])){
				$category=intval($vcc[0]);
				$id_rubrika=intval($vcc[1]);
                                }
				if(1==0){
				var_dump([
				$data["day"],
				$data["geo"],
				$shop,
				$data["pad"],
				$data["wid"],
				$category,
				$id_rubrika,
				$data["hash"],
				$data["url"],
				count($prices),
				$data["datetime"],
				$data["ip"]
				]);
				}
				
				
				
				$this->insertViewsSth->execute([
				$data["day"],
				$data["geo"],
				$shop,
				$data["pad"],
				$data["wid"],
				$category,
				$id_rubrika,
				$data["hash"],
				$data["url"],
				count($prices),
				$data["datetime"],
				$data["ip"]
				]);
			}
        }			
	}else{
		$this->insertViewsSth->execute([
				$data["day"],
				$data["geo"],
				0,
				$data["pad"],
				$data["wid"],
				0,
				0,
				$data["hash"],
				$data["url"],
				0,
				$data["datetime"],
				$data["ip"]
				]);
	}
	#print_r($data); return;
	
		$data=[];
	}
	private function servStat(){
				

	    $date=date("Y-m-d");
	    $today = Carbon::now();
		if($today->hour<2){
		$date = $today->yesterday()->format('Y-m-d');
		
	   
	    }
		#var_dump($date);
		$pdo = \DB::connection("advertise")->getPdo();
		$pgpdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select id,name from iso2_tree where geo_id=?";
		$stath=$pdo->prepare($sql);
		$sql="select day,shop_id,wid,id_tree,id_geo,sum(price) as price
		,count(*) as clicks
		from myadvert_clicks where day = '$date'
		group by day,shop_id,wid,id_tree,id_geo";
		$data=$pgpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		$poloz=[];
		foreach($data as $d){
			$stath->execute([$d["id_geo"]]);
			$p=$stath->fetch(\PDO::FETCH_ASSOC);
			if(!$p) continue;
			$id_geo=$p["id"];
			$d["category"]=$d["id_tree"];
			$poloz[$d["day"]][$d["shop_id"]][$d["wid"]][$d["category"]][$id_geo]["clicks"]=1;
			$poloz[$d["day"]][$d["shop_id"]][$d["wid"]][$d["category"]][$id_geo]["summa"]=floatval($d["price"]);
		}
		
		$sql="select shop_id
        ,geo_id
        ,day
        ,wid
        ,category
        ,sum(cnt) as views from myadver_summa_views
         where day = '$date'
        group by shop_id,geo_id,day,wid,category";
		#var_dump($poloz); die();
	    $dvta=$pgpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach($dvta as $d){	
		$poloz[$d["day"]][$d["shop_id"]][$d["wid"]][$d["category"]][$d["geo_id"]]["views"]=$d["views"];
		if(isset($poloz[$d["day"]][$d["shop_id"]][$d["wid"]][$d["category"]][$d["geo_id"]]["clicks"])){
		#var_dump($d);
		#die();
		}
		}
	$sql="
	update myadver_summa_clicks 
    set views =?,
    clicks =?,
    summa =?
	WHERE 
	day=? and
    shop_id =? and 
	id_region =? and
	id_tree =? and
	pad =?
		";
		$sthUpdatePids=$pgpdo->prepare($sql);		
	$sql="
		insert into myadver_summa_clicks (
    day,
    shop_id ,
	id_region ,
	id_tree,
	pad,
    domain ,
    region ,
    category,
    shop,
	views,
    clicks,
    summa
    ) select ?,?,?,?,?,?,?,?,?,?,?,?
    WHERE NOT EXISTS (SELECT 1 FROM  myadver_summa_clicks WHERE 
	day=? and
    shop_id =? and 
	id_region =? and
	id_tree =? and
	pad =?
	) ";
		$sthInsertPids=$pgpdo->prepare($sql);			
		foreach($poloz as $day=>$shops){
			foreach($shops as $shop=>$wids){
				    $shopname=null;
				    $this->selectAdvSth->execute([$shop]);
					$sarefre	=$this->selectAdvSth->fetch(\PDO::FETCH_ASSOC);
					if(!$sarefre){
						$shopname="не найдено";
					}else
					$shopname=$sarefre["name"];
				
				foreach($wids as $wid=>$categories){
					$pad=0;
				    $domain="не указан";
					if($wid){
					$this->selectPad->execute([$wid]);
					$pds=$this->selectPad->fetch(\PDO::FETCH_ASSOC);
					
					if($pds["pad"])
					    $pad=$pds["pad"];
					if($pds["ltd"])
						$domain=$pds["ltd"];
					}
					
					foreach($categories as $category=>$regions){
					if($category){
				    $this->selectYandexSth->execute([$category]);
					$yarefre	=$this->selectYandexSth->fetch(\PDO::FETCH_ASSOC);
					if(!$yarefre){
											$yagname="не найдено";
					
					}else
					$yagname=$yarefre["uniq_name"];
				    }else{
					$yagname="не указано";
				    }
						foreach($regions as $region => $arr){
												$this->selectIsoSth->execute([$region]);
					                            $refre	=$this->selectIsoSth->fetch(\PDO::FETCH_ASSOC);
					                            if(!$refre){
					                            $regname="не найдено";
					                            }else
					                            $regname=$refre["name"];
					$views = isset($arr["views"])?$arr["views"]:0;
					$clicks=isset($arr["clicks"])?$arr["clicks"]:0;
					$summa=isset($arr["summa"])?$arr["summa"]:0;
					
					if($clicks)
print "	$day,$shop,$region,$category,$pad,$domain,$regname,$yagname,$shopname \n";			
							$sthUpdatePids->execute([
							$views,$clicks,$summa,
							$day,$shop,$region,$category,$pad
							]);
							$sthInsertPids->execute([
							$day,$shop,$region,$category,$pad,$domain,$regname,$yagname,$shopname,$views,$clicks,$summa,
							$day,$shop,$region,$category,$pad
							]);				
							
					        # print $day." : ".$shop." : ".$wid." $category  $region $pad $domain $shopname $yagname $regname\n";
						} 
					}
				}	
			}
		}	
	}
}
