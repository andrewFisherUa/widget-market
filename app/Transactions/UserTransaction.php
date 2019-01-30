<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserTransaction extends Model
{
	protected $table = 'user_transactions';
    protected $fillable = [
        'day', 'user_id'
    ];
	
	
	
}
