@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center"></h4>
		<div class="col-xs-12">
			<h4 class="text-center">Список всех рефералов</h4>
			<div>
				{!! $refers->render() !!}
				<table class="table table-hover table-bordered" style="margin-top: 10px">
					<thead>
						<tr>
							<td>Имя</td>
							<td>id_рефер</td>
							<td>Email</td>
							<td>Менеджер</td>
							<td>Рефералы</td>
							<td>Сумма</td>
						</tr>
					</thead>
					<tbody>
						@foreach($refers as $refer)
							<!--{{$user=\App\UserProfile::where('user_id', $refer->user_id)->first()}}-->
							<tr>
								<td><a href="{{route('admin.home', ['id_user'=>$user->user_id])}}" target="_blank">{{$user->name}}</a></td>
								<td>{{$user->refer_id}}</td>
								<td>{{$user->email}}</td>
								<td>
								@if ($user->manager)
									<!--{{$manager=\App\UserProfile::where('user_id', $user->manager)->first()}}-->
									@if ($manager)
										<a href="{{route('admin.home', ['id_user'=>$manager->user_id])}}">{{$manager->name}}</a>
									@endif
								@endif
								</td>
								<td>
								<!-- {{$clients=\App\UserProfile::where('referer', $refer->user_id)->orderBy('created_at', 'desc')->get()}} -->
								<span class="detail_com" style="cursor: pointer; color: #5757ff; font-weight: bold;"
									data-container="body" data-toggle="popover" data-html="true" tabindex="0" data-placement="bottom" data-content="
									@foreach ($clients as $client)
										<b style='float:left'>{{date('Y-m-d',strtotime($client->created_at))}}</b> <a href='{{route('admin.home', ['id'=>$client->user_id])}}' target='_blank' style='float:right'>{{$client->name}}</a><br>
									@endforeach
								">Подробнее
								</span>
								</td>
								<td>{{$refer->summa}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			{!! $refers->render() !!}
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
		.detail_com:focus, detail_com:active{
			outline: none!important;
		}
		.popover{
			width: 400px!important;
			max-width: 400px!important;
		}
		#app{
			margin-bottom: 220px!important;
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