<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;

class Refer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'statistic:refer {date?}';
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
		exit;
		$date= $this->argument('date');
		if(!$date){
			$date=date("Y-m-d");
		}
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select date,pid, substring(referrer from '://((?:(?!://).)+?)/') as url, count(*) as cnt from frame_pid 
		where date='$date' group by pid, substring(referrer from '://((?:(?!://).)+?)/'), date order by cnt desc";
		$data=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql=" insert into frame_refer (
		day,
		pid,
		url,
		cnt)
		select ?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM frame_refer WHERE day=? and pid=? and url=? )
		;";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update frame_refer 
		set cnt=cnt+?
		WHERE day=? and pid=? and url=?
		";
		$sthUpdatePids=$pdo->prepare($sql);
		foreach ($data as $d){
			$sthUpdatePids->execute([$d['cnt'],$d['day'],$d['pid'],$d['url']]);
			$sthInsertPids->execute([$d['day'],$d['pid'],$d['url'],$d['cnt'],$d['day'],$d['pid'],$d['url']]);
		}
    }
}
