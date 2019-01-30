@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h4 class="text-center">История логинов (Пересекающиеся ip адресса)</h4>
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<td rowspan="2">Группа</td>
					<td rowspan="2">Имена</td>
					<td colspan="2">Последний заход</td>
					<td rowspan="2">Подробнее</td>
				</tr>
				<tr class="text-center">
					<td>IP адерс</td>
					<td>Дата</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($result as $k=>$group)
					<tr>
						<td>
							{{$k+1}}
						</td>
						@php
							$user_guliks = explode(",", $group);
						@endphp
						<td>
							@foreach ($user_guliks as $user_gulik)
								<!--{{$user_g=preg_replace('~[^0-9]+~','',$user_gulik)}}
								{{$users=\App\User::find($user_g)}}-->
								@if ($users)
								<a href="{{route('admin.home', ['user_id'=>$users->id])}}" target="_blank">{{$users->name}}</a>
								@if ($users->profile->referer)
									<!-- {{$refer=\App\UserProfile::where('user_id', $users->profile->referer)->first() }}-->
									@if ($refer)
										(от {{$refer->name}})
									@endif
								@endif
								<br>
								@endif
							@endforeach
						</td>
						<td class="text-center">
							@foreach ($user_guliks as $user_gulik)
								<!--{{$user_g=preg_replace('~[^0-9]+~','',$user_gulik)}}-->
								@foreach ($last_logins as $last_login)
									@if ($last_login['user_id']==$user_g)
										{{$last_login['ip_adress']}}<br>
									@endif
								@endforeach
							@endforeach
						</td>
						<td class="text-center">
							@foreach ($user_guliks as $user_gulik)
								<!--{{$user_g=preg_replace('~[^0-9]+~','',$user_gulik)}}-->
								@foreach ($last_logins as $last_login)
									@if ($last_login['user_id']==$user_g)
										{{$last_login['created_at']}}<br>
									@endif
								@endforeach
							@endforeach
						</td>
						<td class="text-center"><a data-toggle="tooltip" data-placement="bottom" title="Детальная статистика" href="{{route('users_log.auth_log_detail', ['$ids'=>$group])}}"><span class="glyphicon glyphicon-th"></span></a></td>
					</tr>
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