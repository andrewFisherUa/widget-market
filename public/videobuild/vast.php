<?php 
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
function cors() {
    //header('content-type: application/json; charset=utf-8');
    header('content-type: text/xml; charset=utf-8');
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
       
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
}

$jslib="https://kinoclub77.ru/v11/vpvast-min.js";
#$jslib="https://video.market-place.su/v1/build/face.js";
$jslib="https://info.kinoclub77.ru/rank/1975/autovast-min.js";

$block_field="id_block_ru";
$block_a="allow_ru";
$block_field="block_rus";
$block_a="on_rus";
$control_a="control_rus";
$id_block=1;
$moskow=0;
$filename="/home/mp.su/widget.market-place.su/public".$_GET["path"];

$ttmp=explode("/",$_GET["path"]);
$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=videotest','market', 'katamaran_boiler');
$pda = new PDO('pgsql:host=localhost;port=5432;dbname=precluck_market_place','market', 'katamaran_boiler');
//$dbh = new PDO('pgsql:dbname=videotest;host=localhost;username=postgres;password=dribidu'); 
#$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=videotest', 'postgres', 'dribidu1275');

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
$moskow=1;
break;	
}
$pid=intval($ttmp["3"]);

break;
case 3:
$pid=intval($ttmp["2"]);
break;
}
if($pid==4) $pid=300;
if($pid==6) $pid=701;


$sql="
select adslimit,width,height,$control_a as control,$block_a as block_a, $block_field as id_block
,video_category
 from widget_videos where id =$pid and type=3
";


try{
$res=$pda->query($sql)->fetch(\PDO::FETCH_ASSOC);		  

}catch(\Exception $e){

exit;
}
if(!$res["block_a"]){

$new_data=<<<EOF
<?xml version="1.0" encoding="UTF-8"?><VAST xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="2.0">
    <Ad>
        <InLine>
            <AdSystem>MP</AdSystem>
            <AdTitle>MarketPlace</AdTitle>
            <Error>Forbidden</Error>
		</InLine>
    </Ad>
</VAST>	
EOF;
file_put_contents($filename,$new_data);
header('content-type: application/xml charset=utf-8');
header("access-control-allow-origin: *");
echo $new_data;
exit();
}
$ttlt=time();
$new_data = <<<EOF
<?xml version="1.0" encoding="UTF-8"?><VAST xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="2.0">
    <Ad>
        <InLine>
            <AdSystem>MP</AdSystem>
            <AdTitle>MarketPlace</AdTitle>
            <Error><![CDATA[]]></Error>
            <Impression/>
            <Creatives>
                <Creative>
                    <Linear>
                        <Duration>00:00:30</Duration>
                        <TrackingEvents></TrackingEvents>
                        <AdParameters><![CDATA[{"iframe":"//video.market-place.su/v1/face.html","pid":"$pid"}]]></AdParameters>
                        <MediaFiles>
                            <MediaFile apiFramework="VPAID" type="application/javascript">
                                <![CDATA[$jslib?v=$ttlt]]>
                            </MediaFile>
                        </MediaFiles>
                    </Linear>
                </Creative>
            </Creatives>
            <Extensions>
                <Extension type="controls"><![CDATA[0]]></Extension>
            </Extensions>
        </InLine>
    </Ad></VAST>
EOF;
if($pid==1597 ||  $pid==752 || $pid==1379){   
$new_data = <<<EOF
<?xml version="1.0" encoding="UTF-8"?><VAST xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="2.0">
    <Ad>
        <InLine>
            <AdSystem>MP</AdSystem>
            <AdTitle>MarketPlace</AdTitle>
            <Error><![CDATA[]]></Error>
            <Impression/>
            <Creatives>
                <Creative>
                    <Linear>
                        <Duration>00:00:30</Duration>
                        <TrackingEvents></TrackingEvents>
                        <AdParameters><![CDATA[{"duration": "360","pid":"$pid"}]]></AdParameters>
                        <MediaFiles>
                            <MediaFile apiFramework="VPAID" type="application/javascript">
                                <![CDATA[$jslib?v=$ttlt]]>
                            </MediaFile>
                        </MediaFiles>
                    </Linear>
                </Creative>
            </Creatives>
            <Extensions>
                <Extension type="controls"><![CDATA[0]]></Extension>
            </Extensions>
        </InLine>
    </Ad></VAST>
EOF;
}
if($pid==1691 || $pid>1710){
$new_data = <<<EOF
<?xml version="1.0" encoding="UTF-8"?><VAST xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="2.0">
    <Ad>
        <InLine>
            <AdSystem>MP</AdSystem>
            <AdTitle>MarketPlace</AdTitle>
            <Error><![CDATA[]]></Error>
            <Impression/>
            <Creatives>
                <Creative>
                    <Linear>
                        <Duration>00:00:30</Duration>
                        <TrackingEvents></TrackingEvents>
                        <AdParameters><![CDATA[{"duration": "360","pid":"$pid"}]]></AdParameters>
                        <MediaFiles>
                            <MediaFile apiFramework="VPAID" type="application/javascript">
                                <![CDATA[$jslib?v=$ttlt]]>
                            </MediaFile>
                        </MediaFiles>
                    </Linear>
                </Creative>
            </Creatives>
            <Extensions>
                <Extension type="controls"><![CDATA[0]]></Extension>
            </Extensions>
        </InLine>
    </Ad></VAST>
EOF;
}
file_put_contents($filename,$new_data);

cors();
//header("access-control-allow-origin: *");
    echo $new_data;
	exit;
