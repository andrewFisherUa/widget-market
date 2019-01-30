<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VideoClick extends Authenticatable
{
	use EntrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'src','fromUrl', 'count_view', 'count_clicks', 'ratio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
