<?php

namespace App\Console\Commands\Next;
use Illuminate\Console\Command;
use Jenssegers\Agent\Agent;
use Illuminated\Console\WithoutOverlapping;
use Redis;
use Rekrut\Product\Models\Mystatistic as St2;

use Rekrut\Product\Models\Statistic as St1;
use Rekrut\Product\Models\Myrequest as Rt1;
use Rekrut\Product\Models\Yandexparser as Y1;
class Product extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'next:product {vair?} {wid?} {clid?} {oldclid?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function parseMinimum(&$arr)
    {
		  $agent = new Agent();
		  $arr[0]=preg_replace("/^\[|\]$/","",$arr[0]);
		    $agent_user = $arr[5];
		  	$time = strtotime($arr[0]);
			$dtime=$time-($time%600);
			$datetime=date("Y-m-d H:i:s",$time);
			$ddatetime=date("Y-m-d H:i:s",$dtime);
			$day=date("Y-m-d",$time);
			$ip = $arr[1];
			$req=preg_split("/\s+/",$arr[2]);
			if(!$req) return;
			parse_str($req[1], $dd);
			if(!isset($dd["et_"])){
				return;
			}
			$data=json_decode($dd["et_"],true);
		    if(!$data){
		        return;
	        }
			if(!isset($data["iso"])) return;
			$iso=$data["iso"];
			$country=preg_replace('/\-.+$/','',$iso);
			if(!isset($data["hash"])) return;
			
			$hash =$data["hash"];
			$city_name="";
			if(isset($data["city_name"])) 
				$city_name=$data["city_name"];
			$agent->setUserAgent($agent_user);
            if($agent->isMobile()){
				$mobile=1;
			}else{
				$mobile=0;
			    
			}
			
			if($mobile){
			
			#print $agent_user; echo "\n"; #return;
			
			}
			$fdc=count($data["idps"]);
         $re=[
			$day,
			$datetime,
			$ddatetime,
			$data["wid"],
			$hash,
			$data["url"],
			$country,
			$iso,
			$city_name,
			$mobile,
			$ip,
			$fdc
			];
			
            print_r($data["idps"]);
			$this->interSerting->execute($re) ; 
	        foreach($data["idps"] as $idp){
		    $this->updateViewOffer->execute([$idp]);
	        }
	} 
	private function parseMin1($arr){
		$words=preg_split("/\|/ui",rawurldecode($arr[11]));
		$phrazes=preg_split("/\|/ui",rawurldecode($arr[13]));
		$url=rawurldecode($arr[12]);
		var_dump($phrazes);
		print $url."\n";
		if($words){
		foreach($words as $w){
			if($d=trim($w)){
			print $w."->\n";
			}
		}
		}
		
	}
	private function getMin1(){
		$file='/home/kumar/data/stat/r2_.log';
		$tmp_file="/home/kumar/data/stat/r2_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
	    #print $tmp_file."\n";
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$page=50;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
			if($counter>=$page){
				$counter=0;
				St1::getInstance()->reload();
			}
					
				#echo $l."\n";
				try{
				$tmp=preg_split("/\|\|\|/ui",$l);
				#$z=new St1();
				St1::getInstance()->obrViews($tmp);
				#$this->parseMin1($tmp);
				#}catch(\Exception $e){
				#var_dump($e->getMessage())	;
				}catch(\Exception $e){
				var_dump($e->getMessage())	;
				}
				$counter++;
				#VizirY::getInstance()->getData($tmp);
			}
			St1::getInstance()->reload();
	    @fclose($handle);		
		}	

		$cmd ="rm  -f  $tmp_file";
	   `$cmd`;	
		
		
		
	}
private function getMin2(){
		$file='/home/kumar/data/stat/r3_.log';
		$tmp_file="/home/kumar/data/stat/r3_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	        `$cmd`;
		#$tmp_file='/home/kumar/data/stat/r3_.log';
	    print $tmp_file."\n";
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$page=50;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
			
					
				#echo $l."\n";
				try{
				$tmp=preg_split("/\|\|/",$l);
				if(preg_match('/^2[0-9]{3,3}\-[0-9]{2,2}\-[0-9]{2,2}\s[0-9]{2,2}\:[0-9]{2,2}\:[0-9]{2,2}$/',$tmp[0])){
				if($counter>=$page){
				$counter=0;
				St2::getInstance()->reload();
			        }
					$counter++;
#var_dump($tmp);
					St2::getInstance()->register($tmp);
				}else{
				#       var_dump($tmp);
					St2::getInstance()->obrViews($tmp);
				}
				#$tmp=preg_split("/\|\|\|/ui",$l);
				#$z=new St1();
				#St1::getInstance()->obrViews($tmp);
				#$this->parseMin1($tmp);
				#}catch(\Exception $e){
				#var_dump($e->getMessage())	;
				}catch(\Exception $e){
				var_dump($e->getMessage())	;
				}
				
				#VizirY::getInstance()->getData($tmp);
			}
			St2::getInstance()->reload();
	    @fclose($handle);		
		}	

		$cmd ="rm  -f  $tmp_file";
	       `$cmd`;	
		St2::getInstance()->daySumma();
      		St2::getInstance()->SelectSumma();
        		
		
		
	}

private function getMin3(){
		$file='/home/myrobot/data/apistat/3.log';
		$tmp_file="/home/myrobot/data/apistat/3_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		#$tmp_file='/home/myrobot/data/apistat/3.log';
	    print $tmp_file."\n";
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$page=50;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
			
					
				#echo $l."\n";
				try{
				$tmp=preg_split("/::/ui",$l);
                $date=$tmp[0];
                $url=rawurldecode($tmp[1]);
                $zavid=preg_split('/\/\/\//ui',$tmp[2]);
                $vavid=preg_split('/\/\/\//ui',$tmp[3]);
                $mavid=preg_split('/\/\/\//ui',$tmp[4]);
                $ruth=json_decode($tmp[5],1);
                $p1=rawurldecode($zavid[0]);
                $p2=rawurldecode($zavid[1]);
                $p3=rawurldecode($zavid[2]);
                $p4=rawurldecode($vavid[0]);
                $p5=rawurldecode($mavid[0]);
                $p6=rawurldecode($mavid[1]);
                print_r ([$date,$url,$p1,$p2,$p4,$p5,$p6,$p3]);
                print_r($ruth);
				}catch(\Exception $e){
				var_dump($e->getMessage())	;
				}
				

				#VizirY::getInstance()->getData($tmp);
			}
        @fclose($handle);		
		}	

		$cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
        		
		
		
	}	
private function getMin7(){

		$file='/home/mystatistic/product/yandex.log';
		$tmp_file="/home/mystatistic/product/yandex_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		#$tmp_file='/home/kumar/data/stat/r4_.log';
	        print $tmp_file."\n";
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$page=100;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				//echo $l."\n";

				if($counter>=$page){
				  $counter=0;
				  #Rt1::getInstance($index)->reload();
			    }
			        
				Y1::getInstance()->getReq($l);
				$counter++;
				try{
				}catch(\Exception $e){
				var_dump($e->getMessage());
				}

				

			}
			#Rt1::getInstance($index)->reload();
        @fclose($handle);		
		}	
	    $cmd ="rm  -f  $tmp_file";
	   `$cmd`;	
		
	}	
private function getMin4($index){

		Rt1::getInstance($index);
		$file='/home/kumar/data/stat/r4_.log';
		$tmp_file="/home/kumar/data/stat/r4_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		#$tmp_file='/home/kumar/data/stat/r4_.log';
	    print $tmp_file."\n";
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$page=100;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				#echo $l."\n";
				#$tmp=explode("::",$l);
				if($counter>=$page){
				  $counter=0;
				  Rt1::getInstance($index)->reload();
			    }

			        
				Rt1::getInstance($index)->getReq($l);
				$counter++;
				try{
				}catch(\Exception $e){
				var_dump($e->getMessage());
				}

				

			}
			Rt1::getInstance($index)->reload();
        @fclose($handle);		
		}	
	    $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	


#         $cmd="/usr/bin/indexer --config /etc/sphinxsearch/sphinx.conf --rotate  new_queries";	
#		`$cmd`;	
		
	}	
    public function handle()
    {
#		$this->getMin4(1);	
#		return;
		$r_vair= $this->argument('vair');
		$this->postpdo = \DB::connection("product_next")->getPdo();
		if(!$r_vair)     return;
		if($r_vair==11){
		$vie=new \Rekrut\Product\Teaser\Marketgit;
		var_dump($vie);
		return;
                }
		
		if($r_vair==8){
			$wid= $this->argument('wid');
			$clid= $this->argument('clid');
			$oldclid= $this->argument('oldclid');

			if(!$wid || !$clid || !$oldclid) return;
			$cha=new \Rekrut\Product\Models\Yandex\Clid;
			$cha->changeClid($wid,$clid,$oldclid);
			
			return;
		}
		#return;
		 #if($r_vair==7){
		#	 $this->getMin7();	
		# }

		 if($r_vair==4){
                         $this->getMin7();	
			 $this->getMin4(5);	
		 }
		return;
                if($r_vair==1){
		$this->getMin4(1);	
		return;
		}elseif($r_vair==2){
		$this->getMin4(2);	
		return;
                }
		return;
                $this->getMin3();
		$this->getMin2();
		
		print "зокончил первое задание ура\n";
		#$predpdo = \DB::connection("pgstatistic_next")->getPdo();
		print "запреить херню 2\n";

		return;
		$postpdo = \DB::connection("product_next")->getPdo();
		$predpdo = \DB::connection("pgstatistic_next")->getPdo();
		$sql="update offers set view_round = case when coalesce(view_round,0) >=50 then 1 else coalesce(view_round,0)+1 end
        where id =?";
		$this->updateViewOffer=$postpdo->prepare($sql);
		$sql="
    insert into product_views_pages (
    day,
    datetime,
	datetimegroup,
    id_widget,
    hash,
	url,
	country,
    iso,
    name,    
    mobile,
    ip,
    found
   ) values(?,
   ?,
   ?,
   ?,
   ?,
   ?,
   ?,
   ?,
   ?,
   ?,
   ?,
   ?
   )";
   $this->interSerting=$predpdo->prepare($sql);
		
		

		$file='/home/kumar/data/stat/r1_.log';
		$tmp_file="/home/kumar/data/stat/r1_".time()."_.log";
		$cmd ="cp -p $file $tmp_file && cat /dev/null >  $file";
	    `$cmd`;
		
	    print $tmp_file."\n";
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				#echo $l."\n";
				try{
				$tmp=preg_split("/\s+\:\s+/",$l);
				#var_dump($tmp);
				$this->parseMinimum($tmp);
				}catch(\Exception $e){
				var_dump($e->getMessage())	;
				}
				#VizirY::getInstance()->getData($tmp);
			}
	    @fclose($handle);		
		}	

		$cmd ="rm  -f  $tmp_file";
	   `$cmd`;	
	    $this->freeWords();
 
    }

   private function getWord($name){
	   if(isset($this->cacheWords[$name])) return $this->cacheWords[$name];
	   $this->selectWord->execute([$name]);
	   $d= $this->selectWord->fetch(\PDO::FETCH_ASSOC);
	   if($d){
		   $this->cacheWords[$name]=$d["id"];
		   return $this->cacheWords[$name];
	   }
		$this->insertWord->execute([$name,$name]);   
        $d= $this->insertWord->fetch(\PDO::FETCH_ASSOC); 
		$this->cacheWords[$name]=$d["id"];
		return $this->cacheWords[$name];
		
	   #$this->cacheWords=[];
   }
   private function freeWords(){
	   $this->cacheWords=[];
	   $postpdo = \DB::connection("product_next")->getPdo();
	   $sql="
	   insert into _words_server_pages (
        id_server,
        hash,
        id_word
        ) select ?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM _words_server_pages where  id_server=? and hash=? and id_word=?)
	   ";
	   $this->insertPageWord=$postpdo->prepare($sql);
	   $sql="
	   insert into _words (name)
	   select ?
	   WHERE NOT EXISTS (SELECT 1 FROM _words where  name=?)
       returning id 
	   ";
	   $this->insertWord=$postpdo->prepare($sql);
	   $sql="
	   select id  from _words   where  name=?
	   ";
	   $this->selectWord=$postpdo->prepare($sql);
	   
	   $predpdo = \DB::connection("pgstatistic_next")->getPdo();
	   
	   $sql="select * from product_request_pages";
	   $data=$predpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	   $dsp=[];
	   foreach($data as $d){
		   if($d["sphinx_request"]){
		   $tmp=explode("|",preg_replace('/^\(|\)$/ui','',$d["sphinx_request"]));
		   foreach($tmp as $word){
			   $word=trim($word);
			   $id=$this->getWord($word);
			   $dsp[$d["id_server"]][$d["hash"]][$id]=1;
			   var_dump([$d["id_server"],$d["hash"]]);
			  
		   }
		  
		   }
	   }
	   foreach($dsp as $d_server=>$hashes){
		   foreach($hashes as $hash=>$ids_words){
			   foreach($ids_words as $id_word=>$r){
		        print "$d_server : $hash : $id_word \n";	   	
                   $this->insertPageWord->execute([
				   $d_server,$hash,$id_word,$d_server,$hash,$id_word
				   ]);				
			   }
		   }
		   
	   }
	   $this->fugarbadge(); 
	   
       $sql="truncate table product_request_pages";
	   $predpdo->exec($sql);

	  
	  # $this->Jigan=$postpdo->prepare($sql);
	   $sql="
	   select min(id_word) as min1, max(id_word) as max1 from _words_server_pages
	   ";
	   $data=$postpdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
	   $start=$data["min1"];
	   $end=$data["max1"];
	   $pages = round(($end-$start)/10);
	   print $start." - ".$end.":$pages\n";
	   do{
		 # print "eeee-> $start\n";
		   $start=$this->restPages($start,$end,10); 
	   }while($start);
		   
	   
	   #var_dump($data);
	
   }
    private function restPages($start,$end,$page) {
		$stop=$start+$page;
		$res=1;
		if($stop>=$end){
			$stop=$end;
			$res=0;
		}else{
			$res=$stop;
		}
		$postpdo = \DB::connection("product_next")->getPdo();
			   $sql="
	   update  _words_server_summa as a
set summa =b.cnt,
status = 1
from (
select t.id_word
,t.id_server
,t.cnt            
,1
from (
select id_word
,id_server
,count(*) as cnt
from _words_server_pages
	   where id_word>=$start and id_word<=$stop
      group by id_word,id_server
) t
inner join _words_server_summa ws
    on ws.id_word = t.id_word
	and ws.id_server = t.id_server
) as b
where  a.id_word = b.id_word
	and a.id_server = b.id_server ;
    insert into _words_server_summa (
    id_word,
    id_server,
    summa,
    status
)
select t.id_word
,t.id_server
,t.cnt            
,1
from (
select id_word
,id_server
,count(*) as cnt
from _words_server_pages
	   where id_word>=$start and id_word<=$stop
      group by id_word,id_server
) t
left join _words_server_summa ws
    on ws.id_word = t.id_word
	and ws.id_server = t.id_server
	where ws.id_word is null
	";
	    $postpdo->exec($sql);
	    return $res;
    }  
    private function fugarbadge(){
		 #$redis = new Redis(); 
		 #Redis::command('hset', ['garbadgew',"водка",1]);
		 Redis::command('del', ['garbadgew']);
		 $postpdo = \DB::connection("product_next")->getPdo();
		 $sql="select * from _words where status =0";
		 $data=$postpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		 foreach($data as $d){
			 Redis::command('hset', ['garbadgew',$d["name"],1]);
			 #var_dump($d["name"]);
		 }
		 
         # $valueL= Redis::command('hget', ['garbadgew',"водка"]);
		 # var_dump($valueL); die();
		 #var_dump($redis);
		
	}
	
}
