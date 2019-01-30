<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Carbon\Carbon;

class Calculator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'statistic:calculator {date?}';
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
      $date= $this->argument('date'); //дата используется для персчета за конкретную дату.
		if(!$date){
			$date=date("Y-m-d"); //если дата не указана то берется текущая дата
		}
		$today = Carbon::now(); //получаем текущую дату и время
		if($today->hour<1){ //если еще нет часа ночи
			$date = $today->yesterday()->format('Y-m-d'); //получаем дату вчерашнюю
		}
		//$new=new \App\Videosource\Calculator(); 
		//$new->StartDay($date,$date);
		
		
		$newT=new \App\Videosource\CalculatorT(); 
		$newT->StartDay($date,$date);
    }
}
