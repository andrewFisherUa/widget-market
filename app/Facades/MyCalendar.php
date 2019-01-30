<?php 
namespace App\Facades;
use Illuminate\Support\Facades\Facade;
class MyCalendar extends Facade{
    protected static function getFacadeAccessor() { return 'mycalendar'; }
}
