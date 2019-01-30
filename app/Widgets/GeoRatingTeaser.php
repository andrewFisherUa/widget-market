<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class GeoRatingTeaser extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
		
		
		
		
		/*
	    $cols=[];
		$rows=[];
		$monorating=[];
		$pigment=[];
		if(isset($this->config["id"])){
			if(\Auth::user()->hasRole('admin')){
			
			}else{
				
				
				
			}
			$rates=[];
			$vtmp=\DB::connection("advertise")->table("myads_rubriks_iso2_tree")->where("id_company",$this->config["id"])->get();
			foreach($vtmp as $vp){
				$cols[$vp->id_rubrik]=1;
				$rows[$vp->id_iso2]=1;
				$rates[$vp->id_iso2][$vp->id_rubrik]=$vp->price;
				$pigment[$vp->id_iso2][$vp->id_rubrik]=$vp->price;
				$monorating[$vp->id_iso2][$vp->id_rubrik]["price"]=$rates[$vp->id_iso2][$vp->id_rubrik];	
			    $monorating[$vp->id_iso2][$vp->id_rubrik]["changes"]=1;
				
			}
			
		}else{
			$rates=[];
		}
		if(\Auth::user()->hasRole('admin')){
			#print "<pre>"; print_r($rates); print "</pre>";
		}else{
			if(isset($this->config["id"])){
			 $cols=[];
		     $rows=[];
			 $rates=[];
			$sql="select mrti.id_company,i2.geo_id as id_iso2,mrti.id_rubrik,mrti.price
 from advertise_iso2 i2 
inner join (iso2_tree it
inner join (iso2_tree it2
inner join (
select id_company,id_iso2,id_rubrik,price from myads_rubriks_iso2_tree v1 where id_company=".$this->config["id"]."
union
select id_company,id_iso2,id_rubrik,price from myads_rubriks_iso2_tree v1 where id_company=0

) mrti

on mrti.id_iso2=it2.id 
)
on it2.parent_path @> it.parent_path
)
on it.id=i2.geo_id
where i2.shop_id =".$this->config["id"]."
order by it2.parent_path desc,mrti.id_company desc";
		    $vtmp=\DB::connection("advertise")->getPdo()->query($sql)->fetchAll(\PDO::FETCH_CLASS);
			foreach($vtmp as $vp){
				if(!isset($rates[$vp->id_iso2][$vp->id_rubrik])){
					$rates[$vp->id_iso2][$vp->id_rubrik]=$vp->price;	
					$monorating[$vp->id_iso2][$vp->id_rubrik]["price"]=$rates[$vp->id_iso2][$vp->id_rubrik];	
			       
				   
					if($vp->id_company>0){
					$cols[$vp->id_rubrik]=1;
				    $rows[$vp->id_iso2]=1;
					$monorating[$vp->id_iso2][$vp->id_rubrik]["changes"]=1;
					}
					
				}
				
			}
			}

		#echo $sql;	
		}
		
		$def=\DB::connection("advertise")->table("myads_rubriks_iso2_tree")->where("id_company",0)->get();
		$default=[];
		foreach($def as $d){
			$monorating[$d->id_iso2][$d->id_rubrik]["price"]=$d->price;
			if(isset($rates[$d->id_iso2][$d->id_rubrik]) && $rates[$d->id_iso2][$d->id_rubrik]){
			$monorating[$d->id_iso2][$d->id_rubrik]["price"]=$rates[$d->id_iso2][$d->id_rubrik];	
			$monorating[$d->id_iso2][$d->id_rubrik]["changes"]=1;
			}elseif(isset($pigment[$d->id_iso2][$d->id_rubrik]) && $pigment[$d->id_iso2][$d->id_rubrik]){
					#$cols[$d->id_rubrik]=1;
				    #$rows[$d->id_iso2]=1;
				$monorating[$d->id_iso2][$d->id_rubrik]["price"]= $pigment[$d->id_iso2][$d->id_rubrik];	
			    $monorating[$d->id_iso2][$d->id_rubrik]["changes"]=1;
				
			}
			$default[$d->id_iso2][$d->id_rubrik]=$d->price;
		}
		#$default=[];
		
		$ccx=\DB::connection("advertise")->table("myads_rubriks")->where("status",1)->orderBy("name")->get();
		$ipgs=[];
	    $ccd=\DB::connection("advertise")->table("iso2_tree")->orderBy("name")->get();
		foreach($ccd as $arg){
			if(!$arg->parent_id) $arg->parent_id=0;
			$ipgs[$arg->parent_id][$arg->id]=$arg;
			#print_r($arg); echo "<hr>";
		}
		
	#print "<pre>"; print_r($rates); print "</pre>";	
	if(!isset($this->config["id"])){
		$this->config["id"]=null;
	}
	*/
	$rates=[];
	
	if(isset($this->config["id"]) && $this->config["id"]){
	   $rt=\DB::connection('advertise')->table('advertises_teaser_geo')->where("id_company",'=',$this->config["id"])->get();
	   foreach($rt as $r){
		   $rates[$r->id_geo]=1;
		// var_dump($r) ; echo "<hr>";
	   }
	}
	 //var_dump($rates); die();
    $ccd=\DB::connection("advertise")->table("iso2_tree")->orderBy("name")->get();
	$ipgs=[];
			foreach($ccd as $arg){
			if(!$arg->parent_id) $arg->parent_id=0;
			$ipgs[$arg->parent_id][$arg->id]=$arg;
			#print_r($arg); echo "<hr>";
		}
	if(!isset($this->config["id"])){
		$this->config["id"]=null;
	}
	
		 return view('widgets.geo_rating_teaser', [
            'config' => $this->config,'ipgs'=>$ipgs,"rates"=>$rates]);
		/*
        return view('widgets.geo_rating_teaser', [
            'config' => $this->config,'ipgs'=>$ipgs,"ccx"=>$ccx,"rates"=>$rates,"cols"=>$cols,"rows"=>$rows,"default"=>$default,
			"monorating"=>$monorating
        ]);
		*/
    }
}
