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
					<form class="form-inline" role="form" method="get" action=" {{ route('video_statistic.new_video_stat') }}">
						<div class="row">
							<div class="input-group col-xs-2 form-group">
								<span class="input-group-addon">С:</span>
								<input type="text" class="form-control" value="{{$from}}" name="from">
							</div>
							<div class="input-group col-xs-2 form-group">
								<span class="input-group-addon">По:</span>
								<input type="text" class="form-control" value="{{$to}}" name="to">
							</div>
							<div class="input-group col-xs-3 form-group">
								<span class="input-group-addon">Название:</span>
								<input type="text" class="form-control" value="{{$title}}" name="title">
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
				<h4 class="text-center">Статистика по ссылкам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4></h4>
				{!! $stats->appends(["from"=>$from, "to"=>$to, "number"=>$number, "title"=>$title, "order"=>$order, "direct"=>$direct])->render() !!}
				<table class="table table-hover table-bordered">
					<thead>
						<tr>
							@foreach($header as $k=>$row)
								<td>
									@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
								</td>
							@endforeach
							<!--<td>Ссылка</td>
							<td>Загрузки</td>
							<td>Показы</td>
							<td>Клики</td>
							<td>Потери</td>
							<td>Досмотры</td>
							<td>Утиль</td>
							<td>Ctr</td>-->
							<td>По дням</td>
							<td>Клик</td>
						</tr>
					</thead>
					<tr style="background: black; color: white">
						<td>ВСЕГО</td>
						<td>{{$sum_stat->requested}}</td>
						<td>{{$sum_stat->played}}</td>
						<td>{{$sum_stat->clicked}}</td>
						<td>{{$sum_stat->poteri}}</td>
						<td>{{$sum_stat->dosm}}</td>
						<td>{{$sum_stat->util}}</td>
						<td>{{$sum_stat->ctr}}</td>
						<td></td>
						<td></td>
					</tr>
					@foreach ($stats as $stat)
						<tr>
							<td>{{$stat->title}} <b>@if (!$stat->player) (Напрямую) @else @foreach ($players as $player) 
							@if ($player->id==$stat->player) ({{$player->title_for_sait}})
							@endif
							@endforeach
							@endif</b>
							
							@foreach ($LinkComent as $LinkC) 
								@if ($LinkC->id==$stat->id_src) <br> {{$LinkC->coment}}
								@endif
							@endforeach
							
							</td>
							<td>{{$stat->requested}}</td>
							<td>{{$stat->played}}</td>
							<td>{{$stat->clicked}}</td>
							<td>{{$stat->poteri}}</td>
							<td>{{$stat->dosm}}</td>
							<td>{{$stat->util}}</td>
							<td><a href="{{ route('mpstatistica.videopads',['id_src'=>$stat->id_src]) }}">{{$stat->ctr}}</a></td>
							<td><a data-toggle="tooltip" data-placement="bottom" title="Статистика по дням" href="{{ route('video_statistic.new_video_stat_detail', ['id'=>$stat->id_src]) }}"><span class="glyphicon glyphicon-th news-gliph-all color-blue"></span></a></td>
							<td><a data-toggle="tooltip" data-placement="bottom" title="Редактировать ссылку" href="{{ route('video_setting.source.edit', ['id'=>$stat->id_src]) }}" target="_blank"><span class="glyphicon glyphicon-pencil news-gliph-all color-blue"></span></a></td>
						</tr>
					@endforeach
				</table>
				{!! $stats->appends(["from"=>$from, "to"=>$to, "number"=>$number, "title"=>$title, "order"=>$order, "direct"=>$direct])->render() !!}
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
		.color-blue {
			color: rgb(0, 73, 150);
		}
		.news-gliph-all {
			font-size: 16px;
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