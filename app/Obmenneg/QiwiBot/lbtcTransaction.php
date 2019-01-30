<?php

namespace App\Obmenneg\QiwiBot;

use Illuminate\Database\Eloquent\Model;

class lbtcTransaction extends Model
{
	protected $connection= 'obmenneg';
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
}
