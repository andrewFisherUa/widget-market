@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td rowspan="3">src</td>
					<td colspan="6">Россия</td>
					<td colspan="6">СНГ</td>
				</tr>
				<tr>
					<td colspan="3">Десктоп</td>
					<td colspan="3">Мобильный</td>
					<td colspan="3">Десктоп</td>
					<td colspan="3">Мобильный</td>
				</tr>
				<tr>
					<td>Загрузка</td>
					<td>Ошибка</td>
					<td>Удачно</td>
					
					<td>Загрузка</td>
					<td>Ошибка</td>
					<td>Удачно</td>
					
					<td>Загрузка</td>
					<td>Ошибка</td>
					<td>Удачно</td>
					
					<td>Загрузка</td>
					<td>Ошибка</td>
					<td>Удачно</td>
				</tr>
			</thead>
			<tbody>
			@foreach($values as $v)
			<tr>
					<td>{{$v->title}}</td>
					

					<td>{{$v->ru_start}}</td>
					<td>{{$v->ru_error}}</td>
					<td>{{$v->ru_success}}</td>
					
					<td>{{$v->cis_start}}</td>
					<td>{{$v->cis_error}}</td>
					<td>{{$v->cis_success}}</td>
					
					<td>{{$v->desc_start}}</td>
					<td>{{$v->desc_error}}</td>
					<td>{{$v->desc_success}}</td>
					
					<td>{{$v->mob_start}}</td>
					<td>{{$v->mob_error}}</td>
					<td>{{$v->mob_success}}</td>
				</tr>
			
			@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
@push('cabinet_home')
	
@endpush
@push('cabinet_home_js')
	

@endpush