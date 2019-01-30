<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WidgetVideo extends Model
{
        private static $instances=[];
	protected $table='widget_videos';
	protected $fillable = [
        'wid_id',
		'id'
    ];
	public function widget() {
		return $this->hasOne('App\MPW\Widgets\Widget', 'id', 'wid_id');
	}
    public static function getInstance($id) {

     if(!isset(self::$instances[$id])){
      self::$instances[$id]= self::find($id);
      }
      return self::$instances[$id];
    }
	


}
