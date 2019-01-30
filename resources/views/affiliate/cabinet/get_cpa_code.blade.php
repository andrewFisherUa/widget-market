<div id="get_code_{{$d->id}}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="affiliate_modal_header">Получить код <button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
				<textarea id="maincode{{$d->id}}" style="display: block; width: 80%; margin: 10px auto; height: 400px; padding: 6px 12px; font-size: 14px;
				line-height: 1.6; color: #555555; background-color: #fff; background-image: none; border: 1px solid #ccd0d2; border-radius: 4px; 
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); -webkit-transition: border-color ease-in-out 0.15s,
				box-shadow ease-in-out 0.15s; -webkit-transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s; transition: border-color ease-in-out 0.15s, 
				-webkit-box-shadow ease-in-out 0.15s; transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s; 
				transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s; resize: none" readonly>{!! \App\Videosource\DiscUtil::Cpa($d->id,$d->cpa_code) !!}</textarea>
			<div class="text-center" style="margin: 10px 0"></div>
		</div>	
	</div>
</div>