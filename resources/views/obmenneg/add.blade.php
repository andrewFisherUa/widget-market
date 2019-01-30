<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	</head>
<body style="background-color: #fff">
<div class="container">
	<div class="row">
		<div class="col-xs-12">
			@if (Session::has('message_success'))
				<div class="alert alert-success">
					{{ session('message_success') }}
				</div>
			@else
				<div style="height: 76px;"></div>
			@endif
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
						<input name="account_balance" type="number" step="0.0001" placeholder="Можно заполнить позже" class="form-control">
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
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>