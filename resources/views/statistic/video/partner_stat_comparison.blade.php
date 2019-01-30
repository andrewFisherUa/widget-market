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
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager'))
						<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
							<select name='manager' class="form-control">
								<option value="0">Все</option>
								@foreach (\App\Role::whereIn('id', [3,4,5])->get() as $role)
									@foreach ($role->users as $user)
										<option @if ($manager==$user->id) selected @endif value="{{$user->id}}">{{$user->name}}</option>
									@endforeach
								@endforeach
							</select>
						</div>
						@endif
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
				<h4 class="text-center">Видео статистика по партнерам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				<div class="col-xs-12">
					{!! $partner_stats->appends(['search'=>$search, 'number'=>$number, 'order'=>$order, 'direct'=>$direct, 'manager'=>$manager, 'search'=>$search, 'from'=>$from, 'to'=>$to])->render() !!}
					<table class="table table-hover table-bordered" style="margin-top: 10px">
						<thead>
							<tr>
								@foreach($header as $k=>$row)
									<td>
										@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
									</td>
								@endforeach
								<td>Подробнее</td>
							</tr>
						</thead>
						<tr style="background: black; color: white">
							<td>Всего</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_loaded}}</span><br>{{$partner_all_stat->loaded}}<br>
							@if ($partner_all_stat->loaded-$partner_all_stat->old_loaded>0) <span style="color: #009400">+{{$partner_all_stat->loaded-$partner_all_stat->old_loaded}}</span>
							@else
							<span style="color: #e20000;">{{$partner_all_stat->loaded-$partner_all_stat->old_loaded}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_played}}</span><br>{{$partner_all_stat->played}}<br>
							@if ($partner_all_stat->played-$partner_all_stat->old_played>0) <span style="color: #009400">+{{$partner_all_stat->played-$partner_all_stat->old_played}}</span>
							@else
							<span style="color: #e20000;">{{$partner_all_stat->played-$partner_all_stat->old_played}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_calculate}}</span><br>{{$partner_all_stat->calculate}}<br>
							@if ($partner_all_stat->calculate-$partner_all_stat->old_calculate>0) <span style="color: #009400">+{{$partner_all_stat->calculate-$partner_all_stat->old_calculate}}</span>
							@else
							<span style="color: #e20000;">{{$partner_all_stat->calculate-$partner_all_stat->old_calculate}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_deep}}</span><br>{{$partner_all_stat->deep}}<br>
							@if ($partner_all_stat->deep-$partner_all_stat->old_deep>0) <span style="color: #009400">+{{round($partner_all_stat->deep-$partner_all_stat->old_deep,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($partner_all_stat->deep-$partner_all_stat->old_deep,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_util}}</span><br>{{$partner_all_stat->util}}<br>
							@if ($partner_all_stat->util-$partner_all_stat->old_util>0) <span style="color: #009400">+{{round($partner_all_stat->util-$partner_all_stat->old_util,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($partner_all_stat->util-$partner_all_stat->old_util,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_dosm}}</span><br>{{$partner_all_stat->dosm}}<br>
							@if ($partner_all_stat->dosm-$partner_all_stat->old_dosm>0) <span style="color: #009400">+{{round($partner_all_stat->dosm-$partner_all_stat->old_dosm,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($partner_all_stat->dosm-$partner_all_stat->old_dosm,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_clicks}}</span><br>{{$partner_all_stat->clicks}}<br>
							@if ($partner_all_stat->clicks-$partner_all_stat->old_clicks>0) <span style="color: #009400">+{{$partner_all_stat->clicks-$partner_all_stat->old_clicks}}</span>
							@else
							<span style="color: #e20000;">{{$partner_all_stat->clicks-$partner_all_stat->old_clicks}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_ctr}}</span><br>{{$partner_all_stat->ctr}}<br>
							@if ($partner_all_stat->ctr-$partner_all_stat->old_ctr>0) <span style="color: #009400">+{{round($partner_all_stat->ctr-$partner_all_stat->old_ctr,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($partner_all_stat->ctr-$partner_all_stat->old_ctr,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_second}}</span><br>{{$partner_all_stat->second}}<br>
							@if ($partner_all_stat->second-$partner_all_stat->old_second>0) <span style="color: #009400">+{{$partner_all_stat->second-$partner_all_stat->old_second}}</span>
							@else
							<span style="color: #e20000;">{{$partner_all_stat->second-$partner_all_stat->old_second}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_second_all}}</span><br>{{$partner_all_stat->second_all}}<br>
							@if ($partner_all_stat->second_all-$partner_all_stat->old_second_all>0) <span style="color: #009400">+{{$partner_all_stat->second_all-$partner_all_stat->old_second_all}}</span>
							@else
							<span style="color: #e20000;">{{$partner_all_stat->second_all-$partner_all_stat->old_second_all}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_second_summa}}</span><br>{{$partner_all_stat->second_summa}}<br>
							@if ($partner_all_stat->second_summa-$partner_all_stat->old_second_summa>0) <span style="color: #009400">+{{round($partner_all_stat->second_summa-$partner_all_stat->old_second_summa,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($partner_all_stat->second_summa-$partner_all_stat->old_second_summa,4)}}</span>
							@endif
							</td>
							<td><span style="color: rgb(0, 54, 247);">{{$partner_all_stat->old_summa}}</span><br>{{$partner_all_stat->summa}}<br>
							@if ($partner_all_stat->summa-$partner_all_stat->old_summa>0) <span style="color: #009400">+{{round($partner_all_stat->summa-$partner_all_stat->old_summa,4)}}</span>
							@else
							<span style="color: #e20000;">{{round($partner_all_stat->summa-$partner_all_stat->old_summa,4)}}</span>
							@endif
							</td>
							<td></td>
						</tr>
						@foreach ($partner_stats as $partner_stat)
							<tr>
								<td><a href="{{route('admin.home', ['user_id'=>$partner_stat->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="{{$partner_stat->email}}" target="_blank">{{$partner_stat->name}}</a></td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_loaded}}</span><br>{{$partner_stat->loaded}}<br>
								@if ($partner_stat->loaded-$partner_stat->old_loaded>0) <span style="color: #009400">+{{$partner_stat->loaded-$partner_stat->old_loaded}}</span>
								@else
								<span style="color: #e20000;">{{$partner_stat->loaded-$partner_stat->old_loaded}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_played}}</span><br>{{$partner_stat->played}}<br>
								@if ($partner_stat->played-$partner_stat->old_played>0) <span style="color: #009400">+{{$partner_stat->played-$partner_stat->old_played}}</span>
								@else
								<span style="color: #e20000;">{{$partner_stat->played-$partner_stat->old_played}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_calculate}}</span><br>{{$partner_stat->calculate}}<br>
								@if ($partner_stat->calculate-$partner_stat->old_calculate>0) <span style="color: #009400">+{{$partner_stat->calculate-$partner_stat->old_calculate}}</span>
								@else
								<span style="color: #e20000;">{{$partner_stat->calculate-$partner_stat->old_calculate}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_deep}}</span><br>{{$partner_stat->deep}}<br>
								@if ($partner_stat->deep-$partner_stat->old_deep>0) <span style="color: #009400">+{{round($partner_stat->deep-$partner_stat->old_deep,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($partner_stat->deep-$partner_stat->old_deep,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_util}}</span><br>{{$partner_stat->util}}<br>
								@if ($partner_stat->util-$partner_stat->old_util>0) <span style="color: #009400">+{{round($partner_stat->util-$partner_stat->old_util,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($partner_stat->util-$partner_stat->old_util,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_dosm}}</span><br>{{$partner_stat->dosm}}<br>
								@if ($partner_stat->dosm-$partner_stat->old_dosm>0) <span style="color: #009400">+{{round($partner_stat->dosm-$partner_stat->old_dosm,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($partner_stat->dosm-$partner_stat->old_dosm,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_clicks}}</span><br>{{$partner_stat->clicks}}<br>
								@if ($partner_stat->clicks-$partner_stat->old_clicks>0) <span style="color: #009400">+{{$partner_stat->clicks-$partner_stat->old_clicks}}</span>
								@else
								<span style="color: #e20000;">{{$partner_stat->clicks-$partner_stat->old_clicks}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_ctr}}</span><br>{{$partner_stat->ctr}}<br>
								@if ($partner_stat->ctr-$partner_stat->old_ctr>0) <span style="color: #009400">+{{round($partner_stat->ctr-$partner_stat->old_ctr,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($partner_stat->ctr-$partner_stat->old_ctr,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_second}}</span><br>{{$partner_stat->second}}<br>
								@if ($partner_stat->second-$partner_stat->old_second>0) <span style="color: #009400">+{{$partner_stat->second-$partner_stat->old_second}}</span>
								@else
								<span style="color: #e20000;">{{$partner_stat->second-$partner_stat->old_second}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_second_all}}</span><br>{{$partner_stat->second_all}}<br>
								@if ($partner_stat->second_all-$partner_stat->old_second_all>0) <span style="color: #009400">+{{$partner_stat->second_all-$partner_stat->old_second_all}}</span>
								@else
								<span style="color: #e20000;">{{$partner_stat->second_all-$partner_stat->old_second_all}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_second_summa}}</span><br>{{$partner_stat->second_summa}}<br>
								@if ($partner_stat->second_summa-$partner_stat->old_second_summa>0) <span style="color: #009400">+{{round($partner_stat->second_summa-$partner_stat->old_second_summa,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($partner_stat->second_summa-$partner_stat->old_second_summa,4)}}</span>
								@endif
								</td>
								<td><span style="color: rgb(0, 54, 247);">{{$partner_stat->old_summa}}</span><br>{{$partner_stat->summa}}<br>
								@if ($partner_stat->summa-$partner_stat->old_summa>0) <span style="color: #009400">+{{round($partner_stat->summa-$partner_stat->old_summa,4)}}</span>
								@else
								<span style="color: #e20000;">{{round($partner_stat->summa-$partner_stat->old_summa,4)}}</span>
								@endif
								</td>
								<td><a href="{{ route('video_statistic.partner_detail_video_summary', ['id'=>$partner_stat->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Статистика по дням" target="_blank"><span class="glyphicon glyphicon-th news-gliph-all color-blue"></span></a></td>
							</tr>
						@endforeach		
					</table>
					{!! $partner_stats->appends(['search'=>$search, 'number'=>$number, 'order'=>$order, 'direct'=>$direct, 'manager'=>$manager, 'search'=>$search, 'from'=>$from, 'to'=>$to])->render() !!}
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