@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center">Список менеджеров</h4>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td>Имя</td>
					<td>Почта</td>
					<td>Баланс</td>
					<td>Начисления</td>
					<td>Последний вывод</td>
					<td>Клиенты</td>
					<td>Группа коммиссий</td>
				</tr>
			</thead>
			@foreach ($managers as $manager)
				<tr>
					<td><a href="{{route('admin.home', ['id_user'=>$manager->id])}}" target="_blank">{{$manager->name}}</a></td>
					<td>{{$manager->email}}</td>
					<td>{{$manager->Profile->balance}}</td>
					<td><a href="{{route('managers.history', ['id'=>$manager->id])}}" target="_blank" class="btn btn-primary">Подробнее</a></td>
					<td class="text-center">
					<!--{{$pay=\App\Payments\UserPayout::where('user_id', $manager->id)->whereIn('status', [0,1])->orderBy('updated_at', 'desc')->first()}}-->
					@if ($pay)
						@if ($pay->status==0)
							Запрос<br>
							{{date('Y-m-d', strtotime($pay->created_at))}}<br>
							<a href="{{route('managers.history_payout', ['id'=>$manager->id])}}" target="_blank">{{$pay->payout}}</a>
						@elseif ($pay->status==1)
							Оплаченая<br>
							{{date('Y-m-d', strtotime($pay->exit_time_payout))}}<br>
							<a href="{{route('managers.history_payout', ['id'=>$manager->id])}}" target="_blank">{{$pay->payout}}</a>
						@endif
					@endif
					</td>
					<td><a href="{{route('managers.clients', ['id'=>$manager->id])}}" target="_blank">{{count(\App\UserProfile::where('manager', $manager->id)->get())}}</a></td>
					<td>
						<form class="form-inline" role="form" method="post" action="{{route('managers.set_commission')}}">
							{!! csrf_field() !!}
							<input type="text" value="{{$manager->id}}" name="user_id" style="display: none;" hidden>
							<div class="row">
								<div class="col-xs-8 form-group">
									<select class="form-control" name="commissiongroupid">
										@foreach ($commissions as $commission)
											<option @if ($manager->ManagerCommission->commissiongroupid==$commission->commissiongroupid) selected @endif value="{{$commission->commissiongroupid}}">{{$commission->label}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-4 input-group form-group">
									<button type="submit" class="btn btn-success">Применить</button>
								</div>
							</div>
						</form>
					</td>
				</tr>
			@endforeach
		</table>
	</div>
</div>
@endsection