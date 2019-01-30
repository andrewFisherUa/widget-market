<?php

namespace App\Widgets\Advert;
use MyCalendar;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Route;
class Raspisanie extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run(Request $request)
    {
       $m=Route::current()->parameters();
	   //var_dump($m);
	$wsh=[];	
	 if(!isset($m["id"])){
	  $wsh=$request->old('shedule');
	 }else{

		$shedularies=\DB::connection("advertise")->table('shop_schedule_week')->select('ind')->where('shop_id','=',$m["id"])->get();
	
		foreach($shedularies as $ind){
			$r=MyCalendar::getHashData($ind->ind);
			$wsh[$r[0]][$r[1]]='on';
		#print "{$ind->ind} [{$r[0]}/{$r[1]}]<hr>";	
		}
		 
	 }
	 if($wsh){
	// var_dump($wsh);
	 //die();
	 }
	 //$wsh=[];
	   #var_dump($ssp); //die();
       #$k=Request::old('shedule');
        return view('widgets.advert.raspisanie', [
            'config' => $this->config,'wsh'=>$wsh
        ]);
    }
}
