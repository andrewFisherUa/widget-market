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
					<td>Система / счет</td>
					<td>Время</td>
					<td>Тип</td>
					<td>Сумма</td>
					<td>Комментарий</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($operations as $operation)
					<tr style="@if ($operation->type==1) background: rgba(0, 204, 0, 0.15); @elseif ($operation->type==2) background: rgba(255, 0, 35, 0.15); @endif">
						<td>{{$operation->account->title}}</td>
						<td>{{$operation->datetime}}</td>
						<td class="text-left">
							@foreach ($types as $type)
								@if($type->type==$operation->type and $type->shortcode==$operation->shortcode)
									@if($operation->obnal)<b>Обнал</b>@endif {{$type->title}}
								@endif
							@endforeach
						</td>
						<td>{{round($operation->summa,8)}}</td>
						<td>{{$operation->comment}}</td>
					</tr>
				@endforeach
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
