<?php

namespace App\Advertises;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'advertise';
	protected $table = 'advertise_statuses';
}
