<?php

namespace App\MPW\Widgets;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
     private $dbUser;
     private $dbPass;
    public function __construct()
      {
         $this->dbUser=env('DB_USERNAME');
         $this->dbPass=env('DB_PASSWORD');
      }

     protected $table = 'widgets';
	 protected $fillable = array('*');
	public function video() {
		return $this->hasOne('App\WidgetVideo', 'wid_id');
	}
	
	public function product() {
		return $this->hasOne('App\WidgetEditor', 'wid_id');
	}
	
	public function tizer() {
		return $this->hasOne('App\WidgetTizer', 'wid_id');
	}
	
	public function userProfile() {
		return $this->hasOne('App\UserProfile', 'user_id', 'user_id');
	}
	
	public function partnerPad() {
		return $this->hasOne('App\PartnerPad', 'id', 'pad');
	}
	
	public function videoCommisssion($commission){
		if(!$commission) return 0;
		//var_dump($commission); die();
		//var_dump(\DB::table('сommission_groups')->where('commissiongroupid', $commission)->first()); die();
		return \DB::table('сommission_groups')->where('commissiongroupid', $commission)->first()->value;
	}
	
	public function VideoStat($id, $from, $to){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select pid, sum(summa+control_summa) as summa, sum(lease_summa) as lease_summa, 
		sum(loaded+control_loaded) as loaded, 
		sum(calculate+control_calculate) as calculate, 
		sum(played+control_played) as played,
		sum(one_played+control_one_played) as one_played,		
		sum(completed+control_completed) as completed, 
		sum(clicks+control_clicks) as clicks, 
		sum(started+control_started) as started, 
		sum(second_expensive+second_cheap) as second, 
		sum(second_expensive_summa+second_cheap_summa) as second_summa,
		round(avg(case when coef > 0 then coef end)::numeric,2) as coef,
		case when (sum(ads_requested)>0) then round(sum(ads_viewable)/sum(ads_requested)::numeric,4)*100 else 0 end as viewable,
		case when (sum(loaded+control_loaded)>0) then round(sum(one_played+control_one_played)/sum(loaded+control_loaded)::numeric,4)*100 else 0 
		end as util, 
		case when(sum(one_played+control_one_played)>0) then round(sum(played+control_played)/sum(one_played+control_one_played)::numeric,4) 
		else 0 end as deep, 
		case when(sum(played+control_played)>0) then round(sum(completed+control_completed)/sum(played+control_played)::numeric,4)*100 else 0 end as dosm, 
		case when(sum(played+control_played)>0) then round(sum(clicks+control_clicks)/sum(played+control_played)::numeric,4)*100 else 0 end as ctr from pid_summa_full where pid='$id' and day between '$from' and '$to' group by pid";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
	public function ProductStat($id, $productId, $from, $to){
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select sum(yandex_views + ta_views + ts_views + na_views) as loaded, sum(yandex_clicks + ta_clicks + ts_clicks + na_clicks) as clicked, sum(yandex_summa + ta_summa + ts_summa +  na_summa) as summa, 
		sum(our_clicks) as our_clicks, case when 
		sum(yandex_views + ta_views + ts_views + na_views) > 0 then round(sum(yandex_clicks + ta_clicks + ts_clicks + na_clicks)/sum(yandex_views + ta_views + ts_views + na_views)::numeric,4)*100 else 0 end as ctr
		from wid_calculate where pid='$id' and day between '$from' and '$to' group by pid";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
	
	public function VideoTooltip($id){
		$pdo = \DB::connection()->getPdo();
		$sql="select t2.name from widget_videos t1 left join (SELECT p.* FROM dblink 
		('dbname=videotest port=5432 host=localhost user=".$this->dbUser." password=".$this->dbPass."', 
		'select id, name from blocks ') AS p(id int, name varchar)) t2 on t1.block_rus=t2.id or 
		t1.block_cis=t2.id or t1.block_mobil=t2.id where t1.id='$id'";
		$blocks=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);;
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select t2.title from exception t1 left join (select * from links) t2 on t1.id_src=t2.id where t1.pid='$id'";
		$exception=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);;
		$sql="select t2.title from add_links t1 left join (select * from links) t2 on t1.id_src=t2.id where t1.pid='$id'";
		$add=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);;
		$stat=[];
		$stat['block']=$blocks;
		$stat['exception']=$exception;
		$stat['add']=$add;
		return $stat;
		
	}
	
	public function BrandStat($id, $from, $to){
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select pid, sum(showed) as showed, sum(unik_showed) as unik_showed, sum(click) as click, sum(unik_click) as unik_click, case when 
		(sum(click)>0) then round(sum(click)/sum(showed)::numeric,4)*100 else 0 end as ctr, case when (sum(showed)>0) then 
		round(sum(summa)/sum(showed)*1000::numeric,4) else 0 end as cpm, sum(summa) as summa from brand_stat_pid where pid='$id' and day between 
		'$from' and '$to' group by pid";
		$stat=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch();
		return $stat;
	}
	public function getYandexCategories(){
		$cats = $this->partnerPad->widget_categories;
		if($cats){
		$smpd=explode(",",$cats);
	    if($smpd){
		  
		  return \DB::connection("cluck")->table("yandex_categories")->select("id","uniq_name")
		  ->whereIn("id",$smpd)
		  ->orderBy("uniq_name")
		  ->get();
		    //var_dump( $categories->toArray()); die();
	       }
		}
		return [];
	}	
}
