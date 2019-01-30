<?php

namespace App\Models\Widgets;

use Illuminate\Database\Eloquent\Model;

class VideoWidget extends Model
{
     protected $connection= 'mysqlapi';

  public function CommissionGroup() { 
       return $this->hasMany('App\Models\Widgets\CommissionGroup', 'commissiongroupid', 'commission_group')->first();
  }  
  public function ForeignCommissionGroup() {
       return $this->hasMany('App\Models\Widgets\ForeignCommissionGroup', 'commissiongroupid', 'f_commission_group')->first();
      
  } 
}
