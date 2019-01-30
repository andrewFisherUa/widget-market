<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;
use App\User;

class BalanceOnHome extends Model
{
	protected $table = 'balance_on_homes';
    protected $fillable = [
        'day', 'user_id'
    ];
	
	
	
}
