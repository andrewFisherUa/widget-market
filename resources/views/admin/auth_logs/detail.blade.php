@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center">Детальная история логинов по группе адресов {{$ip}}</h4>
		<a href="{{route('users_log.auth_log')}}" class="btn btn-default">Вернуться назад</a>
		<table class="table table-hover table-bordered" style="margin-top: 10px;">
			<thead>
				<tr class="text-center">
					<td>IP адрес</td>
					<td>Имя</td>
					<td>Дата захода</td>
				</tr>
			</thead>
			<tbody>
				<!--{{$old_ip=0}}-->
				@foreach ($groups as $group)
					<tr @if ($old_ip!=$group['ip_adress']) style="border-top: 5px solid" @endif>
						<td>{{$group['ip_adress']}}</td>
						<!--{{$users=\App\User::find($group['user_id'])}}-->
						<td><a href="{{route('admin.home', ['user_id'=>$users->id])}}" target="_blank">{{$users->name}}</a>
						@if ($users->profile->referer)
									<!-- {{$refer=\App\UserProfile::where('user_id', $users->profile->referer)->first() }}-->
									@if ($refer)
										(от {{$refer->name}})
									@endif
								@endif
						</td>
						<td>{{$group['created_at']}}</td>
					</tr>
					<!--{{$old_ip=$group['ip_adress']}}-->
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
@push('cabinet_home')
	<style>
		.table tr td{
			vertical-align:middle!important;
		}
	</style>
@endpush
@push('cabinet_home_js')
	
@endpush