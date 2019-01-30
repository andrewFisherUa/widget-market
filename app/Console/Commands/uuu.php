<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\NoActive;

class uuu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uuu';

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
		$users=\App\UserProfile::get();
		$from=date('Y-m-d',time()-3600*24*14);
		$to=date('Y-m-d');
		$sql="create temp table summa as select user_id, sum(commission) as commission from user_transaction_logs where day between '$from' and '$to' group by user_id";
		$pdo = \DB::connection()->getPdo();
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$cnt=0;
		foreach ($users as $user){
			$qwe=\DB::table('summa')->where('user_id', $user->user_id)->first();
			if (!$qwe){
				$cnt+=1;
				print $user->name."\n";
			}
		}
		var_dump($cnt);
	}
}
