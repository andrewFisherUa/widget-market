<?php

namespace App\Videosource;

use Illuminate\Database\Eloquent\Model;

class Stat
{
 private $srcS=[];
 public $widS=[];
     public function getSrcs($id){
	 if(isset($this->srcS[$id])){
	   return $this->srcS[$id];
	  }
	  	               $this->mysthSrc->execute([$id]);
	                   $result = $this->mysthSrc->fetchAll(\PDO::FETCH_ASSOC);
                      if(count($result)!=1){
	                       print "говно закралось в подземелье $id / ".count($result)."\n"; 
	                      return null;
	                  }else{
	                      $this->srcS[$id]=$result[0];
						   }
		return $this->srcS[$id];				   
	 }
     public function getWid($id){
	 if(isset($this->widS[$id])){
	   return $this->widS[$id];
	  }
	         $this->widS[$id]=\App\Models\Widgets\VideoWidget::find($id);
			 return $this->widS[$id];
      }
	 public function reCalcPad($from=null,$to=null){
	 if(!$from || !$to)
	 $from=$to=date("Y-m-d");
	 $pdo = \DB::connection("pgstatistic")->getPdo();
	 $sql="update newvideo_loaded_keys set summa=0,control_summa=0 where control=0 and day BETWEEN '$from' and '$to'";
	 $pdo->exec($sql);
	$sql=" update
    newvideo_loaded_keys
    set summa=summa+?,
	control_summa=control_summa+?
    WHERE country=? and day =? and pid=? and page_key=? and control=0
     ";	
	 $sthUpdateKeys=$pdo->prepare($sql);
	 $sql="select * from newvideo_loaded_keys  where control =0 and day BETWEEN '$from' and '$to' ";
	 $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	 foreach($result as $r){
	 $widget=$this->getWid($r->pid);
	 if(!$r->played_src) continue;
	 if(!$widget) continue;
     
	 if($r->country =="RU"){
	 $val=round($widget->CommissionGroup()->value/1000,4); 
	 $valC=$val;
	 }else{
	 $val=round($widget->ForeignCommissionGroup()->value/1000,4); 
	 $valC=$val;
	 } 
	 
	 $sthUpdateKeys->execute([$val,$valC,$r->country,$r->day,$r->pid,$r->page_key]);
	  }
	 }
     public function reCalcConytrolSrcs($from=null,$to=null){
	 if(!$from || !$to)
	 $from=$to=date("Y-m-d");
	 $pdo = \DB::connection("pgstatistic")->getPdo();
	 $sql="update newvideo_loaded_keys set summa=0,control_summa=0 where control=1 and day BETWEEN '$from' and '$to'";
	 $pdo->exec($sql);
	 $sql="update newvideo_loaded_keys_control set summa=0 where day BETWEEN '$from' and '$to'";
	 $pdo->exec($sql);
	 $sql=" update
    newvideo_loaded_keys_control
    set summa=?
	
    WHERE country=? and day =? and pid=? and page_key=? and id_src=? 
     ";	
	 $sthUpdateKeysControl=$pdo->prepare($sql);
	  $sql=" update
    newvideo_loaded_keys
    set summa=summa+?
	,control_summa = ?
    WHERE country=? and day =? and pid=? and page_key=? and control=1 
     ";	
	 $sthUpdateKeys=$pdo->prepare($sql);
	 print "recaulc control $from to  $to\n";
	 $sql="select * from newvideo_loaded_keys_control  where day BETWEEN '$from' and '$to' ";
	 $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	 foreach($result as $r){
	 $source=$this->getSrcs($r->id_src);
	 if(!$source) continue;
	 $widget=$this->getWid($r->pid);
	 
	 if(!$widget) continue;
	 
		 
	 if($r->country =="RU"){
	 $val=round($source["contr_sum_r"]*$r->cnt/1000,4); 
	 $valC=round($widget->CommissionGroup()->value*$r->cnt/1000,4);
	 }else{
	 $val=round($source["contr_sum_f"]*$r->cnt/1000,4); 
	 $valC=round($widget->ForeignCommissionGroup()->value*$r->cnt/1000,4); 
	
	 } 
	 
	 if($r->page_key=='040d4705-3925-4e14-9213-ec700c4a62dd')
	 {
	 #print $valC."   >>>> ".$r->country." iii\n";
	 }
	 $sthUpdateKeysControl->execute([$val,$r->country,$r->day,$r->pid,$r->page_key,$r->id_src]);
	 $sthUpdateKeys->execute([$val,$valC,$r->country,$r->day,$r->pid,$r->page_key]);
	 }
    }
    public function reCalc($from=null,$to=null){
	if(!$from || !$to)
	$from=$to=date("Y-m-d");
	$pdo = \DB::connection("pgstatistic")->getPdo();
	$mypdo = \DB::connection("mysqlapi")->getPdo(); 
	$pdo_cluck = \DB::connection("pgsql")->getPdo();
	
	$sql="select * from video_ad_sources where id =? ";
	$this->mysthSrc=$mypdo->prepare($sql);
	$sql="select * from video_widgets where id =? ";
	$this->mysthWid=$mypdo->prepare($sql);
	$this->reCalcConytrolSrcs($from,$to);
	$this->reCalcPad($from,$to);
	$sql="insert into  video_sum_stats (
    day,
    country ,
    loaded ,
    played ,
    calc_played ,
    deep ,
    util,
    dosm ,
    clicked,
	complited,
    ctr,
	summa,
 	created_at	)
	select ?,?,?,?,?,?,?,?,?,?,?,?,NOW()
	WHERE NOT EXISTS (SELECT 1 FROM video_sum_stats WHERE  day =? and country=?) 
    ";
$sthInsertKeys=$pdo_cluck->prepare($sql);
$sql="update  video_sum_stats 
    set loaded = ?,
    played =?,
    calc_played =?,
    deep =? ,
    util=?,
    dosm =?,
    clicked=?,
	complited=?,
    ctr =?,
	summa=?,
 	updated_at=NOW()
	WHERE  day =? and country=?
    ";
	$sthUpdateKeys=$pdo_cluck->prepare($sql);
	$sql="select day,country, sum(summa) as summa
,count(*) as loaded
,coalesce(sum(played_src),0) as played
,count(CASE WHEN played_src>0 THEN 1 END) as calculate
, case when cast(count(CASE WHEN played_src>0 THEN 1 END ) as double precision) >0 then  cast(sum(played_src) as double precision)/cast(count(CASE WHEN played_src>0 THEN 1 END ) as double precision)else 0 end  as deep
,case when cast(count(*) as double precision) >0 then  count(CASE WHEN played_src>0 THEN 1 END)*100/cast(count(*) as double precision) else 0 end as util
,case when  cast(sum(played_src) as double precision) >0 then cast(sum(completed_src) as double precision)*100/cast(sum(played_src) as double precision) else 0 end  as dosmotr
,sum(clicked_src) as clicked_src
,coalesce(sum(completed_src),0) as completed
,case when cast(sum(played_src) as double precision) >0 then cast(sum(clicked_src) as double precision)*100/cast(sum(played_src) as double precision)  else 0 end as ctr
from newvideo_loaded_keys where day BETWEEN '$from' and '$to' 
group by day, country";
	  $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS); 
	  foreach($result as $r){
	  #var_dump($r);
	  $sthUpdateKeys->execute([$r->loaded,
	  $r->played,
	  $r->calculate,
	  $r->deep,
	  $r->util,
	  $r->dosmotr,
	  $r->clicked_src,
	  $r->completed,
	  $r->ctr,
	  $r->summa,
	  $r->day,
	  $r->country
	  ]);
	  $sthInsertKeys->execute([$r->day,
	  $r->country,
	  $r->loaded,
	  $r->played,
	  $r->calculate,
	  $r->deep,
	  $r->util,
	  $r->dosmotr,
	  $r->clicked_src,
	  $r->completed,
	  $r->ctr,
	  $r->summa,
	  $r->day,
	  $r->country
	  ]);
	  
	  }
	  
	  $sql=" 	
	  insert into video_statistic_pids (
    day,
    country ,
	pid,
    loaded ,
    played ,
    calc_played ,
    deep ,
    util,
    dosm ,
    clicked,
    ctr,
	summa,
	control_summa,
 	created_at	)
	select ?,?,?,?,?,?,?,?,?,?,?,?,?,NOW()
	WHERE NOT EXISTS (SELECT 1 FROM video_statistic_pids WHERE  day =? and country=? and pid=?) 
	  
	  ";
$sthInsertKeys=$pdo_cluck->prepare($sql);	  

$sql="update  video_statistic_pids  
    set loaded = ?,
    played =?,
    calc_played =?,
    deep =? ,
    util=?,
    dosm =?,
    clicked=?,
    ctr =?,
	summa=?,
	control_summa=?,
 	updated_at=NOW()
	WHERE  day =? and country=?  and pid=?
    ";
	$sthUpdateKeys=$pdo_cluck->prepare($sql);

    $sql="select day,country,pid, sum(summa) as summa
,count(*) as loaded
,sum(played_src) as played
,sum(control_summa) as control_summa
,count(CASE WHEN played_src>0 THEN 1 END) as calculate
, case when cast(count(CASE WHEN played_src>0 THEN 1 END ) as double precision) >0 then  cast(sum(played_src) as double precision)/cast(count(CASE WHEN played_src>0 THEN 1 END ) as double precision)else 0 end  as deep

,case when cast(count(*) as double precision) >0 then  count(CASE WHEN played_src>0 THEN 1 END)*100/cast(count(*) as double precision) else 0 end as util
,case when  cast(sum(played_src) as double precision) >0 then  cast(sum(completed_src) as double precision)*100/cast(sum(played_src) as double precision) else 0 end  as dosmotr
,sum(clicked_src) as clicked_src
,case when cast(sum(played_src) as double precision) >0 then cast(sum(clicked_src) as double precision)*100/cast(sum(played_src) as double precision)  else 0 end as ctr
from newvideo_loaded_keys where day BETWEEN '$from' and '$to' 
group by day, country ,pid";
	   $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	  foreach($result as $r){
	  
	  	  $sthUpdateKeys->execute([$r->loaded,
	  $r->played,
	  $r->calculate,
	  $r->deep,
	  $r->util,
	  $r->dosmotr,
	  $r->clicked_src,
	  $r->ctr,
	  $r->summa,
	  $r->control_summa,
	  $r->day,
	  $r->country,
	   $r->pid
	  ]);
	  
	   $sthInsertKeys->execute([$r->day,
	  $r->country,
	  $r->pid,
	  $r->loaded,
	  $r->played,
	  $r->calculate,
	  $r->deep,
	  $r->util,
	  $r->dosmotr,
	  $r->clicked_src,
	  $r->ctr,
	  $r->summa,
	  $r->control_summa,  
	  $r->day,
	  $r->country,
	  $r->pid
	  ]);
	  
	 
	  
	  }
	  
	 $sql="
	 insert into  video_statistic_pids_control (
    day,
    pid ,
    country,
    id_src ,
    played,
    summa,
    created_at 
    )
	select ?,?,?,?,?,?,NOW()
	WHERE NOT EXISTS (SELECT 1 FROM video_statistic_pids_control WHERE  day =? and pid=? and country=? and id_src=? ) 
	 "; 
	  $sthInsertKeys=$pdo_cluck->prepare($sql);	  
	  
	  $sql="
	update  video_statistic_pids_control 
    set 
    played =?,
    summa =?,
    updated_at=NOW()
    WHERE  day =? and pid=? and country=? and id_src=? 
	 "; 
	 $sthUpdateKeys=$pdo_cluck->prepare($sql);
	  
	 $sql="select day,country,pid,id_src,sum(cnt) as played,sum(summa) as summa 
     from newvideo_loaded_keys_control where day BETWEEN '$from' and '$to' 
     group by day,country,pid,id_src";  
	     $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	  foreach($result as $r){
	  	 
	   $sthUpdateKeys->execute([
	       $r->played,
	       $r->summa,
	       $r->day,
		   $r->pid,
	       $r->country,
		   $r->id_src
	     ]);
		   $sthInsertKeys->execute([$r->day,
		   $r->pid,
	       $r->country,
      	   $r->id_src,
	       $r->played,
	       $r->summa,
	       $r->day,
		   $r->pid,
	       $r->country,
		   $r->id_src
	     ]);
	  }
	  
	$sql="insert into  video_statistic_pads(
    day,
    country ,
    id_src,
    requested,
    started ,
    played,
    completed,
    clicked,
    poteri,
    ctr,
	dosm,
	util,
    created_at
    )
	select ?,?,?,?,?,?,?,?,?,?,?,?,NOW()
	WHERE NOT EXISTS (SELECT 1 FROM video_statistic_pads WHERE  day =? and country=? and id_src=? ) 
";
	  $sthInsertKeys=$pdo_cluck->prepare($sql);	    
	  
	$sql="update video_statistic_pads
    set requested=?,
    started =?,
    played =?,
    completed=?,
    clicked=?,
    poteri=?,
    ctr=?,
	dosm=?,
	util=?,
    updated_at=NOW()
    WHERE  day =? and country=? and id_src=? 
";
	  $sthUpdateKeys=$pdo_cluck->prepare($sql);	   	  
	  $sql=" select country,
    day,
    id_src ,
   requested ,
    started ,
    played ,
    completed,
    clicked,
    100-case when started>0 then cast(played as double precision)*100/cast(started as double precision)  else 100
 end as  poteri,
    case when played>0 then cast(clicked as double precision)*100/cast(played as double precision)  else 0 end as  ctr,
	case when played>0 then cast(completed as double precision)*100/cast(played as double precision)  else 0 end as  dosmotr,
    case when requested>0 then cast(played as double precision)*100/cast(requested as double precision)  else 0 end as  util
 from newvideo_src where day BETWEEN '$from' and '$to' 
  ";
      $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	  foreach($result as $r){
	  $sthUpdateKeys->execute([
		   $r->requested,
		   $r->started,
	       $r->played,
		   $r->completed,
		   $r->clicked,
	       $r->poteri,
	       $r->ctr,
		   $r->dosmotr,
		   $r->util,
		   $r->day,
		   $r->country,
      	   $r->id_src
	     ]);
	  
	   $sthInsertKeys->execute([$r->day,
		   $r->country,
      	   $r->id_src,
		   $r->requested,
		   $r->started,
	       $r->played,
		   $r->completed,
		   $r->clicked,
	       $r->poteri,
	       $r->ctr,
		   $r->dosmotr,
		   $r->util,
		   $r->day,
		   $r->country,
      	   $r->id_src
	     ]);
	  }
	  
	  $sql="
	  insert into video_statistic_pads_on_pid(
	   day,
	country,
	id_src,
	pid,
	played,
	clicked,
	ctr,
	created_at
	  )
	  select ?,?,?,?,?,?,?,NOW()
	 WHERE NOT EXISTS (SELECT 1 FROM video_statistic_pads_on_pid  WHERE day=? and  country=? and id_src=? and pid=? )  
	  
	  ";
	  $sthInsertKeys=$pdo_cluck->prepare($sql);	    
	  
	  $sql="
	  update  video_statistic_pads_on_pid
	set played=?,
	clicked=?,
	ctr=?,
	updated_at=NOW()
	  
	  WHERE day=? and  country=? and id_src=? and pid=? 
	  
	  "; 
  $sthUpdateKeys=$pdo_cluck->prepare($sql);	   		  
    $sql="
	select 
        day,
	country,
	id_src,
	pid,
	played,
	clicks,

       case when played >0 then cast(clicks as double precision)*100/cast(played as double precision) else 0 end as ctr
from
newvideo_src_ctr
	where day BETWEEN '$from' and '$to' 
	";
	 $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	  foreach($result as $r){
	  
	  $sthUpdateKeys->execute([
	       $r->played,
		   $r->clicks,
	       
		   $r->ctr,
		   $r->day,
		   $r->country,
      	   $r->id_src,
		   $r->pid
	     ]);
	   $sthInsertKeys->execute([$r->day,
		   $r->country,
      	   $r->id_src,
		   $r->pid,
		   $r->played,
		   $r->clicks,
	       
		   $r->ctr,
		   $r->day,
		   $r->country,
      	   $r->id_src,
		   $r->pid
	     ]);
	  }

	
	}
}
