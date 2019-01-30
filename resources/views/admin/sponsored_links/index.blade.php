@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		<a class="btn btn-success" data-toggle="modal" data-target="#add_src">Добавить</a>
		<table class="table table-hover table-bordered" style="margin-top: 10px">
			<thead>
				<tr>
					<td>Дата создания</td>
					<td>Название</td>
					<td>Ссылка</td>
					<td>Кол-во заходов</td>
					<td>Кол-во уникальных заходов</td>
					<td>Кол-во регистрация</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($links as $link)
					<!--{{$uniq=""}}-->
					<tr>
						<td>{{$link->created_at}}</td>
						<td>{{$link->title}}</td>
						<td>{{$link->src}}</td>
						<td>
							@foreach ($visits as $visit)
								@if ($visit->affiliate==$link->affiliate)
									{{$visit->cnt}}
									<!--{{$uniq=$visit->uniq}}-->
								@endif
							@endforeach
						</td>
						<td>{{$uniq}}</td>
						<td>
						@foreach ($regis as $reg)
							@if ($reg->affiliate==$link->affiliate)
								<span style="display: block; cursor: pointer" data-container="body" data-toggle="popover" data-html="true" tabindex="0" data-placement="bottom" data-content="
									@if ($reg->cnt>0)
									<!--{{$ggs=\DB::table('sponsored_links_regis')->leftJoin('users', 'sponsored_links_regis.user_id', '=', 'users.id')->where('affiliate', $link->affiliate)->get()}}-->
									@foreach ($ggs as $gg)
										{{$gg->user_id}} - {{$gg->name}}<br>
									@endforeach
								@endif
								"
								>{{$reg->cnt}}</span>
								
							@endif
						@endforeach
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>


<div id="add_src" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Добавление ссылки<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{ route('s_link.add')}}">
				{!! csrf_field() !!}
				<div class="form-group">
					<label for="title" class="col-xs-4 control-label">Название</label>
					<div class="col-xs-6">
						<input name="title" type="text" value="" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-ffset-1 col-xs-10 text-center">
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')

@endpush