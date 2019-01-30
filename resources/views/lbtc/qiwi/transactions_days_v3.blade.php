@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom: 20px;">
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
	<div class="row">
		<h3 class="text-center">Обмены по Qiwi</h3>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td rowspan="2" style="vertical-align: middle">Дата</td>
					<td colspan="2">Покупка битков</td>
					<td colspan="2">Продажа битков</td>
					<td rowspan="2" style="vertical-align: middle">Прибыль</td>
					<td rowspan="2" style="vertical-align: middle">Детально</td>
				</tr>
				<tr>
					<td>Рубли</td>
					<td>Битки</td>
					
					<td>Рубли</td>
					<td>Битки</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($all['amount_buy'])}}</td>
					<td>{{$all['amount_btc_buy']}}</td>
					<td>{{round($all['amount_sell'])}}</td>
					<td>{{$all['amount_btc_sell']}}</td>
					<td>{{$all['profit']}}</td>
					<td></td>
				</tr>
				@foreach ($stats as $stat)
					<tr>
						<td>{{date('Y-m-d', strtotime($stat['day']))}}</td>
						
						<td>{{round($stat['amount_buy'])}}</td>
						<td>{{round($stat['amount_btc_buy'],6)}}</td>
												
						<td>{{round($stat['amount_sell'])}}</td>
						<td>{{round($stat['amount_btc_sell'],6)}}</td>
						<td data-container="body" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" 
						title="
							
						@if ($stat['details'])
							@php
								$details= str_replace(',','',$stat['details']);
								$details= str_replace('{','',$details);
								$details= str_replace('}','',$details);
								$details = explode(';', $details)
							@endphp
							@foreach ($details as $detail)
								{{$detail}}<br>
							@endforeach
						@endif
						
						">{{$stat['profit']}}</td>
						<td><a href="{{route('lbtc.qiwi.robot.list.v3.detail', ['date'=>$stat['day']])}}" target="_blank" style="display: block">Подробнее</a></td>
					
					</tr>
				@endforeach
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($all['amount_buy'])}}</td>
					<td>{{$all['amount_btc_buy']}}</td>
					<td>{{round($all['amount_sell'])}}</td>
					<td>{{$all['amount_btc_sell']}}</td>
					<td>{{$all['profit']}}</td>
					<td></td>
				</tr>
			</tbody>
		</table>
    </div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')
	<script>
		$('[data-toggle="tooltip"]').tooltip({html:true
		});
	</script>
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

