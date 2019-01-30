@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{{ session('message_danger') }}
		</div>
	@endif
        <div class="col-xs-3">
			<div class="affiliate_cabinet_block text-center">
				<div class="heading text-left">Мой профиль</div>
				<hr class="affilaite_hr">
				<div class="home_avatar_block">
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
						@if (strtotime(date('Y-m-d H:i:s')) < strtotime($user->updated_at)+900)
							<span data-toggle="tooltip" data-placement="bottom" title="В сети" class="home_avatar_green"></span>
						@else
							<span data-toggle="tooltip" data-placement="bottom" title="Был в сети {{$user->updated_at}}" class="home_avatar_red"></span>
						@endif
					@endif
				@if ($user->profile->avatar)
					<img src="/images/avatars/{{$user->profile->avatar}}" class="img-circle cabinet_avatar">
				@else
					<img src="/images/cabinet/no_foto.png" class="img-circle cabinet_avatar">
				@endif
				</div>
				<div class="affiliate_name">{{$user->name}}</div>
				<div class="affiliate_role">@if ($user->hasRole('affiliate'))Вебмастер@elseif ($user->hasRole('advertiser'))Рекламодатель@elseif($user->hasRole('manager'))Менеджер@elseif($user->hasRole('super_manager'))Ст. менеджер@elseif($user->hasRole('admin'))Администратор@endif</div>
				<div class="affiliate_email">{{$user->email}}</div>
				<div class="cabinet_gliph">
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<a href="{{ route('admin.profile.personal', ['id_user'=>$userProf->user_id]) }}" data-toggle="tooltip" data-placement="bottom" title="Профиль"><span class="glyphicon glyphicon-user gliph_affiliate"></span></a>
					@else
					<a href="{{ route('profile.personal') }}" data-toggle="tooltip" data-placement="bottom" title="Профиль"><span class="glyphicon glyphicon-user gliph_affiliate"></span></a>
					@endif
					<a href="{{ route('news.all') }}" data-toggle="tooltip" data-placement="bottom" @if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))) title="Не прочитаных новостей: {{count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))}}" @else title="Новости" @endif class="home_news"><span class="glyphicon glyphicon-envelope gliph_affiliate"></span>@if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))) @if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))>9)<span class="count_news">9+</span> @else <span class="count_news">{{count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))}}</span> @endif @endif</a>
					<a href="{{ route('logout') }}" data-toggle="tooltip" data-placement="bottom" title="Выйти" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-log-out gliph_affiliate"></span></a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
				</div>
			</div>
		</div>
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мой баланс 
				@if ($userProf->user->hasRole('manager') or $userProf->user->hasRole('super_manager')) 
					<a href="{{route('managers.history', ['id'=>$userProf->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика" class="glyphicon glyphicon-question-sign color-blue manager_help" target="_blank"></a>
				@endif
				</div>
				<hr class="affilaite_hr">
				<div class="affiliate_balance text-center">@if ($userProf->balance==0)0.00 @else{{$userProf->balance}}@endif <span class="rur">q</span>
				@if ($userProf->auto_payment==1)
					<span class="glyphicon glyphicon-exclamation-sign color-red" style="font-size: 20px; line-height: 1; position: relative; top: -6px;" data-toggle="tooltip" data-placement="bottom" title="Включен автозаказ выплат"></span>
				@endif
				</div>
				<p class="text-center"><a href="#" data-toggle="modal" data-target="#payment" class="btn btn-primary" role="button">Заказать выплату</a></p>
				<p class="text-center"><a href="#" data-toggle="modal" data-target="#auto_payment" class="btn btn-success" role="button">Автозаказ выплат</a></p>
				<!--{{$today=\App\Transactions\BalanceOnHome::where('user_id', $userProf->id)->where('day', date("Y-m-d"))->first()}}-->
				<!--@if ($today)
				{{$today_balance=round($today->video_commission+$today->product_commission+$today->referal_commission+$today->manager_commission,2)}}
				@else
				{{$today_balance=0.00}}
				@endif-->
				<!--{{$yesterday=\App\Transactions\UserTransactionLog::where('user_id', $userProf->id)->where('day', date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS")))->first()}}-->
				<!--@if ($yesterday)
				{{$yesterday_balance=round($yesterday->commission,2)}}
				@else
				{{$yesterday_balance=0.00}}
				@endif-->
				<!--{{$week=\App\Transactions\UserTransactionLog::where('user_id', $userProf->id)->whereBetween('day', [date("Y-m-d",strtotime(date("Y-m-d")." - 8 DAYS")), date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS"))])->sum('commission')}}-->
				<!--@if ($week)
				{{$week_balance=round($week,2)}}
				@else
				{{$week_balance=0.00}}
				@endif-->
				<!--{{$month=\App\Transactions\UserTransactionLog::where('user_id', $userProf->id)->whereBetween('day', [date("Y-m-d",strtotime(date("Y-m-d")." - 31 DAYS")), date("Y-m-d",strtotime(date("Y-m-d")." - 1 DAYS"))])->sum('commission')}}-->
				<!--@if ($month)
				{{$month_balance=round($month,2)}}
				@else
				{{$month_balance=0.00}}
				@endif-->
				<div class="affiliate_detal_balance"><span>Сегодня:</span><div class="right green">
					+ {{$today_balance}}
				<span class="rur"> q</span></div></div>
				<div class="affiliate_detal_balance"><span>Вчера:</span><div class="right">{{$yesterday_balance}} <span class="rur">q</span></div></div>
				<div class="affiliate_detal_balance"><span>Неделя:</span><div class="right">{{$week_balance}} <span class="rur">q</span></div></div>
				<div class="affiliate_detal_balance"><span>Месяц:</span><div class="right">{{$month_balance}} <span class="rur">q</span></div></div>			
			</div>
			@include('affiliate.cabinet.payment')
			@include('affiliate.cabinet.auto_payment')
		</div>
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мои уведомления</div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
					<div class="panel-group" id="notif_accordion">
						@if (count($user_notif)>0)
						@foreach ($user_notif as $notif)
							@if (count($user->unreadNotifications->where('type', '<>', 'App\Notifications\NewNews')->where('data', $notif->id)))
							<div class="panel panel-default">
								<div class="news_block_all news_block_green">
									<div class="all-news">
										<span class="glyphicon glyphicon-info-sign news-gliph-all color-blue"></span>
										<a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$notif->id}}" class="news-all-header">{{str_limit($notif->header,15)}}</a>
										<a class="remove_notif" href="{{ route('remove_notification', ['id'=>$notif->id])}}"><span class="glyphicon glyphicon-remove"></span></a>
									</div>
								</div>
								<div id="collapse{{$notif->id}}" class="panel-collapse collapse">
									<div class="panel-body">
									{{$notif->header}}
									<hr class="affilaite_hr">
									{{$notif->body}}
									</div>
								</div>
							</div>
							@endif
						@endforeach
						@else
							<div class="no_manager text-center" >У Вас пока что нет новых уведомлений.</div>
						@endif
					</div>
			</div>
		</div>
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Последние новости <a href="{{ route('news.add') }}" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-placement="bottom" title="Добавить новость"></span></a></div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
				<div id="cabinet_news">
				@if (count($news_lim)>0)
				@foreach ($news_lim as $n)
					<div class="col-xs-12 col-xs-12" style="margin-bottom: 15px;">
						<a href="{{url ('news/'.$n->id)}}" class="new">
						<div class="news_block_all 
						@if ($n->important==1) 
							@if(count($user->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
								news_block_green_no_read
							@else
								news_block_green
							@endif
						 @elseif ($n->important==2) 
							@if(count($user->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
								news_block_orange_no_read
							@else
								news_block_orange
							@endif
						 @elseif($n->important==3) 
							@if(count($user->unreadNotifications->where('type', 'App\Notifications\NewNews')->where('data', $n->id)))
								news_block_red_no_read
							@else
								news_block_red
							@endif
						 @endif">
							<div class="all-news">
								@if ($n->type==1)
									<span class="glyphicon glyphicon-info-sign news-gliph-all color-blue"></span>
								@elseif ($n->type==2)
									<span class="glyphicon glyphicon-bell news-gliph-all color-green"></span>
								@elseif ($n->type==3)
									<span class="glyphicon glyphicon-fire news-gliph-all color-red"></span>
								@elseif ($n->type==4)
									<span class="glyphicon glyphicon-usd news-gliph-all color-purple"></span>
								@endif
								<span class="news-all-header">{{str_limit($n->header,15)}}</span>
							</div>
						</div>
					</a>
				</div>
				@endforeach
				@else
					<div class="no_manager text-center">Извините, в данный момент для Вас нет новостей.</div>
				@endif
				</div>
			</div>
		</div>
    </div>
	<div class="row" style="margin-top: 30px;">
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мои площадки <a href="#" data-toggle="modal" data-target="#add_affiliate_domain" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-placement="bottom" title="Добавить площадку"></span></a></div>
				<hr class="affilaite_hr">
				<div id="affiliate_all_pads">
				@if (count($partnerPads)>0)
				@foreach ($partnerPads as $pad)
						<div class="affiliate_pad">
							<div data-toggle="tooltip" data-placement="bottom" title="{{$pad->domain}}" class="affiliate_all_pads_domain">
								{{$pad->domain}}
							</div>
							@if ($pad->status==0)
							<span data-toggle="tooltip" data-placement="bottom" title="На модерации" class="glyphicon glyphicon-time affiliate_all_pads_domain_gliph blue"></span>
							@elseif ($pad->status==2)
							<span data-toggle="tooltip" data-placement="bottom" title="Отклонена" class="glyphicon glyphicon-remove-circle affiliate_all_pads_domain_gliph red"></span>
							@elseif ($pad->status==1)
								@if ($pad->type==1 or $pad->type==3)
									<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на товарный виджет" class="glyphicon glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green"></span>
								@endif
								@if ($pad->type==2 or $pad->type==3)
									<span data-toggle="tooltip" data-placement="bottom" title="Одобрена на видео виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green"></span>
								@endif
							@endif
							<a href="" data-toggle="modal" data-target="#edit_affiliate_domain_{{$pad->id}}">
								<span data-toggle="tooltip" data-placement="bottom" title="Редактировать" class="glyphicon glyphicon glyphicon glyphicon glyphicon-cog affiliate_all_pads_domain_gliph blue pads_config"></span>
							</a>
						</div>
						@include('affiliate.cabinet.edit_affiliate_domain')
				@endforeach
				@else
					<div class="no_manager text-center">После добавления площадки, она будет отображаться здесь. Для добавления площадки нажмите на зеленый плюс в этом блоке.</div>
				@endif
				</div>
			</div>
		</div>
		<!--<div class="col-xs-3 col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Эффективность площадок за сегодня</div>
				<hr class="affilaite_hr">
				{!! $chart->render() !!}
			</div>
		</div>-->
		<div class="col-xs-9">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мои виджеты <a href="#" data-toggle="modal" data-target="#add_affiliate_widget" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-placement="bottom" title="Создать виджет"></span></a></div>
				<hr class="affilaite_hr">
				<form class="form-inline" role="form" method="get"style="margin:5px 0">
					<div class="input-group col-xs-offset-2 col-xs-3 form-group">
						<span class="input-group-addon">С:</span>
						<input type="text" class="form-control" value="{{$from}}" name="from">
					</div>
					<div class="input-group col-xs-3 form-group">
						<span class="input-group-addon">По:</span>
						<input type="text" class="form-control" value="{{$to}}" name="to">
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</form>
				<div id="affiliate_all_widgets">
					<table class="table table-condensed table-hover widget-table">
						@if (count($partnerWidgets)>0)
						<thead>
							<tr class="text-center widget-table-header">
								<td></td>
								<td></td>
								@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
								<td>Запросы</td>
								@endif
								<td>Показы</td>
								@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
								<td>Выкуп</td>
								@endif
								<td>Ctr</td>
								<td>Доход</td>
								<td colspan="4">Действие</td>
							</tr>
						</thead>
						@foreach ($partnerWidgets as $pwidget)
							<tr>
								<td>
									@if ($pwidget->type==1)
										<span data-toggle="tooltip" data-placement="bottom" title="Товарный виджет" class="glyphicon glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green widget-gl"></span>
									@elseif ($pwidget->type==2)
										@if ($pwidget->video['type']==1)
											<span data-toggle="tooltip" data-placement="bottom" title="Автоплей виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
										@elseif ($pwidget->video['type']==2)
											<span data-toggle="tooltip" data-placement="bottom" title="Оверлей виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
										@endif
									@endif
								</td>
								<td>
									@if ($pwidget->type==1)
										Товарный № {{$pwidget->id}} {{$pwidget->partnerPad['domain']}}
									@elseif ($pwidget->type==2)
										@if ($pwidget->video['type']==1)
											Autoplay №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
										@elseif ($pwidget->video['type']==2)
											Overlay №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
										@elseif ($pwidget->video['type']==3)
											Vast №{{$pwidget->video['id']}} {{$pwidget->partnerPad['domain']}}
										@endif
									@endif
								</td>
								<td class="text-center">
									@if ($pwidget->type==1)
										@if ($pwidget->product['driver']==1)
											<span data-toggle="tooltip" data-placement="bottom" title="ТопАдверт" style="font-weight: bold; cursor: pointer;">Т</span>
										@elseif ($pwidget->product['driver']==2)
											<span data-toggle="tooltip" data-placement="bottom" title="Яндекс" style="font-weight: bold; cursor: pointer;">Я</span>
										@endif
										<!--{{($statistic=$pwidget->ProductStat($pwidget->id, $pwidget->product['id'], $from, $to))?"":""}}-->
										@if ($statistic['clicked']>0 and $statistic['summa']>0)
											<span>{{round($statistic['summa'] / $statistic['clicked'],2)}}</span>
										@endif
									@elseif ($pwidget->type==2)
										@if ($pwidget->partnerPad['video_categories']===0)
											<span data-toggle="tooltip" data-placement="bottom" title="Белая категория" style="font-weight: bold; cursor: pointer;">Б</span>
										@elseif ($pwidget->partnerPad['video_categories']===1)
											<span data-toggle="tooltip" data-placement="bottom" title="Адалт категория" style="font-weight: bold; cursor: pointer;">А</span>
										@elseif ($pwidget->partnerPad['video_categories']===2)
											<span data-toggle="tooltip" data-placement="bottom" title="Развлекательная категория" style="font-weight: bold; cursor: pointer;">Р</span>
										@endif
										<span>{{round($pwidget->videoCommisssion($pwidget->video['commission_rus']),0)}} и {{round($pwidget->videoCommisssion($pwidget->video['commission_cis']),0)}}</span></td>
									@endif
									@if ($pwidget->type==1)
										<!--{{($statistic=$pwidget->ProductStat($pwidget->id, $pwidget->product['id'], $from, $to))?"":""}}-->
										<td class="text-center"></td>
										<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['clicked']:0}}</td>
										<td class="text-center"></td>
										<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
										<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
										<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
										<td class="text-center"><a href="{{ route('product_statistic_pid.pid_statistic', ['id'=>$pwidget->product['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
										<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
										<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
									@elseif ($pwidget->type==2)
										<!--{{($statistic=$pwidget->VideoStat($pwidget->video['id'], $from, $to))?"":""}}-->
										<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['calculate']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['clicks']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['util']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
										<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
										<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
										<td class="text-center"><a href="{{ route('video_statistic_pid.pid_statistic', ['id'=>$pwidget->video['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
										<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
										<td class="text-center"></td>
									@endif
								</tr>
							<tr><td colspan="14" style="padding: 0; border: 0;">@include('affiliate.cabinet.get_code')</td></tr>
						@endforeach
						@else
							<div class="no_manager text-center" >После одобрения площадки, Вы сможете создавать виджеты которые будут отображаться здесь.<br>
							Для создания нового виджета нажмите на зеленый плюс в этом блоке.
							</div>
						@endif
					</table>
				</div>
			</div>
		</div>
	</div>
	@if ($user->hasRole('admin') or $user->hasRole('super_manager'))
	<div class="row" style="margin-top: 30px;">
		<div class="col-xs-6">
			<div class="affiliate_cabinet_block text-center">
			@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager'))
			<div class="heading text-left">Запросы видео за последние 6 часов<a href="{{ route('video_statistic.new_graph') }}" target="_blank" class="affiliate_add_domain"><span class="glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="bottom" title="Полный график"></span></a></div>
			<hr class="affilaite_hr">
			{!! $graph->render() !!}
			@endif
			</div>
		</div>
		<div class="col-xs-6">
			<div class="affiliate_cabinet_block text-center">
			@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager'))
			<div class="heading text-left">Показы по товарному виджету за последние 6 часов<a href="{{ route('product_statistic.product_graph')}}" target="_blank" class="affiliate_add_domain"><span class="glyphicon glyphicon-th-list" data-toggle="tooltip" data-placement="bottom" title="Полный график"></span></a></div>
			<hr class="affilaite_hr">
			{!! $graphProduct->render() !!}
			@endif
			</div>
		</div>
	</div>
	@endif
	<div class="row" style="margin-top: 30px;">
		<div class="col-xs-12">
			<!--{{$all_managers=\App\User::whereHas('roles', function ($query) {
			$query->whereIn('id', ['3','4','5']);
			})->orderBy('name', 'asc')->get()}}-->
			<div class="row" style="margin: 10px 0">
				<div class="col-xs-12">
					<form class="form-inline" role="form" method="get" action="">
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">С:</span>
							<input type="text" id="from_for_users" class="form-control" value="{{$from}}" name="from">
						</div>
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">По:</span>
							<input type="text" id="to_for_users" class="form-control" value="{{$to}}" name="to">
						</div>
						<div class="input-group col-xs-3 form-group">
							<span class="input-group-addon">Поиск:</span>
							<input type="text" class="form-control" id="search_clent" value="{{$search}}" name="search">
						</div>
						
						@if (\Auth::user()->hasRole('admin'))
						<div class="input-group col-xs-2 form-group">
							<select name="manager_for_client" id="manager_for_client" class="form-control">
								<option @if ($manager_for_client=='0') selected @endif value="0">Все менеджеры</option>
								<option @if ($manager_for_client=='no_manager') selected @endif value="no_manager">Без менеджера</option>
								@foreach ($all_managers as $manager)
									<option @if ($manager_for_client==$manager->id) selected @endif value="{{$manager->id}}">{{$manager->name}}</option>
								@endforeach
							</select>
						</div>
						@elseif (\Auth::user()->hasRole('super_manager'))
						<div class="input-group col-xs-2 form-group">
							<select name="manager_for_client" id="manager_for_client" class="form-control">
								<option @if ($manager_for_client=='0') selected @endif value="0">Мои клиенты</option>
								<option @if ($manager_for_client=='no_manager') selected @endif value="no_manager">Без менеджера</option>
							</select>
						</div>
						@endif
						<div class="input-group col-xs-1 form-group">
							<select name="number" id="number_for_client" class="form-control">
								<option @if ($number==5) selected @endif value="5">5</option>
								<option @if ($number==10) selected @endif value="10">10</option>
								<option @if ($number==15) selected @endif value="15">15</option>
								<option @if ($number==20) selected @endif value="20">20</option>
								<option @if ($number==30) selected @endif value="30">30</option>
								<option @if ($number==50) selected @endif value="50">50</option>
								<option @if ($number==100) selected @endif value="100">100</option>
							</select>
						</div>
						<div class="col-xs-1 input-group form-group">
							<button type="submit" class="btn btn-primary">Применить</button>
						</div>
					</form>
				</div>
			</div>
			<div class="row" style="margin-top: 10px; margin-left: 0px;">
				<div class="col-xs-12">
					<a href="{{route('global.trash_users')}}" target="_blank" class="btn btn-primary">Не активные юзеры</a>
				</div>
			</div>
			{!! $allUsersActive->appends(["direct"=>$direct, "order"=>$order, "number"=>$number, "from"=>$from, "to"=>$to, "search"=>$search, "manager_for_client"=>$manager_for_client])->render() !!}
			<div class="affiliate_cabinet_bot" style="margin-top: 10px;">
				<ul class="nav nav-tabs nav-justified cust-tabs" role="tablist" id="myTabs">
					<li role="presentation" class="active"><a href="#all_stat" aria-controls="all_stat" role="tab" data-toggle="tab">Суммарная</a></li>
					<li role="presentation"><a id="get_users_video_widgets" href="#video_stat" aria-controls="video_stat" role="tab" data-toggle="tab">Видео</a></li>
					<li role="presentation"><a id="get_users_product_widgets" href="#product_stat" aria-controls="product_stat" role="tab" data-toggle="tab">Товарка</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="all_stat">
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
									<td class="text-center">{{$all_sum->loaded}}</td>
									<td class="text-center">{{$all_sum->calculate}}</td>
									<td class="text-center">{{$all_sum->clicks}}</td>
									<td class="text-center">{{$all_sum->util}}</td>
									<td class="text-center">{{$all_sum->ctr}}</td>
									<td class="text-center">{{$all_sum->summa}}</td>
									<td class="text-center">{{$all_sum->second}}</td>
									<td class="text-center">{{$all_sum->second_summa}}</td>
									<td colspan='5'></td>
								</tr>
							</tbody>
								@foreach ($allUsersActive as $userActive)
								<tbody>
									<tr>
										<td>
											<a data-toggle="collapse" data-parent="#accordion" href="#us-{{$userActive->user_id}}">
												<span data-set="{{$userActive->user_id}}" class="glyphicon glyphicon-plus plus_us_bottom plus_all"></span>
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
										<td class="text-center">{{$userActive->second}}</td>
										<td class="text-center">{{$userActive->second_summa}}</td>
										<td colspan='5'>
										@if ($userActive->dop_status==1)
											<img src="/images/smail/green.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
										@elseif ($userActive->dop_status==2)
											<img src="/images/smail/yellow.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
										@elseif ($userActive->dop_status==3)
											<img src="/images/smail/red.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
										@endif
										<!-- {{$coms=\App\VideoDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
										<!-- {{$Productcoms=\App\ProductDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
										<!--{{$controlcommissions=\App\UserLinkSumma::where('user_id', $userActive->user_id)->get()}}-->
										<!-- {{ $links=\App\VideoSource::orderBy('title', 'asc')->get() }} -->
										@if (count($coms)>0 or count($Productcoms)>0 or count($controlcommissions)>0)
											<span class="glyphicon glyphicon-exclamation-sign default_status" style="color: #ff6a00; font-size: 20px; top: 2px; cursor: pointer;"
												data-container="body" data-toggle="popover" tabindex="0" data-trigger="focus" data-placement="bottom" data-content="
												@foreach ($coms as $com)
													@if ($com->wid_type==1) Автоплей @elseif($com->wid_type==2) Оверлей @endif @if($com->pad_type==0) Белый @elseif ($com->pad_type==1) Адалт @else($com->pad_type==2) Развлек. @endif {{round($com->videoCommisssion($com->commission_rus),2)}} и {{round($com->videoCommisssion($com->commission_cis),2)}}<br>
												@endforeach
												@foreach ($Productcoms as $comm)
													Товарный @if ($comm->driver==1) (ТопАдверт) @elseif ($comm->driver==2) (Яндекс) @endif {{round($comm->commission,2)}}<br>
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
										<a href="{{route('admin.user_active', ['id_user'=>$userActive->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Отметить как активный клиент" style="float: right"><span class="glyphicon glyphicon-eye-open color-green"></span></a>
										@else
										<a href="{{route('admin.user_no_active', ['id_user'=>$userActive->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Отметить как неактивный клиент" style="float: right"><span class="glyphicon glyphicon-eye-close color-red"></span></a>
										@endif
										</td>
									</tr>
								</tbody>
									<tbody id="us-{{$userActive->user_id}}" class="panel-collapse vlogen-tbody collapse">
																
									</tbody>
								@endforeach
						</table>
					</div>
					<div role="tabpanel" class="tab-pane" id="video_stat">
						
					</div>
					<div role="tabpanel" class="tab-pane" id="product_stat">
						
					</div>
				</div>
				@foreach ($allUsersActive as $userActive)
					@include('admin.cabinet.add_user_site')
					@include('admin.cabinet.add_user_widget')
					@include('admin.cabinet.add_user_dop_status')
					@if (\Auth::user()->hasRole('admin'))
						@include('admin.cabinet.add_video_default_on_users')
					@endif
				@endforeach
			</div>
			{!! $allUsersActive->appends(["direct"=>$direct, "order"=>$order, "number"=>$number, "from"=>$from, "to"=>$to, "search"=>$search, "manager_for_client"=>$manager_for_client])->render() !!}
		</div>
	</div>
</div>
@include ('affiliate.cabinet.add_affiliate_domain')
@include ('affiliate.cabinet.add_affiliate_widget')
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<link href="{{ asset('css/news.css') }}" rel="stylesheet">
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	{!! Charts::assets() !!}
	<style>
		.manager_help{
			float: right;
			cursor: pointer;
			font-size: 21px;
			line-height: 14px;
			transition: 0.2s;
		}
		.manager_help:focus, .manager_help:active, .manager_help:hover{
			outline: none;
			 text-decoration: none;
		}
		.users_cabinet_block{
			height: 390px!important;
		}
		#usersActive, #usersNoActive{
			height: 350px;
			overflow: hidden;
		}
		.one_user{
			height: 20px;
		}
		.users_name{
			float: left;
			margin-left: 10px;
			width: 200px;
			overflow: hidden;
			text-align: left;
			text-overflow: ellipsis;
			overflow: hidden;
			white-space: pre;
			text-overflow: ellipsis;
		}
		.users_status{
			float: right;
			margin-right: 10px;
		}
		#notif_accordion{
		    height: 247px;
			overflow: hidden;
			margin: 0 15px;
		}
		#notif_accordion .panel{
			margin-bottom: 13px;
		}
		.remove_notif{
			color: rgb(181, 0, 0);
		}
		.nav > li > a{
			padding: 4px 15px;
			border-radius: 0!important;
		}
		.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
			border-bottom:none!important;
		}
		#affiliate_all_widgets {
			height: 210px;
			overflow: hidden;
		}
		.affiliate_cabinet_bot{
		    border: 1px solid #cacaca;
			background-image: url(/images/cabinet/background_block.png);
			background-color: rgba(199, 199, 199, 0.5);
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
		}
		.affiliate_cabinet_block > .heading {
			font-size: 11px;
			text-transform: uppercase;
			margin: 8px;
			padding: 0;
			height: 14px;
		}
		.plus_us_bottom{
		    font-size: 21px;
			margin: 0 5px;
		}
		.vlogen-tbody{
			background: rgba(255, 255, 255, 0.85);
		}
		.get_code:hover, .get_code:focus, .get_code:active{
		    outline: 0!important;
		}
		.default_status:focus, .default_status:active{
			outline: none!important;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
			$('.default_status').popover({html : true});
		});
	</script>
	<script src="{{ asset('js/custom_scroll/jquery.custom-scroll.min.js') }}"></script>
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
	<script src="https://cdn.rawgit.com/zenorocha/clipboard.js/master/dist/clipboard.min.js"></script>
	<script>
		new Clipboard('.copy-all');
	</script>
	<script>
	$('#affiliate_all_pads').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -32,
  vertical: true,
  horizontal: false
});
$('#affiliate_all_widgets').customScroll({
  offsetTop: 78,
  offsetRight: 16,
  offsetBottom: -78,
  vertical: true,
  horizontal: false
});
	$('#cabinet_news').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
$('#usersActive').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
$('#usersNoActive').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
$('#notif_accordion').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
	</script>
<script>	
$(function() {
    $('input[name="from"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
	$('input[name="to"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
});
$(document).ready(function() {
	$('.pad_for_widget').change(function(){
		var user_parent=$(this).parents('.user_add_widget');
		if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
		user_parent.find($('.type_for_widget')).css('display', 'block');
		user_parent.find($('.pod_type_for_widget')).html(
		'<option value="1">Товарка</option>'
		);
		user_parent.find($('.save_for_widget')).html(
		'<button type="submit" class="btn btn-primary">Сохранить</button>'
		);
		}
		else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
		user_parent.find($('.type_for_widget')).css('display', 'block');
		user_parent.find($('.pod_type_for_widget')).html(
		'<option value="2">Видео</option>'
		);
		user_parent.find($('.type_for_video')).css('display', 'block');
			user_parent.find($('.type_for_video_select')).html(
			'<option value="1">Автоплей</option>' +
			'<option value="2">Оверлей</option>'+
			'<option value="3">Васт ссылка</option>'
			);
		user_parent.find($('.save_for_widget')).html(
		'<button type="submit" class="btn btn-primary">Сохранить</button>'
		);
		}
		else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
		user_parent.find($('.type_for_widget')).css('display', 'block');
		user_parent.find($('.pod_type_for_widget')).html(
		'<option value="1">Товарка</option>' +
		'<option value="2">Видео</option>'
		);
		user_parent.find($('.save_for_widget')).html(
		'<button type="submit" class="btn btn-primary">Сохранить</button>'
		);
		}
		else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
			user_parent.find($('.type_for_widget')).css('display', 'none');
			user_parent.find($('.pod_type_for_widget')).html('');
			user_parent.find($('.type_for_video')).css('display', 'none');
			user_parent.find($('.save_for_widget')).html(' ');
		}
	});
	$('.pod_type_for_widget').change(function(){
		var user_parent=$(this).parents('.user_add_widget');
		if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='1'){
			user_parent.find($('.type_for_video')).css('display', 'none');
			user_parent.find($('.type_for_video_select')).html('');
		}
		else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='2'){
			user_parent.find($('.type_for_video')).css('display', 'block');
			user_parent.find($('.type_for_video_select')).html(
			'<option value="1">Автоплей</option>' +
			'<option value="2">Оверлей</option>'+
			'<option value="3">Васт ссылка</option>'
			);
		}
	});
});
$(document).ready(function() {
	$('.type_for_commission').change(function(){
		var user_parent=$(this).parents('.default_commission');
		if (user_parent.find($('.type_for_commission option:selected')).val()>'0' && user_parent.find($('.type_for_commission option:selected')).val()<'7'){
			user_parent.find($('.video_commission_rus')).css('display', 'block');
			user_parent.find($('.video_commission_cis')).css('display', 'block');
			user_parent.find($('.product_commission')).css('display', 'none');
			user_parent.find($('.commission_save')).css('display', 'block');
			user_parent.find($('.link_select')).css('display', 'none');
			user_parent.find($('.link_summa_rus')).css('display', 'none');
			user_parent.find($('.link_summa_cis')).css('display', 'none');
		}
		if (user_parent.find($('.type_for_commission option:selected')).val()=='0'){
			user_parent.find($('.video_commission_rus')).css('display', 'none');
			user_parent.find($('.video_commission_cis')).css('display', 'none');
			user_parent.find($('.product_commission')).css('display', 'none');
			user_parent.find($('.commission_save')).css('display', 'none');
			user_parent.find($('.link_select')).css('display', 'none');
			user_parent.find($('.link_summa_rus')).css('display', 'none');
			user_parent.find($('.link_summa_cis')).css('display', 'none');
		}
		if (user_parent.find($('.type_for_commission option:selected')).val()>'6' && user_parent.find($('.type_for_commission option:selected')).val()<'9'){
			user_parent.find($('.video_commission_rus')).css('display', 'none');
			user_parent.find($('.video_commission_cis')).css('display', 'none');
			user_parent.find($('.product_commission')).css('display', 'block');
			user_parent.find($('.commission_save')).css('display', 'block');
			user_parent.find($('.link_select')).css('display', 'none');
			user_parent.find($('.link_summa_rus')).css('display', 'none');
			user_parent.find($('.link_summa_cis')).css('display', 'none');
		}
		if (user_parent.find($('.type_for_commission option:selected')).val()>'8'){
			user_parent.find($('.video_commission_rus')).css('display', 'none');
			user_parent.find($('.video_commission_cis')).css('display', 'none');
			user_parent.find($('.product_commission')).css('display', 'none');
			user_parent.find($('.commission_save')).css('display', 'block');
			user_parent.find($('.link_select')).css('display', 'block');
			user_parent.find($('.link_summa_rus')).css('display', 'block');
			user_parent.find($('.link_summa_cis')).css('display', 'block');
		}
	});
});
</script>
<script type="text/javascript">
    $(function() {
        $(document).on('click', '.plus_all', function(event) {
            var id=$(this).data('set');
			event.preventDefault();
			if ($('#us-'+id).hasClass('in')){
				$('#us-'+id).html('');
			}
			else{
				$.post('/user_detail_widgets/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
						$('#us-'+response.id).html(response.view);
						$('[data-toggle="popover"]').popover({html:true});
						$('[data-toggle="tooltip"]').tooltip();
						$('.default_status').popover({html : true});
				});
			}
        });
		$(document).on('click', '.plus_video', function(event) {
            var id=$(this).data('set');
			event.preventDefault();
			if ($('#v-'+id).hasClass('in')){
				$('#v-'+id).html('');
			}
			else{
				$.post('/user_detail_widgets_video/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
						$('#v-'+response.id).html(response.view);
						$('[data-toggle="popover"]').popover({html:true});
						$('[data-toggle="tooltip"]').tooltip();
						$('.default_status').popover({html : true});
				});
			}
        });
		$(document).on('click', '.plus_product', function(event) {
            var id=$(this).data('set');
			event.preventDefault();
			if ($('#p-'+id).hasClass('in')){
				$('#p-'+id).html('');
			}
			else{
				$.post('/user_detail_widgets_product/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
						$('#p-'+response.id).html(response.view);
						$('[data-toggle="popover"]').popover({html:true});
						$('[data-toggle="tooltip"]').tooltip();
						$('.default_status').popover({html : true});
				});
			}
        });
		$(document).on('click', '#get_users_video_widgets', function(event) {
			event.preventDefault();
			$.post('/user_video_widgets/',{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val(), search:$('#search_clent').val(), number: $('#number_for_client').val(), manager_for_client:$('#manager_for_client').val()}, function(response) {
				$('#video_stat').html(response.view);
				$('[data-toggle="popover"]').popover({html:true});
				$('[data-toggle="tooltip"]').tooltip();
				$('.default_status').popover({html : true});
			});
        });
		$(document).on('click', '#get_users_product_widgets', function(event) {
			event.preventDefault();
			$.post('/user_product_widgets/',{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val(), search:$('#search_clent').val(), number: $('#number_for_client').val(), manager_for_client:$('#manager_for_client').val()}, function(response) {
				$('#product_stat').html(response.view);
				$('[data-toggle="popover"]').popover({html:true});
				$('[data-toggle="tooltip"]').tooltip();
				$('.default_status').popover({html : true});
			});
        });
		$('#payment').on('click', '#user_urgently_pay', function(event) {
            if ($('#user_urgently_pay').prop('checked')){
				$('#text_for_user_pay').html('<strong style="color: rgb(181, 0, 0);">Минимальная сумма выплаты 1000 руб.</strong>');
				$('#summa_for_pay').prop('min', '1000');
			}
			else{
				$('#text_for_user_pay').html('<strong>Минимальная сумма выплаты 300 руб.</strong>');
				$('#summa_for_pay').prop('min', '300');
			}
        });
	});
</script>
@endpush