<?php

namespace App\Console\Commands\Widgets;

use Illuminate\Console\Command;

class Getfrom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widgets:getfrom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'widgets:getfrom';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
	public function getCategory(){
	 $pdopg = \DB::connection("videotest")->getPdo(); 
	  $pdoo = \DB::connection()->getPdo(); 
	 $psql = \DB::connection("mysqlapi")->getPdo();
	 $sql ="select * from partner_pads where video_categories=0";
	
	 $links=$pdoo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $sql="update pid_video_settings set video_category = null";
   // $pdopg->exec($sql);
     $sql="update pid_video_settings set video_category =0 where name =?";
	 $sthInsertPids=$pdopg->prepare($sql);
	 
	 foreach($links as $link){
		 //var_dump([$link]);
	 //var_dump([$link["id"], $link["video_categories"]]);
	 $sthInsertPids->execute([$link["domain"]]);
	 }
	}
	public function getWidgetId(){
	   $pdopg = \DB::connection("videotest")->getPdo();
	  $sql="update pid_video_settings set wid_id =? where pid =?";
	 $sthInsertPids=$pdopg->prepare($sql);	
		 $pdoo = \DB::connection()->getPdo(); 
		 $sql ="select * from widget_videos"; 
		 $links=$pdoo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		// foreach($links as $link){
			
		//	 $sthInsertPids->execute([$link["wid_id"],$link["id"]]);
		//	 var_dump($link["wid_id"]); 
			 
		// }
		
		
		
		 
	}
	
	public function getPadsId(){
			
  $ustril=[42=>6,36=>11,20=>10,1=>7,26=>15,32=>12,10=>8,43=>13];	
  

		//$pdopg = \DB::connection("videotest")->getPdo();
				$pdopg=\DB::connection("videotest")->getPdo();
        $sql="insert into pid_video_settings  (pid,width,height,control,id_block_ru,id_block_cis,summa_ru,summa_cis,name)
		select ?,?,?,?,?,?,?,?,?
				WHERE NOT EXISTS (SELECT 1 FROM pid_video_settings WHERE pid=?)
		";
		$sthInsertPids=$pdopg->prepare($sql);
			  $sql="update pid_video_settings set wid_id =? where pid =?";
	          $sthIUpdateWid=$pdopg->prepare($sql);	
			  $sql="update pid_video_settings set name =? where pid =?";
	          $sthIUpdateNAME=$pdopg->prepare($sql);	
		 $sql="update  pid_video_settings set id_block_ru=?,id_block_cis=?,control=?
		 WHERE pid=?
		";
		$sthUpdatePids=$pdopg->prepare($sql);
		$pdo = \DB::connection()->getPdo();
		  $sql2="select t2.id, t3.domain from widgets t1 
		  left join (select id, wid_id from widget_videos) 
		  t2 on t1.id=t2.wid_id 
		  left join (select id, domain from partner_pads)
		  t3 on t1.pad=t3.id 
		  
          ";

		  $wi=$pdo->query($sql2)->fetchAll(\PDO::FETCH_ASSOC);	
		  
		  $pipaa=[];
		  foreach( $wi as $k){
			  $pipaa[$k["id"]]=$k["domain"];
			  
		  }
	    //$stheeelect=$pdo->prepare($sql2);
		//$wi=$stheeelect->execute();
			
		$sql="select * from widget_videos";
		$links=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
			foreach($links as $link){
				$mysettings = \App\MPW\Widgets\VideoSettings::where('pid',$link["id"])->first();
				if(!$mysettings){
					continue;
					$data=[];
					$data["summa_ru"]=$link["commission_rus"];
					$data["summa_cis"]=$link["commission_cis"];
					$data["width"]=$link["width"];
					$data["height"]=$link["height"];
					$id=$link["id"];
					$data["control"]=$link["control"];
					$data["id_block_ru"]=0;
					$data["id_block_cis"]=0;
				//	var_dump($data);
				//echo $link["id"]."\n";	
			  //=>
               //string(2) "30"
              //=>
			  //$data["domain"]="___";
	
				//		$sthUpdatePids->execute([$data["id_block_ru"],$data["id_block_cis"],$data["control"],$id]);
	   //$sthInsertPids->execute([$id,$data["width"],$data["height"],$data["control"],$data["id_block_ru"],$data["id_block_cis"],$data["summa_ru"],$data["summa_cis"],$data["domain"],$id]);
				
				}
				if(!isset($pipaa[$link["id"]])){
					$domain="хрень какая то";
					//var_dump($link["id"]);
					
				}else{
					$domain=$pipaa[$link["id"]];
				}
				$sthIUpdateNAME->execute([$domain,$link["id"]]);
				//$sthIUpdateWid->execute([$link["wid_id"],$link["id"]]);
			    //echo $link["wid_id"]."\n"; 	
			}
		return ;

		
		$sql="delete from pid_video_settings where id=?";
		$sthd=$pdopg->prepare($sql);
		$sql="select * from pid_video_settings order by name";
		$links=$pdopg->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
		foreach($links as $link){
			$videowidget=\DB::connection()->table('widget_videos')->where('id', $link["pid"])->first();
			if(!$videowidget){

				$sthd->execute([$link["id"]]);
			    var_dump([$link["pid"],$link["name"]]);
			//	continue;
			}else{
				//gotowik.ru
				
			}
		}
		return;
		
		$pdoo11 = \DB::connection()->getPdo();
		$sql=" update widgets set pad=? where id=? ";
		
		$sthuu=$pdoo11->prepare($sql);
		$pdopg = \DB::connection("videotest")->getPdo();
		$sql="delete from pid_video_settings where id=?";
		$sthd=$pdopg->prepare($sql);
	    $sql="select * from pid_video_settings order by name";
		$links=$pdopg->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
		foreach($links as $link){
			$widget=\DB::connection()->table('widgets')->where('id', $link["wid_id"])->first();
			if(!$widget){
				$sthd->execute([$link["id"]]);
			    var_dump($link["name"]);
				continue;
			}
			$videowidget=\DB::connection()->table('widget_videos')->where('wid_id', $widget->id)->first();
				if(!$videowidget){
					var_dump($widget);
			    }
			
			$partner=\DB::connection()->table('partner_pads')->where('id', $widget->pad)->first();
			if(!$partner){
			echo $link["name"]." > ".$widget->pad."\n";
			exit();
			}
			if($link["name"]!=$partner->domain){
			
			
			$namepartner=\DB::connection()->table('partner_pads')->where('domain', $link["name"])->first();
			if(!$namepartner){
				
				$videowidget=\DB::connection()->table('widget_videos')->where('wid_id', $widget->id)->first();
				if($videowidget){
				
					\DB::connection()->table('widget_videos')->where('wid_id', $widget->id)->delete();	
				}
			$sthd->execute([$link["id"]]);	
			//\DB::connection()->table('widgets')->where('id', $link["wid_id"])->delete();
			//$widget->delete();	
			//var_dump($videowidget);
			
				
			}else{
				if($widget->id==5 || $widget->id==3){
					
					
				}else{
					//echo $link["name"]." > ".$widget->pad."  ".$widget->id."/".$link["wid_id"]." > ".$partner->domain." ".$namepartner->id." /".$namepartner->domain." \n";
				    ///$sthuu->execute([178,105]);
					//$sthuu->execute([178,111]);
					//$sthuu->execute([178,110]);
					//$sthuu->execute([178,107]);
					//$sthuu->execute([178,106]);
  
				 echo $link["name"]." > ".$widget->pad."  ".$widget->id."/".$link["wid_id"]." > ".$partner->domain." ".$namepartner->id." /".$namepartner->domain." \n";	
				 echo $namepartner->id." / ".$widget->id." / \n";
				 //\DB::connection()->table('widgets')->where('id', $link["wid_id"])->update(["pad"=>$namepartner->id]);
					
				}
				
			}
			}
			
			//var_dump($widget["pad"]);
			
			
			
		}
		
	}
		
public function getException(){
	return $this->getCategory();
	$pdopg = \DB::connection("videotest")->getPdo();
	$sql="select * from   links ";
	$links=$pdopg->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
	$sql="insert into exception (pid,id_src)
	select ?,?
	WHERE NOT EXISTS (SELECT 1 FROM exception WHERE pid=? and id_src =?)
	";
	$sthInsertPids=$pdopg->prepare($sql);
	$lz=[];
	foreach($links as $obj)
	$lz[$obj["id"]]=$obj["title"];
	
	
	$sql="select * from   pid_video_settings ";
	$pids=$pdopg->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
	$pz=[];
	foreach($pids as $obj)
	$pz[$obj["pid"]]=$obj["name"];
	
	
	$config=[
	3 => 13,
4 => 48,
5 => 6,
6 => 19,
7 => 11 ,
9 => 10,
12 => 16,
18 => 28,	
23 =>19,
36  => 14,
40 => 33,
42 => 32,
43 => 33,
44 => 15,
54 => 1,
71 => 41,
76 => 9,
81 => 12,
84 => 50,
85 => 44,
86 => 45,
90 => 8,
91 => 16,
95 =>4,
58 =>-1,
72 =>-1,
100=>3,
101=>7,
103=>47
	];
	 $pdo = \DB::connection("mysqlapi")->getPdo();
	$sql="SELECT * FROM `video_exceptions` order by video_widget_id , id_src";
	 $res=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
	 foreach($res as $r){
		 	 if(!isset($config[$r["id_src"]])){
				//  echo $r["id_src"]."   уйди говно\n";
				  continue;
			 }
	 if(!isset($pz[$r["video_widget_id"]])){
		  // echo $r["video_widget_id"]."   уйди говнопид\n";
				  continue;
		 
	 }
		 //if(1==1  $r["video_widget_id"]==416 && $config[$r["id_src"]] >0){
			 if($config[$r["id_src"]] >0){
 //echo $r["video_widget_id"]."   ".$r["id_src"]." ".$config[$r["id_src"]]." \n";	
     echo "".$pz[$r["video_widget_id"]]." ".$r["video_widget_id"]."   ".$r["id_src"]." ".$config[$r["id_src"]]." ".$lz[$config[$r["id_src"]]]."  \n";	 
		 }		 
		$sthInsertPids->execute([$r["video_widget_id"],$config[$r["id_src"]],$r["video_widget_id"],$config[$r["id_src"]]]); 
	 }
	
	 
	 
	
	
}	 
  private function  getWidgets(){
	  $pdoo = \DB::connection()->getPdo(); 
 $sql="delete from widgets where id > 723";
 $pdoo->exec($sql);
 $sql="delete from widget_videos where type=2";
  $pdoo->exec($sql);
	 
	//  return;
	print "Новые виджеты здравствуйте\n";
	$sql="select * from   partner_pads  where domain ~*  ? ";
    $pdoi = \DB::connection()->getPdo();
    $sthi=$pdoi->prepare($sql);
    $pdoo = \DB::connection()->getPdo(); 
    //$sql="delete from widgets where id > 723";
    //$pdoo->exec($sql);
   // $sql="delete from widget_videos where wid_id > 723";
    //$pdoo->exec($sql);
	  $ustril=[42=>6,36=>11,20=>10,1=>7,26=>15,32=>12,10=>8,43=>13];	
		$pdopg=\DB::connection("videotest")->getPdo();
        $sql="insert into pid_video_settings  (pid,width,height,control,id_block_ru,id_block_cis,summa_ru,summa_cis,name)
		select ?,?,?,?,?,?,?,?,?
				WHERE NOT EXISTS (SELECT 1 FROM pid_video_settings WHERE pid=?)
		";
		$sthInsertPids=$pdopg->prepare($sql);
		 $sql="update  pid_video_settings set id_block_ru=?,id_block_cis=?,control=?
		 WHERE pid=?
		";
		$sthUpdatePids=$pdopg->prepare($sql);
	      $pdo = \DB::connection("mysqlapi")->getPdo();
		  $comissions=[];
		  $sql="SELECT * FROM `partner_commision_group`";
		   $res=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
		  foreach($res as $rw){
		
		  $comissions[$rw["commissiongroupid"]]=$rw["value"];
		  }
		  		  $sql="select t.*,v.*,p.domain from video_widgets t 
left join videoptions v 
on  v.id_video_widget =t.id and v.name in ('height','width','adslimit')
inner join partner_pads p on p.id=t.pad_id
where t.type=3";
$rui=[];
$errorCache=[];
          $res=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
		  foreach($res as $rw){
			  		  if(!isset($comissions[$rw["commission_group"]])){
		 // print  $rw["id"]." :::: ".$rw["control"]." : ".$rw["commission_group"]." : ".$rw["f_commission_group"]." RU\n";
		   continue;
		  }
		  if(!isset($comissions[$rw["f_commission_group"]])){
		  //print  $rw["id"]." : ".$rw["control"]." : ".$rw["commission_group"]." : ".$rw["f_commission_group"]." CIS\n";
		  continue;
		  }
			 //var_dump($rw);
		  $rui[$rw["id"]]["control"] =$rw["control"];
		  $rui[$rw["id"]]["domain"] =$rw["domain"];
		  $rui[$rw["id"]]["summa_ru"]=$comissions[$rw["commission_group"]];
		  $rui[$rw["id"]]["summa_cis"]=$comissions[$rw["f_commission_group"]];
		  if(isset($ustril[$rw["settings_id"]])){
		  $rui[$rw["id"]]["id_block_ru"]=$ustril[$rw["settings_id"]];
		  }else{
		  $rui[$rw["id"]]["id_block_ru"]=0;
		  if(!isset($errorCache[$rw["settings_id"]])){
		  $errorCache[$rw["settings_id"]]=1;
		  }
		  }
		   if(isset($ustril[$rw["foreign_settings_id"]])){
		   $rui[$rw["id"]]["id_block_cis"]=$ustril[$rw["foreign_settings_id"]];
		  }else{
		  $rui[$rw["id"]]["id_block_cis"]=0;
		  if(!isset($errorCache[$rw["foreign_settings_id"]])){
		  $errorCache[$rw["foreign_settings_id"]]=1;
		  }
		  }
		  
		  }
		  foreach($rui as $id=>$data){
		  if($data["id_block_cis"]==0 && $data["id_block_ru"]==0) continue;
		  //var_dump([$id,$data]);
		 //$wid=\App\MPW\Widgets\Video::firstOrNew(['id'=>$id]);
		if(!isset($data["width"])) $data["width"]=550;
		if(!isset($data["height"])) $data["height"]=350;
		if(!isset($data["control"])) $data["control"]=0;
		if(!$data["control"]) $data["control"]=0; 
		 // $wid=new \App\MPW\Widgets\Widget;
		  //$wid->type=2;
		  //$wid->status=0;
		  $sthi->execute(['^'.$data["domain"].'$']);
		  $re=$sthi->fetchAll(\PDO::FETCH_ASSOC);	
		  if(count($re)!=1){
		     continue;
		  }
		  
		 
		  
		    $vwid=\App\WidgetVideo::firstOrNew(['id'=>$id]);
			if($vwid->wid_id) continue;
			 $wid=new \App\MPW\Widgets\Widget;
		     $wid->type=2;
		     $wid->status=0;
			 $vwid->id=$id;
			 var_dump($vwid->id);
			 $wid->user_id=$re[0]["user_id"];
		     $wid->pad=$re[0]["id"];
		     $wid->save();
			 
		  $vwid->wid_id=$wid->id;

		  
		  
		  $vwid->type=2;
		  $vwid->width=$data["width"];
		  $vwid->height=$data["height"];
		  $vwid->on_rus=1;
		  $vwid->on_cis=1;
		  $vwid->on_mobil=1;
		 
		  $vwid->block_rus=$data["id_block_ru"];
		  $vwid->block_cis = $data["id_block_cis"];
		  $vwid->block_mobil=1;
		  $vwid->commission_rus=$data["summa_ru"];
		  $vwid->commission_cis=$data["summa_cis"];	
		  $vwid->save();
		  $sthUpdatePids->execute([$data["id_block_ru"],$data["id_block_cis"],$data["control"],$id]);
	      $sthInsertPids->execute([$id,$data["width"],$data["height"],$data["control"],$data["id_block_ru"],$data["id_block_cis"],$data["summa_ru"],$data["summa_cis"],$data["domain"],$id]);
			// var_dump($vwid->wid_id);
		  }
		  
    }
	public function getWiPad(){
		
		
		$sql="select * from   partner_pads  where domain ~*  ? ";
        $pdoi = \DB::connection()->getPdo();
       


        $sthi=$pdoi->prepare($sql);
		  $pdo = \DB::connection("mysqlapi")->getPdo();
		  $pdopga = \DB::connection("api_market")->getPdo();
		  $sql="select * from mp_template_attributes where id_widget=?";
		  $gestColls=$pdopga->prepare($sql);
		  $sql="select rt.* from mp_widgets r
inner join mp_widget_templates rt
on rt.id=r.mobile_template_id where r.id=?";
		    $gestMobile=$pdopga->prepare($sql);
		  $sql="select * from mp_widgets where id=2325";
          $widd=[];
          $res=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);	
		  foreach($res as $rw){
			 
			  $wi=\App\WidgetEditor::firstOrNew(['id'=>$rw->id]);
			  #$rw->template_code="1:1:0";
			   #var_dump($rw); die();
			  if(1==0 && preg_match('/^(0|\-1)/',$rw->template_code)){
				
     			 //echo $rw->template_code."\n"; 
			  }else{
				  
				  $tmp1=explode(":",$rw->template_code);
				switch($tmp1[0]){
					  case -1:
					  break;
					  case 0:
					  // echo $rw->template_code."\n";   
					  break;
					  case 1:
					    	switch($tmp1[1]){
								case -1:
								
								break;
								case 0: //слайдер
								$wi->type=1;
								$wi->name="block-mono";
								  /*switch($tmp1[2]){  
								  case 0:
								  break;
								  case 1:
								  break;
								  case 2:
								  break;
								    
								  }*/
								break;
								case 1: // таблица
								$wi->type=3;
								switch($tmp1[2]){  
								  case 3:
								  case 0:
								  $wi->name="table";
								  break;
								  case 1:
								  $wi->name="table-no-foto";
								  break;
								  case 2:
								   $wi->name="table-mini";
								  break;
								 // case 3:
								  //break;		
								  default:
								   echo $rw->template_code."\n";   
								  break;									  
								  }
								
								break;		
								case 2: // модульный
								$wi->type=2;
								switch($tmp1[2]){  
								 case 0:
								 $wi->name="module-block-third-1";
								 break;
								 case 7:
								 case 8:
								 case 9:
								 case -1:
								 case 1:
								 $wi->name="module-block";
								 break;
								 case 2:
								 $wi->name="module-block-third";
								 break;
								 case 3:
								  $wi->name="module-block-tetra";
								 break;
								 case 4:
								  $wi->name="module-block-new_widget";
								 break;
								 case 5:
								  $wi->name="module-block-fullo";
								 break;
								 case 6:
								  $wi->name="module-block-yandex_left";
								 break;
															 
								 default:
								 echo $rw->template_code."\n";   
								 break;
								} 
								break;								
							}								
					
					  break;
					  default:
					 echo $rw->template_code."\n";   
					  break;
				  }
				  if(!$wi->name) continue;
				  //print $home."\n";
				  $templateData=$this->getTemplateParams($rw->template,[$rw->affiliate_id,$rw->inner_id]);
				  if(!$templateData){
					 // $templateData
				  continue;
				  }else{
					  $wi->id=$rw->id;
					  
					  $wi->width=$templateData["width"];
					  $wi->height=$templateData["height"];
					  $wi->background=$templateData["backgroundColor"];
					  $wi->border_color=$templateData["borderColor"];
					  $wi->border_width=$templateData["borderWidth"];
                      $wi->mobile = $rw->enabled_mob;
					  $wi->border_radius=0;
					   $gestMobile->execute([$wi->id])	;
					   $vdudu=$gestMobile->fetch(\PDO::FETCH_ASSOC);
					   if($vdudu){
						    $wi->name_mobile=$vdudu["name"];
						    //var_dump($vdudu["name"]);   
						   
					   }
					  
					  #var_dump([$wi]);  
					  $gestColls->execute([$wi->id])	;
					  $vokno=$gestColls->fetchAll(\PDO::FETCH_ASSOC);
					  if(!$vokno){
					    $wi->cols=1;
						$wi->row=1;
					  }else{
						  foreach($vokno as $vok){
							  switch($vok["name"]){
								case "cols":
								$wi->cols=intval($vok["value"]);
								#echo $vok["name"]." : ".$vok["value"]."\n";
                                break;								
								case "rows":
								$wi->row=intval($vok["value"]);
								#echo $vok["name"]." : ".$vok["value"]."\n";
                                break;								  
							  }
							  //echo $vok["name"]." : ".$vok["value"]."\n";
							  #var_dump();
						  }
						  if(!$wi->cols)
							    $wi->cols=1;
						  if(!$wi->row)
						     $wi->row=1;	
						  #var_dump([$wi]);  
					  }
					  
				  }
			  }
			   if(!$wi->wid_id){
				   
				    
				
				    $sthi->execute(['^'.$rw->url.'$']);
		  $reha=$sthi->fetchAll(\PDO::FETCH_ASSOC);	
		  
		  if(count($reha)!=1){
			 
			 
		     continue;
		  }
		           $widL=new \App\MPW\Widgets\Product; 
				    $widL->pad=$reha[0]["id"];
		            $widL->user_id=$reha[0]["user_id"];
					$widL->save();
		            var_dump([$reha[0]["user_id"]]);
				    $wi->wid_id=$widL->id;  
				   
			   }
			  $wi->save();
			 
				  //var_dump($wi->wid_id);
				  
			  //echo $rw->id." / ".$rw->domain."\n";
			 // $widd[$rw->pad_id][$rw->id]=1;
			  //$domains[$rw->pad_id]=$rw->domain;
			 
		  }

	}
	public function getTemplateParams($tpl,$params){
		$home="/home/www/api.market-place.su/templates/widget_templates/widget-".$tpl."/css";
		if(!is_dir($home)){
		return null;
		
		
		}
		$width=null;
		$height=null;
		$borderColor=null;
		$backgroundColor=null;
		$borderWidth=null;
		$files1 = scandir($home);
		 $cssParser=new \App\CSSparser();
		foreach($files1 as $f){
			if(preg_match('/tpl_defaults\.css$/i',$f)){
			//	if(preg_match('/\.css$/i',$f)){
			//echo $home."/".$f."\n";
		//var_dump($cssParser);
		 $co=file_get_contents($home."/".$f);
		 $cssIndex =  $cssParser->ParseCSS($co);
		 $cssArr=$cssParser->GetCSSArray($cssIndex);
		 foreach($cssArr as $key=>$arr){
			 foreach($arr as $k=>$a){
				 
				
				 switch($k){
					 case "width":
				  case "max-width":
				  
				   $arve=preg_split("/\s+/",trim($a));
				 # echo $k." : ".$arve[0]."\n";
				  if($arve[0]=="100%"){
					 $width=0; 
				  }else{
				  $width=intval($arve[0]);	  
				  }
				   break;
				  case "max-height":
				  case "height":
				   $arve=preg_split("/\s+/",trim($a));
				  #echo $k." : ".$arve[0]."\n";
				   if($arve[0]=="100%"){
					 $height=0; 
				  }else{
				   $height=intval($arve[0]);	  
				  }
				   break;
				  case "border-color": 
				  $arve=preg_split("/\s+/",trim($a));
				  $rgba=$this->hex2rgba($arve[0],1);
				  $borderColor=$rgba;
				  #echo $k." : ".$arve[0]." / $rgba\n";
				  
				   break;
				  case "background-color": 
				  #echo $k." : ".$a."\n";
				  $arve=preg_split("/\s+/",trim($a));
				  $rgba=$this->hex2rgba($arve[0],1);
				  $backgroundColor=$rgba;
				   break;
				  case "border-width": 
				  #echo $k." : ".$a."\n";
				   $arve=preg_split("/\s+/",trim($a));
				   $borderWidth=intval($arve[0]);	  
                  break;
				 
				 }
				 
			 }
			 
		 }
		    //var_dump($cssArr);
			//var_dump(array_keys($cssArr));
			//echo "\n";
			}
		}
		if($width===null || $height===null){
			#echo $home."\n";
			return null;
		}
		if($borderColor===null){
			echo $home."\n";
			return null;
		}
		if($backgroundColor===null){
			echo $home."\n";
			return null;
		}
		if($borderWidth===null){
			echo $home."\n";
			return null;
		}
        $cssParser=new \App\CSSparser();
		$home="/home/www/api.market-place.su/templates/widget_templates/widget-".$tpl."/custom/widget_".$params[0]."_wid".$params[1]."/css/user_settings.css";
		if(!is_file($home)){
			 
		#print $home."\n";	
		return null;
		
		
		}else{
			#print $home."\n";	
		 $co=file_get_contents($home);
		 $cssIndex =  $cssParser->ParseCSS($co);
		 $cssArr=$cssParser->GetCSSArray($cssIndex);
		 foreach($cssArr as $key=>$arr){
			 $pos = preg_match('/\.'.$tpl.'(\Z|\s+\.widget\Z)/',$key);
			 if($pos){
			# print $key."| .$tpl\n";
			 	 foreach($arr as $k=>$a){
					  //echo $k." : ".$a."\n";
				 switch($k){
					 case "width":
				  case "max-width":
				  
				   $arve=preg_split("/\s+/",trim($a));
				 #echo $k." : ".$arve[0]."\n";
				  if($arve[0]=="100%"){
					 $width=0; 
				  }else{
				  $width=intval($arve[0]);	  
				  }
				   break;
				  case "max-height":
				  case "height":
				   $arve=preg_split("/\s+/",trim($a));
				  #echo $k." : ".$arve[0]."\n";
				   if($arve[0]=="100%"){
					 $height=0; 
				  }else{
				   $height=intval($arve[0]);	  
				  }
				   break;
				  case "border-color": 
				  $arve=preg_split("/\s+/",trim($a));
				  $rgba=$this->hex2rgba($arve[0],1);
				  $borderColor=$rgba;
				  #echo $k." : ".$arve[0]." / $rgba\n";
				  
				   break;
				  case "background-color": 
				  #echo $k." : ".$a."\n";
				  $arve=preg_split("/\s+/",trim($a));
				  $rgba=$this->hex2rgba($arve[0],1);
				  $backgroundColor=$rgba;
				   #echo $k." : ".$arve[0]." / $rgba\n";
				   break;
				  case "border-width": 
				  #echo $k." : ".$a."\n";
				   $arve=preg_split("/\s+/",trim($a));
				   $borderWidth=intval($arve[0]);	  
                  break;
				  default:
				   #echo $k." : ".$a."\n";
				  break;
				 }

					  }
			 #var_dump($arr);
			 }
			
		 }
			
			
		}
			#echo "-->\n\n";
		#echo $width." : ".$height." : $borderColor : $backgroundColor : $borderWidth\n";
		return [
		"width"=>$width,
		"height"=>$height,
		"borderColor"=>$borderColor,
		"backgroundColor"=>$backgroundColor,
		"borderWidth"=>$borderWidth
		];
		#$cmd="ls -la $home ";
		#echo $cmd."\n";; 
		#`$cmd`;
	}
    public function handle()
    {
		$this->getWiPad();
		//return $this->getException();
	// $this->getPadsId();
		echo "ничего нельзя запускать\n";
		exit();
		
	//exit();	
	//return $this->getWidgetId();
	//return ;
	//return $this->getException();
	$sql="select * from   partner_pads  where domain ~*  ? ";
    $pdoi = \DB::connection()->getPdo();



$sthi=$pdoi->prepare($sql);
 $pdoo = \DB::connection()->getPdo(); 
 $sql="delete from widgets where id > 723";
 $pdoo->exec($sql);
 $sql="delete from widget_videos where wid_id > 723";
  $pdoo->exec($sql);
 
/*
$sql="trancate table widgets";
$sthi=$pdoi->exec($sql);
$sql="trancate table widget_videos";
$sthi=$pdoi->exec($sql);
$sql="trancate table widget_products";
*/
         $ustril=[42=>6,36=>11,20=>10,1=>7,26=>15,32=>12,10=>8,43=>13];	
		$pdopg=\DB::connection("videotest")->getPdo();
        $sql="insert into pid_video_settings  (pid,width,height,control,id_block_ru,id_block_cis,summa_ru,summa_cis,name)
		select ?,?,?,?,?,?,?,?,?
				WHERE NOT EXISTS (SELECT 1 FROM pid_video_settings WHERE pid=?)
		";
		$sthInsertPids=$pdopg->prepare($sql);
		 $sql="update  pid_video_settings set id_block_ru=?,id_block_cis=?,control=?
		 WHERE pid=?
		";
		$sthUpdatePids=$pdopg->prepare($sql);
		  $pdo = \DB::connection("mysqlapi")->getPdo();
		  $comissions=[];
		  $sql="SELECT * FROM `partner_commision_group`";
		   $res=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
		  foreach($res as $rw){
		 
		  $comissions[$rw["commissiongroupid"]]=$rw["value"];
		  }
		
		  $sql="select t.*,v.*,p.domain from video_widgets t 
left join videoptions v 
on  v.id_video_widget =t.id and v.name in ('height','width','adslimit')
inner join partner_pads p on p.id=t.pad_id
where t.id not in (4,5,6,192,175,701,272,300) and t.type=1";

		
		  $sql="select t.*,v.*,p.domain from video_widgets t 
left join videoptions v 
on  v.id_video_widget =t.id and v.name in ('height','width','adslimit')
inner join partner_pads p on p.id=t.pad_id
where t.type=3";

          $res=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);	
		  foreach($res as $rw){
		 
		  if(!isset($comissions[$rw["commission_group"]])){
		  print  $rw["id"]." :::: ".$rw["control"]." : ".$rw["commission_group"]." : ".$rw["f_commission_group"]." RU\n";
		   continue;
		  }
		  if(!isset($comissions[$rw["f_commission_group"]])){
		  print  $rw["id"]." : ".$rw["control"]." : ".$rw["commission_group"]." : ".$rw["f_commission_group"]." CIS\n";
		  continue;
		  }
		  if($rw["name"])
		  $rui[$rw["id"]][$rw["name"]]=$rw["value"];

		  $rui[$rw["id"]]["control"] =$rw["control"];
		  $rui[$rw["id"]]["domain"] =$rw["domain"];
		  $rui[$rw["id"]]["summa_ru"]=$comissions[$rw["commission_group"]];
		  $rui[$rw["id"]]["summa_cis"]=$comissions[$rw["f_commission_group"]];

		  //var_dump($rw);
		  if(isset($ustril[$rw["settings_id"]])){
		  $rui[$rw["id"]]["id_block_ru"]=$ustril[$rw["settings_id"]];
		  }else{
		  $rui[$rw["id"]]["id_block_ru"]=0;
		  if(!isset($errorCache[$rw["settings_id"]])){
		  
		  $errorCache[$rw["settings_id"]]=1;
		  }
		  
		 // print $rw["settings_id"]." ".$rw["domain"]." ".$rw["pad_id"]."\n";
		  //continue;
		 // print  $rw["id"]." : ".$rw["settings_id"]." : ".$rw["foreign_settings_id"]." \n";
		  }
		   if(isset($ustril[$rw["foreign_settings_id"]])){
		   $rui[$rw["id"]]["id_block_cis"]=$ustril[$rw["foreign_settings_id"]];
		  }else{
		  $rui[$rw["id"]]["id_block_cis"]=0;
		  if(!isset($errorCache[$rw["foreign_settings_id"]])){
		  //print $rw["foreign_settings_id"]."\n";
		  $errorCache[$rw["foreign_settings_id"]]=1;
		  }
		  //print $rw["foreign_settings_id"]." ".$rw["domain"]."\n";
		  //continue;
		 // print  $rw["id"]." : ".$rw["settings_id"]." : ".$rw["foreign_settings_id"]." \n";
		  }
		  
		  //print $rw["settings_id"]." ".$rw["domain"]." ".$rw["pad_id"]."\n";
		  }
		  foreach($rui as $id=>$data){
		  if($data["id_block_cis"]==0 && $data["id_block_ru"]==0) continue;
		  //var_dump([$id,$data]);
		 //$wid=\App\MPW\Widgets\Video::firstOrNew(['id'=>$id]);
		if(!isset($data["width"])) $data["width"]=550;
		if(!isset($data["height"])) $data["height"]=350;
		if(!isset($data["control"])) $data["control"]=0;
		if(!$data["control"]) $data["control"]=0;
		  $wid=new \App\MPW\Widgets\Widget;
		  $wid->type=2;
		  $wid->status=0;
		  $sthi->execute(['^'.$data["domain"].'$']);
		  $re=$sthi->fetchAll(\PDO::FETCH_ASSOC);	
		  if(count($re)!=1){
		     continue;
		  }
		 
		  continue;
		  $wid->user_id=$re[0]["user_id"];
		  $wid->pad=$re[0]["id"];
		  $wid->save();
		  $vwid=\App\WidgetVideo::firstOrNew(['id'=>$id]);
		  $vwid->id=$id;
          echo  $vwid->id."\n";
		  $vwid->wid_id=$wid->id;

		  
		  
		  $vwid->type=1;
		  $vwid->width=$data["width"];
		  $vwid->height=$data["height"];
		  $vwid->on_rus=1;
		  $vwid->on_cis=1;
		  $vwid->on_mobil=1;
		 
		  $vwid->block_rus=$data["id_block_ru"];
		  $vwid->block_cis = $data["id_block_cis"];
		  $vwid->block_mobil=1;
		  $vwid->commission_rus=$data["summa_ru"];
		  $vwid->commission_cis=$data["summa_cis"];	
		  $vwid->save();
		  continue;

		var_dump($data);
		continue;
	          $vwid->wid_id=$id;
		  $vwid->type=1;
		  $vwid->width=$data["width"];
		  $vwid->height=$data["height"];
		  $vwid->on_rus=1;
		  $vwid->on_cis=1;
		  $vwid->on_mobil=1;
		  $vwid->block_rus=$data["id_block_ru"];
		  $vwid->block_cis = $data["id_block_cis"];
		  $vwid->block_mobil=1;
		  $vwid->commission_rus=$data["summa_ru"];
		  $vwid->commission_cis=$data["summa_cis"];	
		  $vwid->save();
		$sthUpdatePids->execute([$data["id_block_ru"],$data["id_block_cis"],$data["control"],$id]);
	    $sthInsertPids->execute([$id,$data["width"],$data["height"],$data["control"],$data["id_block_ru"],$data["id_block_cis"],$data["summa_ru"],$data["summa_cis"],$data["domain"],$id]);
		  }
		  return;
		  $wid=\App\MPW\Widgets\Video::firstOrNew(['id'=>6]);
		  $wid->id=6;
		  $wid->type=2;
		  $wid->user_id=416;
		  $wid->pad=423; 
		  $wid->save();
		  $vwid=\App\WidgetVideo::firstOrNew(['wid_id'=>6]);
		  $vwid->wid_id=6;
		  $vwid->type=1;
		  $vwid->width=580;
		  $vwid->height=340;
		  $vwid->on_rus=1;
		  $vwid->on_cis=1;
		  $vwid->on_mobil=1;
		  $vwid->block_rus=6;
		  $vwid->block_cis = 7;
		  $vwid->block_mobil=1;
		  $vwid->commission_rus=80;
		  $vwid->commission_cis=20; 
		  $vwid->save();
           
          $wid=\App\MPW\Widgets\Video::firstOrNew(['id'=>4]);
		  $wid->id=4;
		  $wid->type=2;
		  $wid->user_id=43;
		  $wid->pad=81; 
		  $wid->save();
		  $vwid=\App\WidgetVideo::firstOrNew(['wid_id'=>4]);
		  $vwid->wid_id=4;
		  $vwid->type=1;
		  $vwid->width=550;
		  $vwid->height=350;
		  $vwid->on_rus=1;
		  $vwid->on_cis=1;
		  $vwid->on_mobil=1;
		  $vwid->block_rus=1;
		  $vwid->block_cis = 2;
		  $vwid->block_mobil=1;
		  $vwid->commission_rus=90;
		  $vwid->commission_cis=20; 
		  $vwid->save();		   
		  
		  
    }
function hex2rgba($color, $opacity = false) {
 
	$default = 'rgb(0,0,0)';
 
	//Return default if no color provided
	if(empty($color))
          return $default; 
 
	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }
 
        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }
 
        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);
 
        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }
 
        //Return rgb(a) color string
        return $output;
   }	
}
