@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
		@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		@if (Session::has('message_war'))
		<div class="alert alert-warning">
			{!! session('message_war') !!}
		</div>
		@endif
            <div class="panel panel-default">
                <div class="panel-heading">Запрос на повторное подтверждение email</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ url('register_repeat') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-xs-4 control-label">E-Mail адрес</label>

                            <div class="col-xs-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-6 col-xs-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Запросить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('registration')
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endpush
