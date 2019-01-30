<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ParserBitcoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ParserBitcoins';

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
       //(14:09:24) programmer@jabber.alexavto.ip: key/44fd98d3e73212caa9feca870bb264f1
       //secret/031b653d82da08047c8b0ea4a3814a6d5c234c308d9de5fea4fef5d38a13c631 
		
		
		$lbtckey = '44fd98d3e73212caa9feca870bb264f1'; //ключ localbitcoins test
		$ssecret  = '031b653d82da08047c8b0ea4a3814a6d5c234c308d9de5fea4fef5d38a13c631';
        
		$time = microtime();
		$int=substr($time,11);
		$flo=substr($time,2,5);
		$nonce=$int.$flo;
		$url = '/api/ads/';
		$post_params=array(
		);
		$get_or_post_params_urlencoded=http_build_query($post_params);
		$message=$nonce.$lbtckey.$url.$get_or_post_params_urlencoded;
		
		$signature = strtoupper(hash_hmac('sha256',$message,$ssecret));
		
		
		$curl = curl_init('https://localbitcoins.net'.$url);
		$options = array(
		CURLOPT_HTTPGET => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_TIMEOUT => 45,
		CURLOPT_FILETIME => TRUE,
		//CURLOPT_INTERFACE => "185.60.135.248",
		CURLOPT_HTTPHEADER => array(
		'Apiauth-Key:'.$lbtckey,
		'Apiauth-Nonce:'.$nonce,
		'Apiauth-Signature:'.$signature
		),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		$result=json_decode($json);
		//echo $json;
		
		foreach($result as $key => $varkey){
	       foreach($varkey as $key2 => $varkey2){
		     //echo $key2."\n";
			 var_dump($varkey2);
           }		   
		}
		
		
    }
}
