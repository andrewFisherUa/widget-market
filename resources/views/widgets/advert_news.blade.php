<div class="col-xs-3">

<div class="affiliate_cabinet_block">
<div class="heading text-left">Последние новости  

</div> 
<hr class="affilaite_hr" style="margin-bottom: 10px;"> 
<div id="home_news" class="home_block">
<div id="cabinet_news" class="custom-scroll_container" style="padding-left: 0px; padding-right: 0px;">
<div class="custom-scroll_inner" style="padding-left: 0px; padding-right: 0px; margin-right: -17px; overflow-x: hidden;">
						
			@foreach($news as $new)								
						
			<div class="col-xs-12 col-xs-12" style="margin-bottom: 15px;">
				<a href="/news/{{$new->id}}" class="new">
			<div class="news_block_all 
			 @if($new->important==1)
             news_block_green
             @elseif($new->important==2) news_block_orange_no_read
			 @else news_block_red_no_read
             @endif">
				<div class="all-news">
											<span class="glyphicon glyphicon-info-sign news-gliph-all color-blue"></span>
											<span class="news-all-header">{{ str_limit($new->header, $limit = 15, $end = '...') }}</span>
				</div>
			</div>
			</a>
		</div>
		@endforeach
			</div><div class="custom-scroll_bar-y" style="height: 116px; top: 32px;"></div></div></div></div></div></div>
