@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
    <div class="row" style="margin-bottom: 20px;">
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
				@foreach ($birges as $birge)
					<tr>
						<td>{{$birge->name}}</td>
						<td>{{$birge->buy_cash}}</td>
						<td>{{$birge->sell_cash}}</td>
						<td>{{$birge->buy_bank}}</td>
						<td>{{$birge->sell_bank}}</td>
						<td>{{$birge->buy_wmr}}</td>
						<td>{{$birge->sell_wmr}}</td>
						<td>{{$birge->buy_ym}}</td>
						<td>{{$birge->sell_ym}}</td>
						<td>{{$birge->buy_qiwi}}</td>
						<td>{{$birge->sell_qiwi}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
    </div>
</div>
@endsection
