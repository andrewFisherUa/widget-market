<?php 
Route::group(['prefix' => 'mpteaser'
, 'as' => 'mpteaser.'
,'middleware' => ['web', 'auth']
,'namespace'=>'Mplacegit\Teaser\Controllers']
,function (){
	  Route::get('offers/{id}', ['as' => 'offers','uses'=>'OffersController@index']);
	  Route::post('offers/{id}', ['as' => 'offers','uses'=>'OffersController@save']);
#	  Route::get('partner_product/{id}', ['as' => 'partner_product','uses'=>'ProductController@partner']);
#	  Route::get('partners_product', ['as' => 'partners_product','uses'=>'ProductController@partners']);
#	  Route::get('pads_product', ['as' => 'pads_product','uses'=>'ProductController@pads']);
#	  Route::get('summa_product', ['as' => 'summa_product','uses'=>'ProductController@index']);
#	  Route::get('partner_teaser/{id}', ['as' => 'partner_teaser','uses'=>'TeaserController@partner']);
#	  Route::get('partners_teaser', ['as' => 'partners_teaser','uses'=>'TeaserController@partners']); 
#	  Route::get('pads_teaser', ['as' => 'pads_teaser','uses'=>'TeaserController@pads']); 
#	  Route::get('summa_teaser', ['as' => 'summa_teaser','uses'=>'TeaserController@index']); 
#         Route::get('loaded/{id}', ['as' => 'loaded_server','uses'=>'LoadedController@server']);
#         Route::get('loaded', ['as' => 'loaded','uses'=>'LoadedController@index']);
#	  Route::get('cpa', ['as' => 'cpa','uses'=>'CpaController@index']);
});
