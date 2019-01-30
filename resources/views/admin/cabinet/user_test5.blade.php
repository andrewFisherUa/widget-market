@if ($status==1)
	<div class="alert alert-success">
		<strong>Успех!</strong> {{ $text }}
	</div>
@else
	<div class="alert alert-danger">
		<strong>Успех!</strong> {{ $text }}
	</div>
@endif