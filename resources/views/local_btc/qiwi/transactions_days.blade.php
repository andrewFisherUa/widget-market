@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
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
					<td colspan="4">Покупка битков</td>
					<td colspan="4">Продажа битков</td>
					<td colspan="3" style="vertical-align: middle">Прибыль</td>
					<td rowspan="2" style="vertical-align: middle">Детально</td>
				</tr>
				<tr>
					<td>Рубли</td>
					<td>Битки</td>
					<td>Средневзвешенный курс</td>
					<td>Средний курс</td>
					
					<td>Рубли</td>
					<td>Битки</td>
					<td>Средневзвешенный курс</td>
					<td>Средний курс</td>
					
					<td>Покупка</td>
					<td>Продажа</td>
					<td>Общая</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($all['amount_buy'])}}</td>
					<td>{{$all['amount_btc_buy']}}</td>
					<td>
						@php
							$pdo = \DB::connection("obmenneg")->getPdo();
							$sql="select * from local_balancing where id_ad='609849'";
							$srs=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
							$course_all_buy=0;
							foreach ($srs as $sr){
								$remainder=$sr['amount_btc']*100/$all['amount_btc_buy'];
								$cor=$remainder*$sr['course_fact']/100;
								$course_all_buy += $cor;
							}
						@endphp
						{{round($course_all_buy)}}
					</td>
					<td>{{round($all['course_buy'])}}</td>
					
					<td>{{round($all['amount_sell'])}}</td>
					<td>{{$all['amount_btc_sell']}}</td>
					<td>
						@php
							$pdo = \DB::connection("obmenneg")->getPdo();
							$sql="select * from local_balancing where id_ad='617372'";
							$srs=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
							$course_all_sell=0;
							foreach ($srs as $sr){
								$remainder=$sr['amount_btc']*100/$all['amount_btc_sell'];
								$cor=$remainder*$sr['course_fact']/100;
								$course_all_sell += $cor;
							}
						@endphp
						{{round($course_all_sell)}}
					</td>
					<td>{{round($all['course_sell'])}}</td>
					<td>{{$all['profit_buy']}}</td>
					<td>{{$all['profit_sell']}}</td>
					<td>{{$all['profit']}}</td>
					<td></td>
				</tr>
				@foreach ($stats as $stat)
					<tr>
						<td>{{date('Y-m-d', strtotime($stat['day']))}}</td>
						
						<td>{{round($stat['amount_buy'])}}</td>
						<td>{{round($stat['amount_btc_buy'],6)}}</td>
						<td>
						@php
							$pdo = \DB::connection("obmenneg")->getPdo();
							$sql="select * from local_balancing where id_ad='609849' and date_trunc('day', created)='" . $stat['day'] . "'";
							$srs=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
							$course=0;
							$details_buy='';
							$course_null=0;
							foreach ($srs as $sr){
								$remainder=$sr['amount_btc']*100/$stat['amount_btc_buy'];
								$cor=$remainder*$sr['course_fact']/100;
								$course += $cor;
								if ($sr['return_course']){
									$details_buy= $details_buy . '' . round($sr['amount_btc'],6) . ' - ' . $sr['return_course'] . ';';
								}
								else{
									$course_null+=$sr['amount_btc'];
								}
							}
							if ($course_null>0){
								$details_buy= $details_buy . '' . round($course_null,6) . ' - без продаж;';
							}
						@endphp
						{{round($course)}}
						</td>
						<td>{{round($stat['course_buy'])}}</td>
						<td>{{round($stat['amount_sell'])}}</td>
						<td>{{round($stat['amount_btc_sell'],6)}}</td>
						<td>
						@php
							$pdo = \DB::connection("obmenneg")->getPdo();
							$sql="select * from local_balancing where id_ad='617372' and date_trunc('day', created)='" . $stat['day'] . "'";
							$srs=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchAll(\PDO::FETCH_ASSOC);
							$course=0;
							$course_null=0;
							$remainder_sell=$stat['amount_btc_sell'];
							$details_sell='';
							foreach ($srs as $sr){
								$remainder=$sr['amount_btc']*100/$stat['amount_btc_sell'];
								$cor=$remainder*$sr['course_fact']/100;
								$course += $cor;
								if ($sr['return_course']){
									$details_sell= $details_sell . '' . round($sr['amount_btc'],6) . ' - ' . $sr['return_course'] . ';';
								}
								else{
									$course_null+=$sr['amount_btc'];
								}
							}
							if ($course_null>0){
								$details_sell= $details_sell . '' . round($course_null,6) . ' - без закупок;';
							}
						@endphp
						{{round($course)}}
						</td>
						<td>{{round($stat['course_sell'])}}</td>
						<td @if ($details_buy) data-container="body" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" 
						title="
							@php
								$details_buy = explode(';', $details_buy);
								$i=1;
							@endphp
							@foreach ($details_buy as $detail)
								{{$detail}}
								@if ($i!=count($details_buy))
									<br>
								@endif
								<!--{{$i++}}-->
							@endforeach
						" @endif style="cursor: pointer">{{$stat['profit_buy']}}</td>
						<td @if ($details_sell) data-container="body" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" 
						title="
							@php
								$details_sell = explode(';', $details_sell);
								$i=1;
							@endphp
							@foreach ($details_sell as $detail)
								{{$detail}}
								@if ($i!=count($details_sell))
									<br>
								@endif
								<!--{{$i++}}-->
							@endforeach
						
						" @endif style="cursor: pointer">{{$stat['profit_sell']}}</td>
						<td>{{$stat['profit']}}</td>
						<td><a href="{{route('lbtc.qiwi.robot.list.v3.detail', ['date'=>$stat['day']])}}" target="_blank" style="display: block">Подробнее</a></td>
					</tr>
				@endforeach
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($all['amount_buy'])}}</td>
					<td>{{$all['amount_btc_buy']}}</td>
					<td>{{round($course_all_buy)}}</td>
					<td>{{round($all['course_buy'])}}</td>
					<td>{{round($all['amount_sell'])}}</td>
					<td>{{$all['amount_btc_sell']}}</td>
					<td>{{round($course_all_sell)}}</td>
					<td>{{round($all['course_sell'])}}</td>
					<td>{{$all['profit_buy']}}</td>
					<td>{{$all['profit_sell']}}</td>
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

