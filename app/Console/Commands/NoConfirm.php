<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\NoActive;

class NoConfirm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'no_confirm';

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
		$confirms=\App\ConfirmUser::all();
		foreach ($confirms as $confirm){
			if (date('Y-m-d H:i:s')>date("Y-m-d H:i:s", strtotime($confirm->updated_at ."+"."24"." HOURS"))){
				$user=\App\User::where('email', $confirm->email)->first();
				$userProfile=\App\UserProfile::where('email', $confirm->email)->first();
				if ($user){
					#$user->delete();
				}
				if ($userProfile){
					#$userProfile->delete();
				}
				#$confirm->delete();
			}
		}
	}
}
