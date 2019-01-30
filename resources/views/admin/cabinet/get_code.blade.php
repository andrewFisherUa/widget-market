<div id="get_code_{{$widget->id}}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="affiliate_modal_header">Получить код 2 <button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<textarea id="maincode{{$widget->id}}" style="display: block; width: 80%; margin: 10px auto; height: 200px; padding: 6px 12px; font-size: 14px;
				line-height: 1.6; color: #555555; background-color: #fff; background-image: none; border: 1px solid #ccd0d2; border-radius: 4px; 
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); -webkit-transition: border-color ease-in-out 0.15s,
				box-shadow ease-in-out 0.15s; -webkit-transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s; transition: border-color ease-in-out 0.15s, 
				-webkit-box-shadow ease-in-out 0.15s; transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s; 
				transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s; resize: none" readonly>@if ($widget->type==1 or $widget->type==3)<div class="mpwidget" data-id="{{$widget->id}}"></div><script type="text/javascript"  src="//node.market-place.su/div/api.js?v=2"></script>@elseif ($widget->type==2) @if($widget->video['type']==1){!! \App\Videosource\DiscUtil::VideoAutoplay($widget->video['id']) !!}@elseif($widget->video['type']==2){!! \App\Videosource\DiscUtil::VideoOverplay($widget->video['id']) !!} @elseif ($widget->video['type']==3){!! \App\Videosource\DiscUtil::VideoVast($widget->video['id']) !!} @elseif ($widget->video['type']==4){!! \App\Videosource\DiscUtil::VideoInpage($widget->video['id']) !!} @elseif ($widget->video['type']==5){!! \App\Videosource\DiscUtil::VideoAutoplayMuted($widget->video['id']) !!} @endif @elseif ($widget->type==4) {!! \App\Videosource\DiscUtil::Brand($widget->id) !!} @endif
			</textarea>
			<div class="text-center" style="margin: 10px 0">
				<a class="btn btn-success copy-all" data-clipboard-target="#maincode{{$widget->id}}" style="cursor:pointer; top: 3px; font-size: 18px;">
					Скопировать код
				</a>
			</div>
		</div>
	</div>
</div>