<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersPaymentOption extends Model
{

    protected $fillable = [
        'user_id','payment_id'
    ];

}
