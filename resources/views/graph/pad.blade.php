@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<table class="table"border="1">
		<tr>
		<td>
		pid
		</td>
		<td>
		Дата
		</td>
		<td>
		РОССИЯ (Запросы)
		</td>
		<td>
		РОССИЯ (Показы)
		</td>
		<td>
		РОССИЯ (Клики)
		</td>
		<td>
        РОССИЯ (Сумма)
		</td>
		<td>
        РОССИЯ (За загрузку)
		</td>
		<td>
		СНГ (Запросы)
		</td>
		<td>
		СНГ (Показы)
		</td>
		<td>
		СНГ (Клики)
		</td>
		<td>
		СНГ (Сумма)
		</td>
		<td>
        СНГ (За загрузку)
		</td>
		</tr>
        @foreach($collection as $col)
		<tr>
		<td>
		<a href="./{{$col->pid}}">{{$col->pid}}</a>
		</td>
		<td>
		{{$col->day}}
		</td>
		<td>
		{{$col->russian_requests}}
		</td>
		<td>
		{{$col->russian_views}}
		</td>
		<td>
		{{$col->russian_clicks}}
		</td>
		<td>
		{{$col->russian_summa}}
		</td>
		<td>
		{{$col->russian_avg}}
		</td>
		<td>
		{{$col->forign_requests}}
		</td>
		<td>
		{{$col->forign_views}}
		</td>
		<td>
		{{$col->forign_clicks}}
		</td>
		<td>
		{{$col->forign_summa}}
		</td>
		<td>
		{{$col->forign_avg}}
		</td>
		</tr>
        @endforeach
		</table>
			
    </div>
</div>
@endsection