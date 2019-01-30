<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Auth;
class WidgetController extends Controller
{
     private $dbUser;
     private $dbPass;
    public function __construct()
      {
         $this->dbUser=env('DB_USERNAME');
         $this->dbPass=env('DB_PASSWORD');
      }

	public function edit($id){

		\Auth::user()->touch();
		$widget=\App\MPW\Widgets\Widget::find($id);
		if (!$widget){
			return abort(403);
		}
	
		if (!\Auth::user()->hasRole("admin") and !\Auth::user()->hasRole("super_manager") and !\Auth::user()->hasRole("manager") and \Auth::user()->id!=$widget->user_id){
			return abort(403);
		}
		//var_dump($widget->status); die();
		if ($widget->status==1){
			return abort(403);
		}
			
		$user=\App\User::find($widget->user_id);
		if ($widget->type==1){
		$templateTypes=\DB::table('widget_product_types')->whereNotIn('name', array('mobile'))->get();
		$widgetTemplates=\DB::table('widget_product_templates')->get();
		$widgetCustom=\App\WidgetEditor::firstOrNew(['wid_id'=>$id]);
		//$widget=\App\MPW\Widgets\Widget::find($id);
		if($widgetCustom->widget_categories=="-1"){
		$yandex_categories=$widget->getYandexCategories();
	    }else{
		#var_dump($widgetCustom->widget_categories); die();
		$smpd=explode(",",$widgetCustom->widget_categories);
		$yandex_categories=[];
	    if($smpd && $smpd[0]){
		
		 $yandex_categories=\DB::connection("product_next")->table("yandex_categories")->select("id","uniq_name")
		  ->whereIn("id",$smpd)
		  ->orderBy("uniq_name")
		  ->get();
		    //var_dump( $categories->toArray()); die();
	       }
		}	
		
		$armask=[];
		$mask_pages=\DB::connection("cluck")->table("widget_pages_mask")->where("id_widget","=",$id)->get();
		if($mask_pages){
			
			foreach($mask_pages as $ind=>$maska){
		    $armask[$ind]["url"]=$maska->url;
			$armask[$ind]["searchtext"]=$maska->searchtext;
			$armask[$ind]["strK"]= $maska->categories;
			$armask[$ind]["summa_from"]= $maska->summa_from;
			$armask[$ind]["summa_to"]= $maska->summa_to;
		
			
			if($maska->categories){
				 $tmpc=explode(",",$maska->categories);
			
			$armask[$ind]["categories"]=\DB::connection("product_next")->table("yandex_categories")->select("id","uniq_name")
		  ->whereIn("id",$tmpc)
		  ->orderBy("uniq_name")
		  ->get();
			}else{
				$armask[$ind]["categories"]=[];
			}
			
			}
			
		}
		#print "<pre>";	 var_dump($widget->partnerPad->user_id==558); print "</pre>";	
		//if($widget->partnerPad->user_id==558)
		$selfEdit=0;
	if($widget->partnerPad->user_id==558 or $widget->partnerPad->user_id==431 or $widget->partnerPad->user_id==13 or 
	$widget->partnerPad->user_id==581 or $widget->partnerPad->user_id==605 or $widget->partnerPad->user_id==505 or $widget->partnerPad->user_id==748 
	or $widget->partnerPad->user_id==867)
		$selfEdit=1;
		if(!$yandex_categories){
		$defaultText="мобильный телефон samsung";
		}
		else{
		$defaultText="";
		}
		return view('widget.product.editor', ['user'=>$user, 'templateTypes'=>$templateTypes, 'widgetTemplates'=>$widgetTemplates,"id_widget"=>$id, 'widgetCustom'=>$widgetCustom
		,'yandex_categories'=>$yandex_categories,"page_maska"=>$armask
		, 'widget'=>$widget,"defaultText"=>$defaultText,"selfEdit"=>$selfEdit]);
		}
		
		else if($widget->type==2){
			
			$wid=$widget;
			$widget=\App\WidgetVideo::where('wid_id',$id)->first();
			
			//$mysettings = \App\MPW\Widgets\VideoSettings::where('wid_id',$id)->first();
		   //echo "<pre>"; var_dump($widget->toArray());  echo "</pre>";  die();
			$exceptions=[];
			/*$sql="select  
    case when ec.pid is null then 0 else 1 end as forbidden
    ,l.id
    ,l.title
    from links l 
    left join exception 
    ec on ec.id_src=l.id and ec.pid=".$widget->id."
    order by l.title";*/
	$sql="select t3.id, t3.title, case when t4.pid is null then 0 else 1 end as forbidden from 
	widget_videos t1 left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
	'select id_block, id_link from blocks_links') AS p(id_block int, id_link int)) t2 on t1.block_rus=t2.id_block or 
	t1.block_cis=t2.id_block or t1.block_mobil=t2.id_block left join (SELECT p.* FROM dblink 
	('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 'select id, title from links') AS p(id int, title varchar)) 
	t3 on t2.id_link=t3.id left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
	'select id_src, pid from exception where pid=''$widget->id''') AS p(id_src int, pid int)) t4 on t3.id=t4.id_src where t1.id=".$widget->id." 
	group by t3.id, t3.title, t4.pid order by t3.title";

if($widget->type==1){
$kod=\App\Videosource\DiscUtil::VideoAutoplay($widget->id);
}elseif($widget->type==2){
$kod=\App\Videosource\DiscUtil::VideoOverplay($widget->id);	
}elseif($widget->type==3){
	$kod=\App\Videosource\DiscUtil::VideoVast($widget->id);	
}
elseif($widget->type==4){
	$kod=\App\Videosource\DiscUtil::VideoInpage($widget->id);	
}elseif($widget->type==5){
$kod=\App\Videosource\DiscUtil::VideoAutoplayMuted($widget->id);	
}
elseif($widget->type==6){
$kod=\App\Videosource\DiscUtil::VideoFlyRoll($widget->id);	
}
elseif($widget->type==7){
$kod=\App\Videosource\DiscUtil::VideoFlyRollMuted($widget->id);	
}

$pdo = \DB::connection()->getPdo();
$exceptions=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_CLASS);
$pdo = \DB::connection('videotest')->getPdo();
$sql="select t1.id, t1.title, case when t2.pid is null then 0 else 1 end as active from links t1 left join (select * from add_links where pid='$widget->id') t2 on 
	t1.id=t2.id_src where t1.status='1' order by t1.title asc";
$links=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_CLASS);
            if($widget->type==3){
				$blocks=\App\VideoBlock::where("type",1)->orderBy("name")->get();
			}else
			$blocks=\App\VideoBlock::orderBy("name")->get();
			$linkForWidget=[];
			$linkForPid=[];
			$allLinks=[];
       		        if($widget->autosort){
                        $linkForPid=\DB::connection("videotest")->select("select pl.autosort,pl.util,l.title

                        from pid_links pl                           inner join links l on l.id=pl.id_link
                        where pl.pid=".$widget->id." order by pl.autosort");
                        //var_dump($linkForPid);
                        }

			$commissions=\DB::table('сommission_groups')->where('type', '3')->orderBy('value', 'asc')->get();
			return view('widget.video.editor', ['links'=>$links, 'user'=>$user
                      , 'commissions'=>$commissions, 'wid'=>$wid, 'widget'=>$widget, 'blocks'=>$blocks, "id_widget"=>$id,"exceptions"=>$exceptions, 'linkForWidget'=>$linkForWidget
                      ,'linkForPid'=>$linkForPid
                      , 'allLinks'=>$allLinks,"kod"=>$kod]);
		}
		elseif ($widget->type==3){
			$widgetCustom=\App\WidgetTizer::firstOrNew(['wid_id'=>$id]);
			$widgetTemplates=\DB::table('widget_tizer_templates')->where('type', 1)->get();
			return view('widget.tizer.editor', ['user'=>$user, 'widgetCustom'=>$widgetCustom, 'widgetTemplates'=>$widgetTemplates, 'widget'=>$widget, "id_widget"=>$id]);
		}
		elseif ($widget->type==4){
			$widgetCustom=\App\WidgetBrand::firstOrNew(['wid_id'=>$id]);
			$kod=\App\Videosource\DiscUtil::Brand($widget->id);
			$blocks=\App\BrandBlock::all();
			$sql="select t1.id, t1.title, case when t2.pid is null then 0 else 1 end as forbidden from brand_offers t1 
			left join brand_wid_exep t2 on t1.id=t2.id_src and 
			t2.pid='$id' order by t1.title";
			$pdo = \DB::connection()->getPdo();
			$sources=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_CLASS);
			return view('widget.brand.editor', ['user'=>$user, 'widget'=>$widget, 'kod'=>$kod, 'widgetCustom'=>$widgetCustom, 'blocks'=>$blocks, 'sources'=>$sources]);
		}
		else{
			return abort(403);
		}
	}
	public function render(Request $request){
		\Auth::user()->touch();

	    $data=$request->input('data');
		
		if($data){
		$parameters=json_decode($data,true);
		}else{
		$parameters=[];
		}
		$wid=new \App\WidgetEditor();
		foreach($parameters as $key=>$value){

		 $wid->$key=$value;

		}
	   
		$args=$wid->render();
				//var_dump($args);
		return view('widget.product.render',$args);
		
	}
	
	public function TizerRender(Request $request){
		\Auth::user()->touch();
	    $data=$request->input('data');
		if($data){
		$parameters=json_decode($data,true);
		}else{
		$parameters=[];
		}
		$wid=new \App\WidgetTizer();
		foreach($parameters as $key=>$value){

		 $wid->$key=$value;

		}
	   
		$args=$wid->render();
		
		return view('widget.tizer.render',$args);
		
	}
	
	public function Mobilerender(Request $request){
		\Auth::user()->touch();
	    $data=$request->input('data');
		if($data){
		$parameters=json_decode($data,true);
		}else{
		$parameters=[];
		}
		$wid=new \App\WidgetEditor();
		foreach($parameters as $key=>$value){

		 $wid->$key=$value;

		}
		$args=$wid->mobile_render();
		 
		return view('widget.product.mobile_render',$args);
		
	}
	
	public function TizerMobilerender(Request $request){
		\Auth::user()->touch();
	    $data=$request->input('data');
		if($data){
		$parameters=json_decode($data,true);
		}else{
		$parameters=[];
		}
		$wid=new \App\WidgetTizer();
		foreach($parameters as $key=>$value){

		 $wid->$key=$value;

		}
		$args=$wid->mobile_render();
		 
		return view('widget.tizer.mobile_render',$args);
		
	}
	
	public function saveWidget($id,Request $request){
		
		\Auth::user()->touch();
$parameters=[];
$agrotopot=$request->input('agrotopot');
$blink_categories=$request->input('blink_categories');
$summa_from=$request->input('summa_from');
$summa_to=$request->input('summa_to');
#print "<pre>";  print_r($blink_categories);  print "</pre>"; die();
\DB::connection("cluck")->table("widget_pages_mask")->where("id_widget","=",$id)->delete();
if($agrotopot){
	$agrofirma=$request->input('agrofirma');
$cachmask=[];

	foreach($agrotopot as $ind=>$topot){
		$topot=trim($topot);
		
		if(isset($cachmask[$topot])) continue;
		$cachmask[$topot]=1;
		
		$bk="";
		$sum_f=0;
		$sum_t=0;
		$firma="";
		if($topot){
			
			if(isset($agrofirma[$ind])){
			$firma=trim($agrofirma[$ind]);	
			}
			if($blink_categories && isset($blink_categories[$ind])){
				$bk=trim($blink_categories[$ind]);
				
			}
			if ($summa_from && isset($summa_from[$ind])){
				$sum_f=$summa_from[$ind];
			}
			if ($summa_to && isset($summa_to[$ind])){
				$sum_t=$summa_to[$ind];
			}
			/*
			print "<pre>";  print_r([
					"id_widget"=>$id,
					"url"=>$topot,
					"searchtext"=>$firma,
					"categories"=>$bk,
					"summa_from"=>$sum_f,
					"summa_to"=>$sum_t,
					]);  print "</pre>";
				*/	
				if($bk || $firma){
					\DB::connection("cluck")->table("widget_pages_mask")->insert([
					"id_widget"=>$id,
					"url"=>$topot,
					"searchtext"=>$firma,
					"categories"=>$bk,
					"summa_from"=>$sum_f,
					"summa_to"=>$sum_t,
					]);
		       # print $ind."=>".$topot."/$ind/$firma/$bk<hr>";
				}
			
		
		}
	}
	#die();
}
$cache=time();

#$path = "/home/mp.su/widget.market-place.su/public/product_cache/cache.txt";
#file_put_contents($path, $cache);
 #die();
 
  /*$width=$request->input('width');
  $height=$request->input('height');
  if($width){
  $parameters["width"]=$width;
  }
  if($height){
  $parameters["height"]=$height;
  }*/
  $parameters["width"]=$request->input('width');
  if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')){
  $parameters["driver"]=$request->input('driver');
	  if ($request->input('css_select')){
		$css_select=implode(",", $request->input('css_select'));
	  }
	  else{
		$css_select='h1';
	  }
	  $parameters["css_select"]=$css_select;
  }
  $parameters["height"]=$request->input('height');
  $parameters["name"]=$request->input('template');
  $parameters["cols"]=$request->input('cols');
  $parameters["row"]=$request->input('row');
	if (!$request->input('background')){
		$background=$request->input('background_text');
	}
	else{
		$background=$request->input('background');
	}
	if (!$request->input('border_color')){
		$border_color=$request->input('border_color_text');
	}
	else{
		$border_color=$request->input('border_color');
	}
	if (!$request->input('background_model')){
		$background_model=$request->input('background_model_text');
	}
	else{
		$background_model=$request->input('background_model');
	}
	if (!$request->input('background_model_hover')){
		$background_model_hover=$request->input('background_model_hover_text');
	}
	else{
		$background_model_hover=$request->input('background_model_hover');
	}
	$parameters["background"]=$background;
	$parameters["border_color"]=$border_color;
	$parameters["background_model"]=$background_model;
	$parameters["background_model_hover"]=$background_model_hover;
	
	

	
	$parameters["border_width"]=$request->input('border_width');
	$parameters["border_radius"]=$request->input('border_radius');
	$parameters["font_family"]=$request->input('font_family');
	$parameters["font_size"]=$request->input('font_size');
	$parameters["mobile"]=$request->input('mobile');
	$parameters["name_mobile"]=$request->input('mobile_block_template');
	$parameters["mobile_background"]=$request->input('mobile_background');
	$parameters["mobile_background_model"]=$request->input('mobile_background_model');
	$parameters["mobile_background_model_hover"]=$request->input('mobile_background_model_hover');
	$parameters["mobile_font_family"]=$request->input('mobile_font_family');

	
        $wid=\App\WidgetEditor::firstOrNew(['wid_id'=>$id]);
		$wid->type=$request->input('type');
		
		if (\Auth::user()->hasRole(['admin','super_manager','manager'])){
		$nosearch=$request->input('nosearch');
		if($nosearch)
			$wid->nosearch=1;
		else
			$wid->nosearch=0;
		}
		foreach($parameters as $key=>$value){

		 $wid->$key=$value;

		}
		#
		$wid->widget_categories='';
			$yandex_categories=$request->input('cattuya');
			if(is_array($yandex_categories)){
				$smpd=[];
			foreach($yandex_categories as $yk){
				foreach($yk as $key=>$value){
					$smpd[$key]=1;
                 
				}
			}
			if($smpd){
				$wid->widget_categories=implode(",",array_keys($smpd)); 
			}
			# print "<pre>"; print_r($yandex_categories); print "</pre>"; die();
		    }
		
		
	//    var_dump($parameters); die();
		
		//$wid->name=$request->input('template');
		//$wid->width=$width;
		//$wid->height=$height;
		$widget=\App\MPW\Widgets\Widget::find($wid->wid_id);
		// var_dump($widget->partnerPad); die();
		if($widget->partnerPad->clid)
			$wid->clid=$widget->partnerPad->clid;
			else
		$wid->clid=null;	
		#var_dump($widget->partnerPad->clid); die();
		if($wid->cols && $wid->row){
		$offers_count=$wid->cols*$wid->row;
		
		$widget->offers_count=$offers_count;
		$widget->save();
		}
		
		
		$wid->save();
		$args=$wid->render();
		$path = "/home/mp.su/widget.market-place.su/public/compiled/widget_".$wid->wid_id.".html";
		$path_mobile = "/home/mp.su/widget.market-place.su/public/compiled/widget_mobile_".$wid->wid_id.".html";
		if($parameters["mobile"]){
			$args_mobile=$wid->mobile_render();
			$conf_mobile = view('widget.product.mobile_render',$args_mobile);
			file_put_contents($path_mobile,$conf_mobile);
		}else{
			
		}
		$conf = view('widget.product.render',$args);
	    //echo "<pre>"; var_dump($wid->toArray()); echo "</pre>"; die();
        file_put_contents($path, $conf);
		
		/* print "<pre>"; print_r($request->toArray());  print "</pre>";
        print "<pre>"; print_r($args);  print "</pre>"; die();*/ 
		return back()->with('message_success', "Виджет успешно сохранен.");;
	 
		
	}
	
	public function saveWidgetTizer($id,Request $request){
		\Auth::user()->touch();
$parameters=[];
  $parameters["width"]=$request->input('width');
  $parameters["height"]=$request->input('height');
  $parameters["name"]=$request->input('template');
  $parameters["cols"]=$request->input('cols');
  $parameters["row"]=$request->input('row');
  $parameters["background"]=$request->input('background');
  $parameters["mobile"]=$request->input('mobile')?$request->input('mobile'):0;
	
        $wid=\App\WidgetTizer::firstOrNew(['wid_id'=>$id]);
		foreach($parameters as $key=>$value){

		 $wid->$key=$value;

		}
		if($wid->cols && $wid->row){
		$offers_count=$wid->cols*$wid->row;
		$widget=\App\MPW\Widgets\Widget::find($wid->wid_id);
		$widget->offers_count=$offers_count;
		$widget->save();
		}
		$wid->save();
		$args=$wid->render();
		$path = "/home/mp.su/widget.market-place.su/public/compiled/widget_".$wid->wid_id.".html";
		$path_mobile = "/home/mp.su/widget.market-place.su/public/compiled/widget_mobile_".$wid->wid_id.".html";
		if($parameters["mobile"]){
			$args_mobile=$wid->mobile_render();
			$conf_mobile = view('widget.tizer.mobile_render',$args_mobile);
			file_put_contents($path_mobile,$conf_mobile);
		}else{
			
		}
		$conf = view('widget.tizer.render',$args);
	    //echo "<pre>"; var_dump($wid->toArray()); echo "</pre>"; die();
        file_put_contents($path, $conf);
		
		/* print "<pre>"; print_r($request->toArray());  print "</pre>";
        print "<pre>"; print_r($args);  print "</pre>"; die();*/ 
		return back()->with('message_success', "Виджет успешно сохранен.");;
	 
		
	}
	
	public function saveBrandWidget($id, Request $request){
		\Auth::user()->touch();
		$wid=\App\WidgetBrand::where('wid_id', $id)->first();
		if (!$wid){
			return abort(404);
		}
		$on_rus=$request->input('on_rus')?$request->input('on_rus'):0;
		$on_cis=$request->input('on_cis')?$request->input('on_cis'):0;
		$on_mobil=$request->input('on_mobil')?$request->input('on_mobil'):0;
		if ($on_rus==0 && $on_cis==0 && $on_mobil==0){
			return back()->with('message_war', "Необходимо выбрать как минимум 1 параметр для показа.");
		}
		$wid->on_rus=$on_rus;
		$wid->on_cis=$on_cis;
		$wid->on_mobil=$on_mobil;
		if (\Auth::user()->hasRole('admin')){
			$block_rus=$request->input('block_rus');
			$block_cis=$request->input('block_cis');
			$block_mobil=$request->input('block_mobil');
			$wid->block_rus=$block_rus;
			$wid->block_cis=$block_cis;
			$wid->block_mobil=$block_mobil;
		}
		$wid->save();
		$exceptions=$request->input('exception');
		\DB::connection()->table('brand_wid_exep')->where('pid', $wid->wid_id)->delete();
		if ($exceptions){
			foreach($exceptions as $exception){
			\DB::connection()->table('brand_wid_exep')->insert(array(
						array('pid' => $wid->wid_id, 'id_src' => $exception),
					));	
			}
		}
		\App\Videosource\DiscUtil::BrandUtile($wid->wid_id);
		
		return back()->with('message_success', "Виджет успешно сохранен.");
	}
	
	public function saveVideoWidget($id,Request $request){
	\Auth::user()->touch();
	//var_dump($request->toArray());	die();
	$parameters=[];
	
	//$parameters["deep_bonus_ru"]=0;
	//$parameters["deep_bonus_cis"]=0;
	if ($request->has('deep_bonus_ru')){
		$parameters["deep_bonus_ru"]=intval($request->input('deep_bonus_ru'));
	}
	if ($request->has('deep_bonus_cis')){
		$parameters["deep_bonus_cis"]=intval($request->input('deep_bonus_cis'));
	}
	//var_dump($parameters);	die();
	if ($request->input('adslimit')){
		$parameters["adslimit"]=$request->input('adslimit');
	}
	$parameters["width"]=$request->input('width');
	if ($parameters["width"]!=0 and $parameters["width"]<400){
		$parameters["width"]=400;
	}
	$parameters["height"]=$request->input('height');
	if ($parameters["height"]!=0 and $parameters["height"]<300){
		$parameters["height"]=300;
	}
	if ($id==845){
		$parameters["width"]=480;
		$parameters["height"]=320;
	}
	if ($id==1767 || $id==1860){
		$parameters["width"]=480;
		$parameters["height"]=320;
	}
	$parameters["control_rus"]=$request->input('control_rus')?1:0;

	$parameters["validation"]=$request->input('validations')?1:0;
	$parameters["muting"]=$request->input('muting')?1:0;
	$parameters["autosort"]=$request->input('autosort')?1:0;


	$parameters["control_cis"]=$request->input('control_cis')?1:0;
	$parameters["on_rus"]=$request->input('on_rus')?$request->input('on_rus'):0;
	$parameters["on_cis"]=$request->input('on_cis')?$request->input('on_cis'):0;
	$parameters["on_mobil"]=$request->input('on_mobil')?$request->input('on_mobil'):0;
	if ($request->input('block_rus')){
		$parameters["block_rus"]=$request->input('block_rus');
	}
	if ($request->input('block_cis')){
		$parameters["block_cis"]=$request->input('block_cis');
	}
	if ($request->input('block_mobil')){
		$parameters["block_mobil"]=$request->input('block_mobil');
	}
	if ($request->input('commission_rus')){
		$parameters["commission_rus"]=$request->input('commission_rus');
	}
	if ($request->input('commission_cis')){
		$parameters["commission_cis"]=$request->input('commission_cis');
	}
	
	//$parameters["type"]=1;
	//print "<pre>"; print_r($parameters);  print "</pre>"; die();
	
	$exceptions=$request->input('exception');
	$active=$request->input('active');
	$wid=\App\WidgetVideo::firstOrNew(['wid_id'=>$id]);
	
	//$widgetsComent=\App\MPW\Widgets\Widget::firstOrNew(['id'=>$id]);
	//$widgetsComent->coment=$request->input('coment');
	//$widgetsComent->save();
	
	$widgetsComent = \App\MPW\Widgets\Widget::where('id', '=', $id)->update(array('coment' => $request->input('coment'), 'name' => $request->input('WiName')));
	
	#\App\Videosource\DiscUtil::createVideoSettings($wid,$parameters,$exceptions);

	
	
	#$exceptions=$request->input('exception');
   

	\DB::connection('videotest')->table('exception')->where('pid', $wid->id)->delete();
	if ($exceptions){
			foreach($exceptions as $exception){
			\DB::connection('videotest')->table('exception')->insert(array(
						array('pid' => $wid->id, 'id_src' => $exception),
					));	
			}
	}
	\DB::connection('videotest')->table('add_links')->where('pid', $wid->id)->delete();
	if ($active){
			foreach($active as $act){
			\DB::connection('videotest')->table('add_links')->insert(array(
						array('pid' => $wid->id, 'id_src' => $act),
					));	
			}
	}
    \App\Videosource\DiscUtil::Utile($wid->id);

	
	
	//$wid->type=$request->input('type');
	foreach($parameters as $key=>$value){
		$wid->$key=$value;
	}
	$wid->save();
		//print "<pre>"; print_r($wid); print "</pre>"; die();
	return back()->with('message_success', "Виджет успешно сохранен.");;
	}
	
		public function createWidget(Request $request){
			\Auth::user()->touch();
		  $redirectUrl=app('url')->previous();
		  $type=$request->input('type');
		  $user_id=$request->input('user_id');
		  $pad=$request->input('pad');
		  $typeVideo=$request->input('typeVideo');
		  switch($type){
		  case 1:
		   $wid=new \App\MPW\Widgets\Product;
		  
		  break;
		  case 2:
		   $wid=new \App\MPW\Widgets\Video;
		  break;
		  case 3:
		   $wid=new \App\MPW\Widgets\Teaser;
		  
		  break;
		  case 4:
		   $wid=new \App\MPW\Widgets\Brand;
		  
		  break;
           default:
           die();
           break;		  
		  }
	    $wid->pad=$pad;
		$wid->user_id=$user_id;
		$u_host= \App\MPW\Sources\Urlparser::getLtd($wid->partnerPad->domain);
		if ($u_host){
			$wid->ltd=$u_host;
		}
		$wid->save();
		
		
		
		if($type==1){
		$widget=new \App\WidgetEditor();
		$widget->wid_id=$wid->id;
		$widget->type=2;
		$widget->name="module-block-third";
		$widget->width=400;
		$widget->height=200;
		$widget->cols=1;
		$widget->row=1;
		$widget->widget_categories=$wid->partnerPad->widget_categories;
		$widget->save();
		
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);   
		}
		if($type==2){
			$widget=new \App\WidgetVideo;
			$widget->wid_id=$wid->id;
			$widget->block_rus=49;
			$widget->block_cis=50;
			$widget->type=$typeVideo;
			if ($typeVideo==2){
				$widget->adslimit=2;
			}
			
			$widget->save();
			\App\Videosource\DiscUtil::createVideoSettings($widget,[],[]);
		if ($user_id==831){
			if ($pad!=809 and $pad!=808 and $pad!=861 and $pad!=860){
				$widget->block_rus=56;
				$widget->block_cis=57;
				$widget->block_mobil=56;
				$widget->save();
			}
		}
			
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);  
		}
		if($type==3){
		$widget=new \App\WidgetTizer();
		$widget->wid_id=$wid->id;
		$widget->name="module-block";
		$widget->width=400;
		$widget->height=200;
		$widget->cols=1;
		$widget->row=1;
		$widget->mobile=0;
		$widget->save();
		
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);   
		}
		if($type==4){
		$widget=new \App\WidgetBrand();
		$widget->wid_id=$wid->id;
		$widget->block_rus=7;
		$widget->block_cis=7;
		$widget->block_mobil=7;
		$widget->save();
		
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);   
		}
		
		return redirect()->to($redirectUrl);

		}
		
		public function createWidgetJs(Request $request){
			\Auth::user()->touch();
		  $redirectUrl=app('url')->previous();
		  $type=$request->input('type');
		  $user_id=$request->input('user_id');
		  $pad=$request->input('pad');
		  $typeVideo=$request->input('typeVideo');
		  switch($type){
		  case 1:
		   $wid=new \App\MPW\Widgets\Product;
		  
		  break;
		  case 2:
		   $wid=new \App\MPW\Widgets\Video;
		  break;
		  case 3:
		  $wid=new \App\MPW\Widgets\Teaser;
		   #$wid=new \App\MPW\Widgets\Product;
		  break;
		  case 4:
		   $wid=new \App\MPW\Widgets\Brand;
		  
		  break;
           default:
           die();
           break;		  
		  }
	    $wid->pad=$pad;
		$wid->user_id=$user_id;
		$u_host= \App\MPW\Sources\Urlparser::getLtd($wid->partnerPad->domain);
		if ($u_host){
			$wid->ltd=$u_host;
		}
		$wid->save();
		
		
		if($type==1){
		$widget=new \App\WidgetEditor();
		$widget->driver=$wid->partnerPad->driver;
		$widget->wid_id=$wid->id;
		$widget->type=2;
		$widget->name="module-block-third";
		$widget->width=400;
		$widget->height=200;
		$widget->cols=1;
		$widget->row=1;
		$widget->widget_categories=$wid->partnerPad->widget_categories;
		$widget->save();
		
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);   
		}
		if($type==2){
			$widget=new \App\WidgetVideo;
			$widget->wid_id=$wid->id;
			$widget->block_rus=76;
			$widget->block_cis=77;
			$widget->block_mobil=76;
			$widget->type=$typeVideo;
			if ($typeVideo==2){
				$widget->adslimit=2;
			}
			if ($typeVideo==4){
				$widget->control_rus=1;
				$widget->control_cis=1;
			}
			
			$widget->save();
			
			\App\Videosource\DiscUtil::createVideoSettings($widget,[],[]);
			if ($user_id==831){
				if ($pad!=809 and $pad!=808 and $pad!=861 and $pad!=860){
					$widget->block_rus=56;
					$widget->block_cis=57;
					$widget->block_mobil=56;
					$widget->save();
				}
			}
			if ($typeVideo==1 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=76;
				$widget->block_cis=77;
				$widget->block_mobil=76;
			}
			if ($typeVideo==2 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=12;
				$widget->block_cis=12;
				$widget->block_mobil=12;
			}
			if ($typeVideo==3 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=21;
				$widget->block_cis=21;
				$widget->block_mobil=21;
			}
			if ($typeVideo==4 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=76;
				$widget->block_cis=77;
				$widget->block_mobil=76;
			}
			if ($typeVideo==5 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=72;
				$widget->block_cis=81;
				$widget->block_mobil=72;
				$widget->commission_rus='v-000017';
				$widget->commission_cis='v-000004';
			}
			
			if ($typeVideo==6 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=76;
				$widget->block_cis=77;
				$widget->block_mobil=76;
				$widget->commission_rus='v-000017';
				$widget->commission_cis='v-000004';
			}
			
			if ($typeVideo==7 && $wid->partnerPad->video_categories==0){
				$widget->block_rus=72;
				$widget->block_cis=81;
				$widget->block_mobil=72;
				$widget->commission_rus='v-000017';
				$widget->commission_cis='v-000004';
			}
			$widget->save();
			
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);  
		}
		if($type==3){
			
		$widget=new \App\WidgetTizer();
		$widget->wid_id=$wid->id;
		$widget->name="module-block-third";
		$widget->width=400;
		$widget->height=200;
		$widget->cols=1;
		$widget->row=1;
		$widget->mobile=0;
		$widget->save();
		
		$redirectUrl=route('widget.edit',['id'=>$wid->id]);    
		}
		if($type==4){
		$widget=new \App\WidgetBrand();
		$widget->wid_id=$wid->id;
		$widget->block_rus=7;
		$widget->block_cis=7;
		$widget->block_mobil=7;
		$widget->save();
		
		$redirectUrl=route('home');    
		}
		return response()->json([
			'ok' => true,
			'to' => $redirectUrl
		]);
		return redirect()->to($redirectUrl);

		}
		
		public function test(Request $request){
		\Auth::user()->touch();
		echo "тест ок";
		}
	
	public function deleteWidget($id, Request $request){
		if (!\Auth::user()->hasRole("admin") and !\Auth::user()->hasRole("super_manager") and !\Auth::user()->hasRole("manager") and \Auth::user()->id!=$widget->user_id){
			return abort(403);
		}
		$widget=\App\MPW\Widgets\Widget::find($id);
		$widget->status=1;
		$widget->save();
		$cmd = "/bin/rm -rf /home/www/widget.market-place.su/public/compiled/widget_".$widget->id.".html";
		`$cmd`;
		$cmd = "/bin/rm -rf /home/www/widget.market-place.su/public/compiled/widget_mobile_".$widget->id.".html";
		`$cmd`;
		$wid=\App\WidgetVideo::where('wid_id', $widget->id)->first();
		if ($wid){
			\App\Videosource\DiscUtil::Utile($wid->id);
		}
		\App\Videosource\DiscUtil::BrandUtile($widget->id);
		return back()->with('message_success', "Виджет успешно удален.");	
	}
	
	public function deleteWidgetPost($id, Request $request){
		$widget=\App\MPW\Widgets\Widget::find($id);
		if (!\Auth::user()->hasRole("admin") and !\Auth::user()->hasRole("super_manager") and !\Auth::user()->hasRole("manager") and \Auth::user()->id!=$widget->user_id){
			return abort(403);
		}
		
		$widget->status=1;
		$widget->save();
		$cmd = "/bin/rm -rf /home/www/widget.market-place.su/public/compiled/widget_".$widget->id.".html";
		`$cmd`;
		$cmd = "/bin/rm -rf /home/www/widget.market-place.su/public/compiled/widget_mobile_".$widget->id.".html";
		`$cmd`;
		$wid=\App\WidgetVideo::where('wid_id', $widget->id)->first();
		if ($wid){
			\App\Videosource\DiscUtil::Utile($wid->id);
		}
		\App\Videosource\DiscUtil::BrandUtile($widget->id);
		return response()->json([
			'ok' => true,
			'message' => 'Виджет успешно удален'
		]);
	}
}
