@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if ($errors->has('domain'))
		<div class="alert alert-warning">
			{{ $errors->first('domain') }}
		</div>
		@push ('validator_error')
		<script>
			$(document).ready(function(){
				$("#add_affiliate_domain").modal('show');
			});
		</script>
		@endpush
	@endif
	@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
		@if ($user->profile->dop_status==1)
			<div class="alert alert-success">
				<strong>Внимание! Этот юзер отмечен как "Хороший клиент".</strong> {{$user->profile->text_for_dop_status}}
			</div>
		@elseif ($user->profile->dop_status==2)
			<div class="alert alert-warning">
				<strong>Внимание! Этот юзер отмечен как "Средний клиент".</strong> {{$user->profile->text_for_dop_status}}
			</div>
		@elseif ($user->profile->dop_status==3)
			<div class="alert alert-danger">
				<strong>Внимание! Этот юзер отмечен как "Плохой клиент".</strong> {{$user->profile->text_for_dop_status}}
			</div>
		@endif
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
					@if ($user->profile->vip==1)
						<img src="/images/cabinet/vip.png" style="width: 30px; position: absolute; top: -22px; left: 26px; cursor: pointer;" data-toggle="tooltip" data-placement="bottom" title="VIP клиент">
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
				<div class="heading text-left">Мой баланс</div>
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
		<div class="col-xs-3 col-xs-3">
			<div class="affiliate_cabinet_block text-center">
				<div class="heading text-left">Контакты</div>
				<hr class="affilaite_hr">				
				@if ($manager!='0')
					<div class="affiliate_manager">Ваш менеджер:</div>
					@if (\App\User::find($user->Profile->manager)->Profile->avatar)
						<div class="home_avatar_block">
							@if (strtotime(date('Y-m-d H:i:s'))<strtotime(\App\User::find($user->Profile->manager)->updated_at)+900)
								<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер online" class="home_avatar_green"></span>
							@else
								<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер offline"  class="home_avatar_red"></span>
							@endif
							<img class="img-circle manager_avatar" src="/images/avatars/{{\App\User::find($user->Profile->manager)->Profile->avatar}}">
						</div>
					@else
						<div class="home_avatar_block">
							@if (strtotime(date('Y-m-d H:i:s'))<strtotime(\App\User::find($user->Profile->manager)->updated_at)+900)
								<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер online" class="home_avatar_green"></span>
							@else
								<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер offline"  class="home_avatar_red"></span>
							@endif
							<img class="img-circle manager_avatar" src="/images/cabinet/no_foto.png">
						</div>
					@endif
					<div class="affiliate_name">{{\App\User::find($user->Profile->manager)->Profile->name}}</div>			
					<div class="manager_skype"><b>Skype:</b> <a href='skype:{{\App\User::find($user->Profile->manager)->Profile->skype}}?chat'>{{\App\User::find($user->Profile->manager)->Profile->skype}}</a></div>
					<div class="manager_skype"><b>Email:</b> <a href='mailto:{{\App\User::find($user->Profile->manager)->Profile->email}}'>{{\App\User::find($user->Profile->manager)->Profile->email}}</a></div>
				@else
					<div class="no_manager">После добавления площадки за Вами будет закреплен персональный менеджер, если у Вас есть какие либо вопросы, обратитесь в службу поддержки: <b>support@market-place.su</b></div>
				@endif
			</div>
		</div>
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Последние новости</div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
				<div id="cabinet_news">
				@if (count($news_lim)>0)
				@foreach ($news_lim as $n)
					<div class="col-xs-12" style="margin-bottom: 15px;">
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
		<div class="col-xs-6">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мои площадки <a href="#" data-toggle="modal" data-target="#add_affiliate_domain" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-placement="bottom" title="Добавить площадку"></span></a></div>
				<hr class="affilaite_hr">
				<div id="affiliate_all_pads">
				@if (count($partnerPads)>0)
				@foreach ($partnerPads as $pad)
						<div class="affiliate_pad">
							<div data-toggle="tooltip" data-placement="left" style="width: 440px" title="{{$pad->domain}}" class="affiliate_all_pads_domain">
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
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left" style="font-size: 10px">Эффективность моих площадок за сегодня</div>
				<hr class="affilaite_hr">
				{!! $chart->render() !!}
			</div>
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
	</div>
	<div class="row" style="margin-top: 30px;">
		<div class="col-xs-12">
			<div class="affiliate_cabinet_block" style="height: 400px;">
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
								@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
								<td class="text-center">Зач. глубина</td>
								@else
								<td></td>
								@endif
								<td class="text-center"  data-toggle="tooltip" data-placement="bottom" title="Премия за доп. показы за загрузку" style="cursor:pointer">Бонус за глубину</td>
								<td colspan="5"></td>
							</tr>
						</thead>
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
								<td class="text-center">{{$user_all_sum->summa}}</td>
								@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
								<td class="text-center">{{$user_all_sum->second}}</td>
								@else
								<td></td>
								@endif
								<td class="text-center">{{$user_all_sum->second_summa}}</td>
								<td colspan='5'></td>
							</tr>
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
											<span>{{round($pwidget->videoCommisssion($pwidget->video['commission_rus']),0)}} и {{round($pwidget->videoCommisssion($pwidget->video['commission_cis']),0)}}</span>
										@endif
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
										<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
										<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
										<td class="text-center"><a href="{{ route('product_statistic_pid.pid_statistic', ['id'=>$pwidget->product['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
											<td class="text-center"><a href="{{ route('product_statistic.pid_url_statistic', ['id'=>$pwidget->id])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Url показов"><span class="glyphicon glyphicon-globe color-orange"></span></a></td>
										@else
											<td></td>
											<td></td>
										@endif
									@elseif ($pwidget->type==2)
										<!--{{($statistic=$pwidget->VideoStat($pwidget->video['id'], $from, $to))?"":""}}-->
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td class="text-center">{{$statistic?$statistic['loaded']:0}}</td>
										@else
										<td></td>
										@endif
										<td class="text-center">{{$statistic?$statistic['calculate']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['clicks']:0}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
											<td class="text-center">{{$statistic?$statistic['util']:0}}</td>
										@else
											<td></td>
										@endif
										<td class="text-center">{{$statistic?$statistic['ctr']:0}}</td>
										<td class="text-center">{{$statistic?$statistic['summa']:0}}</td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td class="text-center">{{$statistic?$statistic['second']:0}}</td>
										@else
										<td></td>
										@endif
										<td class="text-center">{{$statistic?$statistic['second_summa']:0}}</td>
										<td class="text-center"><a href="" data-toggle="modal" data-target="#get_code_{{$pwidget->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a></td>
										<td class="text-center"><a href="{{ route('widget.edit', ['id'=>$pwidget->id])}}" data-toggle="tooltip" data-placement="bottom" title="Настройки"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
										<td class="text-center"><a href="{{ route('video_statistic_pid.pid_statistic', ['id'=>$pwidget->video['id']])}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a></td>
										@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
										<td class="text-center"><a href="" data-toggle="tooltip" data-placement="bottom" title="Удалить виджет"><span class="glyphicon glyphicon-trash color-red"></span></a></td>
										<td class="text-center"></td>
										@else
										<td></td>
										<td></td>
										@endif
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
	#affiliate_all_widgets {
			height: 320px;
			overflow: hidden;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
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
	$('#cabinet_news').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
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
			'@if (\Auth::user()->hasRole("admin") or \Auth::user()->hasRole("super_manager") or \Auth::user()->hasRole("manager"))<option value="3">Васт ссылка</option>@endif'
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
			'<option value="2">Оверлей</option>' +
			'@if (\Auth::user()->hasRole("admin") or \Auth::user()->hasRole("super_manager") or \Auth::user()->hasRole("manager"))<option value="3">Васт ссылка</option>@endif'
			);
		}
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