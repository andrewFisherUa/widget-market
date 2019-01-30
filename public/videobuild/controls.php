<?php 
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
// var_dump(111);

function cors() {
    header('content-type: application/json; charset=utf-8');
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
       
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        //header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        //exit(0);
    }

    //echo "You have CORS!";
}

$block_field="id_block_ru";
$block_a="allow_ru";
$block_field="block_rus";
$block_a="on_rus";
$control_a="control_rus";

$excluen=[-1];
$colmuted=0;

$id_block=1;
$moskow=0;
$filename="/home/mp.su/widget.market-place.su/public".$_GET["path"];
$ttmp=explode("/",$_GET["path"]);
$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=videotest','market', 'katamaran_boiler');
$pda = new PDO('pgsql:host=localhost;port=5432;dbname=precluck_market_place','market', 'katamaran_boiler');
$sql="insert into videorequested (pid,domain,exists,url)
select ?,?,?,?
WHERE NOT EXISTS (SELECT 1 FROM videorequested WHERE  pid=? and domain=?)
";
$ddd="_";
$ustr=$pdo->prepare($sql);

//$_SERVER["HTTP_REFERER"]="http://poexaly.com";

$url2="";
if(isset($_SERVER["HTTP_REFERER"])){
	$url2=$_SERVER["HTTP_REFERER"];
$hh=parse_url($_SERVER["HTTP_REFERER"]);
if(isset($hh["host"]))
$ddd=$hh["host"];
}
//$excluen=[-1];

switch(count($ttmp)){
case 4:
switch($ttmp["2"]){
case "sng":
$block_field="id_block_cis";
$block_a="allow_cis";
$block_field="block_cis";
$block_a="on_cis";
$control_a="control_cis";
break;	
case "msk":
$excluen=[-1,124];
$moskow=0;
break;	
}
$pid=$ttmp["3"];

break;
case 3:
$pid=$ttmp["2"];
break;
}
#var_dump($excluen); die();
$mydomain="";
$tpi=explode("_",$pid);
if(isset($tpi[1])){
	$pid=$tpi[1];
	$mydomain=trim($tpi[0]);
	if($mydomain=="video.market-place.su")
		$mydomain="";
	#var_dump($filename); die();

}


$pid=intval($pid);
$autosort=0;
if($pid==4) $pid=300;
if($pid==6) $pid=701;
#if($pid==1099) $mydomain="";

$sql="select t2.id, t2.status from widget_videos t1 left join (select id, status from widgets) t2 on t1.wid_id=t2.id where t1.id=$pid";
$stat=$pda->query($sql)->fetch(\PDO::FETCH_ASSOC);
if ($stat["status"]==1) exit();
$data=["block"=>$id_block,"nt_"=>0,"pid"=>"1","adslimit"=>0,"settings"=>["width"=>550,"height"=>350,"control"=>1]];


if(1==1 && $mydomain){
	
	$sql="
	select t1.adslimit
,t1.width
,t1.height
,t1.$control_a as control
,t1.$block_a as block_a
,t1.$block_field as id_block 
,t1.video_category
,p1.domain
 from 
widget_videos t1 
inner join (widgets w1
inner join partner_pads p1 on p1.id = w1.pad  
) on w1.id=t1.wid_id
where t1.id =$pid 
and (regexp_replace(p1.domain,'^www\.','') = '".preg_replace('/^www\./','',$mydomain)."' or  t1.type=3 or  t1.validation=0)
";

//var_dump($sql); die();
}else{
	
$sql=" 
select adslimit,width,height,autosort
,$control_a as control
,$block_a as block_a, $block_field as id_block
,video_category
 from widget_videos where id =$pid 
";
}

try{
$res=$pda->query($sql)->fetch(\PDO::FETCH_ASSOC);	 
$autosort=$res["autosort"]; 
#var_dump($sql); die();
}catch(\Exception $e){
  $ustr->execute([$pid,$ddd,0,$url2,$pid,$ddd]);
	$data=["error"=>"forbidden user"];
$new_data=json_encode($data);
file_put_contents($filename,$new_data);

header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
}
	
if(!$res){
	$ustr->execute([$pid,$ddd,0,$url2,$pid,$ddd]);
	$data=["error"=>"forbidden user"];
$new_data=json_encode($data);
file_put_contents($filename,$new_data);

header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;

exit();
}
	//$ustr->execute([$pid,$ddd,1,$pid,$ddd]);
if($res["video_category"]==1)
	$data["nt_"]=1;
if(!$res["block_a"]){
	$data=["error"=>"forbidden user"];
$new_data=json_encode($data);
file_put_contents($filename,$new_data);

header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
exit();
	
}

$data["block"]=$id_block=$res["id_block"];
$data["pid"]=$pid;
$data["repeat"]=0;
$data["cheap_util"]=0;
$data["settings"]["width"]=$res["width"];
$data["settings"]["height"]=$res["height"];
$data["settings"]["control"]=$res["control"];

$data["adslimit"]=$res["adslimit"];

//var_dump($aBlock["cheap_util"]) ; die();
//$dbh = new PDO('pgsql:dbname=videotest;host=localhost;username=postgres;password=dribidu'); 
//if($res["video_category"] === 0 $moskow==1 && $_SERVER["REMOTE_ADDR"]=="79.122.189.122"){

if(1==0 || (($res["video_category"] === 0) && $moskow==1)){

	if($pid==724)
		$data["block"]=5;
	elseif($pid==518)
		$data["block"]=24;
	else 
$id_block=$data["block"]=18;

$sql="select * from blocks where id = $id_block ";
try{
$aBlock=$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);		  
}catch(\Exception $e){
	
exit;
}


if(!$aBlock)
	exit;
$sortFiled="bl.sort";
if($aBlock["autosort"])
	$sortFiled="case when bl.prioritet >0 then bl.prioritet  end asc, bl.autosort";
$data["repeat"]=$aBlock["repeat"];
$data["cheap_util"]=$aBlock["cheap_util"];

if($autosort){
		 

$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
 from blocks_links bl
left join blocks b
on b.id=bl.id_block
left join (links l
left join pid_links pl on pl.pid=$pid and pl.id_link=l.id
) on l.id=bl.id_link and l.status=1 
and l.id not  in(select id_src from exception where pid =$pid)
where bl.id_block=".$data["block"]." and (l.cheap=0 or b.cheap_util =0) 
and l.id not in (".implode(",",$excluen).")
order by coalesce(pl.autosort,100)";	

$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		

$data["ads"]=$res; 	
	
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
 from blocks_links bl
 left join blocks b
 
on b.id=bl.id_block

inner join (links l
left join pid_links pl on pl.pid=$pid and pl.id_link=l.id
)
on l.id=bl.id_link and l.status=1 
and l.id not  in(select id_src from exception where pid =$pid)
where bl.id_block=".$data["block"]." and (l.cheap=1 and b.cheap_util >0) 
and l.id not in (".implode(",",$excluen).")
order by  coalesce(pl.autosort,100)";	
$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
 
$data["ads_cheap"]=$res;
	
 
}else{


//$data["repeat"]=$aBlock["repeat"];
//$data["cheap_util"]=$aBlock["cheap_util"];
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
 from blocks_links bl
 
 inner join blocks b
 on b.id=bl.id_block
inner join links l on l.id=bl.id_link and l.status=1 
and l.id not  in(select id_src from exception where pid =$pid)
where bl.id_block=".$data["block"]." and (l.cheap=0 or b.cheap_util =0) 
and l.id not in (".implode(",",$excluen).")
order by $sortFiled";	
 //var_dump($sql); die();
$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
$data["ads"]=$res;  


$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
 from blocks_links bl
 inner join blocks b
on b.id=bl.id_block
inner join links l on l.id=bl.id_link and l.status=1 
and l.id not  in(select id_src from exception where pid =$pid)
where bl.id_block=".$data["block"]." and (l.cheap=1 and b.cheap_util >0) 
and l.id not in (".implode(",",$excluen).")
order by $sortFiled";	
$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
 
$data["ads_cheap"]=$res;
} //end autosort
if ($pid==1275 || $pid==1276 || $pid==1277 || $pid==1278){
	$sql="select bl.sort
	,l.id
	,l.title
	,l.src
	,l.player
	,l.cheap
	,l.partner_script
	,1 as lasti
	from blocks_links bl
	inner join blocks b
	on b.id=bl.id_block
	inner join links l on l.id=bl.id_link and l.status=1 
	where bl.id_block=17 and l.id<>'12'
	and l.id not in (".implode(",",$excluen).")
	order by $sortFiled";	
	$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
	$data["ads_last"]=$res;
}
else{
	$sql="select bl.sort
	,l.id
	,l.title
	,l.src
	,l.player
	,l.cheap
	,l.partner_script
	,1 as lasti
	from blocks_links bl
	inner join blocks b
	on b.id=bl.id_block
	inner join links l on l.id=bl.id_link and l.status=1 
	
	where bl.id_block=17
	and l.id not in (".implode(",",$excluen).")
	order by $sortFiled";	
	$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
	$data["ads_last"]=$res;
}
//if($pid==724){
/*$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
,1 as lasti
from blocks_links bl
inner join blocks b
on b.id=bl.id_block
inner join links l on l.id=bl.id_link and l.status=1 
where bl.id_block=17
order by $sortFiled";	
$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
$data["ads_last"]=$res;*/
//}

	
}else{
	
$sql="select * from blocks where id = $id_block ";
try{
$aBlock=$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);		  
}catch(\Exception $e){

exit;
}
if(!$aBlock)
	exit;



$sortFiled="bl.sort";
if($autosort){
$sql="
create temp table bl_links as 
select bl.id_block
, bl.id_link
, coalesce(pl.autosort,100) as autosort
, coalesce(pl.autosort,100) as sort
, 0 as  prioritet
from blocks_links bl
left join pid_links pl on
pl.pid=$pid  and pl.id_link=bl.id_link
where id_block=$id_block
union 
 select $id_block as id_block
 ,id_src as id_link
 ,0 as autosort
 ,0 as sort
 , 0 as prioritet from add_links where pid=$pid;
";
#if($pid=="1917"){
#var_dump($sql);
#var_dump($excluen);
#die();
#}


$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);	
}else{
if($aBlock["autosort"])
	$sortFiled=" case when bl.prioritet >0 then bl.prioritet  end asc,bl.autosort";
$data["repeat"]=$aBlock["repeat"];
$data["cheap_util"]=$aBlock["cheap_util"];
$sql="create temp table bl_links_tt as 
select id_block, id_link, sort, prioritet from blocks_links where id_block=$id_block
union
select $id_block as id_block, id_src as id_link, 0 as sort, 0 as prioritet from add_links where pid=$pid";

##
$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);	
$sql="create temp table bl_links as 
select t1.*, t2.util, t2.autosort from bl_links_tt t1 left join (select * from links_utils) t2 on t1.id_link=t2.id_link";
$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);	
}// end no autosort
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
 from bl_links bl
 inner join blocks b
on b.id=bl.id_block
inner join links l on l.id=bl.id_link and l.status=1 
and l.id not  in(select id_src from exception where pid =$pid)
where bl.id_block=$id_block and (l.cheap=0 or b.cheap_util =0) 
and l.id not in (".implode(",",$excluen).")
order by $sortFiled";
	$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		  
$data["ads"]=$res;
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
,l.partner_script
 from bl_links bl
  inner join blocks b
on b.id=bl.id_block
inner join links l on l.id=bl.id_link and l.status=1
and l.id not  in(select id_src from exception where pid =$pid)
where bl.id_block=$id_block and (l.cheap=1 and b.cheap_util >0)
and l.id not in(".implode(",",$excluen).")
order by $sortFiled";

$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		

$data["ads_cheap"]=$res;  
 
}
if($pid=="1849")
$colmuted=1;

$data["muted"] = $colmuted;

$new_data=json_encode($data);

file_put_contents($filename,$new_data);

cors();
#header('content-type: application/json; charset=utf-8');
//header("access-control-allow-origin: *");
echo $new_data;
exit();

