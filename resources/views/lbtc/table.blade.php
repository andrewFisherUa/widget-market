@extends('layouts.app')

@section('content')
<div class="container">
	@include('lbtc.top_menu')
    <div class="row">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td rowspan="2" style="vertical-align: middle">
					Система/Счет<br>
					Остаток на счете<br>
					<strong>{{round($usd, 2)}} - USD</strong><br>
					<strong>{{round($rub, 2)}} - RUB</strong>
					</td>
					<td rowspan="2" style="vertical-align: middle">Обналичивание</td>
					<td colspan="2" class="text-center" style="vertical-align: middle">Объем за сегодня</td>
					<td colspan="2" class="text-center" style="vertical-align: middle">Объем за месяц</td>
				</tr>
				<tr>
					<td class="text-center">+</td>
					<td class="text-center">-</td>
					<td class="text-center">+</td>
					<td class="text-center">-</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($valuts as $valut)
					<tr>
						<td>
						{{$valut->title}}<br>
						<strong style="font-size: 18px;">{{round($balance=\App\Obmenneg\AccountBalance::where('id_valut', $valut->id)->first()->account_balance,2)}}</strong><br>
						<a style="cursor: pointer" class="btn btn-success btn-xs" data-toggle="modal" data-target="#edit_account_balance_{{$valut->id}}" data-backdrop="static">Редактировать</a>
						<a href="{{route('lbtc.balance.log', ['id'=>\App\Obmenneg\AccountBalance::where('id_valut', $valut->id)->first()->id])}}" target="_blank" class="btn btn-danger btn-xs">Логи</a>
						<a href="{{route('lbtc.table.month', ['id'=>$valut->id])}}" class="btn btn-primary btn-xs" target="_blank">Детализация</a>
						</td>
						<td>
						<!--@if ($month=="01" or $month=="03" or $month=="05" or $month=="07" or $month=="08" or $month=="10" or $month=="12")
						{{$max=31}}
						@elseif ($month=="04" or $month=="06" or $month=="09" or $month=="11")
						{{$max=30}}
						@elseif ($month=="02" and $year%4==0)
						{{$max=29}}
						@else
						{{$max=28}}
						@endif
						-->
						<!--{{$cache=\App\Obmenneg\CacheOut::where('id_valut', $valut->id)->whereBetween('date', [$year.'-'.$month.'-01', $year.'-'.$month.'-'.$max])->orderBy('date', 'desc')->get()}}-->
						<!--{{$cacheAll=\App\Obmenneg\CacheOut::where('id_valut', $valut->id)->whereBetween('date', [$year.'-'.$month.'-01', $year.'-'.$month.'-'.$max])->orderBy('date', 'desc')->sum('summa')}}-->
						<!--{{$o=1}}-->
						@foreach ($cache as $c)
							@if ($o>3) @php continue @endphp @endif
							{{$c->date}} - {{$c->summa}}<br>
							<!--{{$o++}}-->
						@endforeach
						</td>
						<!--{{($stats=$valut->monthStat($valut->id, $d, $month, $year))?"":""}}-->
						<td class="text-center" style="vertical-align: middle; font-size: 18px; position: relative;">
							<a style="cursor: pointer; display: flex; flex-direction: row; align-items: center; justify-content: center;
							position: absolute; top: 0; bottom: 0; right: 0; left: 0; text-decoration: none; color: green;" data-toggle="modal" 
							data-target="#transaction_{{$valut->id}}" data-backdrop="static"><strong>{{round($stats?$stats['plus']:0,2)}}</strong></a>
						</td>
						<td class="text-center" style="vertical-align: middle; font-size: 18px; position: relative;">
							<a style="cursor: pointer; display: flex; flex-direction: row; align-items: center; justify-content: center;
							position: absolute; top: 0; bottom: 0; right: 0; left: 0; text-decoration: none; color: red;" data-toggle="modal" 
							data-target="#transaction_{{$valut->id}}" data-backdrop="static"><strong>{{round($stats?$stats['minus']:0,2)}}</strong></a>
						</td>
						<!--{{($all=$valut->monthAllStat($valut->id, $month, $year))?"":""}}-->
						<td class="text-center" style="vertical-align: middle; font-size: 18px; position: relative;">
							<a style="cursor: pointer; display: flex; flex-direction: row; align-items: center; justify-content: center;
							position: absolute; top: 0; bottom: 0; right: 0; left: 0; text-decoration: none; color: green;" data-toggle="modal" 
							data-target="#transaction_{{$valut->id}}" data-backdrop="static"><strong>{{round($all?$all['plus']:0,2)}}</strong></a>
						</td>
						<td class="text-center" style="vertical-align: middle; font-size: 18px; position: relative;">
							<a style="cursor: pointer; display: flex; flex-direction: row; align-items: center; justify-content: center;
							position: absolute; top: 0; bottom: 0; right: 0; left: 0; text-decoration: none; color: red;" data-toggle="modal" 
							data-target="#transaction_{{$valut->id}}" data-backdrop="static"><strong>{{round($all?$all['minus']:0,2)}}</strong></a>
						</td>
					</tr>
					<tr>
						<td colspan="6" style="padding: 0">
							<div id="edit_account_balance_{{$valut->id}}" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="affiliate_modal_header">Редактирование остатка {{$valut->title}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
										<hr class="modal_hr">
										<form class="form-horizontal" role="form" method="POST" action="{{route('obmenneg.edit.account.balance.post')}}">
											{{ csrf_field() }}
											<input type="text" value="{{\App\Obmenneg\AccountBalance::where('id_valut', $valut->id)->first()->id}}" name="id" hidden style="display: none;">
											<div class="form-group">
												<label for="valut" class="col-xs-4 control-label">Название системы/счета</label>
												<div class="col-xs-6">
													<input name="valut" type="text" readonly class="form-control" value="{{$valut->title}}">
												</div>
											</div>
											<div class="form-group">
												<label for="account_balance" class="col-xs-4 control-label">Остаток на счете</label>
												<div class="col-xs-6">
													<input name="account_balance" type="number"  step="0.0001" readonly  value="{{\App\Obmenneg\AccountBalance::where('id_valut', $valut->id)->first()->account_balance}}" class="form-control">
												</div>
											</div>
											<div class="form-group">
												<label for="action" class="col-xs-4 control-label">Внести/вычесть</label>
												<div class="col-xs-6">
													<input name="action" type="number"  step="0.0001" class="form-control" required>
												</div>
											</div>
											<div class="form-group">
												<div class="col-xs-offset-1 col-xs-10 text-center">
												  <button type="submit" class="btn btn-primary">Сохранить</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							
							
							<div id="transaction_{{$valut->id}}" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="affiliate_modal_header">Редактирование выводов {{$valut->title}} {{$from}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
										<hr class="modal_hr">
										<!--{{$transactions=$valut->transactions($valut->id, $from)}}-->
										<form class="form-horizontal" role="form" method="POST" action="{{route('lbtc.transaction.post')}}">
											{{ csrf_field() }}
											<input type="text" value="{{$from}}" name="from" style="display: none;" hidden>
											<input type="text" value="{{$valut->id}}" name="id" hidden style="display: none;">
											<div class="row" style="margin: 0;">
												<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none;">Плюс</div>
												<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none;">Минус</div>
												<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none;">Обнал</div>
												<div class="col-xs-6 text-center" style="border: solid 1px #000; border-left: none; border-right: none;">Комментарий</div>
											</div>
											@foreach ($transactions as $transaction)
											<div class="row" style="margin: 0;">
												<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none; padding: 0"><input name="plus[]" value="{{$transaction->plus}}" style="border: none; width: 100%" type="text"></div>
												<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none; padding: 0"><input name="minus[]" value="{{$transaction->minus}}" style="border: none; width: 100%" type="text"></div>
												<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none; padding-top: 1px;"><input class="check" name="qwe[]" @if ($transaction->obnal==1) checked @endif value="1" type="checkbox"><input class="obnal" type="text" value="@if ($transaction->obnal==1) 1 @else 0 @endif" name="obnal[]" hidden></div>
												<div class="col-xs-6 text-center" style="border: solid 1px #000; border-left: none; padding: 0; border-right: none;"><textarea name="comment[]" style="border: none; width: 100%; height: 18px; font-size: 12px; padding: 0; margin: 0; line-height: 1; resize: vertical;">{{$transaction->comment}}</textarea></div>
											</div>
											@endforeach
											<div class="information_json_plus text-center"></div>
											<div class="row" style="padding: 0;">
												<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
													<span class="btn btn-success plus" data-set="{{$valut->id}}" style="padding: 6px;">+строка</span>
												</div>
												<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
													<button type="submit" class="btn btn-primary otp"  style="padding: 6px;">
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
@push('cabinet_home_js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	$(document).ready(function() {
		jQuery('.plus').click(function(){
			var roditel=$('#transaction_'+$(this).data('set'));
			var pl=roditel.find($('.information_json_plus'));
			pl.before(
			'<div class="row" style="margin: 0;">'+
				'<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none; padding: 0"><input name="plus[]" style="border: none; width: 100%" type="text"></div>'+
				'<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none; padding: 0"><input name="minus[]" style="border: none; width: 100%" type="text"></div>'+
				'<div class="col-xs-2 text-center" style="border: solid 1px #000; border-left: none; padding-top: 1px;"><input class="check" name="qwe[]" value="1" type="checkbox"><input class="obnal" type="text" value="0" name="obnal[]" hidden></div>'+
				'<div class="col-xs-6 text-center" style="border: solid 1px #000; border-left: none; padding: 0; border-right: none;"><textarea name="comment[]" style="border: none; width: 100%; height: 18px; font-size: 12px; padding: 0; margin: 0; line-height: 1; resize: vertical;"></textarea></div>'+
			'</div>'
			
			);
		});
		
		
	});
	$(document).on('click', '.check', function(){
		if ($(this).prop("checked")){
			$(this).parent().find('.obnal').val(1);
		}
		else{
			$(this).parent().find('.obnal').val(0);
		}
	});
</script>
@endpush