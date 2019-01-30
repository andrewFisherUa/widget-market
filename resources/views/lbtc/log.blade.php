@extends('layouts.app')

@section('content')
<div class="container">
	@include('lbtc.top_menu')
	<div class="row">
		<h3 class="text-center">Логи по счету {{\App\Obmenneg\Valut::find(\App\Obmenneg\AccountBalance::find($id)->id_valut)->title}}
		</h2>
		{!! $logs->render() !!}
		<table class="table table-bordered" style="table-layout: fixed; margin-top: 20px">
			<thead>
				<tr>
					<td>Дата</td>
					<td>Прежний остаток</td>
					<td>Остаток</td>
					<td>Кто совершил действие</td>
					<td>Пояснение</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($logs as $log)
					<tr>
						<td>{{$log->created_at}}</td>
						<td>{{$log->old_balance}}</td>
						<td>{{$log->new_balance}}</td>
						<td>{{\App\User::find($log->who_action)->name}}</td>
						<td>{{$log->comment}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		{!! $logs->render() !!}
	</div>
</div>
@endsection