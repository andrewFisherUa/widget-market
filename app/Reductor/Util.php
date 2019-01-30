<?php namespace App\Reductor;
use Illuminate\Support\Facades\Redis;
class Util{
	public static $redis;
	public static $spfinx;
	public static function getRedis(){
		if(self::$redis===null){
			 self::$redis = new \Redis(); 
             self::$redis->connect('127.0.0.1', 6379); 
		}
		return self::$redis;
	}
	public static function resetSphinx(){
	    if(self::$spfinx){
	        self::$spfinx->resetFilters();
	        self::$spfinx->resetGroupBy();
			self::$spfinx->setSelect('*');
			self::$spfinx->SetFieldWeights([]);
		}
	}
	public static function getSphinx(){
	    if(!self::$spfinx){
			
	        self::$spfinx = new \Sphinx\SphinxClient();
		    self::$spfinx->setServer("127.0.0.1", "9312");
		}
		return self::$spfinx;
	}
}