<tr>
	<td colspan="16">
		<div class="col-xs-2" style="margin: 5px 0;">
			<button data-toggle="modal" data-target="#add_user_site_{{$id}}" type="submit" class="btn btn-success">
				Добавить площадку
			</button>
		</div>
		<div class="col-xs-2" style="margin: 5px 0;">
			<button data-toggle="modal" data-target="#add_user_widget_{{$id}}" type="submit" class="btn btn-success">
				Создать виджет
			</button>
		</div>
		<div class="col-xs-2" style="margin: 5px 0;">
			<button data-toggle="modal" data-target="#add_user_dop_status_{{$id}}" type="submit" class="btn btn-success">
				Изменить доп. статус
			</button>
		</div>
		@if (\Auth::user()->hasRole('admin'))
			<div class="col-xs-2" style="margin: 5px 0;">
				<button data-toggle="modal" data-target="#add_video_default_on_users_{{$id}}" type="submit" class="btn btn-success">
					Назначить особые комиссии
				</button>
			</div>
			<div class="col-xs-5" style="margin: 5px 0;">
				<form class="form-inline" role="form" method="post" action="{{route('payments.set_commission')}}">
					{!! csrf_field() !!}
					<div class="col-xs-8 form-group">
						<label for="payment" class="col-xs-3 control-label" style="padding: 6px 0;">Сумма</label>
						<div class="col-xs-9">
							<input type="hidden" name="user_id" value="{{$id}}">
							<input name='payment' type="number" step="0.01" class="form-control">
						</div>
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Начислить</button>
					</div>
				</form>
			</div>
		@endif
		<div class="col-xs-5" style="margin: 5px 0;">
			<form class="form-inline" role="form" method="post" action="{{route('admin.user_for_manager', ['id_user'=>$id])}}">
				{!! csrf_field() !!}
				<div class="col-xs-8 form-group">
					<label for="manager" class="col-xs-3 control-label" style="padding: 6px 0;">Менеджер</label>
					<div class="col-xs-9">
						<input type="hidden" name="user_id" value="{{$id}}">
						<!--{{$all_managers=\App\User::whereHas('roles', function ($query) {
						$query->whereIn('id', ['3','4','5']);
						})->orderBy('name', 'asc')->get()}}
						{{$user=\App\UserProfile::where('user_id', $id)->first()}}
						-->
						<select name='manager' class="form-control">
							<option value="0">Не выбран</option>
							@foreach($all_managers as $manager)
								<option @if ($user->manager==$manager->id) selected @endif value="{{$manager->id}}">{{$manager->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Назначить</button>
				</div>
			</form>
		</div>
	</td>
</tr>
<tr>
	<td>Тип</td>
	<td>Виджет</td>
	<td class="text-center">Ставка</td>
	<td class="text-center">Запросы</td>
	<td class="text-center">Показы</td>
	<td class="text-center">Клики</td>
	<td class="text-center">Выкуп</td>
	<td class="text-center">Ctr</td>
	<td class="text-center">Доход</td>
	<td class="text-center">Зач. глубина</td>
	<td class="text-center">Бонус за глубину</td>
	<td colspan="5"></td>
</tr>		
@foreach ($widgets as $widget)
	<tr>
		<td>
			@if ($widget->type==1)
				<span data-toggle="tooltip" data-placement="bottom" title="Товарный виджет" class="glyphicon glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green widget-gl"></span>
			@elseif ($widget->type==2)
				@if ($widget->video['type']==1)
					<span data-toggle="tooltip" data-placement="bottom" title="Автоплей виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
				@elseif ($widget->video['type']==2)
					<span data-toggle="tooltip" data-placement="bottom" title="Оверлей виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
				@endif
			@endif
		</td>
		<td>
			@if ($widget->type==1)
				Товарный № {{$widget->id}} {{$widget->partnerPad['domain']}}
			@elseif ($widget->type==2)
				@if ($widget->video['type']==1)
					Autoplay №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==2)
					Overlay №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==3)
					Vast №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@endif
			@endif
		</td>
		<td class="text-center">
			@if ($widget->type==1)
				@if ($widget->product['driver']==1)
					<span data-toggle="tooltip" data-placement="bottom" title="ТопАдверт" style="font-weight: bold; cursor: pointer;">Т</span>
				@elseif ($widget->product['driver']==2)
					<span data-toggle="tooltip" data-placement="bottom" title="Яндекс" style="font-weight: bold; cursor: pointer;">Я</span>
				@endif
				<!--{{($statistic=$widget->ProductStat($widget->id, $widget->product['id'], $from, $to))?"":""}}-->
				@if ($statistic['clicked']>0 and $statistic['summa']>0)
					<span>{{round($statistic['summa'] / $statistic['clicked'],2)}}</span>
				@endif
			@elseif ($widget->type==2)
				@if ($widget->partnerPad['video_categories']===0)
					<span data-toggle="tooltip" data-placement="bottom" title="Белая категория" style="font-weight: bold; cursor: pointer;">Б</span>
				@elseif ($widget->partnerPad['video_categories']===1)
					<span data-toggle="tooltip" data-placement="bottom" title="Адалт категория" style="font-weight: bold; cursor: pointer;">А</span>
				@elseif ($widget->partnerPad['video_categories']===2)
					<span data-toggle="tooltip" data-placement="bottom" title="Развлекательная категория" style="font-weight: bold; cursor: pointer;">Р</span>
				@endif
				<span>{{round($widget->videoCommisssion($widget->video['commission_rus']),0)}} и {{round($widget->videoCommisssion($widget->video['commission_cis']),0)}}</span></td>
			@endif
			@if ($widget->type==1)
				<!--{{($statistic=$widget->ProductStat($widget->id, $widget->product['id'], $from, $to))?"":""}}-->
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['clicked']:0}}</td>
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
				<td></td>
				<td></td>
				<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$widget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
				<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$widget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td class="text-center"><a href="{{ route('product_statistic_pid.pid_statistic', ['id'=>$widget->product['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
				<td class="text-center"><a href="{{ route('widget.delete', ['id'=>$widget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
				<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$widget->id])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
			@elseif ($widget->type==2)
				<!--{{($statistic=$widget->VideoStat($widget->video['id'], $from, $to))?"":""}}-->
				<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['calculate']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['clicks']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['util']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['second']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['second_summa']:0}}</td>
				<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$widget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
				<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$widget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td class="text-center"><a href="{{ route('video_statistic_pid.pid_statistic', ['id'=>$widget->video['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
				<td class="text-center"><a href="{{ route('widget.delete', ['id'=>$widget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
				<td class="text-center"></td>
			@endif
		</tr>
	<tr><td colspan="14" style="padding: 0; border: 0;">@include('admin.cabinet.get_code')</td></tr>
@endforeach