<?php

namespace App\Console\Commands;
use Illuminated\Console\WithoutOverlapping;
use Illuminate\Console\Command;
use Carbon\Carbon;
class IPtraf extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iptraf';

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
		/*
		sudo stdbuf -oL -eL /usr/sbin/tcpdump -A -s 10240 "tcp port 80 and (((ip[2:2] - ((ip[0]&0xf)<<2)) - ((tcp[12]&0xf0)>>2)) != 0)" | egrep -a --line-buffered ".+(GET |HTTP\/|POST )|^[A-Za-z0-9-]+: " | perl -nle 'BEGIN{$|=1} { s/.*?(GET |HTTP\/[0-9.]* |POST )/\n$1/g; print }'>miwidletfile

		
		*/
		$pdo=\DB::connection("pgstatistic")->getPdo();
		
		
          $my_file="/home/mystatistic/video/megogo.log";
		  $tmp_file="/home/mystatistic/video/megogo_".time()."_.log";
		  $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	      `$cmd`;
		  $handle = @fopen($tmp_file, "r");
		  if ($handle) {
			    while (($buffer = fgets($handle, 4096)) !== false) {
			    $l = str_replace("\n", "", $buffer);
			    if(!$l) continue;
				#print $l."\n";
				try{
				$tmp=preg_split("/\s+\:\s+/",$l);
				#$data=preg_split('/\|/',$tmp[0]);
				\App\LogStat\Video\Kinodr::getInstance()->getData($tmp);
				//var_dump($tmp);echo "\n";
				}catch(\Exception $e){
					
				}
				
				}
		  }
		  @fclose($handle); 
		   $cmd ="rm  -f  $tmp_file";
	      `$cmd`;	
		   \App\LogStat\Video\Kinodr::getInstance()->Zabir();
		          $my_file="/home/mystatistic/video/videomp.log";
		  $tmp_file="/home/mystatistic/video/videomp_".time()."_.log";
		  $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	      `$cmd`;
		  $handle = @fopen($tmp_file, "r");
		  if ($handle) {
			    while (($buffer = fgets($handle, 4096)) !== false) {
			    $l = str_replace("\n", "", $buffer);
			    if(!$l) continue;
				#print $l."\n";
				try{
				$tmp=preg_split("/\s+\:\s+/",$l);
				#$data=preg_split('/\|/',$tmp[0]);
				\App\LogStat\Video\Kinodr::getInstance()->getDataObr($tmp);
				#var_dump($tmp);
				}catch(\Exception $e){
					
				}
				
				}
		  }
		  @fclose($handle); 
		   $cmd ="rm  -f  $tmp_file";
	      `$cmd`;	
		  var_dump("zabir check");
		\App\LogStat\Video\Kinodr::getInstance()->ZabirCheck();
		
		#
		
		return;
		
		$sql="
	insert into iptraf (
    datetime,
    host,
    ref,
    domain,
    get,
	path
    )
	values(?,?,?,?,?,?) ";
	$insertWarnins=$pdo->prepare($sql);
		
		  $tmp_file="/home/myrobot/data/bots/miwidletfile";
          #$my_file="/home/myrobot/data/bots/miwidletfile";
		  #$tmp_file="//home/myrobot/data/bots/miwidletfile_".time()."_.log";
		  #$cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	      #`$cmd`;
		  $handle = @fopen($tmp_file, "r");
		  if ($handle) {
			  $idva=0;
			  $gigamer=0;
			  $unbig=0;
			  $nbig=0;
		  while (($buffer = fgets($handle, 4096)) !== false) {
			    $l = str_replace("\n", "", $buffer);
			    if(!$l) continue;
				if(preg_match('/^HTTP/',$l)){
				if($idva==2){
					if(!isset($prok["get"]))
						$prok["get"]="";
					if(!isset($prok["path"]))
						$prok["path"]="";
					
		  if(!isset($prok["domain"]) || !isset($prok["ref"]) || !isset($prok["datetime"]) || !isset($prok["host"])){
            				$unbig++;
							
					}else{
						var_dump($prok);
						/*
						$insertWarnins->execute([
						$prok["datetime"],
						$prok["host"],
						$prok["ref"],
						$prok["domain"],
						$prok["get"],
						$prok["path"]
						]); 
						*/
						#if(!isset($prok["datetime"])){
						
						#}
					}
				$gigamer++;
				$nbig++;
				$idva=0;	
				$prok=[];
				}
			    #if(preg_match('/^HTTP/',$l)){
					$prok["id"]=$gigamer;
					$idva++;
				    #print $gigamer." / ".$idva." / ".$l;	
				    #print "\n";
				}
				if(preg_match('/^Referer\:\s+(.*)$/',$l,$match)){
                    $prok["ref"]=trim($match[1]);
					$test=parse_url($prok["ref"]);
					if(!$test){
					#print $gigamer." / ".$idva." / ".$prok["ref"];	
				    #print "\n";
					}
					if(!isset($test["host"])){
					$prok["domain"]="";
					$prok["path"]="";
					}else{
					$prok["domain"]=$test["host"];
					if(!isset($test["path"])){
						$prok["path"]="";
					}else{
					$prok["path"]=dirname($test["path"]);
					}
					var_dump($test);
					}
				}
				if(preg_match('/^Host\:\s+(.*)$/',$l,$match)){
					
					$prok["host"]=trim($match[1]);
					if($prok["host"]=="api.market-place.su") exit();
				}	
				if(preg_match('/^GET\s+(.*)$/',$l,$match)){
					$prok["get"]=trim($match[1]);
				}	

				if(preg_match('/^Date\:\s+(.*)$/',$l,$match)){
					
					$dd=Carbon::parse($match[1]); 
					$dd->setTimezone("Europe/Moscow"); 
					#GMT
					#print_r($dd);
					#$prok["datetime"]=trim($match[1]);
					$prok["datetime"]=$dd->format('Y-m-d H:i:s');
				    #print $gigamer." / ".$idva." / ".$prok["host"];	
			        #print "\n";

				}	
				#if($gigamer==1909){
				#print $gigamer." : ".$idva." : ".$l;	
				#print "\n";
				#}				
				#if(preg_match('/^HTTP/',$l)){
				
			        print $gigamer." / ".$idva." / ".$l;	
				    print "\n";
		  }
		 
		  }  
		  @fclose($handle); 
		  # $cmd ="rm  -f  $tmp_file";
	      #`$cmd`;	
		     print "$unbig : $nbig : $gigamer \n";
    }
}
