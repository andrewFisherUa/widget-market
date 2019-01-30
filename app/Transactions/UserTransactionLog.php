<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserTransactionLog extends Model
{
	protected $table = 'user_transaction_logs';
    protected $fillable = [
        'day', 'user_id'
    ];
	
	
	
}
