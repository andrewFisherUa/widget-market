<?php

namespace App\Payments;

use Illuminate\Database\Eloquent\Model;

class PaymentCommission extends Model
{
   protected $table = 'payment_commissions';
   public function save(array $options = [])
   {
      // before save code 
      parent::save();
	  $ssu=new \App\Models\Advertises\Payment();
	  return $ssu->insertSumma($this->user_id,$this->who_add,$this->commission);	 
	 
   }
}
