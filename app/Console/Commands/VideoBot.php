<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\NoActive;

class VideoBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video_bot';

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
		$users=\App\UserProfile::all();
		$date=date('Y-m-d',time()-3600*24);
		$pdo = \DB::connection('videotest')->getPdo();
		$widgets=\App\WidgetVideo::all();
		foreach ($widgets as $widget){
			if ($widget->type==3){
				continue;
			}
			$sql="select t1.pid, t1.url, sum(t1.played) as played, 
			case when (t2.played>0) then round(sum(t1.played)/t2.played::numeric,4)*100 else 0 end as percent_played
			from stat_user_pages t1 
			left join (select pid, sum(played) as played
			from stat_user_pages where pid='$widget->id' and day='$date' group by pid, day) t2 on t1.pid=t2.pid where t1.pid='$widget->id' and 
			t1.day='$date' group by t1.pid, t1.url, t2.played order by percent_played desc limit 1";
			$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($stat['played']<500){
				continue;
			}
			if ($stat['percent_played']>30){
				if (!\App\User::find($widget->widget->user_id)->hasRole('affiliate')){
					continue;
				}
				$user=\App\User::find($widget->widget->user_id);
				if (!$user){
					continue;
				}
				$manager=$user->profile->manager;
				if (!$manager){
					$manager=39;
				}
				$username=$user->name;
				$widgetid=$stat['pid'];
				$url=$stat['url'];
				$notif_header="Больше 30% трафика с одной страницы (Видео)";
				$user_name=$user->name;
				$body="$user_name, виджет id $widgetid, льет трафик со страницы $url .";
				$notif=new AllNotification;
				$notif->user_id=$manager;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send(\App\User::find($manager), (new NoActive($notif->id)));
			}
			
		}
		var_dump($date);
	}
}
