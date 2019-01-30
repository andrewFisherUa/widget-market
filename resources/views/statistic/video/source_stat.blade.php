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
					<form class="form-inline" role="form" method="get" action=" {{ route('video_statistic.video_source_stat') }}">
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
				<h4 class="text-center">Статистика по ссылкам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4></h4>
				<table class="table table-hover table-bordered">
					<tr>
						@foreach($header as $k=>$row)
							<td>
								@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
							</td>
						@endforeach
						<td>По дням</td>
						<td>Клик</td>
						<!--<td >Ссылка</td>
						<td>Загрузки</td>
						<td>Показы</td>
						<td>Клики</td>
						<td>Потери</td>
						<td>Досмотры</td>
						<td>Утиль</td>
						<td>Ctr</td>
						<td>Детал.</td>
						<td>Клик</td>-->
					</tr>
					@foreach ($stats as $stat)
						<tr @if (($stat->poteri) > 30) class="danger body_sum" @endif>
							<td>{{$stat->title}}</td>
							<!-- Всего -->
							<td>{{$stat->requested}}</td>
							<td>{{$stat->played}}</td>
							<td>{{$stat->clicked}}</td>
							<td>{{$stat->poteri}}</td>
							<td>{{$stat->dosm}}</td>
							<td>{{$stat->util}}</td>
							<td>{{$stat->ctr}}</td>
							<td><a data-toggle="tooltip" data-placement="bottom" title="Статистика по дням" href="{{ route('video_statistic.video_source_stat_detail', ['id'=>$stat->id_src]) }}"><span class="glyphicon glyphicon-th"></span></a></td>
							<td>Клик</td>
						</tr>
					@endforeach
					
				</table>
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
			padding: 8px;
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