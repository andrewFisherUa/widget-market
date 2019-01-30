<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VideoError extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:videoerror';

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
	return;
	$datet=date("Y-m-d H:00:00");
	$pdo = \DB::connection("pgstatistic")->getPdo();
	$sql="insert into new_video_error_stat(
	datetime ,
    ip,
    id,
    event,
	message
	) values (?,?,?,?,?)
	"; 
	$sth=$pdo->prepare($sql);
	$pvo = \DB::connection("videotest")->getPdo();
	$sql="truncate table src_events ";
	$pvo->exec($sql);
	$sql="insert into src_events (datetime,id_src,event,country,device,message)
	values(?,?,?,?,?,?)";
	$sthv=$pvo->prepare($sql);
	
       	$tmp_file="/home/myrobot/data/videostatistic/statistic-error_".time()."_.log";
        $cmd ="cp -p /home/myrobot/data/videostatistic/staterror-video.log $tmp_file && cat /dev/null >  /home/myrobot/data/videostatistic/staterror-video.log";
		
		 //$cmd ="cp -p /home/myrobot/data/videostatistic/staterror-video.log $tmp_file ";
		`$cmd`;
		$handle = @fopen($tmp_file, "r");
		if ($handle) {
			$counter=0;
			$counterr=0;
			while (($buffer = fgets($handle, 4096)) !== false) {
				$l = str_replace("\n", "", $buffer);
				$tmp=preg_split("/\s+\:\s+/",$l);
				//var_dump($tmp[1]); 
				$tmp[0]=preg_replace("/^\[|\]$/","",$tmp[0]);
				$time = strtotime($tmp[0]);
				  $country=trim($tmp[2]);
		   if($country!='RU')
		   $country='CIS';
				$datetime=date("Y-m-d H:i:s",$time);
					if (!$tmp){
					continue;
					}
					if (count($tmp)<2){
					continue;
					}
					if (!isset($tmp[5])){
					continue;
					}
				  $req=preg_split("/\s+/",$tmp[5]);
				   parse_str($req[1], $arr);
		
					if (!isset($arr['data'])){
					continue;
					}
					
						$data=json_decode($arr["data"],true);
		if(isset($data["is_mobile"])){
		    $is_mobile=$data["is_mobile"];
			//echo $is_mobile." ----- \n";
	    }	
		else{
		 $is_mobile=0;
		}
		if(!$is_mobile)
 $is_mobile=0;
 else $is_mobile=1;                    
					if(isset($data["resultat"])){
						if($data["id"]!="99999"){
						$id=$data["id"];
						
						}
						else{
						$id=$data["rid"];
						}
						$mess="";
						if($data["resultat"]["type"]=="loading_error" && isset($data["resultat"]["media"]["status"])){
						
						$mess = urldecode($data["resultat"]["media"]["status"]);
						//var_dump($data["resultat"]);
						}
						
						$sthv->execute([$datet,$id,$data["resultat"]["type"],$country,$is_mobile,$mess]);
						echo $id."    -> ".$data["resultat"]["type"]."\n";
						//var_dump($data);
						}
						 continue;		
						//echo $datetime."\n";
						//if(isset($data["event"]["type"]) && $data["event"]["type"]=="xml_error"){
						if(isset($data["error"]) && $data["error"]["type"]=="xml_error" && isset($data["error"]["media"])){
						//var_dump([$datetime,$data,$tmp]);
						//var_dump([$data]);
						var_dump([$data["id"],$data["rid"],$data["error"]["media"]["status"]]);
						}
                       				
						//if(isset($data["data"]["data"])){
												if(isset($data["event"]["type"]) && $data["event"]["type"]=="mixtraf" && isset($data["zx"]) && is_array($data["zx"])){
												
																		$data["zx"]["ip"]=$tmp[1];
						$data["zx"]["date"]=$datetime;
												var_dump($data["zx"]);
												
												}
												continue;
						if(isset($data["dw"]) && ! is_array($data["dw"])){
						//var_dump($data["dw"]);
						//if( $tmp[1] == "188.162.172.0")
						//echo $datetime." : ".$tmp[1]." : ".$data["dw"]."\n";
						}
						//}
						if(!$data["id"] || $data["id"]!="99999") {
						
						continue;
						}
						$ip=$tmp[1];
						$eventS="";
						$message="";
						$id=0;
						if(isset($data["rid"])){
						if(is_array($data["rid"]))
						$id=$data["rid"]["id_src"];
						else
						$id=$data["rid"];
						}
						
						$eventS="";
						
						if(isset($data["event"]) && isset($data["event"]["type"])){
						$eventS=$data["event"]["type"];
						if(isset($data["event"]["teas"]))
						$eventS.="-".$data["event"]["teas"];
						}else{
						
						}
						if($eventS=="error"){
						if(isset($data["event"]["data"]["status"])){
						$message=$data["event"]["data"]["status"];
						
						}else{
						
						}
						}

			$sth->execute([$datetime,$ip,$id,$eventS,$message]);
						echo $ip.":$id:$eventS:$datetime:$message\n";
				
                   
				 
				}
			}	
			$cmd ="rm  -f  $tmp_file";
	        `$cmd`;	
    }
}
