<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserLinkSumma extends model
{
    protected $table = 'user_link_summas';
	protected $fillable = [
        'user_id', 'link_id'
    ];

}
