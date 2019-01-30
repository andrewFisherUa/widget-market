<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Transaction extends Model
{
private $ref_sum=[];
    protected $fillable = [
        'day'
    ];

	public function commissionVideoWidgets($date){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select day, pid, sum(summa+second_expensive_summa+second_cheap_summa+control_summa-lease_summa) as summa from pid_summa_full where day='$date' group by day, pid";
		$video=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$video_com=[];
		foreach ($video as $v){
			$video_widget=\DB::table('widget_videos')->where('id', $v['pid'])->first();
			if ($video_widget){
				$widget=\App\MPW\Widgets\Widget::where('id', $video_widget->wid_id)->first();
				if ($widget){
					if (strtotime($date)<strtotime(date('2018-01-01'))){
						if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
							or 
						($widget->userProfile['referer'] 
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01')))){
							$v['summa']=$v['summa']*1.1;
						}
					}
					if (strtotime($date)<strtotime(date('2018-07-23'))){
						if ($widget->userProfile['user_id']==705){
							$v['summa']=$v['summa']*1.2;
						}
					}
					$dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first();
					if ($dop){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$v['summa']=$v['summa']*1.1;
						}
					}
					$dopp=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $widget->user_id)->first();
					if ($dopp){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$v['summa']=$v['summa']*1.1;
						}
					}
					if (!isset($video_com[$widget->user_id]))
					$video_com[$widget->user_id]=[];
					if (!isset($video_com[$widget->user_id]['commission']))
					$video_com[$widget->user_id]['commission']=$v['summa'];
					else
					$video_com[$widget->user_id]['commission']+=$v['summa'];
				}
				if ($widget){
					$user=\App\UserProfile::where('user_id', $widget->user_id)->first();
					if ($user){
						if ($user->dop_status==3){
							continue;
						}
						$refer=\App\UserProfile::where('user_id', $user->referer)->first();
						if ($refer){
							if (!isset($this->ref_sum[$refer->user_id]))
							$this->ref_sum[$refer->user_id]=[];
							if (!isset($this->ref_sum[$refer->user_id][$user->user_id]))
							$this->ref_sum[$refer->user_id][$user->user_id]=round($v['summa']*0.05,2);
							else
							$this->ref_sum[$refer->user_id][$user->user_id]+=round($v['summa']*0.05,2);
						}
					}
				}
			}
		}
		foreach ($video_com as $k=>$video){
			$commission=\App\Transactions\UserTransaction::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->video_commission=$video['commission'];
			$commission->save();
		}
	}
	
	public function commissionProductWidgets($date){
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select day, pid, sum(yandex_summa+ta_summa) as summa from wid_calculate where day='$date' group by day, pid";
		$product=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_com=[];
		foreach ($product as $p){
				$widget=\App\MPW\Widgets\Widget::where('id', $p['pid'])->first();
				if ($widget){
					if (strtotime($date)<strtotime(date('2018-01-01'))){
						if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
							or 
						($widget->userProfile['referer'] 
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01')))){
							$p['summa']=$p['summa']*1.1;
						}
					}
					$dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first();
					if ($dop){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$p['summa']=$p['summa']*1.1;
						}
					}
					$dopp=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $widget->user_id)->first();
					if ($dopp){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$p['summa']=$p['summa']*1.1;
						}
					}
					if (!isset($product_com[$widget->user_id]))
					$product_com[$widget->user_id]=[];
					if (!isset($product_com[$widget->user_id]['commission']))
					$product_com[$widget->user_id]['commission']=$p['summa'];
					else
					$product_com[$widget->user_id]['commission']+=$p['summa'];
				}
				if ($widget){
					$user=\App\UserProfile::where('user_id', $widget->user_id)->first();
					if ($user){
						$refer=\App\UserProfile::where('user_id', $user->referer)->first();
						if ($refer){
							if (!isset($this->ref_sum[$refer->user_id]))
							$this->ref_sum[$refer->user_id]=[];
							if (!isset($this->ref_sum[$refer->user_id][$user->user_id]))
							$this->ref_sum[$refer->user_id][$user->user_id]=round($p['summa']*0.05,2);
							else
							$this->ref_sum[$refer->user_id][$user->user_id]+=round($p['summa']*0.05,2);
						}
					}
				}
		}
		foreach ($product_com as $k=>$product){
			$commission=\App\Transactions\UserTransaction::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->product_commission=$product['commission'];
			$commission->save();
		}
		
		
	}
	
	public function commissionTeaserWidgets($date){
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select day, pid, sum(ts_summa) as summa from wid_calculate where day='$date' group by day, pid";
		$teaser=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$teaser_com=[];
		foreach ($teaser as $p){
				$widget=\App\MPW\Widgets\Widget::where('id', $p['pid'])->first();
				if ($widget){
					if (strtotime($date)<strtotime(date('2018-01-01'))){
						if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
							or 
						($widget->userProfile['referer'] 
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01')))){
							$p['summa']=$p['summa']*1.1;
						}
					}
					$dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first();
					if ($dop){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$p['summa']=$p['summa']*1.1;
						}
					}
					$dopp=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $widget->user_id)->first();
					if ($dopp){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$p['summa']=$p['summa']*1.1;
						}
					}
					if (!isset($teaser_com[$widget->user_id]))
					$teaser_com[$widget->user_id]=[];
					if (!isset($teaser_com[$widget->user_id]['commission']))
					$teaser_com[$widget->user_id]['commission']=$p['summa'];
					else
					$teaser_com[$widget->user_id]['commission']+=$p['summa'];
				}
				if ($widget){
					$user=\App\UserProfile::where('user_id', $widget->user_id)->first();
					if ($user){
						$refer=\App\UserProfile::where('user_id', $user->referer)->first();
						if ($refer){
							if (!isset($this->ref_sum[$refer->user_id]))
							$this->ref_sum[$refer->user_id]=[];
							if (!isset($this->ref_sum[$refer->user_id][$user->user_id]))
							$this->ref_sum[$refer->user_id][$user->user_id]=round($p['summa']*0.05,2);
							else
							$this->ref_sum[$refer->user_id][$user->user_id]+=round($p['summa']*0.05,2);
						}
					}
				}
		}
		foreach ($teaser_com as $k=>$teaser){
			$commission=\App\Transactions\UserTransaction::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->teaser_commission=$teaser['commission'];
			$commission->save();
		}
		
		
	}
	
	public function commissionManagers($date){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select day, pid, sum(summa+second_expensive_summa+second_cheap_summa+control_summa) as summa from pid_summa_full where day='$date' group by day, pid";
		$video=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$manager_com=[];
		foreach ($video as $v){
			$video_widget=\DB::table('widget_videos')->where('id', $v['pid'])->first();
			if ($video_widget){
				$widget=\DB::table('widgets')->where('id', $video_widget->wid_id)->first();
				if ($widget){
					$user=\DB::table('user_profiles')->where('user_id', $widget->user_id)->first();
					if ($user){
						if ($user->manager){
							$manager=\App\User::where('id', $user->manager)->first();
							if ($manager){
								if ($manager->hasRole('admin') or $manager->hasRole('super_manager') or $manager->hasRole('manager')){
									if (!isset($manager_com[$manager->id]))
									$manager_com[$manager->id]=[];
									if (!isset($manager_com[$manager->id]['commission']))
									$manager_com[$manager->id]['commission']=\DB::table('сommission_groups')->where('commissiongroupid', $manager->ManagerCommission->commissiongroupid)->first()->value;
									if (!isset($manager_com[$manager->id][$user->dop_status]['video']))
									$manager_com[$manager->id][$user->dop_status]['video']=$v['summa'];
									else
									$manager_com[$manager->id][$user->dop_status]['video']+=$v['summa'];
									if (!isset($manager_com[$manager->id]['hist']['video']))
									$manager_com[$manager->id]['hist']['video']=[];
									if (!isset($manager_com[$manager->id]['hist']['video'][$user->dop_status][$user->user_id]))
									$manager_com[$manager->id]['hist']['video'][$user->dop_status][$user->user_id]=$v['summa'];
									else
									$manager_com[$manager->id]['hist']['video'][$user->dop_status][$user->user_id]+=$v['summa'];
								}
							}
						}
					}
				}
			}
		}
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select day, pid, sum(yandex_summa+ta_summa+ts_summa) as summa from wid_calculate where day='$date' group by day, pid";
		$product=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		foreach ($product as $p){
				$widget=\DB::table('widgets')->where('id', $p['pid'])->first();
				if ($widget){
					$user=\DB::table('user_profiles')->where('user_id', $widget->user_id)->first();
					if ($user){
						if ($user->manager){
							$manager=\App\User::where('id', $user->manager)->first();
							if ($manager){
								if ($manager->hasRole('admin') or $manager->hasRole('super_manager') or $manager->hasRole('manager')){
									if (!isset($manager_com[$manager->id]))
									$manager_com[$manager->id]=[];
									if (!isset($manager_com[$manager->id]['commission']))
									$manager_com[$manager->id]['commission']=\DB::table('сommission_groups')->where('commissiongroupid', $manager->ManagerCommission->commissiongroupid)->first()->value;
									if (!isset($manager_com[$manager->id][$user->dop_status]['product']))
									$manager_com[$manager->id][$user->dop_status]['product']=$p['summa'];
									else
									$manager_com[$manager->id][$user->dop_status]['product']+=$p['summa'];
									if (!isset($manager_com[$manager->id]['hist']['product']))
									$manager_com[$manager->id]['hist']['product']=[];
									if (!isset($manager_com[$manager->id]['hist']['product'][$user->dop_status][$user->user_id]))
									$manager_com[$manager->id]['hist']['product'][$user->dop_status][$user->user_id]=$p['summa'];
									else
									$manager_com[$manager->id]['hist']['product'][$user->dop_status][$user->user_id]+=$p['summa'];
								}
							}
						}
					}
				}
		}
		foreach ($manager_com as $k=>$m){
			$hist = '';
			if (isset($m['hist']['product'])){
				foreach ($m['hist']['product'] as $koef=>$users){
					foreach ($users as $user=>$sum){
						if (!isset($hist))
						$hist=$user.":".$sum*$m['commission'].";";
						else
						$hist=substr_replace($hist, $user.":".$sum*$m['commission'].";", 0, 0);
					}
				}
			}
			if (isset($m['hist']['video'])){
				foreach ($m['hist']['video'] as $koef=>$users){
					foreach ($users as $user=>$sum){
						if ($koef==1){
							if ($m['commission']>0){
								$commission=$m['commission']+.01;
							}
							else{
								$commission=$m['commission'];
							}
							if (!isset($hist))
							$hist=$user.":".$sum*$commission.";";
							else
							$hist=substr_replace($hist, $user.":".$sum*$commission.";", 0, 0);
						}
						if ($koef==2){
							$commission=$m['commission'];
							if (!isset($hist))
							$hist=$user.":".$sum*$commission.";";
							else
							$hist=substr_replace($hist, $user.":".$sum*$commission.";", 0, 0);
						}
						if ($koef==3){
							if ($m['commission']>0){
								$commission=$m['commission']-.01;
							}
							else{
								$commission=$m['commission'];
							}
							if (!isset($hist))
							$hist=$user.":".$sum*$commission.";";
							else
							$hist=substr_replace($hist, $user.":".$sum*$commission.";", 0, 0);
						}
					}
				}
			}
			if (!isset($m[1]['product'])){
			$m[1]['product']=0;
			}
			if (!isset($m[2]['product'])){
			$m[2]['product']=0;
			}
			if (!isset($m[3]['product'])){
			$m[3]['product']=0;
			}
			if (!isset($m[1]['video'])){
			$m[1]['video']=0;
			}
			if (!isset($m[2]['video'])){
			$m[2]['video']=0;
			}
			if (!isset($m[3]['video'])){
			$m[3]['video']=0;
			}
			if ($m['commission']>0){
				$summa=($m[1]['video']*($m['commission']+.01))+($m[2]['video']*$m['commission'])+($m[3]['video']*($m['commission']-.01))
				+(($m[1]['product']+$m[2]['product']+$m[3]['product'])*$m['commission']);
			}
			else{
				$summa=($m[1]['video']+$m[2]['video']+$m[3]['video']+$m[1]['product']+$m[2]['product']+$m[3]['product'])*$m['commission'];
			}
			
			/*if ($k==16){
			$m['video']=0;
			}*/
			
			$commission=\App\Transactions\UserTransaction::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->manager_commission=$summa;
			$commission->save();
			$manager_hist=\App\Transactions\ManagerCommissionTransacion::firstOrNew(['day' => $date, 'user_id' => $k]);
			$manager_hist->history=$hist;
			$manager_hist->summa=$summa;
			$manager_hist->save();
		}
	}
	
	public function commissionReferal($date){
		$referal=[];
		foreach ($this->ref_sum as $id=>$ref){
			foreach ($ref as $user=>$sum){
				if (!isset($referal[$id]))
				$referal[$id]=[];
				if (!isset($referal[$id]['summa']))
				$referal[$id]['summa']=$sum;
				else
				$referal[$id]['summa']+=$sum;
				if (!isset($referal[$id]['hist']))
				$referal[$id]['hist']=$user.":".$sum.";";
				else
				$referal[$id]['hist']=substr_replace($referal[$id]['hist'], $user.":".$sum.";", 0, 0);
			}
		}
		foreach ($referal as $k=>$ref){
		$commission=\App\Transactions\UserTransaction::firstOrNew(['day' => $date, 'user_id' => $k]);
		$commission->referal_commission=$ref['summa'];
		$commission->save();
		$commission_ref=\App\Transactions\UserReferalTransacion::firstOrNew(['day' => $date, 'user_id' => $k]);
		$commission_ref->history=$ref['hist'];
		$commission_ref->summa=$ref['summa'];
		$commission_ref->save();
		}
	}
	
	public function testVideo($date){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select day, pid, sum(summa+second_expensive_summa+second_cheap_summa+control_summa) as summa from pid_summa_full where day='$date' group by day, pid";
		$video=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$video_com_us=0;
		$com=[];
		foreach ($video as $v){
			$video_widget=\DB::table('widget_videos')->where('id', $v['pid'])->first();
			if ($video_widget){
				$widget=\DB::table('widgets')->where('id', $video_widget->wid_id)->first();
				if ($widget){
					if ($video_com_us==0){
					$video_com_us=$v['summa'];
					}
					else{
					$video_com_us+=$v['summa'];
					}
					if (!isset($com['hist'][$widget->user_id]))
					$com['hist'][$widget->user_id]=$v['summa'];
					else
					$com['hist'][$widget->user_id]+=$v['summa'];
				}
			}
		}
		$hist='';
		$m_com=0.005;
		foreach ($com['hist'] as $user=>$sum){
			if (!isset($hist))
				$hist=$user.":".$sum*$m_com.";";
			else
				$hist=substr_replace($hist, $user.":".$sum*$m_com.";", 0, 0);
			}
			$summa=$video_com_us*$m_com;
			$manager_hist=\App\Transactions\ManagerCommissionTransacion::firstOrNew(['day' => $date, 'user_id' => 1]);
			$manager_hist->history=$hist;
			$manager_hist->summa=$summa;
			$manager_hist->save();
		$commission=\App\Transactions\UserTransaction::firstOrNew(['day' => $date, 'user_id' => 1]);
		$commission->manager_commission=$video_com_us*$m_com;
		$commission->save();
	}
	
	public function transactionOnBalance($date){
		$commissions=\App\Transactions\UserTransaction::where('day', $date)->get();
		foreach ($commissions as $commission){
			$summa=$commission->video_commission+$commission->product_commission+$commission->teaser_commission+$commission->referal_commission+$commission->manager_commission;
			$userProfile=\App\UserProfile::where('user_id', $commission->user_id)->first();
			$commission_new=\App\Transactions\UserTransactionLog::firstOrNew(['day' => $commission->day, 'user_id' => $commission->user_id]);
			$commission_new->commission=$summa;
			$commission_new->save();
		}
//		$users=\App\UserProfile::whereHas('roles', function ($query) {
//			$query->whereIn('id', [1, 3, 4, 5]);
//			})->get();
		$users=\App\User::whereHas('roles', function ($query) {
			$query->whereIn('id', [1, 3, 4, 5]);
			})->get();
		foreach ($users as $user){

			$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->id)->sum('commission');
			$summa=\App\Transactions\UserTransactionLog::where('user_id', $user->id)->sum('commission');
			$payout_sum=\App\Payments\UserPayout::where('user_id', $user->id)->whereNotIn('status', array(2,3))->sum('payout');
			$old_balance=\App\UserBalanceOld::where('user_id', $user->id)->sum('balance');


			$old_tran=0;
				if ($user->id=='216'){
					$tr=\App\Payments\UserPayout::find(2);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(3);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(4);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(8);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(22);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='255'){
					$tr=\App\Payments\UserPayout::find(5);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='308'){
					$tr=\App\Payments\UserPayout::find(6);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='415'){
					$tr=\App\Payments\UserPayout::find(7);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='90'){
					$tr=\App\Payments\UserPayout::find(9);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='39'){
					$tr=\App\Payments\UserPayout::find(10);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='257'){
					$tr=\App\Payments\UserPayout::find(11);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='206'){
					$tr=\App\Payments\UserPayout::find(12);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='84'){
					$tr=\App\Payments\UserPayout::find(13);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='43'){
					$tr=\App\Payments\UserPayout::find(14);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='111'){
					$tr=\App\Payments\UserPayout::find(15);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='56'){
					$tr=\App\Payments\UserPayout::find(16);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='310'){
					$tr=\App\Payments\UserPayout::find(17);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='296'){
					$tr=\App\Payments\UserPayout::find(18);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='159'){
					$tr=\App\Payments\UserPayout::find(19);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='6'){
					$tr=\App\Payments\UserPayout::find(20);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='90'){
					$tr=\App\Payments\UserPayout::find(21);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='60'){
					$tr=\App\Payments\UserPayout::find(23);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='88'){
					$tr=\App\Payments\UserPayout::find(24);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->id=='5'){
					$tr=\App\Payments\UserPayout::find(25);
					if ($user->id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}




                		if(!$user->Profile){
                		  var_dump([$user->id." / ".$user->name,$payment_commission+$summa+$old_balance-$payout_sum+$old_tran]);
                                  continue;
                                }	
			$profile=$user->Profile;	

			$user->Profile->balance=$payment_commission+$summa+$old_balance-$payout_sum+$old_tran;
			$user->Profile->save();

                        if($user->id==10522){
			  var_dump($user->id." : ".$user->Profile->balance);
                        }


			if ($profile->balance<0){
				print 'Внимание у юзера '.$user->name.' минусовой баланс.'."\n";
			}















                }
                //die();
/*

		$users=\App\UserProfile::whereHas('roles', function ($query) {
			$query->whereIn('id', [1, 3, 4, 5]);
			})->get();
		foreach ($users as $user){

			$payment_commission=\App\Payments\PaymentCommission::where('user_id', $user->user_id)->sum('commission');
			$summa=\App\Transactions\UserTransactionLog::where('user_id', $user->user_id)->sum('commission');
			$payout_sum=\App\Payments\UserPayout::where('user_id', $user->user_id)->whereNotIn('status', array(2,3))->sum('payout');
			$old_balance=\App\UserBalanceOld::where('user_id', $user->user_id)->sum('balance');

			
			$old_tran=0;
				if ($user->user_id=='216'){
					$tr=\App\Payments\UserPayout::find(2);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(3);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(4);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(8);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
					$tr=\App\Payments\UserPayout::find(22);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='255'){
					$tr=\App\Payments\UserPayout::find(5);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='308'){
					$tr=\App\Payments\UserPayout::find(6);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='415'){
					$tr=\App\Payments\UserPayout::find(7);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='90'){
					$tr=\App\Payments\UserPayout::find(9);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='39'){
					$tr=\App\Payments\UserPayout::find(10);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='257'){
					$tr=\App\Payments\UserPayout::find(11);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='206'){
					$tr=\App\Payments\UserPayout::find(12);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='84'){
					$tr=\App\Payments\UserPayout::find(13);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='43'){
					$tr=\App\Payments\UserPayout::find(14);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='111'){
					$tr=\App\Payments\UserPayout::find(15);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='56'){
					$tr=\App\Payments\UserPayout::find(16);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='310'){
					$tr=\App\Payments\UserPayout::find(17);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='296'){
					$tr=\App\Payments\UserPayout::find(18);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='159'){
					$tr=\App\Payments\UserPayout::find(19);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='6'){
					$tr=\App\Payments\UserPayout::find(20);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='90'){
					$tr=\App\Payments\UserPayout::find(21);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='60'){
					$tr=\App\Payments\UserPayout::find(23);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='88'){
					$tr=\App\Payments\UserPayout::find(24);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
				if ($user->user_id=='5'){
					$tr=\App\Payments\UserPayout::find(25);
					if ($user->user_id==$tr->user_id){
						if ($tr->status==1 or $tr->status==0){
							$old_tran+=$tr->payout;
						}
					}
				}
			if($user->user_id==10522){
			var_dump($user->user_id." : ".$summa);
			}
			$user->balance=$payment_commission+$summa+$old_balance-$payout_sum+$old_tran;
			$user->save();
			if ($user->balance<0){
				print 'Внимание у юзера '.$user->name.' минусовой баланс.'."\n";
			}
		}
	*/	
	}
	
	public function commissionVideoWidgetsToday($date){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select day, pid, sum(summa+second_expensive_summa+second_cheap_summa+control_summa) as summa from pid_summa_full where day='$date' group by day, pid";
		$video=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$video_com=[];
		foreach ($video as $v){
			$video_widget=\DB::table('widget_videos')->where('id', $v['pid'])->first();
			if ($video_widget){
				$widget=\App\MPW\Widgets\Widget::where('id', $video_widget->wid_id)->first();
				if ($widget){
					if (strtotime($date)<strtotime(date('2018-01-01'))){
						if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
							or 
						($widget->userProfile['referer'] 
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01')))){
							$v['summa']=$v['summa']*1.1;
						}
					}
					$dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first();
					if ($dop){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$v['summa']=$v['summa']*1.1;
						}
					}
					if (!isset($video_com[$widget->user_id]))
					$video_com[$widget->user_id]=[];
					if (!isset($video_com[$widget->user_id]['commission']))
					$video_com[$widget->user_id]['commission']=$v['summa'];
					else
					$video_com[$widget->user_id]['commission']+=$v['summa'];
				}
			}
		}
		foreach ($video_com as $k=>$video){
			$commission=\App\Transactions\BalanceOnHome::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->video_commission=$video['commission'];
			$commission->save();
		}
	}
	
	public function commissionProductWidgetsToday($date){
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select day, pid, sum(yandex_summa+ta_summa) as summa from wid_calculate where day='$date' group by day, pid";
		$product=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_com=[];
		foreach ($product as $p){
				$widget=\App\MPW\Widgets\Widget::where('id', $p['pid'])->first();
				if ($widget){
					if (strtotime($date)<strtotime(date('2018-01-01'))){
						if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
							or 
						($widget->userProfile['referer'] 
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01')))){
							$p['summa']=$p['summa']*1.1;
						}
					}
					$dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first();
					if ($dop){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$p['summa']=$p['summa']*1.1;
						}
					}
					if (!isset($product_com[$widget->user_id]))
					$product_com[$widget->user_id]=[];
					if (!isset($product_com[$widget->user_id]['commission']))
					$product_com[$widget->user_id]['commission']=$p['summa'];
					else
					$product_com[$widget->user_id]['commission']+=$p['summa'];
				}
		}
		foreach ($product_com as $k=>$product){
			$commission=\App\Transactions\BalanceOnHome::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->product_commission=$product['commission'];
			$commission->save();
		}
		
		
	}
	
	public function commissionTeaserWidgetsToday($date){
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select day, pid, sum(ts_summa) as summa from wid_calculate where day='$date' group by day, pid";
		$product=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$product_com=[];
		foreach ($product as $p){
				$widget=\App\MPW\Widgets\Widget::where('id', $p['pid'])->first();
				if ($widget){
					if (strtotime($date)<strtotime(date('2018-01-01'))){
						if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
							or 
						($widget->userProfile['referer'] 
							and 
						strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01')))){
							$p['summa']=$p['summa']*1.1;
						}
					}
					$dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first();
					if ($dop){
						if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14){
							$p['summa']=$p['summa']*1.1;
						}
					}
					if (!isset($product_com[$widget->user_id]))
					$product_com[$widget->user_id]=[];
					if (!isset($product_com[$widget->user_id]['commission']))
					$product_com[$widget->user_id]['commission']=$p['summa'];
					else
					$product_com[$widget->user_id]['commission']+=$p['summa'];
				}
		}
		foreach ($product_com as $k=>$product){
			$commission=\App\Transactions\BalanceOnHome::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->teaser_commission=$product['commission'];
			$commission->save();
		}
		
		
	}
	
	public function commissionManagersToday($date){
		$pdo = \DB::connection('videotest')->getPdo();
		$sql="select day, pid, sum(summa+second_expensive_summa+second_cheap_summa+control_summa) as summa from pid_summa_full where day='$date' group by day, pid";
		$video=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$manager_com=[];
		foreach ($video as $v){
			$video_widget=\DB::table('widget_videos')->where('id', $v['pid'])->first();
			if ($video_widget){
				$widget=\DB::table('widgets')->where('id', $video_widget->wid_id)->first();
				if ($widget){
					$user=\DB::table('user_profiles')->where('user_id', $widget->user_id)->first();
					if ($user){
						if ($user->manager){
							$manager=\App\User::where('id', $user->manager)->first();
							if ($manager){
								if ($manager->hasRole('admin') or $manager->hasRole('super_manager') or $manager->hasRole('manager')){
									if (!isset($manager_com[$manager->id]))
									$manager_com[$manager->id]=[];
									if (!isset($manager_com[$manager->id]['commission']))
									$manager_com[$manager->id]['commission']=\DB::table('сommission_groups')->where('commissiongroupid', $manager->ManagerCommission->commissiongroupid)->first()->value;
									if (!isset($manager_com[$manager->id]['video']))
									$manager_com[$manager->id]['video']=$v['summa'];
									else
									$manager_com[$manager->id]['video']+=$v['summa'];
								}
							}
						}
					}
				}
			}
		}
		$pdo = \DB::connection('pgstatistic')->getPdo();
		$sql="select day, pid, sum(yandex_summa+ta_summa+ts_summa) as summa from wid_calculate where day='$date' group by day, pid";
		$product=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		foreach ($product as $p){
				$widget=\DB::table('widgets')->where('id', $p['pid'])->first();
				if ($widget){
					$user=\DB::table('user_profiles')->where('user_id', $widget->user_id)->first();
					if ($user){
						if ($user->manager){
							$manager=\App\User::where('id', $user->manager)->first();
							if ($manager){
								if ($manager->hasRole('admin') or $manager->hasRole('super_manager') or $manager->hasRole('manager')){
									if (!isset($manager_com[$manager->id]))
									$manager_com[$manager->id]=[];
									if (!isset($manager_com[$manager->id]['commission']))
									$manager_com[$manager->id]['commission']=\DB::table('сommission_groups')->where('commissiongroupid', $manager->ManagerCommission->commissiongroupid)->first()->value;
									if (!isset($manager_com[$manager->id]['product']))
									$manager_com[$manager->id]['product']=$p['summa'];
									else
									$manager_com[$manager->id]['product']+=$p['summa'];
								}
							}
						}
					}
				}
		}
		var_dump($manager_com);
		foreach ($manager_com as $k=>$m){
			if (!isset($m['product'])){
			$m['product']=0;
			}
			if (!isset($m['video'])){
			$m['video']=0;
			}
			$summa=($m['video']+$m['product'])*$m['commission'];
			$commission=\App\Transactions\BalanceOnHome::firstOrNew(['day' => $date, 'user_id' => $k]);
			$commission->manager_commission=$summa;
			$commission->save();
		}
	}
	
}
