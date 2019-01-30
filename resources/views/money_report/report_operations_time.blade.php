@extends('layouts.app')

@section('content')
<div class="container">
	@include('money_report.menu')
	<div class="row">
		<h3 class="text-center">Операции в период с {{$from}} по {{$to}}</h3>
		<div class="col-xs-12" style="margin: 5px 0;">
			<form class="form-inline" role="form" method="get">
				<div class="row">
					<div class="input-group col-xs-2 form-group">
						<select name="type" class="form-control">
							<option value="0">Все</option>
							<option @if ($type==1) selected @endif value="1">Входящие</option>
							<option @if ($type==2) selected @endif value="2">Исходящие</option>
						</select>
					</div>
					<div class="input-group col-xs-2 form-group">
						<select name="shortcode" class="form-control">
							<option value="0">Все</option>
							@foreach ($typesMenu as $menu)
								<option @if ($shortcode==$menu->shortcode) selected @endif value="{{$menu->shortcode}}">{{$menu->title}}</option>
							@endforeach
							<option @if ($shortcode=='rbtobm') selected @endif value="rbtobm">Робот и обменник</option>
						</select>
					</div>
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">С:</span>
						<input type="text" class="form-control" value="{{$from}}" name="from">
					</div>
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">По:</span>
						<input type="text" class="form-control" value="{{$to}}" name="to">
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
		<div class="col-xs-12">
			По состоянию курса на {{$spec_cources->date}} | USD={{$spec_cources->usd}} | BTC={{$spec_cources->btc}}
		</div>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td>Тип</td>
					<td>Сумма</td>
					@if ($shortcode=='obm')
					<td>Сумма руб.</td>
					<td>Сумма btc.</td>
					@endif
					<td>Детально</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($sum->summa)}}</td>
					@if ($shortcode=='obm')
					<td>{{round($launch['summa_opened'])}}</td>
					<td>{{round($launch['opened_btc'],8)}}</td>
					@endif
					<td></td>
				</tr>
				@foreach ($operations as $operation)
					<tr style="@if ($operation->type==1) background: rgba(0, 204, 0, 0.15); @elseif ($operation->type==2) background: rgba(255, 0, 35, 0.15); @endif">
						<td class="text-left">
							@foreach ($types as $type)
								@if($type->type==$operation->type and $type->shortcode==$operation->shortcode)
									<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="
										<table>
											
										@foreach ($cachs as $cach)
											@if ($cach->shortcode==$operation->shortcode and $cach->type==$operation->type)
												<tr style='border-bottom: 1px solid #fff'>
												<td>{{$cach->title}} {{$cach->valut}}</td>
												<td>@if ($cach->type==1)+@else-@endif{{$cach->summa}}</td>
												</tr>
											@endif
										@endforeach
											</tr>
										</table>
									">
									{{$type->title}}
									</span>
								@endif
							@endforeach
						</td>
						<td>{{round($operation->summa)}}</td>
						@if ($shortcode=='obm')
						<td></td>
						<td></td>
						@endif
						<td>
							@if ($operation->shortcode=='obm' or $operation->shortcode=='rbt')
							<a href="{{route('money.report.time.detal', ['from'=>$from, 'to'=>$to, 'shortcode'=>'rbtobm'])}}">Детально</a>
							@else
								<a href="{{route('money.report.time.detal', ['from'=>$from, 'to'=>$to, 'shortcode'=>$operation->shortcode])}}">Детально</a>
							@endif
						</td>
					</tr>
				@endforeach
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($sum->summa)}}</td>
					@if ($shortcode=='obm')
					<td>{{round($launch['summa_closed'])}}</td>
					<td>{{round($launch['closed_btc'],8)}}</td>
					@endif
					<td></td>
				</tr>
				@if ($shortcode=='obm')
				<tr style="background: #000; color: #fff">
					<td>Разница</td>
					<td></td>
					@if ($shortcode=='obm')
					<td>{{round($launch['summa_closed']-$launch['summa_opened'])}}</td>
					<td>{{round($launch['closed_btc']-$launch['opened_btc'],8)}}</td>
					@endif
					<td></td>
				</tr>
				@endif
			</tbody>
		</table>
    </div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	<style>
		.table>thead>tr>td, .table>tbody>tr>td{
			vertical-align: middle;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip({html:true})
		})
	</script>
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
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
