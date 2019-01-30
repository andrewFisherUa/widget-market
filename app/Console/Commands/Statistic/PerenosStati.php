<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;

class PerenosStati extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'PerenosStati';
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
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select * from pid_summa order by id asc";
		$data=$pdo->query($sql)->fetchAll(\PDO::FETCH_CLASS);
		$sql="insert into pid_summa_full (pid
		,day
		,country
		,summa
		,control_summa
		,loaded
		,control_loaded
		,played
		,control_played
		,calculate
		,control_calculate
		,one_played
		,control_one_played
		,completed
		,control_completed
		,clicks
		,control_clicks
		,started
		,control_started
		,second_expensive_all
		,second_expensive
		,second_expensive_summa
		,second_cheap_all
		,second_cheap
		,second_cheap_summa
		,lease_summa)
		select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? 
		WHERE NOT EXISTS (SELECT 1 FROM pid_summa_full WHERE pid=? and day =? and country =? ) ";
		$sthInsertPids=$pdo->prepare($sql);
		foreach ($data as $d){
			if ($d->pid==752 or $d->pid==939 or $d->pid==977 or $d->pid==974 or $d->pid==973){
				$sthInsertPids->execute([
					$d->pid
					,$d->day
					,$d->country
					,0
					,$d->summa
					,0
					,$d->loaded
					,0
					,$d->played
					,0
					,$d->calculate
					,0
					,$d->played
					,0
					,$d->completed
					,0
					,$d->clicks
					,0
					,$d->started
					,$d->second_all
					,$d->second
					,$d->second_summa
					,$d->second_cheap_all
					,$d->second_cheap
					,$d->second_cheap_summa
					,$d->lease_summa
					,$d->pid
					,$d->day
					,$d->country]);
			}
			else{
				$sthInsertPids->execute([
					$d->pid
					,$d->day
					,$d->country
					,$d->summa
					,0
					,$d->loaded
					,0
					,$d->played
					,0
					,$d->calculate
					,0
					,$d->played
					,0
					,$d->completed
					,0
					,$d->clicks
					,0
					,$d->started
					,0
					,$d->second_all
					,$d->second
					,$d->second_summa
					,$d->second_cheap_all
					,$d->second_cheap
					,$d->second_cheap_summa
					,$d->lease_summa
					,$d->pid
					,$d->day
					,$d->country]);
			}
		}
		var_dump('я усё');
    }
}
