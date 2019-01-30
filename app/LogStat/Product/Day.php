<?php
namespace App\LogStat\Product;
class Day{
	 private static $instance=null;
	 private $products=[];
	 private $sthProduct;
	 private $Sth;
	 private $day;
	 public static function getInstance($day){
	 if(self::$instance==null){
		 
	 self::$instance=new self;
	 self::$instance->prepareData();
	 self::$instance->day=$day;
	 }
	 return self::$instance; 
	}
	public function getProductInfo($id_product){
		 if(!isset($this->products[$id_product])){ 
			
		 $this->sthProduct->execute([$id_product]);
		  
		 $result = $this->sthProduct->fetch(\PDO::FETCH_ASSOC);
		 if(!$result) 
			 $this->products[$id_product]=[];
		 else
			 $this->products[$id_product]=$result;
		 }
		
		 
		 return $this->products[$id_product];
	}
	public function getDayStatistic(){
		$pdonewstat = \DB::connection("pgstatistic_new")->getPdo();
		$sql="select * from new_views where day='".$this->day."'";
		$data=$pdonewstat->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		$topwidgets=[];
		$mywidgets=[];
		foreach($data as $d){
			if($d["ids_product"]){
				$tontop=0;
				$products=explode("|",$d["ids_product"]);
				foreach($products as $id){
					$p=$this->getProductInfo($id);
					if(!$p) continue;
					if($p["advert_id"]){
						$tontop|=1;
					}else{
						$tontop|=2;
					}
				}
				if($tontop&1){ 
				if(!isset($mywidgets[$d["id_widget"]]))
					$mywidgets[$d["id_widget"]]=["views"=>0,"clicks"=>0];
				    $mywidgets[$d["id_widget"]]["views"]++;
				}
				if($tontop&2){ 
				if(!isset($topwidgets[$d["id_widget"]]))
				     $topwidgets[$d["id_widget"]]=["views"=>0,"clicks"=>0];
				     $topwidgets[$d["id_widget"]]["views"]++;
				}
			}
		}	
		
		$sql="select * from new_clicks where day='".$this->day."'";
		$data=$pdonewstat->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		$i=0;
		foreach($data as $d){
			$tontop=0;
			$p=$this->getProductInfo($d["product_id"]);
			$i++;
			if($p["advert_id"]){
				$tontop|=1;
					}else{
				$tontop|=2;
					}
			#echo $i." : $tontop : ".$p["name"]."\n";
			if($tontop&1){ 
				if(!isset($mywidgets[$d["id_widget"]]))
					$mywidgets[$d["id_widget"]]=["views"=>0,"clicks"=>0];
				    $mywidgets[$d["id_widget"]]["clicks"]++;
				}
				if($tontop&2){ 
				if(!isset($topwidgets[$d["id_widget"]]))
				     $topwidgets[$d["id_widget"]]=["views"=>0,"clicks"=>0];
				     $topwidgets[$d["id_widget"]]["clicks"]++;
				}
			
			continue;
		}
		foreach($mywidgets as $id_widget=>$da){
		    $this->sthMyUpdate->execute([
			$da["views"],$da["clicks"],
			$this->day,
			$id_widget
			]);
			$count = $this->sthMyUpdate->rowCount();
			
			print $this->day." : ".$id_widget." : >".$da["views"]." : ".$da["clicks"]." =>$count\n";
			if(!$count){
		    $this->sthMyInsert->execute([
			$this->day,
			$id_widget,
			$da["views"],$da["clicks"],
			$this->day,
			$id_widget
			]);
			}
		}
		foreach($topwidgets as $id_widget=>$da){
			$this->sthTopUpdate->execute([
			$da["views"],$da["clicks"],
			$this->day,
			$id_widget
			]);
			$count = $this->sthTopUpdate->rowCount();
			
			print $this->day." : ".$id_widget." : >".$da["views"]." : ".$da["clicks"]." =>$count\n";
			if(!$count){
		    $this->sthTopInsert->execute([
			$this->day,
			$id_widget,
			$da["views"],$da["clicks"],
			$this->day,
			$id_widget
			]);
			}
			
		
		}

		$this->updateIso();

	}
	private function prepareData(){
	$sql="select o.id,o.name
	,a.advert_id
	,a.name as ads_name
	from offers o 
	inner join ads a on a.id=o.ads_id
	where o .id=?
	";
	$pdonewstat = \DB::connection("pg_product")->getPdo();
	$this->sthProduct=$pdonewstat->prepare($sql);
	$pdonewrestat = \DB::connection("pgstatistic_new")->getPdo();
	$sql="insert into  new_tmp_top_summary (
    day,
    id_widget,
    views,
    clicks
    ) select ?,?,?,?
	 WHERE NOT EXISTS (SELECT 1 FROM new_tmp_top_summary  WHERE day=? and id_widget=?)
	";
	$this->sthTopInsert=$pdonewrestat->prepare($sql);
	$sql="update new_tmp_top_summary
    set views=?,
    clicks=?
	WHERE day=? and id_widget=?
	";
	$this->sthTopUpdate=$pdonewrestat->prepare($sql);
		$sql="insert into  new_tmp_my_summary (
    day,
    id_widget,
    views,
    clicks
    ) select ?,?,?,?
	 WHERE NOT EXISTS (SELECT 1 FROM new_tmp_my_summary  WHERE day=? and id_widget=?)
	";
	$this->sthMyInsert=$pdonewrestat->prepare($sql);
	$sql="update new_tmp_my_summary
    set views=?,
    clicks=?
	WHERE day=? and id_widget=?
	";
	$this->sthMyUpdate=$pdonewrestat->prepare($sql);
	
	//var_dump($this->sthProduct);
	}
	private function updateIso(){
		$pdoadvert = \DB::connection("advertise")->getPdo();
		$pdoproduct = \DB::connection("pg_product")->getPdo();
		#$sql=
		$sql="select * from iso2_tree";
		$data=\DB::connection("advertise")->select($sql);
		foreach($data as $d){
			#print $d->country."/".$d->code."\n";
		}
		//var_dump($data);
		
	} 
}	