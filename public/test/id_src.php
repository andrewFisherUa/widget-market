<?php 
$key=$_SERVER["REMOTE_ADDR"] . "_" .$_GET["id"];
//echo $key;
//var_dump($_GET["id"])
//var_dump($_SERVER["REMOTE_ADDR"]);
try {
    $redis = new Redis();
    $redis->connect('localhost:6379');
} catch(RedisException $e) {
    exit('Connect error');
}
$redis->setEx($key, 180, 'значение 1');
//$value = $redis->get('test');
//var_dump($value);
//exit;*/
/*error_reporting(E_ALL);
ini_set('display_errors', 1);
$data["pid"]=1321;
$data["block"]=[];
$qq=(object) [
    'id' => 83,
    'src' => 'https://video.market-place.su/vast/nashe_demop.xml',
  ];
 $qqq=(object) [
    'id' => 84,
    'src' => 'https://video.market-place.su/vast/nashe_demop.xml',
  ];
 array_push($data["block"], $qq, $qqq);
$new_data=json_encode($data);
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
exit();
*/
?>