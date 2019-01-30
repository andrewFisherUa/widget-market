<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class Calculator // extends Model
{
   public $pidcomission=[];
   public $widcomission=[];
   public $linkcomission=[];
   public $pidscontrol=[];
   public $pids=[]; 
   public $blocks=[];
   public $pidlinkcontrol=[];
   public $glubinacommission=[];
   public $cheapcommission=[];
   public function StartDay($from=null,$to=null){
   if(!$from || !$to){
       $from=$to=date("Y-m-d");
       
      }
	  $this->prepareData();
	  $this->makeCommon($from,$to);
	  $this->makeControl($from,$to);
	  $this->makeBlock($from,$to);
	  $pdo = \DB::connection("videotest")->getPdo();
	  $sql="insert into pid_summa (pid
	  ,day
	  ,country
	  ,summa
	  ,control_summa
	  ,loaded
	  ,deep
	  ,util
	  ,calculate
	  ,played
	  ,completed
	  ,clicks
	  ,started
	  ,control_links_summa
	  ,nocontrol_links_summa
	  ,second
	  ,second_summa
	  ,second_all
	  ,second_cheap
	  ,second_cheap_all
	  ,second_cheap_summa
	  ,lease_summa)
	  select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
	  WHERE NOT EXISTS (SELECT 1 FROM pid_summa WHERE pid=? and day =? and country =? ) ";
	  $sthInsertPids=$pdo->prepare($sql);
	   $sql="update pid_summa set summa=? ,control_summa =?,loaded=?,deep=?,util=?,calculate=?,played=?,completed=?,clicks=?,started=?
	   ,control_links_summa=?
	   ,nocontrol_links_summa=?
	   ,second=?
	   ,second_summa=?
	   ,second_all=?
	   ,second_cheap=?
	   ,second_cheap_all=?
	   ,second_cheap_summa=?
	   ,lease_summa=?
	   WHERE pid=? and day =? and country =? ";
	  $sthUpdatePids=$pdo->prepare($sql);
	  foreach($this->pids as $day=>$pids){
	       foreach($pids as $pid=>$countries){
		    foreach($countries as $country=>$sum){
				$sum["control_links_summa"]=0 ;
				$sum["nocontrol_links_summa"]=0 ;
				
				         if(1==1 || $pid==752){
						  if(isset($this->pidscontrol[$day][$pid][$country]))	 
						  {
							 if(isset($this->pidscontrol[$day][$pid][$country][1])){
							 $sum["control_links_summa"]	 =$this->pidscontrol[$day][$pid][$country][1];
							  #echo $day."/".$pid."/".$country."\n";
							 }  
							 if(isset($this->pidscontrol[$day][$pid][$country][0])){
							 $sum["nocontrol_links_summa"]	 =$this->pidscontrol[$day][$pid][$country][0];
							 }  
							  #echo $day."/".$pid."/".$country."\n";
							 # var_dump($this->pidscontrol[$day][$pid][$country]);
						  }
							 
						 }
				 	   
				
			if(!isset($sum["summa"])) $sum["summa"]=0;
			if(!isset($sum["completed"])) $sum["completed"]=0;
			if(!isset($sum["clicks"])) $sum["clicks"]=0;
			if(!isset($sum["start"])) $sum["start"]=0;
			if(!isset($sum["control_summa"])) $sum["control_summa"]=0;
			if(!isset($sum["loaded"])) $sum["loaded"]=0;
			if(!isset($sum["played"])) $sum["played"]=0;
			if(!isset($sum["calculate"])) $sum["calculate"]=0;
			if(!isset($sum["deep"])) $sum["deep"]=0;
            if(!isset($sum["util"])) $sum["util"]=0;
			if(!isset($sum["second"])) $sum["second"]=0;
			if(!isset($sum["second_summa"])) $sum["second_summa"]=0;
			if(!isset($sum["second_all"])) $sum["second_all"]=0;
			if(!isset($sum["second_cheap"])) $sum["second_cheap"]=0;
			if(!isset($sum["second_cheap_all"])) $sum["second_cheap_all"]=0;
			if(!isset($sum["lease_summa"])) $sum["lease_summa"]=0;
			if(!isset($sum["second_cheap_summa"])) $sum["second_cheap_summa"]=0;
					if($pid==752 || $pid==939 || $pid==977 || $pid==974 || $pid==973){
					$sum["summa"]=$sum["control_summa"];
					//$sum["calculate"]=$sum["played"];
					$sum["second"]=0;
					$sum["second_summa"]=0;
					$sum["second_all"]=0;
					$sum["second_cheap"]=0;
					$sum["second_cheap_all"]=0;
					$sum["second_cheap_summa"]=0;
					}
		      //echo $day." > ".$pid." > $country ".$sum["control_summa"]." ".$sum["summa"]."\n";
			  $sthUpdatePids->execute([$sum["summa"]
			  ,$sum["control_summa"]
			  ,$sum["loaded"]
			  ,$sum["deep"]
			  ,$sum["util"]
			  ,$sum["calculate"]
			  ,$sum["played"]
			  ,$sum["completed"]
			  ,$sum["clicks"]
			  ,$sum["start"]
			  ,$sum["control_links_summa"]
			  ,$sum["nocontrol_links_summa"]
			  ,$sum["second"]
			  ,$sum["second_summa"]
			  ,$sum["second_all"]
			  ,$sum["second_cheap"]
			  ,$sum["second_cheap_all"]
			  ,$sum["second_cheap_summa"]
			  ,$sum["lease_summa"]
			  ,$pid,$day,$country]);
			  $sthInsertPids->execute([$pid
			  ,$day
			  ,$country
			  ,$sum["summa"]
			  ,$sum["control_summa"]
			  ,$sum["loaded"]
			  ,$sum["deep"]
			  ,$sum["util"]
			  ,$sum["calculate"]
			  ,$sum["played"]
			  ,$sum["completed"]
			  ,$sum["clicks"]
			  ,$sum["start"]
			  ,$sum["control_links_summa"]
			  ,$sum["nocontrol_links_summa"]
			  ,$sum["second"]
			  ,$sum["second_summa"]
			  ,$sum["second_all"]
			  ,$sum["second_cheap"]
			  ,$sum["second_cheap_all"]
			  ,$sum["second_cheap_summa"]
			  ,$sum["lease_summa"]
			  ,$pid
			  ,$day
			  ,$country]);
		    }
		   }
	  }
	  $this->makeLinks($from,$to);
	
	  
   }
   private function makeLinks($from=null,$to=null){
   $pdo = \DB::connection("videotest")->getPdo();
    if(!$from || !$to){
       $from=$to=date("Y-m-d");
       
      } 
	  $sql="insert into  links_summa (day,id_src,country,requested,played,util)
	  select ?,?,?,?,?,?
	  	  WHERE NOT EXISTS (SELECT 1 FROM links_summa WHERE day=? and id_src =? and country=? )
	  ";
	   $sthInsertPids=$pdo->prepare($sql);
	   	  $sql="update  links_summa set requested=?,played=?,util=?
	      WHERE day=? and id_src =? and country=?
	  ";
	   $sthUpdatePids=$pdo->prepare($sql);
   	  $sql="
select id_src,
country,
day,
requested,
played,
case when requested>0 then cast(played as double precision)*100/cast(requested as double precision)  else 0 end as  util
from (
 select t.id_src,country
,date(t.datetime) as day
,sum(t.requested) requested,
sum(t.played) as played
from stat_links t
where t. datetime >= '$from' and country is not null
          group by t.id_src,country,date(t.datetime)
)t
	  ";
	  $data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	    foreach($data as $d){
		if($d["id_src"]==13)
		//echo $d["day"]." > ".$d["id_src"]." > ".$d["country"]." \n";
		$sthUpdatePids->execute([$d["requested"],$d["played"],$d["util"],$d["day"],$d["id_src"],$d["country"]]);
		$sthInsertPids->execute([$d["day"],$d["id_src"],$d["country"],$d["requested"],$d["played"],$d["util"],$d["day"],$d["id_src"],$d["country"]]);
		$sthUpdatePids->execute([$d["requested"],$d["played"],$d["util"],$d["day"],$d["id_src"],$d["country"]]);
		//var_dump($d);
		}
   }
   private function makeControl($from=null,$to=null){

  $pdo = \DB::connection("videotest")->getPdo();
   if(!$from || !$to){
       $from=$to=date("Y-m-d");
       
      } 
	 $sql="select day,pid,country,id_src,sum(cnt)as cnt
from stat_control
where day between '$from' and '$to'
group by day,pid,country,id_src 
"; 

$ttt=0;$zz=0;

	 $data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	    foreach($data as $d){
		
		 if(!isset($this->linkcomission[$d["id_src"]])){
		
	     $val=0;
	     }
	
		 if($d["country"]=="RU"){
			 if (isset($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]))
			 $val=round($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]['summa_rus']/1000,4)*$d['cnt'];
			 else
			 $val=round($this->linkcomission[$d["id_src"]]->summa_rus/1000,4)*$d["cnt"];
		 }
		 else{
			if (isset($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]))
			$val=round($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]['summa_cis']/1000,4)*$d['cnt'];
			else
			$val=round($this->linkcomission[$d["id_src"]]->summa_cis/1000,4)*$d["cnt"];
		}		 
		 $d["summa"]=$val;
		 $d['lease_summa']=$val;
		 // if($d["pid"]==6 && $val==0 )
		  //echo  $d["pid"]." $val  ".$d["cnt"]."  ".$d["id_src"]." ".$d["country"]."///// ------------------------>\n";
		 if(!isset($this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"])){
		 $this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"]=0;
		 
		 }
		 if($d["pid"]==6){
		  $zz+=$d["cnt"];
		 $ttt+=$d["summa"];
		 }
		 $this->pids[$d["day"]][$d["pid"]][$d["country"]]["control_summa"]+=$d["summa"]; 
		
		 //print $d["pid"]." /// ///".$d["summa"]."\n";
		
	    }
		 $sql="select day,pid,country,id_src,control,sum(cnt)as cnt
from stat_control
where day between '$from' and '$to'
group by day,pid,country,id_src,control
"; 

$ttt=0;$zz=0;

	 $data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	     foreach($data as $d){ 
				 if(!isset($this->linkcomission[$d["id_src"]])){
	     $val=0;
	     }
		 if($d["country"]=="RU"){
			 if (isset($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]))
			 $val=round($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]['summa_rus']/1000,4)*$d['cnt'];
			 else
			 $val=round($this->linkcomission[$d["id_src"]]->summa_rus/1000,4)*$d["cnt"];
		 }
		 else{
			if (isset($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]))
			$val=round($this->pidlinkcontrol[$d["pid"]][$d["id_src"]]['summa_cis']/1000,4)*$d['cnt'];
			else
			$val=round($this->linkcomission[$d["id_src"]]->summa_cis/1000,4)*$d["cnt"];
		}		 
		 $d["summa"]=$val;
		
		  if(!isset($this->pidscontrol[$d["day"]][$d["pid"]][$d["country"]][$d["control"]])){

			   #print $d["pid"]."  ->>  ".$d["control"] ."  ->>>  ".$d["summa"]."\n"; 
		
			  $this->pidscontrol[$d["day"]][$d["pid"]][$d["country"]][$d["control"]]=$d["summa"];
			  
		  }else{
			  $this->pidscontrol[$d["day"]][$d["pid"]][$d["country"]][$d["control"]]+=$d["summa"];  
		  }
		}
	  
	}  
  private function makeCommon($from=null,$to=null){
  $pdo = \DB::connection("videotest")->getPdo();
   if(!$from || !$to){
       $from=$to=$date("Y-m-d");
       
      }
	 $sql="
select day,pid,country,
count(*) as loaded,
count(CASE WHEN played>0 THEN 1 END) as calculate,
sum(played) as played,
sum(completed) as completed,
sum(clicks) as clicks,
sum(start) as start,
count(CASE WHEN second_round>0 THEN 1 END) as second,
sum(second_round) as second_all,
count(CASE WHEN second_cheap>0 THEN 1 END) as second_cheap,
sum(second_cheap) as second_cheap_all,
case when cast(count(CASE WHEN played>0 THEN 1 END ) as double precision) >0 then  cast(sum(played) as double precision)/cast(count(CASE WHEN played>0 THEN 1 END ) as double precision)else 0 end  as deep,
case when cast(count(*) as double precision) >0 then  count(CASE WHEN played>0 THEN 1 END)*100/cast(count(*) as double precision) else 0 end as util,
lease as lease
from  stat_user_pages
where day between '$from' and '$to'
group by day,pid,country,lease
	 

	 ";
	 $data=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	 foreach($data as $d){
	 //if(isset($this->pidcomission[$d["pid"]])){
	  if(isset($this->widcomission[$d["pid"]])){
	 
	 if($d["country"]=="RU")
	 //$val=round($this->pidcomission[$d["pid"]]->commission_rus/1000,4)*$d["calculate"]; 
     $val=round($this->widcomission[$d["pid"]]->com_ru/1000,4)*$d["calculate"]; 
	 else
	//$val=round($this->pidcomission[$d["pid"]]->commission_cis/1000,4)*$d["calculate"];
     $val=round($this->widcomission[$d["pid"]]->com_cis/1000,4)*$d["calculate"]; 
	 $d["summa"]=$val;
	 }else{
	 $d["summa"]=0;
	 }
	 if ($d["country"]=="RU"){
		$d["second_summa"]=round($this->glubinacommission["rus"]->value/1000,4)*$d["second"];
	 }
	 else{
		$d["second_summa"]=round($this->glubinacommission["cis"]->value/1000,4)*$d["second"];
	 }
	 if ($d["country"]=="RU"){
		$d["second_cheap_summa"]=round($this->cheapcommission["rus"]->value/1000,4)*$d["second_cheap"];
	 }
	 else{
		$d["second_cheap_summa"]=round($this->cheapcommission["cis"]->value/1000,4)*$d["second_cheap"];
	 }
	 
	//if($d["pid"]==6){
	//echo $d["summa"]." ->  ".$d["calculate"]." ".$d["pid"]." ".$d["loaded"]."\n";
	
	//}
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["summa"]=$d["summa"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["completed"]=$d["completed"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["start"]=$d["start"]; 
      $this->pids[$d["day"]][$d["pid"]][$d["country"]]["clicks"]=$d["clicks"]; 	  
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["loaded"]=$d["loaded"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["played"]=$d["played"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["calculate"]=$d["calculate"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["deep"]=$d["deep"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["util"]=$d["util"]; 
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["second"]=$d["second"];
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_summa"]=$d["second_summa"];
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_all"]=$d["second_all"];
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap"]=$d["second_cheap"];
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap_all"]=$d["second_cheap_all"];
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["second_cheap_summa"]=$d["second_cheap_summa"];
	  if ($d["lease"]==1){
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"]=$d["summa"]+$d["second_summa"]+$d["second_cheap_summa"];
	  }
	  else{
	  $this->pids[$d["day"]][$d["pid"]][$d["country"]]["lease_summa"]=0;
	  } 

	 }
   } 
 public function prepareData(){
	    //$comission = \App\WidgetVideo::All();
		 $pda = \DB::connection()->getPdo();
		 $sql="select 
t.id
,t.commission_rus
,t.commission_cis
,k_ru.value as com_ru
,k_cis.value as com_cis
 from widget_videos t

inner join сommission_groups as k_ru
on k_ru.commissiongroupid = t.commission_rus
inner join сommission_groups as k_cis
on k_cis.commissiongroupid = t.commission_cis";
$comissian=$pda->query($sql)->fetchAll(\PDO::FETCH_CLASS);
foreach($comissian as $pid){
	$this->widcomission[$pid->id]=$pid;
	//var_dump($pid);
}
$sql="select t1.user_id, t1.link_id, t1.summa_rus, t1.summa_cis, t3.id as pid from user_link_summas t1 left join 
(select id,user_id from widgets) t2 on t1.user_id=t2.user_id left join (select id, wid_id from widget_videos) 
t3 on t2.id=t3.wid_id";
$pid_links=$pda->query($sql)->fetchAll(\PDO::FETCH_CLASS);
foreach ($pid_links as $pid_link){
	if (!$pid_link->pid){
		continue;
	}
	$this->pidlinkcontrol[$pid_link->pid][$pid_link->link_id]['summa_rus']=$pid_link->summa_rus;
	$this->pidlinkcontrol[$pid_link->pid][$pid_link->link_id]['summa_cis']=$pid_link->summa_cis;
}

		 $pdo = \DB::connection("videotest")->getPdo();
		$sql="select pid as id ,summa_ru as commission_rus 
,summa_cis as commission_cis
 from pid_video_settings";
 $comission=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		foreach($comission as $pid){
		
	    // if($pid->id==4) var_dump($pid);
		//$val=round($widget->CommissionGroup()->value/1000,4); 
		$this->pidcomission[$pid->id]=$pid;
		
			//echo $this->pids[$pid->id]->commission_rus;	
		}
	//$comissionSource=\App\VideoSource::All();
	
	 $sql="select * from links";
	$comissionSource=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	foreach($comissionSource as $src){
	
	$this->linkcomission[$src->id]=$src;
	    
	}
	$this->glubinacommission["rus"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000000')->first();
	$this->glubinacommission["cis"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000001')->first();
	$this->cheapcommission["rus"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000002')->first();
	$this->cheapcommission["cis"]=\DB::table('сommission_groups')->where('commissiongroupid', 'g-000003')->first();
	
    }
private function makeBlock($from=null,$to=null){
     $pdo = \DB::connection("videotest")->getPdo();
	 
	  $sql="insert into block_summa (day,id_block,requested,calculated)
	    select ?,?,?,?
	  WHERE NOT EXISTS (SELECT 1 FROM block_summa WHERE day =? and id_block=?) 
	  ";
	  $sthInsertPids=$pdo->prepare($sql); 
	  $sql="update block_summa set requested =? ,calculated=?
	   
	  WHERE day =? and id_block=?
	  ";
	   $sthUpdatePids=$pdo->prepare($sql);
       if(!$from || !$to){
       $from=$to=$date("Y-m-d");
      
	   
      }
	   $sql="
  select day,id_block,count(*) as requested
,count(CASE WHEN played>0 THEN 1 END) as calculate
 from stat_block_pages

	   where day between '$from' and '$to'
	   
	   group by day,id_block ";
	   $blocks=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	   foreach( $blocks as $block){
	    $sthUpdatePids->execute([$block["requested"],$block["calculate"],$block["day"],$block["id_block"]]);
	    $sthInsertPids->execute([$block["day"],$block["id_block"],$block["requested"],$block["calculate"],$block["day"],$block["id_block"]]);
	  //  $this->blocks[$block["day"]]=; 
	   //var_dump($block);
	   }
	  $sql="select * FROM pg_stat_activity where state ='active' ORDER BY xact_start;";
   }	
}

