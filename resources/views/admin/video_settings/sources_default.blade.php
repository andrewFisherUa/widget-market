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
		<h4 class="text-center">Все видео ссылки</h4>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-4"><b>Название</b></div>
				<div class="col-xs-4"><b>Цена Россия</b></div>
				<div class="col-xs-4"><b>Цена СНГ</b></div>
			</div>
			<hr style="margin: 5px 0;">
			<form role="form" method="post" action="{{route('video_setting.sources.defolte.post')}}">
			{!! csrf_field() !!}
				@foreach ($sources as $source)
					<div class="row">
						<div class="col-xs-4">{{$source->title}}</div>
						<input type="text" name="id[]" value="{{$source->id}}" hidden style="display: none">
						<div class="col-xs-4"><input type="number" name="rus[]" value="{{$source->summa_rus}}"></div>
						<div class="col-xs-4"><input type="number" name="cis[]" value="{{$source->summa_cis}}"></div>
					</div>
					<hr style="margin: 5px 0;">
				@endforeach
				<div class="row">
					<div class="col-xs-12 text-center input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<style>
		.control-label{
			margin-top: 7px!important;
		}
		.form-inline{
		margin-bottom: 20px;
		}
	</style>
@endpush