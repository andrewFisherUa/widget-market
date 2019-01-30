<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class RegistrationLog extends model
{
    protected $table = 'registration_log';
	protected $fillable = [
        'user_id', 'name', 'email', 'ip'
    ];

}
