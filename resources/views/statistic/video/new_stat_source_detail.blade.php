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
				<h4 class="text-center">Статистика по ссылке {{$videoSource->title}} <b>@if (!$videoSource->player) (Напрямую) @else @foreach ($players as $player) 
							@if ($player->id==$videoSource->player) ({{$player->title_for_sait}})
							@endif
							@endforeach
							@endif</b> в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				<div class="col-xs-12">
					{!! $stats_all->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
					<div>
						<ul class="nav nav-tabs nav-justified cust-tabs">
							<li class="heading text-left active"><a href="#all_stat" data-toggle="tab">Весь трафик</a></li>
							<li class="heading text-left"><a href="#desctop_stat" data-toggle="tab">Десктопный трафик</a></li>
							<li class="heading text-left"><a href="#mobile_stat" data-toggle="tab">Мобильный трафик</a></li>
						</ul>
						<div class="tab-content">
							
							<div class="tab-pane active" id="all_stat" style="margin-top: 10px;">
								<div>
									<ul class="nav nav-tabs nav-justified cust-tabs">
										<li class="heading text-left active"><a href="#all_all" data-toggle="tab">Всего</a></li>
										<li class="heading text-left"><a href="#all_ru" data-toggle="tab">Россия</a></li>
										<li class="heading text-left"><a href="#all_cis" data-toggle="tab">СНГ</a></li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="all_all">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												<td><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
												@foreach($header as $k=>$row)
													@if ($k>14)
														<td>
															@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
														</td>
													@endif
												@endforeach
												</tr>
											</thead>

											<tr style="background: black; color: white">
												<td>Всего</td>												
												<td>{{$sumStatAll->requested}}</td>
												<td>{{$sumStatAll->played}}</td>
												<td>{{$sumStatAll->clicked}}</td>
												<td>{{$sumStatAll->poteri}}</td>
												<td>{{$sumStatAll->dosm}}</td>
												<td>{{$sumStatAll->util}}</td>
												<td>{{$sumStatAll->ctr}}</td>
											</tr>
											@foreach ($stats_all as $stat_all)
												<tr>
													<td>{{$stat_all->day}}</td>
													
													<td>{{$stat_all->requested}}</td>
													<td>{{$stat_all->played}}</td>
													<td>{{$stat_all->clicked}}</td>
													<td>{{$stat_all->poteri}}</td>
													<td>{{$stat_all->dosm}}</td>
													<td>{{$stat_all->util}}</td>
													<td>{{$stat_all->ctr}}</td>
												</tr>
											@endforeach

										</table>
										</div>
										<div class="tab-pane" id="all_ru">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													@foreach($header as $k=>$row)
														@if ($k<8 )
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>
													
													<td>{{$sumStatAll->requested_ru}}</td>
													<td>{{$sumStatAll->played_ru}}</td>
													<td>{{$sumStatAll->clicked_ru}}</td>
													<td>{{$sumStatAll->poteri_ru}}</td>
													<td>{{$sumStatAll->dosm_ru}}</td>
													<td>{{$sumStatAll->util_ru}}</td>
													<td>{{$sumStatAll->ctr_ru}}</td>
												</tr>
												@foreach ($stats_all as $stat_all)
													<tr>
														<td>{{$stat_all->day}}</td>
														
														<td>{{$stat_all->requested_ru}}</td>
														<td>{{$stat_all->played_ru}}</td>
														<td>{{$stat_all->clicked_ru}}</td>
														<td>{{$stat_all->poteri_ru}}</td>
														<td>{{$stat_all->dosm_ru}}</td>
														<td>{{$stat_all->util_ru}}</td>
														<td>{{$stat_all->ctr_ru}}</td>
													</tr>
												@endforeach
											</table>
										</div>
										<div class="tab-pane" id="all_cis">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													<td><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
													@foreach($header as $k=>$row)
														@if ($k!=0 and $k>7 and $k<15)
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>													
													<td>{{$sumStatAll->requested_cis}}</td>
													<td>{{$sumStatAll->played_cis}}</td>
													<td>{{$sumStatAll->clicked_cis}}</td>
													<td>{{$sumStatAll->poteri_cis}}</td>
													<td>{{$sumStatAll->dosm_cis}}</td>
													<td>{{$sumStatAll->util_cis}}</td>
													<td>{{$sumStatAll->ctr_cis}}</td>
												</tr>
												@foreach ($stats_all as $stat_all)
													<tr>
														<td>{{$stat_all->day}}</td>
														<td>{{$stat_all->requested_cis}}</td>
														<td>{{$stat_all->played_cis}}</td>
														<td>{{$stat_all->clicked_cis}}</td>
														<td>{{$stat_all->poteri_cis}}</td>
														<td>{{$stat_all->dosm_cis}}</td>
														<td>{{$stat_all->util_cis}}</td>
														<td>{{$stat_all->ctr_cis}}</td>
													</tr>
												@endforeach
											</table>
										</div>
									</div>
								</div>
							</div>
						
						
						
							<div class="tab-pane" id="desctop_stat" style="margin-top: 10px;">
								<div>
									<ul class="nav nav-tabs nav-justified cust-tabs">
										<li class="heading text-left active"><a href="#desctop_all" data-toggle="tab">Всего</a></li>
										<li class="heading text-left"><a href="#desctop_ru" data-toggle="tab">Россия</a></li>
										<li class="heading text-left"><a href="#desctop_cis" data-toggle="tab">СНГ</a></li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="desctop_all">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												<td><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
												@foreach($header as $k=>$row)
													@if ($k>14)
														<td>
															@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
														</td>
													@endif
												@endforeach
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>												
												<td>{{$sumStatDesc->requested}}</td>
												<td>{{$sumStatDesc->played}}</td>
												<td>{{$sumStatDesc->clicked}}</td>
												<td>{{$sumStatDesc->poteri}}</td>
												<td>{{$sumStatDesc->dosm}}</td>
												<td>{{$sumStatDesc->util}}</td>
												<td>{{$sumStatDesc->ctr}}</td>
											</tr>
											@foreach ($stats_desc as $stat_desc)
												<tr>
													<td>{{$stat_desc->day}}</td>
													
													<td>{{$stat_desc->requested}}</td>
													<td>{{$stat_desc->played}}</td>
													<td>{{$stat_desc->clicked}}</td>
													<td>{{$stat_desc->poteri}}</td>
													<td>{{$stat_desc->dosm}}</td>
													<td>{{$stat_desc->util}}</td>
													<td>{{$stat_desc->ctr}}</td>
												</tr>
											@endforeach
										</table>
										</div>
										<div class="tab-pane" id="desctop_ru">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													@foreach($header as $k=>$row)
														@if ($k<8 )
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>
													
													<td>{{$sumStatDesc->requested_ru}}</td>
													<td>{{$sumStatDesc->played_ru}}</td>
													<td>{{$sumStatDesc->clicked_ru}}</td>
													<td>{{$sumStatDesc->poteri_ru}}</td>
													<td>{{$sumStatDesc->dosm_ru}}</td>
													<td>{{$sumStatDesc->util_ru}}</td>
													<td>{{$sumStatDesc->ctr_ru}}</td>
												</tr>
												@foreach ($stats_desc as $stat_desc)
													<tr>
														<td>{{$stat_desc->day}}</td>
														
														<td>{{$stat_desc->requested_ru}}</td>
														<td>{{$stat_desc->played_ru}}</td>
														<td>{{$stat_desc->clicked_ru}}</td>
														<td>{{$stat_desc->poteri_ru}}</td>
														<td>{{$stat_desc->dosm_ru}}</td>
														<td>{{$stat_desc->util_ru}}</td>
														<td>{{$stat_desc->ctr_ru}}</td>
													</tr>
												@endforeach
											</table>
										</div>
										<div class="tab-pane" id="desctop_cis">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													<td><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
													@foreach($header as $k=>$row)
														@if ($k!=0 and $k>7 and $k<15)
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>													
													<td>{{$sumStatDesc->requested_cis}}</td>
													<td>{{$sumStatDesc->played_cis}}</td>
													<td>{{$sumStatDesc->clicked_cis}}</td>
													<td>{{$sumStatDesc->poteri_cis}}</td>
													<td>{{$sumStatDesc->dosm_cis}}</td>
													<td>{{$sumStatDesc->util_cis}}</td>
													<td>{{$sumStatDesc->ctr_cis}}</td>
												</tr>
												@foreach ($stats_desc as $stat_desc)
													<tr>
														<td>{{$stat_desc->day}}</td>
														<td>{{$stat_desc->requested_cis}}</td>
														<td>{{$stat_desc->played_cis}}</td>
														<td>{{$stat_desc->clicked_cis}}</td>
														<td>{{$stat_desc->poteri_cis}}</td>
														<td>{{$stat_desc->dosm_cis}}</td>
														<td>{{$stat_desc->util_cis}}</td>
														<td>{{$stat_desc->ctr_cis}}</td>
													</tr>
												@endforeach
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="mobile_stat" style="margin-top: 10px;">
								<div>
									<ul class="nav nav-tabs nav-justified cust-tabs">
										<li class="heading text-left active"><a href="#mobile_all" data-toggle="tab">Всего</a></li>
										<li class="heading text-left"><a href="#mobile_ru" data-toggle="tab">Россия</a></li>
										<li class="heading text-left"><a href="#mobile_cis" data-toggle="tab">СНГ</a></li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="mobile_all">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													<td><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
													@foreach($header as $k=>$row)
														@if ($k>14)
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>													
													<td>{{$sumStatMobile->requested}}</td>
													<td>{{$sumStatMobile->played}}</td>
													<td>{{$sumStatMobile->clicked}}</td>
													<td>{{$sumStatMobile->poteri}}</td>
													<td>{{$sumStatMobile->dosm}}</td>
													<td>{{$sumStatMobile->util}}</td>
													<td>{{$sumStatMobile->ctr}}</td>
												</tr>
												@foreach ($stats_mobile as $stat_mobile)
													<tr>
														<td>{{$stat_mobile->day}}</td>
														<td>{{$stat_mobile->requested}}</td>
														<td>{{$stat_mobile->played}}</td>
														<td>{{$stat_mobile->clicked}}</td>
														<td>{{$stat_mobile->poteri}}</td>
														<td>{{$stat_mobile->dosm}}</td>
														<td>{{$stat_mobile->util}}</td>
														<td>{{$stat_mobile->ctr}}</td>
													</tr>
												@endforeach
											</table>
										</div>
										<div class="tab-pane" id="mobile_ru">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													@foreach($header as $k=>$row)
														@if ($k<8)
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>
													
													<td>{{$sumStatMobile->requested_ru}}</td>
													<td>{{$sumStatMobile->played_ru}}</td>
													<td>{{$sumStatMobile->clicked_ru}}</td>
													<td>{{$sumStatMobile->poteri_ru}}</td>
													<td>{{$sumStatMobile->dosm_ru}}</td>
													<td>{{$sumStatMobile->util_ru}}</td>
													<td>{{$sumStatMobile->ctr_ru}}</td>
												</tr>
												@foreach ($stats_mobile as $stat_mobile)
													<tr>
														<td>{{$stat_mobile->day}}</td>
														<td>{{$stat_mobile->requested_ru}}</td>
														<td>{{$stat_mobile->played_ru}}</td>
														<td>{{$stat_mobile->clicked_ru}}</td>
														<td>{{$stat_mobile->poteri_ru}}</td>
														<td>{{$stat_mobile->dosm_ru}}</td>
														<td>{{$stat_mobile->util_ru}}</td>
														<td>{{$stat_mobile->ctr_ru}}</td>
													</tr>
												@endforeach
											</table>
										</div>
										<div class="tab-pane" id="mobile_cis">
											<table class="table table-hover table-bordered" style="margin-top: 10px">
												<thead>
													<tr>
													<td><a class="table_href" href="/{{$header[0]['url']}}">{{$header[0]['title']}}</a></td>
													@foreach($header as $k=>$row)
														@if ($k>7 and $k<15)
															<td>
																@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
															</td>
														@endif
													@endforeach
													</tr>
												</thead>
												<tr style="background: black; color: white">
													<td>Всего</td>
													<td>{{$sumStatMobile->requested_cis}}</td>
													<td>{{$sumStatMobile->played_cis}}</td>
													<td>{{$sumStatMobile->clicked_cis}}</td>
													<td>{{$sumStatMobile->poteri_cis}}</td>
													<td>{{$sumStatMobile->dosm_cis}}</td>
													<td>{{$sumStatMobile->util_cis}}</td>
													<td>{{$sumStatMobile->ctr_cis}}</td>
												</tr>
												@foreach ($stats_mobile as $stat_mobile)
													<tr>
														<td>{{$stat_mobile->day}}</td>
														<td>{{$stat_mobile->requested_cis}}</td>
														<td>{{$stat_mobile->played_cis}}</td>
														<td>{{$stat_mobile->clicked_cis}}</td>
														<td>{{$stat_mobile->poteri_cis}}</td>
														<td>{{$stat_mobile->dosm_cis}}</td>
														<td>{{$stat_mobile->util_cis}}</td>
														<td>{{$stat_mobile->ctr_cis}}</td>
													</tr>
												@endforeach
											</table>
										</div>
									</div>
								</div>
							</div>
						</div> 
					</div>
					{!! $stats_all->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
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