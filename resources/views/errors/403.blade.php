@extends('layouts.app_error')

@section('content')
<div class="container">
    <div class="row">
		<div class="col-xs-12 text-center">
		<span style="font-size: 200px; color: rgba(59, 67, 113, 0.95);">403</span><br>
		<h2><b>Доступ запрещен</b></h2>
		<p><b>Причины, которые могли привести к ошибке</b></p>
		<p>1. Адрес был набран неправильно. Проверьте правильность адерса.</p>
		<p>2. Вам не разрешено просматривать данную страницу.</p>
		<a href="/" class="btn btn-danger">Вернуться на главную</a>
		</div>
    </div>
</div>
@endsection

