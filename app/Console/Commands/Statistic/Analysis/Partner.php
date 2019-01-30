<?php

namespace App\Console\Commands\Statistic\Analysis;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminated\Console\WithoutOverlapping;

use App\LogStat\Product\Vizir as VizirY;
use App\LogStat\Product\My as MPY;
use App\LogStat\Product\Nei as Nei;
use App\LogStat\Product\Day as Day;
use App\LogStat\Product\Site as Site;
class Partner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:analysis:partner';

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
    public function handle()
    {
	// 	
	$predpdo = \DB::connection("pgstatistic_new")->getPdo();
	
		$file='/home/mystatistic/product/wvizor.log';
		$tmp_file="/home/mystatistic/product/wvizor_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				#echo $l."\n";
				$tmp=preg_split("/\s+\:\s+/",$l);
				VizirY::getInstance()->getData($tmp);
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;	

	

		$file='/home/mystatistic/product/cart.log';
		$tmp_file="/home/mystatistic/product/cart_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				#echo $l."\n";
				$tmp=preg_split("/\s+\:\s+/",$l);
				MPY::getInstance()->getData($tmp);
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
        $countriesw=[];
		$regions=[];
		$file='/home/www/statistica.market-place.su/g_ip/geo_ip_rezult_log.php';
		$tmp_file="/home/www/statistica.market-place.su/g_ip/geo_ip_rezult_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				try{
				$tmp=preg_split("/\s+\:\s+/",$l);
				$data=preg_split('/\|/',$tmp[0]);
				var_dump($data[1]);echo "\n";
				$regions[$data[0]][$data[2]][$data[3]][$data[4]]=1;
				$countriesw[$data[0]]=$data[1];
				}catch(\Exception $e){
					
				}
				//MPY::getInstance()->getData($tmp);
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;		
		$file='/home/mystatistic/product/newview.log';
		$tmp_file="/home/mystatistic/product/newview_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				try{
				$tmp=preg_split("/\s+\:\s+/",$l);
				Nei::getInstance()->getData($tmp);
				#print $l."\n";	
				
				}catch(\Exception $e){
					var_dump($e->getMessage());
				}
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		
		
		$file='/home/mystatistic/product/newclick.log';
		$tmp_file="/home/mystatistic/product/newclick_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				try{
				#print $l."\n";	
				
				}catch(\Exception $e){
					
				}
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;		
		
		
		
		
		
		
		
		$today = Carbon::now();
		if($today->hour<2){
			$date = $today->yesterday()->format('Y-m-d');
		}else{
			$date=$today->format('Y-m-d');
		}
		Day::getInstance($date)->getDayStatistic();
		
		$file='/home/mystatistic/product/apirequest.log';
		$tmp_file="/home/mystatistic/product/apirequest_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		#$tmp_file='/home/mystatistic/product/apirequest.log';
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				try{
				#print $l."\n";	
				$data=preg_split('/\|\|/',$l);
				Site::getInstance()->getData($data);
				}catch(\Exception $e){
					var_dump($e->getMessage()); die();
				}
			}
	    @fclose($handle);		
		}		
		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		
		Site::getInstance()->makeSumma();
		
		
		return;
		
		$predprod = \DB::connection("pg_product")->getPdo();
	$sql="CREATE temp TABLE geo_ip_tmp 
	as 
	select
	country_code,
    region_code ,
    country_name,
    region_name,
    city_name,
	last_ip
	from geo_ip limit 1
	";
	$predprod->exec($sql);
	$sql=" insert into geo_ip_tmp(
	country_code,
    region_code,
    country_name,
    region_name,
    city_name,
	last_ip
	) values (?,?,?,?,?,?)
    ";
	$sth=$predprod->prepare($sql);
		print "\nhttp://geolite.maxmind.com/download/geoip/misc/region_codes.csv\n";
		
			foreach($regions as $country=>$regs){
				$country_name=$countriesw[$country];
			foreach($regs as $region=>$cities){
				if($region && $region != "-"){
				$region_name=geoip_region_name_by_code ( $country, $region);
				//$region_name=mb_convert_encoding($region_name, "UTF-8", "auto");
				
				if(!$region_name) $region_name="-";
				}else{
				$region_name="-";
				}
				foreach($cities as $city=>$o){
				$city = iconv('ASCII', 'UTF-8//IGNORE', $city);
				$region_name = iconv('ASCII', 'UTF-8//IGNORE', $region_name);
				$region_name=trim($region_name);
				$region=trim($region);
				$country_name=trim($country_name);
				$country=trim($country);
				$city=trim($city);
				$region_name = preg_replace('/\'\'/ui','\'\'',$region_name);
				$city = preg_replace('/\'\'/ui','\'\'',$city);
				$z=array_keys($o);
					$ind=rand (0,(count($z)-1));
					$ip=trim($z[$ind]);
					//var_dump($z,$z[$ind]);
					if(mb_strlen($ip)<16){
				
				
				     echo "/$country/$region/$country_name/$region_name/$city/$ip\n";	
					 $sth->execute([$country,$region,$country_name,$region_name,$city,$ip]);
                    }				 
				}
			}
			}
		
		$sql="
		UPDATE geo_ip as a
SET last_ip=b.last_ip,
    city_name = b.city_name,
	iso_code=null
FROM(
   select  
     t1.country_code,
     t1.region_code ,
     t1.country_name,
     t1.region_name,
     t1.city_name,
	 t1.last_ip
from geo_ip_tmp t1
inner join geo_ip t2
on   t1.country_code=t2.country_code
     and t1.region_code=t2.region_code
     and t1.country_name=t2.country_name
     and t1.region_name=t2.region_name
	 and (t2.last_ip is null or t1.city_name <> t2.city_name
	 )
	)   as b
	where a.country_code=b.country_code
     and a.region_code=b.region_code
     and a.country_name=b.country_name
     and a.region_name=b.region_name
     and a.city_name=b.city_name;
	";
	$sql.="

	insert into   geo_ip(
    country_code,
    region_code ,
    country_name,
    region_name,
    city_name,
	last_ip
)
select  t1.country_code,
     t1.region_code ,
     t1.country_name,
     t1.region_name,
     t1.city_name,
	 t1.last_ip
from geo_ip_tmp t1
left join geo_ip t2
on   t1.country_code=t2.country_code
     and t1.region_code=t2.region_code
     and t1.country_name=t2.country_name
     and t1.region_name=t2.region_name
     and t1.city_name=t2.city_name
	 where t2.id is null
	";		
	$predprod->exec($sql);
	
	$d=date("H");
	if($d=="09"){
		//$this->getISO();
	}
	
	//GET http://api.sypexgeo.net/json/123.45.67.89	
	//http://api.sypexgeo.net/json/46.107.230.44
    }
	private function getISO(){
		$predprod = \DB::connection("pg_product")->getPdo();
		
		$sql="
		update geo_ip a  
set iso_code=b.iso_code
from (
select t1.* from geo_ip t1
inner join (
select
country_code,region_code,iso_code
from geo_iso_custom where iso_code is not null
) t2
on t1.country_code=t2.country_code
and t1.region_code=t2.region_code
where t1.iso_code is  null
)b
where a.country_code=b.country_code
and a.region_code=b.region_code
		";
		$predprod->exec($sql);	
		$sql="update geo_ip set iso_code=?, iso_city_name=? where id=?";
		$sth1=$predprod->prepare($sql);
		$sql="update geo_ip set iso_code=null, iso_city_name=null,last_ip=null where id=?";
		$sth2=$predprod->prepare($sql);
		$sql="select * from geo_ip where iso_code is null and last_ip is not null 
		and region_code<>'-'
		order by case when country_code='RU' then 1
                      when country_code='UA' then 2   
					  when country_code='BY' then 1   
					  when country_code='KZ' then 2   
                      else 3 end 
 limit 20 ";
		$data=$predprod->query($sql)->fetchALL(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			$res=$this->prepCurl($d["last_ip"]);
			$ch=0;
			if($res && $result=json_decode($res,1)){
				if(isset($result["city"]) && isset($result["city"]["name_en"]) && $result["region"]["iso"]){
					$sth1->execute([$result["region"]["iso"],$result["city"]["name_en"],$d["id"]]);
					$ch=1;
					print $result["region"]["iso"].":".$result["city"]["name_en"].":".$d["city_name"].":".$result["region"]["name_en"].":".$d["region_name"]."\n";
				}
			#var_dump($result);	
			}
			if($ch==0){
				$sth2->execute([$d["id"]]);
				# сменить ip
			}
			
		}
		
	    $sql="update geo_ip set iso_code=null, iso_city_name=null,last_ip=null where 
		country_code=? and region_code=?  and iso_code=?";
		$sth3=$predprod->prepare($sql);	
		$pravda=[];
		$crivda=[];
		$sql="
select t1.country_code,t1.region_code,t2.iso_code,count(*) as cnt
,sum(case when lower(t2.city_name) = lower(t2.iso_city_name) then 1 else 0 end) as s
from (
select country_code,region_code,count(*) as cnt from
(
select country_code,region_code,iso_code
from  geo_ip
where iso_code is not null
group by country_code,region_code,iso_code
) t 
group by  country_code,region_code
having count(*)>1
) t1
inner join geo_ip t2
on t1.country_code=t2.country_code
and t1.region_code=t2.region_code
and t2.iso_code is not null
group by t1.country_code,t1.region_code,t2.iso_code
order by t1.country_code,t1.region_code,cnt desc
		";
		$data=$predprod->query($sql)->fetchALL(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			if($d["s"]==0){
				$sth3->execute([$d["country_code"],$d["region_code"],$d["iso_code"]]);
				
			}else{
				$pravda[$d["country_code"]][$d["region_code"]][$d["iso_code"]]=1;

			}
			
		}
$sql="
select t2.country_code,t2.region_code,t1.iso_code
,sum(case when lower(t2.city_name) = lower(t2.iso_city_name) then 1 else 0 end) as s
,count(*) as cnt
from (
select iso_code,count(*) as cnt from
(
select country_code,region_code,iso_code
from  geo_ip
where iso_code is not null
group by country_code,region_code,iso_code
) t 
group by  iso_code
having count(*)>1
)t1
inner join geo_ip t2
on t1.iso_code=t2.iso_code
group by  t2.country_code,t2.region_code,t1.iso_code
";
		$data=$predprod->query($sql)->fetchALL(\PDO::FETCH_ASSOC);
		foreach($data as $d){
			if($d["s"]==0){
				$sth3->execute([$d["country_code"],$d["region_code"],$d["iso_code"]]);
				
			}else{
				$pravda[$d["country_code"]][$d["region_code"]][$d["iso_code"]]=1;
			}
			
		}
	$sql="update geo_ip set iso_code=? where 
		country_code=? and region_code=?  and last_ip is not null";
		$sth4=$predprod->prepare($sql);		
		
		foreach($pravda as $country=>$regions){
			foreach($regions as $region=>$codes){
				foreach($codes as $code=>$f){
					$sth4->execute([$code,$country,$region]);
					print "$country $region $code\n";
				}
			}
		}
     // var_dump($pravda);
		
		#return;
		$sql="	UPDATE geo_ip as a
SET iso_code=b.iso_code
FROM(
select 
t1.country_code
,t1.region_code
,t1.iso_code
,t2.id
from (
select 
t1.country_code
,t1.region_code
,t1.iso_code
from geo_ip t1
where t1.iso_code is not null
and lower(t1.city_name) = lower(t1.iso_city_name)
group by t1.country_code
,t1.region_code
,t1.iso_code
) t1
inner join geo_ip t2
on t1.country_code=t2.country_code
  and t1.region_code=t2.region_code
  and t2.iso_code is null and t2.last_ip is not null

) b
where a.id = b.id;";

$sql="
truncate table geo_iso;
insert into geo_iso (
    country_code,
    region_code,
    iso_code,
    country
)
select t1.country_code,t1.region_code,t2.iso_code
,regexp_replace(t2.iso_code,'\-[^\-]+$', '','g')
 from (
select country_code,region_code,count(*) as cnt from
(
select country_code,region_code,iso_code
from  geo_ip
where iso_code in(
select iso_code from (
select country_code,region_code,iso_code
from  geo_ip where iso_code is not null
group by country_code,region_code,iso_code
) t2
group by iso_code
having count(*)=1 
)
group by country_code,region_code,iso_code
) t 
group by  country_code,region_code
having count(*)=1
) t1
inner join geo_ip t2
on t1.country_code=t2.country_code
and t1.region_code=t2.region_code
and t2.iso_code is not null
group by  t1.country_code,t1.region_code,t2.iso_code";
		$predprod->exec($sql);	
		$sql="
		delete from  geo_iso_custom where iso_code is null;
		insert into geo_iso_custom (
    country_code,
    region_code,
	country_name,
	region_name 
    )
select t1.country_code,
    t1.region_code,
    t1.country_name,
    t1.region_name	
from (
select 
		t1.country_code,
    t1.region_code,
    t1.country_name,
    t1.region_name
	from (
		select country_code,
    region_code,
    country_name,
    region_name
from geo_ip
group by
country_code,
    region_code,
    country_name,
    region_name
	) t1
left join geo_iso t2
on t1.country_code=t2.country_code
and t1.region_code=t2.region_code
where t2.iso_code is null
) t1
left join geo_iso_custom t2
on t1.country_code=t2.country_code
and t1.region_code=t2.region_code
where t2.iso_code is null
	
	";
		$predprod->exec($sql);	
		
		$sql="select iso_code from geo_iso
group by iso_code
having count(*)>1;

select country_code,region_code from geo_iso
group by country_code,region_code
having count(*)>1;";
		
		
		
	}
	
    function prepCurl($ip){
		$url="http://api.sypexgeo.net/json/$ip";
			$ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36"); 
        $result = curl_exec($ch); 
		curl_close($ch); 
		if(!$result) return;
		return $result;
		
    }
	
}
