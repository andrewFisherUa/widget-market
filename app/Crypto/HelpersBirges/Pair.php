<?php

namespace App\Crypto\HelpersBirges;

use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
		
	public function index($symbols, $pair){
		$pair=mb_strtolower($pair);
		$ft['from']=substr($pair, 0, 2);
		$ft['to']=substr($pair, 2);
		if (isset($symbols[$ft['from']])){
			if (isset($symbols[$ft['to']]))
			return $ft;
		}
		$ft['from']=substr($pair, 0, 3);
		$ft['to']=substr($pair, 3);
		if (isset($symbols[$ft['from']])){
			if (isset($symbols[$ft['to']]))
			return $ft;
		}
		$ft['from']=substr($pair, 0, 4);
		$ft['to']=substr($pair, 4);
		if (isset($symbols[$ft['from']])){
			if (isset($symbols[$ft['to']]))
			return $ft;
		}
		$ft['from']=substr($pair, 0, 5);
		$ft['to']=substr($pair, 5);
		if (isset($symbols[$ft['from']])){
			if (isset($symbols[$ft['to']]))
			return $ft;
		}
	}
	
	public function convert($from, $to){
		if ($to!='rub'){
			$from_1=$centrobanks=\DB::connection('crypto')->table('centrobank')->where('charcode', $from)->first();
			$to_1=$centrobanks=\DB::connection('crypto')->table('centrobank')->where('charcode', $to)->first();
			return ($to_1->nominal*$from_1->value)/($from_1->nominal*$to_1->value);
		}
		else{
			$from_1=$centrobanks=\DB::connection('crypto')->table('centrobank')->where('charcode', $from)->first();
			return $from_1->value;
		}
	}
}
