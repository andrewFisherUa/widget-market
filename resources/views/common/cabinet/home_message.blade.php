@if ($status==1)
	<div class="alert alert-success">
		<strong>Успех!</strong> {{ $message }}
	</div>
@else
	<div class="alert alert-danger">
		<strong>Не удача!</strong> {{ $message }}
	</div>
@endif