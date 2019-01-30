<?php
namespace App\MPW\Widgets;

use Illuminate\Database\Eloquent\Model;

class AdvertCategory extends Model
{
	 protected $connection = 'cluck';
	 public $timestamps = false;
	 protected $table = 'main_categories';
}
