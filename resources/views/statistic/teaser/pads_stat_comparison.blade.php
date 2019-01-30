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
							{{--
						<div class="input-group col-xs-2 form-group" style="margin: 5px 0;">
							<select name="category" class="form-control">
								<option value="all">Все</option>
								<option @if ($category=='white') selected @endif value="white">Белые</option>
								<option @if ($category=='adult') selected @endif  value="adult">Адалт</option>
								<option @if ($category=='razv') selected @endif  value="razv">Развлекательные</option>
							</select>
						</div>
							--}}
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
				<h4 class="text-center">Тизер статистика по площадкам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				
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
									
							</tr>
							<tr style="background: black; color: white">
							     <td>Всего</td>
								 <td>
								 <span style="color: rgb(0, 54, 247);">{{$pads_stat_all['old_views']}}</span><br>
								 {{$pads_stat_all['views']}}<br>
								  @if($pads_stat_all['views_rozn']>0)
								    <span style="color: #009400;">+{{$pads_stat_all['views_rozn']}}</span>
								  @else
									 <span style="color: #e20000;">{{$pads_stat_all['views_rozn']}}</span> 
                                  @endif	
								 </td>
								 <td>
								 <span style="color: rgb(0, 54, 247);">{{$pads_stat_all['old_clicks']}}</span><br>
								 {{$pads_stat_all['clicks']}}<br>
								  @if($pads_stat_all['clicks_rozn']>0)
								    <span style="color: #009400;">+{{$pads_stat_all['clicks_rozn']}}</span>
								  @else
									 <span style="color: #e20000;">{{$pads_stat_all['clicks_rozn']}}</span> 
                                  @endif	
								 </td>
								 <td>
								 <span style="color: rgb(0, 54, 247);">{{$pads_stat_all['old_ctr']}}</span><br>
								 {{$pads_stat_all['ctr']}}<br>
								  @if($pads_stat_all['ctr_rozn']>0)
								    <span style="color: #009400;">+{{$pads_stat_all['ctr_rozn']}}</span>
								  @else
									 <span style="color: #e20000;">{{$pads_stat_all['ctr_rozn']}}</span> 
                                  @endif	
								 </td>
								 								 <td>
								 <span style="color: rgb(0, 54, 247);">{{$pads_stat_all['old_cpc']}}</span><br>
								 {{$pads_stat_all['cpc']}}<br>
								  @if($pads_stat_all['cpc_rozn']>0)
								    <span style="color: #009400;">+{{$pads_stat_all['cpc_rozn']}}</span>
								  @else
									 <span style="color: #e20000;">{{$pads_stat_all['cpc_rozn']}}</span> 
                                  @endif	
								 </td>
																 								 <td>
								 <span style="color: rgb(0, 54, 247);">{{$pads_stat_all['old_summa']}}</span><br>
								 {{$pads_stat_all['summa']}}<br>
								  @if($pads_stat_all['summa_rozn']>0)
								    <span style="color: #009400;">+{{$pads_stat_all['summa_rozn']}}</span>
								  @else
									 <span style="color: #e20000;">{{$pads_stat_all['summa_rozn']}}</span> 
                                  @endif	
								 </td> 
								 <td></td>
								
							<tr>	
							@foreach ($pads_stats as $pad_stat)
							<tr>
							      <td>{{$pad_stat->name}}</td>
								  <td>
								  <span style="color: rgb(0, 54, 247);">{{$pad_stat->old_views}}</span><br>{{$pad_stat->views}}<br>
								   @if($pad_stat->views_rozn>0)
								    <span style="color: #009400;">+{{$pad_stat->views_rozn}}</span>
								  @else
									 <span style="color: #e20000;">{{$pad_stat->views_rozn}}</span> 
                                  @endif	
								  </td>
								  <td>
								  <span style="color: rgb(0, 54, 247);">{{$pad_stat->old_clicks}}</span><br>{{$pad_stat->clicks}}<br>
								   @if($pad_stat->clicks_rozn>0)
								    <span style="color: #009400;">+{{$pad_stat->clicks_rozn}}</span>
								  @else
									 <span style="color: #e20000;">{{$pad_stat->clicks_rozn}}</span> 
                                  @endif	
								  </td>
								  <td>
								  <span style="color: rgb(0, 54, 247);">{{$pad_stat->old_ctr}}</span><br>{{$pad_stat->ctr}}<br>
								  @if($pad_stat->ctr_rozn>0)
								    <span style="color: #009400;">+{{$pad_stat->ctr_rozn}}</span>
								  @else
									 <span style="color: #e20000;">{{$pad_stat->ctr_rozn}}</span> 
                                  @endif	
								  </td>
								  <td>
								  <span style="color: rgb(0, 54, 247);">{{$pad_stat->old_cpc}}</span><br>{{$pad_stat->cpc}}<br>
								  @if($pad_stat->cpc_rozn>0)
									  <span style="color: #009400;">+{{$pad_stat->cpc_rozn}}</span>
								  @else
									 <span style="color: #e20000;">{{$pad_stat->cpc_rozn}}</span> 
                                  @endif									  
								  </td>
								  <td>
								  <span style="color: rgb(0, 54, 247);">{{$pad_stat->old_summa}}</span><br>{{$pad_stat->summa}}<br>
								  @if($pad_stat->summa_rozn>0)
									  <span style="color: #009400;">+{{$pad_stat->summa_rozn}}</span>
								  @else
									 <span style="color: #e20000;">{{$pad_stat->summa_rozn}}</span> 
                                  @endif									  
								  </td>
								 <td><a href="{{route('teaser_statistic.teaser_detail_pad_one', ['id'=>$pad_stat->pad])}}" data-toggle="tooltip" data-placement="bottom" title="Статистика по дням" target="_blank"><span class="glyphicon glyphicon-th news-gliph-all color-blue"></span></a></td>
																  
							</tr>
							@endforeach
						</thead>		
						
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