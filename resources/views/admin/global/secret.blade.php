@extends('layouts.app')

@section('content')


<div class="container">
	<div class="row">
		<div class="col-xs-12" style="margin: 10px 0;">
			<form class="form-inline" role="form" method="get" action="">
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" id="from_for_users" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" id="to_for_users" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="input-group col-xs-3 form-group">
					<span class="input-group-addon">Поиск:</span>
					<input type="text" class="form-control" id="search_clent" value="{{$search}}" name="search">
				</div>
				<div class="input-group col-xs-1 form-group">
					<select name="number" id="number_for_client" class="form-control">
						<option @if ($number==5) selected @endif value="5">5</option>
						<option @if ($number==10) selected @endif value="10">10</option>
						<option @if ($number==15) selected @endif value="15">15</option>
						<option @if ($number==20) selected @endif value="20">20</option>
						<option @if ($number==30) selected @endif value="30">30</option>
						<option @if ($number==50) selected @endif value="50">50</option>
						<option @if ($number==100) selected @endif value="100">100</option>
					</select>
				</div>
				<div class="col-xs-1 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
			</form>
		</div>
		{!! $stats->appends(["direct"=>$direct, "order"=>$order])->render() !!}
		<table id="video_is" class="table table-condensed table-hover widget-table" style="table-layout: fixed;">
			<thead>
				<colgroup>
					<col span="1" style="width: 28px">
					<col span="1" style="width: 207px">
					<col span="1" style="width: 80px">
					<col span="1" style="width: 87px">
					<col span="1" style="width: 79px">
					<col span="1" style="width: 70px">
					<col span="1" style="width: 71px">
					<col span="1" style="width: 60px">
					<col span="1" style="width: 76px">
					<col span="1" style="width: 122px">
					<col span="1" style="width: 153px">
					<col span="5" style="width: 27px">
				</colgroup>
				<tr style="border-bottom: 1px solid #8c8c8c;">
					<td></td>
					@foreach($header as $k=>$row)
						<td class="@if ($k!=0) text-center @endif" style="@if ($k==1) min-width: 90px; @endif">
							@if($row['index'])<a href="{{$row['url']}}" data-set="{{$row['index']}}" @if ($order==$row['index']) style="font-weight: bold; color: #216a94;" @endif class="table_href">{{$row['title']}} @if ($order==$row['index']) @if($direct=='asc')&#9650;@else&#9660;@endif @endif</a>@else {{$row['title']}} @endif
						</td>
					@endforeach
					<td colspan='5'></td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff">
					<td></td>
					<td>Всего</td>
					<td></td>
					<td class="text-center"></td>
					<td class="text-center">{{$all_sum->played}}</td>
					<td class="text-center">{{$all_sum->clicks}}</td>
					<td class="text-center"></td>
					<td class="text-center">{{$all_sum->ctr}}</td>
					<td class="text-center">{{$all_sum->summa}}</td>
					<td></td>
					<td></td>
					<td colspan='5'></td>
				</tr>
			</tbody>
			@foreach ($stats as $userActive)
				<tbody>
					<tr>
						<td>
							<a data-toggle="collapse" data-parent="#accordion" href="#p-{{$userActive->user_id}}">
								<span data-set="{{$userActive->user_id}}" class="glyphicon glyphicon-plus plus_us_bottom plus_product"></span>
							</a>
						</td>
						<td>
							<a href="{{route('admin.home', ['user_id'=>$userActive->user_id])}}" target="_blank" style="color: #636b6f;">{{$userActive->name}} @if ($userActive->vip==1)<img src="/images/cabinet/vip.png" data-toggle="tooltip" data-placement="bottom" title="VIP клиент" style="width: 20px; position: relative; top: -3px; cursor: pointer;">@endif</a>
							@if ($userActive->referer)
								<!--{{$usRef=\App\UserProfile::where('user_id', $userActive->referer)->first()}}-->
								@if ($usRef)
									<a href="{{route('admin.home', ['user_id'=>$usRef->user_id])}}" target="_blank" style="color: #0064ff; font-weight: bold;"> (от {{$usRef->name}})</a>
								@endif
							@endif
						</td>
						<td></td>
						<td class="text-center"></td>
						<td class="text-center">{{$userActive->played}}</td>
						<td class="text-center">{{$userActive->clicks}}</td>
						<td class="text-center"></td>
						<td class="text-center">{{$userActive->ctr}}</td>
						<td class="text-center">{{$userActive->summa}}</td>
						<td></td>
						<td></td>
						<td colspan='5'>
							@if ($userActive->dop_status==1)
								<img src="/images/smail/green.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
							@elseif ($userActive->dop_status==2)
								<img src="/images/smail/yellow.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
							@elseif ($userActive->dop_status==3)
								<img src="/images/smail/red.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
							@endif
							<!-- {{$Productcoms=\App\ProductDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
							@if (count($Productcoms)>0)
								<span class="glyphicon glyphicon-exclamation-sign default_status" style="color: #ff6a00; font-size: 20px; top: 2px; cursor: pointer;"
								data-container="body" data-toggle="popover" tabindex="0" data-trigger="focus" data-placement="bottom" data-content="
									@foreach ($Productcoms as $comm)
										Товарный @if ($comm->driver==1) (ТопАдверт) @elseif ($comm->driver==2) (Яндекс) @endif {{round($comm->commission,2)}}<br>
									@endforeach
								">
								</span>
							@endif
						</td>
					</tr>
				</tbody>
				<tbody id="p-{{$userActive->user_id}}" class="panel-collapse vlogen-tbody collapse">
									
				</tbody>
			@endforeach
		</table>
	</div>
</div>
{!! $stats->appends(["direct"=>$direct, "order"=>$order])->render() !!}
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
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
		$(document).on('click', '.plus_product', function(event) {
            var id=$(this).data('set');
			event.preventDefault();
			if ($('#p-'+id).hasClass('in')){
				$('#p-'+id).html('');
			}
			else{
				$.post('/user_detail_widgets_product/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
						$('#p-'+response.id).html(response.view);
						$('[data-toggle="popover"]').popover({html:true});
						$('[data-toggle="tooltip"]').tooltip();
						$('.default_status').popover({html : true});
				});
			}
        });
	</script>
@endpush