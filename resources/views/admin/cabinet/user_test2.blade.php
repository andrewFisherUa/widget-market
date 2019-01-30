<div class="affiliate_balance text-center">@if ($user->profile->balance==0)0.00 @else{{$user->profile->balance}}@endif <span class="rur">q</span>
	@if ($user->profile->auto_payment==1)
		<span class="glyphicon glyphicon-exclamation-sign color-red" style="font-size: 20px; line-height: 1; position: relative; top: -6px;" data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" title="Включен автозаказ выплат"></span>
	@endif
</div>
<p class="text-center"><a href="#" data-toggle="modal" data-target="#payment" class="btn btn-primary" role="button">Заказать выплату</a></p>
<p class="text-center"><a href="#" data-toggle="modal" data-target="#auto_payment" class="btn btn-success" role="button">Автозаказ выплат</a></p>
<!--{{$today=\App\Transactions\BalanceOnHome::where('user_id', $user->id)->where('day', date("Y-m-d"))->first()}}-->
<!--@if ($today)
{{$today_balance=round($today->video_commission+$today->product_commission+$today->referal_commission+$today->manager_commission,2)}}
@else
{{$today_balance=0.00}}
@endif-->
<!--{{$yesterday=\App\Transactions\UserTransactionLog::where('user_id', $user->id)->where('day', date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS")))->first()}}-->
<!--@if ($yesterday)
{{$yesterday_balance=round($yesterday->commission,2)}}
@else
{{$yesterday_balance=0.00}}
@endif-->
<!--{{$week=\App\Transactions\UserTransactionLog::where('user_id', $user->id)->whereBetween('day', [date("Y-m-d",strtotime(date("Y-m-d")." - 8 DAYS")), date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS"))])->sum('commission')}}-->
<!--@if ($week)
{{$week_balance=round($week,2)}}
@else
{{$week_balance=0.00}}
@endif-->
<!--{{$month=\App\Transactions\UserTransactionLog::where('user_id', $user->id)->whereBetween('day', [date("Y-m-d",strtotime(date("Y-m-d")." - 31 DAYS")), date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS"))])->sum('commission')}}-->
<!--@if ($month)
{{$month_balance=round($month,2)}}
@else
{{$month_balance=0.00}}
@endif-->
<div class="affiliate_detal_balance"><span>Сегодня:</span><div class="right green">	+ {{$today_balance}}<span class="rur"> q</span></div></div>
<div class="affiliate_detal_balance"><span>Вчера:</span><div class="right">{{$yesterday_balance}} <span class="rur">q</span></div></div>
<div class="affiliate_detal_balance"><span>Неделя:</span><div class="right">{{$week_balance}} <span class="rur">q</span></div></div>
<div class="affiliate_detal_balance" style="border-bottom: 0;"><span>Месяц:</span><div class="right">{{$month_balance}} <span class="rur">q</span></div></div>
@include('affiliate.cabinet.test_auto_payment')
@include('affiliate.cabinet.test_payment')