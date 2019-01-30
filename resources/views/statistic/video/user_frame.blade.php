@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<form class="form-inline" role="form" method="get">
				<div class="row">
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">С:</span>
						<input type="text" class="form-control" value="{{$from}}" name="from">
					</div>
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">По:</span>
						<input type="text" class="form-control" value="{{$to}}" name="to">
					</div>
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">Количетсво:</span>
						<input type="text" class="form-control" value="{{$number}}" name="number">
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row" style="margin: 10px 0;">
		<h3 class="text-center">Статистика по фреймам клиента {{$user->name}}</h3>
		{!! $stats->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
		<table class="table table-hover table-bordered">
			<tr>
				@foreach($header as $k=>$row)
					<td>
						@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
					</td>
				@endforeach
			</tr>
			<tbody>
				<tr style="background: #000; color: #fff;">
					<td>Всего</td>
					<td></td>
					<td>{{$sum}}</td>
					<td></td>
					<td></td>
				</tr>
				@foreach ($stats as $stat)
				<tr>
					<td>{{$stat->pid}}</td>
					<td>{{$stat->url}}</td>
					<td>{{$stat->cnt}}</td>
					<td>{{$stat->coef}}</td>
					<td><a target="_blank" href="{{route('video_statistic.frame_stat_user_detail', ['id'=>$stat->pid, 'url'=>$stat->url])}}">Дательно</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		{!! $stats->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
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
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
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