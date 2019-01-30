<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Carbon\Carbon;

class CalculatorB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'statistic:calculatorB';
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
		$newT=new \App\Videosource\CalculatorB(); 
		$newT->StartDay();
    }
}
