@extends('layouts.app')

@section('content')
<div class="container">
	@include('lbtc.top_menu')
	<div class="row" style="margin: 5px 0;">
		<a href="#" data-toggle="modal" data-target="#transaction" class="btn btn-sm btn-success">Редактировать транзакции</a>
	</div>
    <div class="row text-center">
		<h3>Детализация {{$valut->title}} за {{$from}}</h3>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td>Дата записи</td>
					<td>Сумма</td>
					<td>Коментарий</td>
				</tr>
			</thead>
			@foreach ($stats as $stat)
				<tr>
					<td>{{$stat->created_at}}</td>
					<td><span style="font-weight: bold; @if ($stat->plus - $stat->minus > 0) color: green; @else color: red; @endif">{{$stat->plus - $stat->minus}}</span></td>
					<td>{{$stat->comment}}</td>
				</tr>
			@endforeach
		</table>
		<div id="transaction" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="affiliate_modal_header">Редактирование выводов {{$valut->title}} {{$from}}<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
					<hr class="modal_hr">
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
						@foreach ($stats as $transaction)
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
    </div>
</div>
@endsection
@push('cabinet_home_js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	$(document).ready(function() {
		jQuery('.plus').click(function(){
			var roditel=$('#transaction');
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