<?php

namespace App\Console\Commands\Statistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
class StatAdvert extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:advert {date?}';

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
        //
		$this->ClearLast();
		
    }
    public function ClearLast()
    {
        //
	     $pdo=\DB::connection("pgstatistic")->getPdo();
	    if (!$date = $this->argument('date')) {
	    $date=date("Y-m-d");
	    $today = Carbon::now();
		if($today->hour<2){
		$date = $today->yesterday()->format('Y-m-d');
		}
	   
	    }
		 $dest =new \App\Videosource\Advert();

		 $dest->collectViews($date);

		
		$H=preg_replace('/^0/','',date("H"));
		if($H==9){
		#print $H."\n";	
			
		}else return;
		$Todays=date("Y-m-d",(time()-3600*24*2));
		$sql="delete from advert_stat_pages where datetime <'$Todays'";
		$pdo->exec($sql); 
		$sql="delete from widget where datetime <'$Todays'";
		$pdo->exec($sql); 
		return;
		$sql="select count(*) as cnt, min(datetime) as d from advert_stat_pages where datetime >='$Todays'";
		print $sql."\n";
		$data=$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
		var_dump($data);
    }	
}
