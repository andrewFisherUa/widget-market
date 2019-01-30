{{--<div id="get_code_{{$pwidget->id}}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="affiliate_modal_header">Получить код<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<textarea id="maincode{{$pwidget->id}}" style="display: block; width: 80%; margin: 10px auto; height: 200px; padding: 6px 12px; font-size: 14px;
				line-height: 1.6; color: #555555; background-color: #fff; background-image: none; border: 1px solid #ccd0d2; border-radius: 4px; 
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); -webkit-transition: border-color ease-in-out 0.15s,
				box-shadow ease-in-out 0.15s; -webkit-transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s; transition: border-color ease-in-out 0.15s, 
				-webkit-box-shadow ease-in-out 0.15s; transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s; 
				transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s; resize: none" readonly>				@if ($pwidget->type==1)<div class="mpwidget" data-id="{{$pwidget->id}}"></div><script type="text/javascript"  src="//node.market-place.su/div/api.js?v=2"></script>@elseif ($pwidget->type==2) @if($pwidget->video['type']==1){!! \App\Videosource\DiscUtil::VideoAutoplay($pwidget->video['id']) !!}@elseif($pwidget->video['type']==2){!! \App\Videosource\DiscUtil::VideoOverplay($pwidget->video['id']) !!} @elseif ($pwidget->video['type']==3){!! \App\Videosource\DiscUtil::VideoVast($pwidget->video['id']) !!} @elseif ($pwidget->video['type']==4){!! \App\Videosource\DiscUtil::VideoInpage($pwidget->video['id']) !!} @elseif ($pwidget->video['type']==5){!! \App\Videosource\DiscUtil::VideoAutoplayMuted($pwidget->video['id']) !!} @endif @elseif ($pwidget->type==4) {!! \App\Videosource\DiscUtil::Brand($pwidget->id) !!} @endif
			</textarea>
			<div class="text-center" style="margin: 10px 0">
				<a class="btn btn-success copy-all" data-clipboard-target="#maincode{{$pwidget->id}}" style="cursor:pointer; top: 3px; font-size: 18px;">
					Скопировать код
				</a>
			</div>
		</div>
	</div>
</div>--}}