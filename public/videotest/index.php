<?php


$PgDbh = new \PDO('pgsql:host=localhost;port=5432;dbname=statistic_market_place;user=market;password=katamaran_boiler');
$sql="select * from advert_stat_pages
where id_server =61 and datetime >'2017-12-01 16:51:00' and found =0 and country='RU'
order by datetime desc
 limit 100";
$data=$PgDbh->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
foreach($data as $d){
var_dump($d);
echo "<hr>";
}
?>