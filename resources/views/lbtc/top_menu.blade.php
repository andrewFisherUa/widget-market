<div class="row" style="margin: 5px 0;">
	<a class="btn btn-primary btn-sm" href="{{route('lbtc.table')}}">На главную</a>
	<a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add_valut" data-backdrop="static">Добавить систему/счет</a>
</div>
<div id="add_valut" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="affiliate_modal_header">Добавление системы/счета<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{route('obmenneg.add.valut.post')}}">
				{!! csrf_field() !!}
				<div class="form-group">
					<label for="valut" class="col-xs-4 control-label">Название системы/счета</label>
					<div class="col-xs-6">
						<input name="valut" type="text" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="account_balance" class="col-xs-4 control-label">Остаток на счете</label>
					<div class="col-xs-6">
						<input name="account_balance" type="number"  step="0.0001" placeholder="Можно заполнить позже" class="form-control">
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

@push('cabinet_home_top')
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush