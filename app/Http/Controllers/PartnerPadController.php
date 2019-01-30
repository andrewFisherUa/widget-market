<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\PartnerPad;
use Illuminate\Support\Facades\Validator;
use App\Notifications\StatusPad;
use App\AllNotification;
class PartnerPadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addPads(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$domain=mb_strtolower($request->input('domain'));
		$type=$request->input('type');
		$stcurl=$request->input('stcurl');
		$stclogin=$request->input('stclogin');
		$stcpassword=$request->input('stcpassword');
		$parse = parse_url(trim($domain));
		$out = $domain;
		if (isset($parse['host'])) {
			if ($parse['host']){
				$out = $parse['host'];
			}
			else{
				$out = explode('/', $parse['path'], 2);
				$out = array_shift($out);
				$out = trim($out);
			}
		}
		else{
			$out = explode('/', $parse['path'], 2);
			$out = array_shift($out);
			$out = trim($out);
		}
		if ($out){
			$domain = $out;
		}
		$validator=Validator::make(
			array(
				'domain' => $domain
			),
			array(
				'domain' => 'required|string|max:255|unique:partner_pads',
			)
		);
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}

		$partnerPad= new PartnerPad;
		$partnerPad->user_id=$user_id;
		$partnerPad->domain=$domain;
		if ($type){
			$partnerPad->type=$partnerPad->settype($type);
		}
		else{
			$partnerPad->type=$type;
		}
		$partnerPad->stcurl=$stcurl;
		$partnerPad->stclogin=$stclogin;
		$partnerPad->stcpassword=$stcpassword;
		$partnerPad->status=0;
		$partnerPad->save();
		return back()->with('message_success','Площадка успешно добавлена на модерацию.');
    }
	
	public function addPadsJs(Request $request){
		\Auth::user()->touch();
		$user_id=$request->input('user_id');
		$domain=mb_strtolower($request->input('domain'));
		$type=$request->input('type');
		$stcurl=$request->input('stcurl');
		$stclogin=$request->input('stclogin');
		$stcpassword=$request->input('stcpassword');
		$parse = parse_url(trim($domain));
		$out = $domain;
		if (isset($parse['host'])) {
			if ($parse['host']){
				$out = $parse['host'];
			}
			else{
				$out = explode('/', $parse['path'], 2);
				$out = array_shift($out);
				$out = trim($out);
			}
		}
		else{
			$out = explode('/', $parse['path'], 2);
			$out = array_shift($out);
			$out = trim($out);
		}
		if ($out){
			$domain = $out;
		}
		$validator=Validator::make(
			array(
				'domain' => $domain
			),
			array(
				'domain' => 'required|string|max:255|unique:partner_pads',
			)
		);
		if ($validator->fails()){
			return response()->json([
				'ok' => false,
				'message'=>'Домен не прошел валидацию.'
			]);
		}

		$partnerPad= new PartnerPad;
		$partnerPad->user_id=$user_id;
		$partnerPad->domain=$domain;
		if ($type){
			$partnerPad->type=$partnerPad->settype($type);
		}
		else{
			$partnerPad->type=$type;
		}
		$partnerPad->stcurl=$stcurl;
		$partnerPad->stclogin=$stclogin;
		$partnerPad->stcpassword=$stcpassword;
		$partnerPad->status=0;
		$partnerPad->save();
		return response()->json([
				'ok' => true,
				'message'=>'Площадка успешно добавлена на модерацию.'
			]);
    }
	
	public function allPads(Request $request){
		\Auth::user()->touch();
		$list=[];
		$type=$request->input('type');
		$manager=$request->input('manager');
		if ($request->input('poisk')){
			$manager=null;
		}
		if (Auth::user()->hasRole('manager') or $manager){
			if (Auth::user()->hasRole('admin') or (Auth::user()->hasRole('super_manager')) and $manager){
				$users=UserProfile::where('manager', $manager)->get();
			}
			else{
				$users=UserProfile::where('manager', Auth::user()->id)->get();
			}
			
			foreach ($users as $user){
				array_push($list,$user->user_id);
			}
			if ($request->input('poisk')){
				$pads=PartnerPad::whereIn('user_id', $list)->where('domain', 'like', '%'.$request->input('poisk').'%')->orderBy('created_at', 'desc')->paginate(30);
			}
			else{
				$pads=PartnerPad::whereIn('user_id', $list)->where(function ($query) use ($type){
					if ($type==0){
						$query->whereIn('type', [-1,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15])->orWhereNull('type');
					}
					else{
						$query->where('type', $type);
					}
				})->orderBy('created_at', 'desc')->paginate(30);
			}
		}
		else{
			if ($request->input('poisk')){
				$pads=PartnerPad::where('domain', 'like', '%'.$request->input('poisk').'%')->orderBy('created_at', 'desc')->paginate(30);
			}
			else{
				
				$pads=PartnerPad::where(function ($query) use ($type){
					if ($type==0){
						$query->whereIn('type', [-1,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15])->orWhereNull('type');
					}
					else{
						$query->where('type', $type);
					}
				})				
				->orderBy('created_at', 'desc')->paginate(30);
				//var_dump($pads); die();
			}
		}
		return view('admin.pads.index', ['pads'=>$pads, 'type'=>$request->input('type'), 'manager'=>$request->input('manager'), 'poisk'=>$request->input('poisk')]);
	}
	
	public function editPad($id){
		\Auth::user()->touch();
		$pad=PartnerPad::findOrFail($id);
		if (Auth::user()->hasRole('manager')){
			if ($pad->setUser($pad->user_id)->profile->manager!=Auth::user()->id){
				return abort(403);
			}
		}
		$all_widgets=\DB::connection()->table("widgets")->where("pad",$id)->get();
		foreach($all_widgets as $aw){
				#print_r($aw); echo "<br>";
		}
	
		#die();
		
		$all_t_categories=\DB::connection("cluck")->table("teaser_categories_pads")
		->select(\DB::raw("-1 as id"),\DB::raw("'Все' as name"),"category_id")
		->where("pad_id",\DB::raw($id))
		->where("category_id",\DB::raw(-1));
		
		$t_categories=\DB::connection("cluck")->table("teaser_categories as t")->select("t.id","t.name","p.category_id")
		->leftJoin("teaser_categories_pads as p", function($join) use ($id)
                         {
						 $join->on('p.pad_id','=',\DB::raw($id));
						 $join->on('p.category_id','=',"t.id");
						 })->union($all_t_categories)->orderBy("name")
						 ->get();
		
		$k2=0;
		foreach($t_categories as $cat){
			
			if($cat->category_id==-1)
				$k2=1;
		}
		//$t_categories
		#print  "<pre>";var_dump($t_categories );print  "</pre>";  die();
	
	$k1=0;
	  $categories=[];
	  if($pad->widget_categories){
		  $smpd=explode(",",$pad->widget_categories);
	  if($smpd){
		  
		  $categories=\DB::connection("product_next")->table("yandex_categories")->select("id","uniq_name")
		  ->whereIn("id",$smpd)
		  ->orderBy("uniq_name")
		  ->get();
		    //var_dump( $categories->toArray()); die();
	  }
	
		#print   $pad->widget_categories."\n";
	  }
		$clids=\DB::connection()->table('yandex_clid')->get();
		return view('admin.pads.edit_pad', ['pad'=>$pad,'categories'=>$categories,"k1"=>$k1, "clids"=>$clids, "t_categories"=>$t_categories,"k2"=>$k2]);
	}
	
	public function savePad($id, Request $request){
		
		\Auth::user()->touch();
		$pad=PartnerPad::findOrFail($id);
		$type=$request->input('type');
		
		
		$old_status=$pad->status;
		$old_type=$pad->type;
		$pad->widget_categories=""; 	
		if ($type){
			$yandex_categories=$request->input('cattuya');
			if(is_array($yandex_categories)){
				$smpd=[];
			foreach($yandex_categories as $yk){
				foreach($yk as $key=>$value){
					$smpd[$key]=1;
                 
				}
			}
			if($smpd){
				$pad->widget_categories=implode(",",array_keys($smpd)); 
			}
			
		    }
			
			$t_categories=$request->input('id_t_categories');
		 # print "<pre>"; print_r($t_categories); print "</pre>"; die();
			\DB::connection("cluck")->table("teaser_categories_pads")->where("pad_id",$id)->delete();
			if(1==1 && (!$t_categories || isset($t_categories[-1]))){
				
				\DB::connection("cluck")->table("teaser_categories_pads")->insert(
				  ['pad_id' => $id
				, 'category_id' => -1]
				);
				
			}else{
				foreach($t_categories as $key=>$val){
				\DB::connection("cluck")->table("teaser_categories_pads")->insert(
				  ['pad_id' => $id
				, 'category_id' => $key]
				);	
					
				}
			}
			
			#$categories2=\DB::connection("cluck")->table("main_categories_pads")->where("pad_id",$id)->get();
			
			#echo "<pre>"; print_r($categories2); echo "</pre>"; die(); 
			
			
			$pad->domain=mb_strtolower($request->input('domain'));
			$pad->stcurl=$request->input('stcurl');
			$pad->stclogin=$request->input('stclogin');
			$pad->stcpassword=$request->input('stcpassword');
			$pad->status=1;
			
			$pad->type=$pad->settype($type);
			#if ($pad->type==1 or $pad->type==3){
				#$pad->id_categories=$request->input('id_categories');
			#}
			#else {
				$pad->id_categories=null;
			#}
			if ($pad->type=='-1' or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14){
				if ($request->video_categories=='4'){
					return back()->with('message_warning','Не выбрана категория видео.');
				}
				$pad->video_categories=$request->input('video_categories');
			}
			else{
				$pad->video_categories=null;
			}
			if ($pad->type=='-1' or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13){
				if ($request->driver=='0'){
					return back()->with('message_warning', 'Не выбран драйвер');
				}
				$pad->driver=$request->input('driver');
			}
			else{
				$pad->driver=null;
			}
			$old_clid=$pad->clid;
			// драйвер 2 - Яндекс, драйвер 3 - Яндекс API
			if ($request->input('driver')=='2' || $request->input('driver')=='3'){
				if ($request->input('clid')==0){
					return back()->with('message_warning', 'Не выбран клид');
				}
				$pad_clid=PartnerPad::where('clid', $request->input('clid'))->first();
				if ($pad_clid){
					if ($request->input('clid')!='2291286' and $pad_clid->user_id!=$pad->user_id){
						return back()->with('message_warning', 'Выбранный клид занят.');
					}
				}	
				$pad->clid=$request->input('clid');
				$id_yandex=\DB::table('yandex_clid')->where('clid', $request->input('clid'))->first();
				$pad->id_yandex=$id_yandex->id_place;
			}
			else{
				if (!$old_clid){
					$pad->clid=null;
					$pad->id_yandex=null;
				}
				if ($old_clid){
					$widgets=\DB::connection()->table('widgets')->select("id")->where('pad', $id)->where('status', 0)->where('type', 1)->get();
					$wid_clid=null;
					foreach($widgets as $widd){
						$wid_clid=\App\WidgetEditor::where('wid_id', $widd->id)->where('clid', $old_clid)->first();
					}
					if ($wid_clid){
						if ($request->input('clid')!=0){
							$pad->clid=$request->input('clid');
							$id_yandex=\DB::table('yandex_clid')->where('clid', $request->input('clid'))->first();
							$pad->id_yandex=$id_yandex->id_place;
						}
						else{
						return back()->with('message_warning', 'Нельзя удалить клид, так как на это площадке есть виджет с драйвером яндекс.');
						}
					}
					else{
						$pad->clid=null;
						$pad->id_yandex=null;
					}
				}
			}
			if(!$pad->id_categories)
				$pad->id_categories='';
			#print_r($pad);
			#die();
			$pad->save();
			//\DB::connection('videotest')->table('exception')->where('pid', $mysettings->pid)->delete();
			$widgets=\DB::connection()->table('widgets')->select("id")->where('pad', $id)->get();
			foreach($widgets as $widd){
				if ($pad->type=='-1' or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13){
					if ($old_clid!=$pad->clid and $pad->clid){
						$wid=\App\WidgetEditor::where('wid_id', $widd->id)->first();
						if (!$wid){
							continue;
						}
						var_dump($wid->id);
						$wid->id_yandex=$pad->id_yandex;
						$wid->clid=$pad->clid;
						$wid->save();
						$args=$wid->render();
						$path = "/home/www/widget.market-place.su/public/compiled/widget_".$wid->wid_id.".html";
						$conf = view('widget.product.render',$args);
						if ($wid->mobile==1){
						$path_mobile = "/home/www/widget.market-place.su/public/compiled/widget_mobile_".$wid->wid_id.".html";
							$args_mobile=$wid->mobile_render();
							$conf_mobile = view('widget.product.mobile_render',$args_mobile);
							file_put_contents($path_mobile,$conf_mobile);
						}
						
						file_put_contents($path, $conf);
					}
				}
				//$wi=\DB::connection()->table('widget_videos')->select("id")->where('wid_id',$widd->id)->first();
				//if($wi){
				//var_dump($wi); die();	
					
				//}
				\DB::connection()->table('widget_videos')->where('wid_id',$widd->id)->update(["video_category"=>$pad->video_categories]);

			}
			//die();
			\App\Videosource\DiscUtil::Utile();
			$pad->save();
		}
		else{
			$pad->domain=mb_strtolower($request->input('domain'));
			$pad->stcurl=$request->input('stcurl');
			$pad->stclogin=$request->input('stclogin');
			$pad->stcpassword=$request->input('stcpassword');
			$pad->status=2;
			$pad->type=null;
			$pad->id_categories=null;
			$pad->video_categories=null;
			$pad->driver=null;
			$pad->clid=null;
			
			$pad->save();
			$notif_header="Ваша площадка $pad->domain не прошла модерацию";
		}
		/*статус тизерки*/
		$ppdo=\DB::connection()->getPdo();
		
		if($pad->id and $type){
			if(!in_array(1,$type)){
			$sql="update widget_products
              set status=0
              where wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id." and t1.type=1
              )";
			  $ppdo->exec($sql);
		}else{
		$sql="update widget_products
              set status=1
              where wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id." and t1.type=1
              )";	
			  $ppdo->exec($sql);
		}
		
		if(!in_array(4,$type)){
			$sql="update widget_tizers
              set status=0
              where wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id." and t1.type=3
              )";
			 $ppdo->exec($sql);
			  $sql=" update widget_products
              set status=0
              where type3=3 and  wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id." and t1.type=3
              )";
			 
			  $ppdo->exec($sql);
			  // var_dump($sql); die();
		}else{
		$sql="update widget_tizers
              set status=1
              where wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id." and t1.type=3
              )";	
			  $ppdo->exec($sql);
			  $sql="update widget_products
              set status=1
              where type3=3 and wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id." and t1.type=3
              )";	
			 # var_dump($sql); die();
			  $ppdo->exec($sql);
		}
		
		}else{
			$sql="update widget_products
              set status=0
              where wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id."
              );
			  update widget_tizers
              set status=0
              where wid_id in(
              select t1.id from widgets t1 
              where t1.pad=".$pad->id."
              );
			  ";
			  $ppdo->exec($sql);
		}
		if ($old_status!=$pad->status or $old_type!=$pad->type){
			$notif_header="Изменения статуса Вашей площадки $pad->domain";
			if ($pad->status==1){
				if ($pad->type==1){
					$body="Ваша площадка $pad->domain прошла модерацию на товарный виджет.";
				}
				elseif($pad->type==2){
					$body="Ваша площадка $pad->domain прошла модерацию на видео виджет.";
				}
				elseif ($pad->type==3){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет и видео виджет.";
				}
				elseif ($pad->type==4){
					$body="Ваша площадка $pad->domain прошла модерация на тизерный виджет.";
				}
				elseif ($pad->type==5){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет и тизерный виджет.";
				}
				elseif ($pad->type==6){
					$body="Ваша площадка $pad->domain прошла модерация на видео виджет и тизерный виджет.";
				}
				elseif ($pad->type==7){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет, видео виджет и тизерный виджет.";
				}
				elseif ($pad->type==8){
					$body="Ваша площадка $pad->domain прошла модерация на брендирование.";
				}
				elseif ($pad->type==9){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет и брендирование.";
				}
				elseif ($pad->type==10){
					$body="Ваша площадка $pad->domain прошла модерация на видео виджет и брендирование.";
				}
				elseif ($pad->type==11){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет, видео виджет и брендирование.";
				}
				elseif ($pad->type==12){
					$body="Ваша площадка $pad->domain прошла модерация на тизерный виджет и брендирование.";
				}
				elseif ($pad->type==13){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет, тизерный виджет и брендирование.";
				}
				elseif ($pad->type==14){
					$body="Ваша площадка $pad->domain прошла модерация на видео виджет, тизерный виджет и брендирование.";
				}
				elseif ($pad->type=='-1'){
					$body="Ваша площадка $pad->domain прошла модерация на товарный виджет, видео виджет, тизерный виджет и брендирование.";
				}
			}
			if ($pad->status==2){
				$body="Ваша площадка $pad->domain не прошла модерацию.";
			}
			$notif=new AllNotification;
			$notif->user_id=$pad->user_id;
			$notif->header=$notif_header;
			$notif->body=$body;
			$notif->save();
			\Notification::send(\App\User::find($pad->user_id), (new StatusPad($pad->id)));	
		}	
		if ($pad->type){
		return back()->with('message_success','Площадка успешно промодерирована.');
		}
		else{
		return back()->with('message_success','Площадка отклонена.');
		}
	}
	
	public function editPadAffiliate(Request $request){
		\Auth::user()->touch();
		$pad=PartnerPad::where('id', $request->input('pad_id'))->first();
		$pad->stcurl=$request->input('stcurl');
		$pad->stclogin=$request->input('stclogin');
		$pad->stcpassword=$request->input('stcpassword');
		$pad->save();
		return back()->with('message_success','Площадка успешно отредактирована.');
	}
	
	public function editPadJs(Request $request){
		\Auth::user()->touch();
		$pad=PartnerPad::where('id', $request->input('pad_id'))->first();
		if (\Auth::user()->hasRole('affiliate')){
			if ($pad->user_id!=\Auth::user()->id){
				return abort(403);
			}
		}
		$pad->stcurl=$request->input('stcurl');
		$pad->stclogin=$request->input('stclogin');
		$pad->stcpassword=$request->input('stcpassword');
		$pad->save();
		return response()->json([
				'ok' => true,
				'message'=>'Площадка успешно отредактирована.'
			]);
	}
	
}
