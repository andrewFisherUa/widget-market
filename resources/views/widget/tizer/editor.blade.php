@extends('layouts.app')
@push('cabinet_home')

 <link rel="stylesheet" type="text/css" href="https://widget.market-place.su/projects/styles/tree.css?v=1.0"/>
@endpush
@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{!! session('message_success') !!}
		</div>
	@endif
	@if (Session::has('message_war'))
		<div class="alert alert-warning">
			{!! session('message_war') !!}
		</div>
	@endif
		@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
			<a href="{{route('admin.home', ['user_id'=>$user->id])}}" style="font-weight: bold">{{$user->name}}</a>
		@endif
		<h4 class="text-center">Редактирование тизерного виджета №{{$widget->id}} {{$widget->partnerPad->domain}}</h4>
        <div class="col-xs-5">
			<form id="form-custom-widget" class="form-horizontal" method="post" novalidate action="{!! route('widget.tizer.save', ['id'=>$id_widget]) !!}">
			{{ csrf_field() }}
				<input name="id_widget" value="{{$id_widget}}" readonly hidden>
				<div class="form-group">
					<label for="template" class="col-xs-5 control-label">Шаблон</label>
					<div class="col-xs-6">
						<select name="template" id="template_block" class="form-control">
							@foreach ($widgetTemplates as $template)
								<option @if ($widgetCustom->name==$template->name) selected @endif value="{{$template->name}}">{{$template->title}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group" id="mobile_block">
					<label for="mobile_block" class="col-xs-5 control-label">Мобильная версия</label>
					<div class="col-xs-6">
						<input type="checkbox" name="mobile" id="mobile"  @if ($widgetCustom->mobile==1) checked @endif value="1">
					</div>
				</div>
				<div id="group-width" class="form-group">
					<label for="width-template" class="col-xs-5 control-label">Ширина</label>
					<div class="col-xs-6">
						<input name="width" id="width-template" type="number" min="10" @if ($widgetCustom->width==0) readonly @endif value="{{ $widgetCustom->width or '200' }}" class="form-control">
						<div id="width_compl"></div>
					</div>
				</div>
				<div id="group-width-full" class="form-group">
					<label for="width-template-full" class="col-xs-5 control-label">Растянуть по ширине</label>
					<div class="col-xs-6">
						<input name="width_full" id="width-template-full" @if ($widgetCustom->width==0) checked @endif type="checkbox" value="1">
					</div>
				</div>
				<div id="group-height" class="form-group">
					<label for="height-template" class="col-xs-5 control-label">Высота</label>
					<div class="col-xs-6">
						<input name="height" id="height-template" type="number" min="10" @if ($widgetCustom->height==0) readonly @endif value="{{ $widgetCustom->height or '200' }}" class="form-control">
						<div id="height_compl"></div>
					</div>
				</div>
				<div id="group-height-full" class="form-group">
					<label for="height-template-full" class="col-xs-5 control-label">Растянуть по высоте</label>
					<div class="col-xs-6">
						<input name="height_full" id="height-template-full" @if ($widgetCustom->height==0) checked @endif type="checkbox" value="1">
					</div>
				</div>
				<div id="group-cols" class="form-group">
					<label for="column" class="col-xs-5 control-label">Кол-во колонок</label>
					<div class="col-xs-6">
						<input name="cols" id="cols" type="number" min="1" max="10" value="{{ $widgetCustom->cols or '1' }}" class="form-control">
					</div>
				</div>
				<div id="group-row" class="form-group">
					<label for="row" class="col-xs-5 control-label">Кол-во рядов</label>
					<div class="col-xs-6">
						<input name="row" type="number" id="row" min="1" max="10" value="{{ $widgetCustom->row or '1' }}" class="form-control">
					</div>
				</div>
				<div id="group-background-color" class="form-group">
					<label for="background-color" class="col-xs-5 control-label">Цвет фона</label>
					<div class="col-xs-6">
						<input type="text" name="background" class="form-control" id="background-color-input" data-format="rgb" data-opacity="true" value="@if (!stristr($widgetCustom->background_model, 'linear')){{$widgetCustom->background or 'rgba(255,255,255,1)'}}@endif">
					</div>
				</div>
				<div class="form-group text-center">
					<button type="submit" class="btn btn-primary">
						Сохранить
					</button>
				</div>
			</form>
		</div>
		<div class="col-xs-7">
		<ul class="nav nav-tabs" role="tablist" id="myTabs">
			<li id="desctop-block" role="presentation" class="active"><a href="#desctop" aria-controls="desctop" role="tab" data-toggle="tab">Десктоп</a></li>
			<li id="mobile-block"  @if ($widgetCustom->mobile==1) style="display: block" @else style="display: none" @endif role="presentation"><a href="#mobile_area" aria-controls="mobile-area" role="tab" data-toggle="tab">Мобильный</a></li>
			<li role="presentation"><a href="#code" aria-controls="code" role="tab" data-toggle="tab">Код</a></li>
		  </ul>

		  <!-- Tab panes -->
		  <div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="desctop"  data-idtype="{{$widget->type}}" data-id="{{$id_widget}}"></div>
			<div role="tabpanel" class="tab-pane" id="mobile_area"   data-idtype="{{$widget->type}}" data-id="{{$id_widget}}">
			<div id="resize_mobile">
					<img class="resize_mobile_img" src="/images/cabinet/resize.png">
					<img class="resize_mobile_img_hover" src="/images/cabinet/resize-hover.png">
				</div>
				<div id="version" class="btn btn-primary">У меня Android</div>
				<div id="preview_mobile">
					<div id="preview_mobile_cut">
						<div id="preview_mobile_cut_cut">Олимпийские игры, которые пройдут в южнокорейском Пченчхане в феврале 2018 года.</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="code">
			<textarea class="give-code" readonly><div class="mpwidget" data-id="{{$id_widget}}"></div><script type="text/javascript"  src="//node.market-place.su/div/api.js?v=2"></script></textarea>
			</div>
		  </div>
		</div>
    </div>
</div>
</div>
    
@endsection
@push('cabinet_home')
<link rel="stylesheet" type="text/css" href="{{ asset('minicolors/jquery.minicolors.css') }}" />
<style>
.give-code{
width: 100%;
height: 200px;
resize: none;
border: none;
}
.give-code:focus{
border: none;
outline: none; 
}
#border_width, #border_radius{
padding: 6px 0;
border: none;
box-shadow: none;
width: 80%;
float: left;
}
#border-width:focus, #border-radius:focus{
border: none;
box-shadow: none;
}
.min_left, .max_right{
width: 10%;
text-align: center;
line-height: 36px;
display: inline-block;
}
.min_left{
float: left;
}
.max_right{
float: right;
}
#desctop, #mobile_area{
padding-top: 10px;
}
#width_compl, #height_compl{
display: none;
color: #a20000;
font-weight: bold;
}
#preview_mobile{
width: 273px;
height: 571px;
position: relative;
background: url('/images/cabinet/cabinet-mobile.png');
margin: 0 auto;
padding: 5px;
transition: 0.3s;
}
#preview_mobile_cut{
    position: absolute;
    width: 228px;
    height: 406px;
    top: 85px;
    left: 24px;
	padding: 0 5px;
	font-size: 12px;
    text-align: justify;
    color: #000;
	word-wrap: break-word;
    overflow: hidden;
}
#resize_mobile{
	position: relative;
	cursor: pointer;
}
.resize_mobile_img{
    position: absolute;
    z-index: 3;
    -webkit-transition: all .2s;
    -moz-transition: all .2s;
    -o-transition: all .2s;
    -ms-transition: all .2s;
    transition: all .2s;
}
.resize_mobile_img_hover{
    position: absolute;
    z-index: 1;
}
#resize_mobile:hover .resize_mobile_img{
	    opacity: 0;
    -webkit-transition: all .2s;
    -moz-transition: all .2s;
    -o-transition: all .2s;
    -ms-transition: all .2s;
    transition: all .2s;
}
.rotate{
	transform: rotate(-90deg);
}
#preview_mobile_cut_cut{
width : 238px;
height : 406px;
overflow : auto;
cursor: pointer;
}
#version{
position: relative;
top: 50px;
}
.colors_div{
    top: 3px;
    text-align: center;
    border: solid 1px #000;
    line-height: 28px;
    width: 28px;
    height: 28px;
    border-radius: 3px;
    display: inline-block;
}
.color_active{
	background: #252525;
	color: #fff;
}
</style>
@endpush
@push('cabinet_home_js')
<script src="/projects/widgeteditor.js?v=3"></script>
<script src="{{ asset('minicolors/jquery.minicolors.min.js') }}"></script>
<script>
	$(document).ready(function() {
		
		var desctop=$('#desctop');
		var mobile=$('#mobile_area');
		
		console.log(desctop);
		
		var s=new WidgetEditor(desctop);
		var q=new WidgetEditor(mobile);
		if ($('#width-template').val()=='0'){
			var width='100%';
		}
		else{
			var width=$('#width-template').val()+"px";
		}
		if ($('#height-template').val()=='0'){
			var height='100%';
		}
		else{
			var height=$('#height-template').val()+"px";
		}
		
		var data = {
		name: $('#template_block option:selected').val(),
		width: width,
		height: height,
		cols: $('#cols').val(),
		row: $('#row').val(),
		count: $('#cols').val()*$('#row').val(),
		};
		var dataMobil = {
		name: 'block-mobile',
		count: 2,
		};
		console.log(data);
		s.createIframe(data);
		q.createIframe(dataMobil);
		//   console.log(s);
		setTimeout(function(){
			reloaddata();
		}, 2000);
		
		$('#background-color-input').minicolors({
		control: $(this).attr('data-control') || 'hue',
        defaultValue: $(this).attr('data-defaultValue') || '',
        format: $(this).attr('data-format') || 'rgb',
        keywords: $(this).attr('data-keywords') || '',
        inline: $(this).attr('data-inline') === 'true',
        letterCase: $(this).attr('data-letterCase') || 'lowercase',
        opacity: $(this).attr('data-opacity') || 'true',
        position: $(this).attr('data-position') || 'bottom left',
        swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
        change: function(value, opacity) {
			if( !value ) return;
			if( opacity ) value += ', ' + opacity;
			if( typeof console === 'object' ) {
				console.log(value);
            }
			reloaddata();
          },
		theme: 'bootstrap'
		  });
		
		$('#width-template').change(function(){
			reloaddata();
		});
		
		$('#height-template').change(function(){
			reloaddata();
		});
		
		$('#cols').change(function(){
			reloaddata();
		});
		
		$('#row').change(function(){
			reloaddata();
		});
		$('#width-template-full').on("click", function() {
			reloaddata();
		});
		$('#template_block').change(function(){
			reloaddata();
		});
		
		$('#height-template-full').on("click", function() {
			reloaddata();
		});
		function reloaddata(){
			/* Проверки цветов */
			if ($('#template_block option:selected').val()=="module-block-fullo"){
				$('#width-template').prop('min', 200*$('#cols').val());
				if ($('#width-template').val()<200*$('#cols').val()){
					$('#width-template').val(200*$('#cols').val());
					$('#width_compl').html('Мин. ширина 1 блока 200 px');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').prop('min', 25+175*$('#row').val());
				if ($('#height-template').val()<25+175*$('#row').val()){
					$('#height-template').val(25+175*$('#row').val());
					$('#height_compl').html('Мин. высота 1 блока 175px + 25px подвал');
					$('#height_compl').css('display', 'block');
				}
				else{
					$('#height_compl').css('display', 'none');
				}
				
				$('#background-model-label').html('Цвет фона карточки товара');
				$('#background-model-hover-label').html('Цвет фона при наведении');
				$('#group-background-model').css('display', 'block');
				$('#group-cols').css('display', 'block');
				$('#group-height').css('display', 'block');
				$('#group-height-full').css('display', 'block');
				$('#row').prop('min', '1');
			}
			if ($('#template_block option:selected').val()=="module-block"){
				$('#width-template').prop('min', 200*$('#cols').val());
				if ($('#width-template').val()<200*$('#cols').val()){
					$('#width-template').val(200*$('#cols').val());
					$('#width_compl').html('Мин. ширина 1 блока 200px');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').prop('min', 42+158*$('#row').val());
				if ($('#height-template').val()<42+158*$('#row').val()){
					$('#height-template').val(42+158*$('#row').val());
					$('#height_compl').html('Мин. высота 1 блока 158px + 42px подвал и шапка');
					$('#height_compl').css('display', 'block');
				}
				else{
					$('#height_compl').css('display', 'none');
				}
				$('#group-background-model').css('display', 'none');
				$('#background-model-label').html('Цвет фона карточки товара');
				$('#background-model-hover-label').html('Цвет фона при наведении');
				$('#group-cols').css('display', 'block');
				$('#group-height').css('display', 'block');
				$('#group-height-full').css('display', 'block');
				$('#row').prop('min', '1');
			}
			//---------------------------------------------------------------
			if ($('#height-template-full').prop('checked')==true){
				$('#height-template').val('0');
				$('#height-template').prop('readonly', true);
				$('#height_compl').css('display', 'none');
			}
			else{
				$('#height-template').prop('readonly', false);
			}
			if ($('#width-template-full').prop('checked')==true){
				$('#width-template').val('0');
				$('#width-template').prop('readonly', true);
				$('#width_compl').css('display', 'none');
			}
			else{
				$('#width-template').prop('readonly', false);
			}
			if ($('#width-template').val()=='0'){
				var width='100%';
			}
			else{
				var width=$('#width-template').val()+"px";
			}
			if ($('#height-template').val()=='0'){
				var height='100%';
			}
			else{
				var height=$('#height-template').val()+"px";
			}
			var data = {
				name: $('#template_block option:selected').val(),
				width: width,
				height: height,
				cols: $('#cols').val(),
				row: $('#row').val(),
				count: $('#cols').val()*$('#row').val(),
				background: $('#background-color-input').val(),
				};
				console.log(["парамс у",data]);
				console.log(["1234565",data]);
			s.reloadIframe(data);
			
		}
		window.reloaddata=reloaddata;
		$('#mobile').on("click", function() {
			if($(this).is(":checked")){
				$('#mobile-block').css('display', 'block');
				$('#mobile_block_template').css('display', 'block');
				$('#acc_mobile').css('display', 'block');
			}
			else {
				$('#mobile-block').removeClass('active');
				if ($('#mobile_area').hasClass('active')){
					$('#mobile_area').removeClass('active');
					$('#desctop').addClass('active');
					$('#desctop-block').addClass('active');
				}
				$('#mobile-block').css('display', 'none');
				$('#mobile_block_template').css('display', 'none');
				$('#acc_mobile').css('display', 'none');
			}
		});
		$('#form-custom-widget').keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
		
		
	});
</script>
 @endpush