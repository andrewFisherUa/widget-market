@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<div class="col-xs-3">
			<div class="news_block" style="display: inline-block">
				<div class="heading text-right"><a href="/news" class="news_sbr">Вернуться к списку новостей</a></div>
				@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('manager') or Auth::user()->hasRole('super_manager'))
					<div class="heading text-right"><a href="/add_news" class="news_sbr">Добавить новость</a></div>
				@endif
				<hr class="news_hr" style="margin-bottom: 15px;">
				@foreach ($news_lim as $n)
					<div class="col-xs-12 col-xs-12" style="margin-bottom: 15px;">
						<a href="{{url ('news/'.$n->id)}}" class="new">
						<div class="news_block_all 
						@if ($n->important==1) 
							@if(count(\Auth::user()->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
								news_block_green_no_read
							@else
								news_block_green
							@endif
						 @elseif ($n->important==2) 
							@if(count(\Auth::user()->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
								news_block_orange_no_read
							@else
								news_block_orange
							@endif
						 @elseif($n->important==3) 
							@if(count(\Auth::user()->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
								news_block_red_no_read
							@else
								news_block_red
							@endif
						 @endif">
							<div class="all-news">
								@if ($n->type==1)
									<span data-toggle="tooltip" data-placement="bottom" title="Общие новости" class="glyphicon glyphicon-info-sign news-gliph-all color-blue"></span>
								@elseif ($n->type==2)
									<span data-toggle="tooltip" data-placement="bottom" title="Новинки" class="glyphicon glyphicon-bell news-gliph-all color-green"></span>
								@elseif ($n->type==3)
									<span data-toggle="tooltip" data-placement="bottom" title="Важно" class="glyphicon glyphicon-fire news-gliph-all color-red"></span>
								@elseif ($n->type==4)
									<span data-toggle="tooltip" data-placement="bottom" title="Акции" class="glyphicon glyphicon-usd news-gliph-all color-purple"></span>
								@endif
								<span class="news-all-header">{{str_limit($n->header,15)}}</span>
							</div>
						</div>
					</a>
				</div>
				@endforeach
			</div>
		</div>
		<div class="col-xs-9">
			<div class="news_block_one">
				<div class="
				@if ($news->important==1)
				news_block_green_no_read
				@elseif($news->important==2)
				news_block_orange_no_read
				@elseif($news->important==3)
				news_block_red_no_read
				@endif news_one_header
				">
				@if ($news->type==1)
					<span data-toggle="tooltip" data-placement="bottom" title="Общие новости" class="glyphicon glyphicon-info-sign news-gliph-one-news color-blue"></span>
				@elseif ($news->type==2)
					<span data-toggle="tooltip" data-placement="bottom" title="Новинки" class="glyphicon glyphicon-bell news-gliph-one-news color-green"></span>
				@elseif ($news->type==3)
					<span data-toggle="tooltip" data-placement="bottom" title="Важно" class="glyphicon glyphicon-fire news-gliph-one-news color-red"></span>
				@elseif ($news->type==4)
					<span data-toggle="tooltip" data-placement="bottom" title="Акции" class="glyphicon glyphicon-usd news-gliph-one-news color-purple"></span>
				@endif
				{{$news->header}}</div>
				@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('manager') or Auth::user()->hasRole('super_manager'))
					<a href="{{ route ('news.edit',[$id=$news->id])}}" class="news-edit" data-toggle="tooltip" data-placement="bottom" title="Редактировать"><span class="glyphicon glyphicon-pencil news-gliph-all color-blue"></span></a>
					<a href="{{ route ('news.delete', [$id=$news->id])}}" class="news-delete" data-toggle="tooltip" data-placement="bottom" title="Удалить"><span class="glyphicon glyphicon-trash news-gliph-all color-red"></span></a>
				@endif
				<div class="one_news_body">{!!$news->body!!}
				</div>
			</div>
		</div>
	</div>
</div>
@push ('news')
<link href="{{ asset('css/news.css') }}" rel="stylesheet">
@endpush
@push ('newsjs')
<script>
	$(function(){
		$('[data-toggle="tooltip"]').tooltip();
	});
</script>
@endpush
@endsection