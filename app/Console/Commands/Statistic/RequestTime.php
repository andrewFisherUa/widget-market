<?php

namespace App\Console\Commands\Statistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
use AdvStat;
class RequestTime extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:requesttime {date?}';

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
          $pdo=\DB::connection('pgstatistic_new')->getPdo();
		 		  $sql="
          insert into widget_requests (
          day,
          id_server,
          datetime,
          hash,
          request,
		  nosearch,
		  url,
		  loaded
          )
		  values (?,?,?,?,?,?,?,?)
		  ";
		  $sth=$pdo->prepare($sql);
		  $my_file="/home/myrobot/data/videostatistic/request_times.market-place.su.log";
		  $tmp_file="/home/myrobot/data/videostatistic/request_times.market-place_".time()."_.log";
		  $cmd ="cp -p $my_file $tmp_file && cat /dev/null >  $my_file";
	      `$cmd`;
		  $handle = @fopen($tmp_file, "r");
		  if ($handle) {
		  while (($buffer = fgets($handle, 4096)) !== false) {
				
				$l = str_replace("\n", "", $buffer);
			
				$tmp=preg_split("/\|\|\|/",$l); 
				$id_server=0;
				$day=null;
				$daytime=null;
				$hash=null;
				$request=null;
				$nosearch=1;
				$url=null;
				$loaded=0;
				if(isset($tmp[0])){
				$ztmp=explode("_",$tmp[0]);
					$id_server=intval($ztmp[0]);
				}
				if(!$id_server) continue;
				if(isset($tmp[1]))
				$day=trim($tmp[1]);	
				else continue;
				if(isset($tmp[2]))
				$daytime=trim($tmp[2]);	
				else continue;
				if(isset($tmp[3]))
				$hash=trim($tmp[3]);	
				else continue;
				if(isset($tmp[6])){
				$request=rawurldecode(preg_replace('/\'\"/m','',trim($tmp[6])));	
					print $request."\n";
				}
			    else continue;				
				if(isset($tmp[9]))
				$nosearch=intval($tmp[9]);
			    else continue;	
				if(isset($tmp[7]))
				$url=rawurldecode(trim($tmp[7]));
			    else continue;	
				if(isset($tmp[8]))
				$loaded=rawurldecode(trim($tmp[8]));
			    else continue;					
				
				if($nosearch)
					$request=null;
				
				$rvm=[
				$day 
				,$id_server 
				,$daytime 
				,$hash
				,$request
				,$nosearch
				,$url
				,$loaded
				];
				#print $day ." : ".$request; print "\n";
				$sth->execute($rvm);
		      }
			@fclose($handle); 
		  }

		   $cmd ="rm  -f  $tmp_file";
	      `$cmd`;	
		  
		$date= $this->argument('date');
		if(!$date){
			$date=date("Y-m-d");
		}
		$today = Carbon::now();
		if($today->hour<2){
			$date = $today->yesterday()->format('Y-m-d');
		}
		AdvStat::setDate($date);
		AdvStat::Calculate();
		
    }
}
