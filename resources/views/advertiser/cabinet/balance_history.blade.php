@extends('layouts.app')
@section('content')
<div class="container">

@widget('AdvertTop',[])
@role(['admin','super_manager','manager'])
@if(isset($owner))
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',['id_user'=>$owner->id])}}" class="btn btn-success">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',['id_user'=>$owner->id])}}" class="btn btn-primary">Счета пользователя</a>


<a href="{{ route('admin.statistic',['id_user'=>$owner->id,'shop_id'=>0])}}" class="btn btn-primary">Статистика всех рекламных компаний пользователя</a>
</div>
@endif
@endrole
	<form class="form-inline" role="form" method="get">
	@if(Request("sort"))
	<input type="hidden" value="{{Request('sort')}}" name="sort">
	@endif	
	@if(Request("order"))
	<input type="hidden" value="{{Request('order')}}" name="order">
	@endif	

		<div class="row">
			<div class="input-group col-xs-2 form-group">
				<span class="input-group-addon">С:</span>
				<input type="text" class="form-control" value="{{$from}}" name="from">
			</div>
			<div class="input-group col-xs-2 form-group">
				<span class="input-group-addon">По:</span>
				<input type="text" class="form-control" value="{{$to}}" name="to">
			</div>
			<div class="col-xs-1 input-group form-group">
				<button type="submit" class="btn btn-primary">Применить</button>
			</div>
		</div>
	</form>
    <div class="row">
	<div class="text-center"><h4>{!! $title !!}</h4></div>
	</div>
	   <div class="row">
	    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
	    @widget('AdvertLeft',["key"=>4]) 
        </div>
		
   	    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
		
		
         @widget('MybalanceHistory',["company"=>$company,"mod"=>$mod]) 
	    </div>

    </div>

</div>
@include('advertiser.payouts.modal_payout')
@endsection
@push('cabinet_home')
<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
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