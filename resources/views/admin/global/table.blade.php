@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-xs-12">
		<div class="row">
				<div class="col-xs-8">
					<form class="form-inline" role="form" method="get">
						<div class="row">
							<div class="input-group col-xs-4 form-group">
								<span class="input-group-addon">Поиск:</span>
								<input type="text" class="form-control" value="{{$search}}" name="search">
							</div>
							<div class="input-group col-xs-4 form-group">
								<select class="form-control" name="category">
									<option value='0'>Вернуть как было</option>
									<option @if ($category==1) selected @endif value="1">Товарка</option>
									<option @if ($category==2) selected @endif value="2">Видео</option>
									<option @if ($category=='all') selected @endif value="all">Всё</option>
								</select>
							</div>
							<div class="col-xs-2 input-group form-group">
								<button type="submit" class="btn btn-primary">Применить</button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-xs-4 text-right">

				</div>
			</div>
			<div class="row">
				<h4 class="text-center"></h4>
				<div class="col-xs-12">
					<div class="affiliate_cabinet_block text-center users_cabinet_block">
						{!! $globals->appends(["search"=>$search])->render() !!}
						<table class="table table-hover table-bordered" style="margin-top: 10px">
							<thead>
								<tr>
									<td>ID юзера</td>
									<td>Имя</td>
									<td>Email</td>
									<td>Площадки</td>
									<td>Менеджер</td>
									<td>Статус</td>
									<td>Доп. статус</td>
								</tr>
							</thead>
							<tbody>
								@foreach ($globals as $global)
									<tr>
										<td>{{$global->user_id}}</td>
										<td>{{$global->name}}
										@if ($global->vip==1)
											<span style="color: rgb(181, 0, 0)"> VIP</span>
										@endif
										</td>
										<td>{{$global->email}}</td>
										<td>{{$global->domain}}</td>
										<td>
										@if ($global->manager)
											{{\App\UserProfile::where('user_id', $global->manager)->first()->name}}
										@endif
										</td>
										<td>
										@if ($global->status==0)
										Активный
										@elseif ($global->status==1)
										Не активный
										@endif
										</td>
										<td>
										@if ($global->dop_status==1)
											<img src="/images/smail/green.png" data-toggle="tooltip" data-placement="bottom" title="{{$global->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
										@elseif ($global->dop_status==2)
											<img src="/images/smail/yellow.png" data-toggle="tooltip" data-placement="bottom" title="{{$global->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
										@elseif ($global->dop_status==3)
											<img src="/images/smail/red.png" data-toggle="tooltip" data-placement="bottom" title="{{$global->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
										@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! $globals->appends(["search"=>$search])->render() !!}
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