<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class CalculatorAndr // extends Model
{
	
	public function StartDay(){
		$from="2018-08-15";
		$to="2018-08-22";
		$pid=752;
		$pdo = \DB::connection("videotest")->getPdo();
		$sql= "select * from pid_summa_full";
		$stats=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
	}
}

