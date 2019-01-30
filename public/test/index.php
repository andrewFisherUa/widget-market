<?php
try {
    $redis = new Redis();
    $redis->connect('localhost:6379');
} catch(RedisException $e) {
    exit('Connect error');
}

//var_dump($value);
//$redis->setEx('test', 10, 'значение 1');
//$value = $redis->get('test');
//var_dump($value);
//exit;*/
error_reporting(E_ALL);
ini_set('display_errors', 1);
$data["pid"]=1321;
$data["nt_"]=0;
$data["settings"]=(object) [
	'width' => 550,
	'height' => 350,
	'control' => 0,
];
$data["block"]=[];
$key=$_SERVER["REMOTE_ADDR"] . "_83"; 
$value = $redis->get($key);
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://video.market-place.su/vast/nashe_demop.xml',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://video.market-place.su/vast/nashe_demop.xml',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
//if (!$value){
/*$qq=(object) [
    'id' => 83,
    //'src' => 'https://instreamvideo.ru/core/vpaid/linear?pid=251&vr=1&rid={rnd}{tems}&puid7=1&puid8=7&puid10=1&puid11=1&puid12=16&dl={ref}&duration=360&vn=',
	'src' => 'https://instreamvideo.ru/core/vpaid/linear?pid=251&vr=1&rid={rnd}{tems}&puid7=1&puid8=7&puid10=1&puid11=1&puid12=16&dl={ref}&duration=360&vn=',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
array_push($data["block"], $qq);
//  }
  $key=$_SERVER["REMOTE_ADDR"] . "_84"; 
$value = $redis->get($key);
//if (!$value){
 $qqq=(object) [
    'id' => 84,
    'src' => '//moevideo.biz/vast/?ref=apptoday.ru&impressionAfterPaid=1',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://code.vihub.ru/video?plid=804&startdelay=0&ref={ref}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://cdn.malokacha.club/vast.php?hash=s47Yt0E7HhsgfoJJ',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://adserver.otm-r.com/get?site_id=5a412191798be67e3ffc5898&placement_id=5a4121b85815562e06072452&domain={refurl}&page={ref}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://adserver.otm-r.com/get?site_id=5937c44e1c24c410cc59a5f9&placement_id=5937c475418a2c111815ad1a&domain={refurl}&page={ref}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://www.mwayss.com/vast.xml?key=7e05d5da89a4fa9442543a3dd3661b22&vastv=3.0&ch={refurl}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);

$qqqq=(object) [
    'id' => 85,
    'src' => '//exchange.buzzoola.com/adv/XKYmYyY14N3jPtX-HGY_iZ-fSx7l1BOGyipKxXvRpyQ/vast3',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);

	$qqqq=(object) [
    'id' => 85,
    'src' => 'https://am15.net/video.php?type=direct&s=78167&show=preroll',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	$qqqq=(object) [
    'id' => 85,
    'src' => 'http://cdn.malokacha.club/vast.php?id=2351&subid=1',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://cdn.malokacha.club/vast.php?hash=bme1cRjxTHryOVuS',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
	$qq=(object) [
    'id' => 83,
    //'src' => 'https://instreamvideo.ru/core/vpaid/linear?pid=251&vr=1&rid={rnd}{tems}&puid7=1&puid8=7&puid10=1&puid11=1&puid12=16&dl={ref}&duration=360&vn=',
	'src' => 'https://instreamvideo.ru/core/vpaid/linear?pid=251&vr=1&rid={rnd}{tems}&puid7=1&puid8=7&puid10=1&puid11=1&puid12=16&dl={ref}&duration=360&vn=',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
array_push($data["block"], $qq);
//  }
  $key=$_SERVER["REMOTE_ADDR"] . "_84"; 
$value = $redis->get($key);
//if (!$value){
 $qqq=(object) [
    'id' => 84,
    'src' => '//moevideo.biz/vast/?ref=apptoday.ru&impressionAfterPaid=1',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://code.vihub.ru/video?plid=804&startdelay=0&ref={ref}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://cdn.malokacha.club/vast.php?hash=s47Yt0E7HhsgfoJJ',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://adserver.otm-r.com/get?site_id=5a412191798be67e3ffc5898&placement_id=5a4121b85815562e06072452&domain={refurl}&page={ref}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://adserver.otm-r.com/get?site_id=5937c44e1c24c410cc59a5f9&placement_id=5937c475418a2c111815ad1a&domain={refurl}&page={ref}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://www.mwayss.com/vast.xml?key=7e05d5da89a4fa9442543a3dd3661b22&vastv=3.0&ch={refurl}',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);

$qqqq=(object) [
    'id' => 85,
    'src' => '//exchange.buzzoola.com/adv/XKYmYyY14N3jPtX-HGY_iZ-fSx7l1BOGyipKxXvRpyQ/vast3',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);

	$qqqq=(object) [
    'id' => 85,
    'src' => 'https://am15.net/video.php?type=direct&s=78167&show=preroll',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	$qqqq=(object) [
    'id' => 85,
    'src' => 'http://cdn.malokacha.club/vast.php?id=2351&subid=1',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
$qqqq=(object) [
    'id' => 85,
    'src' => 'https://cdn.malokacha.club/vast.php?hash=bme1cRjxTHryOVuS',
	'title' => 'Наше демо',
	'player' => 0,
	'cheap' => 0,
	'psc' => ''
  ];
	array_push($data["block"], $qqqq);
	
	*/
//  }
$new_data=json_encode($data);
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
exit();

?>