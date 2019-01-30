<?php
namespace App\LogStat\Product;
class Nei{
	 private static $instance=null;
	 private $widgets=[];
	 private $widgetsSth;
	 private $Sth;
	 public static function getInstance(){
	 if(self::$instance==null){
	 self::$instance=new self;
	 self::$instance->prepareData();
	 }
	 return self::$instance; 
	}
	 public function getWidgetInfo($id_widget){
		 if(!isset($this->widgets[$id_widget])){ 
			
		 $this->widgetsSth->execute([$id_widget]);
		  
		 $result = $this->widgetsSth->fetch(\PDO::FETCH_ASSOC);
		 if(!$result) 
			 $this->widgets[$id_widget]=[];
		 else
			 $this->widgets[$id_widget]=$result;
		 }
		
		 
		 return $this->widgets[$id_widget];
	  }
	  public function getData(&$arr){
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		$ip = $arr[1];
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
		if(mb_strlen($ip)>15) return;
		$agent=preg_replace('/\"/','',trim($arr[8]));
		if(!isset($arr[6])) return;
		$site=preg_replace('/\"/','',trim($arr[6]));
		
		parse_str($req[1], $dd);
		
		  if(!$dd || !isset($dd["et_"])){
		    return;
	       }	
		$data=json_decode($dd["et_"],true);
		if(!$data){
		    return;
	    }
		if(!isset($data["wid"])) return;
		
		if(!isset($data["url"])) return;
		if(!isset($data["iso"])) return;
		
		$found=0;
		$idps=[];
		if(isset($data["idps"])){
			$idps=array_values($data["idps"]);
			$found=count($idps);

		}
		$site=$data["url"];
		$sitedata=$this-> getFullUrl($site); 
        if(!$sitedata) return;
		$md5=$sitedata[0];
		$site=$sitedata[1];
		$r=[$day,
		$data["wid"],
		$md5,
		$datetime,
		$site,
		$ip,
		$found,
		implode("|",$idps),
		$data["iso"]
		];
		$this->Sth->execute($r);
		
	  }	
	public function getFullUrl($host,$flag=1){
		$host=rawurldecode($host);
	if($host && !preg_match('/^https?\:\/\//ui',$host))
		$host='http://'.$host;
    $proto="";
	$query="";
	if($flag){
	$parsed=parse_url($host);
	if(!isset($parsed["host"])){
	     return false;
	}
	$path=(isset($parsed["path"]))?$parsed["path"]:"";
	$query=(isset($parsed["query"]))?'?'.$parsed["query"]:"";
	$query=$path.$query;
	$proto=$parsed["scheme"]."://";
	$newhost = $host = $parsed["host"];
	}else{
		$newhost = $host;
	}
	$test = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE",  $host);
	if($test == $host){
		$encoded = idn_to_utf8($host);
		if($encoded != $host){
		$newhost=$encoded;
		}
	}
	
	$ahh='/([^\.]+)\.([^.]{2,7}|рф|cc\.[^.]{2,4}|co\.[^.]{2,4}|(at|com)\.ua)$/ui';
	if(preg_match($ahh,$newhost,$m)){
		
		$newhost=$m[0];
	}
	$_MD5=md5($query);
	return [$_MD5,"$proto$newhost$query"];

    }  
	public function  prepareData(){
		$pdo = \DB::connection("pgstatistic_new")->getPdo();
		$sql="insert into new_views(
         day,
         id_widget,
         hash,
         datetime,
         url,
         ip,
		 found,
		 ids_product,
		 iso
        ) 
		values (?,?,?,?,?,?,?,?,?)
		";   
		$this->Sth=$pdo->prepare($sql);
		return;
		$pdo = \DB::connection()->getPdo();
		
		
		$sql="select t.pad,t.ltd,t.user_id
		,u.name as user_name
		,huppert.manager
		,huppert.name as manager_name
		from widgets t
		inner join users u on u.id=t.user_id
		inner join partner_pads pp on pp.id = t.pad
		inner join user_profiles huppert on huppert.user_id = t.user_id
		where t.id=?";
		$sql="select t.pad,t.ltd,t.user_id
		from widgets t
		where t.id=?";
		$this->widgetsSth=$pdo->prepare($sql);
		var_dump("get prepare");
	}
	
}