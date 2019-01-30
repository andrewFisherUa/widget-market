@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{!! session('message_success') !!}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{!! session('message_danger') !!}
		</div>
	@endif
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{route('advertiser.save_offer_company')}}">
			{{ csrf_field() }}
			<input type="text" name="id_company" value="{{$company->id}}" hidden style="display: none">
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Текст предложения</label>
				<div class="col-xs-9">
					<input type="text" name="name" value="" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Под текст предложения</label>
				<div class="col-xs-9">
					<input type="text" name="sub_name" value="" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Ссылка</label>
				<div class="col-xs-9">
					<input type="text" name="url" value="" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Изображение предложения</label>
				<div class="col-xs-9">
					<input type="file" class="form-control" name="img">
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">
					Добавить
				</button>
			</div>
		</form>
		
    </div>
</div>
@endsection
@push ('cabinet_home_top')
@endpush
@push ('cabinet_home_js')
@endpush