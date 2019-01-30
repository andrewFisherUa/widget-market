<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class AdvertSettingsController extends Controller
{
   public function categories(){
	   $categories = \DB::connection("cluck")->table("main_categories as t")->orderBy("name")->get();
	   return view('admin.advert_settings.categories', ["categories"=>$categories]);
   }
   public function createCategory(){
	   $category=new \App\MPW\Widgets\AdvertCategory;
	   $category->id=0;
	    $yandex_categories=[];
	    return view('admin.advert_settings.add_category', ["category"=>$category,"yandex_categories"=>$yandex_categories]);
   }
   public function category($id){
	   $category = \DB::connection("cluck")->table("main_categories as t")->where("id",$id)->first();
	   if(!$category){
	   abort(404); }
	   
	   $yandex_categories= \DB::connection("cluck")->table("main_categories_yandex as p")
	   ->join("yandex_categories", "id","id_yandex")
	   ->where("id_category",$id)
	   ->orderBy("name")
	   ->get();
	   #var_dump($yandex_categories->toArray()); die();
	   return view('admin.advert_settings.category', ["category"=>$category,"yandex_categories"=>$yandex_categories]);
   }
     public function saveCategory($id = null,Request $request){
	
		$name=$request->input('name');
		$templates=$request->input('templates');
		$yandex_categories=$request->input('cattuya');
		
		$validator=Validator::make(
			array(
				'name' => $name
			),
			array(
				'name' => 'required|string|max:255',
			));
			if ($validator->fails()){
          
			return back()->withErrors($validator)->withInput();
			
			 
			}
		if($id)
		$category=\App\MPW\Widgets\AdvertCategory::find($id);
	    else
		$category=new \App\MPW\Widgets\AdvertCategory;
	    $category->name=$name;
		$category->templates=$templates;
		$category->save();
		\DB::connection("cluck")->table("main_categories_yandex")->where("id_category",$category->id)->delete();
		if(is_array($yandex_categories)){
			foreach($yandex_categories as $yk){
				foreach($yk as $key=>$value){
				\DB::connection("cluck")->table("main_categories_yandex")->insert(
				  ['id_yandex' => $key
				, 'id_category' => $category->id]
				);
				}
			}
			# print "<pre>"; print_r($yandex_categories); print "</pre>"; die();
		}
	 return redirect()->route('advert_setting.advert_category',["id"=>$category->id])->with('message_success','Вещи сохранились');
	 #return back()->with('message_success','Все вещи сохранились');	

   } 
  public function deleteCategory($id){
	   $category = $category=\App\MPW\Widgets\AdvertCategory::find($id);
	   \DB::connection("cluck")->table("main_categories_yandex")->where("id_category",$id)->delete();
	   if(!$category){
	   abort(404); }
	   $name=$category->name;
	   $category->delete();
	 
	   return redirect()->route('advert_setting.advert_categories')->with('message_success','Категория '.$name.'удалена навсегда');
	   #var_dump($id); die();
    } 
}

