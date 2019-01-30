<?php
namespace App\LogStat\Product;
class Site{
	 private static $instance=null;
	 private $pages=[];
	 private $sthProduct;
	 private $Sth;
	 private $servers=[];
	 private $words=[];
	 private $wordstat=[];
	 public static function getInstance(){
	 if(self::$instance==null){
		 
	 self::$instance=new self;
	 self::$instance->prepareData();
	 }
	 return self::$instance; 
	}
  public function getData(&$arr){
		if(!isset($arr[0]) || !$arr[0]) return;
		$day=preg_replace('/\s.+$/','',$arr[0]);
		if(!isset($arr[2]) || !intval($arr[2])) return;
		if(!isset($arr[1]) || !$arr[1]) return;
		
		if(!isset($arr[3])) return;
		if(!isset($arr[4]) || !$arr[4]) return;
		$id_server=intval($arr[2]);
		$hash=$arr[1];
		
		$url=rawurldecode($arr[4]);
		if($arr[3])
		$text=rawurldecode($arr[3]);
		else
		$text="none";	
	    if(!isset($this->words[$text][$id_server][$day]))
			$this->words[$text][$id_server][$day]=0;
        $this->words[$text][$id_server][$day]++;
		if(!isset($this->pages[$text][$id_server][$day][$hash]))
		$this->pages[$text][$id_server][$day][$hash]=$url;
		
		/*
		foreach($tmp as $t){
			$t=trim($t);
			if($t)
			{
			$this->servers[$id_server][$hash][$url][$t]=1;
			#print $t."\n";
			}
		}
		if(!isset($this->servers[$id_server][$hash][$url])){
			$this->servers[$id_server][$hash][$url][""]=1;
		}
		#$this->servers[$id_server][$hash][$url]=1;
		#print "[$id_server][$hash][$url]\n";
		*/
  }
  public function makeSumma(){
      #var_dump(["вобще ничего нет блять"]); 
	  #return;
	  $pdonewrestat = \DB::connection("pgstatistic_new")->getPdo();
	  $sql="insert into  new_wordstat_words (
        name
        ) select ? 
	    WHERE NOT EXISTS (SELECT 1 FROM new_wordstat_words  WHERE name=?)
		returning id
	  ";
	  $sthInsertWord=$pdonewrestat->prepare($sql);
	  $sql="select id from  new_wordstat_words 
       WHERE name=?
	  ";
	  $sthSelectWord=$pdonewrestat->prepare($sql);
	  $sql="insert into new_wordstat_servers (
    day,
    server_id,
    word_id,
    cnt
    ) select ?,?,?,? 
	WHERE NOT EXISTS (SELECT 1 FROM new_wordstat_servers  WHERE  day=?
    and server_id=?
    and word_id=?)
	";
	$sthInsertWordServer=$pdonewrestat->prepare($sql);
	  $sql="update new_wordstat_servers 
    set cnt=cnt+?
    WHERE  day=?
    and server_id=?
    and word_id=?
	";
	$sthUpdateWordServer=$pdonewrestat->prepare($sql);	
	$caches=[];
	
	  foreach($this->words as $word=>$servers){
		  #print $word."\n";
		 if($word=="none"){
			 $id_word=0;
		 }else{
			if(isset($caches[$word])){
				 $id_word=$caches[$word];
			}else{
				
		 $sthSelectWord->execute([$word]);
		 $tmp_word = $sthSelectWord->fetch(\PDO::FETCH_ASSOC);
		 if(!$tmp_word){
		 $sthInsertWord->execute([$word,$word]);
		 $tmp_word = $sthInsertWord->fetch(\PDO::FETCH_ASSOC);
		 $id_word=$tmp_word["id"];
		 }else{
		 $id_word=$tmp_word["id"];
		 }
		 $caches[$word]=$id_word;
	     }
		 }
		 foreach($servers as $id_server=>$days){
			 foreach($days as $day=>$cnt){
				$sthUpdateWordServer->execute([
			    $cnt,
				$day,
				$id_server,
				$id_word
				]);
				$count = $sthUpdateWordServer->rowCount();
				if($count==0){
				$sthInsertWordServer->execute([
				$day,
				$id_server,
				$id_word,
				$cnt,
				$day,
				$id_server,
				$id_word
				]);
				}
		        print  $word.",$id_word,$id_server,$day,$cnt\n";		 
				$this->regiSterPage($word,$id_word,$id_server,$day,$cnt);
			 }
		 }
		 
	  }
	  return;
  }
  private function regiSterPage($word,$id_word,$id_server,$day,$cnt){
	  if(!isset($this->pages[$word][$id_server][$day])) return;
	  foreach($this->pages[$word][$id_server][$day] as $hash=>$url){
		  $this->sthInsertWordPage->execute(
		  [$id_word
		  ,$id_server
		  ,$hash
		  ,$url
		  ,$id_word
		  ,$id_server
		  ,$hash
		  ]
		  );
		  
		  print  $word.",$id_word,$id_server,$day,$cnt,$hash,$url,\n";	
	  }
  }
  private function prepareData(){
	$pdonewrestat = \DB::connection("pgstatistic_new")->getPdo();
	$sql="insert into  new_wordstat_servers_pages (
    word_id,
    server_id,
    hash,
    url)
	select ?,?,?,?
	 WHERE NOT EXISTS (SELECT 1 FROM new_wordstat_servers_pages where  word_id=? and
    server_id=? and
    hash=?
	)
    ";
	$this->sthInsertWordPage=$pdonewrestat->prepare($sql);
  }
}
	