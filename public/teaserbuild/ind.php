<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

exit();
    function getRealUserIp(){
    switch(true){
      case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
      case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
      case (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && !preg_match('/^192\./',$_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
      default : return $_SERVER['REMOTE_ADDR'];
       }
    }

class teaserWidget{

   private 	$src_id=0;
   private 	$fromUrl='';
   public $domain='';
   public $permission=7; 
   public $geograpgic=[]; 
   private 	$cnt=0;
   public function 	__construct($data){

	    if(!isset($data["src_id"])){
		    throw new Exception('Деление на двануля.');
	    }
		$this->pda = new PDO('pgsql:host=localhost;port=5432;dbname=advertise','market', 'katamaran_boiler');
		$this->pdo = new PDO('pgsql:host=localhost;port=5432;dbname=precluck_market_place','market', 'katamaran_boiler');
	    $this->src_id=intval($data["src_id"]);
	    if(!isset($data["cn"]))
		    $this->cnt=$data["cn"];
	    if(!isset($data["data"])){
			return;
		}
		$parsed=json_decode($data["data"],1);
        $this->fromUrl=urldecode($parsed["fromUrl"]);
		$this->domain=$this->getLtd($this->fromUrl);
		if($this->domain!='market-place.su' && !isset($parsed["stat"])){
			 throw new Exception('Деление на тринуля.');
		}
		
		if($this->domain!='market-place.su' && isset($parsed["stat"])){ 
		$sql="select 
        t.pad
        ,t.offers_count 
        ,t.type
		,hupp.dop_status
		,lower(t.ltd) as ltd
        ,tp.old_id
        ,tp.wid_id
        ,tp.driver
        ,tp.widget_categories
        ,tp.css_selector		
		,tp.nosearch
		,tp.mobile
		,tp.clid
		,tp.type3
		from 
        (select 
        tp.id as old_id
        ,tp.wid_id
        ,tp.driver
        ,tp.widget_categories
		,tp.css_select as css_selector
		,tp.nosearch
		,tp.mobile
		,tp.clid
		,type3
             from widget_products tp where wid_id= ".$this->src_id." and status = 1
             union all 
             select 
		tp.id as old_id,
        tp.wid_id,
        1000 as driver,
        '' as widget_categories,
		'' as css_selector,
		0 as nosearch,
		tp.mobile,
		0 as clid,
		3 as type3
           from widget_tizers tp where wid_id=".$this->src_id." and status = 1
        ) tp
        inner join (widgets t
		inner join user_profiles hupp
		on hupp.user_id=t.user_id
		) on t.id= tp.wid_id
        and t.type =tp.type3
        order by tp.old_id desc limit 1
";


        $wed=$this->pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
		if(!$wed){
			 throw new Exception('no widget.');
		}
	    //var_dump($wed); 
		$dop_status=$wed["dop_status"];
		
		$this->permission=0;
		//return;
		if($dop_status==3)
			$this->permission|=4;
		if($dop_status==2)
			$this->permission|=2;
		if($dop_status==1)
			$this->permission|=1;
		
		$this->getgeo();
		}
		
   }
   	public static function getLtd($host,$flag=1){
	if(!preg_match('/^https?\:\/\//ui',$host))
		$host='https://'.$host;
	#print $host."<br>";
	if($flag){
	$parsed=parse_url($host);
	if(!isset($parsed["host"])){
		return false;
		#var_dump(["недомен",$parsed]); die();
	}
	$newhost = $host = $parsed["host"];
	}else{
		$newhost = $host;
	}
	
	$test = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE",  $host);
	if($test == $host){
		$encoded = idn_to_utf8($host);
		if($encoded != $host){
		#print $encoded."";
		#print "<br>";			
		$newhost=$encoded;
		}
	}
	if($flag){
	$ahh='/([^\.]+)\.([^.]{2,7}|рф|cc\.[^.]{2,4}|co\.[^.]{2,4}|(at|com)\.ua)$/ui';
	if(preg_match($ahh,$newhost,$m)){
		$ltd=$m[0];
	}
	else{
		return false;
		#var_dump(["недомен 2",$newhost]); die();
		
	}
	}else{
		$ltd=$newhost;
	}
	return $ltd;
   }
  protected function getgeo(){
	  $ip=getRealUserIp();
	  $res = geoip_record_by_name($ip);
	  $idRegion=null;
	  if (isset($res["country_code"]) && $res["country_code"]) {
                $idCountry = $res["country_code"];
            }
            if (isset($res["region"]) && $res["region"]) {
                $idRegion = $res["region"];
            }
			if(!$idRegion || $idRegion=='-'){
			$idRegion=$idCountry;
			}
$sql="
select t2.*  from iso2_tree t1
left join iso2_tree t2
on t2.parent_path @> t1.parent_path
where t1.country='$idCountry' and t1.code='$idRegion'
";	

$zeo=$this->pda->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

if(!$zeo){
	 throw new Exception('no geo.');
}

foreach($zeo as $z){
	$this->geograpgic[$z["id"]]=1;
}

#var_dump($sql); die();
#				   $sql="select t1.id as orig,t2.id,t2.country from iso2_tree t1
#left join  iso2_tree t2 on t2.parent_path @>t1.parent_path
#where t1.geo_id=$id";

	  #var_dump($idCountry); 
	  #die();
	  	    #
            

	  
  }
}
try{
$t=new teaserWidget($_GET);

$pda = new PDO('pgsql:host=localhost;port=5432;dbname=advertise', 'mavrik', 'povorotnetuda2');
$sql="select t.ind as id
	,t.id_company
	,t.title as name
	,t.descript as sub_name
	,t.src as url
	,t.img ,a.persent,a.site_permissions
from teasers_offers_new t 
inner join advertises a on a.id=t.id_company
where (a.site_permissions&".$t->permission.">0) and t.status=1
"; 
if($t->geograpgic){
	$sql="
	select t.ind as id
	,t.id_company
	,t.title as name
	,t.descript as sub_name
	,t.src as url
	,t.img
	,a.persent,a.site_permissions
from teasers_offers_new t 
inner join (advertises a
inner join advertises_teaser_geo atg
on atg.id_company=a.id
and atg.id_geo in(".implode(",",array_keys($t->geograpgic)).")
) on a.id=t.id_company
where (a.site_permissions&".$t->permission.">0) and t.status=1
"; 

}
$data=$pda->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
}
catch(\Exception $e){
$data=[];
	
}


if(1==0 && !$data){
	$fl='/home/myrobot/data/1.txt';
	$txt="".date("Y-m-d H:i:s")."::".$t->domain." \n";
	file_put_contents($fl,$txt,\FILE_APPEND);
}
$new_data=json_encode($data);
#file_put_contents($filename,$new_data);
header('content-type: application/json; charset=utf-8');
if($_SERVER['REQUEST_SCHEME']=='https'){
	if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }elseif(isset($_SERVER['HTTP_REFERER'])){
		$tmp=parse_url($_SERVER['HTTP_REFERER']);
		if(isset($tmp["host"])){
		header("Access-Control-Allow-Origin: ".$tmp["host"]."");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}
	}
}else{
header("Access-Control-Allow-Origin: *");
}
echo $new_data;

