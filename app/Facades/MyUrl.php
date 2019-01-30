<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
class MyUrl extends Facade{
	
    protected static function getFacadeAccessor() {
		return 'MyUrl'; 
		}
	
}
