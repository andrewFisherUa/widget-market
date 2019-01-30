<?php

namespace App\BrandStat;

//use Illuminate\Database\Eloquent\Model;

class Stat //extends Model
{
    private static $instance=null;
	private $attribs=[];
	private $days=[];

    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	}
	return self::$instance;
	}
	public function getData($date){
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$sql="select t1.pid, t1.day, t1.offer_id, t1.country, count(t1.page_key) as showed, count(distinct (t1.ip)) as unik_showed, coalesce(t2.click,0) 
		as click, coalesce(t2.unik_click,0) as unik_click from brand_stat_pages t1 left join (select id_widget, date, offer_id, country, 
		count(distinct (page_key)) as click, count(distinct (ip)) as unik_click from brand_stat_clicks where date='$date' group by id_widget, date, 
		offer_id, country) t2 on t1.pid=t2.id_widget and t1.offer_id=t2.offer_id and t1.country=t2.country where t1.day='$date' group by t1.pid, 
		t1.country, t1.day, t1.offer_id, t2.click, t2.unik_click";
		$stats = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into brand_stat (pid,day,id_offer,country,showed,unik_showed,click,unik_click)
			select ?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM brand_stat WHERE pid=? and day=? and id_offer=? and country=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update brand_stat set showed=?, unik_showed=?, click=?, unik_click=? 
			WHERE pid=? and day=? and id_offer=? and country=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($stats as $stat){
			$sthUpdate->execute([$stat['showed'], $stat['unik_showed'], $stat['click'], $stat['unik_click'], $stat['pid'], $stat['day'], $stat['offer_id'], $stat['country']]);
			$sthInsert->execute([$stat['pid'], $stat['day'], $stat['offer_id'], $stat['country'], $stat['showed'], $stat['unik_showed'], $stat['click'], $stat['unik_click'], $stat['pid'], $stat['day'], $stat['offer_id'], $stat['country']]);
		}
	}

	
}
