<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
//$dbh = new PDO('pgsql:dbname=videotest;host=localhost;username=postgres;password=dribidu'); 
$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=videotest','market', 'katamaran_boiler');
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
 from blocks_links bl
inner join links l on l.id=bl.id_link and l.status=1
where bl.id_block=1 and l.cheap=0
order by bl.sort";
	$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		  

$data=["block"=>1,"pid"=>"1","settings"=>["width"=>550,"height"=>350,"control"=>0]];
$data["ads"]=$res;
$sql="select bl.sort
,l.id
,l.title
,l.src
,l.player
,l.cheap
 from blocks_links bl
inner join links l on l.id=bl.id_link and l.status=1
where bl.id_block=1 and l.cheap=1
order by bl.sort";
$res=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
$data["ads_cheap"]=$res;
$new_data=json_encode($data);
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
exit();

?>