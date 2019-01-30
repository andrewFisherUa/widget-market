@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<form class="form-horizontal" role="form" method="post" action="{{ route('news.update',[$id=$news->id])}}">
		{!! csrf_field() !!}
			<div class="col-xs-4">
				<div class="news_block">
					<div class="heading text-right">Для кого новость</div>
					<hr class="news_hr">
					<div class="form-group">
						<label for="role" class="col-xs-10 control-label">Вебмастерам</label>
						<div class="col-xs-2 custom-radio">
							<input name="role" value="1" class="controls" @if ($news->role==1) checked @endif type="radio" checked>
						</div>
					</div>
					<div class="form-group">
						<label for="role" class="col-xs-10 control-label">Рекламодателям</label>
						<div class="col-xs-2 custom-radio">
							<input name="role" value="2" class="controls" @if ($news->role==2) checked @endif type="radio">
						</div>
					</div>
					<div class="heading text-right">Приоритет</div>
					<hr class="news_hr">
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">К ознакомлению <span class="glyphicon glyphicon-exclamation-sign news-gliph color-green"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="important" value="1" class="controls" @if ($news->important==1) checked @endif type="radio" checked>
						</div>
					</div>
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">На заметку <span class="glyphicon glyphicon-exclamation-sign news-gliph color-orange"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="important" value="2" class="controls" @if ($news->important==2) checked @endif type="radio">
						</div>
					</div>
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">Обязательно к прочтению <span class="glyphicon glyphicon-exclamation-sign news-gliph color-red"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="important" value="3" class="controls" @if ($news->important==3) checked @endif type="radio">
						</div>
					</div>
					<div class="heading text-right">Тип новости</div>
					<hr class="news_hr">
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">Общие новости <span class="glyphicon glyphicon-info-sign news-gliph color-blue"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type" value="1" class="controls" type="radio" @if ($news->type==1) checked @endif checked>
						</div>
					</div>
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">Новинки <span class="glyphicon glyphicon-bell news-gliph color-green"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type" value="2" class="controls" @if ($news->type==2) checked @endif type="radio">
						</div>
					</div>
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">Важно <span class="glyphicon glyphicon-fire news-gliph color-red"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type" value="3" class="controls" @if ($news->type==3) checked @endif type="radio">
						</div>
					</div>
					<div class="form-group">
						<label for="header" class="col-xs-10 control-label">Акции <span class="glyphicon glyphicon-usd news-gliph color-purple"></span></label>
						<div class="col-xs-2 custom-radio">
							<input name="type" value="4" class="controls" @if ($news->type==4) checked @endif type="radio">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12 text-center">
						<button type="submit" class="btn btn-primary">
							Сохранить
						</button>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-8">
				<div class="form-group">
					<label for="header" class="col-xs-12 text-center">Заголовок новости</label>
					<div class="col-xs-12">
						<input id="osn_header" name="header" type="text" class="form-control" value="{{$news->header}}" required>
					</div>
				</div>
				<div class="form-group">
					<label for="anoun" class="col-xs-12 text-center">Анонс</label>
					<div class="col-xs-12">
						<textarea name="anoun" class="form-control" maxlength="255" style="resize: none;" required>{{$news->anoun}}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="body" class="col-xs-12 text-center">Текст новости</label>
					<div class="col-xs-12">
						<textarea name="body" type="text" id="text_body" class="form-control" required>{!! $news->body !!}</textarea>
					</div>
				</div>
			</div>
		</form>
	</div>
	<h3 class="text-center">Как будет выглядеть новость</h3>
	<div class="row" style="margin-bottom: 20px;">
		<div class="col-xs-12">
		<ul class="nav nav-tabs" role="tablist" id="myTabs">
			<li id="desctop-block" role="presentation" class="active"><a href="#sait" aria-controls="sait" role="tab" data-toggle="tab">На сайте</a></li>
			<li id="mobile-block" role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">В email</a></li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="sait" style="padding: 20px;">
				<div class="col-xs-9 col-xs-9">
					<div class="news_block_one">
						<div id="site_header" class="
						@if ($news->important==1)
						news_block_green_no_read
						@elseif($news->important==2)
						news_block_orange_no_read
						@elseif($news->important==3)
						news_block_red_no_read
						@endif news_one_header
						">
						@if ($news->type==1)
							<span id="gliph_one_n" class="glyphicon glyphicon-info-sign news-gliph-one-news color-blue"></span>
						@elseif ($news->type==2)
							<span id="gliph_one_n" class="glyphicon glyphicon-bell news-gliph-one-news color-green"></span>
						@elseif ($news->type==3)
							<span id="gliph_one_n" class="glyphicon glyphicon-fire news-gliph-one-news color-red"></span>
						@elseif ($news->type==4)
							<span id="gliph_one_n" class="glyphicon glyphicon-usd news-gliph-one-news color-purple"></span>
						@endif
						<span id="site_text_header" class="text-center">{{$news->header}}</span>
						</div>
						<div id="site_body" class="one_news_body">{!!$news->body!!}
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="email">НА емайле</div>
		  </div>
		</div>
		</div>
</div>
@push ('news')
<link href="{{ asset('css/news.css') }}" rel="stylesheet">
@endpush
@push ('ckeditor')
<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>
<script>
	$(document).ready(function() {
    var editor = CKEDITOR.replace( 'text_body' );
	editor.on( 'change', function( evt ) {
		$('#site_body').html(evt.editor.getData());
	});
	$('#osn_header').change(function(){
			$('#site_text_header').html($('#osn_header').val())
		});
	});
	$('input[name=important]').change(function(){
		$('#site_header').removeClass();
		if ($('input[name=important]:checked').val()=='1'){
			$('#site_header').addClass('news_block_green_no_read news_one_header');
		}
		else if ($('input[name=important]:checked').val()=='2'){
			$('#site_header').addClass('news_block_orange_no_read news_one_header');
		}
		else if ($('input[name=important]:checked').val()=='3'){
			$('#site_header').addClass('news_block_red_no_read news_one_header');
		}
	});
	$('input[name=type]').change(function(){
		$('#gliph_one_n').removeClass();
		if ($('input[name=type]:checked').val()=='1'){
			$('#gliph_one_n').addClass('glyphicon glyphicon-info-sign news-gliph-one-news color-blue');
		}
		else if ($('input[name=type]:checked').val()=='2'){
			$('#gliph_one_n').addClass('glyphicon glyphicon-bell news-gliph-one-news color-green');
		}
		else if ($('input[name=type]:checked').val()=='3'){
			$('#gliph_one_n').addClass('glyphicon glyphicon-fire news-gliph-one-news color-red');
		}
		else if ($('input[name=type]:checked').val()=='4'){
			$('#gliph_one_n').addClass('glyphicon glyphicon-usd news-gliph-one-news color-purple');
		}
	});
	
                
</script>
@endpush
@push ('newsjs')
<script>
	
	
</script>
@endpush
@endsection