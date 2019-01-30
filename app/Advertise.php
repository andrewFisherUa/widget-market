<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
class Advertise extends Model
{
    //
    protected $connection = 'advertise';

	protected $fillable = [
        'user_id'
    ];
	public function Status(){

        return $this->hasOne('\App\Advertises\Status','id','status');

	}
	public function getLastUpdate(){
	
	if(!$this->last_time) return '';
	//var_dump($this->last_time); //2017-12-14 15:02:17.998908
	return Carbon::createFromFormat('Y-m-d H:i:s.u', $this->last_time)->toDateTimeString();
	}
	
	public function UserStatus(){
		$user_st=\DB::connection('advertise')->table('advertise_user_status')->select('status')->where('id_company', $this->id)->get();
		$stats=array();
		foreach ($user_st as $st){
			foreach ($st as $s){
				array_push($stats, $s);
			}
		}
		return $stats;
	}
	public function save(array $options = array())
     {
    // before save code
     if($this->id) { 
	$status=$this->status;
	$oldStatus=$this->getOriginal('status');
	
	$name=$this->name;
	$oldName=$this->getOriginal('name');
	$limit_clicks=$this->limit_clicks;
	$oldLimit_clicks=$this->getOriginal('limit_clicks');
	/*
	
    0   Ожидает модерации
    1   В работе
    2   Отклонён	
	4	Приостановлен
    5	Удалён
    6 	Недостаток средств
	*/
	
	
	if($oldStatus!=$status && $this->id){
		$key='microdolg_'.$this->id;
		switch($status){
		case 4:
		case 0:
		case 6:		
		Redis::command('set', [$key,time()]);
		break;
		case 2:
		case 5:
		Redis::command('set', [$key,1]);
		break;
		case 1:
		Redis::command('del', [$key]);
		break;

		}
		#print "<pre>"; print_r([$oldStatus,$status,$oldName,$name]); print "</pre>"; die();
	}
	 }
    parent::save();
    }
}
