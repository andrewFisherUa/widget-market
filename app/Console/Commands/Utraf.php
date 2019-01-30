<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;

class Utraf extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:utraf';

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
	
    public function handle(Request $request)
    {
		$url='https://utraff.com/index.php?r=api/get-stat-subid';
		$array_message=array(
			'key'=>'vg-7xpOjlBX4Vs4-AbUDuf3AD1aBXeMr',
			'start_date'=>'2018-03-28',
			'end_date'=>'2018-03-28',
			'host'=>'market-place.su',
		);
		$curl = curl_init($url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
			CURLOPT_POSTFIELDS => $array_message
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$result=json_decode($json);
		$p=0;
		foreach ($result as $r){
			foreach ($r as $q){
				foreach ($q as $s){
					echo $s->subid . "   " . $s->views . "   " . $s->amount;
					echo "\n";
				}
			}
		}
	}
}
