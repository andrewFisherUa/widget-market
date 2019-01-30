<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class MultyVideo extends Command
{
use WithoutOverlapping;
private $playEvents=[];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:multyvideo';

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
    public function handle()
    {
	 $tmp_file="/home/myrobot/data/videostatistic/mirstat_".time()."_.log";
        $cmd ="cp -p /home/myrobot/data/videostatistic/mir-video.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/mir-video.log";
		//$cmd ="cp -p /home/myrobot/data/videostatistic/mir-video.log $tmp_file ";
	   `$cmd`;
	   	 $handle = @fopen($tmp_file, "r");
         if ($handle) {
        while (($buffer = fgets($handle, 4096)) !== false) {
		    $l = str_replace("\n", "", $buffer);
			//echo $l."\n";
			$tmp=preg_split("/\s+\:\s+/",$l);
			if(!$tmp || count($tmp)<>9) continue;
	        $req=preg_split("/\s+/",$tmp[5]);
			parse_str($req[1], $arr);
			 if(!isset($arr["data"])){
	           continue;
	         }
			
		    $data=json_decode($arr["data"],true); 
			
			if(!$data || count($data)<5 ) continue;
			if(!isset($data["counter"])) continue;
			 $tmp[0]=preg_replace("/^\[|\]$/","",$tmp[0]);
	         $time = strtotime($tmp[0]);
			 //$time=$time-($time%180);
	         $datetime=date("Y-m-d H:i:s",$time);
			 $agent=trim($tmp[8]);
			 
			 $this->registerPlayerEvents($data,$datetime,$agent);
			 continue;
			 #$tmp[0]=preg_replace("/^\[|\]$/","",$tmp[0]);
	         #$time = strtotime($tmp[0]);
			 $time=$time-($time%180);
	         $datetime=date("Y-m-d H:i:s",$time);
			if($data["media"]["type"]=="VPAIDPlayer"){
			
			
			if($data["event"]=="error"){
			
			
			if(is_array($data["data"]["status"]))
			var_dump([$datetime,$data["data"]]);
			else
			echo $datetime."   ".$data["id_src"]." ".$data["data"]["status"]."\n";
			
			}else{
			
			}
			
			}
			
			//var_dump($data);
		    }
		}
		 $cmd ="rm  -f  $tmp_file";
	    `$cmd`;	
		$this->dispatchPlayerEvents();
    }
 private function registerPlayerEvents(&$data,$datetime,$agent)
    {	
	if(!isset($data["id_src"])) return;
	
	switch($data["id_src"]){
	case 3:
	case 36:
	break;
	default:
	#return;
	break;
	}
	print $data["event"]."\n";
	if($data["event"]=='FAILED'){
	print_r($data);
	echo "\n";
	}
	if(!isset($this->playEvents[$data["page_key"]]))
	$this->playEvents[$data["page_key"]]=[];
	if(!isset($this->playEvents[$data["page_key"]][$data["id_src"]]))
	$this->playEvents[$data["page_key"]][$data["id_src"]]=[];
	if(!isset($this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]]))
	$this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]]=[];
	if(!isset($this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]]))
	$this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]]=[];
	if(!isset($this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]][$data["event"]]))
	$this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]][$data["event"]]=[];
            $msg="";
			if(isset($data["data"]["status"])){
	        if(is_array($data["data"]["status"]))
			#var_dump([$datetime,$data["data"]]);
			$msg=serialize($data["data"]["status"]);
			else
			$msg=urldecode($data["data"]["status"]);
	        }else{
			#var_dump($data);
			
			}
		array_push($this->playEvents[$data["page_key"]][$data["id_src"]][$data["counter"]][$data["event"]],[$datetime,$msg,$agent]);
	
	
	#var_dump($data["id_src"]);
	#var_dump($data["page_key"]);
	}
private function dispatchPlayerEvents(){
     $pdo = \DB::connection("pgstatistic")->getPdo();
	 $sql="truncate table video_player_events";
	 $pdo->exec($sql);
	 $sql="insert into video_player_events (page_key,id_src,ind,event_name,datetime,message,agent)
	 values (?,?,?,?,?,?,?)
	 ";
	 $sth=$pdo->prepare($sql);
     foreach($this->playEvents as $page_key=>$srcs){
	     foreach($srcs as $id_src=>$counters){
		 //sort($counters);
		     foreach($counters as $cnt=>$events){
			 
			   foreach($events as $event=>$msgs){
			   foreach($msgs as  $msg=>$arr){
			   $sth->execute([$page_key,$id_src,$cnt,$event,$arr[0],$arr[1],$arr[2]]);
			   #print $page_key.":".$id_src.": ".$cnt." :".$event.">".$arr[0].">".$arr[1]."\n";
			   }
			  }
		   }   
		 }
	  }
	  $sql="select * from video_player_events
where event_name ='error'
order by datetime desc";
	  $sql="
	  select event_name,count(*) from video_player_events
      group by  event_name
	  ";
	  
	  $sql="
	  select * from video_player_events
order by page_key,id_src,ind
	  ";
    }	
}
