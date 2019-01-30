<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VideoBlock extends Authenticatable
{
 protected $connection = 'videotest';
 protected $table = 'blocks';


public $timestamps = false;


    public function Sources(){
	  $sources=$this->belongsToMany(
            'App\VideoSource', 'blocks_links',
            'id_block', 'id_link'
        );
	return $sources->orderByRaw('case when prioritet>0 then prioritet when prioritet<0 then 999999 else 99999 end')->orderBy('sort', 'asc');
		return $sources;
	//var_dump($sources); die();
        $sources=$this->belongsToMany(
            'App\VideoSource', 'video_block_to_sources',
            'id_block', 'id_source'
        );
	    return $sources->select('id', 'title', 'src', 'timeout', 'limit', 'player', 'cheap')->orderBy('order', 'asc');
	}
	
	public function SourcesOnAuto(){
	  $sources=$this->belongsToMany(
            'App\VideoSource', 'blocks_links',
            'id_block', 'id_link'
        )->withPivot('autosort');
	return $sources->orderBy('autosort', 'asc');
		return $sources;
	//var_dump($sources); die();
        $sources=$this->belongsToMany(
            'App\VideoSource', 'video_block_to_sources',
            'id_block', 'id_source'
        );
	    return $sources->select('id', 'title', 'src', 'timeout', 'limit', 'player', 'cheap')->orderBy('order', 'asc');
	}
	
	public function SourcesOnAutoPrioritet(){
	  $sources=$this->belongsToMany(
            'App\VideoSource', 'blocks_links',
            'id_block', 'id_link'
        );
	return $sources->orderByRaw('case when prioritet>0 then prioritet when prioritet<0 then 999999 else 99999 end')->orderBy('autosort', 'asc');
		return $sources;
	//var_dump($sources); die();
        $sources=$this->belongsToMany(
            'App\VideoSource', 'video_block_to_sources',
            'id_block', 'id_source'
        );
	    return $sources->select('id', 'title', 'src', 'timeout', 'limit', 'player', 'cheap')->orderBy('order', 'asc');
	}
	
	public function saveOptionsFile($str){
        $path = public_path()."/video_blocks/".$this->id.".json";
        file_put_contents($path,$str);
	}

}
