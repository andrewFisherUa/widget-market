@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			@include('helper.top_menu')
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h4 class="text-center">Справка</h4>		
				<script type="text/html" id="tpl" style="display: none">
					<div class="panel panel-default">
						<div class="panel-heading" id="__{id}">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse{id}">
									{title}
								</a>
							</h4>
						</div>
						<div id="collapse{id}" class="panel-collapse collapse">
							<div class="panel-body">
								{body}
							</div>
						</div>
					</div>
				</script>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<ul class="nav nav-tabs nav-justified cust-tabs">
					<li class="heading text-left active"><a href="#product" data-toggle="tab">Товарный виджет</a></li>
					<li class="heading text-left"><a href="#video" data-toggle="tab">Видео виджет</a></li>
					<li class="heading text-left"><a href="#tizer" data-toggle="tab">Тизерная реклама</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="product" style="margin-top: 10px;">
						<div class="panel-group product_content" id="accordion">
						</div>
					</div>
					<div class="tab-pane" id="video" style="margin-top: 10px;">
						<div class="panel-group video_content" id="accordion">
						</div>
					</div>
					<div class="tab-pane" id="tizer" style="margin-top: 10px;">
						<div class="panel-group tizer_content" id="accordion">
						</div>
					</div>
				</div>
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
		.panel-default > .panel-heading{
			background-color: #e4e4e4!important;
		}
  </style>
@endpush
@push ('cabinet_home_js')
<script src="//partner.market-place.su/last_helper/reference/helper.js"></script>
<script>
	$(document).ready(function(){
		load_faq('product');
		load_faq1('video');
		load_faq2('tizer');
	});
</script>
<script type="text/javascript" src="//partner.market-place.su/scripts/simplebox_util.js"></script>
@endpush
@endsection