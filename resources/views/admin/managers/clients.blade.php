@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center">Клиенты менеджера <a href="{{route('admin.home', ['id_user'=>$manager->user_id])}}" target="_blank">{{$manager->name}}</a></h4>
		<p>Всего клиентов: {{count(\App\UserProfile::where('manager', $manager->user_id)->get())}}</p>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<td>Дата регистрации</td>
					<td>Имя</td>
					<td>Email</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($users as $user)
					<tr>
						<td>{{$user->created_at}}</td>
						<td><a href="{{route('admin.home', ['id_user'=>$user->user_id])}}" target="_blank">{{$user->name}}</a></td>
						<td>{{$user->email}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
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