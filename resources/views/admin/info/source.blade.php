@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		@if (Session::has('message_danger'))
			<div class="alert alert-danger">
				{{ session('message_danger') }}
			</div>
		@endif
		<div class="col-xs-12">
			<h4 class="text-center">Инфо по вычетам</h4>
			<form class="form-inline" role="from" method="post">
				{!! csrf_field() !!}
				<input type="text" value="zaq111" name="key" hidden>
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="input-group col-xs-3 form-group">
					<span class="input-group-addon">Название:</span>
					<input type="text" class="form-control" value="{{$name_src}}" name="name_src">
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
			</form>
			<table class="table table-hover table-bordered" style="margin-top: 10px">
				<thead>
					<tr>
						<td>Дата</td>
						<td>Название</td>
						<td>Разница</td>
						<td>Срезано</td>
						<td>Комментарий</td>
					</tr>
				</thead>
				<tbody>
					@foreach ($all as $al)
						<tr style="background: #000; color: #fff">
							<td>Всего</td>
							<td>{{$al->name_src}}</td>
							<td>{{$al->discrepancy}}</td>
							<td>{{$al->cut}}</td>
							<td>Наш плюс по показам: {{$al->profit}}</td>
						</tr>
					@endforeach
						@if (($from>'2017-10-24' and $from<'2017-12-10') or ($to>'2017-10-24' and $to<'2017-12-10'))
							<tr style="background: #000; color: #fff">
								<td>Всего</td>
								<td>Медиавейс 1.4</td>
								<td>227827</td>
								<td>13669,62 руб.</td>
								<td>В период с 2017-10-24 по 2017-12-10 нам недочи 227827 показов, пропорционально были срезаны средства с юзеров на сумму 13669,62 руб., позже медиавейс признали свою непровоту и согласились выплатить по нашей стате, наш чистый плюс 13669,62 руб.</td>
							</tr>
						@endif
					@foreach ($stats as $stat)
						<tr>
							<td>{{$stat->day}}</td>
							<td>{{$stat->name_src}}</td>
							<td>{{$stat->discrepancy}}</td>
							<td>{{$stat->cut}}</td>
							<td>{{$stat->comment}}</td>
						</tr>
					@endforeach
				</tbody>
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