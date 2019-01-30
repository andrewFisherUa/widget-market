<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdvertiseTeaser extends Model
{
    //
    protected $connection = 'advertise';

	protected $fillable = [
        'user_id'
    ];
}
