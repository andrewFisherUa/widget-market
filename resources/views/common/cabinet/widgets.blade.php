<form class="form-inline" id="user_wid" role="form" method="get" style="margin:5px 0">
	<div class="input-group col-xs-offset-2 col-xs-3 form-group">
		<span class="input-group-addon">С:</span>
		<input type="text" class="form-control" value="{{$from}}" name="from">
	</div>
	<div class="input-group col-xs-3 form-group">
		<span class="input-group-addon">По:</span>
		<input type="text" class="form-control" value="{{$to}}" name="to">
	</div>
	<div class="col-xs-2 input-group form-group">
		<a id="widget_submit" class="btn btn-primary">Применить</a>
	</div>
</form>


<div id="affiliate_all_widgets" @if ($user->hasRole('affiliate')) style="height: auto" @endif>
	@if (\Auth::user()->hasRole('admin') and $user->hasRole('affiliate') or \Auth::user()->hasRole('super_manager') and $user->hasRole('affiliate') or \Auth::user()->hasRole('manager') and $user->hasRole('affiliate'))
			<div class="col-xs-2" style="margin: 5px 0;">
				<button  data-toggle="modal" data-target="#add_affiliate_domain" type="submit" class="btn btn-success">
					Добавить площадку
				</button>
			</div>
			<div class="col-xs-2" style="margin: 5px 0;">
				<button data-toggle="modal" data-target="#add_affiliate_widget" type="submit" class="btn btn-success">
					Создать виджет
				</button>
			</div>
			<div class="col-xs-2" style="margin: 5px 0;">
				<button data-toggle="modal" data-target="#add_user_dop_status" type="submit" class="btn btn-success">
					Изменить доп. статус
				</button>
			</div>
		@if (\Auth::user()->hasRole('admin') or \Auth::user()->id==16)
			<div class="col-xs-3" style="margin: 5px 0;">
				<button data-toggle="modal" data-target="#add_default_on_users" type="submit" class="btn btn-success">
					Назначить особые комиссии
				</button>
			</div>
		@endif
		@if (\Auth::user()->hasRole('admin'))
			<div class="col-xs-5" style="margin: 5px 0;">
				<form class="form-inline" role="form" id="all_users_commission">
					{!! csrf_field() !!}
					<div class="col-xs-8 form-group">
						<label for="payment" class="col-xs-4 control-label" style="padding: 6px 0;">Сумма</label>
						<div class="col-xs-8">
							<input type="hidden" name="user_id" value="{{$user->id}}">
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
			<form class="form-inline" role="form" id="all_users_manager">
				{!! csrf_field() !!}
				<div class="col-xs-8 form-group">
					<label for="manager" class="col-xs-3 control-label" style="padding: 6px 0;">Менеджер</label>
					<div class="col-xs-9">
						<input type="hidden" name="user_id" value="{{$user->id}}">
						<!--{{$all_managers=\App\User::whereHas('roles', function ($query) {
						$query->whereIn('id', ['3','4','5']);
						})->orderBy('name', 'asc')->get()}}
						-->
						<select name='manager' class="form-control">
							<option value="0">Не выбран</option>
							@foreach($all_managers as $manager)
								<option @if ($user->profile->manager==$manager->id) selected @endif value="{{$manager->id}}">{{$manager->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-xs-2 input-group form-group">
					<a id="submit" class="btn btn-primary">Назначить</a>
				</div>
			</form>
		</div>
		@if (\Auth::user()->hasRole('admin'))
			<div class="col-xs-3" style="margin: 5px 0;">
				@if ($user->profile->lease==0)
				<button type="submit" data-set="{{$user->id}}" class="user_lease btn btn-success">
					Отметить как арендный
				</button>
				@else
				<button type="submit" data-set="{{$user->id}}" class="user_no_lease btn btn-success">
					Снять с арендны
				</button>
				@endif
			</div>
		@endif
	@endif
	<table class="table table-condensed table-hover widget-table">
		<thead>
			<tr class="text-center widget-table-header">
				<td>Тип</td>
				<td>Виджет</td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center">Ставка</td>
					<td class="text-center">Запросы</td>
				@else
					<td>Cpc</td>
					<td></td>
				@endif
				<td class="text-center">Показы</td>
				<td class="text-center">Клики</td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center">Выкуп</td>
				@else
					<td></td>
				@endif
				<td class="text-center">Ctr</td>
				<td class="text-center">Доход</td>
				<td class="text-center"  data-toggle="tooltip" data-placement="bottom" title="Премия за доп. показы за загрузку" style="cursor:pointer">Бонус за глубину</td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center">К. ботности</td>
					<td class="text-center">Видимость</td>
				@else
					<td></td>
					<td></td>
				@endif
				<td colspan="6"></td>
			</tr>
		</thead>
		@php
		if(($user->id==10818)){
		$user_all_sum->calculate+=$user_all_sum->second;
		}
		@endphp
		@if (count($partnerWidgets)>0)
			<tr style="background: #000; color: #fff">
				<td></td>
				<td>Всего</td>
				<td></td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center">{{$user_all_sum->loaded}}</td>
				@else
					<td></td>
				@endif
				<td class="text-center">{{$user_all_sum->calculate}}</td>
				<td class="text-center">{{$user_all_sum->clicks}}</td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center">{{$user_all_sum->util}}</td>
				@else
					<td></td>
				@endif
				<td class="text-center">{{$user_all_sum->ctr}}</td>
				<td class="text-center">
				@if ($user_all_sum->lease_summa>0)
					@if (\Auth::user()->hasRole('affiliate'))
					@else
						{{$user_all_sum->summa}}
					@endif
				@else
					{{$user_all_sum->summa}}
				@endif
				</td>
				<td class="text-center">
				@if ($user_all_sum->lease_summa>0)
					@if (\Auth::user()->hasRole('affiliate'))
					@else
					{{$user_all_sum->second_summa}}
					@endif
				@else
					{{$user_all_sum->second_summa}}
				@endif
				</td>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<td class="text-center">{{$user_all_sum->coef}}</td>
					<td class="text-center">{{$user_all_sum->viewable}}</td>
				@else
					<td></td>
					<td></td>
				@endif
				<td colspan='6'></td>
			</tr>
			@foreach ($partnerWidgets as $pwidget)
			
				<tr>
					<td>
						@if ($pwidget->type==1)
							<span data-toggle="tooltip" data-placement="bottom" title="Товарный виджет" class="glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green widget-gl"></span>
						@elseif ($pwidget->type==2)
							@if ($pwidget->video['type']==1)
								<span data-toggle="tooltip" data-placement="bottom" title="Автоплей виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@elseif ($pwidget->video['type']==2)
								<span data-toggle="tooltip" data-placement="bottom" title="Оверлей виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@elseif ($pwidget->video['type']==3)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Vast" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@elseif ($pwidget->video['type']==4)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="InPage виджет" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@elseif ($pwidget->video['type']==5)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Autoplay muted" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@elseif ($pwidget->video['type']==6)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Fly-roll" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@elseif ($pwidget->video['type']==7)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Fly-roll muted" class="glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
							@endif
						@elseif ($pwidget->type==3)
							<span data-toggle="tooltip" data-placement="bottom" title="Тизерный виджет" class="glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green widget-gl"></span>
						@elseif ($pwidget->type==4)
							<span data-toggle="tooltip" data-placement="bottom" title="Брендирование" class="glyphicon glyphicon-picture affiliate_all_pads_domain_gliph green widget-gl"></span>
						@endif
					</td>
					<td style="position: relative;" @if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')) class="erty" @endif>
						@if ($pwidget->type==1)
							Товарный № {{$pwidget->id}} {{$pwidget->partnerPad['domain']}}
						@elseif ($pwidget->type==2)
							@if ($pwidget->video['type']==1)
								Autoplay №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@elseif ($pwidget->video['type']==2)
								Overlay №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@elseif ($pwidget->video['type']==3)
								Vast №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@elseif ($pwidget->video['type']==4)
								InPage №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@elseif ($pwidget->video['type']==5)
								Autoplay muted №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@elseif ($pwidget->video['type']==6)
								Fly-roll №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@elseif ($pwidget->video['type']==7)
								Fly-roll muted №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
							@endif
							@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<!--{{($tool=$pwidget->VideoTooltip($pwidget->video['id']))?"":""}}-->
								<span data-toggle="tooltip" data-trigger="hover" data-html="true" data-placement="bottom" title="
								<table style='table-layout: fixed;'>
								<colgroup>
									<col span='3' style='width: 150px'>
								</colgroup>
									<tr style='border-bottom: solid 1px'>
										<td style='border-right: solid 1px'>Блоки</td>
										<td style='border-right: solid 1px'>Исключенные</td>
										<td style='border-right: solid 1px'>Добавленные</td>
										<td>Комментарий</td>
									</tr>
									<tr>
										<td style='border-right: solid 1px'>
											@foreach ($tool['block'] as $bl)
												@if ($bl){{$bl['name']}}<br>@endif
											@endforeach
										</td>
										<td style='border-right: solid 1px'>
											@foreach ($tool['exception'] as $ex)
												@if ($ex){{$ex['title']}}<br>@endif
											@endforeach
										</td>
										<td style='border-right: solid 1px'>
											@foreach ($tool['add'] as $add)
												@if ($add){{$add['title']}}<br>@endif
											@endforeach
										</td>
										<td>
											{{$pwidget->coment}}
										</td>
									</tr>
								</table>" class="glyphicon glyphicon-question-sign green"></span>
								
								<br>{{$pwidget->name}}
						@endif	
						@elseif ($pwidget->type==3)
							Тизерный № {{$pwidget->id}} {{$pwidget->partnerPad['domain']}}
						@elseif ($pwidget->type==4)
							Брендирование № {{$pwidget->id}} {{$pwidget->partnerPad['domain']}}
						@endif
							@if (strtotime(date('Y-m-d'))<strtotime(date('2018-01-01')))
								@if ((strtotime($pwidget->userProfile['created_at'])<strtotime(date('2017-12-01'))
									and 
									strtotime($pwidget->partnerPad['created_at'])>strtotime(date('2017-12-01'))) 
									or ($pwidget->userProfile['referer'] 
									and 
									strtotime($pwidget->partnerPad['created_at'])>strtotime(date('2017-12-01'))))
									<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
									color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: absolute; right: 0; top: -5px">+10%</span>
								@endif
							@endif
							@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $pwidget->user_id)->first())
							@if (strtotime(date('Y-m-d'))<strtotime($pwidget->userProfile['created_at'])+3600*24*14)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
								color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
							@endif
						@endif
						@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $pwidget->user_id)->first())
							@if (strtotime(date('Y-m-d'))<strtotime($pwidget->userProfile['created_at'])+3600*24*14)
								<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
								color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
							@endif
						@endif
						
					</td>
					<td class="text-center">
						@if ($pwidget->type==1)
							@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
								@if ($pwidget->product['driver']==1)
									<span data-toggle="tooltip" data-placement="bottom" title="ТопАдверт" style="font-weight: bold; cursor: pointer;">Т</span>
								@elseif ($pwidget->product['driver']==2)
									<span data-toggle="tooltip" data-placement="bottom" title="Яндекс" style="font-weight: bold; cursor: pointer;">Я</span>
								@endif
							@endif
							<!--{{($statistic=$pwidget->ProductStat($pwidget->id, $pwidget->product['id'], $from, $to))?"":""}}-->
							@if ($statistic['clicked']>0 and $statistic['summa']>0)
								<span>{{round($statistic['summa'] / $statistic['clicked'],2)}}</span>
							@endif
						@elseif ($pwidget->type==2)
							@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
								@if ($pwidget->partnerPad['video_categories']===0)
									<span data-toggle="tooltip" data-placement="bottom" title="Белая категория" style="font-weight: bold; cursor: pointer;">Б</span>
								@elseif ($pwidget->partnerPad['video_categories']===1)
									<span data-toggle="tooltip" data-placement="bottom" title="Адалт категория" style="font-weight: bold; cursor: pointer;">А</span>
								@elseif ($pwidget->partnerPad['video_categories']===2)
									<span data-toggle="tooltip" data-placement="bottom" title="Развлекательная категория" style="font-weight: bold; cursor: pointer;">Р</span>
								@endif
								@if ($pwidget->video['control_rus']==1 or $pwidget->video['control_cis']==1)
								<span style="color: #a51313;font-weight: bold;">K</span>
								@else
								<span>{{round($pwidget->videoCommisssion($pwidget->video['commission_rus']),0)}} и {{round($pwidget->videoCommisssion($pwidget->video['commission_cis']),0)}}</span>
								@endif
							@endif
						@elseif ($pwidget->type==3)
							<!--{{($statistic=$pwidget->ProductStat($pwidget->id, $pwidget->tizer['id'], $from, $to))?"":""}}-->
							@if ($statistic['clicked']>0 and $statistic['summa']>0)
								<span>{{round($statistic['summa'] / $statistic['clicked'],2)}}</span>
							@endif
						@elseif ($pwidget->type==4)
							<!--{{($statistic=$pwidget->BrandStat($pwidget->id, $from, $to))?"":""}}-->
							{{$statistic?$statistic['cpm']:0}}
						@endif
					</td>
					@if ($pwidget->type==1)
						<!--{{($statistic=$pwidget->ProductStat($pwidget->id, $pwidget->product['id'], $from, $to))?"":""}}-->
						<td class="text-center"></td>
						<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['clicked']:0}}</td>
						<td class="text-center"></td>
						<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
						<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
							{{--<td class="text-center"><a href="{{ route('product_statistic_pid.pid_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>--}}
						
                        <td class="text-center"><a href="{{ route('rekrut_product.nextstat.wid', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>						
						
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<!--<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>-->
							<td class="text-center"><a class="delete_widget" data-set="{{$pwidget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
							<!--<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>-->
							<td class="text-center"><a href="{{ route('advertiser.widget_statistic', ['widget_id'=>$pwidget->id])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
							<td></td>
						@else
							<td class="text-center"><a class="delete_widget" data-set="{{$pwidget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
							<td class="text-center"><a href="{{ route('advertiser.widget_statistic', ['widget_id'=>$pwidget->id])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
							<td></td>
						@endif
					@elseif ($pwidget->type==2)
						<!--{{($statistic=$pwidget->VideoStat($pwidget->video['id'], $from, $to))?"":""}}-->
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
						@else
							<td></td>
						@endif
							@php
		if(($user->id==10818 && $statistic['calculate'])){
		$statistic['calculate']+=$statistic['second'];
		}
@endphp
						<td class="text-center">{{$statistic?$statistic['calculate']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['clicks']:0}}</td>
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<td class="text-center">{{$statistic?$statistic['util']:0}}</td>
						@else
							<td></td>
						@endif
						<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
						<td class="text-center">
						@if ($statistic['lease_summa']>0)
							@if (\Auth::user()->hasRole('affiliate'))
							@else
								{{$statistic?$statistic['summa']:0}}
							@endif
						@else
							{{$statistic?$statistic['summa']:0}}
						@endif
						</td>
						<td class="text-center">
						@if ($statistic['lease_summa']>0)
							@if (\Auth::user()->hasRole('affiliate'))
							@else
								{{$statistic?$statistic['second_summa']:0}}
							@endif
						@else
							{{$statistic?$statistic['second_summa']:0}}
						@endif
						</td>
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<td class="text-center">{{$statistic?$statistic['coef']:0}}</td>
							<td class="text-center">{{$statistic?$statistic['viewable']:0}}</td>
						@else
							<td></td>
							<td></td>
						@endif
						<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
						<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
						<td class="text-center"><a href="{{ route('video_statistic_pid.pid_statistic', ['id'=>$pwidget->video['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<!--<td class="text-center"><a href="" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>-->
							<td class="text-center">{{--<a class="delete_widget" data-set="{{$pwidget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a>--}}</td>
							<td class="text-center"><a href="{{ route('video_statistic.pid_urls_video_statistic', ['id'=>$pwidget->video['id']])}}" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
							<td></td>
						@else
							<td class="text-center">{{--<a class="delete_widget" data-set="{{$pwidget->id}}" style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a>--}}</td>
							<td></td>
							<td></td>
						@endif
					@elseif ($pwidget->type==3)
						<!--{{($statistic=$pwidget->ProductStat($pwidget->id, $pwidget->tizer['id'], $from, $to))?"":""}}-->
						<td class="text-center"></td>
						<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['clicked']:0}}</td>
						<td class="text-center"></td>
						<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
						<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
						<td class="text-center"><a href="{{ route('teaser_statistic_pid.pid_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<!--<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>-->
							<td class="text-center"></td>
							<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
							<td></td>
						@else
							<td class="text-center"></td>
							<td></td>
							<td></td>
						@endif
					@elseif ($pwidget->type==4)
						<!--{{($statistic=$pwidget->BrandStat($pwidget->id, $from, $to))?"":""}}-->
						<td class="text-center"></td>
						<td class="text-center">{{$statistic?$statistic['showed']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['click']:0}}</td>
						<td class="text-center"></td>
						<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
						<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
						<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
						<td class="text-center"><a href="{{ route('brand_statistic_pid.pid_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							<td class="text-center"></td>
							<td class="text-center"><a href="{{route('brand_statistic.pid_url_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
							<td></td>
						@else
							<td class="text-center"></td>
							<td></td>
							<td></td>
						@endif
					@endif
				</tr>
				<tr><td colspan="14" style="padding: 0; border: 0;">@include('affiliate.cabinet.get_code')</td></tr>
			
			@endforeach
		@else
			<div class="no_manager text-center" style="clear:both">После одобрения площадки, Вы сможете создавать виджеты которые будут отображаться здесь.<br>
				Для создания нового виджета нажмите на зеленый плюс в этом блоке.
			</div>
		@endif
	</table>
</div>




@include ('common.cabinet.modal.add_widget')
@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
	@include ('common.cabinet.modal.add_user_dop_status_admin')
	@if (\Auth::user()->hasRole('admin'))
		@include('common.cabinet.modal.add_default_on_users_admin')
	@endif
@endif
