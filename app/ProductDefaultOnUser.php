<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDefaultOnUser extends Model
{
	protected $fillable = [
        'user_id', 'driver'
    ];
}
