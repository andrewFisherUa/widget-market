<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
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
		<form class="form-inline" role="form" method="get" style="margin:5px 0">
			<div class="input-group col-xs-3 form-group">
				<select name='month' class="form-control">
					<option @if ($month=="god") @endif value="god">---</option>
					<option @if ($month=="01") selected @endif value="01">Январь</option>
					<option @if ($month=="02") selected @endif value="02">Февраль</option>
					<option @if ($month=="03") selected @endif value="03">Март</option>
					<option @if ($month=="04") selected @endif value="04">Апрель</option>
					<option @if ($month=="05") selected @endif value="05">Май</option>
					<option @if ($month=="06") selected @endif value="06">Июнь</option>
					<option @if ($month=="07") selected @endif value="07">Июль</option>
					<option @if ($month=="08") selected @endif value="08">Август</option>
					<option @if ($month=="09") selected @endif value="09">Сентябрь</option>
					<option @if ($month=="10") selected @endif value="10">Октябрь</option>
					<option @if ($month=="11") selected @endif value="11">Ноябрь</option>
					<option @if ($month=="12") selected @endif value="12">Декабрь</option>
				</select>
			</div>
			<div class="input-group col-xs-2 form-group">
				<input name='year' class="form-control" value="{{$year}}">
			</div>
			<div class="col-xs-2 input-group form-group">
				<button type="submit" class="btn btn-primary">Применить</button>
			</div>
		</form>
		<a class="btn btn-primary" data-toggle="modal" data-target="#add_valut" data-backdrop="static">Добавить систему/счет</a>
		<a class="btn btn-primary" data-toggle="modal" data-target="#edit_account_balance" data-backdrop="static">Редактировать</a>
		<a class="btn btn-primary" href="{{route('obmenneg.first')}}">За сегодня</a>
		<a class="btn btn-primary" href="{{route('obmenneg.index')}}">За месяц</a>
		<h3 class="text-center">Обналичивание с карты {{$valut->title}}</h3>
		<table class="table table-bordered" style="table-layout: fixed; margin-top: 20px">
			<thead>
				<tr>
					<td>Дата</td>
					<td>Сумма</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff;">
					<td>Всего</td>
					<td>{{$sum}}</td>
				</tr>
				@foreach ($cache as $c)
					<tr>
						<td>{{$c->date}}</td>
						<td>{{$c->summa}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		
	</div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
$('.modal_exit').on("click", function() {
	$('#table_obm').attr('src', $('#table_obm').attr('src'));
	$('#balance').attr('src', $('#balance').attr('src'));
});
</script>
</body>
</html>