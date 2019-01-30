@extends('layouts.app')

@section('content')
<table class="table table-condensed table-hover widget-table">
	<thead>
		<tr>
			<td>пид</td>
			<td>страна</td>
			<td>сумма</td>
			<td>к_сумма</td>
			<td>загрузки</td>
			<td>к_загрузки</td>
			<td>показы</td>
			<td>к_показы</td>
			<td>засчитаные</td>
			<td>к_засчитаные</td>
			<td>1 показ на загрузку</td>
			<td>к_1 показ на загрузку</td>
			<td>комплит</td>
			<td>к_комплит</td>
			<td>клик</td>
			<td>к_клик</td>
			<td>старт</td>
			<td>к_старт</td>
			<td>дорогие_повторы</td>
			<td>дорогие_повторы на загрузку</td>
			<td>дорогие_повторы_сумма</td>
			<td>дешевые_повторы</td>
			<td>дешевые_повторы на загрузку</td>
			<td>дешевые_повторы_сумма</td>
			<td>аренда</td>
		</tr>
	</thead>
	@foreach ($data as $d)
		<tr>
			<td>{{$d['pid']}}</td>
			<td>{{$d['country']}}</td>
			<td>{{$d['summa']}}</td>
			<td>{{$d['control_summa']}}</td>
			<td>{{$d['loaded']}}</td>
			<td>{{$d['control_loaded']}}</td>
			<td>{{$d['played']}}</td>
			<td>{{$d['control_played']}}</td>
			<td>{{$d['calculate']}}</td>
			<td>{{$d['control_calculate']}}</td>
			<td>{{$d['one_played']}}</td>
			<td>{{$d['control_one_played']}}</td>
			<td>{{$d['completed']}}</td>
			<td>{{$d['control_completed']}}</td>
			<td>{{$d['clicks']}}</td>
			<td>{{$d['control_clicks']}}</td>
			<td>{{$d['started']}}</td>
			<td>{{$d['control_started']}}</td>
			<td>{{$d['second_expensive_all']}}</td>
			<td>{{$d['second_expensive']}}</td>
			<td>{{$d['second_expensive_summa']}}</td>
			<td>{{$d['second_cheap_all']}}</td>
			<td>{{$d['second_cheap']}}</td>
			<td>{{$d['second_cheap_summa']}}</td>
			<td>{{$d['lease_summa']}}</td>
		</tr>
	@endforeach
</table>
@endsection