@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom: 20px;">
		<form class="form-inline" role="form" method="post" action="">
		{{ csrf_field() }}
		</form>
	</div>
	<div class="row">
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td rowspan="2" style="vertical-align: middle">Дата</td>
					<td colspan="3">Покупка Биктоинов</td>
					<td colspan="3">Продажа Биктоинов</td>
					<td rowspan="2" style="vertical-align: middle">Процент</td>
				</tr>
				<tr>
					<td>Yandex</td>
					<td>Btc</td>
					<td>Курс</td>
					<td>Yandex</td>
					<td>Btc</td>
					<td>Курс</td>
				</tr>
			</thead>
			<tr style="background: #000; color: #fff">
				<td>Всего</td>
				<td>{{$all['amount_buy']}}</td>
				<td>{{$all['amount_btc_buy']}}</td>
				<td>{{$all['curs_buy']}}</td>
				<td>{{$all['amount_sell']}}</td>
				<td>{{$all['amount_btc_sell']}}</td>
				<td>{{$all['curs_sell']}}</td>
				<td>
				@if ($all['curs_sell'] and $all['curs_buy'])
					{{($all['curs_sell']-$all['curs_buy'])/$all['curs_sell']*100}}
				@endif
				</td>
			</tr>
			@foreach ($stats as $stat)
				<tr>
					<td>{{$stat['day']}}</td>
					<td>{{$stat['amount_buy']}}</td>
					<td>{{$stat['amount_btc_buy']}}</td>
					<td>{{$stat['curs_buy']}}</td>
					<td>{{$stat['amount_sell']}}</td>
					<td>{{$stat['amount_btc_sell']}}</td>
					<td>{{$stat['curs_sell']}}</td>
					<td>
						@if ($stat['curs_sell'] and $stat['curs_buy'])
							{{($stat['curs_sell']-$stat['curs_buy'])/$stat['curs_sell']*100}}
						@endif
					</td>
				</tr>
			@endforeach
		</table>
    </div>
</div>
@endsection
