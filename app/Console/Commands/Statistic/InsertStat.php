<?php

namespace App\Console\Commands\Statistic;

use Illuminate\Console\Command;
use Exception;
use Illuminated\Console\WithoutOverlapping;
class InsertStat extends Command
{
use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:insert_stat {date?}';

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
		$date= $this->argument('date');
		if(!$date){
			$date=date("Y-m-d");
		}
		\App\MPW\Statistic\Video\StatisticPads::getInstance()->isertStat($date);
		var_dump('прошел пады');
		\App\MPW\Statistic\Video\StatisticPadsOnPid::getInstance()->isertStat($date);
		var_dump('прошел пад он пид');
		\App\MPW\Statistic\Video\StatisticBlocks::getInstance()->isertStat($date);
		var_dump('прошел блок');
		\App\MPW\Statistic\Video\StatisticPids::getInstance()->isertStat($date);
		var_dump('прошел пид');
	}
}
