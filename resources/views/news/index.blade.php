@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if (Session::has('message_war')))
		<div class="alert alert-warning">
			{{ session('message_war') }}
		</div>
	@endif
		<div class="col-xs-3">
			<div class="news_block">
				@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('manager') or Auth::user()->hasRole('super_manager'))
					<div class="heading text-right"><a href="/add_news" class="news_sbr">Добавить новость</a></div>
				@endif
				<div class="heading text-right">Фильтры</div>
				<hr class="news_hr">
				<form role="form" method="get" action="{{ route('news.all')}}" class="form-horizontal">
					<div class="form-group">
						<label for="important" class="col-xs-10 control-label">К ознакомлению <span class="glyphicon glyphicon-exclamation-sign news-gliph color-green"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="important" value="1" type="radio" @if($imp==1) checked @endif class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="important" class="col-xs-10 control-label">На заметку <span class="glyphicon glyphicon-exclamation-sign news-gliph color-orange"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="important" value="2" type="radio" @if($imp==2) checked @endif class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="important" class="col-xs-10 control-label">Обязательно к прочтению <span class="glyphicon glyphicon-exclamation-sign news-gliph color-red"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="important" value="3" type="radio" @if($imp==3) checked @endif class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-xs-10 control-label">Общие новости <span class="glyphicon glyphicon-info-sign news-gliph color-blue"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type[]" value="1" type="checkbox"
							@foreach ($type as $t)
								@if ($t=='1') checked @endif
							@endforeach
							class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-xs-10 control-label">Новинки <span class="glyphicon glyphicon-bell news-gliph color-green"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type[]" value="2" type="checkbox"
							@foreach ($type as $t)
								@if ($t==2) checked @endif
							@endforeach
							class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-xs-10 control-label">Важно <span class="glyphicon glyphicon-fire news-gliph color-red"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type[]" value="3" type="checkbox"
							@foreach ($type as $t)
								@if ($t==3) checked @endif
							@endforeach
							class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-xs-10 control-label">Акции <span class="glyphicon glyphicon-usd news-gliph color-purple"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type[]" value="4" type="checkbox" 
							@foreach ($type as $t)
								@if ($t==4) checked @endif
							@endforeach
							class="controls">
						</div>
					</div>
					@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('manager') or Auth::user()->hasRole('super_manager'))
					<div class="form-group">
						<label for="role" class="col-xs-10 control-label">Вебмастерам</label>
						<div class="col-xs-2 custom-radio">
							<input name="role[]" value="1" type="checkbox"
							@foreach ($role as $r)
								@if ($r==1) checked @endif
							@endforeach
							class="controls">
						</div>
					</div>
					<div class="form-group">
						<label for="role" class="col-xs-10 control-label">Рекламодателям</label>
						<div class="col-xs-2 custom-radio">
							<input name="role[]" value="2" type="checkbox"
							@foreach ($role as $r)
								@if ($r==2) checked @endif
							@endforeach
							class="controls">
						</div>
					</div>
					@endif
					<div class="form-group">
						<div class="col-xs-12 text-center">
							<button type="submit" class="btn btn-primary">
							Применить
							</button>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 text-center">
							<a href="/news" class="news_sbr">Сбросить всё</a>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 text-center">
							<a href="{{ route('news.read_all') }}" class="news_sbr">Отметить все как прочитанные</a>
						</div>
					</div>
					
				</form>
			</div>
		</div>
		<div class="col-xs-9">
		<!-- {{$notifs=\Auth::user()->unreadNotifications->where('type', 'App\Notifications\NewNews')}} -->
			@foreach ($news as $n)
				<div class="col-xs-12" style="margin-bottom: 15px;">
					<a href="{{url ('news/'.$n->id)}}" class="new"><div class="news_block_all 
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
							{{ str_limit($n->anoun, 40) }}
							<div class="news-date">
							{{date('d-m-Y', strtotime($n->created_at))}}
							</div>
						</div>
					</div></a>
					@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('manager') or Auth::user()->hasRole('super_manager'))
					<a href="{{ route ('news.edit',[$id=$n->id])}}" class="news-edit" data-toggle="tooltip" data-placement="bottom" title="Редактировать"><span class="glyphicon glyphicon-pencil news-gliph-all color-blue"></span></a>
					<a href="{{ route ('news.delete', [$id=$n->id])}}" class="news-delete" data-toggle="tooltip" data-placement="bottom" title="Удалить"><span class="glyphicon glyphicon-trash news-gliph-all color-red"></span></a>
					@endif
				</div>
			@endforeach
			<div style="text-align: center">
			{!! $news->appends(["important"=>$imp, "type"=>$type, "role"=>$role])->render() !!}
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