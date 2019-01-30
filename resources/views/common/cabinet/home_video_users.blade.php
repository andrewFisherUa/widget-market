{!! $stats->appends(["direct"=>$direct, "order"=>$order])->render() !!}
<table id="video_is" class="table table-condensed table-hover widget-table" style="table-layout: fixed;">
	<thead>
		<colgroup>
			<col span="1" style="width: 26px">
			<col span="1" style="width: 185px">
			<col span="1" style="width: 65px">
			<col span="1" style="width: 75px">
			<col span="1" style="width: 75px">
			<col span="1" style="width: 68px">
			<col span="1" style="width: 69px">
			<col span="1" style="width: 58px">
			<col span="1" style="width: 74px">
			<col span="1" style="width: 120px">
			<col span="1" style="width: 117px">
			<col span="1" style="width: 80px">
			<col span="6" style="width: 26px">
		</colgroup>
		@if (\Auth::user()->hasRole('admin'))
			<tr style="background: #000; color: #fff">
				<td colspan="6">На балансах: {{$all_balance['all']}} <span class="rur">q</span></td>
				<td colspan="6">За сегодня: {{$all_balance['today']}} <span class="rur">q</span></td>
				<td colspan="6">На выводе: {{$all_balance['payment']}} <span class="rur">q</span></td>
			</tr>
		@endif
		<tr style="border-bottom: 1px solid #8c8c8c;">
			<td></td>
			@foreach($header as $k=>$row)
				<td class="@if ($k!=0) text-center @endif" style="@if ($k==1) min-width: 90px; @endif">
					@if($row['index'])<a data-set="{{$row['index']}}" @if ($order==$row['index']) style="font-weight: bold; color: #216a94;" @endif class="table_href">{{$row['title']}} @if ($order==$row['index']) @if($direct=='asc')&#9650;@else&#9660;@endif @endif</a>@else {{$row['title']}} @endif
				</td>
			@endforeach
			<td colspan='6'></td>
		</tr>
	</thead>
	<tbody>
		<tr style="background: #000; color: #fff">
			<td></td>
			<td>Всего</td>
			<td></td>
			<td class="text-center">{{$all_sum->loaded}}</td>
			<td class="text-center">{{$all_sum->calculate}}</td>
			<td class="text-center">{{$all_sum->clicks}}</td>
			<td class="text-center">{{$all_sum->util}}</td>
			<td class="text-center">{{$all_sum->ctr}}</td>
			<td class="text-center">{{$all_sum->summa}}</td>
			<td class="text-center">{{$all_sum->coef}}</td>
			<td class="text-center">{{$all_sum->second_summa}}</td>
			<td class="text-center">{{$all_sum->viewable}}</td>
			<td colspan='6'></td>
		</tr>
	</tbody>
	@foreach ($stats as $userActive)
		<tbody>
			<tr>
				<td>
					<a data-toggle="collapse" data-parent="#accordion" href="#v-{{$userActive->user_id}}">
						<span data-set="{{$userActive->user_id}}" class="glyphicon glyphicon-plus plus_us_bottom plus_video"></span>
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
				<td class="text-center">{{$userActive->loaded}}</td>
				<td class="text-center">{{$userActive->calculate}}</td>
				<td class="text-center">{{$userActive->clicks}}</td>
				<td class="text-center">{{$userActive->util}}</td>
				<td class="text-center">{{$userActive->ctr}}</td>
				<td class="text-center">{{$userActive->summa}}</td>
				<td class="text-center">{{$userActive->coef}}</td>
				<td class="text-center">{{$userActive->second_summa}}</td>
				<td class="text-center">{{$userActive->viewable}}</td>
				<td colspan='6'>
					@if ($userActive->dop_status==1)
						<img src="/images/smail/green.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; position: relative; top: -3px">
					@elseif ($userActive->dop_status==2)
						<img src="/images/smail/yellow.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; position: relative; top: -3px">
					@elseif ($userActive->dop_status==3)
						<img src="/images/smail/red.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; position: relative; top: -3px">
					@endif
					<!-- {{$coms=\App\VideoDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
					<!--{{$controlcommissions=\App\UserLinkSumma::where('user_id', $userActive->user_id)->get()}}-->
					<!-- {{ $links=\App\VideoSource::orderBy('title', 'asc')->get() }} -->
					@if (count($coms)>0 or count($controlcommissions)>0)
						<span class="glyphicon glyphicon-exclamation-sign default_status" style="color: #ff6a00; font-size: 20px; top: 2px; cursor: pointer;"
						data-container="body" data-toggle="popover" tabindex="0" data-trigger="focus" data-placement="bottom" data-content="
							@foreach ($coms as $com)
								@if ($com->wid_type==1) Автоплей @elseif($com->wid_type==2) Оверлей @endif @if($com->pad_type==0) Белый @elseif ($com->pad_type==1) Адалт @else($com->pad_type==2) Развлек. @endif {{round($com->videoCommisssion($com->commission_rus),2)}} и {{round($com->videoCommisssion($com->commission_cis),2)}}<br>
							@endforeach
							@foreach ($controlcommissions as $cont)
								@foreach ($links as $l)
									@if ($l->id==$cont->link_id)
										{{$l->title}} {{$cont->summa_rus}} и {{$cont->summa_cis}}
										<br>
									@endif
								@endforeach
							@endforeach
						">
						</span>
					@endif
					@if ($userActive->status==1)
					<a class="active_user" data-set="{{$userActive->user_id}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Отметить как активный клиент" style="float: right; cursor: pointer"><span class="glyphicon glyphicon-eye-open color-green"></span></a>
					@else
					<a class="no_active_user" data-set="{{$userActive->user_id}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Отметить как неактивный клиент" style="float: right; cursor: pointer"><span class="glyphicon glyphicon-eye-close color-red"></span></a>
					@endif
				</td>
			</tr>
		</tbody>
		<tbody id="v-{{$userActive->user_id}}" class="panel-collapse vlogen-tbody collapse">
						
		</tbody>
	@endforeach
</table>
@foreach ($stats as $userActive)
	@include('common.cabinet.modal.add_user_site_video')
	@include('common.cabinet.modal.add_user_widget_video')
	@include('common.cabinet.modal.add_user_dop_status_video')
	@if (\Auth::user()->hasRole('admin'))
		@include('common.cabinet.modal.add_default_on_users_video')
	@endif
@endforeach
{!! $stats->appends(["direct"=>$direct, "order"=>$order])->render() !!}