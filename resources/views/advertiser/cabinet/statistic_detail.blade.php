@extends('layouts.app')
@section('content')
@push('cabinet_home_top')
<style>
.table > thead > tr > th {
	white-space: nowrap;
	
	
}
</style>
@endpush
<div class="container">
@widget('AdvertTop',[])
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
			<div class="input-group col-xs-4 form-group">
				
				<input type="text" class="form-control" value="{{Request('name')}}" name="name">
			</div>
			<div class="col-xs-1 input-group form-group">
				<button type="submit" class="btn btn-primary">Применить</button>
			</div>
		</div>
	</form>
    <div class="row">
	<div class="text-center"><h4 >{!! $title !!}</h4></div>
	{{-- <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">статистика компании</div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"><b>Всех</b></div>
	--}}
	</div>
	   <div class="row">
	    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		@widget('AdvertLeft',["key"=>3])
        </div>
		
   	    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
		
		
		@widget('Mystatisticdetail',["company"=>$company,"mod"=>$mod]) 
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