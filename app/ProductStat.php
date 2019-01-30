<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStat extends Model
{
	public function insertCalculate($date){
         $dbUser=env('DB_USERNAME');
         $dbPass=env('DB_PASSWORD');


		$pdo=\DB::connection("pgstatistic")->getPdo();
		$sql="select t1.*, t2.id as wid_id, t2.user_id as user_id, t7.dop_status as dop_status, coalesce(t4.commission, 1) as yandex_cof, coalesce(t5.commission, 1) as 
ta_cof, 
		coalesce(t6.cnt,0) as our_clicks from wid_summa t1 left join 
		(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$dbUser." password=".$dbPass."', 
		'select id, user_id from 
		widgets') AS p(id int, user_id int)) t2 on t1.pid=t2.id left join 
(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$dbUser." password=".$dbPass."', 
		'select user_id, commission from 
		product_default_on_users where driver=''1''') AS p(user_id int, commission numeric(10,4))) t4 on t2.user_id=t4.user_id left join 
(SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$dbUser." password=".$dbPass."', 
		'select user_id, commission from 
		product_default_on_users where driver=''2''') AS p(user_id int, commission numeric(10,4))) t5 on t2.user_id=t5.user_id 
		left join (select id_widget, count(page_key) as cnt from advert_stat_clicks where date='$date' group by id_widget) t6 on t1.pid=t6.id_widget 
		left join (SELECT p.* FROM dblink ('dbname=precluck_market_place port=5432 host=localhost user=".$dbUser." password=".$dbPass."', 
		'select user_id, dop_status from user_profiles') AS p(user_id int, dop_status int)) t7 on t2.user_id=t7.user_id 

		where t1.day='$date'";
		$widgets = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		$yandex_com=\DB::table('сommission_groups')->where('commissiongroupid', 'p-000001')->first();
		$ta_com=\DB::table('сommission_groups')->where('commissiongroupid', 'p-000002')->first();
		$insert="";
		$sql="insert into wid_calculate (pid,day,yandex_views,yandex_clicks,yandex_summa,ta_views,ta_clicks,ta_summa,ts_views,ts_clicks,ts_summa,na_views,na_clicks,na_summa,our_clicks)
			select ?,?,?,?,?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM wid_calculate WHERE pid=? and day=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update wid_calculate set yandex_views=?, yandex_clicks=?, yandex_summa=?, ta_views=?, ta_clicks=?, ta_summa=?,
		ts_views=?,ts_clicks=?,ts_summa=?,na_views=?,na_clicks=?,na_summa=?,our_clicks=?
		  WHERE pid=? and day=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($widgets as $wid){
				#if($wid)
			if ($wid['user_id']!='6'){
				$wid['yandex_views']=round($wid['yandex_views']*$wid['yandex_cof'],0);
				$wid['yandex_clicks']=round($wid['yandex_clicks']*$wid['yandex_cof'],0);
				$wid['yandex_summa']=$wid['yandex_summa']*$wid['yandex_cof'];
			
				$wid['ta_views']=round($wid['ta_views']*$wid['ta_cof'],0);
				$wid['ta_clicks']=round($wid['ta_clicks']*$wid['ta_cof'],0);
				$wid['ta_summa']=$wid['ta_summa']*$wid['ta_cof'];
				
				$wid['ts_views']=round($wid['ts_views']*$wid['ta_cof'],0);
				$wid['ts_clicks']=round($wid['ts_clicks']*$wid['ta_cof'],0);
				$wid['ts_summa']=$wid['ts_summa']*$wid['ta_cof'];
			}
			if ($wid['user_id']=='56'){
				//$wid['yandex_summa']=$wid['yandex_clicks']*2.5;
				$wid['ta_summa']=$wid['ta_clicks']*2.5;
				if ($wid['dop_status']==3){
					$wid['ts_summa']=$wid['ts_summa']*0.5;
				}
				elseif ($wid['dop_status']==2){
					$wid['ts_summa']=$wid['ts_summa']*0.65;
				}else{
				$wid['ts_summa']=$wid['ts_summa']*$ta_com->value;
				}
			}
			else if ($wid['user_id']=='6'){
				$wid['yandex_summa']=$wid['yandex_summa']*$wid['yandex_cof'];
				$wid['ta_summa']=$wid['ta_summa']*$wid['ta_cof'];
				if ($wid['dop_status']==3){
					$wid['ts_summa']=$wid['ts_summa']*0.5;
				}
				elseif ($wid['dop_status']==2){
					$wid['ts_summa']=$wid['ts_summa']*0.65;
				}else{
				$wid['ts_summa']=$wid['ts_summa']*$ta_com->value;
				}
			}
			else if ($wid['user_id']=='360'){
				$wid['ta_summa']=$wid['ta_summa']*0.5;
				$wid['yandex_summa']=$wid['yandex_summa']*0.5;
				$wid['ts_summa']=$wid['ts_summa']*0.5;
			}
			else{
				$wid['yandex_summa']=$wid['yandex_summa']*$yandex_com->value;
				$wid['ta_summa']=$wid['ta_summa']*$ta_com->value;
				if ($wid['dop_status']==3){
					$wid['ts_summa']=$wid['ts_summa']*0.5;
				}
				elseif ($wid['dop_status']==2){
					$wid['ts_summa']=$wid['ts_summa']*0.65;
				}else{
				$wid['ts_summa']=$wid['ts_summa']*$ta_com->value;
				}
			}
			$sthUpdate->execute([$wid['yandex_views'], $wid['yandex_clicks'], $wid['yandex_summa'], $wid['ta_views'], $wid['ta_clicks'], $wid['ta_summa'], $wid['ts_views'], $wid['ts_clicks'], $wid['ts_summa'],$wid['na_views'], $wid['na_clicks'], $wid['na_summa'], $wid['our_clicks'], $wid['pid'], $wid['day'],]);
			$sthInsert->execute([$wid['pid'], $wid['day'], $wid['yandex_views'], $wid['yandex_clicks'], $wid['yandex_summa'], $wid['ta_views'], $wid['ta_clicks'], $wid['ta_summa'], $wid['ts_views'], $wid['ts_clicks'], $wid['ts_summa'],$wid['na_views'], $wid['na_clicks'], $wid['na_summa'], $wid['our_clicks'], $wid['pid'], $wid['day']]);
		}
	}
}
