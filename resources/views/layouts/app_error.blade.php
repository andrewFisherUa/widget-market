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
