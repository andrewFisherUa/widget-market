@extends('layouts.app')

@section('content')
<div class="container">

@if($config['pref'] && isset($config["wparams"]["id_user"]))
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',$config['wparams'])}}" class="btn btn-primary">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',$config['wparams'])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.balance_history',['id_user'=>$config['wparams']['id_user'],'shop_id'=>0])}}" class="btn btn-primary">Взаиморасчёты пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$config['wparams']['id_user'],'shop_id'=>0])}}" class="btn btn-primary">Статистика всех рекламных компаний пользователя</a>
<a href="{{ route('admin.disco',$config['wparams'])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@endif

    <div class="row">
	@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{!! session('message_danger') !!}
		</div>
		@endif
		
		<div class="col-xs-12 col-xs-12">
		<ul class="nav nav-tabs nav-justified cust-tabs" role="tablist" id="myTabs">
				<li role="presentation" class="active"><a style="padding: 10px 0;" href="#personal" aria-controls="personal" role="tab" data-toggle="tab">Личные данные</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#payments" aria-controls="mobile-area" role="tab" data-toggle="tab">Платежные реквизиты</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#partner_pads" aria-controls="code" role="tab" data-toggle="tab">Рекламные площадки</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#password" aria-controls="code" role="tab" data-toggle="tab">Смена пароля</a></li>
			</ul>
			<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="personal">
					<div class="row">
						<div class="col-xs-6 col-xs-offset-0 personal_form" style="margin-left: 15px;">
							<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ route('profile.personal.save') }}">
							{{ csrf_field() }}
								<div class="form-group">
									<input type="text" id="id" name="id" value="{{$userProf->id}}" readonly hidden>
								</div>
								<div class="form-group {{ $errors->has('avatar') ? ' has-error' : '' }}">
									<label for="avatar" class="col-xs-6 control-label personal_label">Аватар:
									@if ($userProf->avatar)
									<img class="profile_avatar" src="/images/avatars/{{$userProf->avatar}}">
									@endif
									</label>
									<div class="col-xs-6">
										<input id="avatar" type="file" class="form-control" name="avatar" value="">
										@if ($errors->has('avatar'))
												<span class="help-block">
													<strong>{{ $errors->first('avatar') }}</strong>
												</span>
											@endif
									</div>
								</div>
								<div class="form-group">
									<label for="lastname" class="col-xs-6 control-label personal_label">Фамилия:</label>
									<div class="col-xs-6">
										<input id="lastname" type="text" class="form-control" placeholder="Фамилия" name="lastname" value="{{$userProf->lastname}}" required>
									</div>
								</div>
								<div class="form-group">
									<label for="firstname" class="col-xs-6 control-label personal_label">Имя:</label>
									<div class="col-xs-6">
										<input id="firstname" type="text" class="form-control" placeholder="Имя" name="firstname" value="{{$userProf->firstname}}" required>
									</div>
								</div>
								<div class="form-group">
									<label for="phone" class="col-xs-6 control-label personal_label">Контактный телефон:</label>
									<div class="col-xs-6">
										<input id="phone" type="text" class="form-control bfh-phone" name="phone" placeholder="Телефон" value="{{$userProf->phone}}" data-format="+7 (ddd) ddd-dddd" maxlength="17">
									</div>
								</div>
								<div class="form-group">
									<label for="icq" class="col-xs-6 control-label personal_label">ICQ:</label>
									<div class="col-xs-6">
										<input id="icq" type="text" class="form-control" name="icq" placeholder="Icq" value="{{$userProf->icq}}">
									</div>
								</div>
								<div class="form-group">
									<label for="skype" class="col-xs-6 control-label personal_label">Skype:</label>
									<div class="col-xs-6">
										<input id="skype" type="text" class="form-control" name="skype" placeholder="Skype" value="{{$userProf->skype}}">
									</div>
								</div>
								@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
									<div class="form-group">
										<label for="unsubscribe" class="col-xs-6 control-label personal_label">Отписаться от рассылки</label>
										<div class="col-xs-6">
											<input type="checkbox" name="unsubscribe" style="margin-top: 11px;" @if ($userProf->user->unsubscribe) checked @endif value="1">
										</div>
									</div>
								@endif
								<div class="form-group">
									<label for="submit" class="col-xs-6 control-label personal_label"></label>
									<div class="col-xs-6">
										<button type="submit" class="btn btn-success">
										Сохранить
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="payments">
                @if ($requisite)
                @include('advertiser.payouts.payout.edit_requisite',['requisite'=>$requisite])
                @else
                @include('advertiser.payouts.payout.create_requisite')
                @endif
				

					
					
					
				</div>
				<div role="tabpanel" class="tab-pane" id="partner_pads">
					<div class="row">
						<div class="col-xs-10 col-xs-offset-0 personal_form"  style="margin-left: 15px;">
						<table class="table table-condensed table-hover">
								<thead>
									<tr>
										<td>Площадка</td>
										<td>Статус</td>
										<td>Дата добавления</td>
									</tr>
								</thead>
								<tbody>
								@foreach($advertises as $adv)
								    <tr>
										<td>{{$adv->name}}</td>
										<td>{{$adv->statname}}</td>
										<td>{{$adv->created_at}}</td>
									</tr>
								@endforeach
								</tbody>
						</table>		
						
						
						
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="password">
					<div class="row">
						<div class="col-xs-6  personal_form col-xs-offset-0" style="margin-left: 15px;">
							<form class="form-horizontal" role="form" method="POST" action="{{route('password.change')}}">
							{{ csrf_field() }}
								<div class="form-group">
									<input type="text" id="id" name="id" value="{{$userProf->id}}" readonly hidden>
								</div>
								<div class="form-group">
									<label for="old_pass" class="col-xs-6 control-label personal_label">Введите прежний пароль:</label>
									<div class="col-xs-6">
										<input type="password" class="form-control" placeholder="Прежний пароль" name="old_pass" value="{{ old('old_pass') }}" required>
									</div>
								</div>
								<div class="form-group{{ $errors->has('new_pass') ? ' has-error' : '' }}">
									<label for="new_pass" class="col-md-6 control-label personal_label">Введите новый пароль:</label>
									<div class="col-xs-6">
										<input type="password" class="form-control" placeholder="Новый пароль" name="new_pass" value="{{ old('new_pass') }}" required>
										@if ($errors->has('new_pass'))
											<span class="help-block">
												<strong>{{ $errors->first('new_pass') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-group">
									<label for="confirm_new_pass" class="col-xs-6 control-label personal_label">Повторите новый пароль:</label>
									<div class="col-xs-6">
										<input type="password" class="form-control" placeholder="Повторите новый пароль" name="new_pass_confirmation" value="{{ old('new_pass_confirmation') }}" required>
									</div>
								</div>
								<div class="form-group">
									<label for="submit" class="col-xs-6 control-label personal_label"></label>
									<div class="col-xs-6">
										<button type="submit" class="btn btn-success">
										Сохранить
										</button>
									</div>
								</div>
							</form>							
						</div>
					</div>
				</div>				
				
				
				
				
			</div>
			
		</div>

	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	<style>
	.radio_buttons {
    font-size: 14px
	}
	.radio_buttons div {
		float: left;
	}
	.radio_buttons input {
		position: absolute;
		left: -9999px;
	}
	.radio_buttons label {
		display: block;
		margin: 0px;
		padding: 8px 10px;
		border: 1px solid transparent;
		color: #333;
		background-color: #ebebeb;
		border-color: #1f648b;
		cursor: pointer;
	}
	.radio_buttons input:checked + label {
		color: #fff;
		background-color: #3097D1;
		border-color: #1f648b;
	}
	.radio_buttons div:first-child label {
		margin-left: 0;
		border-top-left-radius: 4px;
		border-bottom-left-radius: 4px;
	}
	.radio_buttons div:last-child label {
		border-top-right-radius: 4px;
		border-bottom-right-radius: 4px;
	}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="{{ asset('js/bootstrap-formhelpers-phone.js') }}"></script>
		<script>
      	$(function(){
        if(window.location.hash) {
         $('a[href="' + window.location.hash + '"]').tab('show');
        }
        });
	</script>
@endpush