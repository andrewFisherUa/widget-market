<?php
namespace App\LogStat\Video;
class Kinodr{
	private static $instance=null;
	private $requests=[];
	private $nodes=[];
	private $sthProduct;
	private $Sth;
	private $day;
	public static function getInstance(){
	    if(self::$instance==null){
	        self::$instance=new self;
			self::$instance->prepareData();
			
	    }
	    return self::$instance; 
	}
	public function getDataObr(&$arr){
		#var_dump($arr);
		#return;
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		$servername=mb_strtolower($arr[1]);
		$serverip=$arr[2];
		$clientip=$arr[3];
		$referer=preg_replace('/\"/','',trim($arr[8]));
		
		$gets=preg_replace('/\"/','',trim($arr[7]));
				
		if(preg_match('/([^\s]+\.js)(\?|\Z|\s)/ui',$gets,$mt)){
			
			$darta=[];
			$que="";
			$tmp=parse_url($referer);
			$script=$mt[1];
			$host="-";
			$path="-";
			$myRef="-";
			$subid=0;
			$id_src=0;
			
			$serverLink="-";
			if(isset($tmp["host"]))
				$host=$tmp["host"];
			if(isset($tmp["path"]))
				$path=$tmp["path"];
			if(isset($tmp["query"])){
				#$que=rawurldecode($tmp["query"]);
				$que=$tmp["query"];
				parse_str($que, $dd);
				if($dd){
					if(isset($dd["data"])){
						
						$darta=json_decode($dd["data"],1);
						if(!$darta){
							//var_dump($que);
						}else{
							if(isset($darta["id_src"]))
							$id_src=$darta["id_src"];
							if(isset($darta["url"])){
								$tmpc=parse_url($darta["url"]);
								if(isset($tmpc["host"]))
									$serverLink=$tmpc["host"];
								parse_str($darta["url"], $vv);
								#$url=rawurldecode($darta["url"]);
							        if(isset($vv["referer_url"])){
									    $myRef=$vv["referer_url"];
								    }elseif(isset($vv["referer"])){
										$myRef=$vv["referer"];
								    }elseif(isset($vv["target-ref"])){
										$myRef=$vv["target-ref"];
								    }else{
								        //var_dump([$vv,$darta["url"]]);		 	 
								    }
									 
							    
						    }
						}	
						
						#var_dump($darta);
						
					}elseif(isset($dd["subid"])){
						$subid=$dd["subid"];
						#var_dump([$host,$mt[1],$dd["subid"]]);	
				        //var_dump($dd);
					}
				}
			//if($serverLink=="-"){
			//	var_dump($darta);
			//}
			if(!isset($this->nodes[$host][$serverLink][$id_src][$myRef][$script]))
				$this->nodes[$host][$serverLink][$id_src][$myRef][$script]=0;
			    $this->nodes[$host][$serverLink][$id_src][$myRef][$script]++;
					 #$this->requests[$servername][$serverip][$darta["id_src"]][$path]=0;
			#print "$host $serverLink $myRef $id_src $script \n";	
			}
			
		    #var_dump($tmp);    
				#var_dump([$host,$mt[1]]);	
			#var_dump([$mt[1],$referer]);
			#var_dump($referer);
		    //print $gets." ->\n";
		}else{
			#var_dump([$referer,$gets]); 
				#return;
		}
	}	
	public function getData(&$arr){
		$arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		$servername=mb_strtolower($arr[1]);
		$serverip=$arr[2];
		$clientip=$arr[3];
		$referer=preg_replace('/\"/','',trim($arr[8]));
		#var_dump($arr);
		#return;
		#$ip = $arr[1];
		#$region=$arr[3];
		$time = strtotime($arr[0]);
		#$dtime=$time-($time%180);
		$datetime=date("Y-m-d H:i:s",$time);
		#$ddatetime=date("Y-m-d H:i:s",$dtime);
		#$day=date("Y-m-d",$time);
		#$country=trim($arr[2]);
		#$region=trim($arr[3]);
		#$day=date("Y-m-d",$time);
		$req=preg_split("/\s+/",$arr[7]);
		if(!$req) return;
		parse_str($req[1], $dd);
		 if(!$dd){
		    return;
	       }	
		   
        $vdv=array_keys($dd);		   
		if(!$vdv) return;
		$errorflag=0;
		$path=$vdv[0];
		   switch($servername){
			   case "video.kinodrevo.ru":
			   case "kinodrevo.ru":
			   if($serverip!="185.60.135.248")
				   $errorflag=1;
			   break;
			   case "mp.vidlent.ru":
			   if($serverip!="92.63.111.110")
				   $errorflag=1;
			   break;
			   default:
			   
			   #echo $servername.":".$serverip."\n";
			   break;
		   }
		   #$this->requests[$servername][$serverip][$path]=1;
		   #$vdv=array_keys($dd);
		   if(isset($vdv[1]) && $vdv[1]=="data"){
			 $darta=json_decode($dd["data"],1);
			 $rurl=null;
			 if(isset($darta["url"]))
             $rurl=rawurldecode($darta["url"]);				 
			 
			 if(isset($darta["id_src"])){
				 if(in_array($darta["id_src"],array(13,14))){
					 if($servername=="mp.vidlent.ru"){
						  
					 }else{
					 $errorflag=1;
					 }
				 }
				 if($errorflag){
					  $name ="-";
					  if(isset($this->links[$darta["id_src"]]))
						  $name=$this->links[$darta["id_src"]];
					 $dui=[
					 $servername
					 ,$serverip
					 ,$clientip
					 ,$darta["id_src"]
					 ,$path
					 ,$name
					 ,$referer
					 ,$rurl
					 ,$datetime
					 ];
					 $this->sthError->execute($dui);
					//var_dump($dui); 
				 }
				 #$this->requests[$servername][$serverip][$path][]=1;
				 if(!isset($this->requests[$servername][$serverip][$darta["id_src"]][$path]))
					 $this->requests[$servername][$serverip][$darta["id_src"]][$path]=0;
				     $this->requests[$servername][$serverip][$darta["id_src"]][$path]++;
			  //var_dump([$servername,$darta]);	   	  
			 }else{
				 var_dump([$servername,$darta]);	   	   
			 }
			 
		     
		   }
		
	}
	 public function ZabirCheck(){
		 $pdo=\DB::connection("pgstatistic_new_video")->getPdo();
		 #return;
		 $sql="insert into node_callback_ref (
    from_host,
    link_host,
    link_ref,
    is_src,
	script_name,
	src_name,
    cnt 
)
select ?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM node_callback_ref  WHERE 
	from_host =? and
    link_host =? and
    link_ref =? and
    is_src =? and 
	script_name=?
	)
	";
	$sth=$pdo->prepare($sql);
	
		 $sql="update node_callback_ref 
    set src_name=?,
	    cnt =cnt+?
    WHERE 
	from_host =? and
    link_host =? and
    link_ref =? and
    is_src =? and 
	script_name=?
	";
	$sthU=$pdo->prepare($sql);
	


		 #$this->nodes[$host][$serverLink][$id_src][$myRef]=0;
		 	foreach($this->nodes as $host=>$serlinks){
				foreach($serlinks as $serlink=>$srcs){
					foreach($srcs as $id_src=>$myrefs){
						
							$name ="-";
					        if(isset($this->links[$id_src]))
						    $name=$this->links[$id_src];
						foreach($myrefs as $myref=>$scripts){
							
							foreach($scripts as $script=>$cnt){
							$sthU->execute([$name,$cnt,$host,$serlink,$myref,$id_src,$script]);	
							 $cn=$sthU->rowCount();
						     if(!$cn){	
						    $sth->execute([$host,$serlink,$myref,$id_src,$script,$name,$cnt
							,$host,$serlink,$myref,$id_src,$script]);
					        print "$host,$serlink,$myref,$id_src,$name,$script,$cnt\n";	
							 }
							}
					    }
					}
				}
            }		 
	 }	 
	
     public function Zabir(){
		 $pdo=\DB::connection("pgstatistic_new_video")->getPdo();
		 $sql="
        insert into kinodrevo_frame (
    domain,
    ip, 
    id_src,
    path,
    src_name,
    cnt
    ) select ?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM kinodrevo_frame  WHERE domain=? and ip=? and id_src=? and path=?)
	";
	$sth=$pdo->prepare($sql);
	 $sql="
    update kinodrevo_frame 
    
    set src_name=?,
    cnt=cnt+?
	 WHERE domain=? and ip=? and id_src=? and path=?
	";
	$sthU=$pdo->prepare($sql);
		 foreach($this->requests as $servername=>$ips){
			 foreach($ips as $ip=>$srcs){
				  foreach($srcs as $src=>$pathes){
					  
					  $name ="-";
					  if(isset($this->links[$src]))
						  $name=$this->links[$src];
					  foreach($pathes as $path=>$cnt){
						  $di=[
						  $name
						  ,$cnt
						  ,$servername
						  ,$ip
						  ,$src
						  ,$path
						  ];
						  $sthU->execute($di);
						  $cn=$sthU->rowCount();
						  if(!$cn){
						  
						  $di=[
						  $servername
						  ,$ip
						  ,$src
						  ,$path
						  ,$name
						  ,$cnt
						  ,$servername
						  ,$ip
						  ,$src
						  ,$path
						  ];
						  $sth->execute($di);
						 	  
						  }
			              print "$servername,$ip,$src,$name,$path,$cnt\n";	 
					  }
				  }	  
			 
			 }
		 }
		#
	}	
	private function prepareData(){
		$this->links=[];
		
		$sql="select * from links where id in(14,13)";
		$sql="select * from links";
		$data=\DB::connection("videotest")->select($sql);
		foreach($data as $d){
			
			$this->links[$d->id]=$d->title;
		}
		$sql="
		insert into kinodrevo_frame_error( 
    domain,
    ip,
    client_ip,
    id_src,
    path,
    src_name,
    referer,
    to_url,
	datetime
	)
    values (?,?,?,?,?,?,?,?,?)
		";
		$this->sthError=\DB::connection("pgstatistic_new_video")->getPdo()->prepare($sql);
		#pgstatistic_new_video
	}
}	

