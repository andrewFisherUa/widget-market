<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SinonimController extends Controller
{       
        private static $search;
        private $indexes;
        private $exclude=["для"=>1,"и"=>1];
		private $Levels;
		public function resetSphinx(){
	    if(self::$search){
	        self::$search->resetFilters();
	        self::$search->resetGroupBy();
		}
		return self::$search;
	}
	public function getSphinx(){
	    if(!self::$search){
	        self::$search = new \Sphinx\SphinxClient();
	        self::$search->setServer("127.0.0.1", "9312");
		}
		return self::$search;
	}	
public function deleteSinonim(Request $request){
	  $id=$request->input('id');
	 
	  if($id){
		  \DB::connection("advertise")->table("sinonims")->where("id",$id)->delete();
		  
		    $pdo =\DB::connection("advertise")->getPdo();
		  	$comm="/usr/bin/indexer  --rotate  sinonims";
			$sql="insert into schedlary (type,comm)
			select 2,?
			WHERE NOT EXISTS (SELECT 1 FROM schedlary  WHERE type=2 and comm =?)
			";
			$sth=$pdo->prepare($sql);
			$sth->execute([$comm,$comm]);
	  }
     return back();
	}	
	public function addSinonim(Request $request){
		   $name=$request->input('name');
		   $sinonim=$request->input('sinonim');
		    $validator=Validator::make(
			array(
				'name' => $name,
				'sinonim' => $sinonim
			),
			array(
				'name' => 'required|string|max:255',
				'sinonim' => 'required|string|max:255'
			));
			if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
			}
			$pdo =\DB::connection("advertise")->getPdo();
			$sql="insert into sinonims (name,sinonim)
			select ?,?
			WHERE NOT EXISTS (SELECT 1 FROM sinonims  WHERE name=? and sinonim=?)
			";
			$sth=$pdo->prepare($sql);
			$sth->execute([$name,$sinonim,$name,$sinonim]);
			$comm="/usr/bin/indexer  --rotate  sinonims";
			$ql="update sinonims set indexed = 1 where indexed = 0";
			$sql="insert into schedlary (type,comm,ql_)
			select 2,?,?
			WHERE NOT EXISTS (SELECT 1 FROM schedlary  WHERE type=2 and comm =?)
			";
			$sth=$pdo->prepare($sql);
			$sth->execute([$comm,$ql,$comm]);
			 #echo shell_exec("/usr/bin/indexer  --rotate  sinonims"); #`/usr/bin/indexer  --rotate  sinonims`;
			return back();
	}
    public function index(Request $request){
		$name =$request->input("name");
		if($name){
		$this->makePara($name);	
                $r=$this->split_($name,1);
                if($r)
		$this->makeArraySingle($r);
		
	    $results=[];
        if($this->indexes){
			$ii=$this->indexes;
        $tmp=\DB::connection("advertise")->table("sinonims")->whereIn("id",array_keys($this->indexes))->get();
		$results=$tmp->sortByDesc(function ($product, $key) use ($ii){
        return $ii[$product->id] ;
         });
		}		
		}else{
		$results=\DB::connection("advertise")->table("sinonims")->orderBy("name")->get();
		}
		
		
		
		
		foreach($results as $r){
			#print "<pre>";  var_dump($r);   print "</pre>";
		}
		return view('advertiser.cabinet.sinonim', ["collection"=>$results,"name"=>$name]);
		#var_dump("тут я был");
	}
	private function makePara($name){
		
		$arr=$this->split_($name);
		$para=[];
		$lastword="";
		foreach($arr as $a){
			if($lastword){
				$para[]=[$lastword,$a];
			}
			
			$lastword=$a;
		} 
		if($para){
			$this->getSinonims($para);
			#print "<pre>";  var_dump($para);   print "</pre>";
		}
		
	}
	private function checkCack($t){
		if(!$t)
		return false;
	     
	    if(isset($this->exclude[$t])) 
		return false;
	    return true;
	}
	private function split_($str,$flag=0){	
        $arr=preg_split('/[^АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюяA-z0-9\-]+/ui',$str);	
                if(!$flag)
		return $arr;
		$varr=[];
                foreach($arr as $t){
		    $t=trim($t);
			if($this->checkCack($t))
		    $varr[]=$t;
		}
                return $varr;

		
    }
	public function makeArraySingle($arr){
		$sphinx=$this->getSphinx(); 
		$this->resetSphinx();

	        $sphinx->SetMatchMode( \Sphinx\SphinxClient::SPH_MATCH_EXTENDED2); 
				#$sphinx->SetMatchMode( \Sphinx\SphinxClient::SPH_MATCH_ALL); 
		$sphinx->setRankingMode( \Sphinx\SphinxClient::SPH_RANK_PROXIMITY_BM25  );
	    $sphinx->setSortMode(\Sphinx\SphinxClient::SPH_SORT_EXTENDED, "@relevance desc");
		$sphinx->setSelect('*');
		$sphinx->setLimits(0,9); 
		foreach($arr as $a){
			 $str="^$a$";
  		     #echo $str."<hr>";
		      
			 $sphinx->addQuery($str,"sinonims");				
		}
		 	     $results=$sphinx->runQueries(); 
		 #$this->print_($results); $this->stop_();
		 $i=0;
	 	 foreach($results as $res){
				if(isset($res["matches"])){
					#$this->print_($res);
					foreach($res["matches"] as $key=>$m){

						#print "<pre>";  var_dump($m);   print "</pre>";	
						$this->indexes[$key]=$m["weight"];
						
						
					}
				}
						 $i++; 	
			}	

	}
    private function getSinonims($arr){	
    	$sims=[];
		$sphinx=$this->getSphinx(); 
		$this->resetSphinx();
        $sphinx->SetMatchMode( \Sphinx\SphinxClient::SPH_MATCH_EXTENDED2); 
		$sphinx->setRankingMode( \Sphinx\SphinxClient::SPH_RANK_PROXIMITY_BM25  );
	    $sphinx->setSortMode(\Sphinx\SphinxClient::SPH_SORT_EXTENDED, "@relevance desc");
		$sphinx->setLimits(0,3); 
		foreach($arr as $a){
			 $str=implode(" ",$a);
			 #echo $str."<hr>";
		      
			 $sphinx->addQuery($str,"sinonims");				
		}
		
		 $results=$sphinx->runQueries(); 
		 	foreach($results as $res){
				if(isset($res["matches"])){
					foreach($res["matches"] as $key=>$m){
						 #print "<pre>";  var_dump($m);   print "</pre>";	
						$this->indexes[$key]=$m["weight"];
						
						
					}
				
				
				}
				 	
			}	
		
		
		
    }
}
