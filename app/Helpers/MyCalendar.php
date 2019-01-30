<?php 
namespace App\Helpers;
use Illuminate\Support\Facades\Redis;
class MyCalendar {
	public $id; 
	public $schedule; 
	public $HouerkRang=24; 
	public $maxd=14196;
    public function weekSchedule($id,$schedule)
    {
		$this->id=$id;
		$this->schedule=$schedule;
    }
	public function saveNave(){
	
		$pdo=\DB::connection("advertise")->getPdo();
	$sql="delete from shop_schedule_week where shop_id={$this->id} ";
		$pdo->exec($sql);
		$sql="insert into shop_schedule_week 
		(shop_id,ind)
		values (?,?)
		";
		$mvrk=$pdo->prepare($sql);
		if(!$this->schedule){
			
			$mvrk->execute([$this->id,$this->maxd]);
			Redis::command('del', ['day_week_'.$this->id]);
			#$pdo->exec($sql);
			return;
		}
		$day=date("w");
		$h=date("G");
		
		$nowind=$this->getHashIndex([$day,$h]);
		$data=$this->getHashData($nowind);
		$summa=0;
		$cacheIn=[];
		#print "тудей : [".$day."/".$h." ] $nowind {$data[0]} / {$data[1]} <hr>";
		
		foreach($this->schedule as $ww=>$data){
			foreach($data as $d=>$o){
			$ind=$this->getHashIndex([$ww,$d]);
			if(isset($cacheIn[$ind])){
			#	print "не встечал ли кто где кого? <hr>"; exit();
			}
			$cacheIn[$ind]=1;
			$summa+=$ind;
			$data=$this->getHashData($ind);
		    #print "[ $ww/".$d." ]  $ind <=> {$data[0]} / {$data[1]}<hr>";	
			}
		}
			
		if($summa==$this->maxd){
			#$valueL= 
			Redis::command('set', ['day_week_'.$this->id,$this->maxd]);
		$mvrk->execute([$this->id,$this->maxd]);
		return;		
		}
		if($cacheIn){
			
		Redis::command('set', ['day_week_'.$this->id,implode("|",array_keys($cacheIn))]);	
		}else{
			
		}
		foreach ($cacheIn as $ind1=>$wo){
		$mvrk->execute([$this->id,$ind1]);	
		}
		
		#print $summa." это сумма<hr>";
	}
   public function getHashIndex($arr){
	   return $arr[0]*$this->HouerkRang+($arr[1]+1);
   }	   
   public function getHashData($ind){
	   $res=[0,0];
	   $res[0]=floor(($ind-1)/($this->HouerkRang));
	   $res[1]=($ind-1)%($this->HouerkRang);
	   return $res;
	   
   }	   
}
