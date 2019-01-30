<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Api extends Controller
{
	
   public function setSavePage(Request $request){
		$data=$request->toArray();
		$idserver=$request->input('idserver');
		$md5=$request->input('md5');
		$text=$request->input('text');
		$id_category=$request->input('id_category');
		$sql="
		update widget_pages_ind
		set id_category ='".$id_category."', searchtext='".$text."' WHERE id_server=".$idserver." and hash='".$md5."';
		insert into widget_pages_ind(
    id_server,
    hash,
	id_category,
    searchtext )
	select ".$idserver.",'".$md5."','".$id_category."','".$text."'
	WHERE NOT EXISTS (SELECT 1 FROM widget_pages_ind WHERE id_server=".$idserver." and hash='".$md5."')
	";
	   $this->removeCache(); 
	  $pdo=\DB::connection("cluck")->getPdo();
	  $pdo->exec($sql);
	    $this->removeCache();
		    return response()->json([
			'ok' => true
			]);
	  
   }
   public function setStatusMaska(Request $request){
		$data=$request->toArray();
		$idserver=$request->input('idserver');
		$md5=$request->input('md5');
		$flag=$request->input('flag');
		$sql="
		update widget_pages_ind
		set maskaflag=".$flag." WHERE id_server=".$idserver." and hash='".$md5."';
		insert into widget_pages_ind(
    id_server,
    hash,
    maskaflag )
	select ".$idserver.",'".$md5."',".$flag."
	WHERE NOT EXISTS (SELECT 1 FROM widget_pages_ind WHERE id_server=".$idserver." and hash='".$md5."')
	";
	   $pdo=\DB::connection("cluck")->getPdo();
	   $pdo->exec($sql);
	   $this->removeCache();
		return response()->json([
			'ok' => true
			]);
	}
	public function setStatusPage(Request $request){
		$data=$request->toArray();
		$idserver=$request->input('idserver');
		$md5=$request->input('md5');
		$flag=$request->input('flag');
		$nombre=$request->input('nombre');
		$sql="
		update widget_pages_ind
		set status=".$flag." WHERE id_server=".$idserver." and hash='".$md5."';
	    ";
	   $pdo=\DB::connection("cluck")->getPdo();
	   $pdo->exec($sql);
	   $this->removeCache();
		return response()->json([
			'ok' => true
			]);
	}
  public function removeCache(){
	  $cache = file_get_contents('https://widget.market-place.su/product_cache/cache.txt');
	  if(!$cache) $cache=0;
	  $cache=intval($cache)+1;
	  file_put_contents('/home/www/widget.market-place.su/public/product_cache/cache.txt',$cache);
	  
  }	
}
