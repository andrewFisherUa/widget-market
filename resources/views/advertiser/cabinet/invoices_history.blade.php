@extends('layouts.app')
@section('content')

<div class="container">
@role(['admin','super_manager','manager'])
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',['id_user'=>$user->id])}}" class="btn btn-success">Страница пользователя</a>
<a href="{{ route('admin.balance_history',['id_user'=>$user->id,'shop_id'=>0])}}" class="btn btn-primary">Взаиморасчёты пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$user->id,'shop_id'=>0])}}" class="btn btn-primary">Статистика всех рекламных компаний пользователя</a>
<a href="{{ route('admin.disco',['id_user'=>$user->id])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@endrole

    <div class="row">
		@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{{ session('message_danger') }}
		</div>
	@endif
	<div class="row" style="margin-top: 30px;">
	<div class="row">
<div class="col-xs-10 form-group">  
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
			<div class="col-xs-2 form-group">  
				статус
			</div>
		    <div class="input-group col-xs-4 form-group">
			
				<span class="input-group-addon">Поиск:</span>
				<input type="text" class="form-control" value="{{Request('name')}}" name="name">
			</div>
			<div class="col-xs-1 input-group form-group">
				<button type="submit" class="btn btn-primary">Применить</button>
			</div>
		</div>
	</form>
	</div>
	  <div class="col-xs-2">
			<a href="#" data-toggle="modal" data-target="#advertiser_payout" class="btn btn-success" role="button">Пополнить баланс</a></p>
			</div>
		</div>	
		
			@include('advertiser.payouts.modal_payout',['id_user'=>$user->id])
@if(isset($admin))
@widget('UserInvoices',["user"=>$user,"admin"=>1])
@else
@widget('UserInvoices',["user"=>$user])
@endif
       </div>	
	</div>
</div>	
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
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