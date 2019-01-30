<?php

namespace App\Console\Commands\Obmenneg;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;


class BalancingV2 extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:balancing_v2';

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
		
		//qiwi
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select id_ad, contact_id, amount, amount_btc, round(amount/amount_btc::numeric,2) as course, 
		case when id_ad='609849' then round(amount/amount_btc::numeric,2)*1.02 else round(amount/amount_btc::numeric,2)*0.99 end as course_fact,
		case when id_ad='609849' then round(amount/amount_btc::numeric,2)*1.02*amount_btc else round(amount/amount_btc::numeric,2)*0.99*amount_btc end
		as amount_fact,created_at from local_robots where id_ad in ('617372', '609849') and status='9' order by created_at asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into local_balancing (id_ad, contact_id, amount, amount_btc, course, course_fact, amount_fact, remainder, created)
			select ?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM local_balancing WHERE contact_id=?)";
		$sthInsert=$pdo->prepare($sql);
		foreach ($stats as $stat){
			$sthInsert->execute([$stat['id_ad'], $stat['contact_id'], $stat['amount'], $stat['amount_btc'], $stat['course'], $stat['course_fact'], 
			$stat['amount_fact'], $stat['amount_btc'], $stat['created_at'], $stat['contact_id']]);
		}
		$sql="select * from local_balancing where id_ad='609849' and status='0' order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		$sql="update local_balancing set status=?, remainder=?, details=?
			WHERE contact_id=?";
		$sthUpdate=$pdo->prepare($sql);
		
		$sql="update local_balancing set status=?, remainder=?, details=?, return_course=?, profit=?
			WHERE contact_id=?";
		$sthUpdated=$pdo->prepare($sql);
		
		$sql="update local_balancing set status=?, remainder=? 
			WHERE contact_id=?";
		$sthUpdatedd=$pdo->prepare($sql);

		foreach ($buys as $buy){
			$i=1;
			$sells=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '617372')->where('status', '0')->orderBy('created', 'asc')->get();
			$remainder_buy=$buy['remainder'];
			while($i<=count($sells)){
				$sql="select * from local_balancing where id_ad='617372' and status='0' order by created asc";
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
		
		$sql="select * from local_balancing where id_ad='617372' and status in ('0','3') order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach ($buys as $buy){
			$i=1;
			$sells=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609849')->where('remainder', '>', '0')->orderBy('created', 'asc')->get();
			$remainder_buy=$buy['remainder'];
			while($i<=count($sells)){
				$sql="select * from local_balancing where id_ad='609849' and remainder>'0' order by created asc";
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
		
		//яндекс
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="select id_ad, contact_id, amount, amount_btc, round(amount/amount_btc::numeric,2) as course, 
		case when id_ad='609928' then round(amount/amount_btc::numeric,2)*1.015 else round(amount/amount_btc::numeric,2)*0.99 end as course_fact,
		case when id_ad='609928' then round(amount/amount_btc::numeric,2)*1.015*amount_btc else round(amount/amount_btc::numeric,2)*0.99*amount_btc end
		as amount_fact,created_at from local_robots where id_ad in ('609305', '609928') and status='9' order by created_at asc";
		$stats=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$sql="insert into local_balancing (id_ad, contact_id, amount, amount_btc, course, course_fact, amount_fact, remainder, created)
			select ?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM local_balancing WHERE contact_id=?)";
		$sthInsert=$pdo->prepare($sql);
		foreach ($stats as $stat){
			$sthInsert->execute([$stat['id_ad'], $stat['contact_id'], $stat['amount'], $stat['amount_btc'], $stat['course'], $stat['course_fact'], 
			$stat['amount_fact'], $stat['amount_btc'], $stat['created_at'], $stat['contact_id']]);
		}
		$sql="select * from local_balancing where id_ad='609928' and status='0' order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		$sql="update local_balancing set status=?, remainder=?, details=?
			WHERE contact_id=?";
		$sthUpdate=$pdo->prepare($sql);
		
		$sql="update local_balancing set status=?, remainder=?, details=?, return_course=?, profit=?
			WHERE contact_id=?";
		$sthUpdated=$pdo->prepare($sql);
		
		$sql="update local_balancing set status=?, remainder=? 
			WHERE contact_id=?";
		$sthUpdatedd=$pdo->prepare($sql);
		
		foreach ($buys as $buy){
			$i=1;
			$sells=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609305')->where('status', '0')->orderBy('created', 'asc')->get();
			$remainder_buy=$buy['remainder'];
			while($i<=count($sells)){
				$sql="select * from local_balancing where id_ad='609305' and status='0' order by created asc";
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
		
		$sql="select * from local_balancing where id_ad='609305' and status in ('0','3') order by created asc";
		$buys=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach ($buys as $buy){
			$i=1;
			$sells=\DB::connection('obmenneg')->table('local_balancing')->where('id_ad', '609928')->where('remainder', '>', '0')->orderBy('created', 'asc')->get();
			$remainder_buy=$buy['remainder'];
			while($i<=count($sells)){
				$sql="select * from local_balancing where id_ad='609928' and remainder>'0' order by created asc";
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
		\DB::connection('obmenneg')->table('local_ads')->where('id_ad', '617372')->update(['actual_price'=>$qiwi_actual]);
		
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
		\DB::connection('obmenneg')->table('local_ads')->where('id_ad', '609849')->update(['actual_price'=>$qiwi_actual]);
		
		$sql="select sum(remainder) as amount_btc from local_balancing where id_ad='609928' and status='0' group by id_ad";
		$yandex_sum=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select * from local_balancing where id_ad='609928' and status='0' order by created asc";
		$yandexs=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$yandex_actual=0;
		foreach ($yandexs as $yandex){
			$procent=$yandex['remainder'] / $yandex_sum['amount_btc'];
			$sum=$procent*$yandex['course_fact'];
			$yandex_actual+=$sum;
		}
		$yandex_actual=round($yandex_actual*1.01);
		\DB::connection('obmenneg')->table('local_ads')->where('id_ad', '609305')->update(['actual_price'=>$yandex_actual]);
		
		$sql="select sum(remainder) as amount_btc from local_balancing where id_ad='609305' and status='0' group by id_ad";
		$yandex_sum=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetch(\PDO::FETCH_ASSOC);
		$sql="select * from local_balancing where id_ad='609305' and status='0' order by created asc";
		$yandexs=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
		$yandex_actual=0;
		foreach ($yandexs as $yandex){
			$procent=$yandex['remainder'] / $yandex_sum['amount_btc'];
			$sum=$procent*$yandex['course_fact'];
			$yandex_actual+=$sum;
		}
		$yandex_actual=round($yandex_actual*0.985);
		\DB::connection('obmenneg')->table('local_ads')->where('id_ad', '609928')->update(['actual_price'=>$yandex_actual]);
	}
		
}
