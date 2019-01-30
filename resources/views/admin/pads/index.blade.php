@extends('layouts.app')

@section('content')
<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-inline" role="form" method="get" action=" {{ route('pads.all') }}">
						<div class="row">
							<div class="col-xs-3 form-group">
								<label for="poisk" class="col-xs-4 control-label">Поиск</label>
								<div class="col-xs-8">
									<input name='poisk' class="form-control" value="{{$poisk}}">
								</div>
							</div>
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager'))
							<div class="col-xs-3 form-group">
								<label for="manager" class="col-xs-4 control-label">Менеджер</label>
								<div class="col-xs-8">
									<select name='manager' class="form-control">
										<option value="0">Все</option>
										@foreach (\App\Role::whereIn('id', [3,4,5])->get() as $role)
											@foreach ($role->users as $user)
												<option @if ($manager==$user->id) selected @endif value="{{$user->id}}">{{$user->name}}</option>
											@endforeach
										@endforeach
									</select>
								</div>
							</div>
							@endif
							<div class="col-xs-2 form-group">
								<label for="type" class="col-xs-3 control-label">Тип</label>
								<div class="col-xs-9">
									<select name='type' class="form-control">
										<option @if ($type==0) selected @endif value="0">Все</option>
										<option @if ($type==1) selected @endif value="1">Товарный</option>
										<option @if ($type==2) selected @endif value="2">Видео</option>
										<option @if ($type==4) selected @endif value="4">Тизерный</option>
									</select>
								</div>
							</div>
							<div class="col-xs-2 input-group form-group">
								<button type="submit" class="btn btn-primary">Применить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		<div class="row">
		
		{!! $pads->appends(["poisk"=>$poisk, "type"=>$type, "manager"=>$manager])->render() !!}
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="text-center">
					<td>Партнер</td>
					<td>Менеджер</td>
					<td>Площадка</td>
					<td>Дата добавления</td>
					<td>Удалить</td>
				</tr>
			</thead>
			@foreach ($pads as $pad)
			@php
			$userset=$pad->setUser($pad->user_id);
			@endphp
				<tr @if ($pad->status==0) class="info" @endif>
			@if($userset)	
					<td><a href="{{ route('admin.home', ['id_user'=>$pad->user_id]) }}" target="_blank">{{$pad->setUser($pad->user_id)->name}}</a>
					@if ($pad->userProfile($pad->user_id)->referer)
						<!--{{$usRef=\App\UserProfile::where('user_id', $pad->userProfile($pad->user_id)->referer)->first()}}-->
						@if ($usRef)
							<a href="{{route('admin.home', ['user_id'=>$usRef->user_id])}}" target="_blank" style="color: #0064ff; font-weight: bold;"> (от {{$usRef->name}})</a>
						@endif
					@endif
					@if ($pad->status==1 and strtotime(date('Y-m-d'))<strtotime(date('2018-01-01')))
						@if ((strtotime($pad->setUser($pad->user_id)->created_at)<strtotime(date('2017-12-01'))
							and 
							strtotime($pad->created_at)>strtotime(date('2017-12-01'))) 
							or ($pad->userProfile($pad->user_id)->referer 
							and 
							strtotime($pad->created_at)>strtotime(date('2017-12-01'))))
						<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
						color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
						@endif
					@endif
					@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $pad->user_id)->first())
						@if ($pad->status==1 and strtotime(date('Y-m-d'))<strtotime($pad->setUser($pad->user_id)->created_at)+3600*24*14)
							<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
							color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
						@endif
					@endif
					@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $pad->user_id)->first())
						@if ($pad->status==1 and strtotime(date('Y-m-d'))<strtotime($pad->setUser($pad->user_id)->created_at)+3600*24*14)
							<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
							color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
						@endif
					@endif
					</td>
					<td class="text-center">
					@if ($pad->userProfile($pad->user_id)->manager)
						<!--{{$usRef=\App\UserProfile::where('user_id', $pad->userProfile($pad->user_id)->manager)->first()}}-->
						@if ($usRef)
							<a href="{{route('admin.home', ['user_id'=>$usRef->user_id])}}" target="_blank">{{$usRef->name}}</a>
						@endif
					@endif
					</td>
					<td>
						@if ($pad->status==0)
							@if ($pad->type==-1 or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13)
								<span data-toggle="tooltip" data-placement="bottom" title="Модерация на товарный виджет" class="glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green"></span>
							@endif
							@if ($pad->type==-1 or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14)
								<span data-toggle="tooltip" data-placement="bottom" title="Модерация на видео виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green"></span>
							@endif
							@if ($pad->type==-1 or $pad->type==4 or $pad->type==5 or $pad->type==6 or $pad->type==7 or $pad->type==12 or $pad->type==13 or $pad->type==14)
								<span data-toggle="tooltip" data-placement="bottom" title="Модерация на тизерный виджет" class="glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green"></span>
							@endif
							@if ($pad->type==-1 or $pad->type==8 or $pad->type==9 or $pad->type==10 or $pad->type==11 or $pad->type==12 or $pad->type==13 or $pad->type==14)
								<span data-toggle="tooltip" data-placement="bottom" title="Модерация на брендирование" class="glyphicon glyphicon-picture affiliate_all_pads_domain_gliph green"></span>
							@endif
						@elseif ($pad->status==1)
							@if ($pad->type==-1 or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13)
								<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на товарный виджет" class="glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green"></span>
							@endif
							@if ($pad->type==-1 or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14)
								<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на видео виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green"></span>
							@endif
							@if ($pad->type==-1 or $pad->type==4 or $pad->type==5 or $pad->type==6 or $pad->type==7 or $pad->type==12 or $pad->type==13 or $pad->type==14)
								<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на тизерный виджет" class="glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green"></span>
							@endif
							@if ($pad->type==-1 or $pad->type==8 or $pad->type==9 or $pad->type==10 or $pad->type==11 or $pad->type==12 or $pad->type==13 or $pad->type==14)
								<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на брендирование" class="glyphicon glyphicon-picture affiliate_all_pads_domain_gliph green"></span>
							@endif
						@elseif ($pad->status==2)
							<span data-toggle="tooltip" data-placement="bottom" title="Отклонена" class="glyphicon glyphicon-remove-circle affiliate_all_pads_domain_gliph red"></span>
						@endif
					<a href="{{ route('pads.edit', ['id'=>$pad->id])}}" target="_blank">{{$pad->domain}}</a>
					</td>
					<td>{{$pad->created_at}}</td>
					<td></td>
					@else
					<td>Пользователь не найден</td>
					<td></td>
				 	<td>	
	                {{$pad->domain}}			
				    </td>	
					<td>
					{{$pad->created_at}}			
				</td>	
			@endif		
				</tr>
			@endforeach
		</table>
		{!! $pads->appends(["poisk"=>$poisk, "type"=>$type, "manager"=>$manager])->render() !!}
		
	</div>
</div>
@endsection
@push('cabinet_home')
	<style>
	.affiliate_all_pads_domain_gliph{
		font-size: 21px;
		top: 5px;
		cursor: pointer;
	}
	.red{
		color: #9c2512;
	}
	.green{
		color: #20895e;
	}
	.yellow{
		color: #f7d11a;
	}
	.form-inline .form-control{
	width: 100%!important;
	}
	.form-inline .control-label{
		margin-top: 7px;
	}
	.form-inline{
		margin-bottom: 10px;
	}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
	<script>
		$('select').change (function(){
			$("input[name='poisk']").val('');
		});
	</script>
@endpush