<div id="edit_affiliate_domain_{{$pad->id}}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Редактирование площадки {{$pad->domain}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{ route('edit.pad_affiliate')}}">
				{!! csrf_field() !!}
				<input type="hidden" name="pad_id" value="{{$pad->id}}">
				<div class="form-group">
					<label for="domain" class="col-xs-4 control-label">Доменное имя</label>
					<div class="col-xs-6">
						<input name="domain" type="text" value="{{$pad->domain}}" class="form-control" readonly>
					</div>
				</div>
				<div class="form-group">
					<label for="stcurl" class="col-xs-4 control-label">Статистика, URL</label>
					<div class="col-xs-6">
						<input name="stcurl" type="text" value="{{$pad->stcurl}}" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="stclogin" class="col-xs-4 control-label">Логин для статистики</label>
					<div class="col-xs-6">
						<input name="stclogin" type="text" value="{{$pad->stclogin}}" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="stcpassword" class="col-xs-4 control-label">Пароль для статистики</label>
					<div class="col-xs-6">
						<input name="stcpassword" type="text" value="{{$pad->stcpassword}}" class="form-control">
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