<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
		<style>
		input[type='number'] {
			-moz-appearance:textfield;
		}

		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			-webkit-appearance: none;
		}
		input:focus, textarea:focus{
		    outline:none;
		}
		</style>
	</head>
<body style="background-color: #fff">
<div class="container">
		<div class="row" style="padding: 0;">
			<div class="col-xs-12" style="padding: 0;">
				<div class="col-xs-6" style="padding: 0; text-align: center; border: solid 1px;"><b>{{$valut->title}}</b></div>
				<div class="col-xs-6" style="padding: 0; text-align: center; border: solid 1px;">Комментарий</div>
			</div>
		</div>
		<div class="row" style="padding: 0;">
			<div class="col-xs-6" style="padding: 0;">
				<div class="col-xs-6" style="padding: 0; text-align: center; border: solid 1px;">+</div>
				<div class="col-xs-6" style="padding: 0; text-align: center; border: solid 1px;">-</div>
			</div>
			<div class="col-xs-6" style="padding: 0; text-align: center; border: solid 1px; height: 24px;"></div>
		</div>
		<form class="form-horizontal" role="form" method="POST" action="{{route('obmenneg.edit.stat.post')}}">
			{{ csrf_field() }}
			<input type="text" value="{{$valut->id}}" name="id" hidden style="display: none;">
			<input type="text" value="{{$from}}" name="from" hidden style="display: none;">			
			@foreach($transactions as $transaction)
				<div class="row" style="padding: 0;">
					<div class="col-xs-3" style="padding: 0; border: solid 1px;"><input name="plus[]" value="{{$transaction->plus}}" style="border: none; width: 100%" type="number" step="0.0001"></div>
					<div class="col-xs-3" style="padding: 0; border: solid 1px;"><input name="minus[]" value="{{$transaction->minus}}" style="border: none; width: 100%" type="number" step="0.0001"></div>
					<div class="col-xs-6" style="padding: 0; border: solid 1px;"><textarea name="comment[]" style="border: none; width: 100%; height: 18px; font-size: 10px; padding: 0; margin: 0; line-height: 1; resize: vertical;">{{$transaction->comment}}</textarea></div>
			</div>
			@endforeach
			<div class="information_json_plus text-center"></div>
			
			<div class="row" style="padding: 0;">
				<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
					<span class="btn btn-success plus" style="padding: 6px;">+строка</span>
				</div>
				<div class="col-xs-5 text-center" style="padding: 0; margin: 5px 0;">
					<button type="submit" class="btn btn-primary"  style="padding: 6px;">
						Сохранить
					</button>
				</div>
			</div>
		</form>
		
		
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	$(document).ready(function() {
		jQuery('.plus').click(function(){
			jQuery('.information_json_plus').before(
			'<div class="row" style="padding: 0;">' + 
				'<div class="col-xs-3" style="padding: 0; border: solid 1px;"><input name="plus[]" style="border: none; width: 100%" type="number" step="0.0001"></div>'+
				'<div class="col-xs-3" style="padding: 0; border: solid 1px;"><input name="minus[]" style="border: none; width: 100%" type="number" step="0.0001"></div>'+
				'<div class="col-xs-6" style="padding: 0; border: solid 1px;"><textarea name="comment[]" style="border: none; width: 100%; height: 18px; font-size: 10px; padding: 0; margin: 0; line-height: 1; resize: vertical;"></textarea></div>'+
			'</div>'
			);
		});
	});
</script>
</body>
</html>