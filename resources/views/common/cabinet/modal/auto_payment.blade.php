<div id="auto_payment" data-set="{{$user->profile->vip}}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Настройка автозаказа выплат<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<!--{{ $all_options = count(\DB::table('users_payment_options')->where('user_id', $user->id)->whereNotNull('value')->get())}}-->
			@if ($all_options>0)
			<form class="form-horizontal" role="form" >
				{!! csrf_field() !!}
				<input type="hidden" name="user_id" value="{{$user->id}}">
				<div class="form-group">
					<label for="auto_pay" class="col-xs-4 control-label">Включить автозаказ</label>
					<div class="col-xs-6">
						<input type="checkbox" name="auto_pay" value="1" @if ($user->profile->auto_payment) checked @endif style="margin-top: 12px;">
					</div>
				</div>
				<!-- {{$autoPay=\App\Payments\UserPaymentAuto::where('user_id', $user->id)->first()}} -->
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Выберите день недели</label>
					<div class="col-xs-6">
						<select name="day" class="form-control">
							<option @if ($autoPay) @if ($autoPay->day==1) selected @endif @endif value="1">Понедельник</option>
							<option @if ($autoPay) @if ($autoPay->day==2) selected @endif @endif value="2">Вторник</option>
							<option @if ($autoPay) @if ($autoPay->day==3) selected @endif @endif value="3">Среда</option>
							<option @if ($autoPay) @if ($autoPay->day==4) selected @endif @endif value="4">Четверг</option>
							<option @if ($autoPay) @if ($autoPay->day==5) selected @endif @endif value="5">Пятница</option>
							<option @if ($autoPay) @if ($autoPay->day==6) selected @endif @endif value="6">Суббота</option>
							<option @if ($autoPay) @if ($autoPay->day==0) selected @endif @endif value="0">Воскресенье</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Срочный вывод</label>
					<div class="col-xs-6">
						<input name="urgently" @if ($autoPay) @if ($autoPay->urgently) checked @endif @endif value="1" type="checkbox" style="margin-top: 12px;">
						@if (!$user->profile->vip  and !$user->hasRole('admin') and !$user->hasRole('manager') and !$user->hasRole('super_manager'))
						<span class="help-block" style="margin: 0; color: rgb(181, 0, 0);">
								<strong>С включенным срочным выводом комиссия 6%</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Платежная система</label>
					<div class="col-xs-6">
						<!-- {{$pay_option=\DB::table('payment_options')->orderBy('id', 'asc')->get()}} -->
						<select name="pay_option" class="form-control">
							@foreach ($pay_option as $option)
								@if ($option->id!=2)
								@if (\DB::table('users_payment_options')->where('user_id', $user->id)->where('payment_id', $option->id)->first())
									@if (\DB::table('users_payment_options')->where('user_id', $user->id)->where('payment_id', $option->id)->first()->value)
										<option  @if ($autoPay) @if ($autoPay->payment_id==$option->id) selected @endif @endif value="{{$option->id}}">{{$option->name}} @if ($option->id==6) Комиссия 1% @endif</option>
									@endif
								@endif
								@endif
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
					  <a id="auto_payment_submit" class="btn btn-primary">Сохранить</a>
					</div>
				</div>
			</form>
			@else
				<div class="row">
					<div class="col-xs-offset-1 col-xs-10">
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('manager') or \Auth::user()->hasRole('super_manager'))
							<p>Пожалуйста, заполните платежные реквизиты в <a href="{{route('admin.profile.personal', ['id_user'=>$user->id])}}">редакторе профиля</a></p>
						@else
							<p>Пожалуйста, заполните платежные реквизиты в <a href="{{route('profile.personal')}}">редакторе профиля</a></p>
						@endif
					</div>
				</div>
			@endif
		</div>
	</div>
</div>