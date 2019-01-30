<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class Video extends Command
{
use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:video';

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
	try{
        print "начало\n";
		$this->datapids=["no"=>[],"yes"=>[]];
		$mypdo = \DB::connection("mysqlapi")->getPdo(); 
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$hour=date("H");
		if($hour=="03" || 1==0){
		
		$contrday=date("Y-m-d");
		$sql="delete from  newvideo_loaded where day <'$contrday';
         delete from  newvideo_loaded_keys where day <'$contrday';
         delete from newvideo_loaded_keys_control where day <'$contrday';
         delete from  newvideo_src where day <'$contrday';
		 delete from  newvideo_src_ctr where day <'$contrday';
		 
		";
		$pdo->exec($sql);

		}
	$sql="
	 insert into videostatistic_frameads(id_link)
	 select ? 
	 WHERE NOT EXISTS (SELECT 1 FROM videostatistic_frameads WHERE id_link=?) 
	";
	$this->cachedFrameSth=$pdo->prepare($sql);
        $sql="select * from video_widgets where id =? ";
		$this->mysthWid=$mypdo->prepare($sql);
		
		
		$sql="select id_link from videostatistic_frameads ";
	    $relinke = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	    $this->excentingSrc=[];
		
		foreach($relinke as $o){
	    $this->excentingSrc[$o["id_link"]]=1;
	    }
		$this->loadedSrcClicks=[];
		$this->cachedGraphicsBanner=[];
		$this->cachedFrameLinks=[];
		$this->loadedSrcEvents=[];
		$this->validWidgets=[];
		$this->NovalidWidgets=[];
		$this->loadedWidgets=[];
		$this->loadedEvents=[];
		$this->commonStrike();
		$logMegogo='/home/myrobot/data/videostatistic/framevideo.market-place.su-videostat.log';
        $confMegogo =["mgg_start_ad"=>"filterPlayMedia"
		,"mgg_end_ad"=>"complete"
		,"mgg_click_ad"=>"clickThrough"
		,"mgg_imp_ad"=>"startPlayMedia"];		
		$logVroll='/home/myrobot/data/videostatistic/videoroll.market-place.su-videostat.log';	
		$confVroll =["has_reklam_videoroll" => "filterPlayMedia"
		,"end_watch" => "complete"
		,"vid_has_advert" => "startPlayMedia"];	
		$logTvigle='/home/myrobot/data/videostatistic/tvigle.market-place.su-videostat.log';	
		$confTvigle =["advert_start" => "filterPlayMedia"
		,"advert_complete" => "complete"
		,"advert_click" => "clickThrough"
		,"on_api_ready" => "startPlayMedia"];	
		$this->specialStrike($logMegogo,$confMegogo);
		$this->specialStrike($logVroll,$confVroll);
		$this->specialStrike($logTvigle,$confTvigle);
		print_r($this->datapids);
		 $this->FillLoadedPid();
         $new=new \App\Videosource\Stat();
		 $new->reCalc();
		 }catch(\Exception $e){
		 file_put_contents('/home/myrobot/data/videostatistic/cacheExceptions',date('Y-m-d H:i:s').' : Выброшено исключение: '.  $e->getMessage()." / ".$e->getLine(). "\n",\FILE_APPEND);
         echo 'Выброшено исключение: '.  $e->getMessage()." / ".$e->getLine(). "\n";
         }
	    }
	public function checkFrameSrc($id)
	{
	
	if(isset($this->cachedFrameLinks[$id])) return;
	$this->cachedFrameLinks[$id]=1;
	$this->cachedFrameSth->execute([$id,$id]);
	print $id." +\n";
    }		
	private function checkPid($pid)
    {
	if(isset($this->validWidgets[$pid])) return true;
	if(isset($this->NovalidWidgets[$pid])) return false;
		              $this->mysthWid->execute([$pid]);
	                  $result = $this->mysthWid->fetchAll(\PDO::FETCH_ASSOC);
                      if(count($result)!=1){
					  $this->NovalidWidgets[$pid]=1;
	                     print "говно закралось в подземелье $pid / ".count($result)."\n"; 
	                     return false;
	                  }else{
	                      $this->validWidgets[$pid]=$result[0];
						  return true;
					  }
	                  
	return false;
    }	
    private function registerDopSrc($day,$country,$event,$id_src){
   
	    if(!isset($this->loadedSrcEvents[$country]))
	    $this->loadedSrcEvents[$country]=[];
    	if(!isset($this->loadedSrcEvents[$country][$day]))
	    $this->loadedSrcEvents[$country][$day]=[];
		if(!isset($this->loadedSrcEvents[$country][$day][$id_src]))
	    $this->loadedSrcEvents[$country][$day][$id_src]=[];
		if(!isset($this->loadedSrcEvents[$country][$day][$id_src][$event]))
	    $this->loadedSrcEvents[$country][$day][$id_src][$event]=1;
		else
	    $this->loadedSrcEvents[$country][$day][$id_src][$event]++;
	
	
	}	
	
	private function registerEventSrc($pid,$day,$country,$key,$event,$id_src)
    {
	#"filterPlayMedia"
    if($event=="clickThrough" || $event=="filterPlayMedia"){
		if(!isset($this->loadedSrcClicks[$country]))
	    $this->loadedSrcClicks[$country]=[];
    	if(!isset($this->loadedSrcClicks[$country][$day]))
	    $this->loadedSrcClicks[$country][$day]=[];
		if(!isset($this->loadedSrcClicks[$country][$day][$id_src]))
	    $this->loadedSrcClicks[$country][$day][$id_src]=[];
		if(!isset($this->loadedSrcClicks[$country][$day][$id_src][$pid]))
	    $this->loadedSrcClicks[$country][$day][$id_src][$pid]=[];
		if(!isset($this->loadedSrcClicks[$country][$day][$id_src][$pid][$event]))
	    $this->loadedSrcClicks[$country][$day][$id_src][$pid][$event]=1;
		else
		$this->loadedSrcClicks[$country][$day][$id_src][$pid][$event]++;
    }
	
	if($event=="srcRequest"){
	    if(!isset($this->loadedSrcEvents[$country]))
	    $this->loadedSrcEvents[$country]=[];
    	if(!isset($this->loadedSrcEvents[$country][$day]))
	    $this->loadedSrcEvents[$country][$day]=[];
		if(!isset($this->loadedSrcEvents[$country][$day][$id_src]))
	    $this->loadedSrcEvents[$country][$day][$id_src]=[];
		if(!isset($this->loadedSrcEvents[$country][$day][$id_src][$event]))
	    $this->loadedSrcEvents[$country][$day][$id_src][$event]=1;
		else
	    $this->loadedSrcEvents[$country][$day][$id_src][$event]++;
	}
	
	
	
	
	    if(!isset($this->loadedEvents[$country]))
	     $this->loadedEvents[$country]=[];
    	if(!isset($this->loadedEvents[$country][$day]))
	    $this->loadedEvents[$country][$day]=[];
	    if(!isset($this->loadedEvents[$country][$day][$pid]))
		$this->loadedEvents[$country][$day][$pid]=[];
		if(!isset($this->loadedEvents[$country][$day][$pid][$key]))
		$this->loadedEvents[$country][$day][$pid][$key]=[];
        if(!isset($this->loadedEvents[$country][$day][$pid][$key][$event]))
		$this->loadedEvents[$country][$day][$pid][$key][$event]=[];	
        if(!isset($this->loadedEvents[$country][$day][$pid][$key][$event][$id_src]))
		$this->loadedEvents[$country][$day][$pid][$key][$event][$id_src]=1;			
	    else
        $this->loadedEvents[$country][$day][$pid][$key][$event][$id_src]++;
    }		
	private function registerPlayedPid($pid,$day,$country,$id_src)
    {
	    if(!isset($this->PLayedWidgets[$country]))
	    $this->PLayedWidgets[$country]=[];
    	if(!isset($this->PLayedWidgets[$country][$day]))
	    $this->PLayedWidgets[$country][$day]=[];
	    if(!isset($this->PLayedWidgets[$country][$day][$pid]))
	    $this->PLayedWidgets[$country][$day][$pid]=1;
	    else
        $this->PLayedWidgets[$country][$day][$pid]++;
    }	
	private function registerLoadedPid($pid,$day,$country)
    {
	    if(!isset($this->loadedWidgets[$country]))
	    $this->loadedWidgets[$country]=[];
    	if(!isset($this->loadedWidgets[$country][$day]))
	    $this->loadedWidgets[$country][$day]=[];
	    if(!isset($this->loadedWidgets[$country][$day][$pid]))
	    $this->loadedWidgets[$country][$day][$pid]=1;
	    else
        $this->loadedWidgets[$country][$day][$pid]++;
    }
	private function registerGraphics($id_src,$datetime,$event)
    {
	     if(!isset($this->cachedGraphicsBanner[$datetime])){
		 $this->cachedGraphicsBanner[$datetime]=[];
		 
		 }
		 if(!isset($this->cachedGraphicsBanner[$datetime][$id_src])){
		 $this->cachedGraphicsBanner[$datetime][$id_src]=1;
		 }
		 else{
		 $this->cachedGraphicsBanner[$datetime][$id_src]++;
		 }
	    
    }	
	
    private function FillLoadedPid()
    {
	$pdo = \DB::connection("pgstatistic")->getPdo();
	if(1==0){
	#$sql="truncate table newvideo_loaded";
	#$pdo->exec($sql);
	#$sql="truncate table newvideo_loaded_keys";
	#$pdo->exec($sql);
	#$sql="truncate table newvideo_loaded_keys_control";
	#$pdo->exec($sql);
	}
	$sql="insert into newvideo_loaded (country,day,pid,loaded_widgets,played_src)
	select ?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM newvideo_loaded WHERE country=? and day =? and pid=?) 
	";
	$sthInsertPids=$pdo->prepare($sql);
		$sql="update  newvideo_loaded
         set loaded_widgets=loaded_widgets+?
		 ,played_src = played_src+?
		WHERE country=? and day =? and pid=?
	";
	$sthUpdatePids=$pdo->prepare($sql);
	
	$sql=" insert into 
	newvideo_loaded_keys (
    country,
    day,
    pid,
	page_key,
	control,
    loaded_src,
    played_src,
	completed_src,
	clicked_src
	)
	select ?,?,?,?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM newvideo_loaded_keys WHERE country=? and day =? and pid=? and page_key=?) 
 ";
	$sthInsertKeys=$pdo->prepare($sql);
$sql=" update
    newvideo_loaded_keys
    set loaded_src = loaded_src+?,
    played_src = played_src+?
	
	 WHERE country=? and day =? and pid=? and page_key=? 
 ";	
	$sthUdateKeys=$pdo->prepare($sql);
	
$sql="
insert into 
newvideo_loaded_keys_control(
    country, 
    day,
    pid,
	page_key,
	id_src,
    cnt)
select ?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM newvideo_loaded_keys_control WHERE country=? and day =? and pid=? and page_key=? and id_src=?) 
";	
$sthInsertKeysControl=$pdo->prepare($sql);
$sql=" update
    newvideo_loaded_keys_control
    set cnt=cnt+?
    WHERE country=? and day =? and pid=? and page_key=? and id_src=? 
 ";	
	$sthUpdateKeysControl=$pdo->prepare($sql);
	foreach($this->loadedWidgets as $country => $days){
	    foreach($days as $day => $pids){
	    foreach($pids as $pid=>$cnt){
		 $sthUpdatePids->execute([$cnt,0,$country,$day,$pid]);
		 $sthInsertPids->execute([$country,$day,$pid,$cnt,0,$country,$day,$pid]);
		 #print $country.":".$day.":".$pid.":".$cnt."\n";
		}
	   }
	 }
	 	foreach($this->PLayedWidgets as $country => $days){
	    foreach($days as $day => $pids){
	    foreach($pids as $pid=>$cnt){
		 $sthUpdatePids->execute([0,$cnt,$country,$day,$pid]);
		 $sthInsertPids->execute([$country,$day,$pid,0,$cnt,$country,$day,$pid]);
		 #print $country.":".$day.":".$pid.":".$cnt."\n";
		 }
	    }
	   }
	 	foreach($this->loadedEvents as $country => $days){
	    foreach($days as $day => $pids){
	    foreach($pids as $pid=>$keys){
		$control=$this->validWidgets[$pid]["control"];
		if(!$control)
		$control=0;
		foreach($keys as $key=>$events){
		 $loaded=0;
		 $played=0;
		 $complete=0;
		 $clicke=0;

		  if(isset($events["srcRequest"])){
		  $loaded=count($events["srcRequest"]);

		  }
		  if(isset($events["filterPlayMedia"])){
		  $played=array_sum($events["filterPlayMedia"]);

		  }  
		  if(isset($events["complete"])){
		  $complete=array_sum($events["complete"]);
		 
		  }
		  if(isset($events["clickThrough"])){
		  $clicke=array_sum($events["clickThrough"]);
		  }

		if($played && $complete){
		}
		if($control){
		
		  if(isset($events["filterPlayMedia"])){
		       foreach($events["filterPlayMedia"] as $id_src=>$cnt11){
			    #print $id_src." > ".$cnt11."\n";
				$sthUpdateKeysControl->execute([$cnt11,$country,$day,$pid,$key,$id_src]);
				$sthInsertKeysControl->execute([$country,$day,$pid,$key,$id_src,$cnt11,$country,$day,$pid,$key,$id_src]);
			   }
			}
		}
		$sthUdateKeys->execute([$loaded ,$played,$country,$day,$pid,$key]);
		$sthInsertKeys->execute([$country,$day,$pid,$key,$control,$loaded ,$played,$complete,$clicke,$country,$day,$pid,$key]);
		
		#print $country.":".$day.":".$pid.":  $key :  $loaded  $played  ".serialize($events)."\n";
		#foreach($events as $event=>$cnt){
		 #print $country.":".$day.":".$pid.":  $key :  $event : $cnt ++\n";
		 #}
		 }
		# $sthUpdatePids->execute([0,$cnt,$country,$day,$pid]);
		# $sthInsertPids->execute([$country,$day,$pid,0,$cnt,$country,$day,$pid]);
		 
		 }
	    }
	   }
	   $sql="
	insert into newvideo_src (
    country,
    day,
    id_src ,
	requested ,
    started ,
    played ,
    completed,
	clicked
    )
	 select ?,?,?,?,?,?,?,? 
	 WHERE NOT EXISTS (SELECT 1 FROM newvideo_src WHERE country=? and day =? and id_src=?) 
	   ";
	$sthInsertKeys=$pdo->prepare($sql);   
		   $sql="
	update newvideo_src set
    requested =requested+?,
    started =started+?,
    played =played+?,
    completed =completed+?,
	clicked =clicked+?
    WHERE country=? and day =? and id_src=?
	   ";   
	$sthUpdateKeys=$pdo->prepare($sql);
	   foreach($this->loadedSrcEvents as $country => $days){
	    foreach($days as $day => $srcs){
		   foreach($srcs as $id_src=>$events){
		   $requested =0;
    $started =0;
    $played =0;
    $completed =0;
	 $clicked =0;

		   if(isset($events["srcRequest"])){
		   $requested=$events["srcRequest"];
		   }
		   if(isset($events["startPlayMedia"])){
		   $started=$events["startPlayMedia"];
		   }
		   if(isset($events["filterPlayMedia"])){
		   $played=$events["filterPlayMedia"];
		   }
		   if(isset($events["complete"])){
		   $completed=$events["complete"];
		   }	
 if(isset($events["clickThrough"])){
		  $clicked=$events["clickThrough"];
		   }		   
		   #if($id_src==54 || $id_src==3 || $id_src==36)
		   #print " $country /  $day   $id_src /  $requested $started $played $completed $clicked\n"; 
		  		$sthUpdateKeys->execute([$requested,$started,$played,$completed,$clicked,$country,$day,$id_src]);
		        $sthInsertKeys->execute([$country,$day,$id_src,$requested,$started,$played,$completed,$clicked,$country,$day,$id_src]); 
		   }

	      }
		}
		
	$pdo = \DB::connection("pgstatistic")->getPdo();
	$sql="insert into  videostatistic_graph (datetime,id_src,cnt)
	select ?,?,? 
	WHERE NOT EXISTS (SELECT 1 FROM videostatistic_graph WHERE datetime=? and id_src=?) 
	";
	$sthInsert=$pdo->prepare($sql);
	$sql="update  videostatistic_graph set cnt=cnt+?
      WHERE datetime=? and id_src=?
	";
	$sthUpdate=$pdo->prepare($sql);	
    foreach($this->cachedGraphicsBanner as $time=>$srcs){
	 foreach($srcs as $id_src=>$cnt){
	   $sthUpdate->execute([$cnt,$time,$id_src]);
	   $sthInsert->execute([$time,$id_src,$cnt,$time,$id_src]);
	   #print $time." $id_src $cnt\n";
	 }
	
	}
	$sql="
	insert into newvideo_src_ctr (
	day,
	country,
	id_src,
	pid,
	clicks,
	played
	)
	select ?,?,?,?,?,?
	WHERE NOT EXISTS (SELECT 1 FROM newvideo_src_ctr WHERE day=? and  country=? and id_src=? and pid=?) 
	";
	$sthInsert=$pdo->prepare($sql);
	$sql="
	update newvideo_src_ctr set clicks=clicks+?,played=played+?
    WHERE day=? and  country=? and id_src=? and pid=?
	";
	$sthUpdate=$pdo->prepare($sql);	
	

		   foreach($this->loadedSrcClicks as $country => $days){
	       foreach($days as $day => $srcs){
		   foreach($srcs as $id_src=>$pids){
		   foreach($pids as $pid=>$events){
		   $clicked=0;
		   $played=0;
		   
		     if(isset($events["filterPlayMedia"])){
		   $played=$events["filterPlayMedia"];
		   }
		 
          if(isset($events["clickThrough"])){
		  $clicked=$events["clickThrough"];
		   }		   
		   
		   #print " $day $country $id_src  $pid  $cnt $clicked,$played \n";
		   $sthUpdate->execute([$clicked,$played,$day,$country,$id_src,$pid]);
		   $sthInsert->execute([$day,$country,$id_src,$pid,$clicked,$played,$day,$country,$id_src,$pid]);
		   
		   }
		   }
		   }
		   }
	
	
	 
    }	
    public function commonStrike(){
       $tmp_file="/home/myrobot/data/videostatistic/largestat_".time()."_.log";
	   $cmd ="cp -p /home/myrobot/data/videostatistic/api.market-place.su-videostat.log $tmp_file && cat /dev/null > /home/myrobot/data/videostatistic/api.market-place.su-videostat.log";
	   `$cmd`;
	   
	 $handle = @fopen($tmp_file, "r");
     if ($handle) {
        while (($buffer = fgets($handle, 4096)) !== false) {
		    $l = str_replace("\n", "", $buffer);
			$tmp=preg_split("/\s+\:\s+/",$l);
			if(!$tmp || count($tmp)<5) continue;
	        $req=preg_split("/\s+/",$tmp[5]);
			$ip=$tmp[1];
			parse_str($req[1], $arr);
			 if(!isset($arr["data"])){
			  $ths->failContinueRead([]);
	           continue;
	         }
			 $country=trim($tmp[2]);
			 if($country!='RU')
			 $country='SNG';
			 $tmp[0]=preg_replace("/^\[|\]$/","",$tmp[0]);
	         $time = strtotime($tmp[0]);
	         if(!isset($ccs["1"])){
	        # print date("Y-m-d H:i:s",$time)."\n";
	         $ccs["1"]=1;
	         }
	         $time=$time-($time%180);
	         $datetime=date("Y-m-d H:i:s",$time);
	         $day=date("Y-m-d",$time);
			 $data=json_decode($arr["data"],true);
			
			   
			 if(!isset($data["fromUrl"])){
			 $this->failContinueRead([]);
			    continue;
			 }
			
             $url=urldecode($data["fromUrl"]);
			#              $brom=$data["brom"];
	         $tdu=parse_url($url);
	         if(isset($tdu["host"]))
	         $host=$tdu["host"];
	         else
	         $host="";
			 if(!$host){
			 #$this->failContinueRead([]);
			 #continue;
			 }
			 if(!isset($data["event"])){
			 $this->failContinueRead([]);
			  continue;
			 }
	         $eventName=$data["event"];
			 if(!isset($data["pid"])){
			 $this->failContinueRead([]);
			    continue;
			 }
			

			 $pid=$data["pid"];	
			 	if(!$this->checkPid($pid)) continue;	
             if($eventName=="loadWidget"){
			
			 $this->registerGraphics(0,$datetime,$eventName);
			 
			 $this->registerLoadedPid($pid,$day,$country);
			 }
			  if(!isset($data["id_src"])){
			  $this->failContinueRead([]);
			  continue;
			  }
			 $id_src  = $data["id_src"];
             if($id_src ==5){
			// var_dump($data);
			print $eventName."\n";
			 }
			 if(!isset($data["key"])){

			 $this->failContinueRead([]);
			 continue;
			 }
			 
			 $dataKey=trim($data["key"]);
			 $this->datakeys[$dataKey]=1;
			 
			 if($eventName=="srcRequest"){ 
			 $this->registerEventSrc($pid,$day,$country,$dataKey,$eventName,$id_src);
			 }
			 if(isset($this->excentingSrc[$id_src])){
			
			 $this->failContinueRead([]);
			 continue;
			 }
			 if($eventName=="filterPlayMedia"){ 
			 $this->registerPlayedPid($pid,$day,$country,$id_src);
			 } 
			 
			 
			 if($eventName=="filterPlayMedia" || $eventName=="complete" || $eventName=="clickThrough"){ 
			 $this->registerEventSrc($pid,$day,$country,$dataKey,$eventName,$id_src);
			 }
			 if($eventName=="filterPlayMedia"){
			 $this->registerGraphics($id_src,$datetime,$eventName);
			 }
			 if($eventName=="startPlayMedia" || $eventName=="filterPlayMedia" || $eventName=="complete" || $eventName=="clickThrough"){ 
			  $this->registerDopSrc($day,$country,$eventName,$id_src);
			  #print $id_src." :: ".$eventName."\n";
			  }
		    }
	    }
	     $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
    }
    public function specialStrike($configFile,$configArr){
      $tmp_file="/home/myrobot/data/videostatistic/largestat_".time()."_.log";
	  $cmd ="cp -p $configFile $tmp_file && cat /dev/null > $configFile";
	 `$cmd`;
	  $handle = @fopen($tmp_file, "r");
      if ($handle) {
        while (($buffer = fgets($handle, 4096)) !== false) {
		    $l = str_replace("\n", "", $buffer);
			 $tmp=preg_split("/\s+\:\s+/",$l);
			 if(!$tmp || count($tmp)<5) continue;
			 $ip=$tmp[1];
			 if(isset($tmp[5])){
	         $req=preg_split("/\s+/",$tmp[5]);
			 }
			 parse_str($req[1], $arr);
			 if(!isset($arr["data"])){
			   $this->failContinueRead([]);
	           continue;
	         }	                                
			 $country=trim($tmp[2]);
			 if($country!='RU')
			 $country='SNG';
			 $tmp[0]=preg_replace("/^\[|\]$/","",$tmp[0]);
	         $time = strtotime($tmp[0]);
	         if(!isset($ccs["1"])){
	         #print date("Y-m-d H:i:s",$time)."\n";
	         $ccs["1"]=1;
	         }
	         $time=$time-($time%180);
	         $datetime=date("Y-m-d H:i:s",$time);
	         $day=date("Y-m-d",$time);
			  $data=json_decode($arr["data"],true);
			 if(!isset($data["site"])){
			 $this->failContinueRead([]);
			 continue;
			 }
			
			  $host=trim($data["site"]);
			 if(!isset($data["event"])){
			 $this->failContinueRead([]);
			    continue;
			 }
			 $eventName=$data["event"];
			 if(!isset($configArr[$eventName]) || !$configArr[$eventName]){
			 	$this->failContinueRead([]);
			    continue;
			 }
			 $eventName=$configArr[$eventName];
			  if(!isset($data["mypid"]) || $data["mypid"] =="-" || $data["mypid"] =="11"){
			  $this->failContinueRead([]);
			  continue;
			  }
			  if($data["mypid"]=="11"){
			  print_r([$eventName,$ip,$data]);
			  }

			  $pid=$data["mypid"];
			  if(!$this->checkPid($pid)) continue;	
			  if(!isset($data["id_src"])){
			  $this->failContinueRead([]);
			  continue;
			  }
			 $id_src= $data["id_src"];
			 if($eventName=="filterPlayMedia"){ 
			 $this->registerPlayedPid($pid,$day,$country,$id_src);
			 }
			 if(!isset($data["key"]) || $data["key"]=="klu" || $data["key"]==""){
             $this->failContinueRead([]);
			 continue;
		     }
			 $dataKey=trim($data["key"]);
			 if($eventName=="filterPlayMedia" || $eventName=="complete" || $eventName=="clickThrough"){ 
			 $this->registerEventSrc($pid,$day,$country,$dataKey,$eventName,$id_src);
			 }
			 if($eventName=="filterPlayMedia"){
			 $this->registerGraphics($id_src,$datetime,$eventName);
			 }
			 if($eventName=="startPlayMedia" || $eventName=="filterPlayMedia" || $eventName=="complete" || $eventName=="clickThrough"){ 
			 $this->registerDopSrc($day,$country,$eventName,$id_src);
			 
			 # print $id_src." :: ".$eventName."\n";
			  }
			  $this->checkFrameSrc($id_src);
			 #print $host." $id_src $ip $pid $eventName\n";
			 #$res=$this->chackPacket($l);
	    }
      }	  
	  $cmd ="rm  -f  $tmp_file";
	 `$cmd`;	
    }   
   
   
   
	    private function failContinueRead($args){
		}
	private function AddPlus($key,&$arr){
	if($key!=3) return;
	if(!isset($arr[$key]))
	$arr[$key]=1;
	else
	$arr[$key]++;
	}
    private function failRead($args){
	print_r( $args);
	print "\n";
	return [];
	
    switch($args["events"]){
	case "no pid":

	break;

	}
	return [];
	print $args["events"]."\n";
	print_r( $args["data"]["id_src"]);
	print "\n";
	return [];
	}	
  	
}
