@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
	<div class="row">
		<div class="col-xs-5">
			<h3 class="text-center">Покупка Btc за Yandex {{date('Y-m-d', strtotime($date))}}</h3>
			<table class="table table-hover table-bordered text-center">
				<thead>
					<tr>
						<td>Время</td>
						<td>Рубли</td>
						<td>Битки</td>
						<td>Курс</td>
						<td>Прибыль</td>
					</tr>
				</thead>
				<tbody>
					<tr style="background: #000; color: #fff;">
						<td>Всего</td>
						<td>{{$buy_stats_all['amount']}}</td>
						<td>{{$buy_stats_all['amount_btc']}}</td>
						<td>{{round($buy_stats_all['course'])}}</td>
						<td>{{$buy_stats_all['profit']}}</td>
					</tr>
					@foreach ($buy_stats as $buy_stat)
						<tr>
							<td>{{$buy_stat['created']}}</td>
							<td>{{$buy_stat['amount']}}</td>
							<td>{{$buy_stat['amount_btc']}}</td>
							<td>{{round($buy_stat['course_fact'])}}</td>
							<td data-container="body" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" 
							title="
							@if ($buy_stat['details'])
								@php
									$details = explode(';', $buy_stat['details']);
									$i=1;
								@endphp
								@foreach ($details as $detail)
									{{$detail}}
									@if ($i!=count($details))
										<br>
									@endif
									<!--{{$i++}}-->
								@endforeach
							@endif
							" style="cursor: pointer">@if ($buy_stat['profit']) {{$buy_stat['profit']}} @else неизвестно @endif</td>
						</tr>
					@endforeach
					<tr style="background: #000; color: #fff;">
						<td>Всего</td>
						<td>{{$buy_stats_all['amount']}}</td>
						<td>{{$buy_stats_all['amount_btc']}}</td>
						<td>{{round($buy_stats_all['course'])}}</td>
						<td>{{$buy_stats_all['profit']}}</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="col-xs-7">
			<h3 class="text-center">Продажа Btc за Yandex {{date('Y-m-d', strtotime($date))}}</h3>
			<table class="table table-hover table-bordered text-center">
				<thead>
					<tr>
						<td>Время</td>
						<td>Рубли</td>
						<td>Битки</td>
						<td>Курс</td>
						<td>Средневзвешенный курс закупки</td>
						<td>Прибыль</td>
					</tr>
				</thead>
				<tbody>
					<tr style="background: #000; color: #fff;">
						<td>Всего</td>
						<td>{{$sell_stats_all['amount']}}</td>
						<td>{{$sell_stats_all['amount_btc']}}</td>
						<td>{{round($sell_stats_all['course'])}}</td>
						<td>{{round($sell_stats_all['return_course'])}}</td>
						<td>{{$sell_stats_all['profit']}}</td>
					</tr>
					@foreach ($sell_stats as $sell_stat)
						<tr>
							<td>{{$sell_stat['created']}}</td>
							<td>{{$sell_stat['amount']}}</td>
							<td>{{$sell_stat['amount_btc']}}</td>
							<td>{{round($sell_stat['course_fact'])}}</td>
							<td>{{round($sell_stat['return_course'])}}</td>
							<td data-container="body" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" 
						title="
						@if ($sell_stat['details'])
							@php
								$details = explode(';', $sell_stat['details']);
								$i=1;
							@endphp
							@foreach ($details as $detail)
								{{$detail}}
								@if ($i!=count($details))
									<br>
								@endif
								<!--{{$i++}}-->
							@endforeach
						@endif
						" style="cursor: pointer">@if ($sell_stat['profit']) {{$sell_stat['profit']}} @else неизвестно @endif</td>
						</tr>
					@endforeach
					<tr style="background: #000; color: #fff;">
						<td>Всего</td>
						<td>{{$sell_stats_all['amount']}}</td>
						<td>{{$sell_stats_all['amount_btc']}}</td>
						<td>{{round($sell_stats_all['course'])}}</td>
						<td>{{round($sell_stats_all['return_course'])}}</td>
						<td>{{$sell_stats_all['profit']}}</td>
					</tr>
				</tbody>
			</table>
		</div>
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

