<?php

namespace App\MPW\Widgets;

use Illuminate\Database\Eloquent\Model;

class Video extends Widget
{
    protected $fillable = array('*');
    protected $attributes = array(
   'type' => 2,
   'status' => 0
    );

}
