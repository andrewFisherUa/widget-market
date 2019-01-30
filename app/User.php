<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Mail;

class User extends Authenticatable
{
	use EntrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
 
	private static $instances=[];
    protected $fillable = [
        'name', 'email', 'password',
    ];
	public function sendPasswordResetNotification($token)
    {
		$email=$this->email;
		//здесь тоже поменять
		Mail::send('email.register.password_reset', ['token' => $token], function($message) use ($email){
			$message->from('support@market-place.su');
			$message->to($email)->subject('Сброс пароля');
		});
    }
	
	public function Profile()
    {
        return $this->hasOne('App\UserProfile','user_id','id');
    }
	public function ManagerCommission()
    {
        return $this->hasOne('App\ManagerCommission','user_id','id');
    }
	public function roles()
	{
		return $this->belongsToMany('App\Role');
	}

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public function ManagerOnUsers(){
		#return $this->hasOne('App\UserProfile','id','manager');
		return $this->hasMany('App\UserProfile','manager','id');
	}
	public static function getInstance($id) {

     if(!isset(self::$instances[$id])){
      self::$instances[$id]= self::find($id);
      }
      return self::$instances[$id];
    }
}
