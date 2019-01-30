@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center"></h4>
		<div class="col-xs-12">
			<h4 class="text-center">Общая сумма</h4>
			<form class="form-inline" role="form" method="get" action="">
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" id="from_for_users" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" id="to_for_users" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
			</form>
			<div>
				{!! $sums->render() !!}
				<table class="table table-hover table-bordered" style="margin-top: 10px">
					<thead>
						<tr>
							<td>Дата</td>
							<td>Видео</td>
							<td>Товарка</td>
							<td>Тизерка</td>
							<td>Рефералы</td>
							<td>Менеджеры</td>
							<td>Всего</td>
						</tr>
					</thead>
					<tbody>
						<tr style="background: #000; color: #fff">
							<td>Всего</td>
							<td>{{$all_sum->video_commission}}</td>
							<td>{{$all_sum->product_commission}}</td>
							<td>{{$all_sum->teaser_commission}}</td>
							<td>{{$all_sum->referal_commission}}</td>
							<td>{{$all_sum->manager_commission}}</td>
							<td>{{$all_sum->video_commission+$all_sum->product_commission+$all_sum->teaser_commission+$all_sum->referal_commission+$all_sum->manager_commission}}</td>
						</tr>
						@foreach ($sums as $sum)
							<tr>
								<td>{{$sum->day}}</td>
								<td>{{$sum->video_commission}}</td>
								<td>{{$sum->product_commission}}</td>
								<td>{{$sum->teaser_commission}}</td>
								<td>{{$sum->referal_commission}}</td>
								<td>{{$sum->manager_commission}}</td>
								<td>{{$sum->video_commission+$sum->product_commission+$sum->teaser_commission+$sum->referal_commission+$sum->manager_commission}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{!! $sums->render() !!}
			</div>
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
		.detail_com:focus, detail_com:active{
			outline: none!important;
		}
		.popover{
			width: 400px!important;
			max-width: 400px!important;
		}
		#app{
			margin-bottom: 220px!important;
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