<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BrandBlock extends Authenticatable
{

public $timestamps = false;


    public function Sources(){
		$sources=$this->belongsToMany(
            'App\BrandOffer', 'brand_block_links',
            'id_block', 'id_link'
        );
		return $sources->orderBy('sort', 'asc');
	}
	
	public function saveOptionsFile($str){
        //$path = public_path()."/video_blocks/".$this->id.".json";
        //file_put_contents($path,$str);
	}

}
