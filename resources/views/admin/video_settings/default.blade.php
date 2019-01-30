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
		@include('admin.video_settings.top_menu')
	</div>
	<div class="row">
		<h4 class="text-center">Дефолтовые настройки</h4>
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<td>Название</td>
					<td>Блок Россия</td>
					<td>Блок Мобильный</td>
					<td>Блок СНГ</td>
					<td>Коммиссия Россия</td>
					<td>Коммиссия СНГ</td>
					<td>Действие</td>
				</tr>
			</thead>
			@foreach ($defaults as $default)
				<tr>
					<td>{{$default->name}}</td>
					<td>{{\App\VideoBlock::where('id', $default->block_rus)->first()->name}}</td>
					<td>{{\App\VideoBlock::where('id', $default->block_mobile)->first()->name}}</td>
					<td>{{\App\VideoBlock::where('id', $default->block_cis)->first()->name}}</td>
					<td>{{\DB::table('сommission_groups')->where('commissiongroupid', $default->commission_rus)->first()->label}}</td>
					<td>{{\DB::table('сommission_groups')->where('commissiongroupid', $default->commission_cis)->first()->label}}</td>
					<td><a href="{{route('video_setting.default.id', ['id'=>$default->id])}}" class="btn btn-primary" target="_blank">Редактировать</a></td>
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