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
		<div class="col-xs-12">
		<h4 class="text-center">Все видео блоки</h4>
		<form class="form-inline" role="form" method="get" action="">
			<div class="row">
				<div class="input-group col-xs-3 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-3 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
			</div>
		</form>
		</div>
	</div>
	<div class="row">
		<table class="table table-hover table-bordered" style="margin-top: 10px;">
			<thead>
				<tr class="text-center">
				@foreach($header as $k=>$row)
					<td>
						@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
					</td>
				@endforeach
					<td colspan="2">Действие</td>
				</tr>
			</thead>
			@foreach ($blocks as $block)
				<tr>
					<td>{{$block->name}}</td>
					<td class="text-center">{{$block->requested}}</td>
					<td class="text-center">{{$block->calculated}}</td>
					<td class="text-center"><a href="{{ route('video_setting.block.edit', ['id'=>$block->id]) }}" class="btn btn-primary">Редактировать</a></td>
					<td class="text-center"><a href="" class="btn btn-danger">Удалить</a></td>
				</tr>
			@endforeach
		</table>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
	
	<script>
		
$(function() {
    $('input[name="from"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
	$('input[name="to"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
});

	</script>
@endpush