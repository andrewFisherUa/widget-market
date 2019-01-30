<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoDefaultOnUser extends Model
{
	protected $fillable = [
        'user_id', 'pad_type', 'wid_type',
    ];
	public function videoCommisssion($commission){
		return \DB::table('сommission_groups')->where('commissiongroupid', $commission)->first()->value;
	}
}
