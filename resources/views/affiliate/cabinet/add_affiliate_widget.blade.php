<div id="add_affiliate_widget" class="modal fade user_add_widget">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Создание виджета<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{ route('widget.create')}}">
				{!! csrf_field() !!}
				<input type="hidden" name="user_id" value="{{$userProf->user_id}}">
				<div class="form-group">
					<label for="pad" class="col-xs-4 control-label">Выберите площадку</label>
					<div class="col-xs-6">
					<select name="pad" class="form-control pad_for_widget">
						<option data-type="0">Выберите площадку</option>
						@foreach ($partnerPads as $ppad)
							@if ($ppad->status==1)
								<option value="{{$ppad->id}}" data-type="{{$ppad->type}}">{{$ppad->domain}}</option>
							@endif
						@endforeach
					</select>
					</div>
				</div>
				<div class="form-group">
					<div class="type_for_widget" style="display: none">
						<label for="pad" class="col-xs-4 control-label">Выберите тип виджета</label>
						<div class="col-xs-6"> 
							<select name="type" class="form-control pod_type_for_widget">
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="type_for_video" style="display: none">
						<label for="type_for_video" class="col-xs-4 control-label">Выберите тип видео</label>
						<div class="col-xs-6">
							<select name="typeVideo" class="form-control type_for_video_select">
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center save_for_widget">

					</div>
				</div>
			</form>
		</div>
	</div>
</div>