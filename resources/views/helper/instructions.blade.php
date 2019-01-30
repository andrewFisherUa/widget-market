@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			@include('helper.top_menu')
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h4 class="text-center">Инструкции</h4>
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
				<!--<div class="menu" style="display: block; clear: both;">
					<span id="tovar" class="title active">Товарный виджет</span>
					<a download="" href="https://partner.market-place.su/last_helper/instrukciya_dlya_raboty_polzovatelya2.pdf" data-toggle="tooltip" data-placement="bottom" title="Скачать инструкцию товарного виджета" style="color: #007b00; float: right; font-size: 20px;"><span class="glyphicon glyphicon-floppy-saved"></a>
				</div>-->
				<div class="menu" style="display: block; clear: both;">
					<span id="autoplay" class="title active">Autoplay виджет</span>
					<a download="" href="https://partner.market-place.su/last_helper/AutoPlay.pdf" href="" data-toggle="tooltip" data-placement="bottom" title="Скачать инструкцию autoplay виджета" style="color: #007b00; float: right; font-size: 20px;"><span class="glyphicon glyphicon-floppy-saved"></a>
				</div>
				<div class="menu" style="display: block; clear: both;">
					<span id="overlay" class="title">Overlay виджет</span>
					<a download="" href="https://partner.market-place.su/last_helper/OverLay.pdf" href="" data-toggle="tooltip" data-placement="bottom" title="Скачать инструкцию overlay виджета" style="color: #007b00; float: right; font-size: 20px;"><span class="glyphicon glyphicon-floppy-saved"></a>
				</div>
			</div>
			<div class="col-xs-9" id="right_site">
				<iframe style="height: 100%; width: 100%; border: none" src="https://partner.market-place.su/last_helper/AutoPlay.pdf"></iframe>
			</div>
		</div>
	</div>
@push ('cabinet_home')
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
			color: #5a5a5a;
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
		#left_site, #right_site{
			height: 100%;
			overflow: hidden;
		}
		.active{
		color: #222222!important;
		}
	</style>
@endpush
@push ('cabinet_home_js')
<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
	<script>
		$(document).ready(function(){
			$('#tovar').click(function(){
				$('iframe').prop('src', 'https://partner.market-place.su/last_helper/instrukciya_dlya_raboty_polzovatelya2.pdf');
				$('#tovar').addClass("active");
				$('#autoplay').removeClass("active");
				$('#overlay').removeClass("active");
				
			});
			$('#autoplay').click(function(){
				$('iframe').prop('src', 'https://partner.market-place.su/last_helper/AutoPlay.pdf');
				$('#tovar').removeClass("active");
				$('#autoplay').addClass("active");
				$('#overlay').removeClass("active");
			});
			$('#overlay').click(function(){
				$('iframe').prop('src', 'https://partner.market-place.su/last_helper/OverLay.pdf');
				$('#tovar').removeClass("active");
				$('#autoplay').removeClass("active");
				$('#overlay').addClass("active");
			})
		})
	</script>
@endpush
@endsection