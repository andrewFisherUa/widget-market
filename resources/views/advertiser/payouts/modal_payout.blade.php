<div id="advertiser_payout"  class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Пополнение баланса<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
				<!--advertiser.first.step.payout-->
				<form class="form-horizontal" role="form" method="post" action="{{route('advertiser.payout')}}">
					{!! csrf_field() !!}
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
						@if ($id_user)
							<input type="hidden" name="user_id" value="{{$id_user}}">
						@else
							<input type="hidden" name="user_id" value="{{\Auth::user()->id}}">
						@endif
					@else
						<input type="hidden" name="user_id" value="{{\Auth::user()->id}}">
					@endif
					<div class="form-group">
						<label for="summa" class="col-xs-4 control-label">Выберите способ оплаты</label>
						<div class="col-xs-6">
							<select name="payout_sistem" id="payout_sistem" class="form-control">
								<!--<option value="0">Не выбрано</option>
								<option value="1">Счет в банке (Юр. лицо)</option>-->
                                <option value="1">Счет в банке (Юр. лицо)</option>
								<option value="2">WebMoney Rub</option>
                                
							</select>
						</div>
					</div>
					<div class="form-group" id="iwmid">
						<label for="summa" class="col-xs-4 control-label">Wmid</label>
						<div class="col-xs-6">
							<input type="text" name="wmid"  class="form-control">
						</div>
					</div>
					<div class="form-group" id="iinvoicetype">
						<label for="summa" class="col-xs-4 control-label">Тип</label>
						<div class="col-xs-6">
							<select name="invoicetype"  class="form-control">
							 <option value="1">Обычный</option>
							 <option value="2">Авансовый</option>
							 
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="summa" class="col-xs-4 control-label">Сумма</label>
						<div class="col-xs-6">
							<input type="number" step="0.01" name="summa" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 text-center" id="submit_payout">
							<button class="btn btn-primary">Оплатить</button>
						</div>
					</div>
				</form>
		</div>
	</div>
</div>
@push('cabinet_home_js')
<script>
function checkpaymentForm(){
	if($('#payout_sistem').val()==1){
		$('#iwmid').hide();
		$('#iinvoicetype').show();
		
	}else{
		$('#iinvoicetype').hide();
		$('#iwmid').show();
	    
	}

}
$(document).ready(function(){
checkpaymentForm();
$('#payout_sistem').change(function(){
    checkpaymentForm();	
    });
});
</script>
@endpush
