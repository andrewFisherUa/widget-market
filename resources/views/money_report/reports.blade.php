@extends('layouts.app')

@section('content')
<div class="container">
	@include('money_report.menu')
	<div class="row">
		<h3 class="text-center">Все отчеты</h3>
		<div class="col-xs-12" style="margin: 5px 0;">
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
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td>Id</td>
					<td>Время открытия</td>
					<td>Время закрытия</td>
					<td>Сумма на момент открытия</td>
					<td>Сумма на момент закрытия</td>
					<td>Подробнее</td>
					{{--@if (\Auth::user()->hasRole('admin'))--}}<td colspan="2">Редактор</td>{{--@endif--}}
				</tr>
			</thead>
			<tbody>
				@foreach ($reports as $rep)
					<tr>
						<td>{{$rep->id}}</td>
						<td>{{$rep->opened}}</td>
						<td>{{$rep->closed}}</td>
						<td>
						{{round($rep->summa_opened,2)}} руб / 
						{{round($rep->summa_opened/$rep->cources($rep->closed)->usd,2)}} usd / 
						{{round($rep->summa_opened/$rep->cources($rep->closed)->btc,8)}} btc
						</td>
						<td>
						@if ($rep->closed)
							{{round($rep->summa_closed,2)}} руб / 
							{{round($rep->summa_closed/$rep->cources($rep->closed)->usd,2)}} usd / 
							{{round($rep->summa_closed/$rep->cources($rep->closed)->btc,8)}} btc
						@endif
						</td>
						<td><a href="{{route('money.report.operation', ['id'=>$rep->id])}}">Подробнее</a></td>
						@if (\Auth::user()->hasRole(['admin']) or \Auth::user()->id==37)
							@if ((\Auth::user()->hasRole('admin')) or (\Auth::user()->id==37 and ($rep->id==$report->id or $rep->id==$report->id)))
								<td data-toggle="modal" data-target="#report_opertaion_plus_{{$rep->id}}" data-backdrop="static" style="cursor: pointer;">+</td>
								<td data-toggle="modal" data-target="#report_opertaion_minus_{{$rep->id}}" data-backdrop="static" style="cursor: pointer;">-</td>
							@endif
						@endif
					</tr>
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->id==37)
						@if ((\Auth::user()->hasRole('admin')) or (\Auth::user()->id==37 and ($rep->id==$report->id or $rep->id==$report->id)))
							<tr>
								<td colspan="8" style="padding: 0">
									<div id="report_opertaion_plus_{{$rep->id}}" class="modal fade">
										<div class="modal-dialog" style="width: 800px;">
											<div class="modal-content" style="padding-bottom: 20px;">
												<div class="affiliate_modal_header">Изменение входящих операций по отчету #{{$rep->id}} от {{$rep->opened}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
												<hr class="modal_hr">
												<form class="form-inline" role="form" method="POST" action="{{route('money.edit.report')}}">
													{!! csrf_field() !!}
													<input type="text" value="{{$rep->id}}" name="reports_id" hidden>
													<input type="text" value="1" name="type" hidden>
													<!--{{$operations=\App\MoneyReport\ReportOperation::where('reports_id', $rep->id)->orderBy('datetime', 'asc')->get()}}-->
													@foreach ($operations as $operation)
														@if ($operation->type==1)
															<div class="row">
																<div class="input-group col-xs-2 form-group">
																	<select name="account_id[]" class="form-control" @if($operation->shortcode=='rbt') readonly @endif>
																		<option value="no">--Тип операции</option>
																		@foreach ($accounts as $account)
																			@if ($account->valuts->status==0)
																				@php continue @endphp
																			@endif
																			<option @if ($operation->accounts_id==$account->id) selected @endif value="{{$account->id}}">{{$account->title}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="input-group col-xs-3 form-group"><input @if($operation->shortcode=='rbt') readonly @endif type="text" name="datetime[]" value="{{$operation->datetime}}" placeholder="время" class="form-control"></div>
																<div class="input-group col-xs-2 form-group"><input @if($operation->shortcode=='rbt') readonly @endif type="text" name="summa[]" placeholder="сумма" value="{{$operation->summa}}" class="form-control"></div>
																<div class="input-group col-xs-2 form-group">
																	<select name="shortcode[]" class="form-control" @if($operation->shortcode=='rbt') readonly @endif>
																		<option value="no">--Тип операции</option>
																		@foreach ($types as $type)
																			@if ($type->type==1)
																				<option @if ($operation->shortcode==$type->shortcode) selected @endif value="{{$type->shortcode}}">{{$type->title}}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div class="input-group col-xs-2 form-group">
																	<textarea class="form-control" name="comment[]" @if($operation->shortcode=='rbt') readonly @endif style="height: 36px;">{{$operation->comment}}</textarea>
																</div>
															</div>
															<hr class="modal_hr" style="margin-top: 1px; margin-bottom: 1px;">
														@endif
													@endforeach
													<div class="information_json_plus text-center"></div>
													<div class="row" style="padding: 0;">
														<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
															<span class="btn btn-success plus" data-id="{{$rep->id}}" style="padding: 6px;">+строка</span>
														</div>
														<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
															<button type="submit" class="btn btn-primary"  style="padding: 6px;">
																Сохранить
															</button>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
									
									<div id="report_opertaion_minus_{{$rep->id}}" class="modal fade"> 
										<div class="modal-dialog" style="width: 800px;">
											<div class="modal-content" style="padding-bottom: 20px;">
												<div class="affiliate_modal_header">Изменение исходящих операций по отчету #{{$rep->id}} от {{$rep->opened}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
												<hr class="modal_hr">
												<form class="form-inline" role="form" method="POST" action="{{route('money.edit.report')}}">
													{!! csrf_field() !!}
													<input type="text" value="{{$rep->id}}" name="reports_id" hidden>
													<input type="text" value="2" name="type" hidden>
													<!--{{$operations=\App\MoneyReport\ReportOperation::where('reports_id', $rep->id)->orderBy('datetime', 'asc')->get()}}-->
													@foreach ($operations as $operation)
														@if ($operation->type==2)
															<div class="row">
																<div class="input-group col-xs-2 form-group">
																	<select name="account_id[]" class="form-control" @if($operation->shortcode=='rbt') readonly @endif>
																		<option value="no">--Тип операции</option>
																		@foreach ($accounts as $account)
																			@if ($account->valuts->status==0)
																				@php continue @endphp
																			@endif
																			<option @if ($operation->accounts_id==$account->id) selected @endif value="{{$account->id}}">{{$account->title}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="input-group col-xs-2 form-group"><input @if($operation->shortcode=='rbt') readonly @endif type="text" name="datetime[]" value="{{$operation->datetime}}" placeholder="время" class="form-control"></div>
																<div class="input-group col-xs-2 form-group"><input @if($operation->shortcode=='rbt') readonly @endif type="text" name="summa[]" placeholder="сумма" value="{{$operation->summa}}" class="form-control"></div>
																<div class="input-group col-xs-2 form-group">
																	<select name="shortcode[]" class="form-control" @if($operation->shortcode=='rbt') readonly @endif>
																		<option value="no">--Тип операции</option>
																		@foreach ($types as $type)
																			@if ($type->type==2)
																				<option @if ($operation->shortcode==$type->shortcode) selected @endif value="{{$type->shortcode}}">{{$type->title}}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div class="input-group col-xs-2 form-group">
																	<textarea class="form-control" name="comment[]" @if($operation->shortcode=='rbt') readonly @endif style="height: 36px;">{{$operation->comment}}</textarea>
																</div>
																<div class="input-group col-xs-1 form-group">
																	@if($operation->shortcode!='rbt')
																		<input class="check" value="1" @if($operation->obnal) checked @endif type="checkbox">
																		<input class="obnal" type="text" value="@if($operation->obnal) 1 @else 0 @endif" name="obnal[]" hidden>
																	@else
																		<input class="check" value="1" @if($operation->obnal) checked @endif type="checkbox">
																		<input class="obnal" type="text" value="@if($operation->obnal) 1 @else 0 @endif" name="obnal[]" hidden>
																	@endif
																</div>
															</div>
															<hr class="modal_hr" style="margin-top: 1px; margin-bottom: 1px;">
														@endif
													@endforeach
													<div class="information_json_minus text-center"></div>
													<div class="row" style="padding: 0;">
														<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
															<span class="btn btn-success minus" data-id="{{$rep->id}}" style="padding: 6px;">+строка</span>
														</div>
														<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
															<button type="submit" class="btn btn-primary"  style="padding: 6px;">
																Сохранить
															</button>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endif
					@endif
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
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {
			jQuery('.plus').click(function(){
				jQuery ('#report_opertaion_plus_'+$(this).data('id')+' .information_json_plus').before(
				'<div class="row">'+
					'<div class="input-group col-xs-2 form-group">'+
						'<select name="account_id[]" class="form-control">'+
							'<option value="no">--Система/счет</option>'+
							'@foreach ($accounts as $account)'+
								'@if ($account->valuts->status==0)'+
									'@php continue @endphp'+
								'@endif'+
								'<option value="{{$account->id}}">{{$account->title}}</option>'+
							'@endforeach'+
						'</select>'+
					'</div>'+
					'<div class="input-group col-xs-3 form-group" style="margin: 0 2px;"><input type="text" name="datetime[]" value="{{date("Y-m-d H:i:s")}}" placeholder="время" class="form-control"></div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;"><input type="text" name="summa[]" placeholder="сумма" class="form-control"></div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;">'+
						'<select name="shortcode[]" class="form-control">'+
							'<option value="no">--Тип операции</option>'+
							'@foreach ($types as $type)'+
								'@if ($type->type==1)'+
									'@if ($type->shortcode!="rbt")'+
										'<option value="{{$type->shortcode}}">{{$type->title}}</option>'+
									'@endif'+
								'@endif'+
							'@endforeach'+
						'</select>'+
					'</div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;">'+
						'<textarea class="form-control" name="comment[]" style="height: 36px;"></textarea>'+
					'</div>'+
				'</div>'+
				'<hr class="modal_hr" style="margin-top: 1px; margin-bottom: 1px;">'
				);
			});
		});
		$(document).ready(function() {
			jQuery('.minus').click(function(){
				jQuery ('#report_opertaion_minus_'+$(this).data('id')+' .information_json_minus').before(
				'<div class="row">'+
					'<div class="row">'+
					'<div class="input-group col-xs-2 form-group">'+
						'<select name="account_id[]" class="form-control">'+
							'<option value="no">--Система/счет</option>'+
							'@foreach ($accounts as $account)'+
								'@if ($account->valuts->status==0)'+
									'@php continue @endphp'+
								'@endif'+
								'<option value="{{$account->id}}">{{$account->title}}</option>'+
							'@endforeach'+
						'</select>'+
					'</div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;"><input type="text" name="datetime[]" value="{{date("Y-m-d H:i:s")}}" placeholder="время" class="form-control"></div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;"><input type="text" name="summa[]" placeholder="сумма" class="form-control"></div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;">'+
						'<select name="shortcode[]" class="form-control">'+
							'<option value="no">--Тип операции</option>'+
							'@foreach ($types as $type)'+
								'@if ($type->type==2)'+
									'@if ($type->shortcode!="rbt")'+
										'<option value="{{$type->shortcode}}">{{$type->title}}</option>'+
									'@endif'+
								'@endif'+
							'@endforeach'+
						'</select>'+
					'</div>'+
					'<div class="input-group col-xs-2 form-group" style="margin: 0 2px;">'+
						'<textarea class="form-control" name="comment[]" style="height: 36px;"></textarea>'+
					'</div>'+
					'<div class="input-group col-xs-1 form-group">'+
						'<input class="check" value="1" type="checkbox">'+
						'<input class="obnal" type="text" value="0" name="obnal[]" hidden>'+
					'</div>'+
				'</div>'+
				'<hr class="modal_hr" style="margin-top: 1px; margin-bottom: 1px;">'
				);
			});
		});
		$(document).on('click', '.check', function(){
			console.log($(this).parent().find('.obnal').val());
			if ($(this).prop("checked")){
				$(this).parent().find('.obnal').val(1);
				console.log($(this).parent().find('.obnal').val());
			}
			else{
				$(this).parent().find('.obnal').val(0);
			}
		});
		
	</script>
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
