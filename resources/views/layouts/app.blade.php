<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Партнерская сеть Market-Place') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
	<link href="https://widget.market-place.su/css/cabinet/home.css" rel="stylesheet">
	@stack('registration')
	@stack('cabinet_home')
	@stack('news')
	@stack('cabinet_home_top')
	<style>
		#app{
			min-height: 80vh;
		}
		.container{
		width: 1200px !important;
		}
		
		/*стили для топ меню*/
		.navbar .container {
			width: auto!important;
		}
		.navbar .divider-vertical {
    width: 1px;
    height: 40px;
    margin: 0 9px;
    overflow: hidden;
    background-color: #222222;
    border-right: 1px solid #333333;
}
		.navbar .nav>li>a:hover,  .navbar .nav>li>a:focus, .navbar .nav>li>a:active{
    color: #ffffff;
    text-decoration: none;
    background-color: transparent;
}
		.nav>li>a {
			display: block;
		}
		.navbar .nav .active>a, .navbar .nav .active>a:hover {
			color: #ffffff;
			text-decoration: none;
			background-color: #222222;
		}
		.navbar .nav>li>a {
			float: none;
			padding: 9px 7px 11px;
			line-height: 19px;
			color: #999999;
			text-decoration: none;
			text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
		}
		
.navbar .brand {
    display: block;
    float: left;
    padding: 8px 20px 12px;
    margin-left: -20px;
    font-size: 20px;
    font-weight: 200;
    line-height: 1;
    color: #999999;
}
		.navbar .nav>li {
			display: block;
			float: left;
		}
		.navbar .nav {
			position: relative;
			left: 0;
			display: block;
			float: left;
			margin: 0 10px 0 0;
		}
		.navbar-inner {
			min-height: 40px;
			padding-right: 20px;
			padding-left: 20px;
			background-color: #2c2c2c;
			background-image: -moz-linear-gradient(top,#333333,#222222);
			background-image: -ms-linear-gradient(top,#333333,#222222);
			background-image: -webkit-gradient(linear,0 0,0 100%,from(#333333),to(#222222));
			background-image: -webkit-linear-gradient(top,#333333,#222222);
			background-image: -o-linear-gradient(top,#333333,#222222);
			background-image: linear-gradient(top,#333333,#222222);
			background-repeat: repeat-x;
			/*-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;*/
			filter: progid:dximagetransform.microsoft.gradient(startColorstr='#333333',endColorstr='#222222',GradientType=0);
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
		}
		.navbar {
			margin-bottom: 18px;
			overflow: visible;
			border: none!important;
		}
		.navbar {
			color: #999999;
		}
		.navbar .dropdown-menu:before {
    position: absolute;
    top: -7px;
    left: 9px;
    display: inline-block;
    border-right: 7px solid transparent;
    border-bottom: 7px solid #ccc;
    border-left: 7px solid transparent;
    border-bottom-color: rgba(255,255,255,1);
    content: '';
}
.dropdown-menu li>a:hover, .dropdown-menu .active>a, .dropdown-menu .active>a:hover {
    color: #ffffff;
    text-decoration: none;
    background-color: #0088cc;
}
.dropdown-menu .divider {
    height: 2px;
    margin: 8px 1px;
    overflow: hidden;
    background-color: #e5e5e5;
    border-bottom: 1px solid #ffffff;
}
.dropdown-menu a {
    display: block;
    padding: 3px 15px;
    clear: both;
    font-weight: normal;
    line-height: 18px;
    color: #333333;
    white-space: nowrap;
}
		.open .dropdown-menu {
    display: block;
}
.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none;
    float: left;
    min-width: 160px;
    padding: 4px 0;
    margin: 1px 0 0;
    list-style: none;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0,0,0,0.2);
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    -moz-box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
}
.nav .open > a, .nav .open > a:hover, .nav .open > a:focus{
    color: #ffffff;
    text-decoration: none;
    background-color: transparent;
}
.nav li+.nav-header {
    margin-top: 9px;
}
.nav .nav-header {
    display: block;
    padding: 3px 15px;
    font-size: 11px;
    font-weight: bold;
    line-height: 18px;
    color: #999999;
    text-shadow: 0 1px 0 rgba(255,255,255,0.5);
    text-transform: uppercase;
}
.market_header_notif{
position: absolute;
    right: -5px;
    top: 2px;
    background: red;
    width: 16px;
    height: 16px;
    border-radius: 16px;
    text-align: center;
    line-height: 16px;
    font-weight: bold;
    color: #fff;
    font-size: 12px;
}
.popover{
    font-family: "OpenSansRegular"!important;
}
a:hover,a:focus,a:active{
	outline:0!important;
}
	</style>
	
</head>
<body>
    <div id="app">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="/"><img style="width: 100px;" src="/images/mp_logo.png"></a>
					<div class="nav-collapse">
						@if (!Auth::guest())
						<ul class="nav">
							<li><a href="/">Главная</a></li>
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
							<li>
								<a href="{{route('pads.all')}}" class="market_place_header">Площадки
									@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager'))
										<!--{{$cntPad=count(\App\PartnerPad::where('status', '0')->get())}}-->
										@if ($cntPad>0)
										<span class="market_header_notif">@if ($cntPad<=9){{$cntPad}}@else 9+ @endif</span>
										@endif
									@else
										<!--{{$cntPad=count(\App\PartnerPad::where('status', '0')->whereIn('user_id', Auth::user()->ManagerOnUsers->pluck('user_id')->toArray())->get())}}-->
										@if ($cntPad>0)
										<span class="market_header_notif">@if ($cntPad<=9){{$cntPad}}@else 9+ @endif</span>
										@endif
									@endif
								</a>
							</li>
							@endif
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager'))
								<li>
									<!--{{$payouts=\App\Payments\UserPayout::where('status', 0)->get()}}-->
									<a href="{{route('payments.payouts')}}" class="market_place_header">Запросы выплат
									@if(count($payouts))
									<span class="market_header_notif">
										{{count($payouts)}}
									</span>
									@endif
									</a>
								</li>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Настройки видео <b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="{{route('video_setting.sources')}}">Редактор ссылок</a></li>
										<li><a href="{{route('video_setting.blocks.all')}}">Редактор блоков</a></li>
										@if (Auth::user()->hasRole('admin'))
											<li><a href="{{route('video_setting.default')}}">Редактор дефолтов</a></li>
										@endif
									</ul>
								</li>
								{{--<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Настройки бренд <b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="{{route('brand_setting.all.source')}}">Редактор ссылок</a></li>
										<li><a href="{{route('brand_setting.all.block')}}">Редактор блоков</a></li>
									</ul>
								</li>--}}
							@endif
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Статистика <b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="{{route('video_statistic.video_summary')}}">Видео суммарная статистика</a></li>
										<li><a href="{{route('video_statistic.new_graph')}}">Видео график</a></li>
										<li><a href="{{route('video_statistic.new_video_stat')}}">Видео статистика по ссылкам</a></li>
										<li><a href="{{route('video_statistic.pads_video_summary')}}">Видео статистика по площадкам</a></li>
										<li><a href="{{route('video_statistic.partner_video_summary')}}">Видео статистика по партнерам</a></li>
										<li><a href="{{route('video_statistic.video_hour')}}">Видео статистика по часам</a></li>
										<li><a href="{{route('video_statistic.group.ip.all')}}">Видео статистика по ip</a></li>
										<li class="divider"></li>
										{{--<li><a href="{{route('product_statistic.product_summary')}}">Товарка суммарная статистика</a></li>--}}
										<li><a href="{{route('rekrut_product.nextstat')}}">Товарка суммарная статистика</a></li>
										
										{{--<li><a href="{{route('product_statistic.product_detail_pads')}}">Товарка по площадкам</a></li>--}}
										<li><a href="{{route('rekrut_product.nextstat.pads')}}">Товарка по площадкам</a></li>
											{{--<li><a href="{{route('product_statistic.product_detail_users')}}">Товарка статистика по партнерам</a></li>--}}
										<li><a href="{{route('rekrut_product.nextstat.part')}}">Товарка статистика по партнерам</a></li>
										<!--<li><a href="{{route('product_statistic.product_graph')}}">Товарка график</a></li>-->
										
										<li class="divider"></li>
										<li><a href="{{route('advertiser.site_statistic',['sort'=>'clicks','order'=>'desc'])}}">Товарка анализ по площадкам</a></li>
										<li><a href="{{route('mpstatistica.loaded',[])}}">Товарка анализ трафика</a></li>
											{{--<li><a href="{{route('mpstatistica.cpa',[])}}">Товарка заказы cpa</a></li>--}}
										
										<!--<li class="divider"></li> 
										<li class="nav-header">Заголовок</li>
										<li><a href="#">Ссылка</a></li>-->
										<li class="divider"></li>
										<li>
										{{--<a href="{{route('teaser_statistic.teaser_summary')}}">Тизерка суммарная статистика</a>--}}
										<a href="{{route('mpstatistica.summa_teaser')}}">Тизерка суммарная статистика</a>
										</li>
										{{--<li><a href="{{route('teaser_statistic.teaser_detail_pads')}}">Тизерка статистика по площадкам</a></li>
										<li><a href="{{route('teaser_statistic.teaser_detail_users')}}">Тизерка статистика по партнерам</a></li>--}}
										<li><a href="{{route('mpstatistica.pads_teaser')}}">Тизерка статистика по площадкам</a></li>
										<li><a href="{{route('mpstatistica.partners_teaser')}}">Тизерка статистика по партнерам</a></li>
										<li class="divider"></li>
										<li><a href="{{route('brand_statistic.summary_statisitc')}}">Бренд суммарная статистика</a></li>
										<li><a href="{{route('brand_statistic.source_statisitc')}}">Товарка график</a></li>
										<li class="divider"></li>
										<li><a href="{{route('global.referals')}}">Рефералы</a></li>
										<li class="divider"></li>
										@if (Auth::user()->hasRole('admin'))
										<li><a href="{{route('managers.all')}}">Статистика менеджеров</a></li>
										@else
										<li><a href="{{route('managers.history', ['id'=>\Auth::user()->id])}}">Моя статистика</a></li>
										@endif
										@if (Auth::user()->hasRole('admin'))
										<li class="divider"></li>
										<li><a href="{{route('admin.all_stat_sum')}}">Общая сумма</a></li>
										<li><a href="{{route('info_admin.source_info_key')}}">Secret page</a></li>
										@endif
										<!--<li class="divider"></li>
										<li><a href="{{route('video_statistic.frame')}}">Фреймы</a></li>
										<li><a href="{{route('video_statistic.frame.user')}}">Фреймы по виджетам</a></li>-->
									</ul>
								</li>
							@endif
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
								<li class="dropdown">
									<a href="#" class="dropdown-toggle market_place_header" data-toggle="dropdown">Новости <b class="caret"></b>
									@if(count(Auth::user()->unreadNotifications->where('type', "App\Notifications\NewNews")))
									<span class="market_header_notif">
									@if (count(Auth::user()->unreadNotifications->where('type', "App\Notifications\NewNews"))<=9)
										{{count(Auth::user()->unreadNotifications->where('type', "App\Notifications\NewNews"))}}
										@else 9+ @endif
									</span>
									@endif
									</a>
									<ul class="dropdown-menu custom-menu">
										<li><a href="{{route('news.all')}}">Все новости</a></li>
										<li><a href="{{route('news.add')}}">Добавить новость</a></li>
										<li><a href="{{route('news.unsubscribe')}}">Отписанные юзеры</a></li>
									</ul>
								</li>
							@else
								<li>
									<a href="{{route('news.all')}}" class="market_place_header">Новости</a>
								</li>
							@endif
							@role('advertiser')
							<li class="dropdown">
							<a href="#" class="dropdown-toggle market_place_header" data-toggle="dropdown">Взаиморасчёты <b class="caret"></b></a>
								<ul class="dropdown-menu custom-menu">
									<li><a href="/adv_/history/0">История</a></li>
									<li><a href="{{route('advertiser.invoices_history')}}">Счета</a></li>
								</ul>
							</li>
							<li>
									<a href="/adv_/statistic/0" class="market_place_header">Статистика</a>
							</li>
							@endrole
							
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
								<li class="dropdown">
								<a href="#" class="dropdown-toggle market_place_header" data-toggle="dropdown">Разное <b class="caret"></b></a>
								<ul class="dropdown-menu custom-menu">
									<li><a href="{{route('s_link.all')}}">Рекламные ссылки</a></li>
									@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager'))
										<li>
											<a href="{{route('users_log.auth_log')}}" class="market_place_header">История логинов</a>
										</li>
									@endif
									<li><a href="{{route('global.registration.log')}}">Логи регистрации</a></li>
									@if (Auth::user()->hasRole('admin') or Auth::user()->id==37)
									<li><a href="{{route('lbtc.list')}}">Парсеры локала</a></li>
									<li><a href="{{route('obmenneg.first')}}">Таблица остатков</a></li>
									<li><a href="{{route('lbtc.birges')}}">Таблица курсов</a></li>
									@endif
								</ul>
							</li>
							@endif
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
							<li class="dropdown">	
							  <a href="#" class="dropdown-toggle market_place_header" data-toggle="dropdown">Прямые реклы <b class="caret"></b></a>
							    <ul class="dropdown-menu custom-menu">
								<li><a href="{{route('advertiser.add_admin')}}">Списoк прямыз рекламодателей</a></li>
								<li><a href="{{route('mpproducts.test')}}">Настройка топ категорий</a></li>
								</ul>
							</li>	
							@endif
							@if (Auth::user()->hasRole('admin') or Auth::user()->hasRole('super_manager') or Auth::user()->hasRole('manager'))
								@include('helper.newtoppanel')
							@endif
							<li class="dropdown">
								<a href="#" class="dropdown-toggle market_place_header" data-toggle="dropdown">Помощь <b class="caret"></b></a>
								<ul class="dropdown-menu custom-menu">
									<li><a href="{{route('help.reference')}}">Справка</a></li>
									<li><a href="{{route('help.k_base')}}">База знаний</a></li>
									<li><a href="{{route('help.instructions')}}">Инструкции</a></li>
								</ul>
							</li>
						</ul>
						
						<ul class="nav pull-right">
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								@if (\Auth::user()->profile->avatar)
									<img src="/images/avatars/{{\Auth::user()->profile->avatar}}" class="img-circle" style="width: 18px; height: 18px; background: #fff">
								@else
									<img src="/images/cabinet/no_foto.png" class="img-circle" style="width: 18px; height: 18px; background: #fff">
								@endif
								{{ Auth::user()->name }}<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="nav-header">Баланс: {{\Auth::user()->profile->balance}}</li>
									<li class="divider"></li>
									<li><a href="{{ route('profile.personal') }}">Профиль</a></li>
									<li><a onclick="event.preventDefault(); document.getElementById('logout-form-app').submit();">Выйти</a>
									<form id="logout-form-app" action="{{ route('logout') }}" method="POST" style="display: none;">
										{{ csrf_field() }}
									</form></li>
									
									
									
									
								</ul>
							</li>
						</ul>
						@else
						<ul class="nav pull-right">
							<li><a href="{{route('login')}}">Войти</a></li>
							<li class="divider-vertical"></li>
							<li><a href="{{route('register')}}">Регистрация</a></li>
						</ul>
						@endif
					</div>
				</div>
			</div>
		</div>
        @yield('content')
	
    </div>

	@include('layouts.footer')

    <!-- Scripts -->
   <script src="{{ asset('js/app.js') }}"></script>


	@stack('cabinet_home_bottom')
	@stack('cabinet_home_js')
	@stack('validator_error')
	@stack('donut')
	@stack('ckeditor')
	@stack('newsjs')
	<script>
		$('[data-toggle="popover"]').popover({html:true
		});
	</script>
	@if (Auth::guest() or Auth::user()->hasRole('affiliate'))
	<!-- jivosite -->
	<script type='text/javascript'>
    (function(){ var widget_id = 'RdVYPcHk7q';var d=document;var w=window;function l(){
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
	</script>
	@endif
	<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter32567715 = new Ya.Metrika({
                    id:32567715,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
</body>
</html>
