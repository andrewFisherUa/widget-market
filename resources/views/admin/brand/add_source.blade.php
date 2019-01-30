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
		<h4 class="text-center">Добавление ссылки на брендирование</h4>
			<form class="form-horizontal" enctype="multipart/form-data" method="post" action="">
			{!! csrf_field() !!}
				<div class="form-group @if ($errors->has('title')) has-error @endif">
					<label for="title" class="col-xs-4 control-label">Название</label>
					<div class="col-xs-6">
						<input type="text" name="title" value="{{old('title')}}" class="form-control" required>
						@if ($errors->has('title'))
							<span class="help-block">
								<strong>{{ $errors->first('title') }}</strong>
							</span>
						@endif
					</div>
				</div>
				<div class="form-group @if ($errors->has('src')) has-error @endif">
					<label for="src" class="col-xs-4 control-label">Ссылка</label>
					<div class="col-xs-6">
						<input type="text" name="src" value="{{old('src')}}" class="form-control" required>
						@if ($errors->has('src'))
							<span class="help-block">
								<strong>{{ $errors->first('src') }}</strong>
							</span>
						@endif
					</div>
				</div>
				<div class="form-group @if ($errors->has('img')) has-error @endif">
					<label for="img" class="col-xs-4 control-label">Картинка</label>
					<div class="col-xs-6">
						<input type="file" class="form-control" value="{{old('img')}}" name="img" required>
						@if ($errors->has('img'))
							<span class="help-block">
								<strong>{{ $errors->first('img') }}</strong>
							</span>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="summa_rus" class="col-xs-4 control-label">Цена Россия</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="summa_rus" value="{{old('summa_rus')}}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="summa_cis" class="col-xs-4 control-label">Цена СНГ</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="summa_cis" value="{{old('summa_cis')}}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-xs-offset-3 text-center">
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
	</div>
</div>
@endsection
@push('cabinet_home')

@endpush
@push('cabinet_home_js')

@endpush