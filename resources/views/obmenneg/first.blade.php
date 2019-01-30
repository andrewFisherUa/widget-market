<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
		<link href="{{ asset('datepicker/bootstrap-datepicker.css') }}" rel="stylesheet">
		<style>
			.sticky{
				position: -webkit-sticky;
				position: -moz-sticky;
				position: -ms-sticky;
				position: -o-sticky;
				position: sticky;
			}
		</style>
	</head>
<body>
<div class="container" style='margin-top: 20px;'>
	<div class="row">
		<a class="btn btn-primary" data-toggle="modal" data-target="#add_valut" data-backdrop="static">Добавить систему/счет</a>
		<a class="btn btn-primary" data-toggle="modal" data-target="#edit_account_balance" data-backdrop="static">Редактировать</a>
		<a class="btn btn-primary" href="{{route('obmenneg.index')}}">Детализация месяца</a>
		<table class="table table-bordered" style="position: absolute; left: 0; margin-top: 20px">
			<thead>
				<tr>
					<td rowspan="2">Остаток на счете<br>
					<b>Итого: </b>{{$sum}}</td>
					<td rowspan="2">Система/Счет</td>
					<td rowspan="2">Обналичивание</td>
					<td colspan="2">Объем месяц</td>
					<td rowspan="2" style="position:">Объем сутки</td>
				</tr>
				<tr>
					<td>+</td>
					<td>-</td>
				</tr>
			</thead>
			<!--@if ($month=="01" or $month=="03" or $month=="05" or $month=="07" or $month=="08" or $month=="10" or $month=="12")
			{{$l=31}}
			@elseif ($month=="04" or $month=="06" or $month=="09" or $month=="11")
			{{$l=30}}
			@elseif ($month=="02" and $year%4==0)
			{{$l=29}}
			@else
			{{$l=28}}
			@endif
			-->
			<tbody>
				@foreach ($valuts as $valut)
					<tr>
						<td class="sticky" style="left: 0; background: #f3f3f3;">
						<!--{{$balance=\App\Obmenneg\AccountBalance::where('id_valut', $valut->id)->first()}}
							{{$bal=$balance->account_balance}}
						-->
						{{$bal}}
						<br><a style="cursor: pointer" data-toggle="modal" data-target="#edit_account_balance_{{$valut->id}}" data-backdrop="static">Редактировать</a>
						<br><a href="{{route('obmenneg.account.balance.log', ['id'=>$balance->id])}}" target="_blank">Логи</a>
						</td>
						<td class="sticky" style="left: 113px; background: #f3f3f3;">{{$valut->title}}</td>
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
						<span><b>Всего: {{$cacheAll}}</b></span><br>
						<a style="cursor: pointer" data-toggle="modal" data-target="#caching_balance_{{$valut->id}}" data-backdrop="static">Добавить запись</a>
						<br><a href="{{route('obmenneg.cache.out.valut', ['id'=>$valut->id])}}" target="_blank">Посмотреть все</a>
						</td>
						<!--{{($all=$valut->monthAllStat($valut->id, $month, $year))?"":""}}-->
						<td style="word-wrap: break-word;">
						{{$all?$all['plus']:0}}
						</td>
						<td style="word-wrap: break-word;">
						{{$all?$all['minus']:0}}
						</td>
						<td style="padding: 0; vertical-align: middle;">
							<table class="table-bordered" style="width: 100%;">
								<tr>
									<td colspan="2" style="text-align: center;">{{$d}}.{{$month}}</td>
								</tr>
								<tr>
									<td style="min-width: 80px; text-align: center;"><a style="cursor: pointer; display: block" data-toggle="modal" data-target="#edit_ost_balance_{{$valut->id}}" data-backdrop="static">+</a></td>
									<td style="min-width: 80px; text-align: center;"><a style="cursor: pointer; display: block" data-toggle="modal" data-target="#edit_ost_balance_{{$valut->id}}" data-backdrop="static">-</a></td>
								</tr>
								<tr>
									<!--{{($stats=$valut->monthStat($valut->id, $d, $month, $year))?"":""}}-->
									<td style="min-width: 80px; text-align: center; word-wrap: break-word;">{{$stats?$stats['plus']:0}}</td>
									<td style="min-width: 80px; text-align: center; word-wrap: break-word;">{{$stats?$stats['minus']:0}}</td>
								</tr>
							</table>
						</td>

					</tr>
				@endforeach
			</tbody>
		</table>
		
		@foreach ($valuts as $valut)
			<div id="caching_balance_{{$valut->id}}" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="affiliate_modal_header">Снятие наличных {{$valut->title}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
						<hr class="modal_hr">
						<form class="form-horizontal" role="form" method="POST" action="{{route('obmenneg.add.cache.post')}}">
							{{ csrf_field() }}
							<input type="text" value="{{$valut->id}}" name="id" hidden style="display: none;">
							<div class="form-group">
								<label for="valut" class="col-xs-4 control-label">Название системы/счета</label>
								<div class="col-xs-6">
									<input name="valut" type="text" readonly class="form-control" value="{{$valut->title}}">
								</div>
							</div>
							<div class="form-group">
								<label for="valut" class="col-xs-4 control-label">Дата снятия</label>
								<div class="col-xs-6">
									<input name="date" type="text" class="form-control" value="{{date('Y-m-d')}}">
								</div>
							</div>
							<div class="form-group">
								<label for="valut" class="col-xs-4 control-label">Сумма снятия</label>
								<div class="col-xs-6">
									<input name="summa" type="numeric" step="0.0001" class="form-control" value="">
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
			
			<!--{{$from=$year.'-'.$month.'-'.$d}}-->
			<div id="edit_ost_balance_{{$valut->id}}" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="affiliate_modal_header">Редактирование {{$valut->title}} за {{$from}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
						<hr class="modal_hr">
						<iframe class="test_frame_{{$valut->id}}" style="width: 100%; height: 100%; border: none;" src="{{route('obmenneg.edit.account.balance.id', ['id'=>$valut->id, 'from'=>$from])}}"></iframe>
					</div>
				</div>
			</div>
			<!--{{$balance=\App\Obmenneg\AccountBalance::where('id_valut', $valut->id)->first()}}
				{{$bal=$balance->account_balance}}-->
			<div id="edit_account_balance_{{$valut->id}}" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="affiliate_modal_header">Редактирование остатка {{$valut->title}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
						<hr class="modal_hr">
						<form class="form-horizontal" role="form" method="POST" action="{{route('obmenneg.edit.account.balance.post')}}">
							{{ csrf_field() }}
							<input type="text" value="{{$balance->id}}" name="id" hidden style="display: none;">
							<div class="form-group">
								<label for="valut" class="col-xs-4 control-label">Название системы/счета</label>
								<div class="col-xs-6">
									<input name="valut" type="text" readonly class="form-control" value="{{$valut->title}}">
								</div>
							</div>
							<div class="form-group">
								<label for="account_balance" class="col-xs-4 control-label">Остаток на счете</label>
								<div class="col-xs-6">
									<input name="account_balance" type="number" readonly  value="{{$bal}}"  step="0.0001" class="form-control">
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
		@endforeach
		
		<div id="add_valut" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="affiliate_modal_header">Добавление системы/счета<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
					<hr class="modal_hr">
					<form class="form-horizontal" role="form" method="post" action="{{route('obmenneg.add.valut.post')}}">
						{!! csrf_field() !!}
						<div class="form-group">
							<label for="valut" class="col-xs-4 control-label">Название системы/счета</label>
							<div class="col-xs-6">
								<input name="valut" type="text" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label for="account_balance" class="col-xs-4 control-label">Остаток на счете</label>
							<div class="col-xs-6">
								<input name="account_balance" type="number"  step="0.0001" placeholder="Можно заполнить позже" class="form-control">
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
		
		<div id="edit_account_balance" class="modal fade">
			<div class="modal-dialog" style="width: 80%; height: 80%; min-height: 500px;">
				<div class="modal-content" style="height: 100%;">
				<div class="affiliate_modal_header">Редактирование по дням<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
					<hr class="modal_hr">
					<iframe id="balance" src="{{route('obmenneg.edit.account.balance')}}" style="width: 100%; border: none; height: 90%;"></iframe>
				</div>
			</div>
		</div>
		
	</div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('datepicker/bootstrap-datepicker.ru.min.js') }}"></script>
<script>
$('.modal_exit').on("click", function() {
	$('#table_obm').attr('src', $('#table_obm').attr('src'));
	$('#balance').attr('src', $('#balance').attr('src'));
});
</script>
<script>	
$(document).ready(function() {
	$('input[name="date"]').datepicker({
		"format": "yyyy-mm-dd",
		language: "ru",
		autoclose: true
	});
});	
</script>
</body>
</html>