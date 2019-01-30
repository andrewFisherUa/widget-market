<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;
use App\AllNotification;
use App\Notifications\NoActive;
use App\Notifications\UserDown;

class UserActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_active';

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
         $dbUser=env('DB_USERNAME');
         $dbPass=env('DB_PASSWORD');

		
		$users=\App\UserProfile::all();
		$from=date('Y-m-d',time()-3600*24*14);
		$to=date('Y-m-d');
		$pdo = \DB::connection()->getPdo();
		$sql="create temp table proverka_users as select t1.user_id, t1.manager, t1.status, coalesce(t5.loaded,0) as video_loaded, 
		coalesce(t6.loaded,0) as product_played 
		from user_profiles t1 
		left join (select id, pad, user_id from widgets) t2 on t1.user_id=t2.user_id
		left join (select id, wid_id from widget_videos) t3 on t2.id=t3.wid_id
		left join (select id, wid_id from widget_products) t4 on t2.id=t4.wid_id
		left join (select id, wid_id from widget_tizers) t7 on t2.id=t7.wid_id
		
		left join (SELECT p.* FROM dblink ('dbname=videotest port=5432 host=localhost user=".$dbUser." password=".dbPass."', 
		'select pid, sum(loaded+control_loaded) as loaded from pid_summa_full where day between ''$from'' and ''$to'' group by pid') AS p(pid int,  loaded int)) 
		t5 on t3.id=t5.pid

		left join (SELECT p.* FROM dblink ('dbname=statistic_market_place port=5432 host=localhost user=".$dbUser." password=".$dbPass."', 
		'select pid, sum(coalesce(yandex_views, 0)+coalesce(ta_views, 0)+coalesce(ts_views, 0)) as loaded from wid_calculate where day between ''$from'' and ''$to'' 
		group by pid') AS p(pid int, loaded int)) t6 on t4.wid_id=t6.pid or t7.wid_id=t6.pid";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$allSums=\DB::table('proverka_users')->select(\DB::raw('user_id, manager, status, sum(coalesce(video_loaded,0)) as video,
		sum(coalesce(product_played,0)) as product'))->groupBy('user_id', 'manager', 'status')->orderBy('user_id')->get();
		foreach ($users as $user){
			if ($user->user_id==1 or $user->user_id==2 or $user->user_id==16 or $user->user_id==37 or $user->user_id==39){
				continue;
			}
			if (date('Y-m-d') < date("Y-m-d",strtotime("$user->created_at + "."14"." DAYS"))){
				continue;
			}		
			$sum=\DB::table('proverka_users')->select(\DB::raw('user_id, manager, status, sum(coalesce(video_loaded,0)) as video,
			sum(coalesce(product_played,0)) as product'))->where('user_id', $user->user_id)->groupBy('user_id', 'manager', 'status')->first();
			if (!$sum){
				continue;
			}
			if ($sum->video=='0' and $sum->product=='0'){
				if ($user->status==1){
					continue;
				}
				$user->status=1;
				$user->save();
				if (!$user->manager){
					continue;
				}
				$notif_header="Неактивный клиент";
				$user_name=$user->name;
				$body="$user_name перенесен в неактивные клиенты.";
				$notif=new AllNotification;
				$notif->user_id=$user->manager;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send(\App\User::find($user->manager), (new NoActive($notif->id)));
				$notifs=new AllNotification;
				$notifs->user_id=$user->id;
				$notifs->header='Перенос в неактивные';
				$notifs->body='Перенос в неактивные';
				$notifs->save();
				\Notification::send(\App\User::find($user->id), (new UserDown($notifs->id)));
			}
			else{
				if (!$user->status){
					continue;
				}
				$user->status=null;
				$user->save();
				if (!$user->manager){
					continue;
				}
				$notif_header="Клиент начал работать";
				$user_name=$user->name;
				$body="$user_name перенесен в активные клиенты, так как у него пошли загрузки.";
				$notif=new AllNotification;
				$notif->user_id=$user->manager;
				$notif->header=$notif_header;
				$notif->body=$body;
				$notif->save();
				\Notification::send(\App\User::find($user->manager), (new NoActive($notif->id)));
			}
		}
		
		
		/*foreach ($noActives as $noActive){
			if (date('Y-m-d H:i:s')<date("Y-m-d H:i:s", strtotime($noActive->user->updated_at ."+"."7"." DAYS"))){
				if ($noActive->user->hasRole('affiliate')){
					$user_name=$noActive->user->name;
					$time=$noActive->user->updated_at;
					$notif_header="Заход неактивного клиента $user_name.";
					$body="$user_name был онлайн $time.";
					$proverka=AllNotification::where('header', $notif_header)->first();
					if (!$proverka){
						if ($noActive->manager){
							$notif=new AllNotification;
							$notif->user_id=$noActive->manager;
							$notif->header=$notif_header;
							$notif->body=$body;
							$notif->save();
							$delay = \Carbon\Carbon::now()->addseconds(10);
							\Notification::send(\App\User::find($noActive->manager), (new NoActive($notif->id))->delay($delay));
						}
					}
					else{
						if (date('Y-m-d H:i:s')>date("Y-m-d H:i:s", strtotime($proverka->created_at ."+"."7"." DAYS"))){
							if ($noActive->manager){
							$notif=new AllNotification;
							$notif->user_id=$noActive->manager;
							$notif->header=$notif_header;
							$notif->body=$body;
							$notif->save();
							$delay = \Carbon\Carbon::now()->addseconds(10);
							\Notification::send(\App\User::find($noActive->manager), (new NoActive($notif->id))->delay($delay));
							}
						}
					}
				}
			}
		}*/
	}
}
