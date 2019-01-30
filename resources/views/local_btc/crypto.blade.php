@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td>Id</td>
					<td>Название</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($coins as $coin)
					<tr>
						<td>{{$coin->id}}</td>
						<td>{{$coin->name}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
    </div>
</div>
@endsection
