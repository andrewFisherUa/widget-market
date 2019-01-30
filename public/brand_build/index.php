<?php 
error_reporting(E_ALL);
$filename="/home/www/widget.market-place.su/public".$_GET["path"];
$ttmp=explode("/",$_GET["path"]);
$id_block=1;
$block="block_rus";
$on="on_rus";
# 'market', 'Sdf40vcdTmv5'
# 
#            'username' => ,
#            'password' => ,

$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=precluck_market_place','market', 'katamaran_boiler');
$ustr=$pdo->prepare($sql);
switch(count($ttmp)){
case 3:
$pid=$ttmp["2"];
break;
case 4;
switch($ttmp["2"]){
case "sng";
$block="block_cis";
$on="on_cis";
break;
case "mobile";
$block="block_mobil";
$on="on_mobil";
break;
}
$pid=$ttmp["3"];
break;
}
$tpi=explode("_",$pid);
if(isset($tpi[1])){
	$pid=$tpi[1];
	$mydomain=trim($tpi[0]);
	if($mydomain=="video.market-place.su")
		$mydomain="";
}
$pid=intval($pid);
$sql="select t2.id, t2.status from widget_brands t1 left join (select id, status from widgets) t2 on t1.wid_id=t2.id where t1.wid_id=$pid";
$stat=$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
if ($stat["status"]==1) exit();
$data=["block"=>$id_block, "pid"=>$pid];

if(1==1 && $mydomain){
	
	$sql="
	select 
t1.$block as id_block
,t1.$on as block_on
,p1.domain
 from 
widget_brands t1 
inner join (widgets w1
inner join partner_pads p1 on p1.id = w1.pad  
) on w1.id=t1.wid_id
where t1.id =$pid 
and (regexp_replace(p1.domain,'^www\.','') = '".preg_replace('/^www\./','',$mydomain)."')
";

}else{
	
$sql=" 
select $block as id_block,$on as block_on
 from widget_brands where wid_id =$pid 
";
}
try{
$res=$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC); 

}catch(\Exception $e){
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

if(!$res["block_on"]){
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
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.img
 from brand_block_links bl
 
 inner join brand_blocks b
 on b.id=bl.id_block
inner join brand_offers l on l.id=bl.id_link 
where bl.id_block=".$data["block"]." and b.status='0' and l.status='0' and l.id not in(select id_src from brand_wid_exep where pid =$pid) order by bl.sort";	
$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$data["ads"]=$res;  
$new_data=json_encode($data);
file_put_contents($filename,$new_data);

header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
exit();
var_dump($res);
var_dump($pid);

?>