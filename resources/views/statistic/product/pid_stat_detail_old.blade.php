@extends('layouts.app')

@section('content')
@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
	<!--{{$admin=1}}-->
@else
	<!--{{$admin=0}}-->
@endif
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-xs-12">
		<div class="row">
				<div class="col-xs-8">
					<form class="form-inline" role="form" method="get">
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
				<div class="col-xs-4 text-right">
					<div> 
						<span class="">Здесь параметр по которому выделять строку: </span><span class="right_pok">%</span>
					</div>
					<div>
						<span class="label label-danger celi_pok"></span><span class="right_pok">Ниже %</span>
					</div>
				</div>
			</div>
			<div class="row">
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<a href="{{route('admin.home', ['user_id'=>$user->id])}}" style="font-weight: bold">{{$user->name}}</a>
				@endif
				<h4 class="text-center">Статистика по виджету № {{$widget->id}} {{$widget->partnerPad->domain}}</h4>
				<div class="col-xs-12">
					<div>
						{!! $stats->appends(["from"=>$from, "to"=>$to])->render() !!}
						<table class="table table-hover table-bordered" style="margin-top: 10px">
							<thead>
								<tr>
									<td>Дата</td>
									<td>Показы</td>
									<td>Клики</td>
									@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
									<td>Наши клики</td>
									<td>Яндекс клики</td>
									<td>Топадверт клики</td>
									@endif
									<td>Ctr</td>
									<td>Cpc</td>
									<td>Сумма</td>
								</tr>
							</thead>
							<tbody>
								<tr style="background: #000; color: #fff;">
									<td>Всего</td>
									<td>{{$statsAll->views}}</td>
									<td>{{$statsAll->clicks}}</td>
									@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
									<td>{{$statsAll->our_clicks}}</td>
									<td></td>
									<td></td>
									@endif
									<td>{{$statsAll->ctr}}</td>
									<td>{{$statsAll->cpc}}</td>
									<td>{{$statsAll->summa}}</td>
								</tr>
								@foreach ($stats as $stat)
									<tr>
										<td>{{$stat->day}}</td>
										<td>{{$stat->views}}</td>
										<td>{{$stat->clicks}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$stat->our_clicks}}</td>
										<td>{{$stat->yandex_clicks}}</td>
										<td>{{$stat->ta_clicks}}</td>
										@endif
										<td>{{$stat->ctr}}</td>
										<td>{{$stat->cpc}}</td>
										<td>{{$stat->summa}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! $stats->appends(["from"=>$from, "to"=>$to])->render() !!}
					</div>
				</div>
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