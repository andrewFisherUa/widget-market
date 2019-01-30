<tr>
	<td colspan="17">
		<div class="col-xs-2" style="margin: 5px 0;">
			<button data-toggle="modal" data-target="#add_user_site_product_{{$id}}" type="submit" class="btn btn-success">
				Добавить площадку
			</button>
		</div>
		<div class="col-xs-2" style="margin: 5px 0;">
			<button data-toggle="modal" data-target="#add_user_widget_product_{{$id}}" type="submit" class="btn btn-success">
				Создать виджет
			</button>
		</div>
		<div class="col-xs-2" style="margin: 5px 0;">
			<button data-toggle="modal" data-target="#add_user_dop_status_product_{{$id}}" type="submit" class="btn btn-success">
				Изменить доп. статус
			</button>
		</div>
		@if (\Auth::user()->hasRole('admin'))
			<div class="col-xs-2" style="margin: 5px 0;">
				<button data-toggle="modal" data-target="#add_default_on_users_product_{{$id}}" type="submit" class="btn btn-success">
					Назначить особые комиссии
				</button>
			</div>
			<div class="col-xs-5" style="margin: 5px 0;">
				<form class="form-inline" role="form" id="all_users_commission">
					{!! csrf_field() !!}
					<div class="col-xs-8 form-group">
						<label for="payment" class="col-xs-3 control-label" style="padding: 6px 0;">Сумма</label>
						<div class="col-xs-9">
							<input type="hidden" name="user_id" value="{{$id}}">
							<input name='payment' type="number" step="0.01" class="form-control">
						</div>
					</div>
					<div class="col-xs-2 input-group form-group">
						<a id="submit" class="btn btn-primary">Начислить</a>
					</div>
				</form>
			</div>
		@endif
		<div class="col-xs-5" style="margin: 5px 0;">
			<form class="form-inline" role="form" id="all_users_manager"">
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
					<a id="submit" class="btn btn-primary">Назначить</a>
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
	<td class="text-center">Наши клики</td>
	<td class="text-center">Выкуп</td>
	<td class="text-center">Ctr</td>
	<td class="text-center">Доход</td>
	<td class="text-center">Бонус за глубину</td>
	<td colspan="6"></td>
</tr>		
@foreach ($widgets as $widget)
	<tr>
		<td>
			@if ($widget->type==1)
				<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Товарный виджет" class="glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green widget-gl"></span>
			@elseif ($widget->type==2)
				@if ($widget->video['type']==1)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Автоплей виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
				@elseif ($widget->video['type']==2)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Оверлей виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
				@elseif ($widget->video['type']==3)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Vast" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
				@elseif ($widget->video['type']==4)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="InPage виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
				@endif
			@elseif ($widget->type==3)
				<span data-toggle="tooltip" data-placement="bottom" title="Тизерный виджет" class="glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green widget-gl"></span>
			@elseif ($widget->type==4)
				<span data-toggle="tooltip" data-placement="bottom" title="Брендирование" class="glyphicon glyphicon-picture affiliate_all_pads_domain_gliph green widget-gl"></span>
			@elseif ($widget->video['type']==5)
				<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Autoplay muted" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
			@endif
		</td>
		<td style="position: relative;">
			@if ($widget->type==1)
				Товарный № {{$widget->id}} {{$widget->partnerPad['domain']}}
			@elseif ($widget->type==2)
				@if ($widget->video['type']==1)
					Autoplay №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==2)
					Overlay №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==3)
					Vast №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==4)
					InPage №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==5)
					Autoplay muted №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==6)
					Fly-roll №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@elseif ($widget->video['type']==7)
					Fly-roll muted №{{$widget->video['id']}} {{$widget->partnerPad['domain']}}
				@endif
			@elseif ($widget->type==3)
				Тизерный № {{$widget->id}} {{$widget->partnerPad['domain']}}
			@elseif ($widget->type==4)
				Брендирование № {{$widget->id}} {{$widget->partnerPad['domain']}}
			@endif
			@if (strtotime(date('Y-m-d'))<strtotime(date('2018-01-01')))
								@if ((strtotime($widget->userProfile['created_at'])<strtotime(date('2017-12-01'))
									and 
									strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
									or ($widget->userProfile['referer'] 
									and 
									strtotime($widget->partnerPad['created_at'])>strtotime(date('2017-12-01'))))
									<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
									color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: absolute; right: 0; top: -5px">+10%</span>
								@endif
							@endif
							@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $widget->user_id)->first())
							@if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
								color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
							@endif
						@endif
						@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $widget->user_id)->first())
							@if (strtotime(date('Y-m-d'))<strtotime($widget->userProfile['created_at'])+3600*24*14)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
								color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
							@endif
						@endif
		</td>
		<td class="text-center">
			@if ($widget->type==1)
				@if ($widget->product['driver']==1)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="ТопАдверт" style="font-weight: bold; cursor: pointer;">Т</span>
				@elseif ($widget->product['driver']==2)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Яндекс" style="font-weight: bold; cursor: pointer;">Я</span>
				@endif
				<!--{{($statistic=$widget->ProductStat($widget->id, $widget->product['id'], $from, $to))?"":""}}-->
				@if ($statistic['clicked']>0 and $statistic['summa']>0)
					<span>{{round($statistic['summa'] / $statistic['clicked'],2)}}</span>
				@endif
			@elseif ($widget->type==2)
				@if ($widget->partnerPad['video_categories']===0)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Белая категория" style="font-weight: bold; cursor: pointer;">Б</span>
				@elseif ($widget->partnerPad['video_categories']===1)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Адалт категория" style="font-weight: bold; cursor: pointer;">А</span>
				@elseif ($widget->partnerPad['video_categories']===2)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Развлекательная категория" style="font-weight: bold; cursor: pointer;">Р</span>
				@endif
				<span>{{round($widget->videoCommisssion($widget->video['commission_rus']),0)}} и {{round($widget->videoCommisssion($widget->video['commission_cis']),0)}}</span></td>
			@elseif ($widget->type==3)
				<!--{{($statistic=$widget->ProductStat($widget->id, $widget->tizer['id'], $from, $to))?"":""}}-->
				@if ($statistic['clicked']>0 and $statistic['summa']>0)
					<span>{{round($statistic['summa'] / $statistic['clicked'],2)}}</span>
				@endif
			@elseif ($widget->type==4)
				<!--{{($statistic=$widget->BrandStat($widget->id, $from, $to))?"":""}}-->
				{{$statistic?$statistic['cpm']:0}}
			@endif
			@if ($widget->type==1)
				<!--{{($statistic=$widget->ProductStat($widget->id, $widget->product['id'], $from, $to))?"":""}}-->
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['clicked']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['our_clicks']:0}}</td>
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
				<td></td>
				<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$widget->id}}" class="get_code"><span  data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
				<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$widget->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Настройки"  target="_blank"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td class="text-center"><a href="{{ route('product_statistic_pid.pid_statistic', ['id'=>$widget->id])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
				<!--<td class="text-center"><a href="{{ route('widget.delete', ['id'=>$widget->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>-->
				<td class="text-center"><a class="delete_widget" data-set="{{$widget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
				<!--<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$widget->id])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>-->
				<td class="text-center"><a href="{{ route('advertiser.widget_statistic', ['widget_id'=>$widget->id])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
				<td></td>
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
				<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$widget->id}}" class="get_code"><span  data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
				<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$widget->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Настройки"  target="_blank"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td class="text-center"><a href="{{ route('video_statistic_pid.pid_statistic', ['id'=>$widget->video['id']])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
				<!--<td class="text-center"><a href="{{ route('widget.delete', ['id'=>$widget->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>-->
				<td class="text-center"><a class="delete_widget" data-set="{{$widget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
				<td class="text-center"><a href="{{ route('video_statistic.pid_urls_video_statistic', ['id'=>$widget->video['id']])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
				<td></td>
			@elseif ($widget->type==3)
				<!--{{($statistic=$widget->ProductStat($widget->id, $widget->tizer['id'], $from, $to))?"":""}}-->
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['clicked']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['our_clicks']:0}}</td>
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
				<td></td>
				<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$widget->id}}" class="get_code"><span data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
				<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$widget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td class="text-center"><a href="{{ route('teaser_statistic_pid.pid_statistic', ['id'=>$widget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<!--<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>-->
					<td class="text-center"><a class="delete_widget" data-set="{{$widget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
					<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$widget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
				@else
					<td class="text-center"><a class="delete_widget" data-set="{{$widget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
					<td></td>
				@endif
				<td></td>
			@elseif ($widget->type==4)
				<!--{{($statistic=$widget->BrandStat($widget->id, $from, $to))?"":""}}-->
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['showed']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['click']:0}}</td>
				<td class="text-center"></td>
				<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
				<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
				<td></td>
				<td></td>
				<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$widget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
				<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$widget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td class="text-center"><a href="{{ route('brand_statistic_pid.pid_statistic', ['id'=>$widget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center"><a class="delete_widget" data-set="{{$widget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
					<td class="text-center"><a href="{{route('brand_statistic.pid_url_statistic', ['id'=>$widget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
				@else
					<td class="text-center"><a class="delete_widget" data-set="{{$widget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
					<td></td>
				@endif
				<td></td>
			@endif
		</tr>
	<tr><td colspan="14" style="padding: 0; border: 0;">@include('admin.cabinet.get_code')</td></tr>
@endforeach