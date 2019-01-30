<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BrandOffer extends Model
{
    //	

	public function toBlocks(){
		return $this->belongsToMany(
            'App\BrandOffer', 'brand_block_links',
            'id_block', 'id_link'
        );
	}
}
