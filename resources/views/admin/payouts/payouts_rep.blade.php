@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
		@endif
		@if (Session::has('message_danger'))
			<div class="alert alert-danger">
				{{ session('message_danger') }}
			</div>
		@endif
		<div class="col-xs-12">
			<form id="payout_for_wmr" class="form-inline" role="form" action="" method="get">
				<div class="input-group col-xs-4 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-4 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-success">Применить</button>
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<table class="table table-hover table-bordered" style="margin-top: 10px; table-layout: fixed">
			<thead>
				<tr>
					<td>Кошелек</td>
					<td>Сумма</td>
				</tr>
			</thead>
			<tbody>
				@foreach($payouts as $payout)
					<tr>
						<td>
						@foreach ($pay_options as $pay_option)
							@if ($payout->pay_option==$pay_option->id)
								{{$pay_option->name}}
							@endif
						@endforeach
						</td>
						<td>{{$payout->summa}}</td>
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