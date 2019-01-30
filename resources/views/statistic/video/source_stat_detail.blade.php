@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
		<div class="row">
				<div class="col-xs-8">
					<form class="form-inline" role="form" method="get" action=" {{ route('video_statistic.video_source_stat_detail', ['id'=>$id]) }}">
						<div class="row">
							<div class="input-group col-xs-4 form-group">
								<span class="input-group-addon">С:</span>
								<input type="text" class="form-control" value="{{$from}}" name="from">
							</div>
							<div class="input-group col-xs-4 form-group">
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
						<span class="">Порог потерь: </span><span class="right_pok">30%</span>
					</div>
					<div>
						<span class="label label-danger celi_pok"></span><span class="right_pok">Ниже 30%</span>
					</div>
				</div>
			</div>
			<div class="row">
				<h4 class="text-center">Статистика ссылки №{{$st->id_src}} "{{$st->title}}" в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				<table class="table table-hover table-bordered">
					<tr>
						<td rowspan="2"><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
						<td colspan="7">Россия</td>
						<td colspan="7">СНГ</td>
						<td colspan="7">Всего</td>
					</tr>
					<tr>
						@foreach($header as $k=>$row)
							@if ($k!=0)
								<td>
									@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
								</td>
							@endif
						@endforeach
					</tr>
					@foreach ($stats as $stat)
						<tr @if (($stat->poteri) > 30) class="danger body_sum" @endif>
							<td>{{$stat->day}}</td>
							<!-- Россия -->
							<td>{{$stat->requested_ru}}</td>
							<td>{{$stat->played_ru}}</td>
							<td>{{$stat->clicked_ru}}</td>
							<td>{{$stat->poteri_ru}}</td>
							<td>{{$stat->dosm_ru}}</td>
							<td>{{$stat->util_ru}}</td>
							<td>{{$stat->ctr_ru}}</td>
							<!-- СНГ -->
							<td>{{$stat->requested_cis}}</td>
							<td>{{$stat->played_cis}}</td>
							<td>{{$stat->clicked_cis}}</td>
							<td>{{$stat->poteri_cis}}</td>
							<td>{{$stat->dosm_cis}}</td>
							<td>{{$stat->util_cis}}</td>
							<td>{{$stat->ctr_cis}}</td>
							<!-- Всего -->
							<td>{{$stat->requested}}</td>
							<td>{{$stat->played}}</td>
							<td>{{$stat->clicked}}</td>
							<td>{{$stat->poteri}}</td>
							<td>{{$stat->dosm}}</td>
							<td>{{$stat->util}}</td>
							<td>{{$stat->ctr}}</td>
						</tr>
					@endforeach
				</table>
				<div class="col-xs-12 text-center">
					{!!$stats->appends(['from' => $from,'to'=>$to])->render() !!}
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
			font-size: 12px;
			text-align: center;
		}
		.table > thead > tr > th, .table > thead > tr > td, .table > tbody > tr > th, .table > tbody > tr > td, .table > tfoot > tr > th, .table > tfoot > tr > td{
			vertical-align: middle;
			padding: 4px;
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
	</style>
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