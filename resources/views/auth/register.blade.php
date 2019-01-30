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
                <div class="panel-heading">Регистрация</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}
						<input type="text" name="referer" value="{{Cookie::get('aid')}}" hidden style="display: none">
						<input type="text" name="link" value="{{Cookie::get('link')}}" hidden style="display: none">
						<div class="form-group">
                            <div class="col-xs-6 col-xs-offset-4">
								<div class="radio_buttons">
									<div>
										<input type="radio" name="type" value="1" id="radio1" @if (old('type')!=2) checked @endif />
										<label for="radio1">Вебмастер</label>
									</div>
									{{--<div>
										<input type="radio" name="type" value="2" id="radio2" @if (old('type')==2) checked @endif />
										<label for="radio2">Рекламодатель</label>
									</div>--}}
								</div>
							</div>
                        </div>
						
                        <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                            <label for="firstname" class="col-xs-4 control-label">Имя</label>

                            <div class="col-xs-6">
                                <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required autofocus>

                                @if ($errors->has('firstname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('firstname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
                            <label for="lastname" class="col-xs-4 control-label">Фамилия</label>

                            <div class="col-xs-6">
                                <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required autofocus>

                                @if ($errors->has('lastname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

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

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-xs-4 control-label">Пароль</label>

                            <div class="col-xs-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-xs-4 control-label">Повторите пароль</label>

                            <div class="col-xs-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
						 <div class="form-group">
                            <label for="password-confirm" class="col-xs-4 control-label"></label>

                            <div class="col-xs-6">
                                <input id="prav" type="checkbox"> Нажимая кнопку «Зарегистрироваться», Вы подтверждаете, что ознакомились с <a href="https://partner.market-place.su/faq.php" target="_blank"><b>правилами проекта</b></a>
                            </div>
                        </div>
						
                        <div class="form-group">
                            <div class="col-xs-6 col-xs-offset-4">
                                <button id="reg" type="submit" class="btn btn-primary" disabled="true" >
                                    Зарегистрироваться
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
@push ('cabinet_home_js')
	<script>
	$(document).ready(function() {
		$('#prav').change(function(){
			if ($('#prav').prop('checked')==true){
				$('#reg').prop('disabled', false);
			}
			else{
				$('#reg').prop('disabled', true);
			}
		});
	});
	</script>
@endpush
@push('registration')
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	<link href="{{ asset('css/register_custom.css') }}" rel="stylesheet">
@endpush
