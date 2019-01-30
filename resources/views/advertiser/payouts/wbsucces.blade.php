@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{{ session('message_danger') }}
		</div>
	@endif
		<div class="row">
			<div class="col-xs-6 col-xs-offset-3">
				<form class="form-horizontal" role="form" method="post" action="{{route('advertiser.wb.payout')}}">
					{!! csrf_field() !!}
					<input type="hidden" name="wminvoiceid" value="{{$payouts->wminvoiceid}}">
					<div class="form-group">
						<label for="code" class="col-xs-4 control-label">Введите код из смс</label>
						<div class="col-xs-6">
							<input name="code" type="text" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-offset-1 col-xs-10 text-center">
							<button class="btn btn-primary">Оплатить</button>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div>
</div>

@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')

@endpush