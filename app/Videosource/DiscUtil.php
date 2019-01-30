<?php

namespace App\Videosource;

use Illuminate\Support\Str;
class DiscUtil 
{
    public static function Utile($pid=null){
		if(1==0 && $pid){
		             $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/vvv/".$pid.".json";
	                `$cmd`;
	                 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/vvv/sng/".$pid.".json";
	                `$cmd`;
	                 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/vvv/msk/".$pid.".json";
	                `$cmd`;
					
					 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/videovast/".$pid.".xml";
	                `$cmd`;
	                 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/videovast/sng/".$pid.".xml";
	                `$cmd`;
	                 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/videovast/msk/".$pid.".xml";
	                `$cmd`;
		}else{
					 $cmd = 'find /home/mp.su/widget.market-place.su/public/vvv/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
					 $cmd = 'find /home/mp.su/widget.market-place.su/public/vvv/sng/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
					 
				//echo $cmd ; die();
					 $cmd = 'find  /home/mp.su/widget.market-place.su/public/vvv/msk/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
	                                 $cmd = 'find  /home/mp.su/widget.market-place.su/public/videovast/ -maxdepth 1 -mindepth 1 -type f -name "*.xml" -delete';
	                `$cmd`;
	                                 $cmd = 'find  /home/mp.su/widget.market-place.su/public/videovast/sng/ -maxdepth 1 -mindepth 1 -type f -name "*.xml" -delete';
	                `$cmd`;
			                 $cmd = 'find  /home/mp.su/widget.market-place.su/public/videovast/msk/ -maxdepth 1 -mindepth 1 -type f -name "*.xml" -delete';
	                `$cmd`;
					/*новый прехороший загрузчик ссылок*/

					 $cmd = 'find  /home/mp.su/dest.market-place.su/version/sng/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
					 $cmd = 'find  /home/mp.su/dest.market-place.su/version2/sng/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
					 $cmd = 'find  /home/mp.su/dest.market-place.su/version/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
					 $cmd = 'find  /home/mp.su/dest.market-place.su/version2/  -maxdepth 1 -mindepth 1 -type f -name "*.json" -delete';
	                `$cmd`;
					 $cmd = 'find  /home/mp.su/dest.market-place.su/c202/  -maxdepth 1 -mindepth 1 -type f -name "*.xml" -delete';
	                `$cmd`;
                        


		}
		
	}
	
	public static function BrandUtile($pid=null){
		if(1==0 && $pid){
		             $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/brand_widget/".$pid.".json";
	                `$cmd`;
					 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/brand_widget/sng/".$pid.".json";
	                `$cmd`;
	                 
		}else{
					 $cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/brand_widget/*.json";
	                `$cmd`;
					$cmd = "rm -rf  /home/mp.su/widget.market-place.su/public/brand_widget/sng/*.json";
	                `$cmd`;
		}
		
	}
	
public static function createVideoSettings($wid,$parameters,$exceptions){
	        $widget=\App\MPW\Widgets\Widget::find($wid->wid_id);
			
			if(!$widget) die();
			if ($widget->status==1) die();
			$pdo = \DB::connection()->getPdo();
            $sql="select t3.domain,t3.video_categories from widgets t1 
		   left join partner_pads
		   t3 on t1.pad=t3.id 
		   where t1.id=".$wid->wid_id."
           ";
		   $domain=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		  
		   //$mysettings = \App\MPW\Widgets\VideoSettings::firstOrNew(['wid_id'=>$wid->wid_id]);
		   //$mysettings["pid"]=$wid->id;
		//if($domain){
		//if($domain["domain"])
		//$mysettings["name"]=$domain["domain"];
	    //if($domain["video_categories"]!==null)
			#$widget->block_rus=$mysettings["id_block_ru"];
			#$widget->block_cis=$mysettings["id_block_cis"];
			#$widget->commission_rus=$mysettings["id_block_ru"];
			#$widget->commission_cis=$mysettings["id_block_cis"];
			#$mysettings["video_category"]=$domain["video_categories"];
			if($domain){
			$wid->video_category=$domain["video_categories"];
			}
		//else
		//	return;
	    
		//}
		
	    # return;
		$userdata=\DB::table("video_default_on_users as vd")
		->select("vd.commission_cis","vd.commission_rus","c_ru.value as ru_value","c_cis.value as cis_value")
        ->join("сommission_groups as c_ru","c_ru.commissiongroupid","=","vd.commission_rus")
	    ->join("сommission_groups as c_cis","c_cis.commissiongroupid","=","vd.commission_cis")
		->where("wid_type",$wid->type)
		->where("pad_type",$wid->video_category)
		->where("user_id","=",$widget->user_id)
		->first();
		

		
		if($userdata){
			$wid->commission_rus=$userdata->commission_rus;
			$wid->commission_cis=$userdata->commission_cis;
			#$mysettings["comission_rus"]=
			#$mysettings["comission_cis"]=$userdata->commission_cis;
			
		}
		

		$defaultdata=\DB::table("video_defaults as vd")
		->select("vd.width","vd.height","vd.commission_cis","vd.commission_rus","c_ru.value as ru_value","c_cis.value as cis_value","vd.block_rus","vd.block_cis","vd.block_mobile")
		->join("сommission_groups as c_ru","c_ru.commissiongroupid","=","vd.commission_rus")
	    ->join("сommission_groups as c_cis","c_cis.commissiongroupid","=","vd.commission_cis")
		->where("wid_type",$wid->type)
		->where("pad_type",$wid->video_category)
		->first();
		
		if(!$defaultdata) return;
		$wid->block_rus=$defaultdata->block_rus;
		$wid->block_cis=$defaultdata->block_cis;
		if(!$userdata){
		$wid->commission_rus=$defaultdata->commission_rus;
		$wid->commission_cis=$defaultdata->commission_cis;
		}
		$wid->width=$defaultdata->width;
		$wid->height=$defaultdata->height;
		$wid->save();

			
}	
public static function VideoVastUnder($pid){
     function checkBrokendir($pid){
        $comp=1;
        $dir="/home/mp.su/dest.market-place.su/rank/$pid";
        $tru=is_dir($dir);
        if(!$tru){
        $comp=mkdir($dir,0775);
        $tru=is_dir($dir);
        }
        $ofile ="/home/mp.su/dest.market-place.su/lib/overplay/autovast-min.js";
        $ifile=$dir."/autovast-min.js";
        if(1==1 || !is_file($ifile)){
            if (!copy($ofile,$ifile)) {
                echo "не удалось скопировать $ifile...\n"; exit();
            }
        }
	return 'https://info.kinoclub77.ru/c202/'.$pid.'.xml';
     }
     switch($pid){
       case 1975:
       case 1976:
       break;
       default ;
#       return '';
       break;
      }
return checkBrokendir($pid);

//var_dump($pid);
//die();
//     return checkBrokendir($pid);
}

public static function VideoVast($pid){
                       $str=self::VideoVastUnder($pid);
                 if($str) {
                  return $str;
                 }
		return 'https://widget.market-place.su/videovast/'.$pid.'.xml';
  
    }
public static function VideoAutoplay($pid){

         if($pid==1849){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,fly:0,autoplay:1});</script>
               ';

         }


         if($pid==1729){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:0,fly:3,autoplay:1});</script>
               ';

         }


         if($pid>1707){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:0,autoplay:1});</script>
               ';
              }

        
		return '<div id="mp_wid_'.$pid.'"></div>
		<script type="text/javascript"  src="//video.market-place.su/v1/build/inline.js"></script>
		<script>
var cls=new myVastMultyClient();
cls.playAuto({container:"mp_wid_'.$pid.'",pid:'.$pid.'});
	</script>';
  
    }
public static function LazerAutoplayMuted($pid){
         return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/v1864/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1});</script>
               ';


}
public static function VideoAutoplayMutedUnder($pid){
     function checkBrokendir($pid){
        $comp=1;
        $dir="/home/mp.su/dest.market-place.su/rank/$pid";
        $tru=is_dir($dir);
        if(!$tru){
        $comp=mkdir($dir,0775);
        $tru=is_dir($dir);
        }
        $ofile ="/home/mp.su/dest.market-place.su/lib/overplay/fly-min.js";
        $ifile=$dir."/fly-min.js";
        if(1==1 || !is_file($ifile)){
            if (!copy($ofile,$ifile)) {
                echo "не удалось скопировать $ifile...\n"; exit();
            }
        }
        $wfile="https://info.kinoclub77.ru/rank/$pid/fly-min.js";
        return '
            <div id="mp_wid_'.$pid.'"></div>
            <script type="text/javascript"  src="'.$wfile.'"></script>
            <script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1});</script> 
        ';
     }
     return checkBrokendir($pid);

/*     switch($pid){
      case 1858:
      return checkBrokendir($pid);
      break;
     }
*/
     return '';
}
public static function VideoAutoplayMuted($pid){
         $str=self::VideoAutoplayMutedUnder($pid);
         if($str) return $str;
         $lazarew=[1864,1865,1866,1867,1868];
         if(in_array($pid,$lazarew))
         return self::LazerAutoplayMuted($pid);


         if($pid>1707){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/v4/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1});</script>
               ';
              }


     if($pid==1461 || $pid==1485 || $pid==1513 || $pid==1722 || $pid==1671){
     return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/v4/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1,fly:3,flymarge:[0,100]});</script>
        ';
     }
     if($pid==1461){
     return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/v4/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1,fly:3,flymarge:[0,100]});</script>
        ';
     }
    if($pid==1706){
      return '
     	  	  <div id="mp_wid_'.$pid.'"></div>
			  <script type="text/javascript"  src="https://eu.market-place.su/auto-min.js"></script>
		<script>
		CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1,fly:0});
	</script>

       ';
    }
    if($pid==1689){
      return '
     	  	  <div id="mp_wid_'.$pid.'"></div>
			  <script type="text/javascript"  src="https://eu.market-place.su/v4/fly-min.js"></script>
		<script>
		CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1,fly:4});
	</script>

       ';
    }
    if($pid==1655){
       return '<div id="mp_wid_'.$pid.'"></div>
<script type="text/javascript"  src="https://eu.market-place.su/auto-min.js"></script>
<script>
CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",autoplay:1,muted:1,limit:0});
</script>
       ';
}



		return '<div id="mp_wid_'.$pid.'"></div>
		<script type="text/javascript"  src="//video.market-place.su/v1/build/inlinem.js"></script>
		<script>
var cls=new myVastMultyClient();
cls.playAuto({container:"mp_wid_'.$pid.'",pid:'.$pid.'});
	</script>';
  
    }
	
public static function VideoFlyRoll($pid){
         if($pid==1724){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/v4/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:0,autoplay:1,fly:3});</script>
               ';
              }


             if($pid>1707){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:0,autoplay:1,flyonly:1,fly:3,flymarge:[0,0]});</script>
               ';
              }
        
	return '<script type="text/javascript"  src="//video.market-place.su/v1/build/infly.js"></script>
		<script>
var cls=new myVastMultyClient();
cls.playAuto({container:"mp_wid_'.$pid.'",pid:'.$pid.'});
	</script>';
  
    }

public static function VideoFlyRollMuted($pid){

              if($pid>1707){
               return '
        <div id="mp_wid_'.$pid.'"></div>
        <script type="text/javascript"  src="https://eu.market-place.su/fly-min.js"></script>
	<script>CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",limit:0,muted:1,autoplay:1,flyonly:1,fly:3,flymarge:[0,0]});</script>
               ';
              }

        
		return '<script type="text/javascript"  src="//video.market-place.su/v1/build/inflym.js"></script>
		<script>
var cls=new myVastMultyClient();
cls.playAuto({container:"mp_wid_'.$pid.'",pid:'.$pid.'});
	</script>';
  
    }
	
	public static function Brand($pid){
		
    return '<script>
	(function(){
        window.vrconf={pid:'.$pid.'};
        var s= document.createElement("script");
        s.src="//video.market-place.su/brand/build/brand.js";
        s.charset="UTF-8";
        document.body.appendChild(s);
    })();
	</script>';
	}
	
	public static function VideoInpage($pid){
	        return '
    	  	  <div id="mp_wid_'.$pid.'"></div>
		  <script type="text/javascript"  src="https://eu.market-place.su/test-min.js"></script>
		<script>
		CreateOverplayWidget({pid:"'.$pid.'",selector:"#mp_wid_'.$pid.'",inpage:1});
	</script>

';
		return '<div id="mp_wid_'.$pid.'"></div>
		<script type="text/javascript"  src="//video.market-place.su/v1/build/inpage.js"></script>
		<script>
var cls=new myVastMultyClient();
cls.playAuto({container:"mp_wid_'.$pid.'",pid:'.$pid.'});
	</script>';
  
    }

public static function VideoOverplayUnder($pid){
     function checkBrokendir($pid){
        $comp=1;
        $dir="/home/mp.su/dest.market-place.su/rank/$pid";
        $tru=is_dir($dir);
        if(!$tru){
        $comp=mkdir($dir,0775);
        $tru=is_dir($dir);
        }
        $ofile ="/home/mp.su/dest.market-place.su/lib/overplay/over-min.js";
        $ifile=$dir."/over-min.js";
        if(1==1 || !is_file($ifile)){
            if (!copy($ofile,$ifile)) {
                echo "не удалось скопировать $ifile...\n"; exit();
            }
        }
        $wfile="https://info.kinoclub77.ru/rank/$pid/over-min.js";
	return '<script type="text/javascript"  src="'.$wfile.'"></script>
        <script>CreateOverStopWidget({pid:"'.$pid.'",selector:"video",ttl:15,limit:0});</script> 
<!-- 
selector : css selector на контейнер с видео
ttl :  время ожидания рекламы  
-->
		';
     }
     return checkBrokendir($pid);
}


public static function VideoOverplay($pid){

              return self::VideoOverplayUnder($pid);

        $pdo = \DB::connection("videotest")->getPdo();
		
		 

                   
		return '<script type="text/javascript"  src="//video.market-place.su/v1/build/overplay.js"></script>
		<script>
        new  CreateOverplayWidget({pid:"'.$pid.'",selector:"video"});
	    </script>
		';
        return '';
    }  
public static function Cpa($pid,$cpa){
	    if(!$cpa){
		$cpa=Str::random(16);	
		$sql="update advertises set cpa_code='$cpa' where id=$pid";
		\DB::connection("advertise")->getPdo()->exec($sql);	
		}
		$str="Разместите на всех страницах код:
<script>
(function(m,p,w,t,k,z,h){m['MPWmetObject']=k;m[k]=m[k]||function(){(m[k].q=m[k].q||[]).push(arguments); console.log(m[k].q);},m[k].l=1*new Date();z=p.createElement(w),h=p.getElementsByTagName(w)[0];   z.async=1;z.src=t;h.parentNode.insertBefore(z,h)})(window,document,'script','//node.market-place.su/div/build/metrika.js','mpwa_');
mpwa_('create', 'UKR-KOMI-$cpa', 'auto');
</script>

При совершении покупки запустите комманду javascript, указав стоимость покупки числом
<script>
mpwa_('send', '_shopcart',{id:[],summa:20000.00});
</script>";
		return $str;

    }		
	  
}
