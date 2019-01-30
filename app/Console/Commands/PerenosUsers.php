<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MPW\Sources\VideoSource;
use App\User;
use App\PartnerPad;
use App\UserProfile;

class PerenosUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perenosusers';

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
		$sql="SELECT t1.user_email, t2.email FROM referers t1 left join (select * from user_profile) t2 on t1.referer_id=t2.aff_id where t1.user_affiliate_id>0";
		$sources = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($sources as $source){
			$user=\App\UserProfile::where('email', $source['user_email'])->first();
			if (!$user){
				continue;
			}
			$refer=\App\UserProfile::where('email', $source['email'])->first();
			if (!$refer){
				continue;
			}
			$user->referer=$refer->user_id;
			$user->save();
		}
		/*foreach ($sources as $source){
			if ($source['email']=='chagasergey1501@gmail.com'){
				continue;
			}
			$user=User::firstOrNew(['email'=>$source['email']]);
			$user->name=$source['name'];
			//$user->email=$source['email'];
			$user->password=$source['password'];
			$user->status='1';
			$user->save();
			if ($source['email']=='manager@automediya.ru'){
				$user->detachRole('5');
				$user->attachRole('5');
			}
			else if ($source['email']=='DomSmile@yandex.ru'){
				$user->detachRole('3');
				$user->attachRole('3');
			}
			else if ($source['email']=='Katerina@market-place.su'){
				$user->detachRole('3');
				$user->attachRole('3');
			}
			else if ($source['email']=='ti_ne_proidesh@mail.ru'){
				$user->detachRole('3');
				$user->attachRole('3');
			}
			else if ($source['email']=='Pavel@market-place.su'){
				$user->detachRole('4');
				$user->attachRole('4');
			}
			else{
				$user->detachRole('1');
				$user->attachRole('1');
			}
			$userProfile=UserProfile::firstOrNew(['id'=>$user->id, 'email'=>$source['email']]);
			$userProfile->user_id=$user->id;
			$userProfile->name=$source['name'];
			if (empty($source['firstname'])){
				$userProfile->firstname='пустое имя';
			}
			else{
				$userProfile->firstname=$source['firstname'];
			}
			if (empty($source['lastname'])){
				$userProfile->lastname='пустая фамилия';
			}
			else{
			$userProfile->lastname=$source['lastname'];
			}
			//$userProfile->email=$source['email'];
			$userProfile->phone=$source['phone'];
			$userProfile->icq=$source['icq'];
			$userProfile->skype=$source['skype'];
			$userProfile->id_for_pads=$source['id_for_pads'];
			if ($source['id_manager']==1){
				$userProfile->manager=16;
			}
			else if ($source['id_manager']==3){
				$userProfile->manager=2;
			}
			else if ($source['id_manager']==13){
				$userProfile->manager=37;
			}
			else if ($source['id_manager']==14){
				$userProfile->manager=39;
			}
			else if ($source['id_manager']==15){
				$userProfile->manager=38;
			}
			else {
				$userProfile->manager=null;
			}
			if (strlen($source['aff_id'])>13){
				$userProfile->refer_id=null;
			}
			else{
				$userProfile->refer_id=$source['aff_id'];
			}
			$userProfile->save();
			var_dump("Записал юзера");
		}
		
		$sqli="SELECT t1.id as id_pad, t1.aff_id, t1.domain, t1.tag_id, t1.stcurl, t1.stclogin, t1.ststpassword, t1.id_categories, t1.datetime, t1.status, t1.datetime FROM `partner_pads` t1 left join (select * from partner_affiliate) t2 on t1.aff_id=t2.id left join (select * from users where not last_login_ip is null and not last_login_ip='0.0.0.0') t3 on t2.affiliate_id=t3.aff_id where not t3.aff_id is null";
		$sourc = $pdo->query($sqli)->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach ($sourc as $sour){
			if ($sour['aff_id']==109){
				continue;
			}
			if ($sour['aff_id']==340){
				continue;
			}
			if ($sour['aff_id']==346){
				continue;
			}
			if ($sour['aff_id']==328){
				continue;
			}
			if ($sour['aff_id']==319){
				continue;
			}
			if ($sour['aff_id']==321){
				continue;
			}
			if ($sour['aff_id']==342){
				continue;
			}
			$pad=PartnerPad::firstOrNew(['domain'=>$sour['domain']]);
			$pad->stcurl=$sour['stcurl'];
			$pad->stclogin=$sour['stclogin'];
			$pad->stcpassword=$sour['ststpassword'];
			if ($sour['status']==1){
				$pad->status=2;
				$pad->type=null;
				$pad->id_categories=null;
				$pad->video_categories=null;
			}
			else if ($sour['status']==2){
				$pad->status=1;
				$pad->type=1;
				$pad->id_categories=$sour['id_categories'];
				$pad->video_categories=null;
			}
			else if ($sour['status']==16){
				$pad->status=1;
				$pad->type=2;
				$pad->id_categories=null;
				$pad->video_categories=$sour['tag_id'];
			}
			else if ($sour['status']==18){
				$pad->status=1;
				$pad->type=3;
				$pad->id_categories=$sour['id_categories'];
				$pad->video_categories=$sour['tag_id'];
			}
			else{
				continue;
			}
			var_dump($pad->domain);
			$user_for_pad=UserProfile::where('id_for_pads', $sour['aff_id'])->first();
			$pad->user_id=$user_for_pad->user_id;
			if ($sour['datetime']){
			$pad->created_at=$sour['datetime'];
			}
			else{
			$pad->created_at='2016-01-01 00:00:00';
			}
			$pad->save();
			var_dump("Записал площадку");
		}*/
	}
}
