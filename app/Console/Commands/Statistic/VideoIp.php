<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
class VideoIp extends Command
{
use WithoutOverlapping;
private $playEvents=[];
private $groupPidsNoControl=[];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:video_ip';

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
		$date=date('Y-m-d');
		$pdo = \DB::connection("videotest")->getPdo();
		
		$sql="insert into stat_ip (day,ip,cnt)
			select ?,?,? WHERE NOT EXISTS (SELECT 1 FROM stat_ip WHERE day=? and ip=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update stat_ip set cnt=?
			WHERE day=? and ip=?";
		$sthUpdate=$pdo->prepare($sql);
		
	    $sql="select day, ip, count(*) as cnt from stat_user_pages where played>'0' and day='$date' group by ip, day";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($stats as $stat){
			if ($stat['cnt']<100 or $stat['ip']=='176.213.140.214'){
				continue;
			}
			$sthUpdate->execute([$stat['cnt'], $stat['day'], $stat['ip']]);
			$sthInsert->execute([$stat['day'], $stat['ip'], $stat['cnt'], $stat['day'], $stat['ip']]);
		}
	}
}
