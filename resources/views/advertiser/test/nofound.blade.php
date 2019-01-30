@extends('layouts.app')
@push('cabinet_home_top')
<style>
.table > thead > tr > th {
	white-space: nowrap;
}

.model-image-container{
	width: 100px;
    height: 100px;
    padding: 0 17px 0 0;
    margin: 4px 0 0;
    flex-grow: 1;
    align-self: flex-start;
	display:block;
}
img.model-image {
    max-width: 90%;
    max-height: 100%;
    margin: 0;
    vertical-align: middle;
    border: 0;
}

</style>
@endpush
@section('content')
<div class="container">


<div class="row">

</div>
{{--
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
--}}
	    <div class="row" style="margin-bottom:25px">
		       <div class="text-center"><h4 >Результат для страницы</h4>
			   <a href="{{$pageurl}}" target="_blank">{{$pageurl}}</a>
			   </div>
	    </div>
	   <div class="row">
	    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<div class="panel-left-padding">
		{{--
		<div>
		<a href="{{route('advertiser.site_statistic')}}">Cтатистика всех площадок наша</a>
		</div> 

		<div>
		<a href="{{route('advertiser.yandex_statistic')}}">Cтатистика всех площадок яндекс</a>
		</div> 
		<div>
		<a href="{{route('advertiser.yandex_statistic')}}">Вернуться на статистику по сайту</a>
		</div>
        --}} 
		
		 @widget('ApiDebagger',$debag) 
		{{--
		<div>
		<a href="{{route('advertiser.warnings_statistic')}}">Оповещения системы</a>@if($cnt_new_warning) новых <span class="badge">{{$cnt_new_warning}} </span> @endif
		</div>
		--}}
		</div>
		

		
        </div>
		
   	    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	    <div class ="container">

       
				<div style="margin-bottom:25px">
		         <form method="GET" action="" accept-charset="UTF-8">
				@if(Request('count'))
				<input name="count" type="hidden" value ="{{Request('count')}}">	
				@endif	
				@if(Request('pad'))
				<input name="pad" type="hidden" value ="{{Request('pad')}}">	
				@endif	
				@if(Request('t'))
				<input name="t" type="hidden" value ="{{Request('t')}}">	
				@endif	
				@if(Request('dpu'))
				<input name="dpu" type="hidden" value ="{{Request('dpu')}}">	
				@endif	
				
			<div class="row">
                <div class="col-md-8">
				    <div class="form-group">
					    <div class="col-md-12">
					        <input name="name" class="form-control" type="text" value ="{{Request('name')}}">
					    </div>
				    </div>
		         </div> 
				 
				 <div class="col-md-2">
					    <input value="Искать" class="btn btn-primary btn-md" type="submit">
                 </div>
              </div>
             </form>
		</div>
		 </div>
Ничего не найдено
		
		
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