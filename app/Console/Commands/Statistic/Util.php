<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class Util extends Command
{
use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:util';

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
    $pdo = \DB::connection("videotest")->getPdo();


$sql="
create temp table k0i_tmp(
    id_src integer DEFAULT 0,
    requested integer DEFAULT 0,
    played integer DEFAULT 0

)
";
$pdo->exec($sql);
$sql="
insert into k0i_tmp(
    id_src,
    requested,
    played
) values (
 ?
,?
,?
)
";
$sth0=$pdo->prepare($sql);


$sql="
create temp table ki_tmp(
    id_src integer DEFAULT 0,
    autosort integer DEFAULT 0,
    requested integer DEFAULT 0,
    played integer DEFAULT 0,
    util numeric (18,4)  DEFAULT 0
)
";
$pdo->exec($sql);
$sql="
insert into ki_tmp(
    id_src,
    autosort,
    requested,
    played,
    util
) values (
 ?
,?
,?
,?
,?
)
";
$sth=$pdo->prepare($sql);

		$sql="select id_src, requested,played 
		from (select id_src,sum(requested) requested,sum(played) as played   from stat_links 
		where datetime >=(now() - interval '1 hours') group by id_src) t 
	  ";
		$link_sel=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
foreach($link_sel as $ls){
  $sth0->execute([$ls["id_src"],$ls["requested"],$ls["played"]]);
}
$sql="
select id_src
,coalesce(sum(requested),0) as requested
,coalesce(sum(played),0) as played
from util_graph_all
where hour in(
select hour from(
select hour
from util_graph_all
group by hour
order by hour desc
limit 1
) t
order by hour 
limit 1
)
group by id_src

";
$link_sel=\DB::connection("video_")->getPdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

foreach($link_sel as $ls){
                 if($ls["id_src"]==155){
//			var_dump($ls);// die();
		}
  $sth0->execute([$ls["id_src"],$ls["requested"],$ls["played"]]);
}

$sql="
select id_src
,coalesce(sum(requested),0) as requested
,coalesce(sum(played),0) as played
,round((case when coalesce(sum(requested),0)>0 then cast(coalesce(sum(played),0) as numeric)/cast(coalesce(sum(requested),0) as numeric)  else 0 end)*100,4) as   util
from k0i_tmp
group by id_src
order by util desc,requested desc
";
$link_sel=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
$autosort=0;
foreach($link_sel as $ls){
//var_dump($ls["id_src"]);
$ls["util"]=round($ls["util"],4);
if($ls){
$sth->execute([
$ls["id_src"]
,$autosort
,$ls["requested"]
,$ls["played"]
,$ls["util"]
]);
$autosort++;
}
}
$sql="
update blocks_links as r
set autosort=t.autosort
,util=t.util
from (
select 
t1.id_block
,coalesce(t2.util,1000) as util
,t2.id_src
,coalesce(t2.autosort,1000) as autosort
 from blocks_links t1
left join ki_tmp t2
on t2.id_src=t1.id_link

) t
where t.id_block=r.id_block
and t.id_src=r.id_link
";
$pdo->exec($sql);

$sql="
select id
from widget_videos
where autosort=1
";
$wi1=\DB::connection()->getPdo()->query($sql)->fetchAll(\PDO::FETCH_COLUMN);
if($wi1){
$sql="
create temp table k00i_tmp(
    pid integer DEFAULT 0,
    id_src integer DEFAULT 0,
    requested integer DEFAULT 0,
    played integer DEFAULT 0

)
";
$pdo->exec($sql);
$sql="
insert into k00i_tmp(
    pid,
    id_src,
    requested,
    played
) values (
 ?
,?
,?
,?
)
";
$sth00=$pdo->prepare($sql);


$sql="
select pid,id_src
,coalesce(sum(requested),0) as requested
,coalesce(sum(played),0) as played
from util_pidgraph_all
where hour in(
select hour from(
select hour
from util_pidgraph_all
group by hour
order by hour desc
limit 1
) t
order by hour 
limit 1
)
group by pid,id_src

";
#and pid in(".implode(",",$wi1).")
$link_sel=\DB::connection("video_")->getPdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
//var_dump(count($link_sel)); die();
foreach($link_sel as $ls){
  $sth00->execute([$ls["pid"],$ls["id_src"],$ls["requested"],$ls["played"]]);
}
$sql="
select pid,id_src
,coalesce(sum(requested),0) as requested
,coalesce(sum(played),0) as played
,round((case when coalesce(sum(requested),0)>0 then cast(coalesce(sum(played),0) as numeric)/cast(coalesce(sum(requested),0) as numeric)  else 0 end)*100,4) as   util
from k00i_tmp
group by pid,id_src
order by util desc,requested desc
";
$link_sel=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

$pdss=[];
foreach($link_sel as $ls){

if(!isset($pdss[$ls["pid"]])){
$pdss[$ls["pid"]]=[];
echo $ls["pid"]." : ".$ls["id_src"]."\n";
}
array_push($pdss[$ls["pid"]],$ls);
//var_dump($ls["id_src"]);
}
$sql="
create temp table ki__tmp(
    pid integer DEFAULT 0,
    id_src integer DEFAULT 0,
    autosort integer DEFAULT 0,
    requested integer DEFAULT 0,
    played integer DEFAULT 0,
    util numeric (18,4)  DEFAULT 0
)
";
$pdo->exec($sql);
$sql="
insert into ki__tmp(
    pid,
    id_src,
    autosort,
    requested,
    played,
    util
) values (
 ?
,?
,?
,?
,?
,?
)
";
$sth_=$pdo->prepare($sql);

foreach($pdss as $arr){
   $autosort=0;
   foreach($arr as $ls){
   $ls["util"]=round($ls["util"],4);
   if($ls){
   $sth_->execute([
   $ls["pid"]
   ,$ls["id_src"]
   ,$autosort
   ,$ls["requested"]
   ,$ls["played"]
   ,$ls["util"]
   ]);
   $autosort++;
     }
   }
}
$sql="truncate table pid_links";
$pdo->exec($sql);
$sql="
insert into pid_links(
pid,
id_link,
autosort,
util
)
select  
pid,
id_src,
autosort,
util
from
ki__tmp
";
$pdo->exec($sql);




}
//var_dump($wi1);
//foreach($wi1 as $i){
 //print $i["id"]."\n";
//}







	  \App\Videosource\DiscUtil::Utile();
		$day=date("Y-m-d 00:00:00",time()-3600*24*30);			
		$hour=preg_replace('/^0+/','',date("H"));		
         if($hour==16)	{	
		 $sql="delete from stat_links where datetime <'$day'; 
		 delete from util_links where datetime <'$day';
		 ";
		 $pdo->exec($sql);
		 #var_dump([$hour,$day]);
		  }













return ;


$sql="
select id_src
,coalesce(sum(requested),0) as requested
,coalesce(sum(played),0) as played
,round((case when coalesce(sum(requested),0)>0 then cast(coalesce(sum(played),0) as numeric)/cast(coalesce(sum(requested),0) as numeric)  else 0 end)*100,4) as   util
from util_graph
where hour in(
select hour from(
select hour
from util_graph
group by hour
order by hour desc
limit 4
) t
order by hour 
limit 3
)
group by id_src
order by util desc,requested desc

";
$link_sel=\DB::connection("video_")->getPdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
$autosort=0;
foreach($link_sel as $ls){
$ls["util"]=round($ls["util"],4);
if($ls){
$sth->execute([
$ls["id_src"]
,$autosort
,$ls["requested"]
,$ls["played"]
,$ls["util"]
]);
$autosort++;
}
}


return;
$sql="
update blocks_links as r
set autosort=t.autosort
,util=t.util
from (
select 
t1.id_block

,coalesce(t2.util,1000) as util
,t2.id_src
,coalesce(t2.autosort,1000) as autosort
 from blocks_links t1
left join ki_tmp t2
on t2.id_src=t1.id_link

) t
where t.id_block=r.id_block
and t.id_src=r.id_link
";
$pdo->exec($sql);
	  \App\Videosource\DiscUtil::Utile();
		$day=date("Y-m-d 00:00:00",time()-3600*24*30);			
		$hour=preg_replace('/^0+/','',date("H"));		
         if($hour==16)	{	
		 $sql="delete from stat_links where datetime <'$day'; 
		 delete from util_links where datetime <'$day';
		 ";
		 $pdo->exec($sql);
		 #var_dump([$hour,$day]);
		  }
					




return;

	  $this->datetime=date("Y-m-d H:i:s");

		$sql="select id_src, requested, 
		case when requested>0 then cast(played as double precision)*100/cast(requested as double precision)  else 0 end as  util 
		from (select id_src,sum(requested) requested,sum(played) as played   from stat_links 
		where datetime >=(now() - interval '1 hours') group by id_src) t order by util desc,requested asc
	  ";
#	   echo $sql;  die();
		$link_sel=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);





		$sql="insert into links_utils (id_link, util, autosort) select ?,?,? WHERE NOT EXISTS (SELECT 1 FROM links_utils WHERE id_link=?)";
		$sthInsertLinks=$pdo->prepare($sql);
		$sql="update links_utils set util=?, autosort=? WHERE id_link=?";
		$sthUpdateLinks=$pdo->prepare($sql);
		foreach ($link_sel as $key=>$ls){
			$sthUpdateLinks->execute([$ls['util'], $key, $ls['id_src']]);
			$sthInsertLinks->execute([$ls['id_src'], $ls['util'], $key, $ls['id_src']]);
		}

	  
	  $sql="select 
id_src,
requested,
case when requested>0 then cast(played as double precision)*100/cast(requested as double precision)  else 0 end as  util
from (
select id_src,sum(requested) requested,sum(played) as played  from stat_links
where block=? and datetime >=
(now() - interval '1 hours')
	  group by id_src
) t
order by util desc,requested asc
	  ";
	  $this->sel=$pdo->prepare($sql);
/*
$sql="
select id_src,requested,played,
case when requested>0 then cast(played as double precision)/cast(requested as double precision)  else 0 end as  util
from util_graph
where hour in(

select hour from(
select hour
from util_graph
group by hour
order by hour desc
limit 1
) t
order by hour 
limit 1
)
order by util desc,requested asc
";
  $this->sel=\DB::connection("video_")->getPdo()->prepare($sql);
 */
                                         	  

	 $sql="update blocks_links set autosort =1001 where id_block=?";  
	 $this->updStart=$pdo->prepare($sql);
	 $sql="update blocks_links set autosort =?,util=? where id_block=? and id_link=?";  
	 $this->updAsc=$pdo->prepare($sql);
	 $sql="select * from blocks_links where id_block=?";
	 $this->selAsc=$pdo->prepare($sql);
	 $sql="insert into util (id_block,util,datetime) values (?,?,'".$this->datetime."')";
	 $this->insUtil=$pdo->prepare($sql);
	 $this->links_utils=[];
	 $sql="select * from blocks order by id";
	  $blocks=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
	  foreach($blocks as $block){
		 //var_dump($block);
		 $this->repareBlock($block["id"]);
	  }
	 #for($i=1;$i<20;$i++){
	 #$this->repareBlock($i);
	 #}
	// $this->repareBlock(1);
	// $this->repareBlock(2);
	 
	 $sql="	  
	 insert into util_links (id_src,datetime,requested,util,played)
	 select 
id_src,
'".$this->datetime."',
requested,
case when requested>0 then cast(played as double precision)*100/cast(requested as double precision)  else 0 end as  util
,played
from (
select t.id_src,sum(t.requested) requested,sum(t.played) as played   from stat_links t

where t. datetime >=
(now() - interval '1 hours')
	  group by t.id_src
) t
order by util desc,requested asc"; 
	  $pdo->exec($sql);


	  \App\Videosource\DiscUtil::Utile();
	  /*
	  $cmd = "rm -rf  /home/www/precluck.market-place.su/public/videoblock/*.json";
	 `$cmd`;
	  $cmd = "rm -rf  /home/www/precluck.market-place.su/public/videoblock/sng/*.json";
	 `$cmd`;
	  $cmd = "rm -rf  /home/www/precluck.market-place.su/public/videoblock/msk/*.json";
	 `$cmd`;
	                 $cmd = "rm -rf  /home/www/precluck.market-place.su/public/videovast/*.xml";
	                `$cmd`;
	                 $cmd = "rm -rf  /home/www/precluck.market-place.su/public/videovast/sng/*.xml";
	                `$cmd`;
	                 $cmd = "rm -rf  /home/www/precluck.market-place.su/public/videovast/msk/*.xml";
	                `$cmd`;
					*/
		$day=date("Y-m-d 00:00:00",time()-3600*24*30);			
		$hour=preg_replace('/^0+/','',date("H"));		
         if($hour==16)	{	
		 $sql="delete from stat_links where datetime <'$day'; 
		 delete from util_links where datetime <'$day';
		 ";
		 $pdo->exec($sql);
		 #var_dump([$hour,$day]);
		  }
					
    }
	private function repareBlock($id){
		if($id==17) return;
	  $pdo = \DB::connection("videotest")->getPdo();
	
	  $this->sel->execute([$id]);
	  $res= $this->sel->fetchAll(\PDO::FETCH_ASSOC);
	  if(!$res) return;
	  $src_utils=[];
	  $this->updStart->execute([$id]);
	  $i=1;
      foreach($res as $r){
	  $src_utils[$r["id_src"]]=$r;
	  $this->updAsc->execute([$i,$r["util"],$id,$r["id_src"]]);
	  $i++;
      }
	  $this->selAsc->execute([$id]);
	  $resd=  $this->selAsc->fetchAll(\PDO::FETCH_ASSOC);
	  $ar=[];
	  $avg=0;
	  foreach($resd as $rd){
	  if(isset($src_utils[$rd["id_link"]])){
	  $this->links_utils=[];
	  array_push($ar,$src_utils[$rd["id_link"]]["util"]);
	   }
	  }
	   if(count($ar)){
	     $plays=array_sum($ar);
         $avg=$plays/count($ar);
		}
	 $this->insUtil->execute([$id,$avg]);
		
	  
	}
}
