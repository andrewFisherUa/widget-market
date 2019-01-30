@extends('layouts.app')

@section('content')
<div class="container">
	@include('money_report.menu')
	<div class="row">
		@foreach ($spors as $spor)
			@if ($spor->id_ad=='609849')
				<b>Открыт спор!</b> Списаны деньги с киви на сумму {{$spor->amount*1.01}}<br>
			@elseif ($spor->id_ad=='617372')
				<b>Открыт спор!</b> Списаны биткоины с локалбиткоина на сумму {{$spor->amount_btc*1.01}}<br>
			@elseif ($spor->id_ad=='609928')
				<b>Открыт спор!</b> Списаны деньги с яндекс на сумму {{$spor->amount*1.005}}<br>
			@elseif ($spor->id_ad=='609305')
				<b>Открыт спор!</b> Списаны биткоины с локалбиткоина на сумму {{$spor->amount_btc*1.01}}<br>
			@endif
		@endforeach
	</div>
	<div class="row">
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td>Система счет</td>
					<td>Остаток</td>
					<td colspan="2">Редактор</td>
					<td>Все операции</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($accounts as $account)
					<tr @if ($account->valuts->status==0) style="background: red" @endif>
						<td class="text-left">
						<a href="{{route('money.report.operation.month', ['id'=>$account->id])}}">
						{{$account->title}} <b>{{$account->shortcode}}</b> @if($account->card)<span class="glyphicon glyphicon-credit-card" style="top: 3px; color: #3232d2;"></span>@endif
						</a>
						</td>
						<td>
						@if ($account->shortcode=='eth' or $account->shortcode=='btc')
							{{round($account->summa->summa,8)}}
						@else
							{{round($account->summa->summa,2)}}
						@endif
						</td>
						@if ($account->valuts->status==1)
						<td data-toggle="modal" data-target="#report_opertaion_plus_{{$account->id}}" data-backdrop="static" style="cursor: pointer;">+</td>
						<td data-toggle="modal" data-target="#report_opertaion_minus_{{$account->id}}" data-backdrop="static" style="cursor: pointer;">-</td>
						@else
						<td></td>
						<td></td>
						@endif
						<td><a href="{{route('money.operation.report.account', ['report_id'=>$report->id, 'account_id'=>$account->id])}}">Операции</a></td>
					</tr>
					<tr>
						<td colspan="5" style="padding: 0">
							<div id="report_opertaion_plus_{{$account->id}}" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content" style="padding-bottom: 20px;">
										<div class="affiliate_modal_header">Изменение входящих операций по {{$account->title}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
										<hr class="modal_hr">
										<form class="form-inline" role="form" method="POST" action="{{route('money.add.operation')}}">
											{!! csrf_field() !!}
											<input type="text" value="{{$report->id}}" name="reports_id" hidden>
											<input type="text" value="{{$account->id}}" name="accounts_id" hidden>
											<input type="text" value="1" name="type" hidden>
											<!--{{$operations=$account->operations($account->id, $report->id)}}-->
											@foreach ($operations as $operation)
												@if ($operation->type==1)
													<div class="row">
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
													<span class="btn btn-success plus" data-id="{{$account->id}}" style="padding: 6px;">+строка</span>
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
							
							<div id="report_opertaion_minus_{{$account->id}}" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content" style="padding-bottom: 20px;">
										<div class="affiliate_modal_header">Изменение исходящих операций по {{$account->title}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
										<hr class="modal_hr">
										<form class="form-inline" role="form" method="POST" action="{{route('money.add.operation')}}">
											{!! csrf_field() !!}
											<input type="text" value="{{$report->id}}" name="reports_id" hidden>
											<input type="text" value="{{$account->id}}" name="accounts_id" hidden>
											<input type="text" value="2" name="type" hidden>
											<!--{{$operations=$account->operations($account->id, $report->id)}}-->
											@foreach ($operations as $operation)
												@if ($operation->type==2)
													<div class="row">
														<div class="input-group col-xs-3 form-group"><input @if($operation->shortcode=='rbt') readonly @endif type="text" name="datetime[]" value="{{$operation->datetime}}" placeholder="время" class="form-control"></div>
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
															<textarea class="form-control" @if($operation->shortcode=='rbt') readonly @endif name="comment[]" style="height: 36px;">{{$operation->comment}}</textarea>
														</div>
														@if ($account->card)
														<div class="input-group col-xs-2 form-group">
															<input class="check" value="1"  @if($operation->obnal) checked @endif type="checkbox">
															<input class="obnal" type="text" value="@if($operation->obnal) 1 @else 0 @endif" name="obnal[]" hidden>
														</div>
														@endif
													</div>
													<hr class="modal_hr" style="margin-top: 1px; margin-bottom: 1px;">
												@endif
											@endforeach
											<div class="information_json_minus text-center"></div>
											<div class="row" style="padding: 0;">
												<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
													<span class="btn btn-success minus" data-card='{{$account->card}}' data-id="{{$account->id}}" style="padding: 6px;">+строка</span>
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
				@endforeach
			</tbody>
		</table>
    </div>
</div>
@endsection
@push('cabinet_home')
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
				var card=$(this).data('card');
				if (card==1){
					card='<div class="input-group col-xs-2 form-group">'+
							'<input class="check" value="1" type="checkbox">'+
							'<input class="obnal" type="text" value="0" name="obnal[]" hidden>'+
						'</div>';
				}
				else{
					card='';
				}
				jQuery ('#report_opertaion_minus_'+$(this).data('id')+' .information_json_minus').before(
				'<div class="row">'+
					'<div class="input-group col-xs-3 form-group" style="margin: 0 2px;"><input type="text" name="datetime[]" value="{{date("Y-m-d H:i:s")}}" placeholder="время" class="form-control"></div>'+
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
					card+
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

@endpush
