@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom: 20px;">
		<form class="form-inline" role="form" method="post" action="">
		{{ csrf_field() }}
		</form>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<table class="table table-hover table-bordered text-center">
				<thead>
					<tr>
						<td rowspan="2" style="vertical-align: middle">Дата</td>
						<td colspan="5">Покупка битков</td>
					</tr>
					<tr>
						<td>Количество бтс</td>
						<td>Курс закупки</td>
						<td>Курс продажи</td>
						<td>Процент</td>
						<td>Остаток</td>
					</tr>
				</thead>
				<tbody>
				@foreach ($buys as $buy)
					<tr>
						<td>{{date('Y-m-d', strtotime($buy->created))}}</td>
						<td>{{$buy->amount_btc}}</td>
						<td>{{$buy->course}}</td>
						<td>{{$buy->return_course}}</td>
						<td>{{$buy->prosent}}</td>
						<td>{{$buy->remainder}}</td>
					</tr>
				@endforeach
				<tr style="background: #000; color: #fff;">
					<td></td>
					<td></td>
					<td>{{$all['course']}}</td>
					<td></td>
					<td></td>
					<td>{{$all['remainder']}}</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-6">
			<table class="table table-hover table-bordered text-center">
				<thead>
					<tr>
						<td rowspan="2" style="vertical-align: middle">Дата</td>
						<td colspan="5">Продажа битков</td>
					</tr>
					<tr>
						<td>Количество бтс</td>
						<td>Курс закупки</td>
						<td>Курс продажи</td>
						<td>Процент</td>
						<td>Остаток</td>
					</tr>
				</thead>
				<tbody>
				 @foreach ($sells as $sell)
					<tr>
						<td>{{date('Y-m-d', strtotime($sell->created))}}</td>
						<td>{{$sell->amount_btc}}</td>
						<td>{{$sell->return_course}}</td>
						<td>{{$sell->course}}</td>
						<td>{{$sell->prosent}}</td>
						<td>{{$sell->remainder}}</td>
					</tr>
				 @endforeach
				</tbody>
			</table>
		</div>
    </div>
</div>
@endsection
