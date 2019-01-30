<table class="table table-condensed table-hover widget-table" style="table-layout: fixed;">
	<thead>
		<colgroup>
			<col span="1" style="width: 29px">
			<col span="1" style="width: 230px">
			<col span="7" style="width: 76px">
			<col span="1" style="width: 97px">
			<col span="1" style="width: 126px">
			<col span="5" style="width: 31px">
		</colgroup>
		@if (\Auth::user()->hasRole('admin'))
			<tr style="background: #000; color: #fff">
				<td colspan="5">На балансах: {{$all_balance['all']}} <span class="rur">q</span></td>
				<td colspan="5">За сегодня: {{$all_balance['today']}} <span class="rur">q</span></td>
				<td colspan="6">На выводе: {{$all_balance['payment']}} <span class="rur">q</span></td>
			</tr>
		@endif
		<tr style="border-bottom: 1px solid #8c8c8c;">
			<td></td>
			@foreach($header as $k=>$row)
				<td class="@if ($k!=0) text-center @endif" style="@if ($k==1) min-width: 90px; @endif">
					@if($row['index'])<a class="table_href" href="{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
				</td>
			@endforeach
			<td colspan='5'></td>
		</tr>
	</thead>
	<tbody>
		<tr style="background: #000; color: #fff">
			<td></td>
			<td>Всего</td>
			<td></td>
			<td class="text-center"></td>
			<td class="text-center">{{$product_sum->played}}</td>
			<td class="text-center">{{$product_sum->clicks}}</td>
			<td class="text-center"></td>
			<td class="text-center">{{$product_sum->ctr}}</td>
			<td class="text-center">{{$product_sum->summa}}</td>
			<td></td>
			<td></td>
			<td colspan='5'></td>
		</tr>
	</tbody>
	@foreach ($productUsersActive as $userActive)
		<tbody>
			<tr>
				<td>
					<a data-toggle="collapse" data-parent="#accordion" href="#p-{{$userActive->user_id}}">
						<span data-set="{{$userActive->user_id}}" class="glyphicon glyphicon-plus plus_us_bottom plus_product"></span>
					</a>
				</td>
				<td>
					<a href="{{route('admin.home', ['user_id'=>$userActive->user_id])}}" target="_blank" style="color: #636b6f;">{{$userActive->name}} @if ($userActive->vip==1)<img src="/images/cabinet/vip.png" data-toggle="tooltip" data-placement="bottom" title="VIP клиент" style="width: 20px; position: relative; top: -3px; cursor: pointer;">@endif</a>
					@if ($userActive->referer)
						<!--{{$usRef=\App\UserProfile::where('user_id', $userActive->referer)->first()}}-->
						@if ($usRef)
							<a href="{{route('admin.home', ['user_id'=>$usRef->user_id])}}" target="_blank" style="color: #0064ff; font-weight: bold;"> (от {{$usRef->name}})</a>
						@endif
					@endif
				</td>
				<td></td>
				<td class="text-center"></td>
				<td class="text-center">{{$userActive->played}}</td>
				<td class="text-center">{{$userActive->clicks}}</td>
				<td class="text-center"></td>
				<td class="text-center">{{$userActive->ctr}}</td>
				<td class="text-center">{{$userActive->summa}}</td>
				<td></td>
				<td></td>
				<td colspan='5'>
					@if ($userActive->dop_status==1)
						<img src="/images/smail/green.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
					@elseif ($userActive->dop_status==2)
						<img src="/images/smail/yellow.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
					@elseif ($userActive->dop_status==3)
						<img src="/images/smail/red.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
					@endif
					<!-- {{$Productcoms=\App\ProductDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
					@if (count($Productcoms)>0)
						<span class="glyphicon glyphicon-exclamation-sign default_status" style="color: #ff6a00; font-size: 20px; top: 2px; cursor: pointer;"
						data-container="body" data-toggle="popover" tabindex="0" data-trigger="focus" data-placement="bottom" data-content="
							@foreach ($Productcoms as $comm)
								Товарный @if ($comm->driver==1) (ТопАдверт) @elseif ($comm->driver==2) (Яндекс) @endif {{round($comm->commission,2)}}<br>
							@endforeach
						">
						</span>
					@endif
					@if ($userActive->status==1)
					<a href="{{route('admin.user_active', ['id_user'=>$userActive->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Отметить как активный клиент" style="float: right"><span class="glyphicon glyphicon-eye-open color-green"></span></a>
					@else
					<a href="{{route('admin.user_no_active', ['id_user'=>$userActive->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Отметить как неактивный клиент" style="float: right"><span class="glyphicon glyphicon-eye-close color-red"></span></a>
					@endif
				</td>
			</tr>
		</tbody>
		<tbody id="p-{{$userActive->user_id}}" class="panel-collapse vlogen-tbody collapse">
							
		</tbody>
	@endforeach
</table>
@foreach ($productUsersActive as $userActive)
	@include('admin.cabinet.add_user_site')
	@include('admin.cabinet.add_user_widget')
	@include('admin.cabinet.add_user_dop_status')
	@if (\Auth::user()->hasRole('admin'))
		@include('admin.cabinet.add_video_default_on_users')
	@endif
@endforeach