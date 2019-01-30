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
				<h4 class="text-center">Товарная статистика по площадке <b>{{$pad->domain}}</b> в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				{!! $pad_stats->appends(['from'=>$from, 'to'=>$to, 'order'=>$order, 'direct'=>$direct, 'number'=>$number])->render() !!}
				<div class="col-xs-12">
				<table class="table table-hover table-bordered" style="margin-top: 10px">
							<thead>
							<tr>
								
									<td colspan =5>Общ.</td>
									<td colspan =3>Яндекс</td>
									<td colspan =3>Адверт</td>
								    <td colspan =3>Прямые рекламодатели</td>
								</tr>
								<tr>
								
									@foreach($header as $k=>$row)
												<td>
													@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
												</td>
											@endforeach
							    <tbody>
								<tr style="background: #000; color: #fff;">
                                    <td>Всего</td>
									<td>{{$pad_stat_all['views']}}</td>
									<td>{{$pad_stat_all['clicks']}}</td>
									<td>{{$pad_stat_all['ctr']}}</td>									
									<td>{{$pad_stat_all['summa']}}</td>
									<td>{{$pad_stat_all['yclicks']}}</td>
									<td>{{$pad_stat_all['ysumma']}}</td>
									<td>{{$pad_stat_all['ycpc']}}</td>
								    <td>{{$pad_stat_all['tclicks']}}</td>
									<td>{{$pad_stat_all['tsumma']}}</td>
									<td>{{$pad_stat_all['tcpc']}}</td>
									<td>0</td>
									<td>0</td>
									<td>0</td>
								</tr>
								@foreach ($pad_stats as $pad_stat)
										<tr>
											<td>{{$pad_stat->day}}</td>
											<td>{{$pad_stat->views}}</td>
											<td>{{$pad_stat->clicks}}</td>
											<td>{{$pad_stat->ctr}}</td>
											<td>{{$pad_stat->summa}}</td>
											<td>{{$pad_stat->yclicks}}</td>
											<td>{{$pad_stat->ysumma}}</td>
											<td>{{$pad_stat->ycpc}}</td>
											<td>{{$pad_stat->tclicks}}</td>
											<td>{{$pad_stat->tsumma}}</td>
											<td>{{$pad_stat->tcpc}}</td>
											<td>0</td>
											<td>0</td>
											<td>0</td>
										</tr>
									@endforeach	
								</tbody>
							</thead>
                 </table>							
				 
				</div>
				{{--	
				<div class="col-xs-12 col-xs-12">
					{!! $pad_stats->appends(['from'=>$from, 'to'=>$to, 'order'=>$order, 'direct'=>$direct, 'number'=>$number])->render() !!}
					<div class="affiliate_cabinet_block text-center users_cabinet_block">
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
											@foreach($header as $k=>$row)
												<td>
													@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
												</td>
											@endforeach
										</tr>
									</thead>
									<tr style="background: black; color: white">
										<td>Всего</td>
										<td>{{$pad_stat_all->loaded}}</td>
										<td>{{$pad_stat_all->played}}</td>
										<td>{{$pad_stat_all->calculate}}</td>
										<td>{{$pad_stat_all->deep}}</td>
										<td>{{$pad_stat_all->util}}</td>
										<td>{{$pad_stat_all->dosm}}</td>
										<td>{{$pad_stat_all->clicks}}</td>
										<td>{{$pad_stat_all->ctr}}</td>
										<td>{{$pad_stat_all->second}}</td>
										<td>{{$pad_stat_all->second_all}}</td>
										<td>{{$pad_stat_all->second_summa}}</td>
										<td>{{$pad_stat_all->summa}}</td>
									</tr>
									@foreach ($pad_stats as $pad_stat)
										<tr>
											<td>{{$pad_stat->day}}</td>
											<td>{{$pad_stat->loaded}}</td>
											<td>{{$pad_stat->played}}</td>
											<td>{{$pad_stat->calculate}}</td>
											<td>{{$pad_stat->deep}}</td>
											<td>{{$pad_stat->util}}</td>
											<td>{{$pad_stat->dosm}}</td>
											<td>{{$pad_stat->clicks}}</td>
											<td>{{$pad_stat->ctr}}</td>
											<td>{{$pad_stat->second}}</td>
											<td>{{$pad_stat->second_all}}</td>
											<td>{{$pad_stat->second_summa}}</td>
											<td>{{$pad_stat->summa}}</td>
										</tr>
									@endforeach
								</table>
							</div>
							
							<div class="tab-pane" id="rus_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@foreach($header as $k=>$row)
												<td>
													@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
												</td>
											@endforeach
											<!--<td>Дата</td>
											<td>Загрузки</td>
											<td>Показы</td>
											<td>Зачтенные показы</td>
											<td>Глубина</td>
											<td>Утиль</td>
											<td>Досмотры</td>
											<td>Клики</td>
											<td>Ctr</td>
											<td>Доход</td>-->
										</tr>
									</thead>
									<tr style="background: black; color: white">
										<td>Всего</td>
										<td>{{$pad_stat_all_ru->loaded}}</td>
										<td>{{$pad_stat_all_ru->played}}</td>
										<td>{{$pad_stat_all_ru->calculate}}</td>
										<td>{{$pad_stat_all_ru->deep}}</td>
										<td>{{$pad_stat_all_ru->util}}</td>
										<td>{{$pad_stat_all_ru->dosm}}</td>
										<td>{{$pad_stat_all_ru->clicks}}</td>
										<td>{{$pad_stat_all_ru->ctr}}</td>
										<td>{{$pad_stat_all_ru->second}}</td>
										<td>{{$pad_stat_all_ru->second_all}}</td>
										<td>{{$pad_stat_all_ru->second_summa}}</td>
										<td>{{$pad_stat_all_ru->summa}}</td>
									</tr>
									@foreach ($pad_stats_ru as $pad_stat_ru)
										<tr>
											<td>{{$pad_stat_ru->day}}</td>
											<td>{{$pad_stat_ru->loaded}}</td>
											<td>{{$pad_stat_ru->played}}</td>
											<td>{{$pad_stat_ru->calculate}}</td>
											<td>{{$pad_stat_ru->deep}}</td>
											<td>{{$pad_stat_ru->util}}</td>
											<td>{{$pad_stat_ru->dosm}}</td>
											<td>{{$pad_stat_ru->clicks}}</td>
											<td>{{$pad_stat_ru->ctr}}</td>
											<td>{{$pad_stat_ru->second}}</td>
											<td>{{$pad_stat_ru->second_all}}</td>
											<td>{{$pad_stat_ru->second_summa}}</td>
											<td>{{$pad_stat_ru->summa}}</td>
										</tr>
									@endforeach
								</table>
							</div>
							
							<div class="tab-pane" id="cis_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@foreach($header as $k=>$row)
												<td>
													@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
												</td>
											@endforeach
											<!--<td>Дата</td>
											<td>Загрузки</td>
											<td>Показы</td>
											<td>Зачтенные показы</td>
											<td>Глубина</td>
											<td>Утиль</td>
											<td>Досмотры</td>
											<td>Клики</td>
											<td>Ctr</td>
											<td>Доход</td>-->
										</tr>
									</thead>
									<tr style="background: black; color: white">
										<td>Всего</td>
										<td>{{$pad_stat_all_cis->loaded}}</td>
										<td>{{$pad_stat_all_cis->played}}</td>
										<td>{{$pad_stat_all_cis->calculate}}</td>
										<td>{{$pad_stat_all_cis->deep}}</td>
										<td>{{$pad_stat_all_cis->util}}</td>
										<td>{{$pad_stat_all_cis->dosm}}</td>
										<td>{{$pad_stat_all_cis->clicks}}</td>
										<td>{{$pad_stat_all_cis->ctr}}</td>
										<td>{{$pad_stat_all_cis->second}}</td>
										<td>{{$pad_stat_all_cis->second_all}}</td>
										<td>{{$pad_stat_all_cis->second_summa}}</td>
										<td>{{$pad_stat_all_cis->summa}}</td>
									</tr>
									@foreach ($pad_stats_cis as $pad_stat_cis)
										<tr>
											<td>{{$pad_stat_cis->day}}</td>
											<td>{{$pad_stat_cis->loaded}}</td>
											<td>{{$pad_stat_cis->played}}</td>
											<td>{{$pad_stat_cis->calculate}}</td>
											<td>{{$pad_stat_cis->deep}}</td>
											<td>{{$pad_stat_cis->util}}</td>
											<td>{{$pad_stat_cis->dosm}}</td>
											<td>{{$pad_stat_cis->clicks}}</td>
											<td>{{$pad_stat_cis->ctr}}</td>
											<td>{{$pad_stat_cis->second}}</td>
											<td>{{$pad_stat_cis->second_all}}</td>
											<td>{{$pad_stat_cis->second_summa}}</td>
											<td>{{$pad_stat_cis->summa}}</td>
										</tr>
									@endforeach
								</table>
							</div>
						</div>
					</div>
					{!! $pad_stats->appends(['from'=>$from, 'to'=>$to, 'order'=>$order, 'direct'=>$direct, 'number'=>$number])->render() !!}
				</div>
				--}}
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