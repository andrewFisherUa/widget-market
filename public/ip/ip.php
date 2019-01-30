<?php 

/*$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=videotest', 'postgres', 'dribidu1275');
$ip=$_SERVER['REMOTE_ADDR'];
$cnt=0;
$sql="select * from stat_ip where ip='$ip'";
$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
if ($stat)
$cnt=$stat['cnt'];
*/
$cnt=1;
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $cnt;
exit;