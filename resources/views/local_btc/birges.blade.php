@extends('layouts.app')

@section('content')
<div class="container">
	@if (\Auth::user()->id!=933)
	@include('local_btc.top_menu')
	@endif
	<div class="row">
		<h3 class="text-center">Курсы</h3>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td style="vertical-align: middle">Биржа</td>
					<td>Пара</td>
					<td>Bitcoin</td>
					<td>Ethereum</td>
					<td>Usd</td>
					<td>Рублю</td>
					<td>Цена в рублях на 1 биткоин</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($birges as $birge)
					<tr>
						<td>{{$birge->name}}</td>
						<td>{{$birge->fromm}} - {{$birge->tom}}</td>
						<td>{{$birge->btc}}</td>
						<td>{{$birge->eth}}</td>
						<td>{{$birge->usd}}</td>
						<td>{{$birge->rub}}</td>
						<td>
							@if ($birge->fromm=='btc')
								{{$birge->rub}}
							@else
								{{$birge->eth/$birge->btc*$birge->rub}}
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
    </div>
    <div class="row">
		<h3 class="text-center">Bitcoin</h3>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td rowspan="2" style="vertical-align: middle">Биржа</td>
					<td colspan="2">Наличные</td>
					<td colspan="2">Сбер</td>
					<td colspan="2">WebMoney</td>
					<td colspan="2">Яндекс</td>
					<td colspan="2">Qiwi</td>
				</tr>
				<tr>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($btcs as $btc)
					<tr>
						<td>{{$btc->name}}</td>
						<td>{{$btc->buy_cash}}</td>
						<td>{{$btc->sell_cash}}</td>
						<td>{{$btc->buy_bank}}</td>
						<td>{{$btc->sell_bank}}</td>
						<td>{{$btc->buy_wmr}}</td>
						<td>{{$btc->sell_wmr}}</td>
						<td>{{$btc->buy_ym}}</td>
						<td>{{$btc->sell_ym}}</td>
						<td>{{$btc->buy_qiwi}}</td>
						<td>{{$btc->sell_qiwi}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
    </div>
	
	<div class="row">
		<h3 class="text-center">Ethereum</h3>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td rowspan="2" style="vertical-align: middle">Биржа</td>
					<td colspan="2">Наличные</td>
					<td colspan="2">Сбер</td>
					<td colspan="2">WebMoney</td>
					<td colspan="2">Яндекс</td>
					<td colspan="2">Qiwi</td>
				</tr>
				<tr>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Покупка</td>
					<td>Продажа</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($eths as $eth)
					<tr>
						<td>{{$eth->name}}</td>
						<td>{{$eth->buy_cash}}</td>
						<td>{{$eth->sell_cash}}</td>
						<td>{{$eth->buy_bank}}</td>
						<td>{{$eth->sell_bank}}</td>
						<td>{{$eth->buy_wmr}}</td>
						<td>{{$eth->sell_wmr}}</td>
						<td>{{$eth->buy_ym}}</td>
						<td>{{$eth->sell_ym}}</td>
						<td>{{$eth->buy_qiwi}}</td>
						<td>{{$eth->sell_qiwi}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
    </div>
</div>
@endsection
