<div class="panel-group" id="notif_accordion">
	@if (count($user_notif)>0)
		@foreach ($user_notif as $notif)
			@if (count($user->unreadNotifications->where('type', '<>', 'App\Notifications\NewNews')->where('data', $notif->id)))
				<div class="panel panel-default" id="notif_{{$notif->id}}">
					<div class="news_block_all news_block_green">
						<div class="all-news">
							<span class="glyphicon glyphicon-info-sign news-gliph-all color-blue"></span>
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$notif->id}}" class="news-all-header">{{str_limit($notif->header,15)}}</a>
								<a class="remove_notif" data-set="{{$notif->id}}"><span class="glyphicon glyphicon-remove"></span></a>
							</div>
					</div>
					<div id="collapse{{$notif->id}}" class="panel-collapse collapse">
						<div class="panel-body">
						{{$notif->header}}
						<hr class="affilaite_hr">
						{{$notif->body}}
						</div>
					</div>
				</div>
			@endif
		@endforeach
	@else
		<div class="no_manager text-center" >У Вас пока что нет новых уведомлений.</div>
	@endif
</div>