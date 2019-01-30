<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;

class PadClid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'padclid';

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
		$sql="SELECT id, url, clid FROM mp_widgets";
		$clids = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($clids as $clid){
			$pad=\App\PartnerPad::where('domain', $clid['url'])->first();
			if (!$pad){
				/*var_dump($clid['url']);*/
				continue;
			}
			if ($pad->clid and $pad->clid!=$clid['clid'] and $clid['clid']){
				echo "<hr>";
				var_dump($pad->clid);
				var_dump($clid);
				continue;
			}
			$pad->clid=$clid['clid'];
			$pad->save();
		}
	}
}
