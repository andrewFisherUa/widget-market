@extends('layouts.app')

@section('content')
@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
	<!--{{$admin=1}}-->
@else
	<!--{{$admin=0}}-->
@endif
<div class="container">
	<div class="row">
		<a href="{{route('video_statistic.frame')}}">Все записи</a><br>
		<a href="{{route('video_statistic.frame.user')}}">Разбитые по виджетам</a><br>
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
				<h4 class="text-center">Детализация по жулики</h4>
				<div class="col-xs-12">
					<div>
						<table class="table table-hover table-bordered" style="margin-top: 10px; table-layout: fixed;">
							<thead>
								@foreach($header as $k=>$row)
									<td>
										@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
									</td>
								@endforeach
							</thead>
							<tbody>
								@foreach ($stats as $stat)
									<tr>
										<td>{{$stat->id}}</td>
										<td>{{$stat->pid}}</td>
										<td>{{$stat->day}}</td>
										<td style="word-wrap: break-word;">{{$stat->url}}</td>
										<td style="word-wrap: break-word;">{{$stat->refer}}</td>
										<td>{{$stat->cnt}}</td>
										<td style="word-wrap: break-word;">
										@if ($stat->origin=='0')
											@if ($stat->refer!=1)
												&#8594;{{parse_url($stat->refer, PHP_URL_HOST)}}<br>
												&#8594;{{parse_url($stat->url, PHP_URL_HOST)}}
											@else
												Не удалось определить
											@endif
										@else
											<!--{{preg_match_all('#"http([^"]+)"#i', $stat->origin, $matches)}}-->
											@php $elem=array("s://", "://") @endphp
											@foreach ($matches[1] as $mat)
												@php $q=str_replace($elem, "", $mat) @endphp
												&#8594;{{$q}}<br>
											@endforeach
											&#8594;{{parse_url($stat->url, PHP_URL_HOST)}}
										@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! $stats->appends(['from'=>$from, 'to'=>$to, 'order'=>$order, 'direct'=>$direct])->render() !!}
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