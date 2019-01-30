<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \App\User;
use Auth;
use App\UserProfile;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Session;
use Route;
use Storage;
use PDF;
use MyCalendar;
class AdvertiserController extends Controller
{
	
	public function admin($id_user=0){
	//var_dump("да здравствует валерий сопин");
		\Auth::user()->touch();
		$user=Auth::user();
	    if ($id_user){
		
		$user=User::findOrFail($id_user);
		
		
		}
		if (\Auth::user()->hasRole('advertiser')){
			if ($user->id!=\Auth::user()->id){
				return abort(404);
			}
		}
		$statuses=\DB::connection("advertise")->table("advertise_statuses")->orderBy("id")->get();
		
		
		$userProf=UserProfile::where('user_id', $user->id)->first();
		
		return view('advertiser.cabinet.admin', ['user'=>$user,"admin"=>1,"statuses"=>$statuses]);
	}
	public function add_company($id_user=0){
		\Auth::user()->touch();
		$user=Auth::user();
		
		if ($id_user){
		
		$user=User::findOrFail($id_user);
		
		
		}
		
		$userProf=UserProfile::where('user_id', $user->id)->first();
		
		return view('advertiser.cabinet.add_company', ['userProf'=>$userProf,"noprice"=>1,'id_user'=>$id_user]);
	}
	public function admin_edit_company($id_user,$id){
				$company = \App\Advertise::find($id);
		$statuses=\DB::connection("advertise")->table("advertise_statuses")->orderBy("id")->get();
		$formats=\DB::connection("advertise")->table("pricelist_types")->orderBy("id")->get();
		//var_dump($formats);
		$userProf=UserProfile::where('user_id', $company->user_id)->first();
        $stat=[];
				$user_st=\DB::connection("advertise")->table("advertise_user_status")->select('status')->where('id_company', $id)->get();
		$stat=array();
		foreach ($user_st as $key=>$st){
			foreach ($st as $k=>$s){
				array_push($stat, $s);
			}
		}


		return view('advertiser.cabinet.edit_company'
		, ['userProf'=>$userProf,"model"=>$company,"statuses"=>$statuses,"formats"=>$formats,"noprice"=>1,"stat"=>$stat,'id_user'=>$id_user]);
	}
	
	public function firstAddCompany(Request $request){
		if ($request->input('type_company')==1){
			return redirect()->route('advertiser.create_company', ['id_user'=>$request->input('id_user')]);
		}
		elseif ($request->input('type_company')==2){
			return redirect()->route('advertiser.create_company_teaser', ['id_user'=>$request->input('id_user')]);
		}
		else{
			return abort(404);
		}
	}
	
	public function company_exceptions_save($id,Request $request){
		#var_dump($request->toArray()); die();
		$showcheck=$request->input('showcheck');
		$excepts=$request->input('excepts');
		if($showcheck){
			\DB::connection("pgstatistic")->table("advertise_pad_except")->where("ads_id","=",$id)
			->whereIn("pad",array_keys($showcheck))->delete();
		}
		if($excepts){
			$res=[];
			foreach($excepts as $key=>$tri){
			    $res[]=['ads_id'=>$id,'pad'=>$key];
				
			}
			
		}
		\DB::connection("pgstatistic")->table("advertise_pad_except")->insert(
		$res
		);
		return back()->with('message_success', 'Данные сохранены');
	}
	public function company_exceptions($id,Request $request){
		
		
		$company=\App\Advertise::findOrFail($id);
		if (\Auth::user()->hasRole('advertiser')){
			if ($company->user_id!=\Auth::user()->id){
				return abort(403);
			}
		}
		$title='исключения';
		$owner= \App\User::findOrFail($company->user_id);
		$title='Исключения площадок для магазина <b>'.$company->name.'</b> , <b>'.$owner->name.'</b> ';
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		
		return view('advertiser.cabinet.exceptions', ['company'=>$company
		,'from'=>$from,'to'=>$to,'title'=>$title
		]);
	}
	
	public function edit_company($id){
//		var_dump($id_user);
		$company = \App\Advertise::find($id);
		$statuses=\DB::connection("advertise")->table("advertise_statuses")->orderBy("id")->get();
		$formats=\DB::connection("advertise")->table("pricelist_types")->orderBy("id")->get();
		//var_dump($formats);
		$userProf=UserProfile::where('user_id', $company->user_id)->first();
		$user_st=\DB::connection("advertise")->table("advertise_user_status")->select('status')->where('id_company', $company->id)->get();
		$stat=array();
		foreach ($user_st as $key=>$st){
			foreach ($st as $k=>$s){
				array_push($stat, $s);
			}
		}
		 
		
		
		return view('advertiser.cabinet.edit_company'
		, ['userProf'=>$userProf,"model"=>$company,"statuses"=>$statuses,"formats"=>$formats,"noprice"=>0, 'stat'=>$stat]);

		}	
	public function view_company($id){
		$company = \App\Advertise::find($id);
		$userProf=UserProfile::where('user_id', $company->user_id)->first();

		return view('advertiser.cabinet.view_company', ['userProf'=>$userProf,"model"=>$company]);

		}			
	public function get_pps_post($pps){
		if(!$pps) return [];
		$res=[];
		foreach($pps as $id_geo =>$cates){
			foreach($cates as $id_cat =>$prc){
				$price = floatval(preg_replace('/\,/','.',preg_replace('/\s+/','',$prc)));
				if($price){
					$res[]=["id_geo"=>$id_geo,"id_cat"=>$id_cat,"price"=>$price];
				}
			}
		}
	
		return $res;
	}		

	public function save_company_post($id=null,Request $request){
        
		$id_user=0;
		$prev=url()->previous();
		if(preg_match('/\/admin\/([\d]+)/',$prev,$m)){
			$id_user=$m[1];
		
		//var_dump($prev); die();
		}
		#var_dump($id_user); die();
		\Auth::user()->touch();
		$pricegame = $request->input('pricegame');
		$pps=$this->get_pps_post($request->input('pps'));
		
		$yandexshir=$request->input('yandexshir');
		$shedule=$request->input('shedule');
		
		#die();
		
		if(!$yandexshir) $yandexshir=[];
        $use_accessories=$request->input('use_accessories');
		$user_id=$request->input('user_id');
		$vkl=$request->input('vkl');
		$user_st=$request->input('user_st');
		$persent = $request->input('persent');
		$limit_clicks = $request->input('limit_clicks');
		$name=$request->input('name');
		$description=$request->input('description');
		$url=$request->input('url');
		$url_host=$request->input('url_host');
		$status=$request->input('status',0);
		#$type=$request->input('type');
		#var_dump($vkl);
		$type_price=$request->input('type_price',0);		
		if($pricegame){
		#if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')){
			
			$status=$request->input('status',0);
		}

		//$u_host= \App\MPW\Sources\Urlparser::getLtd($url_host);
		$u_host= \App\MPW\Sources\Urlparser::getFullUrl($url_host);
		$fileq = $request->file('file_source');
		
		$validator=Validator::make(
			array(
				'name' => $name,
				'url_host' => $url_host,
				'description'=>$description,
				'url'=>$url,
				//'file_source'=>$file
				
			),
			array(
				'name' => 'required|string|max:255',
				//'url_host' => 'required',
				'description' => 'required',
				//'url' => 'required',
				//'file_source'=>'mimes:csv'
			));
			
            
			
			if(!$u_host){
				
				
			return back()->withErrors(['url_host' => array(
        'required' => 'неверное название сайта',
         )])->withInput();
		    }else{
				#print $u_host; die();
			}
		
			if ($validator->fails()){
				#var_dump($validator); die();
			return back()->withErrors($validator)->withInput();
			#print "<pre>"; print_r($validator->errors()->all()); print "</pre>"; die();
			 
			}
			//var_dump($request->toArray()); die(); 
				
			if($id){
				$company=\App\Advertise::findOrFail($id);
			     //var_dump($request->toArray()); die(); 
				 $old_status=$company->status;

				 
			}else
		$company = new \App\Advertise;
	
		$company->user_id=$user_id;
		$company->name=$name;
		$limit_clicks=intval($limit_clicks);
		if(!$limit_clicks){
			
			$company->limit_clicks=null;
			
		}else{
			if($id){
			#$valueL= Redis::command('del', ['limitclick_'.$id]);
			#$valueL= Redis::command('get', ['limitclick_'.$id]);
			}
			#var_dump($valueL); die();
			$company->limit_clicks=$limit_clicks;
		}
		$company->description=$description;
		$company->persent=$persent; 
		$company->url=$url;
		$company->url_host=$url_host;
		
		$company->type=1;
		
		if($pricegame){
		#if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')){
			$company->type_price=$type_price;
			$company->status=$status;
			if($company->id && isset($old_status) && $old_status!=$status)
			{
				$sql="update advertise_before_search 
		set status=".$status.",datetime=NOW()
		WHERE advertise_id=".$company->id."
		";
		\DB::connection("advertise")->statement($sql);
		$sql="insert into advertise_before_search (advertise_id,status) 
		select ".$company->id.",".$status."
		WHERE NOT EXISTS (SELECT 1 FROM advertise_before_search  WHERE advertise_id=".$company->id.")
		";
		\DB::connection("advertise")->statement($sql);
			}
			
			
			
		}
		if($use_accessories)
		    $company->use_accessories=1;
	    else
			$company->use_accessories=0;
		//$user_st
		$permision=7;
		$permision=0;

		if ($user_st){
			foreach ($user_st as $st){
				switch($st){
				case 1:
				$permision|=1;
                break;				
				case 2:
				$permision|=2;
                break;								
				case 3:
				$permision|=4;
                break;								
				}
			}
			$company->site_permissions=$permision;
		}
		if($id_user){
		$company->status=$status;
		$company->type_price =	$type_price;
		}
		$company->save();
		
		
		MyCalendar::weekSchedule($company->id,$shedule);
		MyCalendar::saveNave();
		//var_dump($yandexshir); die();
		\DB::connection('advertise')->table('advertises_yandex')->where('advertise_id', $company->id)->delete();
        if($yandexshir){
			foreach($yandexshir as $key=>$val){
					\DB::connection('advertise')->table('advertises_yandex')->insert(["advertise_id"=>$company->id, 'yandex_id'=>$key]);
			}

		}
	
		if ($user_st){
			
			
			
			\DB::connection('advertise')->table('advertise_user_status')->where('id_company', $company->id)->delete();
				foreach ($user_st as $st){
					
					\DB::connection('advertise')->table('advertise_user_status')->insert(["id_company"=>$company->id, 'status'=>$st]);
				}
		}
		else{
			\DB::connection('advertise')->table('advertise_user_status')->where('id_company', $company->id)->delete();
			//\DB::connection('advertise')->table('advertise_user_status')->insert(["id_company"=>$company->id, 'status'=>0]);
		}
		
		if($pricegame){

		\DB::connection("advertise")->table("myads_rubriks_iso2_tree")->where("id_company",$company->id)->delete();
					if($vkl){
						
		foreach ($vkl as $key=>$vk){
		foreach($pps as $ps){
			if ($key==$ps['id_geo']){
				 \DB::connection("advertise")->table("myads_rubriks_iso2_tree")->insert([
				  "id_company"=>$company->id
				  ,"id_rubrik"=>$ps["id_cat"]
				  ,"id_iso2"=>$ps["id_geo"]
				  ,"price"=>$ps["price"]
							 ]);
					}
			}
		}
		}else{
		}
		}else{
			\DB::connection("advertise")->table("advertise_iso2")->where("shop_id",$company->id)->delete();
		   if($vkl){
			 
			   foreach ($vkl as $key=>$vk){
				   \DB::connection("advertise")->table("advertise_iso2")->insert([
				  "shop_id"=>$company->id
				  ,"geo_id"=>$key
							 ]);
			   }
			
		   }else{
				   \DB::connection("advertise")->table("advertise_iso2")->insert([
				   "shop_id"=>$company->id
				  ,"geo_id"=>0
							 ]);
		   }	   
			
		}
		if($fileq){
			    $oldfile=$fileq->getPathName();
			//var_dump($_FILES);
			//var_dump($oldfile); die();
				$newfile='/home/www/storage.market-place.su/pricelist/'.$company->id.'.'.$fileq->getClientOriginalExtension();
				$newurl='https://storage.market-place.su/pricelist/'.$company->id.'.'.$fileq->getClientOriginalExtension();
				//$file->move($newfile,$file->getClientOriginalName());
				//var_dump([$oldfile,$newfile,move_uploaded_file($oldfile,$newfile)]); die();
				if(move_uploaded_file($oldfile,$newfile)){
				$company->url=$newurl;
				$company->save();
				}
			}
		if($id_user){
			return redirect()->route('admin.edit_company', ['id_user'=>$id_user,'id' => $company->id])->with('message_success', "Данные сохранились.");;
		}else{
		return redirect()->route('advertiser.edit_company', ['id' => $company->id])->with('message_success', "Данные сохранились.");;
		}
		#return redirect()->route('advertiser.edit_company', ['id' => $company->id]);
		 #
		#return back();
	}
	
	public function add_shop($id_company,$id_user=0){
		
		\Auth::user()->touch();
		$user=Auth::user();
		
		if ($id_user){
		
		$user=User::findOrFail($id_user);
		
		
		}
		$company = \App\Advertise::find($id_company);
		var_dump($company);
		#$userProf=UserProfile::where('user_id', $user->id)->first();
		
		#return view('advertiser.cabinet.add_company', ['userProf'=>$userProf]);
	}	
	

   public function saveOffer($id,Request $request){
	   $offer=\DB::connection("cluck")->table("offers")->where("id",$id)->first();
	   if(!$offer) abort(403);
	   		$yandex_categories=$request->input('cattuya');
			if(is_array($yandex_categories)){
				$smpd=[];

			foreach($yandex_categories as $yk){
				foreach($yk as $key=>$value){
      \DB::connection("cluck")->table("shop_categories_names")->where('id', $offer->shop_category)
      ->update([
	  "id_tree"=>$value
	 ,"edited"=>date("Y-m-d H:i:s")
				 ]);
					print $offer->shop_category."_".$value."<hr>";  
                    break 2;
				}
			}
			#if($smpd){
				#$wid->widget_categories=implode(",",array_keys($smpd)); 
			#}
			# print "<pre>"; print_r($yandex_categories); print "</pre>"; die();
		    }
	   #var_dump($request->toArray());
	  return back();   
	 }
   public function checkOffer($id){
	   
	   $offer=\DB::connection("cluck")->table("offers")->where("id",$id)->first();
	   if(!$offer) abort(403);
	    
	    #print "<pre>"; print_r(["товар",$offer]); print "</pre>";
	    $shop_category=\DB::connection("cluck")->table("shop_categories_names")->where("id",$offer->shop_category)->first();
		
		$yandex_category=\DB::connection("cluck")->table("yandex_categories")->where("id",$shop_category->id_tree)->first();
        #print "<pre>"; print_r(["категория магазина",$yandex_category]); print "</pre>"; die();
		$shop_=\DB::connection("cluck")->table("ads_yml")->where("id",$shop_category->shop_id)->first();
        #print "<pre>"; print_r(["магазинчик",$shop_]); print "</pre>"; 
		if($shop_->id_marka){
		$shops_=\DB::connection("cluck")->table("ads_yml")->where("id_marka",$shop_->id_marka)->get();
        #print "<pre>"; print_r(["все магазины этой марки",$shops_]); print "</pre>";
		}
		
		$offers=\DB::connection("cluck")->table("offers")->where("shop_category",$offer->shop_category)->where("status",1)->paginate(20);
        #print "<pre>"; print_r(["все товары этой категории",$offers]); print "</pre>";
		#$offers=[];
		
		#http://pga.apptoday.ru/
		#ads_yml
		$params=[
		"offer"=>$offer,
		"shop"=>$shop_,
		"shop_category"=>$shop_category,
		"offers"=>$offers,
		"yandex_category"=>$yandex_category
		
		];
		#print "<pre>"; print_r($params); print "</pre>";
		#die();
	   return view('advertiser.cabinet.check_offer',$params);
	}
	
	public function defaultProduct(){
		\Auth::user()->touch();
		$id=0;
		return view('advertiser.default.product_price', ['id'=>$id]);
	}
	
	public function saveDefaultProduct(Request $request){
		\Auth::user()->touch();
		$pps=$this->get_pps_post($request->input('pps'));
		$id=$request->input('id');
		\DB::connection("advertise")->table("myads_rubriks_iso2_tree")->where("id_company",$id)->delete();
		foreach($pps as $ps){
			\DB::connection("advertise")->table("myads_rubriks_iso2_tree")->insert([
				"id_company"=>$id
				,"id_rubrik"=>$ps["id_cat"]
				,"id_iso2"=>$ps["id_geo"]
				,"price"=>$ps["price"]
			]);
		}
		return back();
	}
	
	public function add_company_teaser($id_user=0){
		\Auth::user()->touch();
		$user=Auth::user();
		if ($id_user){
		$user=User::findOrFail($id_user);
		}
		if (\Auth::user()->hasRole('advertiser')){
			if ($user->id!=\Auth::user()->id){
				return abort(404);
			}
		}
		$categories=\DB::table('teaser_categories_new')->orderBy('name', 'asc')->get();
		$browsers=\DB::table('teaser_browser')->orderBy('name', 'asc')->get();
		return view('advertiser.teaser.add_company', ['user'=>$user, 'categories'=>$categories, 'browsers'=>$browsers]);
	}
	
	public function add_company_teaser_save($id=null, Request $request){
		$id_user=0;
		$prev=url()->previous();
		if(preg_match('/\/admin\/([\d]+)/',$prev,$m)){
			$id_user=$m[1];
		//var_dump($id_user); die();
		//var_dump($prev); die();
		}
		$summma=$request->input('summa');
		//var_dump($summma); die();
		$shedule=$request->input('shedule');
		$persent = $request->input('persent');
		//var_dump($persent); die();
		$user_id=$request->input('user_id');
		$name=$request->input('name');
		$categories=$request->input('categories');
		$browser=$request->input('browser');
		$sex=$request->input('sex');
		$kompany_limit_set=$request->input('kompany_limit_set');
		$kompany_limit_value=$request->input('kompany_limit_value')?$request->input('kompany_limit_value'):0;
		$offer_limit_click=$request->input('offer_limit_click')?$request->input('offer_limit_click'):0;
		$offer_limit_time=$request->input('offer_limit_time');
		$vkl=$request->input('vkl');
		$shedule_status=$request->input('shedule_status')?$request->input('shedule_status'):0;
		$status=$request->input('status',0);
		$user_st=$request->input('user_st');
		if ($id){
			$company=\App\Advertise::findOrFail($id);
		}
		else{
			$company= new \App\Advertise;
		}
		
		$company->user_id=$user_id;
		$company->name=$name;
		$company->type=3;
		if (\Auth::user()->hasRole(['admin','super_manager','manager']) or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')){
			$company->status=$status;
		}
		else{
			$company->status=0;
		}
		if($summma)
		$company->myteaser_perclick=$summma;
		$company->gender=$sex;
		$company->kompany_limit_set=$kompany_limit_set;
		$company->kompany_limit_value=$kompany_limit_value;
		$company->offer_limit_click=$offer_limit_click;
		$company->offer_limit_time=$offer_limit_time;
		$company->persent=$persent; 
		$permision=7;
		$permision=0;
		if ($user_st){
			
			foreach ($user_st as $st){
				switch($st){
				case 1:
				$permision|=1;
                break;				
				case 2:
				$permision|=2;
                break;								
				case 3:
				$permision|=4;
                break;								
				}
			}
			$company->site_permissions=$permision;
		}
		$company->save();
		MyCalendar::weekSchedule($company->id,$shedule);
		MyCalendar::saveNave();
		if ($user_st){
			\DB::connection('advertise')->table('advertise_user_status')->where('id_company', $company->id)->delete();
				foreach ($user_st as $st){
					\DB::connection('advertise')->table('advertise_user_status')->insert(["id_company"=>$company->id, 'status'=>$st]);
				}
		}
		else{
			\DB::connection('advertise')->table('advertise_user_status')->where('id_company', $company->id)->delete();
			//\DB::connection('advertise')->table('advertise_user_status')->insert(["id_company"=>$company->id, 'status'=>0]);
		}
		\DB::connection('advertise')->table('advertises_teaser_categories')->where('id_company', $company->id)->delete();
		if (!$categories){
			\DB::connection('advertise')->table('advertises_teaser_categories')->insert(["id_company"=>$company->id, 'id_category'=>0]);
		}
		else{
			foreach ($categories as $category){
				\DB::connection('advertise')->table('advertises_teaser_categories')->insert(["id_company"=>$company->id, 'id_category'=>$category]);
			}
		}
		
		\DB::connection('advertise')->table('advertises_teaser_browser')->where('id_company', $company->id)->delete();
		if (!$browser){
			\DB::connection('advertise')->table('advertises_teaser_browser')->insert(["id_company"=>$company->id, 'id_browser'=>0]);
		}
		else{
			foreach ($browser as $br){
				\DB::connection('advertise')->table('advertises_teaser_browser')->insert(["id_company"=>$company->id, 'id_browser'=>$br]);
			}
		}
		\DB::connection('advertise')->table('advertises_teaser_geo')->where('id_company', $company->id)->delete();
		if (!$vkl){
			\DB::connection('advertise')->table('advertises_teaser_geo')->insert(["id_company"=>$company->id, 'id_geo'=>0]);
		}
		else{
			foreach ($vkl as $key=>$vk){
				\DB::connection('advertise')->table('advertises_teaser_geo')->insert(["id_company"=>$company->id, 'id_geo'=>$key]);
			}
		}

//var_dump($user_st); die();


		
		
		
		if($id_user){
			return redirect()->route('admin.edit_company_teaser', ['id_user'=>$id_user,'id' => $company->id])->with('message_success', "Данные сохранились.");;
		}else{
		return redirect()->route('advertiser.edit_company_teaser', ['id' => $company->id])->with('message_success', "Данные сохранились.");;
		}
		# route('advertiser.edit_company_teaser')
		#$company->status
		#return back();
	}
	public function admin_edit_company_teaser($id_user,$id){
		$company = \App\Advertise::find($id);
		$user=\App\User::findOrFail($company->user_id);
		$categories=\DB::table('teaser_categories_new')->orderBy('name', 'asc')->get();
		$companyCategories=\DB::connection("advertise")->table('advertises_teaser_categories')->select('id_category')->where('id_company', $company->id)->get();
		$cats=array();
		foreach ($companyCategories as $key=>$categor){
			foreach ($categor as $k=>$s){
				array_push($cats, $s);
			}
		}
		$browsers=\DB::table('teaser_browser')->orderBy('name', 'asc')->get();
		$companyBrowsers=\DB::connection("advertise")->table('advertises_teaser_browser')->select('id_browser')->where('id_company', $company->id)->get();
		$brows=array();
		foreach ($companyBrowsers as $key=>$brow){
			foreach ($brow as $k=>$s){
				array_push($brows, $s);
			}
		}
		$user_st=\DB::connection("advertise")->table("advertise_user_status")->select('status')->where('id_company', $company->id)->get();
		$stat=array();
		foreach ($user_st as $key=>$st){
			foreach ($st as $k=>$s){
				array_push($stat, $s);
			}
		}
		$statuses=\DB::connection("advertise")->table("advertise_statuses")->orderBy("id")->get();
		return view('advertiser.teaser.edit_company', ["model"=>$company, 'user'=>$user, 'categories'=>$categories, 'cats'=>$cats, 'browsers'=>$browsers, 
		'brows'=>$brows, 'stat'=>$stat, 'statuses'=>$statuses,"id_user"=>$id_user]);
		
	}
	public function edit_company_teaser($id){
		
		$company = \App\Advertise::find($id);
		$user=\App\User::findOrFail($company->user_id);
		$categories=\DB::table('teaser_categories_new')->orderBy('name', 'asc')->get();
		$companyCategories=\DB::connection("advertise")->table('advertises_teaser_categories')->select('id_category')->where('id_company', $company->id)->get();
		$cats=array();
		foreach ($companyCategories as $key=>$categor){
			foreach ($categor as $k=>$s){
				array_push($cats, $s);
			}
		}
		$browsers=\DB::table('teaser_browser')->orderBy('name', 'asc')->get();
		$companyBrowsers=\DB::connection("advertise")->table('advertises_teaser_browser')->select('id_browser')->where('id_company', $company->id)->get();
		$brows=array();
		foreach ($companyBrowsers as $key=>$brow){
			foreach ($brow as $k=>$s){
				array_push($brows, $s);
			}
		}
		$user_st=\DB::connection("advertise")->table("advertise_user_status")->select('status')->where('id_company', $company->id)->get();
		$stat=array();
		foreach ($user_st as $key=>$st){
			foreach ($st as $k=>$s){
				array_push($stat, $s);
			}
		}
		$statuses=\DB::connection("advertise")->table("advertise_statuses")->orderBy("id")->get();
		return view('advertiser.teaser.edit_company', ["model"=>$company, 'user'=>$user, 'categories'=>$categories, 'cats'=>$cats, 'browsers'=>$browsers, 
		'brows'=>$brows, 'stat'=>$stat, 'statuses'=>$statuses]);
	}
	
	public function add_offers($id){
		$company=\App\Advertise::findOrFail($id);
		return view('advertiser.teaser.add_offers', ['company'=>$company]);
	}
	
	public function save_offers($id=null, Request $request){
		$id_company=$request->input('id_company');
		$name=$request->input('name');
		$sub_name=$request->input('sub_name');
		$url=$request->input('url');
		$validator=Validator::make(
			array(
				'name' => $name,
				'sub_name' => $sub_name
			),
			array(
				'name' => 'required|string|max:255',
				'sub_name' => 'required|string|max:255'
			));
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}
		$offer=\DB::connection('advertise')->table('teaser_offers')->insertGetId(["id_company"=>$id_company, 'url'=>$url, 'name'=>$name, 'sub_name'=>$sub_name]);
		$img = $request->file('img');
		if (!$img){
			\DB::connection('advertise')->table('teaser_offers')->where("id", $offer)->delete();
			return back();
		}
		if ($img) {
				$avatarFormat=$img->getClientOriginalExtension();
						$validator=Validator::make(
						array(
							'img' => $img
						),
						array(
							'img' => 'image|max:512|mimes:jpeg,png,gif',
						)
					);
					if ($validator->fails()){
						return back()->withErrors($validator)->withInput();
					}
            $filename = $offer . '.' . $img->getClientOriginalExtension();
			$filedirect='/home/www/storage.market-place.su/teaser_img/'.$id_company;
			if (!is_dir($filedirect)){
				mkdir($filedirect, 0777, true);
			}
            Image::make($img)->resize(200, 200)->save('/home/www/storage.market-place.su/teaser_img/'.$id_company.'/'.$filename);
			$form=explode(".", $filename);
			$format=array_pop($form);
			\DB::connection('advertise')->table('teaser_offers')->where('id', $offer)->update(['format' => $format]);
        }
		return back()->with('message_success', "Предложение добавлено.");
	}
	public function all_offers($id){
		$company=\App\Advertise::findOrFail($id);
		$offers=\DB::connection('advertise')->table('teaser_offers')->where('id_company', $id)->get();
		//print_r($offers); die();
		return view('advertiser.teaser.all_offers', ['company'=>$company, 'offers'=>$offers]);
	}
	
	public function delete_offers($id){
		$offer=\DB::connection('advertise')->table('teaser_offers')->where("id", $id)->first();
		\DB::connection('advertise')->table('teaser_offers')->where("id", $id)->delete();
		unlink('/home/www/storage.market-place.su/teaser_img/'.$offer->id_company.'/'.$offer->id.'.'.$offer->format);
		return back();
	}
	public function admin_statistic_detail($id_user,$shop_id,Request $request,$mod,$vid){
		
		if(!$mod) abort(403);
		if(!$vid) abort(403);
		$preff='';
						switch($mod){
					case "domain":
					$pad=\App\PartnerPad::findOrFail($vid);
					
					$preff='детализация по домену <b>'.$pad->domain.'</b>';
					
					
					break;
					default:
					abort(404);
					break;
					
				}
		$title="Пока ничего не понятно 2";
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
		$title='Статистика рекламной компании <b>'.$company->name.'</b> , <b>'.$owner->name.'</b> '.$preff;
		}elseif($id_user){
			$owner= \App\User::findOrFail($id_user);
			$title='Статистика рекламных компаний от  <b>'.$owner->name.'</b> '.$preff;
		}else{
				$title='Статистика всех рекламных компаний '.$preff;
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		//var_dump(1); die();
		return view('advertiser.cabinet.statistic_detail', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to]);
	}
	public function admin_statistic($id_user,$shop_id,Request $request,$mod=null){
		
		if(!$mod) $mod="data";
		$title="Пока ничего не понятно 2";
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
		$title='Статистика рекламной компании <b>'.$company->name.'</b> , <b>'.$owner->name.'</b> ';
		}elseif($id_user){
			$owner= \App\User::findOrFail($id_user);
			$title='Статистика всех рекламных компаний от  <b>'.$owner->name.'</b> ';
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		return view('advertiser.cabinet.statistic', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to,'owner'=>$owner]);
	}
	public function statistic_detail($shop_id,Request $request,$mod,$vid){
				if(!$mod) abort(403);
				if(!$vid) abort(403);
				$preff="";
				$user=Auth::user();
				switch($mod){
					case "domain":
					$pad=\App\PartnerPad::findOrFail($vid);
					if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
					$preff='детализация по домену <b>'.$pad->domain.'</b>';
					}else{
						$preff='детализация по домену <b> № '.$pad->id.'</b>';
					}
					
					break;
					default:
					abort(404);
					break;
					
				}
				
				
		$title="Пока ничего не понятно 1";
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
		$title='Статистика рекламной компании <b>'.$company->name.'</b> , <b>'.$owner->name.'</b> '.$preff;
		}else{
			
			if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$title='Статистика рекламной компании '.$preff;
			}elseif($user->hasRole('advertiser')){
				$title='Статистика всех рекламных компаний  <b>'.$user->name.'</b> '.$preff;
			}else{
				
				abort(403);
			}
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		//var_dump(1); die();
		return view('advertiser.cabinet.statistic_detail', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to]);
	}
	public function statistic($shop_id,Request $request,$mod=null){ 
		if(!$mod) $mod="data";
		$title="Пока ничего не понятно 1";
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
		$title='Статистика рекламной компании <b>'.$company->name.'</b> , <b>'.$owner->name.'</b> ';
		}else{
			$user=Auth::user();
			if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$title='Статистика всех компаний';
			}elseif($user->hasRole('advertiser')){
				$title='Статистика всех рекламных компаний от  <b>'.$user->name.'</b> ';
			}else{
				
				abort(403);
			}
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		return view('advertiser.cabinet.statistic', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to]);
	}
	public function admin_balance_history($id_user,$shop_id,Request $request,$mod=null){
		
		if(!$mod) $mod="data";
		$title="Пока ничего не понятно 2";
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
		$title='Взаиморасчёты <b>'.$owner->name.'</b> ';
		}elseif($id_user){
			$owner= \App\User::findOrFail($id_user);
			$title='Взаиморасчёты   <b>'.$owner->name.'</b> ';
		}
		
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
	return view('advertiser.cabinet.balance_history', ['id_user'=>$owner->id,'title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to,"owner"=>$owner]);	
		
    }
	public function balance_history($shop_id,Request $request,$mod=null){
		if(!$mod) $mod="data";
		$title="Пока ничего не понятно 1";
		if($shop_id){
		$company = \App\Advertise::findOrFail($shop_id);
		$owner= \App\User::findOrFail($company->user_id);
		$title='Взаиморасчёты   <b>'.$owner->name.'</b> ';
		}
		else{
			$owner=$user=Auth::user(); 
			if($user->hasRole('admin') || $user->hasRole('super_manager') || $user->hasRole('manager')){
			$title='Статистика всех компаний';
			}elseif($user->hasRole('advertiser')){
				
				$title='Взаиморасчёты   <b>'.$user->name.'</b> ';
				}else{
				
				abort(403);
			}
		}
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		
	return view('advertiser.cabinet.balance_history', ['id_user'=>$owner->id,'title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to]);		
	
    }
	
	public function getpdf(Request $request){
		$conf["wparams"]=Route::current()->parameters;
				$conf["wparams"]=Route::current()->parameters;
		if(!isset($conf["wparams"]["id"])) abort(403);
		$invoice=\App\Payments\Invoice::findOrFail($conf["wparams"]["id"]);
		if($invoice->Requisite->type_payout==2){
		$fanke='/home/www/storage.market-place.su/invoice/ooo.txt';	
		}else{
		$fanke='/home/www/storage.market-place.su/invoice/ip.txt';	
		}
		
		$frt=file($fanke);
		$postavka=[];
		foreach($frt as $kl){
		$kl=trim($kl);
         if($kl){
			 $tmp=explode(":",$kl);
			 $postavka[$tmp[0]]=$tmp[1];
		 #print"<pre>"; var_dump($kl); print"</pre>";	 
		 }
		}
		
		#print"<pre>"; var_dump($invoice->Requisite->type_payout); print"</pre>";
		$conf["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^([admin]+\.)([a-z\-]+)/',$func,$m))
			$conf["pref"]=$m[1];
		$htmlPdf=Storage::exists('invoices_pdf/'.$conf["wparams"]["id"]);
		if($htmlPdf){
			$htmlPdf=Storage::get('invoices_pdf/'.$conf["wparams"]["id"]);
			
			//PDF::loadHTML($htmlPdf)->setWarnings(false)->stream('download.pdf')
			//return response()->download(PDF::loadHTML($htmlPdf)->setWarnings(false)->stream('download.pdf'));
			$pdf=PDF::loadHTML($htmlPdf)->setWarnings(false);//->stream('download.pdf');
			return $pdf->download('market_place_invoice_'.$conf["wparams"]["id"].'.pdf');
			#return PDF::loadHTML($htmlPdf)->setWarnings(false)->stream('download.pdf');
		}
		
		$html = view('advertiser.cabinet.invoice_pdf', ["config"=>$conf,"inv"=>$invoice,"postavka"=>$postavka]);
		return PDF::loadHTML($html)->setWarnings(false)->stream('download.pdf');
	}
	public function download(Request $request){
		$url=$request->input('url');
		if(!$url)  abort(404);
		$vurl=public_path().$url;
		if(!is_file($vurl)) abort(404);
		return response()->download(public_path($url));
		//var_dump($url);
	}
    public function disco(Request $request){
		$conf=[];
		$conf["wparams"]=Route::current()->parameters;
        
		$conf["pref"]="";
		$func=Route::currentRouteName();
		//var_dump($conf["wparams"]);
		$user=\App\User::findOrFail($conf["wparams"]["id_user"]);
		 return view('advertiser.cabinet.disco', ["user"=>$user]);		
	}
    public function invoices_history(Request $request){ 
	
	    $conf=[];
		$conf["wparams"]=Route::current()->parameters;
        
		$conf["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^([a-z]+\.)([a-z\-]+)/',$func,$m))
			$conf["pref"]=$m[1];
         if(isset($conf["wparams"]["id_user"])){
         $user=\App\User::findOrFail($conf["wparams"]["id_user"]);
         }else{
         $user=Auth::user();
         }
		 $from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(3600*24*7)));
		}
		 
		 return view('advertiser.cabinet.invoices_history', ["from"=>$from, "to"=>$to,"user"=>$user]);		
	#var_dump($user);
	}
	public function widget_statistic($widget_id,Request $request,$mod=null){ 
#                var_dump(Session::getId());
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(0)));
		}
		$widget=\App\MPW\Widgets\Widget::find($widget_id);
		if(!$widget) abort(404);
		
		$user=\App\User::find($widget->user_id);
		#var_dump($user);
		$title="статистика виджета № ".$widget->id." для площадки ".$widget->partnerPad->domain."";
	    return view('advertiser.cabinet.widget_statistic', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to,"widget"=>$widget
		,"user"=>$user]);	
	}
	#invoice_print
	public function invoice_print(Request $request){
		$conf=[];
		$conf["wparams"]=Route::current()->parameters;
		if(!isset($conf["wparams"]["id"])) abort(403);
		$invoice=\App\Payments\Invoice::findOrFail($conf["wparams"]["id"]);
		if($invoice->Requisite->type_payout==2){
		$fanke='/home/www/storage.market-place.su/invoice/ooo.txt';	
		}else{
		$fanke='/home/www/storage.market-place.su/invoice/ip.txt';	
		}
		
		$frt=file($fanke);
		$postavka=[];
		foreach($frt as $kl){
		$kl=trim($kl);
         if($kl){
			 $tmp=explode(":",$kl);
			 $postavka[$tmp[0]]=$tmp[1];
		 #print"<pre>"; var_dump($kl); print"</pre>";	 
		 }
		}
		
		
		$conf["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^([admin]+\.)([a-z\-]+)/',$func,$m))
			$conf["pref"]=$m[1];
		$htmlPdf=Storage::exists('invoices_pdf/'.$conf["wparams"]["id"]);
		if(!$htmlPdf){
			$htmlPdf = view('advertiser.cabinet.invoice_pdf', ["config"=>$conf,"inv"=>$invoice,"postavka"=>$postavka]);
			Storage::put('invoices_pdf/'.$conf["wparams"]["id"], $htmlPdf);
		    //return PDF::loadHTML($html)->setWarnings(false)->stream('download.pdf');
		}
		
		$html=Storage::exists('invoices/'.$conf["wparams"]["id"]);
		if($html){
		return Storage::get('invoices/'.$conf["wparams"]["id"]);
		}
		$html = view('advertiser.cabinet.invoice_print', ["config"=>$conf,"inv"=>$invoice,"postavka"=>$postavka]);
		Storage::put('invoices/'.$conf["wparams"]["id"], $html);
		
		
		
		return $html;
	}
	
	public function invoice_view(Request $request){
		
		$conf=[];
		$conf["wparams"]=Route::current()->parameters;
                 if(!isset($conf["wparams"]["id"])) abort(403);
		$conf["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^([a-z]+\.)([a-z\-]+)/',$func,$m))
			$conf["pref"]=$m[1];
         if(isset($conf["wparams"]["id_user"])){
         $user=\App\User::findOrFail($conf["wparams"]["id_user"]);
         }else{
         $user=Auth::user();
         }
		 $title="";
		 #$invoice=\App\Payments\Invoice::findOrFail($conf["wparams"]["id"]);
		 #print"<pre>"; var_dump($invoice->Requisite); print"</pre>";
		return view('advertiser.cabinet.invoice', ["title"=>$title,"config"=>$conf]);
		
	}
	public function invoice_create(Request $request){
        $summa=$request->input('summa');
		$type=$request->input('type',1);
        if(!$summa) abort(403);
		$conf=[];
		$conf["wparams"]=Route::current()->parameters;
        $conf["wparams"]["summa"]=$summa;
         
		$conf["pref"]="";
		$func=Route::currentRouteName();
		if(preg_match('/^([a-z]+\.)([a-z\-]+)/',$func,$m))
			$conf["pref"]=$m[1];
         if(isset($conf["wparams"]["id_user"])){
         $user=\App\User::findOrFail($conf["wparams"]["id_user"]);
         }else{
         $user=Auth::user();
         }
		 #var_dump($conf["wparams"]);
		 
         $requisite=\App\Requisite::where('user_id', $user->id)->first();
		 $error=0;
		 if(!$requisite){
		 $error=1;
		 if($conf['pref']=="admin.")
			 $f1='admin.profile.personal';
		 else
			 $f1='profile.personal';
		 $error_url=route($f1,$conf['wparams']);
		 $title="Вы не заполнили реквизиты";
		 $error_url='<a class="btn btn-primary" href="'.$error_url.'#payments">Заполните реквизиты</a>';
			 
			 
		 }elseif(!$requisite->improved){
		    $error=1;
		   $title="Реквизиты не проверены";
		      $error_url='';
		 }
		 //{{}}
		
         if($error){
		
          return view('advertiser.cabinet.invoice_error', ["config"=>$conf,"title"=>$title,"error_url"=>$error_url]);	
         }
		 
		 $conf["wparams"]["id"]=\App\Payments\Invoice::createOrCheck($requisite,$summa,$type);
		 $urlde=route($conf["pref"].'invoice_view',$conf["wparams"]);  
		 return Redirect($urlde);
	}
	public function site_statistic(Request $request,$mod=null){
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(0)));
		}
		$cnt_new_warning=0;
		$sql="select count(*) as cnt from myadvert_warnings where new=1";
		$d1=\DB::connection("pgstatistic")->select($sql);
		if($d1 && $d1[0]->cnt){
         $cnt_new_warning=$d1[0]->cnt;
		}
		
		$title="статистика площаддок топадверт";
	    return view('advertiser.cabinet.all_widget_statistic', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to,"driver"=>1
		,"cnt_new_warning"=>$cnt_new_warning
		]);	
	}
	public function yandex_statistic(Request $request,$mod=null){
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(0)));
		}
				$cnt_new_warning=0;
		$sql="select count(*) as cnt from myadvert_warnings where new=1";
		$d1=\DB::connection("pgstatistic")->select($sql);
		if($d1 && $d1[0]->cnt){
         $cnt_new_warning=$d1[0]->cnt;
		}
		$title="статистика площаддок яндекс";
	    return view('advertiser.cabinet.all_widget_statistic', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to,"driver"=>2
		,"cnt_new_warning"=>$cnt_new_warning
		]);	
	}
	public function warnings_statistic(Request $request,$mod=null){
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(0)));
		}
		$cnt_new_warning=0;
		$sql="select count(*) as cnt from myadvert_warnings where new=1";
		$d1=\DB::connection("pgstatistic")->select($sql);
		if($d1 && $d1[0]->cnt){
         $cnt_new_warning=$d1[0]->cnt;
		}
		$title="сообщения от Api  market-place";
	
	    return view('advertiser.cabinet.warnings_statistic', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to,"driver"=>2
		,"cnt_new_warning"=>$cnt_new_warning
		]);	
	}	
	public function testpage_api(Request $request){
		
		   $wid=757;
		   $name = $request->input('name');
		   $jns = $request->input('data');
		   $count = $request->input('count');
		   $pad = $request->input('pad');
		   $pid = $request->input('pid');
		   if($pad){
			   $sql="select t1.id 
from widgets  t1
inner join widget_products t2
on t2.wid_id=t1.id
where t1.pad=$pad  and t1.status=0 ";
            $corm=[];
            $dats=\DB::connection()->select($sql);
			foreach($dats as $dt){
				$corm[]=$dt->id;
			}
			if($corm){
				$sql="
				select pid from wid_calculate where  pid in(".implode(",",$corm).")
and day =NOW()::date
order by ta_views desc limit 1";
				$gz=\DB::connection("pgstatistic")->select($sql);
				foreach($gz as $dt){
					$wid=$dt->pid;
				}
			}
		   }elseif($pid){
			   $wid=$pid;
		   }
	 $dpu = $request->input('dpu');
		   $t = $request->input('t');
		   $resp="";
             $params=[
			 "ip"=>$_SERVER["REMOTE_ADDR"]
			 ];

		   if($name){
			$params["text"]=[
			    "texts"=>["h1"=>$name,
			              "title"=>$name
			        ]
			];
		   }else{
$params["text"]=[
			    "texts"=>["h1"=>'',
			              "title"=>''
			        ]
			];
		   }
			if($count)
				$params["count"]=$count;
			if($t)
				$params["t"]=$t;
			if($pad)
				$params["pad"]=$pad;
			if($dpu)
				$params["pdu"]=rawurlencode($dpu);
			
			if($jns){
				$vns=json_decode($jns,true);
				if($vns)
				foreach($vns as $key=>$val){
					$params[$key]=$val;
				}
			}
			
			$url='https://request.market-place.su/api?id='.$wid.'&data='.urlencode(json_encode($params));
			var_dump($url);// die();
			$curl = curl_init();
            curl_setopt_array($curl, array(
            \CURLOPT_RETURNTRANSFER => 1,
            \CURLOPT_URL => $url
            ));
		   $resp = curl_exec($curl);
           curl_close($curl);
		   
		   
		   $dle=json_decode($resp,true);
		   $title="";
		   $debag=[];
		   if(isset($dle["debag"])){
			   $debag=$dle["debag"];
			   
		   }  
		 if(!$dle || !isset($dle["offers"]) || !$dle["offers"]){
		       return view('advertiser.test.nofound',['searchPath'=>'/api',"title"=>$title,'resp'=>$dle,"pageurl"=>rawurldecode($dpu)
			   ,"debag"=>$debag]);	
	             
		   }else{
			  
		   return view('advertiser.test.api',['searchPath'=>'/api',"title"=>$title,'resp'=>$dle,"pageurl"=>rawurldecode($dpu)
		   ,"debag"=>$debag
		   ]);	
		   }
	}
	
	public function site_statistic_pad($pad,Request $request,$mod=null){
		
		$from=$request->input("from");
		$to=$request->input("to");
		if(!$from || !$to){
			$to = date("Y-m-d");
			$from =  date("Y-m-d",(time()-(0)));
		}
		$ppa=\App\PartnerPad::findOrFail($pad);
		#var_dump($ppa->domain);
		$cnt_new_warning=0;
		$sql="select count(*) as cnt from myadvert_warnings where new=1";
		$d1=\DB::connection("pgstatistic")->select($sql);
		if($d1 && $d1[0]->cnt){
         $cnt_new_warning=$d1[0]->cnt;
		}
		$title="сообщения от Api  market-place";
		$title="статистика площадки ".$ppa->domain;
	    return view('advertiser.cabinet.site_statistic_pad', ['title'=>$title,'company'=>[],"mod"=>$mod, "from"=>$from, "to"=>$to
		,"cnt_new_warning"=>$cnt_new_warning]);	
	}

    
    public function company_delete($id){
		$company = \App\Advertise::findOrFail($id);
		
		$company->status=5;
		$company->save();
		return back();
	}
    public function invoice_status($id, Request $request){
		$status=$request->input('status');
		$nombre=$request->input('nombre');
		$sql="";
		switch($status){
		  case "1":
  		  $sql="update user_invoices
		  set payd=null,npp=null,performer_id=".Auth::user()->id."
		  where id=$id";
		   \DB::connection()->getPdo()->query($sql);
		  break;
          case "2":
		  $sql="update user_invoices
		  set payd=NOW(),npp='$nombre',performer_id=".Auth::user()->id."
		  where id=$id";
		   \DB::connection()->getPdo()->query($sql);
		  #\DB::connection()->getPdo()->exec($sql);
          break;		  
		}
		return response()->json([
			'ok' => true,
			'sql'=>$sql,
			'status' => $request->input('status')
		]);
	}	
	public function company_status($id, Request $request){
		$company = \App\Advertise::findOrFail($id);
		$status=$request->input('status');
		$company->status=$status;
		$company->save();
		$sql="update advertise_before_search 
		set status=".$status.",datetime=NOW()
		WHERE advertise_id=".$id."
		";
		\DB::connection("advertise")->statement($sql);
		$sql="insert into advertise_before_search (advertise_id,status) 
		select ".$id.",".$status."
		WHERE NOT EXISTS (SELECT 1 FROM advertise_before_search  WHERE advertise_id=".$id.")
		";
		\DB::connection("advertise")->statement($sql);
		return response()->json([
			'ok' => true,
			'status' => $request->input('status')
		]);
	}
}
