<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
    //
 private static $instance=null;
	private $sources=[];
	private $days=[];
	private $money=[];
	private $cacheEvent=[];
	private $pids=[];
	private $VastLinks=[];
	
    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	self::$instance->prepareData();
	}
	return self::$instance;
	
	}
  public function getData(&$arr){
            #return;
	        $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
			$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$day=date("Y-m-d",$time);
			if (!isset($arr[5])){
				var_dump($arr);
				return;
			}
			$req=preg_split("/\s+/",$arr[5]);
			if(!$req) return;
			 $country=trim($arr[2]);
	    	parse_str($req[1], $dd);
			if (!isset($arr[6])){
				var_dump($arr);
				return;
			}
			$site=preg_replace('/\"/','',trim($arr[6]));

			
			if(!$site) return;
			if(!$dd || !isset($dd["data"])){
		    return;
	        }	  
           $data=json_decode($dd["data"],true);
		   if(!$data){
		    return;
	       }
		   
		     if(isset($data["event"])){
		   	if(preg_match('/myVpaid_/',$data["event"])){
				if(isset($data["page_key"]) && ($data["event"]=="myVpaid__skip" || $data["event"]=="myVpaid__stop")){
		
				 $this->tmpSthEvent->execute([0,$data["page_key"],$data["event"],$country, 0,$site]);
				
				if(!isset($this->cacheEvent[$data["event"]])){
				$this->cacheEvent[$data["event"]]=1;
				
				         var_dump(["калимантан",$country,$site,$data]);
				}
				}
		                }
	       }
		    if(isset($data["id_src"])){
		   	if($data["id_src"]=="58" && preg_match('/kinogo/',$site)){
				         #var_dump(["калимантан",$country,$site,$data]);
		                }
	       }
		   	if(isset($data["pid"])){
						if($data["pid"]=="-1"){
				         #var_dump(["калимантан",$data]);
		                }
			}
		   if(!isset($data["event"])){
		    return;
	      }	
		 if(!isset($data["id_src"])){
		    return;
	       }
		   if(!isset($data["page_key"])){
		    return;
	    }	
			if(!isset($data["pid"])){
		    return;
	    }	
	
		$pid = $data["pid"];

		if($pid !=752) return;
		$page_key=$data["page_key"];
		  
		 #if($country!='RU')
		 #$country='CIS';
	 
		  $id_src =  $data["id_src"];
		  $event=$data["event"];
		  if($event=="AdVideoFirstQuartile"){}else{ return; }
		  #print $page_key."/".$id_src."/".$pid." $event \n";
		  return;
		  
		  if(!isset($this->days[$day])){
			  
		  }
		  
		  
		  if(1==1  ||  $country=='UA'){
		   if($event =="AdVideoStart" && !isset($this->cacheEvent[$page_key][$event])){
			 #$this->tmpSthEvent->execute([$page_key,$event,$country,$site]);
			 #$this->cacheEvent[$page_key][$event]=1;  
			 if($pid == 702){
			 print "$pid $event  $country $site\n";
			 }
		   }
		  if($pid  && (($event=="myChange" && $id_src ==0) || $event=="myEmpty" || $event=="myLate"  || $event=="myBk" || $event=="myTrailer" || $event =="myTrailerLoaded" || $event =="myButtonClose")){
			 # $this->tmpSthEvent->execute([$pid,$page_key,$event,$country, $id_src,$site]);
			  #print " $event  $country $site\n";
		  }
		  }
		  return;
		   $itrailer=$site;
		   #print $site." \n"; return;
		   $hosts=parse_url($site);
		   if(isset($hosts["host"]))
		    $site=$hosts["host"];
		   else
		    $site="_";
		   if(!isset($data["pid"])){
		    return;
	       }
           $pid=	$data["pid"]	;  
           if($event =="AdVideoStart" && isset($this->VastLinks[$pid])){		
		   if( $itrailer == "-"){
			  var_dump([$data]); 
			   
		   }else{
			    #var_dump([$id_src]);
		   }
		   #var_dump([$id_src,$arr]);
		  # print "$pid / ".$data["event"]." / $itrailer\n";
		   }
		  
	}
	public function PrepareData(){
		
        #return;
	   $tabulator = \DB::connection()->table("widget_videos")->where("type",3)->get();
	   foreach($tabulator as $tab){
		  $this->VastLinks[$tab->id]=1;
		 # var_dump($tab->id);
	   }
	   $ppdo= \DB::connection("videotest")->getPdo();
	   var_dump($ppdo);
	   	   	$sql="insert into event_log (pid,page_key,
    event,
    country,
	id_src,
	
    url) values (?,?,?,?,?,?)";
	$this->tmpSthEvent=$ppdo->prepare($sql);
	   
   }
public function RegisterData(){
       #print " ---> Готов на новый заход \n";
	   #$tabulator = \DB::connection()->table("widget_videos");
   }		
}