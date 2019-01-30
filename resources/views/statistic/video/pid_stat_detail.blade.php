@extends('layouts.app')

@section('content')
@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
	<!--{{$admin=1}}-->
@else
	<!--{{$admin=0}}-->
@endif
<div class="container">
	<div class="row">
		<div class="col-xs-12">
		<div class="row">
				<div class="col-xs-8">
					<form class="form-inline" role="form" method="get">
						<div class="row">
							<div class="input-group col-xs-3 form-group">
								<span class="input-group-addon">С:</span>
								<input type="text" class="form-control" value="{{$from}}" name="from">
							</div>
							<div class="input-group col-xs-3 form-group">
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
						<span class="">Здесь параметр по которому выделять строку: </span><span class="right_pok">%</span>
					</div>
					<div>
						<span class="label label-danger celi_pok"></span><span class="right_pok">Ниже %</span>
					</div>
				</div>
			</div>
			<div class="row">
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<a href="{{route('admin.home', ['user_id'=>$user->id])}}" style="font-weight: bold">{{$user->name}}</a>
					<a href="{{route('video_statistic.pid_pad', ['id'=>$pid_stat_sum_all->id])}}" style="float: right; font-weight: bold">Разбивка по ссылкам</a>
				@endif
				{{--@if ($pid_stat_sum_all->id==1461 or $pid_stat_sum_all->id==1485 or $pid_stat_sum_all->id==1513 or $pid_stat_sum_all->id==1517 or $pid_stat_sum_all->id==1597 or $pid_stat_sum_all->id==752  or $pid_stat_sum_all->id==1664  or $pid_stat_sum_all->id==1665)--}}
				@if(1==1)
					<br>
					<a href="{{route('video_statistic_pid.domain_stat_datal', ['id'=>$pid_stat_sum_all->id])}}" style="font-weight: bold">По доменам</a>
				@endif
				<h4 class="text-center">Статистика по виджету №{{$pid_stat_sum_all->id}} {{$pid_stat_sum_all->domain}}</h4>
				<div class="col-xs-12">
					<div>
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
											@if ($admin==1)
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
											@else
												@foreach($header as $k=>$row)
													@if ($k==0 or $k==3 or $k==8 or $k==9 or $k==12)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">@if ($k==3) Показы @else {{$row['title']}} @endif</a>@endif
													</td>
													@endif
												@endforeach
											@endif
										</tr>
									</thead>
									@php
									if(($user->id==10818 && $pid_stat_sum_all->calculate)){
		
		$pid_stat_sum_all->calculate+=$pid_stat_sum_all->second;
									}
@endphp
									<tr style="background: black; color: white">
										<td>Всего</td>
										@if ($admin==1)<td>{{$pid_stat_sum_all->loaded}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_all->played}}</td>@endif
										<td>{{$pid_stat_sum_all->calculate}}</td>
										@if ($admin==1)<td>{{$pid_stat_sum_all->deep}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_all->util}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_all->dosm}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_all->clicks}}</td>@endif
										<td>{{$pid_stat_sum_all->ctr}}</td>
										<td>
										@if ($pid_stat_sum_all->lease_summa>0)
											@if ($admin!=1)
											@else
												{{$pid_stat_sum_all->summa}}
											@endif
										@else
											{{$pid_stat_sum_all->summa}}
										@endif
										</td>
										@if ($admin==1)<td>{{$pid_stat_sum_all->second}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_all->second_all}}</td>@endif
										<td>
										@if ($pid_stat_sum_all->lease_summa>0)
											@if ($admin!=1)
											@else
												{{$pid_stat_sum_all->second_summa}}
											@endif
										@else
											{{$pid_stat_sum_all->second_summa}}
										@endif
										</td>
										@if ($admin==1)<td>{{$pid_stat_sum_all->coef}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_all->viewable}}</td>@endif
									</tr>
									@foreach ($pid_stat_alls as $pid_stat_all)
								@php
								if(($user->id==10818 && $pid_stat_all->calculate)){
		                         $pid_stat_all->calculate+=$pid_stat_all->second;
								}
                                @endphp
										<tr>
											<td>{{$pid_stat_all->day}}</td>
											@if ($admin==1)<td>{{$pid_stat_all->loaded}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_all->played}}</td>@endif
											<td>{{$pid_stat_all->calculate}}</td>
											@if ($admin==1)<td>{{$pid_stat_all->deep}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_all->util}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_all->dosm}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_all->clicks}}</td>@endif
											<td>{{$pid_stat_all->ctr}}</td>
											<td>
											@if ($pid_stat_all->lease_summa>0)
												@if ($admin!=1)
												@else
													{{$pid_stat_all->summa}}
												@endif
											@else
												{{$pid_stat_all->summa}}
											@endif
											</td>
											@if ($admin==1)<td>{{$pid_stat_all->second}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_all->second_all}}</td>@endif
											<td>
											@if ($pid_stat_all->lease_summa>0)
												@if ($admin!=1)
												@else
													{{$pid_stat_all->second_summa}}
												@endif
											@else
												{{$pid_stat_all->second_summa}}
											@endif
											</td>
											@if ($admin==1)<td>{{$pid_stat_all->coef}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_all->viewable}}</td>@endif
										</tr>
									@endforeach
								</table>
							</div>
							
							<div class="tab-pane" id="rus_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@if ($admin==1)
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
											@else
												@foreach($header as $k=>$row)
													@if ($k==0 or $k==3 or $k==8 or $k==9 or $k==12)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">@if ($k==3) Показы @else {{$row['title']}} @endif</a>@endif
													</td>
													@endif
												@endforeach
											@endif
										</tr>
									</thead>
																										@php
								if(($user->id==10818 && $pid_stat_sum_ru->calculate)){
		                         $pid_stat_sum_ru->calculate+=$pid_stat_sum_ru->second;
								}
                                @endphp
									<tr style="background: black; color: white">
										<td>Всего</td>
										@if ($admin==1)<td>{{$pid_stat_sum_ru->loaded}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_ru->played}}</td>@endif
										<td>{{$pid_stat_sum_ru->calculate}}</td>
										@if ($admin==1)<td>{{$pid_stat_sum_ru->deep}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_ru->util}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_ru->dosm}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_ru->clicks}}</td>@endif
										<td>{{$pid_stat_sum_ru->ctr}}</td>
										<td>
										@if ($pid_stat_sum_ru->lease_summa>0)
											@if ($admin!=1)
											@else
												{{$pid_stat_sum_ru->summa}}
											@endif
										@else
											{{$pid_stat_sum_ru->summa}}
										@endif
										</td>
										@if ($admin==1)<td>{{$pid_stat_sum_ru->second}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_ru->second_all}}</td>@endif
										<td>
										@if ($pid_stat_sum_ru->lease_summa>0)
											@if ($admin!=1)
											@else
												{{$pid_stat_sum_ru->second_summa}}
											@endif
										@else
											{{$pid_stat_sum_ru->second_summa}}
										@endif
										</td>
										@if ($admin==1)<td>{{$pid_stat_sum_ru->coef}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_ru->viewable}}</td>@endif
									</tr>
									@foreach ($pid_stat_rus as $pid_stat_ru)
								@php
								if(($user->id==10818 && $pid_stat_ru->calculate)){
		                         $pid_stat_ru->calculate+=$pid_stat_ru->second;
								}
                                @endphp
										<tr>
											<td>{{$pid_stat_ru->day}}</td>
											@if ($admin==1)<td>{{$pid_stat_ru->loaded}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_ru->played}}</td>@endif
											<td>{{$pid_stat_ru->calculate}}</td>
											@if ($admin==1)<td>{{$pid_stat_ru->deep}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_ru->util}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_ru->dosm}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_ru->clicks}}</td>@endif
											<td>{{$pid_stat_ru->ctr}}</td>
											<td>
											@if ($pid_stat_ru->lease_summa>0)
												@if ($admin!=1)
												@else
													{{$pid_stat_ru->summa}}
												@endif
											@else
												{{$pid_stat_ru->summa}}
											@endif
											</td>
											@if ($admin==1)<td>{{$pid_stat_ru->second}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_ru->second_all}}</td>@endif
											<td>
											@if ($pid_stat_ru->lease_summa>0)
												@if ($admin!=1)
												@else
													{{$pid_stat_ru->second_summa}}
												@endif
											@else
												{{$pid_stat_ru->second_summa}}
											@endif
											</td>
											@if ($admin==1)<td>{{$pid_stat_ru->coef}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_ru->viewable}}</td>@endif
										</tr>
									@endforeach
								</table>
							</div>
							
							<div class="tab-pane" id="cis_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											@if ($admin==1)
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
											@else
												@foreach($header as $k=>$row)
													@if ($k==0 or $k==3 or $k==8 or $k==9 or $k==12)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">@if ($k==3) Показы @else {{$row['title']}} @endif</a>@endif
													</td>
													@endif
												@endforeach
											@endif
										</tr>
									</thead>
																	@php
								if(($user->id==10818 && $pid_stat_sum_cis->calculate)){
		                         $pid_stat_sum_cis->calculate+=$pid_stat_sum_cis->second;
								}
                                @endphp 
									<tr style="background: black; color: white">
										<td>Всего</td>
										@if ($admin==1)<td>{{$pid_stat_sum_cis->loaded}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_cis->played}}</td>@endif
										<td>{{$pid_stat_sum_cis->calculate}}</td>
										@if ($admin==1)<td>{{$pid_stat_sum_cis->deep}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_cis->util}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_cis->dosm}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_cis->clicks}}</td>@endif
										<td>{{$pid_stat_sum_cis->ctr}}</td>
										<td>
										@if ($pid_stat_sum_cis->lease_summa>0)
											@if ($admin!=1)
											@else
											{{$pid_stat_sum_cis->summa}}
										@endif
											@else
											{{$pid_stat_sum_cis->summa}}
										@endif
										</td>
										@if ($admin==1)<td>{{$pid_stat_sum_cis->second}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_cis->second_all}}</td>@endif
										<td>
										@if ($pid_stat_sum_cis->lease_summa>0)
											@if ($admin!=1)
											@else
											{{$pid_stat_sum_cis->summa}}
										@endif
											@else
											{{$pid_stat_sum_cis->second_summa}}
										@endif
										</td>
										@if ($admin==1)<td>{{$pid_stat_sum_cis->coef}}</td>@endif
										@if ($admin==1)<td>{{$pid_stat_sum_cis->viewable}}</td>@endif
									</tr>
									@foreach ($pid_stat_ciss as $pid_stat_cis)
								@php
								if(($user->id==10818 && $pid_stat_cis->calculate)){
		                         $pid_stat_cis->calculate+=$pid_stat_cis->second;
								}
                                @endphp
										<tr>
											<td>{{$pid_stat_cis->day}}</td>
											@if ($admin==1)<td>{{$pid_stat_cis->loaded}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_cis->played}}</td>@endif
											<td>{{$pid_stat_cis->calculate}}</td>
											@if ($admin==1)<td>{{$pid_stat_cis->deep}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_cis->util}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_cis->dosm}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_cis->clicks}}</td>@endif
											<td>{{$pid_stat_cis->ctr}}</td>
											<td>
											@if ($pid_stat_cis->lease_summa>0)
												@if ($admin!=1)
												@else
												{{$pid_stat_cis->summa}}
											@endif
												@else
												{{$pid_stat_cis->summa}}
											@endif
											</td>
											@if ($admin==1)<td>{{$pid_stat_cis->second}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_cis->second_all}}</td>@endif
											<td>
											@if ($pid_stat_cis->lease_summa>0)
												@if ($admin!=1)
												@else
												{{$pid_stat_cis->second_summa}}
											@endif
												@else
												{{$pid_stat_cis->second_summa}}
											@endif
											</td>
											@if ($admin==1)<td>{{$pid_stat_cis->coef}}</td>@endif
											@if ($admin==1)<td>{{$pid_stat_cis->viewable}}</td>@endif
										</tr>
									@endforeach
								</table>
							</div>
						</div>
						{!! $pid_stat_alls->appends(['from'=>$from, 'to'=>$to])->render() !!}
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