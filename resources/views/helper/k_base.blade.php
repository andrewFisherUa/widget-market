@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			@include('helper.top_menu')
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h4 class="text-center">База знаний</h4>
				<script type="text/html" id="tpl" style="display: none">
					<div class="view-source" id="__{id}">
						{body}
						</div>
				</script>
				<script type="text/html" id="tpl1" style="display: none">
					<li id="__{id}" for="spoilerid_{id}" href="#">
						{title}
					</li>
				</script>
				<script type="text/html" id="tpl2" style="display: none">
					<li for="spoilerid_{id}" href="#">
						{title}
					</li>
				</script>
				<script type="text/html" id="tpl3" style="display: none">
					<li for="spoilerid_{id}" id="__{id}" href="#">
						{title}
					</li>
				</script>
			</div>
		</div>
		<div class="row" style="height:600px; overflow: hidden;">
			<div class="col-xs-3" id="left_site" style="border: 1px solid #cacaca;
    background-image: url(/images/cabinet/background_block.png);
    background-color: rgba(199, 199, 199, 0.5);
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
	">
				<div id="tovar" class="menu">
					<span class="title">Товарный виджет</span>
					<ul class="menuUl1"></ul>
				</div>
				<div id="video" class="menu">
					<span class="title">Видео виджет</span>
					<ul class="menuUl2"></ul>
				</div>
			</div>
			<div class="col-xs-9"  id="right_site">
				<div class="content"></div>
			</div>
		</div>
	</div>
@push ('cabinet_home')
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<style>
		.menu ul {
			margin: 0;
			list-style: none;
			padding-left: 20px;
			display: none;
		}
		.menu ul li{
			cursor: pointer;
		}
		.menu .title {
			font-size: 16px;
			cursor: pointer;
			font-weight:800;
			color: #222222;
		}
		.menu.open ul {
			display: block;
		}
		.menu.open li {
			padding: 5px 0;
			font-size: 14px;
			color: #565656;
		}
		#left_site, #right_site{
			height: 100%;
			overflow: hidden;
		}
		.custom-scroll_bar-y{
			right: 0!important;
		}
  </style>
@endpush
@push ('cabinet_home_js')
<script src="//partner.market-place.su/last_helper/k_base/helperBaza.js"></script>
<script src="{{ asset('js/custom_scroll/jquery.custom-scroll.min.js') }}"></script>
<script src="{{ asset('js/daterange/moment.js') }}"></script>
<script>
$('#left_site').customScroll({
  offsetTop: 0,
  offsetRight: 0,
  offsetBottom: 0,
  vertical: true,
  horizontal: false
});
$('#right_site').customScroll({
  offsetTop: 0,
  offsetRight: 0,
  offsetBottom: 0,
  vertical: true,
  horizontal: false
});
</script>
<script>
	$(document).ready(function(){
		load_faq1('product');
		load_faq2('video');
	});
</script>
<script>
	$(document).on('click', '.menuUl1 li', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var i=$('.menuUl1 li').index(e.target);
		load_faq(i);
		$('.menuUl2 li').css('text-decoration', 'none');
		$('.menuUl1 li').css('text-decoration', 'none');
		var ili=$('.menuUl1 li').eq(i).css('text-decoration', 'underline');
	});
	$(document).on('click', '.menuUl2 li', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var i=$('.menuUl2 li').index(e.target);
		load_faq4(i);
		$('.menuUl2 li').css('text-decoration', 'none');
		$('.menuUl1 li').css('text-decoration', 'none');
		var ili=$('.menuUl2 li').eq(i).css('text-decoration', 'underline');
	});
</script>
<script>
	var menuElem = document.getElementById('tovar');
	var menuElem2 = document.getElementById('video');
	var titleElem = menuElem.querySelector('.title');
	var titleElem2 = menuElem2.querySelector('.title');
	titleElem.onclick = function() {
		menuElem.classList.toggle('open');
		menuElem2.classList.remove('open');
    };
	titleElem2.onclick = function() {
		menuElem2.classList.toggle('open');
		menuElem.classList.remove('open');
    };
</script>
<script type="text/javascript" src="//partner.market-place.su/scripts/simplebox_util.js"></script>
@endpush
@endsection