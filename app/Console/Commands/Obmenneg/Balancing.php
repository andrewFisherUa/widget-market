<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;


class Balancing extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:balancing';

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
		
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select id_ad, contact_id, amount, amount_btc, round(amount/amount_btc::numeric,2) as course, 
		case when id_ad='609849' then round(amount/amount_btc::numeric,2)*1.02 else round(amount/amount_btc::numeric,2)*0.99 end as course_fact,
		case when id_ad='609849' then round(amount/amount_btc::numeric,2)*1.02*amount_btc else round(amount/amount_btc::numeric,2)*0.99*amount_btc end
		as amount_fact,created_at from local_robots where id_ad in ('617372', '609849') and status='9' order by created_at asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into local_balancing_v2 (id_ad, contact_id, amount, amount_btc, course, course_fact, amount_fact, remainder, created)
			select ?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM local_balancing_v2 WHERE contact_id=?)";
		$sthInsert=$pdo->prepare($sql);
		foreach ($stats as $stat){
			$sthInsert->execute([$stat['id_ad'], $stat['contact_id'], $stat['amount'], $stat['amount_btc'], $stat['course'], $stat['course_fact'], 
			$stat['amount_fact'], $stat['amount_btc'], $stat['created_at'], $stat['contact_id']]);
		}
		$sql="select * from local_balancing_v2 where id_ad='609849' and status='0' order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		$sql="update local_balancing_v2 set status=?, remainder=?, details=?
			WHERE contact_id=?";
		$sthUpdate=$pdo->prepare($sql);
		
		$sql="update local_balancing_v2 set status=?, remainder=?, details=?, return_course=?, profit=?
			WHERE contact_id=?";
		$sthUpdated=$pdo->prepare($sql);
		
		$sql="update local_balancing_v2 set status=?, remainder=? 
			WHERE contact_id=?";
		$sthUpdatedd=$pdo->prepare($sql);
		
		foreach ($buys as $buy){
			$i=1;
			$sells=\DB::connection('obmenneg')->table('local_balancing_v2')->where('id_ad', '617372')->where('status', '0')->orderBy('created', 'asc')->get();
			$remainder_buy=$buy['remainder'];
			while($i<=count($sells)){
				$sql="select * from local_balancing_v2 where id_ad='617372' and status='0' order by created asc";
				$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
				$remainder_sell=$sell['remainder'];
				$details=$sell['details'];
				if ($buy['created']>$sell['created']){
					$remainder=$remainder_sell*100/$sell['amount_btc'];
					$details=$details .  "" . round($remainder,6) . "% - продаж без закупок;";
					$sthUpdate->execute(['3',$remainder_sell,$details,$sell['contact_id']]);
				}
				else{
					if ($remainder_buy>$remainder_sell){
						$remainder=$remainder_sell*100/$sell['amount_btc'];						
						$details=$details .  "" . round($remainder,6) . "% - " . $buy['course_fact'] . ";";
						$courses=explode(";", $details);
						$sum=0;
						$procents=0;
						$allCourse=0;
						$countCourses=0;
						foreach ($courses as $course){
							$courDet=explode(" - ", $course);
							if (count($courDet)==2){
								$pr=preg_replace("/[^.0-9]/", '', $courDet[0]);
								$sm=preg_replace("/[^.0-9]/", '', $courDet[1]);
								if (!$sm){
									continue;
								}
								$procents+=$pr;
								$allCourse+=$sm;
								$countCourses++;
								$kk=$pr*$sm/100;
								$sum+=$kk;
							}
						}
						$ostPr=100-$procents;
						if ($ostPr>0){
							$srCourse=$allCourse/$countCourses;
							$srSum=$ostPr*$srCourse/100;
							$sum+=$srSum;
						}
						$remainder_buy=$remainder_buy-$remainder_sell;
						$remainder=$remainder_sell*100/$buy['remainder'];
						$profit=$sell['amount']-$sum*$sell['amount_btc'];
						$sthUpdated->execute(['1','0',$details,$sum,$profit,$sell['contact_id']]);
						$sthUpdatedd->execute(['0',$remainder_buy,$buy['contact_id']]);
					}
					else{
						$remainder=$remainder_buy*100/$sell['amount_btc'];		
						$details=$details .  "" . round($remainder,6) . "% - " . $buy['course_fact'] . ";";
						$courses=explode(";", $details);
						$sum=0;
						$procents=0;
						$allCourse=0;
						$countCourses=0;
						foreach ($courses as $course){
							$courDet=explode(" - ", $course);
							if (count($courDet)==2){
								$pr=preg_replace("/[^.0-9]/", '', $courDet[0]);
								$sm=preg_replace("/[^.0-9]/", '', $courDet[1]);
								if (!$sm){
									continue;
								}
								$procents+=$pr;
								$allCourse+=$sm;
								$countCourses++;
								$kk=$pr*$sm/100;
								$sum+=$kk;
							}
						}
						$ostPr=100-$procents;
						if ($ostPr>0){
							$srCourse=$allCourse/$countCourses;
							$srSum=$ostPr*$srCourse/100;
							$sum+=$srSum;
						}
						$remainder_sell=$remainder_sell-$remainder_buy;
						$profit=$sell['amount']-$sum*$sell['amount_btc'];
						$sthUpdated->execute(['0',$remainder_sell,$details,$sum,$profit,$sell['contact_id']]);
						$sthUpdatedd->execute(['1',$buy['amount_btc'],$buy['contact_id']]);
						break;
					}
				}
				$i++;
			}
		}
		
		$sql="select * from local_balancing_v2 where id_ad='617372' and status in ('0','3') order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach ($buys as $buy){
			$i=1;
			$sells=\DB::connection('obmenneg')->table('local_balancing_v2')->where('id_ad', '609849')->where('remainder', '>', '0')->orderBy('created', 'asc')->get();
			$remainder_buy=$buy['remainder'];
			while($i<=count($sells)){
				$sql="select * from local_balancing_v2 where id_ad='609849' and remainder>'0' order by created asc";
				$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
				if ($sell){
					$remainder_sell=$sell['remainder'];
					$details=$sell['details'];
					if ($buy['created']>$sell['created']){
						$remainder=$remainder_sell*100/$sell['amount_btc'];
						$details=$details .  "" . round($remainder,6) . "% - покупок без продаж;";
						$sthUpdate->execute(['1','0',$details,$sell['contact_id']]);
					}
					else{
						if ($remainder_buy>$remainder_sell){
							$remainder=$remainder_sell*100/$sell['amount_btc'];						
							$details=$details .  "" . round($remainder,6) . "% - " . $buy['course_fact'] . ";";
							$courses=explode(";", $details);
							$sum=0;
							$procents=0;
							$allCourse=0;
							$countCourses=0;
							foreach ($courses as $course){
								$courDet=explode(" - ", $course);
								if (count($courDet)==2){
									$pr=preg_replace("/[^.0-9]/", '', $courDet[0]);
									$sm=preg_replace("/[^.0-9]/", '', $courDet[1]);
									if (!$sm){
										continue;
									}
									$procents+=$pr;
									$allCourse+=$sm;
									$countCourses++;
									$kk=$pr*$sm/100;
									$sum+=$kk;
								}
							}
							$ostPr=100-$procents;
							if ($ostPr>0){
								$srCourse=$allCourse/$countCourses;
								$srSum=$ostPr*$srCourse/100;
								$sum+=$srSum;
							}
							$remainder_buy=$remainder_buy-$remainder_sell;
							$remainder=$remainder_sell*100/$buy['remainder'];
							$profit=$sum*$sell['amount_btc']-$sell['amount'];
							$sthUpdated->execute(['1','0',$details,$sum,$profit,$sell['contact_id']]);
							$sthUpdatedd->execute(['3',$remainder_buy,$buy['contact_id']]);
						}
						else{
							$remainder=$remainder_buy*100/$sell['amount_btc'];		
							$details=$details .  "" . round($remainder,6) . "% - " . $buy['course_fact'] . ";";
							$courses=explode(";", $details);
							$sum=0;
							$procents=0;
							$allCourse=0;
							$countCourses=0;
							foreach ($courses as $course){
								$courDet=explode(" - ", $course);
								if (count($courDet)==2){
									$pr=preg_replace("/[^.0-9]/", '', $courDet[0]);
									$sm=preg_replace("/[^.0-9]/", '', $courDet[1]);
									if (!$sm){
										continue;
									}
									$procents+=$pr;
									$allCourse+=$sm;
									$countCourses++;
									$kk=$pr*$sm/100;
									$sum+=$kk;
								}
							}
							$ostPr=100-$procents;
							if ($ostPr>0){
								$srCourse=$allCourse/$countCourses;
								$srSum=$ostPr*$srCourse/100;
								$sum+=$srSum;
							}
							$remainder_sell=$remainder_sell-$remainder_buy;
							$profit=$sum*$sell['amount_btc']-$sell['amount'];
							$sthUpdated->execute(['1',$remainder_sell,$details,$sum,$profit,$sell['contact_id']]);
							$sthUpdatedd->execute(['1',$buy['amount_btc'],$buy['contact_id']]);
							break;
						}
					}
				}
				$i++;
				
			}
		}
		
		$sql="select sum(remainder) as amount_btc from local_balancing_v2 where id_ad='617372' and status in ('0', '3') group by id_ad";
		$qiwi_sum=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select * from local_balancing_v2 where id_ad='617372' and status in ('0','3') order by created asc";
		$qiwis=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$qiwi_actual=0;
		foreach ($qiwis as $qiwi){
			$procent=$qiwi['remainder'] / $qiwi_sum['amount_btc'];
			$sum=$procent*$qiwi['course_fact'];
			$qiwi_actual+=$sum;
		}
		$qiwi_actual=round($qiwi_actual*0.98);
		var_dump($qiwi_actual);
		
		$sql="select sum(remainder) as amount_btc from local_balancing_v2 where id_ad='609849' and status='0' group by id_ad";
		$qiwi_sum=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select * from local_balancing_v2 where id_ad='609849' and status='0' order by created asc";
		$qiwis=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$qiwi_actual=0;
		foreach ($qiwis as $qiwi){
			$procent=$qiwi['remainder'] / $qiwi_sum['amount_btc'];
			$sum=$procent*$qiwi['course_fact'];
			$qiwi_actual+=$sum;
		}
		$qiwi_actual=round($qiwi_actual*1.01);
		var_dump($qiwi_actual);
		
		$sql="select sum(remainder) as amount_btc from local_balancing where id_ad='609849' and status='0' group by id_ad";
		$qiwi_sum=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select * from local_balancing where id_ad='609849' and status='0' order by created asc";
		$qiwis=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$qiwi_actual=0;
		foreach ($qiwis as $qiwi){
			$procent=$qiwi['remainder'] / $qiwi_sum['amount_btc'];
			$sum=$procent*$qiwi['course_fact'];
			$qiwi_actual+=$sum;
		}
		$qiwi_actual=round($qiwi_actual*1.01);
		var_dump($qiwi_actual);
		exit;
		//qiwi
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select * from lbtc_robots where id_ad in ('617372', '609849') and status='9' and created_at>'2018-02-28' order by created_at asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into balancing (id_ad, contact_id, amount, amount_btc, course, remainder, created)
			select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM balancing WHERE contact_id=?)";
		$sthInsert=$pdo->prepare($sql);
		foreach ($stats as $stat){
			if ($stat['id_ad']=='617372'){
				$amount=$stat['amount']*0.99;
			}
			elseif ($stat['id_ad']=='609849'){
				$amount=$stat['amount']*1.02;
			}
			else{
				$amount=0;
			}
			$course=$amount/$stat['amount_btc'];
			$sthInsert->execute([$stat['id_ad'], $stat['contact_id'], $stat['amount'], $stat['amount_btc'], $course, $stat['amount_btc'], 
			$stat['created_at'], $stat['contact_id']]);
		}
		$sql="select * from balancing where id_ad='609849' and status='0' order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="update balancing set status=?, return_course=?, remainder=?, prosent=?, profit=?, details=?
			WHERE contact_id=?";
		$sthUpdate=$pdo->prepare($sql);
		$sql="update balancing set remainder=?, return_course=?, profit=?, details=?
			WHERE contact_id=?";
		$sthUpdateed=$pdo->prepare($sql);
		foreach ($buys as $buy){
			$i=1;
			$details='';
			$intt=\DB::connection('obmenneg')->table('balancing')->where('id_ad', '617372')->where('status', '0')->get();
			$count=count($intt);
			$remainder_buy=$buy['remainder'];
			while ($i<=$count) {
				$sql="select * from balancing where id_ad='617372' and status='0' order by created asc";
				$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
				$details=$sell['details'];
				$remainder_sell=$sell['remainder'];
				if ($sell['return_course']){
					$sell_course=($sell['return_course']+$buy['course'])/2;
				}
				else{
					$sell_course=$buy['course'];
				}
				if ($buy['return_course']){
					$buy_course=($buy['return_course']+$sell['course'])/2;
				}
				else{
					$buy_course=$sell['course'];
				}
				if ($remainder_buy>$remainder_sell){
					$details=$details .  "" . round($remainder_sell,6) . "-" . $buy['course'] .";";
					$remainder_buy=$remainder_buy-$remainder_sell;
					$remainder_sell=0;
					$prosent=(($buy_course-$sell_course)/$buy_course)*100;
					$buy_b=$sell_course*$sell['amount_btc'];
					$sell_b=$sell['course']*$sell['amount_btc'];
					$profit=$sell_b-$buy_b;
					
					//array_push($details, "" . $remainder_sell . "-" . $buy['course'] ."");
					//var_dump($details);
					$sthUpdate->execute(['1',$sell_course,$remainder_sell,$prosent,$profit,$details,$sell['contact_id']]);
					$sthUpdateed->execute([$remainder_buy,$buy_course,'0','0',$buy['contact_id']]);
				}
				else{
					$details=$details .  "" . round($remainder_buy,6) . "-" . $buy['course'] .";";
					$remainder_sell=$remainder_sell-$remainder_buy;
					$remainder_buy=0;
					$prosent=(($buy_course-$sell_course)/$buy_course)*100;
					$buy_b=$sell_course*$sell['amount_btc'];
					$sell_b=$sell['course']*$sell['amount_btc'];
					$profit=$sell_b-$buy_b;
					//var_dump($details);
					$sthUpdate->execute(['1',$buy_course,$remainder_buy,$prosent,'0','0',$buy['contact_id']]);
					$sthUpdateed->execute([$remainder_sell,$sell_course,$profit,$details,$sell['contact_id']]);
					break;
				}
				$i++;
			}
			continue;
		}
		
		//yandex
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select * from lbtc_robots where id_ad in ('609305', '609928') and status='9' and created_at>'2018-02-28' order by created_at asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into balancing (id_ad, contact_id, amount, amount_btc, course, remainder, created)
			select ?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM balancing WHERE contact_id=?)";
		$sthInsert=$pdo->prepare($sql);
		foreach ($stats as $stat){
			if ($stat['id_ad']=='609305'){
				$amount=$stat['amount']*0.99;
			}
			elseif ($stat['id_ad']=='609928'){
				$amount=$stat['amount']*1.015;
			}
			else{
				$amount=0;
			}
			$course=$amount/$stat['amount_btc'];
			$sthInsert->execute([$stat['id_ad'], $stat['contact_id'], $stat['amount'], $stat['amount_btc'], $course, $stat['amount_btc'], 
			$stat['created_at'], $stat['contact_id']]);
		}
		$sql="select * from balancing where id_ad='609928' and status='0' order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="update balancing set status=?, return_course=?, remainder=?, prosent=?, profit=?, details=?
			WHERE contact_id=?";
		$sthUpdate=$pdo->prepare($sql);
		$sql="update balancing set remainder=?, return_course=?, profit=?, details=?
			WHERE contact_id=?";
		$sthUpdateed=$pdo->prepare($sql);
		foreach ($buys as $buy){
			$i=1;
			$details='';
			$intt=\DB::connection('obmenneg')->table('balancing')->where('id_ad', '609305')->where('status', '0')->get();
			$count=count($intt);
			$remainder_buy=$buy['remainder'];
			while ($i<=$count) {
				$sql="select * from balancing where id_ad='609305' and status='0' order by created asc";
				$sell=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
				$details=$sell['details'];
				$remainder_sell=$sell['remainder'];
				if ($sell['return_course']){
					$sell_course=($sell['return_course']+$buy['course'])/2;
				}
				else{
					$sell_course=$buy['course'];
				}
				if ($buy['return_course']){
					$buy_course=($buy['return_course']+$sell['course'])/2;
				}
				else{
					$buy_course=$sell['course'];
				}
				if ($remainder_buy>$remainder_sell){
					$details=$details .  "" . round($remainder_sell,6) . "-" . $buy['course'] .";";
					$remainder_buy=$remainder_buy-$remainder_sell;
					$remainder_sell=0;
					$prosent=(($buy_course-$sell_course)/$buy_course)*100;
					$buy_b=$sell_course*$sell['amount_btc'];
					$sell_b=$sell['course']*$sell['amount_btc'];
					$profit=$sell_b-$buy_b;
					$sthUpdate->execute(['1',$sell_course,$remainder_sell,$prosent,$profit,$details,$sell['contact_id']]);
					$sthUpdateed->execute([$remainder_buy,$buy_course,'0','0',$buy['contact_id']]);
				}
				else{
					$details=$details .  "" . round($remainder_buy,6) . "-" . $buy['course'] .";";
					$remainder_sell=$remainder_sell-$remainder_buy;
					$remainder_buy=0;
					$prosent=(($buy_course-$sell_course)/$buy_course)*100;
					$buy_b=$sell_course*$sell['amount_btc'];
					$sell_b=$sell['course']*$sell['amount_btc'];
					$profit=$sell_b-$buy_b;
					$sthUpdate->execute(['1',$buy_course,$remainder_buy,$prosent,'0','0',$buy['contact_id']]);
					$sthUpdateed->execute([$remainder_sell,$sell_course,$profit,$details,$sell['contact_id']]);
					break;
				}
				$i++;
			}
			continue;
		}
	}
		
}
