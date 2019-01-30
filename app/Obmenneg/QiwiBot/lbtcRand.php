<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class lbtcRand extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	private $leng=8;
	public function createString(){
		$chars = 'abcdefghijkmnopqrstuvwxyz0123456789';
		$numChars = strlen($chars);
		$string = '';
		for ($i = 0; $i < $this->leng; $i++) {
			$string .= substr($chars, rand(1, $numChars) - 1, 1);
		}
		return $string;
	}
}
