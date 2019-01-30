<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;

class Videosources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videosources';

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
		$pdo=\DB::connection("mysqlapi")->getPdo();
		$sql="select * from video_ad_sources";
		$sources = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($sources as $source){
			$video_source=VideoSource::firstOrNew(['id'=>$source['id']]);
			$video_source->src=$source['src'];
			$video_source->limit=$source['v_limit'];
			$video_source->timeout=$source['v_timeout'];
			$video_source->title=$source['title'];
			$video_source->client_title=$source['client_title'];
			$video_source->ao=$source['ao'];
			$video_source->category=$source['category'];
			$video_source->summa_rus=$source['contr_sum_r'];
			$video_source->summa_cis=$source['contr_sum_f'];
			$video_source->cheap=$source['cheap'];
			$video_source->save();
			var_dump("Записал");
		}
    }
}
