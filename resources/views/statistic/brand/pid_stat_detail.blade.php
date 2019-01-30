@extends('layouts.app')

@section('content')
@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
	<!--{{$admin=1}}-->
@else
	<!--{{$admin=0}}-->
@endif
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-xs-12">
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
				@endif
				<h4 class="text-center">Статистика по виджету № {{$widget->id}} {{$widget->partnerPad->domain}}</h4>
				<div class="col-xs-12">
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
											<td>Дата</td>
											<td>Показы</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>Уникальные показы</td>
											@endif
											<td>Клики</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>Уникальные клики</td>
											@endif
											<td>Ctr</td>
											<td>Cpm</td>
											<td>Сумма</td>
										</tr>
									</thead>
									<tr style="background: black; color: white">
										<td>Всего</td>
										<td>{{$statsAll->showed}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$statsAll->unik_showed}}</td>
										@endif
										<td>{{$statsAll->click}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$statsAll->unik_cliсk}}</td>
										@endif
										<td>{{$statsAll->ctr}}</td>
										<td>{{$statsAll->cpm}}</td>
										<td>{{$statsAll->summa}}</td>
									</tr>
									@foreach ($stats as $stat)
										<tr>
											<td>{{$stat->day}}</td>
											<td>{{$stat->showed_ru+$stat->showed_cis}}</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>{{$stat->unik_showed_ru+$stat->unik_showed_cis}}</td>
											@endif
											<td>{{$stat->click_ru+$stat->click_cis}}</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>{{$stat->unik_click_ru+$stat->unik_click_cis}}</td>
											@endif
											<td>{{$stat->ctr}}</td>
											<td>{{$stat->cpm}}</td>
											<td>{{$stat->summa_ru+$stat->summa_cis}}</td>
										</tr>
									@endforeach
								</table>
							</div>
							
							<div class="tab-pane" id="rus_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											<td>Дата</td>
											<td>Показы</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>Уникальные показы</td>
											@endif
											<td>Клики</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>Уникальные клики</td>
											@endif
											<td>Ctr</td>
											<td>Cpm</td>
											<td>Сумма</td>
										</tr>
									</thead>
									<tr style="background: black; color: white">
										<td>Всего</td>
										<td>{{$statsRus->showed}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$statsRus->unik_showed}}</td>
										@endif
										<td>{{$statsRus->click}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$statsRus->unik_cliсk}}</td>
										@endif
										<td>{{$statsRus->ctr}}</td>
										<td>{{$statsRus->cpm}}</td>
										<td>{{$statsRus->summa}}</td>
									</tr>
									@foreach ($stats as $stat)
										<tr>
											<td>{{$stat->day}}</td>
											<td>{{$stat->showed_ru}}</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>{{$stat->unik_showed_ru}}</td>
											@endif
											<td>{{$stat->click_ru}}</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>{{$stat->unik_click_ru}}</td>
											@endif
											<td>{{$stat->ctr_ru}}</td>
											<td>{{$stat->cpm_ru}}</td>
											<td>{{$stat->summa_ru}}</td>
										</tr>
									@endforeach
								</table>
							</div>
							
							<div class="tab-pane" id="cis_stat">
								<table class="table table-hover table-bordered" style="margin-top: 10px">
									<thead>
										<tr>
											<td>Дата</td>
											<td>Показы</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>Уникальные показы</td>
											@endif
											<td>Клики</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>Уникальные клики</td>
											@endif
											<td>Ctr</td>
											<td>Cpm</td>
											<td>Сумма</td>
										</tr>
									</thead>
									<tr style="background: black; color: white">
										<td>Всего</td>
										<td>{{$statsCis->showed}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$statsCis->unik_showed}}</td>
										@endif
										<td>{{$statsCis->click}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td>{{$statsCis->unik_cliсk}}</td>
										@endif
										<td>{{$statsCis->ctr}}</td>
										<td>{{$statsCis->cpm}}</td>
										<td>{{$statsCis->summa}}</td>
									</tr>
									@foreach ($stats as $stat)
										<tr>
											<td>{{$stat->day}}</td>
											<td>{{$stat->showed_cis}}</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>{{$stat->unik_showed_cis}}</td>
											@endif
											<td>{{$stat->click_cis}}</td>
											@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td>{{$stat->unik_click_cis}}</td>
											@endif
											<td>{{$stat->ctr_cis}}</td>
											<td>{{$stat->cpm_cis}}</td>
											<td>{{$stat->summa_cis}}</td>
										</tr>
									@endforeach
								</table>
								
							</div>
							{!! $stats->appends(['from'=>$from, 'to'=>$to])->render() !!}
						</div>
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