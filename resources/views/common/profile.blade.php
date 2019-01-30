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
			{!! session('message_danger') !!}
		</div>
		@endif
		<div class="col-xs-12 col-xs-12">
			<ul class="nav nav-tabs nav-justified cust-tabs" role="tablist" id="myTabs">
				<li role="presentation" class="active"><a style="padding: 10px 0;" href="#personal" aria-controls="personal" role="tab" data-toggle="tab">Личные данные</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#payments" aria-controls="mobile-area" role="tab" data-toggle="tab">Платежные реквизиты</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#partner_pads" aria-controls="code" role="tab" data-toggle="tab">Рекламные площадки</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#password" aria-controls="code" role="tab" data-toggle="tab">Смена пароля</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#refers" aria-controls="code" role="tab" data-toggle="tab">Реферальная система</a></li>
				<li role="presentation"><a style="padding: 10px 0;" href="#payouts" aria-controls="code" role="tab" data-toggle="tab">Выплаты</a></li>
			</ul>
			<!-- Tab panes -->
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
								@if($currentRole<100)
									<div class="form-group">
										<label for="client_role" class="col-xs-6 control-label personal_label">Привилегии</label>
										<div class="col-xs-6">
										  <select name= "client_role">
                                                                                    <option value = 0 @if($currentRole==0) selected @endif>Нет</option>
                                                                                    <option value =1  @if($currentRole==1) selected @endif >Вебмастер</option>
                                                                                  </select>
										</div>
									</div>
                                                                   @endif  
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
					<div class="row">
						<div class="col-xs-6 col-xs-offset-0 personal_form" style="margin-left: 15px;">
							<form class="form-horizontal payreq" enctype="multipart/form-data" role="form" method="POST" action="{{ route('profile.payments.save') }}">
							{{ csrf_field() }}
								<div class="form-group">
									<input type="text" id="id" name="id" value="{{$userProf->user_id}}" readonly style="display: none">
								</div>
								@foreach($payreq as $payreq)
									<div @if ($payreq->id==2) style="display: none;" @endif class="form-group">
										<h4 class="col-xs-8 header-payment">{{$payreq->name}} @if ($payreq->id==6) <span>(Коммиссия 3%)</span> @endif</h4>
											<div class="col-xs-4 text-right">
												<div class="radio_buttons" style="display: inline-block; margin: 0;">
													<div>
														<input type="radio" name="preference" value="{{$payreq->id}}" id="radio{{$payreq->id}}" @if($userProf->payment_option_id==$payreq->id) checked @endif>
														<label for="radio{{$payreq->id}}">Предпочтение</label>
													</div>
												</div>
											</div>
										@if ($payreq->id==6)
										<label for="{{$payreq->name}}" class="col-xs-6 control-label personal_label" style="margin-bottom: 10px;">Название банка:</label>
										<div class="col-xs-6" style="margin-bottom: 10px;">
											<input type="text" class="form-control" name="" value="Сбербанк" readonly>
										</div>
										@endif
										<label for="{{$payreq->name}}" class="col-xs-6 control-label personal_label">{{$payreq->label}}:</label>
										<div class="col-xs-6">
											<input type="text" class="form-control" name="pay[]" @foreach($UserPay as $up) @if($payreq->id==$up->payment_id) value="{{$up->value}}" @endif @endforeach>
										</div>
									</div>
									<hr class="person-hr">
								@endforeach
								<div class="form-group">
									<label for="submit" class="col-xs-6 control-label person-form"></label>
									<div class="col-xs-6">
										<button type="submit" class="btn btn-success">
										Сохранить
										</button>
									</div>
								</div>
							</form>
						</div>
						<div class="col-xs-5 col-xs-offset-0 personal_form_right">
							<h3 class="right_header">Внимание!</h3>
							<p>Администратор ПП самостоятельно выберет вариант для вывода из нижеперечисленных реквизитов. Заполните как можно больше вариантов, это поможет ускорить процесс вывода.</p>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="partner_pads">
					<div class="row">
						<div class="col-xs-10 col-xs-offset-0 personal_form"  style="margin-left: 15px;">
							<table class="table table-condensed table-hover">
								<thead>
									<tr>
										<td>Площадка</td>
										<td>Статус</td>
										<td>Статистика</td>
										<td>Логин</td>
										<td>Пароль</td>
										<td>Дата добавления</td>
									</tr>
								</thead>
								@foreach ($pads as $pad)
									<tr>
										<td>{{$pad->domain}}</td>
										<td>
										@if ($pad->status==0)
												<span data-toggle="tooltip" data-placement="bottom" title="На модерации" class="glyphicon glyphicon-time affiliate_all_pads_domain_gliph blue"></span>
											@elseif ($pad->status==2)
												<span data-toggle="tooltip" data-placement="bottom" title="Отклонена" class="glyphicon glyphicon-remove-circle affiliate_all_pads_domain_gliph red"></span>
											@elseif ($pad->status==1)
												@if ($pad->type==-1 or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13)
													<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на товарный виджет" class="glyphicon glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green"></span>
												@endif
												@if ($pad->type==-1 or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14)
													<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на видео виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green"></span>
												@endif
												@if ($pad->type==-1 or $pad->type==4 or $pad->type==5 or $pad->type==6 or $pad->type==7 or $pad->type==12 or $pad->type==13 or $pad->type==14)
													<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на тизерный виджет" class="glyphicon glyphicon glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green"></span>
												@endif
												@if ($pad->type==-1 or $pad->type==8 or $pad->type==9 or $pad->type==10 or $pad->type==11 or $pad->type==12 or $pad->type==13 or $pad->type==14)
													<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на брендирование" class="glyphicon glyphicon-picture affiliate_all_pads_domain_gliph green"></span>
												@endif
										@endif
										</td>
										<td>{{$pad->stcurl}}</td>
										<td>{{$pad->stclogin}}</td>
										<td>{{$pad->stcpassword}}</td>
										<td>{{$pad->created_at}}</td>
									</tr>
								@endforeach
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
				<div role="tabpanel" class="tab-pane" id="refers">
					<div class="row">
						<div class="col-xs-10  personal_form col-xs-offset-1">
							<h3 class="right_header">Реферальная система</h3>
							<p>Привлечение рефералов является одним из самых эффективных способов получения пассивного дохода, ведь Вы получаете процент 
							от заработка привлеченных Вами пользователей. Для того, чтобы привлеченные Вами пользователи становились Вашими рефералами 
							пользователь должен зарегистрироваться по Вашей реферальной ссылке. Если приведённый клиент будет с ботным трафиком, то за такого партнёра бонус зачисляться не будет.<br>
							Мы платим 5% от суммы заработанных средств партнера тому, кто привел его в нашу систему. Срок жизни реферальной cookie составляет 5 суток.</p>
							<p>Реферальная ссылка: https://partner.market-place.su/?aid={{$userProf->refer_id}}</p>
						</div>
						@if (count($referals)>0)
						<div class="col-xs-10  personal_form col-xs-offset-1">
							<table class="table table-condensed table-hover">
								<thead>
									<tr>
										<td>Дата</td>
										<td>Реферал</td>
										<td>Сумма</td>
									</tr>
								</thead>
								<tbody>
									<tr style="background: #000; color: #fff;">
										<td>Всего</td>
										<td></td>
										<td>{{$referals_all}}</td>
									</tr>
									@foreach ($referals as $com)
										@php $hists=explode(";", $com['history']) @endphp
										@foreach ($hists as $hist)
											@php $req=explode(":", $hist) @endphp
											@if ($req['0'] and $req['1'])
												<!-- {{$user=\App\UserProfile::where('user_id', $req['0'])->first()}}-->
												@if ($user)
												<tr>
													<td>{{$com['day']}}</td>
													<td>{{$user->name}}</td>
													<td>{{$req['1']}}</td>
												</tr>
												@endif
											@endif
										@endforeach
									@endforeach
								</tbody>
							</table>
							{{$referals->render()}}
						</div>
						@endif
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="payouts">
					<div class="row">
						<div class="col-xs-7 col-xs-offset-0 personal_form" style="margin-left: 15px;">
							<table class="table table-condensed table-hover">
								<thead>
									<tr class="text-center">
										<td>Дата создания заявки</td>
										<td>Дата закрытия заявки</td>
										<td>Сумма</td>
										<td>Статус</td>
										<td>Остаток</td>
										<td>Отменить</td>
									</tr>
								</thead>
								<tbody>
									@foreach ($payouts as $payout)
										<tr  class="text-center">
											<td>{{$payout->time_payout}}</td>
											<td>
											@if ($payout->exit_time_payout)
											{{date("Y-m-d",strtotime("$payout->exit_time_payout"))}}
											@endif
											</td>
											<td>{{$payout->payout}}</td>
											<td>
											@if ($payout->status==0)
												Ожидает
											@elseif ($payout->status==1)
												Оплачено
											@elseif ($payout->status==2)
												Отказано
											@elseif ($payout->status==3)
												Отменено пользователем
											@endif
											</td>
											<td>{{$payout->balance}}</td>
											<td>
											@if ($payout->status==0)
												<form class="form-inline" role="form" method="post" action="{{route('action_payouts_user')}}">
													{!! csrf_field() !!}
													<input type="text" name="id" value="{{$payout->id}}" style="display: none">
													<input type="text" name="status" value="3" style="display: none">
													<div class="col-xs-12 input-group form-group text-center">
														<button type="submit" class="btn btn-primary">Отменить</button>
													</div>
												</form>
											@else
												Нельзя отменить
											@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							{!! $payouts->render() !!}
						</div>
						<div class="col-xs-4 personal_form_right">
							<h3 class="right_header">Вывод средств</h3>
							<!--{{ $all_options = count(\DB::table('users_payment_options')->where('user_id', $userProf->user_id)->whereNotNull('value')->get())}}-->
							@if ($all_options>0)
							<form class="form-horizontal" role="form" method="post" action="{{ route('user_payout')}}">
								{!! csrf_field() !!}
								<input type="hidden" name="user_id" value="{{$userProf->user_id}}">
								<div class="form-group">
									<label for="summa" class="col-xs-4 control-label">Сумма</label>
									<div class="col-xs-6">
										<input name="summa" id="summa_for_pay" type="number" min="300" max="{{$userProf->balance}}" step="0.01" value="{{floor($userProf->balance)}}" class="form-control" required>
										<span class="help-block" style="margin: 0;" id="text_for_user_pay">
												<strong>Минимальная сумма выплаты 300 руб.</strong>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label for="summa" class="col-xs-4 control-label">Платежная система</label>
									<div class="col-xs-6">
										<!-- {{$pay_option=\DB::table('payment_options')->orderBy('id', 'asc')->get()}} -->
										<select name="pay_option" class="form-control">
											@foreach ($pay_option as $option)
												@if (\DB::table('users_payment_options')->where('user_id', $userProf->user_id)->where('payment_id', $option->id)->first())
													@if (\DB::table('users_payment_options')->where('user_id', $userProf->user_id)->where('payment_id', $option->id)->first()->value)
														<option @if ($userProf->payment_option_id==$option->id) selected @endif value="{{$option->id}}">{{$option->name}} @if ($option->id==6) Комиссия 3% @endif</option>
													@endif
												@endif
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="summa" class="col-xs-4 control-label">Срочный вывод</label>
									<div class="col-xs-6">
										<input name="urgently" value="1" type="checkbox" id="user_urgently_pay" style="margin-top: 12px;">
										@if (!$userProf->vip and !$userProf->User->hasRole('admin') and !$userProf->User->hasRole('manager') and !$userProf->User->hasRole('super_manager'))
										<span class="help-block" style="margin: 0; color: rgb(181, 0, 0);">
												<strong>Комиссия 6%</strong>
										</span>
										@endif
									</div>
								</div>
								<div class="form-group">
									<div class="col-xs-offset-1 col-xs-10 text-center">
									  <button type="submit" class="btn btn-primary">Запросить</button>
									</div>
								</div>
							</form>
							@else
								<div class="row">
									<div class="col-xs-offset-1 col-xs-10">
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('manager') or \Auth::user()->hasRole('super_manager'))
											<p>Пожалуйста, заполните платежные реквизиты в <a href="{{route('admin.profile.personal', ['id_user'=>$userProf->user_id])}}">редакторе профиля</a></p>
										@else
											<p>Пожалуйста, заполните платежные реквизиты в <a href="{{route('profile.personal')}}">редакторе профиля</a></p>
										@endif
									</div>
								</div>
							@endif
						</div>
					</div>
					@if (\Auth::user()->hasRole('admin') and count($pays)>0)
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-12 personal_form">
								<table class="table table-condensed table-hover">
									<thead>
										<tr>
											<td>Дата</td>
											<td>Сумма</td>
											<td>Кто начислил(оштрафовал)</td>
										</tr>
									</thead>
									@foreach ($pays as $pay)
									<tr>
										<td>{{$pay->created_at}}</td>
										<td>{{$pay->commission}}</td>
										<!--{{$who=\App\User::where('id', $pay->who_add)->first()}}-->
										<td>@if ($who) {{$who->name}} @endif</td>
									</tr>
									@endforeach
								</table>
							</div>
						</div>
					</div>
					@endif
				</div>
		  </div>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<style>
		.cust-tabs .active a{
			font-weight: 600;
			color: #3b4371!important;
		}
		.cust-tabs li a{
			color: #3b4371;
			letter-spacing: 1.1px;
		}
		.personal_form{
			border: 1px solid #cacaca;
			background-image: url(/images/cabinet/background_block.png);
			background-color: rgba(199, 199, 199, 0.5);
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			margin-top: 20px;
		}
		.personal_form_right{
			border: 1px solid #cacaca;
			background-image: url(/images/cabinet/background_block.png);
			background-color: rgba(199, 199, 199, 0.5);
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			margin: 20px 15px 0 0px;
			float: right!important
		}
		
		.personal_form_right p{
			font-weight: bolder;
		}
		
		.personal_label{
			text-align: left!important;
		}
		.profile_avatar{
		width: 80px;
		height: 80px;
		border: 1px solid #8c8c8c;
		background: #f5f8fa;
		border-radius: 50%;
		}
		
		.radio_buttons {
			margin: 20px;
			font-size: 14px;
		}
		.radio_buttons div {
			float: left;
		}
		.radio_buttons input {
			position: absolute;
			left: -9999px;
		}
		.radio_buttons div:last-child label {
			border-top-right-radius: 4px;
			border-bottom-right-radius: 4px;
		}
		.radio_buttons div:first-child label {
			margin-left: 0;
			border-top-left-radius: 4px;
			border-bottom-left-radius: 4px;
		}
		.radio_buttons label {
			display: block;
			margin: 0 0 0 -1px;
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
		.person-hr{
		    border: 0;
			border-top: 1px solid #8c8c8c;
			border-top-style: dashed;
			margin-top: 3px;
		}
		.header-payment{
			font-weight: bold;
		}
		.header-payment span{
			color: rgb(181, 0, 0);
		}
		.right_header{
		color: rgba(228, 130, 43, 1);
		text-align: center;
		font-weight: bold;
		}
		.affiliate_all_pads_domain_gliph {
		font-size: 21px;
		top: -1px;
		margin: 0 5px;
		cursor: pointer;
		}
		.blue{
		color: #3097D1;
		}
		.red{
		color: #bf5329
		}
		.green{
		color: #20895e;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="{{ asset('js/bootstrap-formhelpers-phone.js') }}"></script>
		<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
@endpush