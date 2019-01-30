<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\NoActive;

class ProductBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product_bot';

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
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$widgets=\App\MPW\Widgets\Widget::where('type', '1')->get();
		foreach ($widgets as $widget){
			if ($widget->type==3){
				continue;
			}
			$sql="select t1.id_widget, t1.url, count(t1.page_key) as showed,
			case when (t2.showed>0) then round(count(t1.page_key)/t2.showed::numeric,4)*100 else 0 end as precent_showed
			from advert_stat_pages t1 left join 
			(select id_widget, count(page_key) as showed from advert_stat_pages where day='$date' and id_widget='$widget->id' 
			group by id_widget) 
			t2 on t1.id_widget=t2.id_widget
		where t1.id_widget='$widget->id' and char_length(t1.url)>0 and day='$date' group by t1.id_widget, t1.url, t2.showed";
			$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
			if ($stat['showed']<500){
				continue;
			}
			if ($stat['precent_showed']>30){
				if (!\App\User::find($widget->user_id)->hasRole('affiliate')){
					continue;
				}
				$user=\App\User::find($widget->user_id);
				if (!$user){
					continue;
				}
				$manager=$user->profile->manager;
				if (!$manager){
					$manager=16;
				}
				$username=$user->name;
				$widgetid=$stat['id_widget'];
				$url=$stat['url'];
				$notif_header="Больше 30% трафика с одной страницы (Товарка)";
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
