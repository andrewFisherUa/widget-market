@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<td rowspan="2">Название</td>
					<td colspan="2">Сумма</td>
					<td colspan="2">Контрольная сумма</td>
					<td colspan="2">Утиль</td>
					<td colspan="2">Глубина</td>
					<td colspan="2">Запросы</td>
					<td colspan="2">Показы</td>
					<td rowspan="2"></td>
				</tr>
				<tr>
					<td>Россия</td>
					<td>СНГ</td>
					<td>Россия</td>
					<td>СНГ</td>
					<td>Россия</td>
					<td>СНГ</td>
					<td>Россия</td>
					<td>СНГ</td>
					<td>Россия</td>
					<td>СНГ</td>
					<td>Россия</td>
					<td>СНГ</td>
				</tr>
			</thead>
			<tbody>
			@foreach ($pids as $pid)
				<tr>
					<td>{{$pid["day"]}}</td>
					<td>{{$pid["ru_summa"]}}</td>
					<td>{{$pid["ukr_summa"]}}</td>
					<td>{{$pid["ru_control_summa"]}}</td>		
     
					<td>{{$pid["ukr_control_summa"]}}</td>		
                    <td>{{$pid["ru_util"]}}</td>	
                    <td>{{$pid["ukr_util"]}}</td>
                    <td>{{$pid["ru_deep"]}}</td>	
                    <td>{{$pid["ukr_deep"]}}</td>	
                    <td>{{$pid["ru_loaded"]}}</td>	
                    <td>{{$pid["ukr_loaded"]}}</td>
                    <td>{{$pid["ru_played"]}} / {{$pid["ru_calculate"]}}</td>	
                    <td>{{$pid["ukr_played"]}} / {{$pid["ukr_calculate"]}}</td>						
				</tr>	
			@endforeach
			</tbody>

		</table>
	</div>
</div>
@endsection

