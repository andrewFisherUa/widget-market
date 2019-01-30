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
		<h4 class="text-center">Все блоки брендирования</h4>
			<table class="table table-hover table-bordered">
				<thead>
				<tr>
					<td>Название</td>
					<td>Редактирование</td>
					<td>Удаление</td>
				</tr>
				</thead>
				@foreach ($blocks as $block)
					<tr>
						<td>{{$block->title}}</td>
						<td><a href="{{route('brand_setting.edit.block', ['id'=>$block->id])}}" class="btn btn-primary" target="_blank">Редактирование</a></td>
						<td><a href="{{route('brand_setting.delete.block', ['id'=>$block->id])}}" class="btn btn-danger">Удалить</a></td>
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