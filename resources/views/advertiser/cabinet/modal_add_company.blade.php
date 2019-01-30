<div id="modal_add_company"  class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Пополнение баланса<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
				<!--advertiser.first.step.payout-->
				<form class="form-horizontal" role="form" method="post" action="{{route('advertiser.first_add_company')}}">
					{!! csrf_field() !!}
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
						@if ($id_user)
							<input type="hidden" name="id_user" value="{{$id_user}}">
						@else
							<input type="hidden" name="id_user" value="{{\Auth::user()->id}}">
						@endif 
					@else
						<input type="hidden" name="id_user" value="{{\Auth::user()->id}}">
					@endif
					<div class="form-group">
						<label for="summa" class="col-xs-4 control-label">Выберите тип компании</label>
						<div class="col-xs-6">
							<select name="type_company" class="form-control">
								<option value="1">Товарная компания</option>
								@if (\Auth::user()->hasRole(['admin','super_manager','manager','advertiser']))
								<option value="2">Тизерная компания</option>
								@endif
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 text-center">
							<button class="btn btn-primary">Продолжить</button>
						</div>
					</div>
				</form>
		</div>
	</div>
</div>