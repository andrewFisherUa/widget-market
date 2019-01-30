<?php

namespace App\MPW\Widgets;

use Illuminate\Database\Eloquent\Model;

class VideoSettings extends Model
{
    protected $connection = 'videotest';
	protected $table = 'pid_video_settings';
	public $timestamps = false; 

	protected $fillable = [
        'wid_id'
    ];
	
}
