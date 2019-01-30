@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center">Статистика начислений менеджера <a href="{{route('admin.home', ['id_user'=>$manager->user_id])}}" target="_blank">{{$manager->name}}</a></h4>
		{!! $payouts->render() !!}
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td>Дата запроса</td>
					<td>Дата закрытия</td>
					<td>Сумма</td>
					<td>Статус</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($payouts as $payout)
					<tr>
						<td>{{$payout->created_at}}</td>
						<td>{{$payout->exit_time_payout}}</td>
						<td>{{$payout->payout}}</td>
						<td>
							@if ($payout->status==0)
								Запрос
							@elseif ($payout->status==1)
								Оплченая
							@elseif ($payout->status==2)
								Отказаная
							@elseif ($payout->status==3)
								Отменена пользователем
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		{!! $payouts->render() !!}
	</div>
</div>
@endsection
@push('cabinet_home')
	<style>
		.detail_com:focus, detail_com:active{
			outline: none!important;
		}
		.popover{
			height: 390px!important;
			overflow: auto;
			width: 400px!important;
			max-width: 400px!important;
		}
		#app{
			margin-bottom: 220px!important;
		}
	</style>
@endpush