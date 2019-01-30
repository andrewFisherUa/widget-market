<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VideoSource extends Authenticatable
{
    protected $connection = 'videotest';
    protected $table = 'links';
	private static $instances=[];
	protected $hidden = ['pivot'];
    protected $fillable = [
        'src', 'title'
    ];
	
	public function toBlocks(){
		return $this->belongsToMany(
		'App\VideoBlock', 'blocks_links',
		'id_link','id_block');
	}
	
	public static function getInstance($id) {

     if(!isset(self::$instances[$id])){
      self::$instances[$id]= self::find($id);
      }
      return self::$instances[$id];
    }

}
