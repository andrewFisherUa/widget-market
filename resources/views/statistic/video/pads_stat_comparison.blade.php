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
							<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
								<span class="input-group-addon" style="color: rgb(0, 54, 247);">С:</span>
								<input type="text" class="form-control" value="{{$fromOld}}" name="fromOld">
							</div>
							<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
								<span class="input-group-addon" style="color: rgb(0, 54, 247);">По:</span>
								<input type="text" class="form-control" value="{{$toOld}}" name="toOld">
							</div>
							<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
								<span class="input-group-addon">С:</span>
								<input type="text" class="form-control" value="{{$from}}" name="from">
							</div>
							<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
								<span class="input-group-addon">По:</span>
								<input type="text" class="form-control" value="{{$to}}" name="to">
							</div>
							<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
								<span class="input-group-addon">Поиск:</span>
								<input type="text" class="form-control" value="{{$search}}" name="search">
							</div>
						<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
							<select name="category" class="form-control">
								<option value="all">Все</option>
								<option @if ($category=='white') selected @endif value="white">Белые</option>
								<option @if ($category=='adult') selected @endif  value="adult">Адалт</option>
								<option @if ($category=='razv') selected @endif  value="razv">Развлекательные</option>
							</select>
						</div>
						<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
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
						<div class="col-xs-2 input-group form-group" style="margin: 5px 0;">
							<button type="submit" class="btn btn-primary">Применить</button>
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
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_loaded}}</span><br>{{$pads_stat_all->loaded}}<br>
							@if ($pads_stat_all->loaded-$pads_stat_all->old_loaded>0) <span style="color: #009400">+{{$pads_stat_all->loaded-$pads_stat_all->old_loaded}}</span>
							@else
							<span style="color: #e20000;">{{$pads_stat_all->loaded-$pads_stat_all->old_loaded}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_played}}</span><br>{{$pads_stat_all->played}}<br>
							@if ($pads_stat_all->played-$pads_stat_all->old_played>0) <span style="color: #009400">+{{$pads_stat_all->played-$pads_stat_all->old_played}}</span>
							@else
							<span style="color: #e20000;">{{$pads_stat_all->played-$pads_stat_all->old_played}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_calculate}}</span><br>{{$pads_stat_all->calculate}}<br>
							@if ($pads_stat_all->calculate-$pads_stat_all->old_calculate>0) <span style="color: #009400">+{{$pads_stat_all->calculate-$pads_stat_all->old_calculate}}</span>
							@else
							<span style="color: #e20000;">{{$pads_stat_all->calculate-$pads_stat_all->old_calculate}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_deep}}</span><br>{{$pads_stat_all->deep}}<br>
							@if ($pads_stat_all->deep-$pads_stat_all->old_deep>0) <span style="color: #009400">+{{round($pads_stat_all->deep-$pads_stat_all->old_deep,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($pads_stat_all->deep-$pads_stat_all->old_deep,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_util}}</span><br>{{$pads_stat_all->util}}<br>
							@if ($pads_stat_all->util-$pads_stat_all->old_util>0) <span style="color: #009400">+{{round($pads_stat_all->util-$pads_stat_all->old_util,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($pads_stat_all->util-$pads_stat_all->old_util,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_dosm}}</span><br>{{$pads_stat_all->dosm}}<br>
							@if ($pads_stat_all->dosm-$pads_stat_all->old_dosm>0) <span style="color: #009400">+{{round($pads_stat_all->dosm-$pads_stat_all->old_dosm,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($pads_stat_all->dosm-$pads_stat_all->old_dosm,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_clicks}}</span><br>{{$pads_stat_all->clicks}}<br>
							@if ($pads_stat_all->clicks-$pads_stat_all->old_clicks>0) <span style="color: #009400">+{{$pads_stat_all->clicks-$pads_stat_all->old_clicks}}</span>
							@else
							<span style="color: #e20000;">{{$pads_stat_all->clicks-$pads_stat_all->old_clicks}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_ctr}}</span><br>{{$pads_stat_all->ctr}}<br>
							@if ($pads_stat_all->ctr-$pads_stat_all->old_ctr>0) <span style="color: #009400">+{{round($pads_stat_all->ctr-$pads_stat_all->old_ctr,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($pads_stat_all->ctr-$pads_stat_all->old_ctr,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_second}}</span><br>{{$pads_stat_all->second}}<br>
							@if ($pads_stat_all->second-$pads_stat_all->old_second>0) <span style="color: #009400">+{{$pads_stat_all->second-$pads_stat_all->old_second}}</span>
							@else
							<span style="color: #e20000;">{{$pads_stat_all->second-$pads_stat_all->old_second}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_second_all}}</span><br>{{$pads_stat_all->second_all}}<br>
							@if ($pads_stat_all->second_all-$pads_stat_all->old_second_all>0) <span style="color: #009400">+{{$pads_stat_all->second_all-$pads_stat_all->old_second_all}}</span>
							@else
							<span style="color: #e20000;">{{$pads_stat_all->second_all-$pads_stat_all->old_second_all}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_second_summa}}</span><br>{{$pads_stat_all->second_summa}}<br>
							@if ($pads_stat_all->second_summa-$pads_stat_all->old_second_summa>0) <span style="color: #009400">+{{round($pads_stat_all->second_summa-$pads_stat_all->old_second_summa,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($pads_stat_all->second_summa-$pads_stat_all->old_second_summa,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$pads_stat_all->old_summa}}</span><br>{{$pads_stat_all->summa}}<br>
							@if ($pads_stat_all->summa-$pads_stat_all->old_summa>0) <span style="color: #009400">+{{round($pads_stat_all->summa-$pads_stat_all->old_summa,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($pads_stat_all->summa-$pads_stat_all->old_summa,4)}}</span>
							@endif
							</td>
							<td></td>
							<td></td>
						</tr>
						@foreach ($pads_stats as $pad_stat)
							<tr>
								<td><a href="{{route('admin.home', ['user_id'=>$pad_stat->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="{{$pad_stat->name}}" target="_blank">{{$pad_stat->domain}}</a></td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_loaded}}</span><br>{{$pad_stat->loaded}}<br>
								@if ($pad_stat->loaded-$pad_stat->old_loaded>0) <span style="color: #009400">+{{$pad_stat->loaded-$pad_stat->old_loaded}}</span>
								@else
								<span style="color: #e20000;">{{$pad_stat->loaded-$pad_stat->old_loaded}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_played}}</span><br>{{$pad_stat->played}}<br>
								@if ($pad_stat->played-$pad_stat->old_played>0) <span style="color: #009400">+{{$pad_stat->played-$pad_stat->old_played}}</span>
								@else
								<span style="color: #e20000;">{{$pad_stat->played-$pad_stat->old_played}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_calculate}}</span><br>{{$pad_stat->calculate}}<br>
								@if ($pad_stat->calculate-$pad_stat->old_calculate>0) <span style="color: #009400">+{{$pad_stat->calculate-$pad_stat->old_calculate}}</span>
								@else
								<span style="color: #e20000;">{{$pad_stat->calculate-$pad_stat->old_calculate}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_deep}}</span><br>{{$pad_stat->deep}}<br>
								@if ($pad_stat->deep-$pad_stat->old_deep>0) <span style="color: #009400">+{{round($pad_stat->deep-$pad_stat->old_deep,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($pad_stat->deep-$pad_stat->old_deep,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_util}}</span><br>{{$pad_stat->util}}<br>
								@if ($pad_stat->util-$pad_stat->old_util>0) <span style="color: #009400">+{{round($pad_stat->util-$pad_stat->old_util,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($pad_stat->util-$pad_stat->old_util,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_dosm}}</span><br>{{$pad_stat->dosm}}<br>
								@if ($pad_stat->dosm-$pad_stat->old_dosm>0) <span style="color: #009400">+{{round($pad_stat->dosm-$pad_stat->old_dosm,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($pad_stat->dosm-$pad_stat->old_dosm,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_clicks}}</span><br>{{$pad_stat->clicks}}<br>
								@if ($pad_stat->clicks-$pad_stat->old_clicks>0) <span style="color: #009400">+{{$pad_stat->clicks-$pad_stat->old_clicks}}</span>
								@else
								<span style="color: #e20000;">{{$pad_stat->clicks-$pad_stat->old_clicks}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_ctr}}</span><br>{{$pad_stat->ctr}}<br>
								@if ($pad_stat->ctr-$pad_stat->old_ctr>0) <span style="color: #009400">+{{round($pad_stat->ctr-$pad_stat->old_ctr,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($pad_stat->ctr-$pad_stat->old_ctr,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_second}}</span><br>{{$pad_stat->second}}<br>
								@if ($pad_stat->second-$pad_stat->old_second>0) <span style="color: #009400">+{{$pad_stat->second-$pad_stat->old_second}}</span>
								@else
								<span style="color: #e20000;">{{$pad_stat->second-$pad_stat->old_second}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_second_all}}</span><br>{{$pad_stat->second_all}}<br>
								@if ($pad_stat->second_all-$pad_stat->old_second_all>0) <span style="color: #009400">+{{$pad_stat->second_all-$pad_stat->old_second_all}}</span>
								@else
								<span style="color: #e20000;">{{$pad_stat->second_all-$pad_stat->old_second_all}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_second_summa}}</span><br>{{$pad_stat->second_summa}}<br>
								@if ($pad_stat->second_summa-$pad_stat->old_second_summa>0) <span style="color: #009400">+{{round($pad_stat->second_summa-$pad_stat->old_second_summa,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($pad_stat->second_summa-$pad_stat->old_second_summa,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$pad_stat->old_summa}}</span><br>{{$pad_stat->summa}}<br>
								@if ($pad_stat->summa-$pad_stat->old_summa>0) <span style="color: #009400">+{{round($pad_stat->summa-$pad_stat->old_summa,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($pad_stat->summa-$pad_stat->old_summa,4)}}</span>
								@endif
								</td>
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
	$('input[name="fromOld"]').daterangepicker({
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
	$('input[name="toOld"]').daterangepicker({
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