@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
			<div class="col-xs-12">
				<form class="form-inline" role="form" method="get">
					<div class="row">
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">дата 1:</span>
							<input type="text" class="form-control" value="{{$date1}}" name="date1">
						</div>
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">дата 2:</span>
							<input type="text" class="form-control" value="{{$date2}}" name="date2">
						</div>
						<div class="input-group col-xs-2 form-group">
							<input type="text" class="form-control" value="{{$hour}}" name="hour">
						</div>
						<div class="col-xs-2 input-group form-group">
							<button type="submit" class="btn btn-primary">Применить</button>
						</div>
					</div>
					
				</form>
			</div>
		</div>
	<div class="row">
		<div class="col-xs-12">
			<table class="table table-hover table-bordered" style="margin-top: 10px">
				<thead>
					<tr>
						<td>Id виджета</td>
						<td>Домен</td>
						<td>Имя</td>
						<td>дата 1</td>
						<td>дата 2</td>
						<td>Разница даты 2 - дата 1</td>
					</tr>
				</thead>
				@foreach ($stats as $stat)
					<tr>
						<td><a href="{{route('video_statistic_pid.pid_statistic', ['id'=>$stat->id])}}" target="_blank">{{$stat->id}}</a></td>
						<td>{{$stat->domain}}</td>
						<td><a href="{{route('admin.home', ['user_id'=>$stat->user_id])}}" target="_blank">{{$stat->name}}</a></td>
						<td>{{$stat->request_1}}</td>
						<td>{{$stat->request_2}}</td>
						<td>
							<span style="font-weight: bold; @if ($stat->razn>0)	color: green; @elseif ($stat->razn<0) color: red; @endif">{{$stat->razn}}</span>
						</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	<style>
		.table{
			text-align: center;
		}
		.table > thead > tr > th, .table > thead > tr > td, .table > tbody > tr > th, .table > tbody > tr > td, .table > tfoot > tr > th, .table > tfoot > tr > td{
			vertical-align: middle;
			border: 1px solid #ababab;
		}
		.body_sum{
			font-weight: bolder;
		}
		.celi_pok{
			display: inline-block!important;
			width: 10px;
			height: 10px;
		}
		.rur{
		font-style: normal;
		}
		.right_pok{
		display: inline-block;
		width: 200px;
		}
		.table_href{
		color: inherit;
		}
		.cust-tabs .active a{
			font-weight: 600;
			color: #3b4371!important;
		}
		.cust-tabs li a{
			color: #3b4371;
			letter-spacing: 1.1px;
		}
	</style>
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
    $('input[name="date1"]').daterangepicker({
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
	$('input[name="date2"]').daterangepicker({
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