@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<table class="table">
			<tr>
				
				<td>Площадка</td>
				<td>Запросы</td>
				<td>Взыграл</td>
				<td>Первая четверть</td>
				<td>Половина</td>
				<td>Первая четверть</td>
				<td>Окончание</td>
				<td>Досмотры</td>
				<td>Утиль</td>
			</tr>
			@foreach($collection as $col)
			<tr>
				
				<td>{{$sites[$col->pad_id]}}</td>
				<td>{{$col->plays_all}}</td>
				<td>{{$col->fplays_all}}</td>
				<td>{{$col->firstplays_all}}</td>
				<td>{{$col->midplays_all}}</td>
				<td>{{$col->thirdplays_all}}</td>
				<td>{{$col->completeplays_all}}</td>
				<td>{{$col->deep}}</td>
				<td>{{$col->util}}</td>
			</tr>
        @endforeach		
		</table>
			{!! $collection->render() !!}
    </div>
</div>
@endsection
