@extends('layouts.app')

@section('content') 
<div id="user_id" hidden>{{$user->id}}</div>
<div class="container">
	@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
		<div class="row">
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
		</div>
	@endif
	@if (Session::has('message_success'))
	<div class="row" style="margin-top: 5px; margin-bottom: 5px;">
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	</div>
	@endif
	<div class="row">
		<div id="home_message">
			
		</div>
	</div>
    <div class="row">
		<!-- профиль -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block text-center">
				<div class="heading text-left">Мой профиль</div>
				<hr class="affilaite_hr">
				<div id="home_profile" class="home_block">
					<div class="loaded">
					
					</div>
				</div>
			</div>
		</div>
		<!-- Баланс -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мой баланс 
				@if ($user->hasRole('manager') or $user->hasRole('super_manager')) 
					<a href="{{route('managers.history', ['id'=>$user->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Детальная статистика" class="glyphicon glyphicon-question-sign color-blue manager_help" target="_blank"></a>
				@endif
				</div>
				<hr class="affilaite_hr">
				<div id="home_balance" class="home_block">
					<div class="loaded">
					
					</div>
				</div>			
			</div>
		</div>
		@if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager'))
		<!-- Уведомления админа -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мои уведомления</div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
				<div id="home_notif" class="home_block">
					<div class="loaded">
					
					</div>
				</div>	
			</div>
		</div>
		@else
		<!-- Контакты юзера-->
		<div class="col-xs-3 col-xs-3">
			<div class="affiliate_cabinet_block text-center">
				<div class="heading text-left">Контакты</div>
				<hr class="affilaite_hr">				
				<div id="home_contacts" class="home_block">
					<div class="loaded">
					
					</div>
				</div>	
			</div>
		</div>
		@endif
		<!-- Новости -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Последние новости @if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')) <a href="{{ route('news.add') }}" class="affiliate_add_domain" target="_blank"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Добавить новость"></span></a> @endif</div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
				<div id="home_news" class="home_block">
					<div class="loaded">
					
					</div>
				</div>	
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 30px;">
		@if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager'))
			<!-- Плоащдки админа -->
			<div class="col-xs-3">
				<div class="affiliate_cabinet_block" style="height: 400px;">
					<div class="heading text-left">Мои площадки <a href="#" data-toggle="modal" data-target="#add_affiliate_domain" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Добавить площадку"></span></a></div>
					<hr class="affilaite_hr">
					<div id="home_pads" class="home_block">
						<div class="loaded">
						
						</div>
					</div>	
				</div>
			</div>
			<!-- Виджеты админа -->
			<div class="col-xs-9">
				<div class="affiliate_cabinet_block" style="height: 400px;">
					<div class="heading text-left">Мои виджеты <a href="#" data-toggle="modal" data-target="#add_affiliate_widget" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Создать виджет"></span></a></div>
					<hr class="affilaite_hr">
					<div id="home_widgets" class="home_block">
						<div class="loaded">
						
						</div>
					</div>	
				</div>
			</div>
		@else
			<!-- Плоащдки юзера -->
			<div class="col-xs-6">
				<div class="affiliate_cabinet_block">
					<div class="heading text-left">Мои площадки <a href="#" data-toggle="modal" data-target="#add_affiliate_domain" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Добавить площадку"></span></a></div>
					<hr class="affilaite_hr">
					<div id="home_pads" class="home_block">
						<div class="loaded">
						
						</div>
					</div>	
				</div>
			</div>
			<!-- Эффективность площадок юзера -->
			<div class="col-xs-3">
				<div class="affiliate_cabinet_block">
					<div class="heading text-left" style="font-size: 10px">Эффективность моих площадок за сегодня</div>
					<hr class="affilaite_hr">
					<div id="home_efficiency" class="home_block">
						<div class="loaded">
							
						</div>
					</div>	
				</div>
			</div>
			<!-- Уведомления юезра -->
			<div class="col-xs-3">
				<div class="affiliate_cabinet_block">
					<div class="heading text-left">Мои уведомления</div>
					<hr class="affilaite_hr" style="margin-bottom: 10px;">
					<div id="home_notif" class="home_block">
						<div class="loaded">
						
						</div>
					</div>	
				</div>
			</div>
		@endif
	</div>
	@if ($user->hasRole('admin') or $user->hasRole('super_manager'))
	<div class="row" style="margin-top: 30px;">
		<!-- График видео -->
		<div class="col-xs-6">
			<div class="affiliate_cabinet_block text-center" style="height: 360px;">
				<div class="heading text-left">Запросы видео за последние 6 часов <a href="{{ route('video_statistic.new_graph') }}" target="_blank" class="affiliate_add_domain"><span class="glyphicon glyphicon-th-list" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Полный график"></span></a></div>
				<hr class="affilaite_hr">
				<div id="home_video_graph" class="home_block" style="height: 328px;">
					<div class="loaded">
						
					</div>
				</div>	
			</div>
		</div>
		<!-- График товарка -->
		<div class="col-xs-6">
			<div class="col-xs-12">
				<div class="affiliate_cabinet_block text-center" style="height: 175px">
					<div class="heading text-left">Показы по товарному виджету за последние 6 часов <!--<a href="{{ route('product_statistic.product_graph')}}" target="_blank" class="affiliate_add_domain"><span class="glyphicon glyphicon-th-list" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Полный график"></span></a>--></div>
					<hr class="affilaite_hr">
					<div id="home_product_graph" class="home_block" style="height: 142px;">
						<div class="loaded">
							
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12">
				<div class="affiliate_cabinet_block text-center" style="height: 175px; margin-top: 10px;">
					<div class="heading text-left">Показы по тизерному виджету за последние 6 часов <!--<a href="" target="_blank" class="affiliate_add_domain"><span class="glyphicon glyphicon-th-list" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Полный график"></span></a>--></div>
					<hr class="affilaite_hr">
					<div id="home_teaser_graph" class="home_block"  style="height: 142px;">
						<div class="loaded">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	
	@if ($user->hasRole('affiliate'))
		<div class="row" style="margin-top: 30px;">
			<!-- Виджеты юзера -->
			<div class="col-xs-12">
				<div class="affiliate_cabinet_block" style="height: auto; min-height: 300px;">
					<div class="heading text-left">Мои виджеты <a href="#" data-toggle="modal" data-target="#add_affiliate_widget" class="affiliate_add_domain"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Создать виджет"></span></a></div>
					<hr class="affilaite_hr">
					<div id="home_widgets" class="home_block" style="height: auto; min-height: 300px;">
						<div class="loaded">
						
						</div>
					</div>	
				</div>
			</div>
		</div>
	@endif
	@if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager'))
	<div class="row" style="margin-top: 30px;">
		<!-- Все юзеры -->
		<div class="col-xs-12">
			<div id="home_users">
				<div class="affiliate_cabinet_block" style="margin-top: 10px;">
					<div class="loaded">
								
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
</div>

@endsection
@push('cabinet_home_top')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/news.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	<style>
		.home_block{
			height: 247px;
		}
		.loaded{
			height: 100%;
			width: 100%;
			background: url('/images/100x100_spinner.gif');
			background: url(/images/100x100_spinner.gif) no-repeat;
			background-size: 100px 100px;
			background-position: center center;
		}
	</style>
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
	</style>
	<style>
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
			cursor: pointer;
		}
		.default_status:hover, .default_status:focus, .default_status:active{
			outline: none;
		}
		.erty .tooltip-inner{
				max-width: 450px!important;
		}
	</style>
	@if ($user->hasRole('admin') or $user->hasRole('super_manager') or $user->hasRole('manager'))
		<style>
			#affiliate_all_widgets {
				height: 320px;
				overflow: hidden;
			}
			#home_pads{
				height: 367px;
			}
			#affiliate_all_pads{
				height: 367px;
			}
			.table_href{
				cursor: pointer;
			}
			.table_href:hover, .table_href:active, .table_href:focus{
				text-decoration: none;
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
			.nav > li > a{
				padding: 4px 15px;
				border-radius: 0!important;
			}
			.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
				border-bottom:none!important;
			}
		</style>
	@else
		<style>
			#affiliate_all_widgets {
				height: 320px;
				overflow: hidden;
			}
		</style>
	@endif
@endpush
@push('cabinet_home_bottom')
<script src="{{ asset('js/cabinet/profile.js') }}"></script>
<script src="{{ asset('js/cabinet/notif.js') }}"></script>
<script src="{{ asset('js/cabinet/news.js') }}"></script>
<script src="{{ asset('js/cabinet/graph_video.js') }}"></script>
<script src="{{ asset('js/cabinet/graph_product.js') }}"></script>
<script src="{{ asset('js/cabinet/graph_teaser.js') }}"></script>
<script src="{{ asset('js/cabinet/graph_client.js') }}"></script>
<script src="{{ asset('js/cabinet/pads.js') }}"></script>
<script src="{{ asset('js/cabinet/widgets.js?v=0.0.0.2') }}"></script>
<script src="{{ asset('js/cabinet/balance.js') }}"></script>
@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
<script src="{{ asset('js/cabinet/home_users.js?v=0.0.0.4') }}"></script>
@endif

<script src="{{ asset('js/cabinet/contacts.js') }}"></script>

<script src="https://cdn.rawgit.com/zenorocha/clipboard.js/master/dist/clipboard.min.js"></script>
<script>
	<!--new Clipboard('.copy-all');-->
</script>
<script type="text/javascript">
   $(document).ready(function() {
		profile();
		notif();
		news();
		graph_video();
		graph_product();
		graph_teaser();
		graph_client();
		contacts();
		pads();
		widgets();
		balance();
		@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
		home_users();
		@endif
   });
</script>
@endpush