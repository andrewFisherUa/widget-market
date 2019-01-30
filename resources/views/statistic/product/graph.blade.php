@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h4 class="text-center">График по товарному виджету в период с {{date("d-m-Y H:i:s",strtotime(date("Y-m-d H:i:s")." - ".$period." HOURS"))}} по {{date("d-m-Y H:i:s")}}</h4>
			<form class="form-inline" role="form" method="get" action="">
				<div class="row" style="text-align:center">
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">Период:</span>
						<input type="text" class="form-control" value="{{$period}}" name="period">
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div>{!! $graph->render() !!}</div>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<style>
	.morris-hover.morris-default-style{
		max-height: 300px;
		overflow: auto;
	}
	#white_links, #adult_links, #razv_links, #not_me_links, #all_links{
	height: 270px;
	overflow: hidden;
	}
	.graph_block{
	border: 1px solid #cacaca;
    background-image: url(/images/cabinet/background_block.png);
    background-color: rgba(199, 199, 199, 0.5);
    height: 300px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
	}
	.checkbox_for_src{
		float: right;
		margin-right: 10px!important;
	}
	.title_for_src{
		width: calc(100% - 30px);
		display: inline-block;
	}
	</style>
	{!! Charts::assets() !!}
@endpush
@push('cabinet_home_js')
<script src="{{ asset('js/custom_scroll/jquery.custom-scroll.min.js') }}"></script>
<script>
	$(document).ready(function() {
		$('#category').change(function(){
			if ($('#category option:selected').val()=="0"){
				for (i=0; i<$('.white_links').length; i++){
					$('.white_links')[i].checked=true;
				}
				for (i=0; i<$('.adult_links').length; i++){
					$('.adult_links')[i].checked=true;
				}
				for (i=0; i<$('.razv_links').length; i++){
					$('.razv_links')[i].checked=true;
				}
				for (i=0; i<$('.not_me_links').length; i++){
					$('.not_me_links')[i].checked=true;
				}
				for (i=0; i<$('.all_links').length; i++){
					$('.all_links')[i].checked=true;
				}
			}
			if ($('#category option:selected').val()=="1"){
				for (i=0; i<$('.white_links').length; i++){
					$('.white_links')[i].checked=true;
				}
				for (i=0; i<$('.adult_links').length; i++){
					$('.adult_links')[i].checked=false;
				}
				for (i=0; i<$('.razv_links').length; i++){
					$('.razv_links')[i].checked=false;
				}
				for (i=0; i<$('.not_me_links').length; i++){
					$('.not_me_links')[i].checked=false;
				}
				for (i=0; i<$('.all_links').length; i++){
					$('.all_links')[i].checked=false;
				}
			}
			if ($('#category option:selected').val()=="2"){
				for (i=0; i<$('.white_links').length; i++){
					$('.white_links')[i].checked=false;
				}
				for (i=0; i<$('.adult_links').length; i++){
					$('.adult_links')[i].checked=true;
				}
				for (i=0; i<$('.razv_links').length; i++){
					$('.razv_links')[i].checked=false;
				}
				for (i=0; i<$('.not_me_links').length; i++){
					$('.not_me_links')[i].checked=false;
				}
				for (i=0; i<$('.all_links').length; i++){
					$('.all_links')[i].checked=false;
				}
			}
			if ($('#category option:selected').val()=="3"){
				for (i=0; i<$('.white_links').length; i++){
					$('.white_links')[i].checked=false;
				}
				for (i=0; i<$('.adult_links').length; i++){
					$('.adult_links')[i].checked=false;
				}
				for (i=0; i<$('.razv_links').length; i++){
					$('.razv_links')[i].checked=true;
				}
				for (i=0; i<$('.not_me_links').length; i++){
					$('.not_me_links')[i].checked=false;
				}
				for (i=0; i<$('.all_links').length; i++){
					$('.all_links')[i].checked=false;
				}
			}
			if ($('#category option:selected').val()=="4"){
				for (i=0; i<$('.white_links').length; i++){
					$('.white_links')[i].checked=false;
				}
				for (i=0; i<$('.adult_links').length; i++){
					$('.adult_links')[i].checked=false;
				}
				for (i=0; i<$('.razv_links').length; i++){
					$('.razv_links')[i].checked=false;
				}
				for (i=0; i<$('.not_me_links').length; i++){
					$('.not_me_links')[i].checked=true;
				}
				for (i=0; i<$('.all_links').length; i++){
					$('.all_links')[i].checked=false;
				}
			}
			if ($('#category option:selected').val()=="5"){
				for (i=0; i<$('.white_links').length; i++){
					$('.white_links')[i].checked=false;
				}
				for (i=0; i<$('.adult_links').length; i++){
					$('.adult_links')[i].checked=false;
				}
				for (i=0; i<$('.razv_links').length; i++){
					$('.razv_links')[i].checked=false;
				}
				for (i=0; i<$('.not_me_links').length; i++){
					$('.not_me_links')[i].checked=false;
				}
				for (i=0; i<$('.all_links').length; i++){
					$('.all_links')[i].checked=true;
				}
			}
			
			
		})
	})
</script>
<script>
	$('#white_links').customScroll({
		offsetTop: 32,
		offsetRight: 16,
		offsetBottom: -32,
		vertical: true,
		horizontal: false
	});
	$('#adult_links').customScroll({
		offsetTop: 32,
		offsetRight: 16,
		offsetBottom: -32,
		vertical: true,
		horizontal: false
	});
	$('#razv_links').customScroll({
		offsetTop: 32,
		offsetRight: 16,
		offsetBottom: -32,
		vertical: true,
		horizontal: false
	});
	$('#not_me_links').customScroll({
		offsetTop: 32,
		offsetRight: 16,
		offsetBottom: -32,
		vertical: true,
		horizontal: false
	});
	$('#all_links').customScroll({
		offsetTop: 32,
		offsetRight: 16,
		offsetBottom: -32,
		vertical: true,
		horizontal: false
	});
</script>
@endpush