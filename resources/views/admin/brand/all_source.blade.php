@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		@if (Session::has('message_warning'))
			<div class="alert alert-warning">
				{{ session('message_warning') }}
			</div>
		@endif
	</div>
	<div class="row">
		<h4 class="text-center">Ссылки на брендирование</h4>
			<table class="table table-hover table-bordered">
				<thead>
				<tr>
					<td>Название</td>
					<td>Ссылка</td>
					<td>Картинка</td>
					<td>Редактирование</td>
					<td>Удаление</td>
				</tr>
				</thead>
				@foreach ($brands as $brand)
					<tr>
						<td>{{$brand->title}}</td>
						<td>{{$brand->src}}</td>
						<td>
							<img style="width: 200px; height: auto" src="//storage.market-place.su/brand_img/{{$brand->img}}">
						</td>
						<td><a href="{{route('brand_setting.edit.source', ['id'=>$brand->id])}}" target="_blank" class="btn btn-primary">Редактировать</a></td>
						<td><a href="{{route('brand_setting.delete.source', ['id'=>$brand->id])}}" class="btn btn-danger">Удалить</a></td>
					</tr>
				@endforeach
			</table>
	</div>
</div>
@endsection
@push('cabinet_home')

@endpush
@push('cabinet_home_js')

@endpush