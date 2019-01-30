@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-xs-12">
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
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">Количетсво:</span>
						<input type="text" class="form-control" value="{{$number}}" name="number">
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row" style="margin: 10px 0;">
		{!! $stats->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
		<table class="table table-hover table-bordered" style="margin-top: 10px; table-layout: fixed;">
			<tr>
				@foreach($header as $k=>$row)
					<td>
						@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
					</td>
				@endforeach
			</tr>
			<tbody>
				@foreach ($stats as $stat)
				<tr>
					<td>{{$stat->pid}}</td>
					<td>{{$stat->date}}</td>
					<td style="word-wrap: break-word;">{{$stat->url}}</td>
					<td style="word-wrap: break-word;">{{$stat->referrer}}</td>
					<td style="word-wrap: break-word;">
						@if ($stat->origins=='0')
							@if ($stat->referrer!=1)
								&#8594;{{parse_url($stat->referrer, PHP_URL_HOST)}}<br>
								&#8594;{{parse_url($stat->url, PHP_URL_HOST)}}
							@else
								Не удалось определить
							@endif
							@else
								<!--{{preg_match_all('#"http([^"]+)"#i', $stat->origins, $matches)}}-->
							@php $elem=array("s://", "://") @endphp
								@foreach ($matches[1] as $mat)
									@php $q=str_replace($elem, "", $mat) @endphp
									&#8594;{{$q}}<br>
								@endforeach
								&#8594;{{parse_url($stat->url, PHP_URL_HOST)}}
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		{!! $stats->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
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