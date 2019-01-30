<?php


$curl = curl_init('https://widget.market-place.su/teaser_offers');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		header("Content-type: application/json; charset=utf-8");
		echo $json; exit();


$ip=$_SERVER['REMOTE_ADDR'];
$amount=10;
if(!isset($_GET["data"])) exit();
$data=json_decode($_GET["data"],true);

$url=$data["ref"];
$amount=$data["cnt"];
$aData = [
    'action' => 'getFeed',
    'width' => 100,
    'block_id' => 855068,
    'site_id' => 305972,
    'site' => [
        // нужен путь текущей страницы
       'referer' => $url, 'page' => $url
    ],
    'user' => [
        'ua' => $_SERVER['HTTP_USER_AGENT'],
        // нужен IP без прокси
        'ip' => $ip,
        'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE']
    ],
    'client_side' => false,
    'ad' => [
        'amount' => $amount,
        'no_content' => true
    ]
];
#print "<pre>";  print_r(["запрос",$aData]);   print "</pre>"; 
$curl = curl_init('https://g4p.redtram.com/?i=19017&f=json');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
		);
		curl_setopt_array($curl, $options);
		$sResult=(curl_exec ($curl));
$sJson = json_encode($aData, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);

$sJson = json_encode($aData, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);

$rCurl = curl_init("https://nnvkh.com/feed.php");
curl_setopt($rCurl, CURLOPT_POSTFIELDS, $sJson);
curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($rCurl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($rCurl, CURLOPT_POST, true);
curl_setopt($rCurl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Length: ' . strlen($sJson))
);

$sResult = curl_exec($rCurl);
header("Content-type: application/json; charset=utf-8");
echo $sResult; exit();
$aResult = json_decode($sResult, true);
//print "<pre>";  print_r(["ответ",$aResult]);   print "</pre>"; 