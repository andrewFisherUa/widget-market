<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\ActiveUserTr;

class UserActiveDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_active_down';

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
		$users=\App\UserProfile::All();
		foreach ($users as $user){
			$yesterday=\App\Transactions\UserTransaction::where('user_id', $user->user_id)->where('day', date('Y-m-d',time()-3600*24))->first();
			$before_yesterday=\App\Transactions\UserTransaction::where('user_id', $user->user_id)->where('day', date('Y-m-d',time()-3600*48))->first();
			if ($before_yesterday){
				$b_commission=$before_yesterday->video_commission+$before_yesterday->product_commission+$before_yesterday->teaser_commission;
				if ($yesterday){
					$y_commission=$yesterday->video_commission+$yesterday->product_commission+$yesterday->teaser_commission;
					if ($y_commission<30){
						var_dump('пропустил');
						continue;
					}
					if ($y_commission>0){
						$dolya=$b_commission/$y_commission;
					}
					else{
						$dolya=1;
					}
					if ($dolya>'2'){
						var_dump($user->name);
						if ($user->manager){
							$notif_header="$user->name просада по стате в 2 раза.";
							$body="У юзера $user->name стата за вчера в 2 раза меньше чем за позавчера.";
							$notif=new AllNotification;
							$notif->user_id=$user->manager;
							$notif->header=$notif_header;
							$notif->body=$body;
							$notif->save();
							$delay = \Carbon\Carbon::now()->addseconds(10);
							\Notification::send(\App\User::find($user->manager), (new ActiveUserTr($notif->id))->delay($delay));
						}
					}
				}
				else{
					if ($b_commission<30){
						var_dump('пропустил');
						continue;
					}
					var_dump($user->name);
					if ($user->manager){
						$notif_header="$user->name снял виджет либо не идет стата.";
						$body="У юзера $user->name нет статы за вчера.";
						$notif=new AllNotification;
						$notif->user_id=$user->manager;
						$notif->header=$notif_header;
						$notif->body=$body;
						$notif->save();
						$delay = \Carbon\Carbon::now()->addseconds(10);
						\Notification::send(\App\User::find($user->manager), (new ActiveUserTr($notif->id))->delay($delay));
					}
				}
			}
			else{
				continue;
			}
		}
	}
}
