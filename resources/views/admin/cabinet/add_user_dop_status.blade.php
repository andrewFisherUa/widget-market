<div id="add_user_dop_status_{{$userActive->user_id}}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Назначение дополнительного статуса<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{ route('admin.user_for_dop_status', ['id_user'=>$userActive->user_id])}}">
				{!! csrf_field() !!}
				<input type="hidden" name="user_id" value="{{$userActive->user_id}}">
				<div class="form-group">
					<label for="dop_status" class="col-xs-6 control-label">Отметить как "Хороший клиент" <img src="/images/smail/green.png" style="width: 15px; height: 15px; top: 0px; cursor: pointer;"></label>
					<div class="col-xs-6">
						<input name="dop_status" @if ($userActive->dop_status==1) checked @endif value="1" type="radio" style="margin-top: 12px">
					</div>
				</div>
				<div class="form-group">
					<label for="dop_status" class="col-xs-6 control-label">Отметить как "Средний клиент" <img src="/images/smail/yellow.png" style="width: 15px; height: 15px; top: 0px; cursor: pointer;"></label>
					<div class="col-xs-6">
						<input name="dop_status" @if ($userActive->dop_status==2) checked @endif value="2" type="radio"  style="margin-top: 12px">
					</div>
				</div>
				<div class="form-group">
					<label for="dop_status" class="col-xs-6 control-label">Отметить как "Плохой клиент" <img src="/images/smail/red.png" style="width: 15px; height: 15px; top: 0px; cursor: pointer;"></label>
					<div class="col-xs-6">
						<input name="dop_status" @if ($userActive->dop_status==3) checked @endif value="3" type="radio"  style="margin-top: 12px">
					</div>
				</div>
				<div class="form-group">
					<label for="dop_status" class="col-xs-6 control-label">Без отметки</label>
					<div class="col-xs-6">
						<input name="dop_status" @if (!$userActive->dop_status) checked @endif value="0" type="radio"  style="margin-top: 12px">
					</div>
				</div>
				<div class="form-group">
					<label for="text_for_dop_status" class="col-xs-4 control-label">Сопроводительный коментарий</label>
					<div class="col-xs-6">
						<textarea name="text_for_dop_status" class="form-control" style="resize: none;">
							{{$userActive->text_for_dop_status}}
						</textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-ffset-1 col-xs-10 text-center">
					  <button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>