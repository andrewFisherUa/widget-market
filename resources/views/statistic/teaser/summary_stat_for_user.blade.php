@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
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
								<select name="number" class="form-control">
									<option @if ($number==5) selected @endif value="5">5</option>
									<option @if ($number==10) selected @endif value="10">10</option>
									<option @if ($number==15) selected @endif value="15">15</option>
									<option @if ($number==20) selected @endif value="20">20</option>
									<option @if ($number==30) selected @endif value="30">30</option>
									<option @if ($number==50) selected @endif value="50">50</option>
									<option @if ($number==100) selected @endif value="100">100</option>
								</select>
							</div>
							<div class="col-xs-2 input-group form-group">
								<button type="submit" class="btn btn-primary">Применить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<h4 class="text-center">Статистика <a href="{{route('admin.home', ['user_id'=>$userP->id])}}" target="_blank">{{$userP->name}}</a> по тизерным виджетам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				@if ($userP->Profile->manager)
				Менеджер: <a href="{{route('admin.home', ['user_id'=>$userP->Profile->manager])}}" target="_blank">{{\App\User::find($userP->Profile->manager)->name}}</a>
				@endif
				<div class="col-xs-12">
					{!! $summaryStats->appends(["from"=>$from, "order"=>$order, "direct"=>$direct, 'number'=>$number, "to"=>$to, "manager"=>$manager])->render() !!}
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@foreach($header as $k=>$row)
												<td>
													@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
												</td>
											@endforeach
										</tr>
									</thead>
									<tbody>
										<tr style="background: #000; color: #fff;">
											<td>Всего</td>
											<td>{{$summaryStatsAll->views}}</td>
											<td>{{$summaryStatsAll->clicks}}</td>
											<td>{{$summaryStatsAll->ctr}}</td>
											<td>{{$summaryStatsAll->cpc}}</td>
											<td>{{$summaryStatsAll->summa}}</td>
										</tr>
										@foreach ($summaryStats as $summaryStat)
											<tr>
												<td>{{$summaryStat->day}}</td>
												<td>{{$summaryStat->views}}</td>
												<td>{{$summaryStat->clicks}}</td>
												<td>{{$summaryStat->ctr}}</td>
												<td>{{$summaryStat->cpc}}</td>
												<td>{{$summaryStat->summa}}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
					{!! $summaryStats->appends(["from"=>$from, "order"=>$order, "direct"=>$direct, 'number'=>$number, "to"=>$to, "manager"=>$manager])->render() !!}
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