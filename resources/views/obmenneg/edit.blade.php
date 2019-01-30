<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
		<link href="{{ asset('datepicker/bootstrap-datepicker.css') }}" rel="stylesheet">
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
	<div class="row">
		<div class="col-xs-12">
			<form class="form-inline" role="form" method="get" style="margin:5px 0">
				<div class="input-group col-xs-2 form-group">
					<input name="from" class="form-control" value="{{$from}}">
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
			</form>
		</div>
		<form role="form" method="POST" action="{{route('obmenneg.edit.position')}}">
			{{ csrf_field() }}
			<button type="submit" class="btn btn-primary"  style="padding: 6px; position: absolute; left: 50%;">
				Сохранить позиции
			</button>
		<div id="sortable">
			@foreach ($valuts as $valut)
				<div class="col-xs-3" style="height: 300px; margin: 10px 0; padding: 0 5px">
					<input type="text" name="id[]" value="{{$valut->id}}" style="display: none" hidden>
					<span class="btn btn-primary sortable pull-right" style="padding: 0; position: absolute; right: 5px;"><img src="/images/arrow.png" style="width: 16px; height: 16px;"></span>
					<iframe class="test_frame_{{$valut->id}}" style="width: 100%; height: 100%; border: none;" src="{{route('obmenneg.edit.account.balance.id', ['id'=>$valut->id, 'from'=>$from])}}"></iframe>
				</div>
			@endforeach
			
		</div>
		</form>
	</div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('datepicker/bootstrap-datepicker.ru.min.js') }}"></script>
<script>	
$(document).ready(function() {
	$('input[name="from"]').datepicker({
		"format": "yyyy-mm-dd",
		language: "ru"
	});
});	
</script>
<script>
$( function() {
		$( "#sortable" ).sortable();
	});
</script>
</body>
</html>