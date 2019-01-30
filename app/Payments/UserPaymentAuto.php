<?php

namespace App\Payments;

use Illuminate\Database\Eloquent\Model;

class UserPaymentAuto extends Model
{
    protected $table = 'user_payment_auto';
	protected $fillable = [
        'user_id'
    ];
}
