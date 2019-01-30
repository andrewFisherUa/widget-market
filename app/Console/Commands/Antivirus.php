<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Antivirus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anti:virus';

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
          #$cmd='find  /home/www/fitburg.ru/ -wholename "*.php" -perm 777';
		  $cmd='find  /home/www/api.market-place.su/ -wholename "*.php"';
		  #$cmd="ls -la ";
		  exec ( $cmd, $r);
		  foreach($r as $t){
			  $wf=file($t);
			  foreach($wf as $w){
				  //var_dump($w);
			  }
			  if(preg_match('/\<\?php(!=\?\>)+\<\?php\n/ui',$w,$m)){
				  print $t."\n"; 
				  var_dump($m);
				  
			  }  
			  /*
			  $fc =file_get_contents($t);
			  if(preg_match('/\<\?php.+\<\?php/ui',$fc,$m)){
				  print $t."\n"; 
				  $fc=preg_replace('/\<\?php.+\<\?php/ui','<?php ',$fc);
				  file_put_contents($t,$fc);
				  $c1="chmod 775 $t";
				  `$c1`;
				   $c1="chown www-data $t";
				  `$c1`;
               sleep(1);
			   */
			  #}


			  
		     
		  }
		  
    }
}
