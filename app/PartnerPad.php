<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PartnerPad extends Authenticatable
{

    protected $fillable = [
        'user_id', 'domain'
    ];
	public function setType($type){
		$cnt=count($type);
		if ($cnt>3){
			$ctype=-1;
		}
		else if ($cnt==2 or $cnt==3){
			$product=0;
			$video=0;
			$tizer=0;
			$brand=0;
			foreach ($type as $t){
				if ($t==1){
					$product=$t;
				}
				else if ($t==2){
					$video=$t;
				}
				else if ($t==4){
					$tizer=$t;
				}
				else if ($t==8){
					$brand=$t;
				}
			}
			$ctype=$product+$video+$tizer+$brand;
		}
		else{
			foreach ($type as $tp){
				$ctype=$tp;
			}
		}
		return $ctype;
	}
	public function setUser($user_id){
		$user=User::find($user_id);
		return $user;
	}
	
	public function userProfile($user_id){
		$user=\App\UserProfile::where('user_id', $user_id)->first();
		return $user;
	}

}
