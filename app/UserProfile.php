<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class UserProfile extends Authenticatable
{
	use EntrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','user_id','firstname', 'lastname', 'name', 'email'
    ];
	
	public function User()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
