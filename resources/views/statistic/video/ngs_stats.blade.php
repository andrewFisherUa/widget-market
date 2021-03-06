@extends('layouts.app')

@section('content')
@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
	<!--{{$admin=1}}-->
@else
	<!--{{$admin=0}}-->
@endif
<div class="container">
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
		@if ($admin)
		<div class="row" style="margin-top: 10px;">
			<div class="col-xs-12">
				<form class="form-inline" role="form" method="post" action="{{route('video_statistic_pid.domain_stat_excel')}}">
					{!! csrf_field() !!}
					<div class="row">
						<input type="text" name="id" value="{{$id}}" hidden>
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">С:</span>
							<input type="text" class="form-control" value="{{$from}}" name="from">
						</div>
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">По:</span>
							<input type="text" class="form-control" value="{{$to}}" name="to">
						</div>
						<div class="col-xs-2 input-group form-group">
							<button type="submit" class="btn btn-success">Выгрузить excel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		@endif
			<div class="row">
				<h4 class="text-center">Cтатистика по доменам в период с {{$from}} по {{$to}}</h4>
				<div class="col-xs-12">
					{!! $statsAll->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
					<div>
						<ul class="nav nav-tabs nav-justified cust-tabs">
							<li class="heading text-left active"><a href="#summary_stat" data-toggle="tab">Общая статистика</a></li>
							<li class="heading text-left"><a href="#rus_stat" data-toggle="tab">Статистика по России</a></li>
							<li class="heading text-left"><a href="#cis_stat" data-toggle="tab">Статистика по СНГ</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="summary_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@if ($admin==1)
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
											@else
												@foreach($header as $k=>$row)
													@if ($k==0 or $k==1 or $k==3 or $k==10)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">@if ($k==3) Показы @else {{$row['title']}} @endif</a>@endif
													</td>
													@endif
												@endforeach
											@endif
										</tr>
									</thead>
									<tbody>
										@foreach ($statsAll as $stat)
											<tr>
												<td>{{$stat->host}}</td>
												<td>{{$stat->requested}}</td>
												@if ($admin==1)<td>{{$stat->played}}</td>@endif
												<td>{{$stat->calc_played}}</td>
												
												@if ($admin==1)
												<td>{{$stat->depth}}</td>
												<td>{{$stat->util}}</td>
												<td>{{$stat->completed}}</td>
												<td>{{$stat->clicks}}</td>
												<td>{{$stat->ctr}}</td>
												<td>{{$stat->bot}}</td>
												
												@endif
												<td>{{$stat->summa}}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							
							<div class="tab-pane" id="rus_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@if ($admin==1)
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
											@else
												@foreach($header as $k=>$row)
													@if ($k==0 or $k==1 or $k==3 or $k==10)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">@if ($k==3) Показы @else {{$row['title']}} @endif</a>@endif
													</td>
													@endif
												@endforeach
											@endif
										</tr>
									</thead>
									<tbody>
										@foreach ($statsRus as $stat)
											<tr>
												<td>{{$stat->host}}</td>
												<td>{{$stat->requested}}</td>
												@if ($admin==1)<td>{{$stat->played}}</td>@endif
												<td>{{$stat->calc_played}}</td>
												@if ($admin==1)
												<td>{{$stat->depth}}</td>
												<td>{{$stat->util}}</td>
												<td>{{$stat->completed}}</td>
												<td>{{$stat->clicks}}</td>
												<td>{{$stat->ctr}}</td>
												<td>{{$stat->bot}}</td>
												
												@endif
												<td>{{$stat->summa}}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							
							<div class="tab-pane" id="cis_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@if ($admin==1)
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
											@else
												@foreach($header as $k=>$row)
													@if ($k==0 or $k==1 or $k==3 or $k==10)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">@if ($k==3) Показы @else {{$row['title']}} @endif</a>@endif
													</td>
													@endif
												@endforeach
											@endif
										</tr>
									</thead>
									<tbody>
										@foreach ($statsCis as $stat)
											<tr>
												<td>{{$stat->host}}</td>
												<td>{{$stat->requested}}</td>
												@if ($admin==1)<td>{{$stat->played}}</td>@endif
												<td>{{$stat->calc_played}}</td>
												@if ($admin==1)
												<td>{{$stat->depth}}</td>
												<td>{{$stat->util}}</td>
												<td>{{$stat->completed}}</td>
												<td>{{$stat->clicks}}</td>
												<td>{{$stat->ctr}}</td>
												<td>{{$stat->bot}}</td>
												<td>{{$stat->summa}}</td>
												@endif
												<td>{{$stat->summa}}</td>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					{!! $statsAll->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
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