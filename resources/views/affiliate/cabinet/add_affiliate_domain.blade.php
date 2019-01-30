<div id="add_affiliate_domain" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Добавление площадки<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{ route('add.pads')}}">
				{!! csrf_field() !!}
				<input type="hidden" name="user_id" value="{{$userProf->user_id}}">
				<div class="form-group{{ $errors->has('domain') ? ' has-error' : '' }}">
					<label for="domain" class="col-xs-4 control-label">Доменное имя</label>
					<div class="col-xs-6">
						<input name="domain" type="text" value="{{ old('domain') }}" class="form-control" required>
						@if ($errors->has('domain'))
							<span class="help-block">
								<strong>{{ $errors->first('domain') }}</strong>
							</span>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="type" class="col-xs-4 control-label person-form">Тип желаемого виджета:</label>
					<div class="col-xs-6">
						<div class="col-xs-6 text-center">
						Товарный<br>
						<input type="checkbox" class="" name="type[]" value="1">
						</div>
						<div class="col-xs-6 text-center">
						Видео<br>
						<input type="checkbox" class="" name="type[]" value="2">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="stcurl" class="col-xs-4 control-label">Статистика, URL</label>
					<div class="col-xs-6">
						<input name="stcurl" type="text" value="{{ old('stcurl') }}" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="stclogin" class="col-xs-4 control-label">Логин для статистики</label>
					<div class="col-xs-6">
						<input name="stclogin" type="text" value="{{ old('stclogin') }}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="stcpassword" class="col-xs-4 control-label">Пароль для статистики</label>
					<div class="col-xs-6">
						<input name="stcpassword" type="text" value="{{ old('stcpassword') }}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
					  <button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>