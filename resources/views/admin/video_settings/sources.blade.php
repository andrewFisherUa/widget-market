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
		<h4 class="text-center">Все видео ссылки</h4>
		<form class="form-inline" role="form" method="get" action=" {{ route('video_setting.sources') }}">
			<div class="row">
				<div class="col-xs-3 form-group">
					<label for="title" class="col-xs-4 control-label">Поиск</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" value="{{$title}}" name="title">
					</div>
				</div>
				<div class="col-xs-4 form-group">
					<label for="player" class="col-xs-5 control-label" style="margin-top: -5px!important;">Тип проигрывания</label>
					<div class="col-xs-7">
						<select name='player' class="form-control">
							<option @if ($player=='all') selected @endif value="all">Все</option>
							<option @if ($player=='line') selected @endif value="line">Прямые</option>
							@foreach ($players as $play)
								<option @if ($player==$play['id']) selected @endif value="{{$play['id']}}">{{$play['title']}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-xs-3 form-group">
					<label for="category" class="col-xs-4 control-label">Категория</label>
					<div class="col-xs-8">
						<select name='category' class="form-control">
							<option @if ($category=='all') selected @endif value="all">Все</option>
							<option @if ($category=='white') selected @endif value="white">Белые</option>
							<option @if ($category=='adult') selected @endif value="adult">Адалт</option>
							<option @if ($category=='razv') selected @endif value="razv">Развлекательные</option>
						</select>
					</div>
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
			</div>
		</form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<td>Название</td>
					<td>Блоки</td>
					<td>Дешевая</td>
					<td>Цвет</td>
					<td colspan="2">Действие</td>
				</tr>
			</thead>
			@foreach ($sources as $source)
			<tr>
					<td>{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</td>
					<td>
					@if($source->toBlocks)
						@foreach($source->toBlocks as $block)
							{{$block->name}}, &nbsp;
						@endforeach
					@endif
					</td>
					<td>@if ($source->cheap) Да @endif</td>
					<td style="vertical-align: middle"><div style="display: block; width: 26px; height: 26px; margin: 0 auto; border-radius: 5px; background: {{$source->color}}"></div></td>
					<td class="text-center"><a class="btn btn-primary" href="{{ route('video_setting.source.edit', ['id'=>$source->id]) }}">Редактировать</a></td>
					<td class="text-center"><a class="btn btn-danger" href="{{ route('video_setting.source.delete', ['id'=>$source->id]) }}">Удалить</a></td>
			</tr>
			@endforeach
		</table>
	</div>
</div>
@endsection
@push('cabinet_home')
	<style>
		.control-label{
			margin-top: 7px!important;
		}
		.form-inline{
		margin-bottom: 20px;
		}
	</style>
@endpush