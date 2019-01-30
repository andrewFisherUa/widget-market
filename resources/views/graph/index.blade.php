@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<table class="table">
			<tr>
				
				<td>Ссылка</td>
				<td>Количество зачитанных показов</td>
				<td>Количество кликов</td>
				<td>Соотношение</td>
			</tr>
		@foreach($collection as $col)
			<tr>
				
				<td>{{$col->host}}</td>
				<td>{{$col->played}}</td>
				<td>{{$col->clicked}}</td>
				<td>{{$col->deep}}</td>
			</tr>
        @endforeach		
		</table>
		{!! $collection->render() !!}
    </div>
</div>
@endsection
