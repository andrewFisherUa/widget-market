@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h4 class="text-center">Видео график в период с {{date("d-m-Y H:i:s",strtotime(date("Y-m-d H:i:s")." - ".$period." HOURS"))}} по {{date("d-m-Y H:i:s")}}</h4>
			<form class="form-inline" role="form" method="get" action=" {{ route('video_statistic.new_graph') }}">
				<div class="row" style="text-align:center">
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">Период:</span>
						<input type="text" class="form-control" value="{{$period}}" name="period">
					</div>
					<div class="form-group">
						<label for="pad" class="col-xs-8 control-label" style="margin-top: 6px">Запросы: </label>
						<div class="col-xs-4">
							<input type="checkbox" name="requested" value="0" @if (isset($requested)) checked @endif style="margin-top: 12px">
						</div>
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
				<div class="row" style="display: flex; justify-content: center; margin-top: 15px;">
					<div class="col-xs-3">
						<div class="graph_block">
							<div class="heading text-left">Белые <span style="float: right">Все <input id="all_white" type="checkbox" name="all_white"></span></div>
							<hr class="affilaite_hr">
							<div id="white_links">
								@foreach ($links as $link)
									@if ($link['category']=='0' and $link['status']=='1')
										<span class="title_for_src">{{$link['title']}}</span>
										<input type="checkbox" 
										@if ($id_src)
											@foreach ($id_src as $src)
												@if ($src==$link['id'])
													checked
												@endif
											@endforeach
										@endif
										name="id_src[]" value="{{$link['id']}}" class="white_links white_links_1 checkbox_for_src">
										<hr class="affilaite_hr">
									@endif
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="graph_block">
							<div class="heading text-left">Адалт <span style="float: right">Все <input id="all_adult" type="checkbox" name="all_adult"></span></div>
							<hr class="affilaite_hr">
							<div id="adult_links">
								@foreach ($links as $link)
									@if ($link['category']=='1' and $link['status']=='1')
										<span class="title_for_src">{{$link['title']}}</span>
										<input type="checkbox" 
										@if ($id_src)
											@foreach ($id_src as $src)
												@if ($src==$link['id'])
													checked
												@endif
											@endforeach
										@endif
										name="id_src[]" value="{{$link['id']}}" class="white_links white_links_2 checkbox_for_src">
										<hr class="affilaite_hr">
									@endif
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="graph_block">
							<div class="heading text-left">Развлекательные <span style="float: right">Все <input id="all_razv" type="checkbox" name="all_razv"></span></div>
							<hr class="affilaite_hr">
							<div id="razv_links">
								@foreach ($links as $link)
									@if ($link['category']=='2' and $link['status']=='1')
										<span class="title_for_src">{{$link['title']}}</span>
										<input type="checkbox" 
										@if ($id_src)
											@foreach ($id_src as $src)
												@if ($src==$link['id'])
													checked
												@endif
											@endforeach
										@endif
										name="id_src[]" value="{{$link['id']}}" class="white_links white_links_3 checkbox_for_src">
										<hr class="affilaite_hr">
									@endif
								@endforeach
							</div>
						</div>
					</div>
					
					<div class="col-xs-3">
						<div class="graph_block">
							<div class="heading text-left">Удаленные и выключенные <span style="float: right">Все <input id="all_delete" type="checkbox" name="all_delete"></span></div>
							<hr class="affilaite_hr">
							<div id="all_links">
								@foreach ($links as $link)
									@if ($link['status']<>'1')
										<span class="title_for_src">{{$link['title']}}</span>
										<input type="checkbox" 
										@if ($id_src)
											@foreach ($id_src as $src)
												@if ($src==$link['id'])
													checked
												@endif
											@endforeach
										@endif
										name="id_src[]" value="{{$link['id']}}" class="white_links white_links_4 checkbox_for_src">
										<hr class="affilaite_hr">
									@endif
								@endforeach
							</div>
						</div>
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
		width: 250px!important;
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
		var n=0;
		for (i=0; i<$('.white_links_1').length; i++){
				if ($('.white_links_1')[i].checked==true){
					n++;
				}
		}
		if (n==$('.white_links_1').length){
			$('#all_white').prop('checked', true);
		}
		n=0;
		for (i=0; i<$('.white_links_2').length; i++){
				if ($('.white_links_2')[i].checked==true){
					n++;
				}
		}
		if (n==$('.white_links_2').length){
			$('#all_adult').prop('checked', true);
		}
		n=0;
		for (i=0; i<$('.white_links_3').length; i++){
				if ($('.white_links_3')[i].checked==true){
					n++;
				}
		}
		if (n==$('.white_links_3').length){
			$('#all_razv').prop('checked', true);
		}
		n=0;
		for (i=0; i<$('.white_links_4').length; i++){
				if ($('.white_links_4')[i].checked==true){
					n++;
				}
		}
		if (n==$('.white_links_4').length){
			$('#all_delete').prop('checked', true);
		}
		$('#all_white').on("click", function() {		
			if($(this).is(":checked")){
				for (i=0; i<$('.white_links_1').length; i++){
					$('.white_links_1')[i].checked=true;
				}
			}
			else {
				for (i=0; i<$('.white_links_1').length; i++){
					$('.white_links_1')[i].checked=false;
				}
			}
		});
		
		$('#all_adult').on("click", function() {
			if($(this).is(":checked")){
				for (i=0; i<$('.white_links_2').length; i++){
					$('.white_links_2')[i].checked=true;
				}
			}
			else {
				for (i=0; i<$('.white_links_2').length; i++){
					$('.white_links_2')[i].checked=false;
				}
			}
		});
		
		$('#all_razv').on("click", function() {
			if($(this).is(":checked")){
				for (i=0; i<$('.white_links_3').length; i++){
					$('.white_links_3')[i].checked=true;
				}
			}
			else {
				for (i=0; i<$('.white_links_3').length; i++){
					$('.white_links_3')[i].checked=false;
				}
			}
		});
		
		$('#all_delete').on("click", function() {
			if($(this).is(":checked")){
				for (i=0; i<$('.white_links_4').length; i++){
					$('.white_links_4')[i].checked=true;
				}
			}
			else {
				for (i=0; i<$('.white_links_4').length; i++){
					$('.white_links_4')[i].checked=false;
				}
			}
		});
		
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