<?php

namespace App\MoneyReport;

use Illuminate\Database\Eloquent\Model;

class ReportOperation extends Model
{
    protected $connection= 'report';
	
	public function account()
	{
		return $this->hasOne('App\MoneyReport\Account', 'id', 'accounts_id');
	}
}
