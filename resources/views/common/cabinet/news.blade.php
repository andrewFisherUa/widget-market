<div id="cabinet_news">
	@if (count($news_lim)>0)
		@foreach ($news_lim as $n)
			<div class="col-xs-12 col-xs-12" style="margin-bottom: 15px;">
				<a href="{{url ('news/'.$n->id)}}" class="new">
			<div class="news_block_all 
				@if ($n->important==1) 
					@if(count($user->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
						news_block_green_no_read
					@else
						news_block_green
					@endif
				 @elseif ($n->important==2) 
					@if(count($user->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
						news_block_orange_no_read
					@else
						news_block_orange
					@endif
				 @elseif($n->important==3) 
					@if(count($user->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
						news_block_red_no_read
					@else
						news_block_red
					@endif
				 @endif">
				<div class="all-news">
					@if ($n->type==1)
						<span class="glyphicon glyphicon-info-sign news-gliph-all color-blue"></span>
					@elseif ($n->type==2)
						<span class="glyphicon glyphicon-bell news-gliph-all color-green"></span>
					@elseif ($n->type==3)
						<span class="glyphicon glyphicon-fire news-gliph-all color-red"></span>
					@elseif ($n->type==4)
						<span class="glyphicon glyphicon-usd news-gliph-all color-purple"></span>
					@endif
						<span class="news-all-header">{{str_limit($n->header,15)}}</span>
				</div>
			</div>
			</a>
		</div>
		@endforeach
	@else
		<div class="no_manager text-center">Извините, в данный момент для Вас нет новостей.</div>
	@endif
</div>