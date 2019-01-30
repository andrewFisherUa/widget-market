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
		<div class="col-xs-12">
			<!--<div>Актуальный курс покупки: <span style="color: red; font-weight: bold">@if ($actual_buy['course']){{round($actual_buy['course'])}} </span> Количество Btc: {{$actual_buy['remainder']}} @endif</div>-->
			<div>Актуальный курс продажи: <span style="color: red; font-weight: bold">@if ($actual_sell['course']){{round($actual_sell['course'])}} </span> Количество Btc: {{$actual_sell['remainder']}} @endif</div>
		</div>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td rowspan="2" style="vertical-align: middle">Дата</td>
					@if (\Auth::user()->id==1)
						<td colspan="6">Покупка битков</td>
						<td colspan="6">Продажа битков</td>
					@else
						<td colspan="5">Покупка битков</td>
						<td colspan="5">Продажа битков</td>
					@endif
				</tr>
				<tr>
					<td>Рубли</td>
					<td>Битки</td>
					<td>Курс покупки</td>
					<td>Курс продажи</td>
					<td>Процент</td>
					@if (\Auth::user()->id==1)<td>Остаток</td>@endif
					
					<td>Рубли</td>
					<td>Битки</td>
					<td>Курс покупки</td>
					<td>Курс продажи</td>
					<td>Процент</td>
					@if (\Auth::user()->id==1)<td>Остаток</td>@endif
				</tr>
			</thead>
			<tbody>
				@foreach ($stats as $stat)
					<tr>
						<td>{{date('Y-m-d', strtotime($stat['day']))}}</td>
						
						<td>{{round($stat['amount_buy'])}}</td>
						<td>{{round($stat['amount_btc_buy'],6)}}</td>
						<td>{{round($stat['course_buy'])}}</td>
						<td>{{round($stat['return_course_buy'])}}</td>
						<td>{{round($stat['prosent_buy'],4)}}</td>
						@if (\Auth::user()->id==1)<td>{{$stat['remainder_buy']}}</td>@endif
						
						<td>{{round($stat['amount_sell'])}}</td>
						<td>{{round($stat['amount_btc_sell'],6)}}</td>
						<td>{{round($stat['return_course_sell'])}}</td>
						<td>{{round($stat['course_sell'])}}</td>
						<td>{{round($stat['prosent_sell'],4)}}</td>
						@if (\Auth::user()->id==1)<td>{{$stat['remainder_sell']}}</td>@endif
					</tr>
				@endforeach
			</tbody>
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

