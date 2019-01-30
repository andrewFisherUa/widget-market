<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WidgetBrand extends Model
{
	private static $instances=[];
	protected $table='widget_brands';
	protected $fillable = [
        'wid_id'
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
