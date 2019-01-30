<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use baibaratsky\WebMoney\Signer;
use Illuminate\Support\Str;
class rt extends Command
{
	
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rt';

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
		$message=array(
			"action"=>"getfilterdata",
			"project_name"=>"market-place.su_тизерка"
		);
		
		$curl = curl_init('https://my.redtram.com/stat/ajax/');
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $message,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FILETIME => TRUE,
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		var_dump($json);
		/*curl_close($curl);
		$result=json_decode($json);
		var_dump($result);*/
		//$curl = curl_init('https://g4p.redtram.com/?i=19017&ref2=');
	}
}
