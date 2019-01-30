@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
		<div class="row">
				<div class="col-xs-12 col-xs-12">
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
								<span class="input-group-addon">Поиск:</span>
								<input type="text" class="form-control" value="{{$search}}" name="search">
							</div>
							<div class="input-group col-xs-2 form-group">
								<select name="category" class="form-control">
									<option value="all">Все</option>
									<option @if ($category=='white') selected @endif value="white">Белые</option>
									<option @if ($category=='adult') selected @endif  value="adult">Адалт</option>
									<option @if ($category=='razv') selected @endif  value="razv">Развлекательные</option>
								</select>
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
							<div class="col-xs-1 input-group form-group">
								<button type="submit" class="btn btn-primary">Применить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<h4 class="text-center">Видео статистика по площадкам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				<div class="col-xs-12">
					{!! $pads_stats->appends(['search'=>$search,'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'category'=>$category, 'order'=>$order, 'direct'=>$direct])->render() !!}
					<table class="table table-hover table-bordered" style="margin-top: 10px">
						<thead>
							<tr>
								@foreach($header as $k=>$row)
									<td>
										@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
									</td>
								@endforeach
									<td>Подробнее</td>
									<td>Редактор</td>
							</tr>
						</thead>
						<tr style="background: black; color: white">
							<td>Всего</td>
							<td>{{$pads_stat_all->loaded}}</td>
							<td>{{$pads_stat_all->played}}</td>
							<td>{{$pads_stat_all->calculate}}</td>
							<td>{{round($pads_stat_all->deep,2)}}</td>
							<td>{{round($pads_stat_all->util,2)}}</td>
							<td>{{round($pads_stat_all->dosm,2)}}</td>
							<td>{{$pads_stat_all->clicks}}</td>
							<td>{{round($pads_stat_all->ctr,2)}}</td>
							<td>{{$pads_stat_all->second}}</td>
							<td>{{$pads_stat_all->second_all}}</td>
							<td>{{round($pads_stat_all->second_summa,2)}}</td>
							<td>{{round($pads_stat_all->summa,2)}}</td>
							<td>{{round($pads_stat_all->coef,2)}}</td>
							<td>{{round($pads_stat_all->viewable,2)}}</td>
							<td></td>
							<td></td>
						</tr>
						@foreach ($pads_stats as $pad_stat)
							<tr>
								<td><a href="{{route('admin.home', ['user_id'=>$pad_stat->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="{{$pad_stat->name}}" target="_blank">{{$pad_stat->domain}}</a></td>
								<td>{{$pad_stat->loaded}}</td>
								<td>{{$pad_stat->played}}</td>
								<td>{{$pad_stat->calculate}}</td>
								<td>{{round($pad_stat->deep,2)}}</td>
								<td>{{round($pad_stat->util,2)}}</td>
								<td>{{round($pad_stat->dosm,2)}}</td>
								<td>{{$pad_stat->clicks}}</td>
								<td>{{round($pad_stat->ctr,2)}}</td>
								<td>{{$pad_stat->second}}</td>
								<td>{{$pad_stat->second_all}}</td>
								<td>{{round($pad_stat->second_summa,2)}}</td>
								<td>{{round($pad_stat->summa,2)}}</td>
								<td>{{round($pad_stat->coef,2)}}</td>
								<td>{{round($pad_stat->viewable,2)}}</td>
								<td><a href="{{route('video_statistic.pad_statistic', ['id'=>$pad_stat->id])}}" data-toggle="tooltip" data-placement="bottom" title="Статистика по дням" target="_blank"><span class="glyphicon glyphicon-th news-gliph-all color-blue"></span></a></td>
								<td><a href="{{route('pads.edit', ['id'=>$pad_stat->id])}}" data-toggle="tooltip" data-placement="bottom" title="Редактировать площадку" target="_blank"><span class="glyphicon glyphicon-pencil news-gliph-all color-blue"></span></a></td>
							</tr>
						@endforeach		
					</table>
					{!! $pads_stats->appends(['search'=>$search,'from'=>$from, 'to'=>$to, 'search'=>$search, 'number'=>$number, 'category'=>$category, 'order'=>$order, 'direct'=>$direct])->render() !!}
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