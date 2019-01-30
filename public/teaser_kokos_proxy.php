<?php

if(!isset($_GET["data"])) exit();
$data=json_decode($_GET["data"],true);
$ip=$_SERVER['REMOTE_ADDR'];
$ua=$_SERVER['HTTP_USER_AGENT'];

$message=json_encode($array_message);
$dom=json_encode($data['domain']);
$domain = str_replace(array("'",'"'),'',$dom);
$wid=trim($data['wid']);
$domain=$domain . "_-_" . $wid;
$u=json_encode($ua);
$curl = curl_init('https://api.kokos.click/teaser/TESTXXXXXXXXXXXX/?format=json&theme_id=3&site_id=' . $domain . ' &ip=' . $ip . '&ua= ' . $u . '');
$options = array(
	CURLOPT_HTTPGET => 1,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_TIMEOUT => 40,
);
curl_setopt_array($curl, $options);
$json=(curl_exec ($curl));
//var_dump(curl_getinfo($curl));
curl_close($curl);
header("Content-type: application/json; charset=utf-8");
echo $json;
exit;
