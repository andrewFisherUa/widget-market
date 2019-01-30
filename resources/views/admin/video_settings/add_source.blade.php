@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		@if (Session::has('message_warning'))
			<div class="alert alert-warning">
				{{ session('message_warning') }}
			</div>
		@endif
	@include('admin.video_settings.top_menu')
	</div>
	<div class="row">
		<h4 class="text-center">Добавление видео ссылки</h4>
			<form class="form-horizontal" role="form" method="post" action="{{route('video_setting.source.new')}}">
			{!! csrf_field() !!}
				<div class="form-group">
					<label for="title" class="col-xs-4 control-label">Название</label>
					<div class="col-xs-6">
						<input type="text" name="title" value="" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="src" class="col-xs-4 control-label">Ссылка</label>
					<div class="col-xs-6">
						<input type="text" name="src" value="" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="player" class="col-xs-4 control-label">Тип проигрывания</label>
					<div class="col-xs-6">
						<select name="player" class="form-control">
							<option value="0">Напрямую</option>
							@foreach($players as $pl)
								<option value="{{$pl['id']}}">{{$pl['title']}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="category" class="col-xs-4 control-label">Категория ссылки</label>
					<div class="col-xs-6">
						<select name="category" class="form-control">
							<option value='0'>Белая</option>
							<option value='1'>Адалт</option>
							<option value='2'>Развлекательная</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="cheap" class="col-xs-4 control-label">Отметить как дешевая</label>
					<div class="col-xs-6" style="margin-top: 8px;">
						<input type='checkbox' name='cheap' value='1'>
					</div>
				</div>
				<div class="form-group">
					<label for="partner_script" class="col-xs-4 control-label">Партнерский скрипт</label>
					<div class="col-xs-6">
						<textarea class="form-control" style="resize: none;" name="partner_script"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="limit" class="col-xs-4 control-label">Лимит показов</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="limit" value="0" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="timeout" class="col-xs-4 control-label">Таймаут показов</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="timeout" value="0" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="ftime" class="col-xs-4 control-label">Засчет показа (в секундах)</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="ftime" value="0" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="summa_rus" class="col-xs-4 control-label">Цена в России (за 1000 показов)</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="summa_rus" value="" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="summa_cis" class="col-xs-4 control-label">Цена в СНГ (за 1000 показов)</label>
					<div class="col-xs-6">
						<input type="number" min="0" name="summa_cis" value="" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="color" class="col-xs-4 control-label">Цвет ссылки (Для графика)</label>
					<div class="col-xs-6">
						<input type="text" name="color" class="form-control" id="source-color" data-format="rgb" value="">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6 col-xs-offset-3 text-center">
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link rel="stylesheet" type="text/css" href="{{ asset('minicolors/jquery.minicolors.css') }}" />
	<style>
		.blocks_to_source{
		    border: 1px solid #cacaca;
			background-image: url(/images/cabinet/background_block.png);
			background-color: rgba(199, 199, 199, 0.5);
			min-height:300px;
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
		}
		.blocks_to_source > .heading{
		    font-size: 11px;
			text-transform: uppercase;
			margin: 8px;
			padding: 0;
			height: 14px;
		}
		.blocks_to_source hr{
			border: 0;
			border-top: 1px solid #8c8c8c;
			border-top-style: dashed;
			margin-top: 3px;
			margin-bottom: 0;
		}
		.body_blocks{
			margin: 8px;
		}
		.body_blocks a{
			margin: 5px 0;
			color: rgba(38, 62, 197, 0.95);
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="{{ asset('minicolors/jquery.minicolors.min.js') }}"></script>
	<script>
		$(document).ready(function() {
			$('#source-color').minicolors({
		control: $(this).attr('data-control') || 'hue',
        defaultValue: $(this).attr('data-defaultValue') || '',
        format: $(this).attr('data-format') || 'rgb',
        keywords: $(this).attr('data-keywords') || '',
        inline: $(this).attr('data-inline') === 'true',
        letterCase: $(this).attr('data-letterCase') || 'lowercase',
        position: $(this).attr('data-position') || 'bottom left',
        swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
        change: function(value, opacity) {
			if( !value ) return;
			if( opacity ) value += ', ' + opacity;
			if( typeof console === 'object' ) {
				console.log(value);
            }
          },
		theme: 'bootstrap'
		  });
		});
	</script>
@endpush