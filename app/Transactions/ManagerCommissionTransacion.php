<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ManagerCommissionTransacion extends Model
{
	protected $table = 'manager_commission_transacions';
    protected $fillable = [
        'day', 'user_id'
    ];
	
	
	
}
