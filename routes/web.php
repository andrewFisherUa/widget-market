<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
//Route::get('/', 'HomeController@index');
		//var_dump(\Request::ip());
		//exit;
Route::get('/', 'TestHomeController@index')->name('index');
Route::get('/home', 'TestHomeController@index')->name('home');
//Регистрация с подтверждением на email ---------------------------------------------------
Route::get('register', ['as' => 'register', 'uses' => 'Auth\\AuthController@create']);
Route::post('register',['as' => 'register','uses'=>'Auth\\AuthController@createSave']);
Route::get('registration', ['as' => 'registration', 'uses' => 'Auth\\AuthController@createRegister']);
Route::get('register/confirm/{token}',['as' => 'register.confirm','uses'=>'Auth\\AuthController@confirm']);
Route::get('register_repeat', 'Auth\\AuthController@repeat');
Route::post('register_repeat','Auth\\AuthController@repeatPost');
Route::get('login', ['as' => 'login', 'uses' => 'Auth\\AuthController@login']);
Route::get('loginyan', ['as' => 'loginyan', 'uses' => 'Auth\\AuthController@loginYan']);
Route::post('login', ['as' => 'login', 'uses' => 'Auth\\AuthController@loginPost']);
Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\\AuthController@logout']);
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('password/change', ['as'=>'password.change', 'uses' => 'Auth\\ChangePasswordController@change']);
//-----------------------------------------------------------


//Тест для ссылок от 28.08.2018

Route::get('testing_links/video/{id}', ['as'=>'testlinks','uses' => 'NewsController@delete']);
Route::get('my_ip', function () {
  return $_SERVER['REMOTE_ADDR'];
});

Route::get('userrr/{id}/profile', function ($id) {
  return $id;
  
})->name('profile');

//$url = route('profile', ['id' => 1]);


//Тест для Максима-----------------------------------
Route::get('test_graph', ['uses' =>'VideoClickController@index']);
Route::get('test_deep', ['uses' =>'VideoClickController@deep']);
Route::get('test_pad/{id?}', ['uses' =>'VideoClickController@pad']);
#Route::get('test_advert', ['uses' =>'AdvertiserController@add_company']);
#Route::post('test_advert', ['uses' =>'AdvertiserController@add_company_post']);
//-------------------------------
Route::post('/adv_/invoice_status_post/{id}', ['as'=>'advertiser.invoice_status_post','middleware' => ['role:admin|manager|super_manager'], 'uses'=>'AdvertiserController@invoice_status']);

Route::group(['prefix' => 'admin/{id_user}/','as'=>'admin.'
,'middleware' => ['role:admin|manager|super_manager']]
, function()
{
Route::get('home',['as'=>'home','uses'=> 'TestHomeController@index']);
//tiser 
Route::get('edit_company_teaser/{id}', ['as'=>'edit_company_teaser','uses' =>'AdvertiserController@admin_edit_company_teaser']);

//rekl
Route::get('disco', ['as'=>'disco','uses' =>'AdvertiserController@disco']);
Route::get('invoices', ['as'=>'invoices_history','uses' =>'AdvertiserController@invoices_history']);
Route::get('invoice/{id}/view', ['as'=>'invoice_view','uses' =>'AdvertiserController@invoice_view']);	
Route::get('invoice/{id}/print', ['as'=>'invoice_print','uses' =>'AdvertiserController@invoice_print']);	
Route::get('invoice/create', ['as'=>'invoice_create','uses' =>'AdvertiserController@invoice_create']);	
Route::get('edit_company/{id}', ['as'=>'edit_company','uses' =>'AdvertiserController@admin_edit_company']); 
Route::get('statistic/{shop_id}/{mod}/{vid}/detail', ['as'=>'statistic_detail','uses' =>'AdvertiserController@admin_statistic_detail']);
Route::get('statistic/{shop_id}/{mod?}', ['as'=>'statistic','uses' =>'AdvertiserController@admin_statistic']);
Route::get('history/{shop_id}/{mod?}', ['as'=>'balance_history','uses' =>'AdvertiserController@admin_balance_history']);
#Route::get('edit_company/{id}', ['as'=>'edit_company','uses' =>'AdvertiserController@admin_edit_company']); 

Route::get('profile', ['as'=>'profile.personal', 'middleware' => 'auth', 'uses'=> 'HomeController@profile']);
Route::get('user_no_active', ['as'=>'user_no_active', 'middleware' => 'auth', 'uses'=> 'HomeController@userNoActive']);
Route::get('user_active', ['as'=>'user_active', 'middleware' => 'auth', 'uses'=> 'HomeController@userActive']);
Route::post('user_for_manager', ['as'=>'user_for_manager', 'middleware' => 'auth', 'uses'=> 'HomeController@userForManager']);
Route::post('user_for_dop_status', ['as'=>'user_for_dop_status', 'middleware' => 'auth', 'uses'=> 'HomeController@userForDopStatus']);
Route::post('user_for_video_default', ['as'=>'add_video_default_on_users', 'middleware' => 'auth', 'uses'=> 'HomeController@userDefaultVideo']);
Route::post('user_for_video_default_delete', ['as'=>'add_video_default_on_users_delete', 'middleware' => 'auth', 'uses'=> 'HomeController@userDefaultVideoDestroy']);
Route::post('user_for_product_default_delete', ['as'=>'add_product_default_on_users_delete', 'middleware' => 'auth', 'uses'=> 'HomeController@userDefaultProductDestroy']);
Route::post('user_control_summa_delete', ['as'=>'user_control_summa_delete', 'middleware' => 'auth', 'uses'=> 'HomeController@userControlSummaDestroy']);
});
//Ajax
Route::post('user_for_manager_js', ['as'=>'user_for_manager_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userForManagerJs']);
Route::post('user_for_dop_status_js', ['as'=>'user_for_dop_status_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userForDopStatusJs']);
Route::post('user_for_default_js', ['as'=>'add_default_on_users_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userDefaultJs']);
Route::post('user_for_video_default_delete_js', ['as'=>'add_video_default_on_users_delete_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userDefaultVideoDestroyJs']);
Route::post('user_for_product_default_delete_js', ['as'=>'add_product_default_on_users_delete_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userDefaultProductDestroyJs']);
Route::post('user_control_summa_delete_js', ['as'=>'user_control_summa_delete_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userControlSummaDestroyJs']);
Route::post('user_active_js', ['as'=>'user_active_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userActiveJs']);
Route::post('user_no_active_js', ['as'=>'user_no_active_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userNoActiveJs']);
Route::post('user_lease_js', ['as'=>'user_lease_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userLeaseJs']);
Route::post('user_no_lease_js', ['as'=>'user_no_lease_js', 'middleware' => 'auth', 'uses'=> 'HomeController@userNoLeaseJs']);

Route::post('user_detail_widgets/{id}', ['as'=>'post_on_home', 'middleware' => 'auth', 'uses'=> 'HomeController@getDetailWidgets']);
Route::post('user_detail_widgets_video/{id}', ['as'=>'post_on_home_video', 'middleware' => 'auth', 'uses'=> 'HomeController@getDetailWidgetsVideo']);
Route::post('user_detail_widgets_product/{id}', ['as'=>'post_on_home_product', 'middleware' => 'auth', 'uses'=> 'HomeController@getDetailWidgetsProduct']);
Route::post('user_detail_widgets_teaser/{id}', ['as'=>'post_on_home_teaser', 'middleware' => 'auth', 'uses'=> 'HomeController@getDetailWidgetsTeaser']);
Route::post('user_video_widgets', ['as'=>'post_on_home_video_widgets', 'middleware' => 'auth', 'uses'=> 'HomeController@getVideoWidgets']);
Route::post('user_product_widgets', ['as'=>'post_on_home_product_widgets', 'middleware' => 'auth', 'uses'=> 'HomeController@getProductWidgets']);
Route::group(['as'=>'news.','middleware' => ['role:admin|manager|super_manager']]
, function()
{
Route::get('add_news',['as'=>'add','uses'=> 'NewsController@addNews']);
Route::get('edit_news/{id}',['as'=>'edit','uses'=> 'NewsController@edit']);
Route::post('save_news', ['as'=>'save','uses' => 'NewsController@save']);
Route::post('update_news/{id}', ['as'=>'update','uses' => 'NewsController@update']);
Route::get('delete/{id}', ['as'=>'delete','uses' => 'NewsController@delete']);
Route::get('unsubscribe', ['as'=>'unsubscribe','uses' => 'NewsController@unsubscribe']);
});
Route::group(['as'=>'advert_statistic.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('pads_advert_statistic_summary_comparison', ['as'=>'pads_advert_summary_comparison', 'middleware'=>'auth', 'uses'=>'AdvertStatisticController@summaryPadsComparison']);
Route::get('pad_advert_statistic/{id}', ['as'=>'pad_statistic', 'middleware'=>'auth', 'uses'=> 'AdvertStatisticController@statsPad']);
Route::get('partner_advert_statistic_comparison', ['as'=>'partner_advert_summary_comparison', 'middleware'=>'auth', 'uses'=> 'AdvertStatisticController@partnerStatComparison']);
Route::get('partner_advert_statistic/{id}', ['as'=>'partner_statistic', 'middleware'=>'auth', 'uses'=> 'AdvertStatisticController@statsPartner']);
#Route::get('pads_advert_statistic_summary_comparison', ['as'=>'pads_advert_summary_comparison', 'middleware'=>'auth', 'uses'=>'AdvertStatisticController@summaryPadsComparison']);
});



Route::group(['as'=>'video_statistic.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('video_rez',['as'=>'rez','uses'=> 'RezController@Rez']);
Route::get('video_pids',['as'=>'pids','uses'=> 'VideoStatisticController@allPids']);
Route::get('video_statistic/graph',['as'=>'graph','uses'=> 'VideoStatisticController@graph']);
Route::get('video_statistic',['as'=>'video_all_stat','uses'=> 'VideoStatisticController@allStat']);
Route::get('video_statistic/source',['as'=>'video_source_stat','uses'=> 'VideoStatisticController@sourceStat']);
Route::get('video_statistic/source/{id}',['as'=>'video_source_stat_detail','uses'=> 'VideoStatisticController@sourceStatDetail']);
Route::get('new_video_statistic/source', ['as'=>'new_video_stat', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@newSourceStat']);



Route::get('new_video_statistic/source_comparison', ['as'=>'new_video_stat_comparison', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@newSourceStatComparison']);
Route::get('new_video_statistic/source/{id}', ['as'=>'new_video_stat_detail', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@newSourceStatDetail']);
Route::get('new_video_statistic/graph',['as'=>'new_graph', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@newGraph']);
Route::get('video_statistic_summary', ['as'=>'video_summary', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@summaryStat']);
Route::get('pads_video_statistic_summary', ['as'=>'pads_video_summary', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@summaryPads']);
Route::get('pads_video_statistic_summary_comparison', ['as'=>'pads_video_summary_comparison', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@summaryPadsComparison']);
Route::get('video_statistic_hour', ['as'=>'video_hour', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@hourStat']);


Route::get('pad_video_statistic/{id}', ['as'=>'pad_statistic', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@statsPad']);
Route::get('partner_video_statistic', ['as'=>'partner_video_summary', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@partnerVideoStat']);
Route::get('partner_video_statistic_comparison', ['as'=>'partner_video_summary_comparison', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@partnerVideoStatComparison']);

Route::get('partner_detail_video_statistic/{id}', ['as'=>'partner_detail_video_summary', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@partnerDetailVideoStat']);
Route::get('pid_urls_video_statistic/{id}', ['as'=>'pid_urls_video_statistic', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@pidUrlsStatistic']);

Route::get('pid_pad_statistic/{id}', ['as'=>'pid_pad', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@statsPidPad']);
Route::get('video_frame_stat',['as'=>'frame', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@frame']);
Route::get('video_frame_stat_user',['as'=>'frame.user', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@frameUser']);
Route::get('video_frame_stat_detail/{id}',['as'=>'frame.detail', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@frameDetail']);

Route::get('video_group_ip/{id}',['as'=>'group.ip', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@groupIp']);
Route::get('video_group_ip_all/',['as'=>'group.ip.all', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@groupIpAll']);

Route::get('frame_prov/{id}', ['as'=>'frame_prov', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@frameUserId']);
Route::get('frame_stat/{id}', ['as'=>'frame_stat_user', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@frameStatUser']);
Route::get('frame_stat_detal/{id}', ['as'=>'frame_stat_user_detail', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@frameStatUserDetail']);
});
Route::get('all_stat_sum', ['as'=>'admin.all_stat_sum', 'middleware'=>'role:admin', 'uses'=> 'AdminStatisticController@index']);
Route::group(['as'=>'product_statistic.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('product_statistic/summary',['as'=>'product_summary','uses'=> 'ProductStatisticController@summaryStat']);
Route::get('product_statistic/detail_users',['as'=>'product_detail_users','uses'=> 'ProductStatisticController@detailUser']);
Route::get('product_statistic/detail_user/{id}',['as'=>'product_detail_user_one','uses'=> 'ProductStatisticController@detailUserOne']);
Route::get('product_statistic/detail_pads',['as'=>'product_detail_pads','uses'=> 'ProductStatisticController@detailPads']);
Route::get('product_statistic/detail_pad/{id}',['as'=>'product_detail_pad_one','uses'=> 'ProductStatisticController@detailPadOne']);
Route::get('product_statistic/graph',['as'=>'product_graph','uses'=> 'ProductStatisticController@graph']);
Route::get('pid_urls_product_statistic/{id}', ['as'=>'pid_url_statistic', 'middleware'=>'auth', 'uses'=> 'ProductStatisticController@statsPidUrl']);
}

);

Route::group(['as'=>'teaser_statistic.', 'middleware'=>['role:admin|super_manager|manager']]
, function()
{
Route::get('partner_teaser_statistic_comparison', ['as'=>'partner_teaser_summary_comparison', 'middleware'=>'auth', 'uses'=> 'TeaserStatisticController@partnerStatComparison']);
Route::get('pads_teaser_statistic_summary_comparison', ['as'=>'pads_teaser_summary_comparison', 'middleware'=>'auth', 'uses'=>'TeaserStatisticController@summaryPadsComparison']);
Route::get('teaser_statistic/summary', ['as'=>'teaser_summary', 'uses'=>'TeaserStatisticController@summaryStat']);
Route::get('teaser_statistic/detail_users',['as'=>'teaser_detail_users','uses'=> 'TeaserStatisticController@detailUser']);
Route::get('teaser_statistic/detail_user/{id}',['as'=>'teaser_detail_user_one','uses'=> 'TeaserStatisticController@detailUserOne']);
Route::get('teaser_statistic/detail_pads',['as'=>'teaser_detail_pads','uses'=> 'TeaserStatisticController@detailPads']);
Route::get('teaser_statistic/detail_pad/{id}',['as'=>'teaser_detail_pad_one','uses'=> 'TeaserStatisticController@detailPadOne']);
}
);

Route::group(['as'=>'brand_statistic.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('pid_urls_brand_statistic/{id}', ['as'=>'pid_url_statistic', 'middleware'=>'auth', 'uses'=> 'BrandStatisticController@statsPidUrl']);
Route::get('brand_statistic_summary', ['as'=>'summary_statisitc', 'middleware'=>'auth', 'uses'=>'BrandStatisticController@summaryStat']);
Route::get('brand_statistic_source', ['as'=>'source_statisitc', 'middleware'=>'auth', 'uses'=>'BrandStatisticController@sourceStat']);
Route::get('brand_statistic_one_source/{id}', ['as'=>'one_source_statisitc', 'middleware'=>'auth', 'uses'=>'BrandStatisticController@oneSourceStat']);
}
);
Route::group(['as'=>'brand_statistic_pid.','middleware' => ['role:admin|super_manager|manager|affiliate']]
, function()
{
Route::get('pid_brand_statistic/{id}', ['as'=>'pid_statistic', 'middleware'=>'auth', 'uses'=> 'BrandStatisticController@statsPid']);
});
Route::group(['as'=>'video_statistic_pid.','middleware' => ['role:admin|super_manager|manager|affiliate']]
, function()
{
Route::get('pid_video_statistic/{id}', ['as'=>'pid_statistic', 'middleware'=>'auth', 'uses'=> 'VideoStatisticController@statsPid']);
Route::get('domain_stat_detal/{id}', ['as'=>'domain_stat_datal', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@DomainStatDetal']);
Route::post('domain_stat_detal_excel', ['as'=>'domain_stat_excel', 'middleware'=>'auth', 'uses'=>'VideoStatisticController@DomainStatDetalExcel']);
});
Route::group(['as'=>'product_statistic_pid.','middleware' => ['role:admin|super_manager|manager|affiliate']]
, function()
{
Route::get('pid_product_statistic/{id}', ['as'=>'pid_statistic', 'middleware'=>'auth', 'uses'=> 'ProductStatisticController@statsPid']);
});
Route::group(['as'=>'teaser_statistic_pid.','middleware' => ['role:admin|super_manager|manager|affiliate']]
, function()
{
Route::get('pid_teaser_statistic/{id}', ['as'=>'pid_statistic', 'middleware'=>'auth', 'uses'=> 'TeaserStatisticController@statsPid']);
});
Route::group(['as'=>'global.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('trash_users', ['as'=>'trash_users', 'middleware' => 'auth', 'uses'=> 'HomeController@trashUsers']);
Route::get('globaltable', ['as'=>'table', 'middleware'=>'auth', 'uses'=>'HomeController@globalTable']);
Route::get('referals', ['as'=>'referals', 'middleware'=>'auth', 'uses'=>'HomeController@allReferals']);
Route::get('registration_log', ['as'=>'registration.log', 'uses' => 'AdminStatisticController@RegistrLog']);
});
Route::group(['as'=>'video_setting.','middleware' => ['role:admin|super_manager']]
, function()
{

Route::get('video_sources',['as'=>'sources','uses'=> 'VideoSettingsController@allSources']);
Route::get('video_source_add',['as'=>'source.add','uses'=> 'VideoSettingsController@addSource']);
Route::post('video_source_new',['as'=>'source.new','uses'=> 'VideoSettingsController@addSourceNew']);
Route::get('video_sources_defolte',['as'=>'sources.defolte','uses'=> 'VideoSettingsController@allSourcesDefolte']);
Route::post('video_sources_defolte_post',['as'=>'sources.defolte.post','uses'=> 'VideoSettingsController@allSourcesDefoltePost']);
Route::post('video_source_update/{id}',['as'=>'source.update','uses'=> 'VideoSettingsController@updateSource']);
Route::get('video_sources/{id}/edit',['as'=>'source.edit','uses'=> 'VideoSettingsController@editSource']);
Route::get('video_source_delete/{id}',['as'=>'source.delete','uses'=> 'VideoSettingsController@deleteSource']);

Route::get('video_block',['as'=>'blocks.all', 'middleware' => 'auth', 'uses'=> 'VideoSettingsController@blocksAll']);
Route::get('video_block_edit/{id}',['as'=>'block.edit','uses'=> 'VideoSettingsController@blockEdit']);
Route::post('video_block_update/{id}',['as'=>'block.update','uses'=> 'VideoSettingsController@blockUpdate']);
Route::get('video_block_create',['as'=>'block.create','uses'=> 'VideoSettingsController@blockCreate']);
Route::post('video_block_save',['as'=>'block.save','uses'=> 'VideoSettingsController@blockSave']);
Route::get('video_settings_default', ['as'=>'default', 'uses'=>'VideoSettingsController@default']);
Route::get('video_settings_default/{id}', ['as'=>'default.id', 'uses'=>'VideoSettingsController@defaultOne']);
Route::post('video_settings_default_save', ['as'=>'default.save', 'uses'=>'VideoSettingsController@defaultSave']);

});

Route::group(['as'=>'brand_setting.', 'prefix'=>'brand', 'middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('all_source', ['as'=>'all.source', 'middleware'=>'auth', 'uses'=>'BrandController@allSource']);
Route::get('add_source', ['as'=>'add.source', 'middleware'=>'auth', 'uses'=>'BrandController@addSource']);
Route::get('edit_source/{id}', ['as'=>'edit.source', 'middleware'=>'auth', 'uses'=>'BrandController@editSource']);
Route::post('edit_source/{id}', ['as'=>'edit.source', 'middleware'=>'auth', 'uses'=>'BrandController@postEditSource']);
Route::get('delete_source/{id}', ['as'=>'delete.source', 'middleware'=>'auth', 'uses'=>'BrandController@DeleteSource']);
Route::post('add_source', ['as'=>'add.source', 'middleware'=>'auth', 'uses'=>'BrandController@postAddSource']);
Route::get('all_block', ['as'=>'all.block', 'middleware'=>'auth', 'uses'=>'BrandController@allBlock']);
Route::get('add_block', ['as'=>'add.block', 'middleware'=>'auth', 'uses'=>'BrandController@addBlock']);
Route::post('add_block', ['as'=>'add.block', 'middleware'=>'auth', 'uses'=>'BrandController@postAddBlock']);
Route::get('edit_block/{id}', ['as'=>'edit.block', 'middleware'=>'auth', 'uses'=>'BrandController@editBlock']);
Route::post('edit_block/{id}', ['as'=>'edit.block', 'middleware'=>'auth', 'uses'=>'BrandController@postEditBlock']);
Route::get('delete_block/{id}', ['as'=>'delete.block', 'middleware'=>'auth', 'uses'=>'BrandController@deleteBlock']);
});
Route::group(['as'=>'advert_setting.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('advert_category', ['as'=>'add_category', 'uses'=>'AdvertSettingsController@createCategory']);

Route::get('advert_categories/{id}', ['as'=>'advert_category', 'uses'=>'AdvertSettingsController@category']);
Route::post('advert_categories/{id?}', ['as'=>'advert_category', 'uses'=>'AdvertSettingsController@saveCategory']);
Route::post('advert_delete_category/{id?}', ['as'=>'delete_category', 'uses'=>'AdvertSettingsController@deleteCategory']);
Route::get('advert_categories', ['as'=>'advert_categories', 'uses'=>'AdvertSettingsController@categories']);
}); 
   
Route::group(['as'=>'pads.','middleware' => ['role:admin|manager|super_manager']]
, function()
{
Route::get('all_pads',['as'=>'all','uses'=> 'PartnerPadController@allPads']);
Route::get('edit_pad/{id}', ['as'=>'edit','uses'=>'PartnerPadController@editPad']);
Route::post('save_pad/{id}', ['as'=>'save','uses'=>'PartnerPadController@savePad']);
});
//Ajax
Route::post('add_pads_js', ['as'=>'add.pads.js','middleware' => 'auth','uses' =>'PartnerPadController@addPadsJs']);
Route::post('edit_pad_js', ['as'=>'edit.pad.js','middleware' => 'auth','uses' =>'PartnerPadController@editPadJs']);

Route::post('add_pads', ['as'=>'add.pads','middleware' => 'auth','uses' =>'PartnerPadController@addPads']);
Route::post('edit_pad_affiliate', ['as'=>'edit.pad_affiliate','middleware' => 'auth','uses' =>'PartnerPadController@editPadAffiliate']);

Route::group(['as'=>'users_log.','middleware' => ['role:admin|super_manager']]
, function()
{
Route::get('auth_log', ['as'=>'auth_log', 'uses'=>'LogController@AuthLog']);
Route::get('auth_logs/{ids}', ['as'=>'auth_log_detail', 'uses'=>'LogController@AuthLogDetail']);
});

Route::group(['as'=>'managers.','middleware' => ['role:admin']]
, function()
{
Route::get('managers', ['as'=>'all', 'middleware' => 'auth', 'uses'=>'ManagerController@index']);
Route::post('set_commission_manager', ['as'=>'set_commission', 'uses'=>'ManagerController@setCommission']);
});
Route::group(['as'=>'managers.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('detal_commission/{id}', ['as'=>'history', 'middleware' => 'auth', 'uses'=>'ManagerController@history']);
Route::get('manager_clients/{id}', ['as'=>'clients', 'middleware' => 'auth', 'uses'=>'ManagerController@clients']);
Route::get('detal_payout/{id}', ['as'=>'history_payout', 'middleware' => 'auth', 'uses'=>'ManagerController@historyPayout']);
});
Route::group(['as'=>'s_link.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('s_link', ['as'=>'all', 'middleware'=>'auth', 'uses'=>'SponsoredLinksController@index']);
Route::post('s_link_add', ['as'=>'add', 'middleware'=>'auth', 'uses'=>'SponsoredLinksController@add']);
});

/* удалить уведомление */
Route::get('remove_norification/{id}', ['as'=>'remove_notification', 'uses'=>'HomeController@removeNotif']);
/* помощь */
Route::group(['as'=>'help.','middleware' => ['role:admin|manager|super_manager|affiliate|advertiser']]
, function()
{
Route::get('reference',['as'=>'reference','uses'=> 'HelpController@reference']);
Route::get('k_base',['as'=>'k_base','uses'=> 'HelpController@kBase']);
Route::get('instructions',['as'=>'instructions','uses'=> 'HelpController@instructions']);
});
Route::group(['as'=>'client_statistic.','middleware' => ['role:admin|manager|super_manager|affiliate']]
, function()
{

Route::get('statistic_video/{id}',['as'=>'video','uses'=> 'VideoStatisticController@statisticPid']);
});

Route::group(['as'=>'payments.','middleware' => ['role:admin']]
, function()
{
Route::post('payment_commission',['as'=>'set_commission','uses'=> 'PaymentController@setCommission']);
//Ajax
Route::post('payment_commission_js',['as'=>'set_commission_js','uses'=> 'PaymentController@setCommissionJs']);
});

Route::group(['as'=>'payments.','middleware' => ['role:admin|super_manager']]
, function()
{
Route::get('payouts',['as'=>'payouts','uses'=> 'PaymentController@allPayouts']);
Route::get('payouts_rep',['as'=>'payouts.report','uses'=> 'PaymentController@allPayoutsReport']);
Route::post('action_payouts',['as'=>'action_payouts','uses'=> 'PaymentController@actionPayouts']);
Route::post('wmr_xml',['as'=>'wmr_xml','uses'=> 'PaymentController@WebMoney']);
});

Route::post('add_payout', ['as'=>'user_payout', 'uses'=>'PaymentController@addPayout']);
//ajax
Route::post('add_payout_js', ['as'=>'user_payout_js', 'uses'=>'PaymentController@addPayoutJs']);
Route::post('add_payout_auto_js', ['as'=>'user_payout_auto_js', 'uses'=>'PaymentController@addPayoutAutoJs']);

Route::post('add_payout_auto', ['as'=>'user_payout_auto', 'uses'=>'PaymentController@addPayoutAuto']);
Route::post('action_payouts_user',['as'=>'action_payouts_user','uses'=> 'PaymentController@actionPayoutsUser']);

Route::get('profile', ['as'=>'profile.personal', 'middleware' => 'auth', 'uses'=> 'HomeController@profile']);
Route::post('profile', ['as'=>'profile.personal.save', 'middleware' => 'auth', 'uses'=> 'HomeController@profilePost']);
Route::post('setPayments', ['as'=>'profile.payments.save', 'middleware' => 'auth', 'uses'=> 'HomeController@SetPays']);
Route::get('news', ['as'=>'news.all', 'middleware' => 'auth', 'uses'=> 'NewsController@index']);
Route::get('news/{id}', ['as'=>'news.showOne', 'middleware' => 'auth', 'uses'=> 'NewsController@showOne']);
Route::get('read_all_news', ['as'=>'news.read_all','uses' => 'NewsController@readAll']);
//------------------------------------------------
//Тестовый редактор виджета-------------------------------------
Route::get('widget/editor/{id}', ['as'=>'widget.edit','middleware' => 'auth', 'uses' => 'WidgetController@edit']);
Route::get('widget/render', ['as'=>'widget.render','middleware' => 'auth', 'uses' => 'WidgetController@render']);
Route::get('widget/mobile_render', ['as'=>'widget.render','middleware' => 'auth', 'uses' => 'WidgetController@Mobilerender']);
Route::post('widget/create', ['as'=>'widget.create','middleware' => 'auth', 'uses' => 'WidgetController@createWidget']);
Route::get('widget/delete/{id}', ['as'=>'widget.delete','middleware' => 'auth', 'uses' => 'WidgetController@deleteWidget']);
//редактор тизерки
Route::get('widget_tizer/render', ['as'=>'widget.tizer.render','middleware' => 'auth', 'uses' => 'WidgetController@TizerRender']);
Route::post('widget_tizer/save/{id}', ['as'=>'widget.tizer.save','middleware' => 'auth', 'uses' => 'WidgetController@saveWidgetTizer']);
Route::get('widget_tizer/mobile_render', ['as'=>'widget.render','middleware' => 'auth', 'uses' => 'WidgetController@TizerMobilerender']);
//Ajax
Route::post('widget/create_js', ['as'=>'widget.create.js','middleware' => 'auth', 'uses' => 'WidgetController@createWidgetJs']);
Route::post('widget/delete_post/{id}', ['as'=>'widget.delete','middleware' => 'auth', 'uses' => 'WidgetController@deleteWidgetPost']);
Route::post('widget/save/{id}', ['as'=>'widget.save','middleware' => 'auth', 'uses' => 'WidgetController@saveWidget']);
Route::post('widget/video_save/{id}', ['as'=>'widget.video.save','middleware' => 'auth', 'uses' => 'WidgetController@saveVideoWidget']);


Route::post('widget_brand/save/{id}', ['as'=>'widget.brand.save','middleware' => 'auth', 'uses' => 'WidgetController@saveBrandWidget']);
Route::group(['as'=>'advertiser.','prefix' => 'adv_','middleware' => ['role:admin|super_manager|manager|advertiser']]
#Route::group(['as'=>'advertiser.','prefix' => 'adv_']
, function()
{
#Route::get('invoice/{user_id}/create/{mod?}', ['as'=>'invoice_create','uses' =>'AdvertiserController@invoice_create']);	
Route::get('download', ['as'=>'download','uses' =>'AdvertiserController@download']);	
Route::get('getpdf/{id}', ['as'=>'download','uses' =>'AdvertiserController@getpdf']);	

Route::get('invoice/{id}/view', ['as'=>'invoice_view','uses' =>'AdvertiserController@invoice_view']);	
Route::get('invoice/{id}/print', ['as'=>'invoice_print','uses' =>'AdvertiserController@invoice_print']);	
Route::get('invoice/create', ['as'=>'invoice_create','uses' =>'AdvertiserController@invoice_create']);	

#Route::get('invoice/create', ['as'=>'invoice_create','uses' =>'AdvertiserController@invoice_create']);	
Route::get('widget_statistic/{widget_id}', ['as'=>'widget_statistic','uses' =>'AdvertiserController@widget_statistic']);	
Route::get('site_statistic/{pad}', ['as'=>'site_statistic_pad','uses' =>'AdvertiserController@site_statistic_pad']);	
Route::get('testpage/api', ['as'=>'testpage','uses' =>'AdvertiserController@testpage_api']);	
Route::get('site_statistic', ['as'=>'site_statistic','uses' =>'AdvertiserController@site_statistic']);	
Route::get('yandex_statistic', ['as'=>'yandex_statistic','uses' =>'AdvertiserController@yandex_statistic']);	
Route::get('warnings_statistic', ['as'=>'warnings_statistic','uses' =>'AdvertiserController@warnings_statistic']);	


Route::get('history/{shop_id}/{mod?}', ['as'=>'balance_history','uses' =>'AdvertiserController@balance_history']);
Route::get('invoices', ['as'=>'invoices_history','uses' =>'AdvertiserController@invoices_history']);
Route::get('statistic/{shop_id}/{mod}/{vid}/detail', ['as'=>'statistic_detail','uses' =>'AdvertiserController@statistic_detail']);
Route::get('statistic/{shop_id}/{mod?}', ['as'=>'statistic','uses' =>'AdvertiserController@statistic']);


Route::get('admin', ['as'=>'add_admin','middleware' => ['role:admin|super_manager|manager'],'uses' =>'AdvertiserController@admin']);
Route::post('delete_sinonim', ['as'=>'delete_sinonim','uses' =>'SinonimController@deleteSinonim']);

Route::post('sinonim', ['as'=>'add_sinonim','uses' =>'SinonimController@addSinonim']);
Route::get('sinonim', ['as'=>'sinonim','uses' =>'SinonimController@index']);
Route::post('delete_adertise/{id}', ['as'=>'delete_advertise','uses' =>'AdvertiserController@company_delete']); 
Route::get('view_company{id}', ['as'=>'view_company','uses' =>'AdvertiserController@view_company']);
Route::get('create_company/{id_user?}', ['as'=>'create_company','uses' =>'AdvertiserController@add_company']);
Route::get('create_shop/{id_company}', ['as'=>'create_shop','uses' =>'AdvertiserController@add_shop']);
Route::post('save_company/{id?}', ['as'=>'save_company','uses' =>'AdvertiserController@save_company_post']);
Route::get('edit_company/{id}', ['as'=>'edit_company','uses' =>'AdvertiserController@edit_company']);

Route::get('admin/top_orrefs/{id}', ['as'=>'check_company','uses' =>'AdvertiserController@checkOffer']);   
Route::post('admin/top_orrefs/{id}', ['as'=>'check_company','uses' =>'AdvertiserController@saveOffer']);   

Route::post('vibor_company', ['as'=>'first_add_company', 'uses'=>'AdvertiserController@firstAddCompany']);

Route::post('first_step_payout', ['as'=>'first.step.payout', 'uses'=>'AdvertPayoutController@firstStepPayout']);
Route::get('entity_payout', ['as'=>'entity.payout.get', 'uses'=>'AdvertPayoutController@GetentityPayout']);

Route::get('my_entity_dogovor/{id}', ['as'=>'entity.dogovor', 'uses'=>'AdvertPayoutController@entityDogovor']);

Route::get('my_entity', ['as'=>'my.entity', 'uses'=>'AdvertPayoutController@MyEntity']);
Route::post('entity_payout', ['as'=>'entity.payout', 'uses'=>'AdvertPayoutController@entityPayout']);

Route::post('advert_payout', ['as'=>'payout', 'uses'=>'AdvertPayoutController@payout']);
Route::get('get_success', ['as'=>'wb.success.get', 'uses'=>'AdvertPayoutController@wbSuccessGet']);
Route::post('wb_success', ['as'=>'wb.payout', 'uses'=>'AdvertPayoutController@wbSuccess']);

Route::get('requisites/{id?}', ['as'=>'requisites','middleware' => 'auth', 'uses'=>'AdvertPayoutController@Requisites']);
Route::Post('save_requisites', ['as'=>'save.requisites','middleware' => 'auth', 'uses'=>'AdvertPayoutController@SaveRequisites']);

Route::post('company_status_post/{id}', ['as'=>'company_status.requisites','middleware' => 'auth', 'uses'=>'AdvertiserController@company_status']);

Route::get('create_company_teaser/{id_user?}', ['as'=>'create_company_teaser','uses' =>'AdvertiserController@add_company_teaser']);
Route::get('edit_company_teaser/{id}', ['as'=>'edit_company_teaser','uses' =>'AdvertiserController@edit_company_teaser']);
Route::post('save_company_teaser/{id?}', ['as'=>'save_company_teaser','uses' =>'AdvertiserController@add_company_teaser_save']);

Route::get('add_offers_company/{id}', ['as'=>'add_offer_company','uses' =>'AdvertiserController@add_offers']);
Route::post('save_offers_company/{id?}', ['as'=>'save_offer_company','uses' =>'AdvertiserController@save_offers']);
Route::get('all_offers_company/{id?}', ['as'=>'all_offer_company','uses' =>'AdvertiserController@all_offers']);
Route::get('delete_offer_company/{id?}', ['as'=>'delete_offer_company','uses' =>'AdvertiserController@delete_offers']);
Route::post('company_exceptions/{id}', ['as'=>'company.exceptions', 'uses' =>'AdvertiserController@company_exceptions_save']);
Route::get('company_exceptions/{id}', ['as'=>'company.exceptions', 'uses' =>'AdvertiserController@company_exceptions']);
});


Route::group(['as'=>'advert_setting.','prefix' => 'adv_','middleware' => ['role:admin|super_manager']]
, function()
{

Route::get('default_product_price', ['as'=>'default_product','uses'=>'AdvertiserController@defaultProduct']);
Route::post('save_default_product_price', ['as'=>'save_default_product','uses'=>'AdvertiserController@saveDefaultProduct']);
});

Route::group(['as'=>'info_admin.','middleware' => ['role:admin|super_manager|manager']]
, function()
{
Route::get('source_info_key', ['as'=>'source_info_key','uses'=>'HomeController@sourceInfoKey']);
Route::post('source_info', ['as'=>'source_info','uses'=>'HomeController@sourceInfo']);
Route::get('source_info', ['as'=>'source_info','uses'=>'HomeController@sourceInfoGet']);
});


Route::get('video_render', ['as'=>'video.render', 'uses'=>'VideoRenderController@index']);
//--------------------------------
//Route::get('/home', 'HomeController@index')->name('home');
//-----------------------------Роли
Route::get('/roles', 'RolesController@Roles');
//--------------------------------------------
Route::group(['as'=>'test.','middleware' => ['role:admin|manager|super_manager']]
, function()
{
Route::get('/test/email_news',['as'=>'news','uses'=> 'TestController@index']);
});


//Route::get('test_home/', ['middleware'=>'auth', 'uses'=>'TestHomeController@index']);
Route::group(['prefix' => 'admin/{id_user}/','as'=>'test_admin.'
,'middleware' => ['role:admin|manager|super_manager']]
, function()
{
Route::get('test_home_admin',['as'=>'home', 'middleware'=>'auth', 'uses'=> 'TestHomeController@index']);
});
Route::group(['as'=>'cabinet_blocks.']
, function()
{
Route::post('/home_profile', ['as'=>'profile', 'uses'=>'TestHomeController@Profile']);
Route::post('/home_balance', ['as'=>'balance', 'uses'=>'TestHomeController@Balance']);
Route::post('/home_notif', ['as'=>'notif', 'uses'=>'TestHomeController@Notif']);
Route::post('/home_remove_norification/{id}', ['as'=>'remove_notification', 'uses'=>'TestHomeController@removeNotif']);
Route::post('/home_news', ['as'=>'news', 'uses'=>'TestHomeController@News']);
Route::get('/home_graph_video', ['as'=>'graph_video', 'uses'=>'TestHomeController@graphVideo']);
Route::get('/home_graph_product', ['as'=>'graph_product', 'uses'=>'TestHomeController@graphProduct']);
Route::get('/home_graph_teaser', ['as'=>'graph_teaser', 'uses'=>'TestHomeController@graphTeaser']);
Route::get('/home_graph_client', ['as'=>'graph_client', 'uses'=>'TestHomeController@graphClient']);
Route::post('/home_pads', ['as'=>'pads', 'uses'=>'TestHomeController@Pads']);
Route::post('/home_widgets', ['as'=>'widgets', 'uses'=>'TestHomeController@Widgets']); 
Route::post('/home_contacts', ['as'=>'contacts', 'uses'=>'TestHomeController@Contacts']);
Route::post('/home_users', ['as'=>'all_users', 'uses'=>'TestHomeController@HomeUsers']);
Route::post('/home_all_users', ['as'=>'all_users', 'uses'=>'TestHomeController@HomeAllUsers']);
Route::post('/home_video_users', ['as'=>'all_users', 'uses'=>'TestHomeController@HomeVideoUsers']);
Route::post('/home_product_users', ['as'=>'all_users', 'uses'=>'TestHomeController@HomeProductUsers']);
Route::post('/home_teaser_users', ['as'=>'all_users', 'uses'=>'TestHomeController@HomeTeaserUsers']);
Route::post('/home_message', ['as'=>'message', 'uses'=>'TestHomeController@Message']);
Route::get('/secret_alex_page', ['as'=>'secret_alex', 'uses'=>'TestHomeController@AlexPage']);
});

Route::get('/privacy', ['as'=>'privacy', 'uses'=>'AllController@privacy']);
Route::get('/useragree', ['as'=>'useragree', 'uses'=>'AllController@useragree']);

Route::get('/test_obmenneg', ['uses'=>'TestController@obmenneg']);

Route::group(['as'=>'obmenneg.', 'middleware' => ['role:admin|manager|super_manager']]
, function()
{
Route::get('/index_obmenneg',['as'=>'first', 'middleware'=>'auth', 'uses'=> 'LbtcController@table']);
Route::get('/first_obmenneg',['as'=>'index', 'middleware'=>'auth', 'uses'=> 'LbtcController@table']);
Route::get('/table_obmenneg',['as'=>'table', 'middleware'=>'auth', 'uses'=> 'ObmennegController@table']);
Route::get('/obmenneg_add_valut',['as'=>'add.valut', 'middleware'=>'auth', 'uses'=> 'ObmennegController@add']);
Route::post('/obmenneg_add_valut_post',['as'=>'add.valut.post', 'middleware'=>'auth', 'uses'=> 'ObmennegController@add_valut_post']);
Route::get('/edit_account_balance',['as'=>'edit.account.balance', 'middleware'=>'auth', 'uses'=> 'ObmennegController@editAccountBalance']);
Route::get('/valut_edit_balance/{id}/{from}',['as'=>'edit.account.balance.id', 'middleware'=>'auth', 'uses'=> 'ObmennegController@editAccountBalanceOne']);
Route::post('/edit_stat_post',['as'=>'edit.stat.post', 'middleware'=>'auth', 'uses'=> 'ObmennegController@editPost']);
Route::post('/edit_position',['as'=>'edit.position', 'middleware'=>'auth', 'uses'=> 'ObmennegController@editPosition']);
Route::post('/edit_balance',['as'=>'edit.account.balance.post', 'middleware'=>'auth', 'uses'=> 'ObmennegController@editBalance']);
Route::post('/cache_cart',['as'=>'add.cache.post', 'middleware'=>'auth', 'uses'=> 'ObmennegController@addCache']);
Route::get('/cache_out_valut/{id}',['as'=>'cache.out.valut', 'middleware'=>'auth', 'uses'=> 'ObmennegController@CacheValut']);

Route::get('/balance_log/{id}',['as'=>'account.balance.log', 'middleware'=>'auth', 'uses'=> 'ObmennegController@BalanceLog']);

Route::get('/activate_bot', ['as'=>'activate.bot', 'middleware'=>'auth', 'uses'=>'ObmennegController@Bot']);
Route::post('/activate_bot_post', ['as'=>'activate.bot.post', 'middleware'=>'auth', 'uses'=>'ObmennegController@BotPost']);
});

Route::get('/teaser_inform', ['uses'=>'TestController@Teaser']);

Route::get('/teaser_offers', ['uses'=>'TeaserOffersController@index']);

Route::group(['as'=>'lbtc.', 'prefix'=>'lbtc', 'middleware' => 'obmenneg']
, function()
{
Route::get('/', ['as'=>'list', 'middleware'=>'auth', 'uses'=>'LocalBtcController@index']);
Route::get('/index', ['as'=>'list', 'middleware'=>'auth', 'uses'=>'LocalBtcController@index']);

Route::post('parse/{id}', ['as'=>'edit.parse', 'middleware'=>'auth', 'uses'=>'LbtcController@parse']);
Route::post('prosent/{id}', ['as'=>'edit.prosent', 'middleware'=>'auth', 'uses'=>'LbtcController@prosent']);
Route::post('sms', ['as'=>'sms', 'middleware'=>'auth', 'uses'=>'LbtcController@sms']);
Route::get('/table', ['as'=>'table', 'middleware'=>'auth', 'uses'=>'LbtcController@table']);
Route::get('/table/month/{id}', ['as'=>'table.month', 'middleware'=>'auth', 'uses'=>'LbtcController@month']);
Route::get('/table/day/{id}/{date}', ['as'=>'table.day', 'middleware'=>'auth', 'uses'=>'LbtcController@day']);
Route::post('/transaction_post', ['as'=>'transaction.post', 'middleware'=>'auth', 'uses'=>'LbtcController@editPost']);
Route::post('limite', ['as'=>'limite', 'middleware'=>'auth', 'uses'=>'LbtcController@limite']);
Route::get('/balance_log/{id}',['as'=>'balance.log', 'middleware'=>'auth', 'uses'=> 'LbtcController@BalanceLog']);


Route::get('/new_parse/{id}', ['as'=>'edit.new.parse', 'middleware'=>'auth', 'uses'=>'LbtcController@newParse']);

Route::post('parseV2/{id}', ['as'=>'edit.parse.v2', 'middleware'=>'auth', 'uses'=>'LocalBtcController@parse']);
Route::get('/qiwi_robot/', ['as'=>'qiwi.robot.list.v3', 'middleware'=>'auth', 'uses'=>'LocalBtcController@Qiwiv3']);
Route::get('/qiwi_robot/detail/{date}', ['as'=>'qiwi.robot.list.v3.detail', 'middleware'=>'auth', 'uses'=>'LocalBtcController@Qiwiv3Detail']);
Route::get('/yandex_robot/', ['as'=>'yandex.robot.list.v3', 'middleware'=>'auth', 'uses'=>'LocalBtcController@Yandexv3']);
Route::get('/yandex_robot/detail/{date}', ['as'=>'yandex.robot.list.v3.detail', 'middleware'=>'auth', 'uses'=>'LocalBtcController@Yandexv3Detail']);
Route::get('/qiwi_robots/disabled', ['as'=>'qiwi.robot.disabled', 'middleware'=>'auth', 'uses'=>'LocalBtcController@QiwiDisabled']);
Route::post('/qiwi_robots/disabled', ['as'=>'qiwi.robot.disabled', 'middleware'=>'auth', 'uses'=>'LocalBtcController@QiwiDisabledPost']);
Route::get('/qiwi_robots/disabled_info', ['as'=>'qiwi.robot.disabled.info', 'middleware'=>'auth', 'uses'=>'LocalBtcController@QiwiDisabledInfo']);
Route::get('/yandex_robots/disabled', ['as'=>'yandex.robot.disabled', 'middleware'=>'auth', 'uses'=>'LocalBtcController@YandexDisabled']);
Route::post('/yandex_robots/disabled', ['as'=>'yandex.robot.disabled', 'middleware'=>'auth', 'uses'=>'LocalBtcController@YandexDisabledPost']);
Route::get('/yandex_robots/disabled_info', ['as'=>'yandex.robot.disabled.info', 'middleware'=>'auth', 'uses'=>'LocalBtcController@YandexDisabledInfo']);
Route::get('/birges', ['as'=>'birges', 'middleware'=>'auth', 'uses'=>'LocalBtcController@birges']);
Route::get('/crypto', ['as'=>'crypto', 'middleware'=>'auth', 'uses'=>'LocalBtcController@crypto']);

Route::get('/graph', ['as'=>'graph', 'middleware'=>'auth', 'uses'=>'LocalBtcController@Graph']);
});
Route::post('serverpage_setting_page', ['as'=>'serverpage_setting_page', 'middleware'=>'auth', 'uses'=>'Pages\Api@setSavePage']);
Route::any('serverpage_setting_maska', ['as'=>'serverpage_setting_maska', 'middleware'=>'auth', 'uses'=>'Pages\Api@setStatusMaska']);
Route::post('serverpage_setting_plge_flag', ['as'=>'serverpage_setting_plge_flag', 'middleware'=>'auth', 'uses'=>'Pages\Api@setStatusPage']);


Route::get('/video_test_v1', ['middleware'=>['role:admin|super_manager|manager'], 'uses'=>'TestController@videotest']);

Route::get('/api_test', ['uses'=>'TestController@apiTest']);

Route::group(['as'=>'money.', 'prefix'=>'money_report', 'middleware' => 'obmenneg']
, function()
{
	Route::get('/', ['as'=>'index', 'middleware'=>'auth', 'uses'=>'MoneyReportController@index']);
	Route::post('/add_valute', ['as'=>'add.valute', 'middleware'=>'auth', 'uses'=>'MoneyReportController@addValute']);
	Route::post('/add_account', ['as'=>'add.account', 'middleware'=>'auth', 'uses'=>'MoneyReportController@addAccounte']);
	Route::post('/add_operation', ['as'=>'add.operation', 'middleware'=>'auth', 'uses'=>'MoneyReportController@addOperation']);
	Route::get('/operation/{report_id}/{account_id}', ['as'=>'operation.report.account', 'middleware'=>'auth', 'uses'=>'MoneyReportController@ReportAccount']);
	Route::get('/reports', ['as'=>'reports', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reports']);
	Route::get('/report_operation/{id}', ['as'=>'report.operation', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reportOpertaion']);
	Route::get('/report_operation_accounts/{id}', ['as'=>'report.operation.accounts', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reportOpertaionAccounts']);
	Route::get('/report_closed/{id}', ['as'=>'report.closed', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reportClosed']);
	Route::post('/edit_report/', ['as'=>'edit.report', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reportEdit']);
	Route::get('/time_report/', ['as'=>'report.time', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reportTime']);
	Route::get('/time_report_detail/', ['as'=>'report.time.detal', 'middleware'=>'auth', 'uses'=>'MoneyReportController@reportTimeDetail']);
	Route::get('/table/month/{id}', ['as'=>'report.operation.month', 'middleware'=>'auth', 'uses'=>'MoneyReportController@month']);
});