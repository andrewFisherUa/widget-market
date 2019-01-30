<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserReferalTransacion extends Model
{
	protected $table = 'user_referal_transacions';
    protected $fillable = [
        'day', 'user_id'
    ];
	
	
	
}
