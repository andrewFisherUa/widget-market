<?php

namespace App\MPW\Sources;



class Urlparser
{
	public static function getFullUrl($host,$flag=1){
	if(!preg_match('/^https?\:\/\//ui',$host))
		$host='http://'.$host;
    $proto="";
	$query="";
	
	if($flag){
	$parsed=parse_url($host);
	if(!isset($parsed["host"])){
	     return false;
	}
	$path=(isset($parsed["path"]))?$parsed["path"]:"";
	$query=(isset($parsed["query"]))?'?'.$parsed["query"]:"";
	
	$query=$path.$query;
	
	$proto=$parsed["scheme"]."://";
	$newhost = $host = $parsed["host"];
	}else{
		$newhost = $host;
	}
	
	$test = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE",  $host);
	if($test == $host){
		$encoded = idn_to_utf8($host,0,INTL_IDNA_VARIANT_UTS46);
		if($encoded != $host){
		$newhost=$encoded;
		}
	}
	if(1==0){
	$ahh='/([^\.]+)\.([^.]{2,7}|рф|cc\.[^.]{2,4}|co\.[^.]{2,4}|(at|com)\.ua)$/ui';
	if(preg_match($ahh,$newhost,$m)){
		$ltd=$m[0];
	}
	else{
		return false;
		
	}
	}else{
		$ltd=$newhost;
	}
	return "$proto$ltd$query";

    }
	public static function getQuery($host,$flag=1){
	if(!preg_match('/^https?\:\/\//ui',$host))
		$host='http://'.$host;
    $proto="";
	$query="";
	

	$parsed=parse_url($host);
	if(!isset($parsed["host"])){
	     return false;
	}
	$path=(isset($parsed["path"]))?$parsed["path"]:"";
	$query=(isset($parsed["query"]))?'?'.$parsed["query"]:"";
	
	$query=$path.$query;
	
	return "$query";

        }
	public static function getLtd($host,$flag=1){
	if(!preg_match('/^https?\:\/\//ui',$host))
		$host='https://'.$host;
	#print $host."<br>";
	if($flag){
	$parsed=parse_url($host);
	if(!isset($parsed["host"])){
		return false;
		#var_dump(["недомен",$parsed]); die();
	}
	$newhost = $host = $parsed["host"];
	}else{
		$newhost = $host;
	}
	
	$test = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE",  $host);
	if($test == $host){
		$encoded = idn_to_utf8($host,0,INTL_IDNA_VARIANT_UTS46);
		if($encoded != $host){
		#print $encoded."";
		#print "<br>";			
		$newhost=$encoded;
		}
	}
	if($flag){
	$ahh='/([^\.]+)\.([^.]{2,7}|рф|cc\.[^.]{2,4}|co\.[^.]{2,4}|(at|com)\.ua)$/ui';
	if(preg_match($ahh,$newhost,$m)){
		$ltd=$m[0];
	}
	else{
		return false;
		#var_dump(["недомен 2",$newhost]); die();
		
	}
	}else{
		$ltd=$newhost;
	}
	return $ltd;
	#print $newhost." :: $ltd";
	#print "<hr>";
   }
}
