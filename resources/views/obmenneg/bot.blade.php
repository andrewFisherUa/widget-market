<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head></head>
<body style="background-color: #fff">
Статус: @if ($status==1) <span style="color: green">Включен</span> @else <span style="color: red">Выключен</span> @endif<br>
<form class="form-horizontal" role="form" method="post" action="{{route('obmenneg.activate.bot.post')}}">
	{!! csrf_field() !!}
	<select name="activate">
		<option @if ($status==1) selected @endif value="1">Включить</option>
		<option @if ($status==0) selected @endif value="0">Выключить</option>
	</select>
	<button type="submit">Сохранить</button>
</form>
</body>
</html>