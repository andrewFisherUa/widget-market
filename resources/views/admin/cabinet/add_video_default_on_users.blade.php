<div id="add_video_default_on_users_{{$userActive->user_id}}" class="default_commission modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Назначение особенных комиссий<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<!--{{$commissions=\App\VideoDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
			<!--{{$Productcommissions=\App\ProductDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
			<!--{{$controlcommissions=\App\UserLinkSumma::where('user_id', $userActive->user_id)->get()}}-->
			<!-- {{ $links=\App\VideoSource::orderBy('title', 'asc')->where('status', '1')->get() }} -->
			@if (count($commissions)>0 or count($Productcommissions)>0 or count($controlcommissions)>0)
				<h5 class="text-center"><b>Имеющееся особенные комиссии</b></h5>
				<div class="col-xs-8 col-xs-offset-2 text-center">
				@foreach ($commissions as $commission)
					@if ($commission->wid_type==1) Автоплей @elseif ($commission->wid_type==2) Оверлей @endif
					@if ($commission->pad_type==0) Белый @elseif ($commission->pad_type==1) Адалт @elseif ($commission->pad_type==3) Развлекательный @endif
					{{$commission->videoCommisssion($commission->commission_rus)}} и 
					{{$commission->videoCommisssion($commission->commission_cis)}}
					<a href="{{ route('admin.add_video_default_on_users_delete', ['id_user'=>$userActive->user_id]) }}"
					onclick="event.preventDefault(); document.getElementById('delete_default_{{$commission->id}}').submit();"
					data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Удалить комиссию" style="float: right">
						<span class="glyphicon glyphicon-trash color-red"></span>
					</a>
						<form id="delete_default_{{$commission->id}}" action="{{ route('admin.add_video_default_on_users_delete', ['id_user'=>$userActive->user_id]) }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
						<input name='id' value="{{$commission->id}}">
                    </form>
					<br>
				@endforeach
				@foreach ($Productcommissions as $com)
					Товарный виджет @if ($com->driver==1) (ТопАдверт) @elseif ($com->driver==2) (Яндекс) @endif {{$com->commission}}
					<a href="{{ route('admin.add_product_default_on_users_delete', ['id_user'=>$userActive->user_id]) }}"
					onclick="event.preventDefault(); document.getElementById('delete_default_p_{{$com->id}}').submit();"
					data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Удалить комиссию" style="float: right">
						<span class="glyphicon glyphicon-trash color-red"></span>
					</a>
						<form id="delete_default_p_{{$com->id}}" action="{{ route('admin.add_product_default_on_users_delete', ['id_user'=>$userActive->user_id]) }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
						<input name='id' value="{{$com->id}}">
                    </form>
					<br>
				@endforeach
				@foreach ($controlcommissions as $cont)
					@foreach ($links as $l)
						@if ($l->id==$cont->link_id)
							{{$l->title}} {{$cont->summa_rus}} и {{$cont->summa_cis}}
							<a href="{{ route('admin.user_control_summa_delete', ['id_user'=>$userActive->user_id]) }}"
							onclick="event.preventDefault(); document.getElementById('control_default_{{$cont->id}}').submit();"
							data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Удалить комиссию" style="float: right">
								<span class="glyphicon glyphicon-trash color-red"></span>
							</a>
								<form id="control_default_{{$cont->id}}" action="{{ route('admin.user_control_summa_delete', ['id_user'=>$userActive->user_id]) }}" method="POST" style="display: none;">
								{{ csrf_field() }}
								<input name='id' value="{{$cont->id}}">
							</form>
							<br>
						@endif
					@endforeach
				@endforeach
				</div>
			@endif
			<form class="form-horizontal" role="form" method="post" action="{{route('admin.add_video_default_on_users', ['id_user'=>$userActive->user_id])}}">
				{!! csrf_field() !!}
				<input type="hidden" name="user_id" value="{{$userActive->user_id}}">
				<div class="form-group">
					<label for="stclogin" class="col-xs-4 control-label">Выберите тип</label>
					<div class="col-xs-6">
						<select name="type" class="form-control type_for_commission">
							<option value="0">Не выбрано</option>
							<option value="1">Автоплей белый</option>
							<option value="2">Автоплей адалт</option>
							<option value="3">Автоплей развлекательный</option>
							<option value="4">Оверлей белый</option>
							<option value="5">Оверлей адалт</option>
							<option value="6">Оверлей развлекательный</option>
							<option value="7">Товарный виджет (ТопАдверт)</option>
							<option value="8">Товарный виджет (Яндекс)</option>
							<option value="9">Контроль ссылок</option>
						</select>
					</div>
				</div>
				<!--{{$commission_groups=\DB::table('сommission_groups')->where('type', '3')->orderBy('commissiongroupid', 'asc')->get()}}-->
				<div class="form-group video_commission_rus" style="display: none;">
					<label for="stclogin" class="col-xs-4 control-label">Комиссия для России</label>
					<div class="col-xs-6">
						<select name="commission_rus" class="form-control commission_rus">
							@foreach ($commission_groups as $commission_group)
								<option value="{{$commission_group->commissiongroupid}}">{{$commission_group->label}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group video_commission_cis"  style="display: none;">
					<label for="stclogin" class="col-xs-4 control-label">Комиссия для СНГ</label>
					<div class="col-xs-6">
						<select name="commission_cis" class="form-control commission_cis">
							@foreach ($commission_groups as $commission_group)
								<option value="{{$commission_group->commissiongroupid}}">{{$commission_group->label}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group product_commission" style="display: none;">
					<label for="stclogin" class="col-xs-4 control-label">Коэффициент на который умножаются показатели</label>
					<div class="col-xs-6">
						<input type="text" name="product_commission" class="form-control product_commission">
					</div>
				</div>
				<div class="form-group link_select" style="display: none;">
					<label for="stclogin" class="col-xs-4 control-label">Выберите ссылку</label>
					<div class="col-xs-6">
						<select name="link_id" class="form-control">
							@foreach ($links as $link)
								<option value="{{$link->id}}">{{$link->title}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group link_summa_rus" style="display: none;">
					<label for="stclogin" class="col-xs-4 control-label">Сумма за 1000 показов Россия</label>
					<div class="col-xs-6">
						<input type="text" name="link_summa_rus" class="form-control link_summa_rus">
					</div>
				</div>
				<div class="form-group link_summa_cis" style="display: none;">
					<label for="stclogin" class="col-xs-4 control-label">Сумма за 1000 показов СНГ</label>
					<div class="col-xs-6">
						<input type="text" name="link_summa_cis" class="form-control link_summa_cis">
					</div>
				</div>
				<div class="form-group commission_save" style="display: none;">
					<div class="col-xs-offset-1 col-xs-10 text-center">
					  <button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>