<?php

namespace App\MPW\Widgets;

use Illuminate\Database\Eloquent\Model;

class Product extends Widget
{
       protected $attributes = array(
       'type' => 1,
	   'status' => 0
       );
	   public static function getPicture($id,$url){
		   if(!$url){
			return "https://widget.market-place.su/images/cabinet/no_foto.png";
		   }
		if(preg_match('/^http\:/i',$url)){
		return 'https://newapi.market-place.su/api/img/'.$id;
		}else{
		return $url;
		}
	   }
}
