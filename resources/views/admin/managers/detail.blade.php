@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center">Статистика начислений менеджера <a href="{{route('admin.home', ['id_user'=>$manager->user_id])}}" target="_blank">{{$manager->name}}</a></h4>
		{!! $commissions->render() !!}
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td>Дата</td>
					<td>Сумма</td>
					<td>Подробнее</td>
					<td>Активных клиентов</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($commissions as $commission)
					<tr>
						<td>{{$commission->day}}</td>
						<td>{{round($commission->summa,2)}}</td>
						<td>
						<span class="detail_com" style="cursor: pointer; color: #5757ff; font-weight: bold;"
							data-container="body" data-toggle="popover" data-html="true" tabindex="0" data-placement="bottom" data-content="
							
								@php $hists=explode(';', $commission['history']) @endphp
								<!-- {{$cnt=0}} -->
								@foreach ($hists as $hist)
									@php $req=explode(':', $hist) @endphp
									@if ($req['0'] and $req['1'])
										<!--{{$user=\App\UserProfile::where('user_id', $req['0'])->first()}}-->
										<!--{{$cnt+=1}}-->
										@if ($user)
											<a href='{{route('admin.home', ['id_user'=>$user->user_id])}}' target='_blank'>{{$user->name}} - {{$req['1']}}</a><br>
										@endif
									@endif
								@endforeach
						">Подробнее
						</span>
						</td>
						<td><a href="{{route('managers.clients', ['id'=>$manager->user_id])}}" target="_blank">{{$cnt}}</a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
		{!! $commissions->render() !!}
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