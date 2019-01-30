<?php

namespace App\Lbtc\Multy;

#use Illuminate\Database\Eloquent\Model;

class Robot
{
    public static $curl;
	public static $multi;
 	private $lbtckey='1ceedf9fbd71007ddd294f5ad0838913'; //ключ localbitcoins test
	private $lbtcsecret='d9e5cf6a6497a8a4dffdbd80e6b3bc748201bb5cb194fab7ee2142f2d628a994'; //секрет localbitcoins test
    public $connectionURL="localbitcoins.net";
	public function __construct(){
		
		 
		  
	self::$multi=curl_multi_init();
	self::$curl=curl_init();
	$this->socket = stream_socket_client("localbitcoins.net:80", $errno, $errstr, 30);
	#var_dump($fp);
	#die();
	# $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
	 $this->socket=fsockopen( $this->connectionURL, 443);
	
     #     socket_connect($this->socket, "localbitcoins.net",443);
	#	  socket_set_nonblock($this->socket);
	#die();	  
		 // var_dump($socket);
//		  die();
	$options = array(
	CURLOPT_HTTPGET => 1,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_TIMEOUT => 40,
	CURLOPT_FILETIME => TRUE,
	CURLOPT_FORBID_REUSE=>false,
	CURLOPT_FRESH_CONNECT=>false,
	);
	curl_setopt_array(self::$curl, $options);   
	} 
	public function __destruct(){
		    #if(self::$curl)
			 fclose($this->socket);
			curl_close(self::$curl);
			curl_multi_close(self::$multi);

		#var_dump('close');
	} 
	public function getAds(){
		$ads=\DB::connection('obmenneg')->table('all_ads')->get();
		foreach ($ads as $ad){
	$url='/api/ad-get/' . $ad->id_ads . '/';
	$nonce=time();
	$array_message=array(
	);
	var_dump($url);
	$message=http_build_query($array_message);
	$apiauth = $nonce.$this->lbtckey.$url.$message;
	$signature = strtoupper(hash_hmac('sha256',$apiauth,$this->lbtcsecret));
	fwrite($this->socket, "GET $url HTTP/1.0\r\nHost: localbitcoins.net\r\nAccept: */*\r\n\r\n");
    while (!feof($this->socket)) {
        echo fgets($this->socket, 1024);
    }
   
	continue;
	fwrite($this->socket, "GET  $url HTTP/1.1\r\n");
	fwrite($this->socket, "Host: https://localbitcoins.net\r\n");
	fwrite($this->socket, "Connection: close\r\n\r\n");
	$d=stream_get_contents($this->socket);
	
	var_dump($d);
	continue;
	#socket_write($this->socket, "GET  $url HTTP/1.1\r\n");
	 #socket_write($this->socket, "Host: localbitcoins.net\r\n");
     #socket_write($this->socket, "Connection: close\r\n");
	 #socket_write($this->socket, "\r\n");
	 $data = socket_read($this->socket, 0xffff);
	 var_dump($data);
	continue;
	curl_setopt(self::$curl,CURLOPT_URL ,'https://localbitcoins.net'.$url);
	curl_setopt(self::$curl,CURLOPT_POSTFIELDS , $message);
	curl_setopt(self::$curl,CURLOPT_HTTPHEADER , array(
	'Apiauth-Key:'.$this->lbtckey,
	'Apiauth-Nonce:'.$nonce,
	'Apiauth-Signature:'.$signature
	));
	$result=curl_exec(self::$curl);
	if(!$result){
	        print "error\n";	
		}else{
			print "ok\n";	
		}
	}
	return;	   
	$active = null;
    do {
    $mrc = curl_multi_exec(self::$multi, $active);
	
    }while ($mrc == CURLM_CALL_MULTI_PERFORM);
 
     while ($active && $mrc == CURLM_OK) {
		do {
			$mrc = curl_multi_exec(self::$multi, $active);
			var_dump([$active,$mrc]);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }	 
 
 return;
 var_dump([($active && $mrc),CURLM_OK,$mrc, CURLM_CALL_MULTI_PERFORM,$active]);
     return;
    while ($active && $mrc == CURLM_OK) {
		do {
			$mrc = curl_multi_exec(self::$multi, $active);
			
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }	 
    return;	
	foreach ($channels as $channel) {
    $result=json_decode(curl_multi_getcontent($channel));
	var_dump(123);
	if ($result){
		var_dump(1);
	}
    curl_multi_remove_handle(self::$multi, $channel);
    }


	
	}
}
