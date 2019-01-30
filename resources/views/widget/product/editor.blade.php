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
		<h4 class="text-center">Редактирование товарного виджета №{{$widget->id}} {{$widget->partnerPad->domain}}</h4>
        <div class="col-xs-5">
			<form id="form-custom-widget" class="form-horizontal" method="post" novalidate action="{{ route('widget.save', ['id'=>$id_widget]) }}">
			{{ csrf_field() }}
			<input name="id_widget" value="{{$id_widget}}" readonly hidden>
			<div class="panel-group" id="accordion">

				

				{{-- панель Настройки --}}
				@if ($selfEdit or \Auth::user()->hasRole("admin") or \Auth::user()->hasRole("super_manager") or \Auth::user()->hasRole("manager"))
				<div class="panel panel-default">
			    @else
				<div class="panel panel-default" style="display:none">
			    @endif
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Настройки</a>
						</h4>
					</div>
					<div id="collapseThree" class="panel-collapse collapse in">
						<div class="panel-body">
							@if (\Auth::user()->hasRole("admin") or \Auth::user()->hasRole("super_manager") or \Auth::user()->hasRole("manager"))
							<div id="driver_group" class="form-group">
								<label for="driver" class="col-xs-5 control-label">Драйвер</label>
								<div class="col-xs-6">
									<select name="driver" id="driver" class="form-control" onchange="postReload();">
										<option value="1" @if ($widgetCustom->driver == 1) selected @endif >ТопАдверт</option>
										<option value="2" @if ($widgetCustom->driver == 2) selected @endif >Яндекс</option>
										{{-- драйвер для Яндекс API  --}}
										<option value="3" @if ($widgetCustom->driver == 3) @php @endphp selected @endif style='color: blue; font-weight: bold;' >Яндекс API</option>
										
										<option value="11" @if ($widgetCustom->driver == 11) selected @endif >Надави</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="nosearch" class="col-xs-5 control-label">отключить поиск</label>
								   <div class="col-xs-6" style="top: 6px;">
									<input type="checkbox" @if($widgetCustom->nosearch==1) checked @endif name="nosearch" >
									</div>
							</div>
							<div class="form-group">
								<label for="css_select" class="col-xs-5 control-label">css селекты</label>
								<div class="col-xs-6" style="top: 6px;">
									<span>h1</span>
									<input type="checkbox" @if(strripos($widgetCustom->css_select, 'h1')!==false) checked @endif name="css_select[]" value="h1">
									<span>title</span>
									<input type="checkbox" @if(strripos($widgetCustom->css_select, 'title')!==false) checked @endif name="css_select[]" value="title">
									<span>h2</span>
									<input type="checkbox" @if(strripos($widgetCustom->css_select, 'h2')!==false) checked @endif name="css_select[]" value="h2">
								</div>
							</div>
							@endif
							<div class ="cur_panel">
							@if (\Auth::user()->hasRole("admin") or \Auth::user()->hasRole("super_manager") or \Auth::user()->hasRole("manager"))
							<div id="driver_group" class="form-group">
								<label for="driver" class="col-xs-2 control-label">Запрос</label>
								<div class="col-xs-8">
                                <input id="main_yandex_text" type="text" value ="{{$defaultText}}" onkeyup="OnKeyInputSearch(event)"/> 
								</div>
								<div class="col-xs-2">
                                <button type="button" class="btn-sm" onclick=" postReload();">Поиск</button>
								</div>
							</div>
							@endif
							<div class="text-center form-group">
								<div class="col-xs-6">
								    <span data-id="0" class="btn btn-success" onclick="createMyTreeElenent(this,'search','Категории поиска');" >Выбрать категории</span>
									{{--<span data-id="0" onclick="createMyTreeElenent(this,'search','Категории поиска');" class="glyphicon glyphicon-edit"></span>--}}
								</div>
								<div class="col-xs-6">
									<ul id="main_yandex_categories" class="list-inline">
											
											@foreach($yandex_categories as $ya)
												<li onclick="var dst=attachBtnObject(this);  this.parentNode.removeChild(this); postReload(dst);">
													<div class="btn btn-default btn-sm posit-rel" style="white-space: inherit;">
														<input name="cattuya[0][{{$ya->id}}]" value="{{$ya->id}}" type="hidden">
														{{$ya->uniq_name}}
													</div>
												</li> 
											@endforeach
										
									</ul>
								</div>
							</div>
							</div>
							<div class="form-group">
								<h5 class="text-center">Маски</h5>
								<div class="col-xs-12">
									<div id="sortable">
										<div class="JsSel">
										</div>
										@foreach($page_maska as $maska)
													<div class="JsSel cur_panel" style="margin: 5px auto; border-bottom:1px solid #d3e0e9">
			<div class ="insert category">
			<div class="row">
			<label for="driver" class="col-xs-3 control-label">Url:</label>
			<div class="col-xs-6"><input type="text" name="agrotopot[]" value ="{{$maska["url"]}}"></div>
			<div class="col-xs-3"><a class="btn btn-success" onclick="postReload(this.parentNode.parentNode.parentNode.querySelector('.btn.btn-info'));">Поиск</a></div>
			</div>
			<div class="row">
			<label for="driver" class="col-xs-3 control-label">Запрос:</label>
			<input type="hidden" class="mask_category" name ="blink_categories[]" value="{{$maska["strK"]}}">
			<div class="col-xs-6"><input type="text" class="firma" name="agrofirma[]" onkeyup="OnKeyInputSearch(event);" value ="{{$maska["searchtext"]}}"></div>
			<div class="col-xs-3"><a class="minus btn btn-danger">&ndash;</a></div>
			</div>
			
			<div class="row">
				<label for="driver" class="col-xs-3 control-label">Цена:</label>
				{{--<input type="hidden" class="mask_category" name ="blink_categories[]" value="">--}}
				<div class="col-xs-6">
					<input type="number" class="col-xs-5 summa_from" name="summa_from[]" style="padding-left:0; padding-right:0;" value="{{$maska["summa_from"]}}">
					<span class="col-xs-2 text-center" style="padding: 0;">&#8212;</span><input type="number" class="col-xs-5 summa_to" style="float: right; padding-left:0; padding-right:0;" name="summa_to[]" value="{{$maska["summa_to"]}}">
				</div>
			</div>
			
			<div class="row">
			<div class="col-xs-6"><span data-id="" onclick="createMyTreeElenent(this,'search','Категории поиска',1);" class="btn btn-info">Выбрать категорию</span></div>
			<div class="col-xs-6"><ul id="page_yandex_categories" class="list-inline">
			@foreach($maska["categories"] as $ccm)
			<li onclick="var dst=attachBtnObject(this); this.parentNode.removeChild(this); postReload(dst);"><div class="btn btn-default btn-sm posit-rel"><input name="caftuya[][][{{$ccm->id}}]" value="{{$ccm->id}}" type="hidden">{{$ccm->uniq_name}}</div></li>
			@endforeach
			</ul></div>
			</div>
			</div>
			</div>
							@endforeach
							<div class="information_json_plus text-center"></div>
						</div>
						<div class="text-center">
							<span class="btn btn-success plus">Добавить ссылку</span>
						</div>
					</div>
				</div>
			</div>
					</div>
				</div>
				
			</div>
			{{-- конец панели Настройки --}}


				{{-- панель Общие настройки --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Общие настройки</a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group">
								<label for="type" class="col-xs-5 control-label">Тип шаблона</label>
								<div class="col-xs-6">
								<select name="type" id="ttype" class="form-control">
									@foreach ($templateTypes as $tType)
										<option @if (($widgetCustom->driver!= 3) && ($widgetCustom->type==$tType->id)) selected @endif data-type="{{$tType->id}}" value="{{$tType->id}}">{{$tType->title}}</option>
									@endforeach
								</select>
								</div>
							</div>

							<div class="form-group">
								<label for="template" class="col-xs-5 control-label">Шаблон</label>
								<div class="col-xs-6">
									<select name="template" id="template_block" class="form-control">
										@foreach ($widgetTemplates as $wTemplate)
											@if ($widgetCustom->type)
												@if ($wTemplate->type==$widgetCustom->type)
													<option @if ($widgetCustom->name==$wTemplate->name) selected @endif value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>
												@endif
											@else
												@if ($wTemplate->type=='1')
													<option @if ($widgetCustom->name==$wTemplate->name) selected @endif value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>
												@endif
											@endif
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
							<div class="form-group" id="mobile_block_template" @if ($widgetCustom->mobile==1) style="display: block" @else style="display: none;" @endif>
								<label for="mobile_block_template" class="col-xs-5 control-label">Мобильный шаблон</label>
								<div class="col-xs-6">
									<select name="mobile_block_template" id="mobile_block_template" class="form-control">
										@foreach ($widgetTemplates as $wTemplate)
											@if ($wTemplate->type==4)
												<option @if ($widgetCustom->name_mobile==$wTemplate->name) selected @endif value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				{{-- конец панели Общие настройки --}}


				{{-- панель Внешний Вид --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Внешний вид</a>
						</h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse">
						<div class="panel-body">
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
							<div id="group-border-width" class="form-group">
								<label for="border-width" class="col-xs-5 control-label">Толщина рамки</label>
								<div class="col-xs-6">
									<input type="range" name="border_width" class="form-control" value="{{ $widgetCustom->border_width or 1 }}" id="border_width" min="0" max="5" step="1"><span class="max_right" id="value_border">{{ $widgetCustom->border_width or 1 }}</span>
								</div>
							</div>
							<div id="group-border-radius" class="form-group">
								<label for="border-radius" class="col-xs-5 control-label">Закруглить углы</label>
								<div class="col-xs-6">
									<input type="range" name="border_radius" class="form-control" value="{{ $widgetCustom->border_radius or 1 }}" id="border_radius" min="0" max="10" step="1"><span class="max_right" id="radius_border">{{ $widgetCustom->border_radius or 1 }}</span>
								</div>
							</div>
							<div id="group-font-family" class="form-group">
								<label for="font-family" class="col-xs-5 control-label">Шрифт</label>
								<div class="col-xs-6">
									<select name="font_family" id="font-family" class="form-control">
										<option @if ($widgetCustom->font_family=='ArialRegular') selected @endif value="ArialRegular">Arial</option>
										<option @if ($widgetCustom->font_family=='ArimoRegular') selected @endif value="ArimoRegular">Arimo</option>
										<option @if ($widgetCustom->font_family=='GeorgiaRegular') selected @endif value="GeorgiaRegular">Georgia</option>
										<option @if ($widgetCustom->font_family=='MyriadProRegular') selected @endif value="MyriadProRegular">MyriadPro</option>
										<option @if ($widgetCustom->font_family=='OpenSansRegular') selected @endif value="OpenSansRegular">OpenSans</option>
										<option @if ($widgetCustom->font_family=='OxygenRegular') selected @endif value="OxygenRegular">Oxygen</option>
										<option @if ($widgetCustom->font_family=='RobotoRegular') selected @endif value="RobotoRegular">Roboto</option>
										<option @if ($widgetCustom->font_family=='TimesNewRomanRegular') selected @endif value="TimesNewRomanRegular">TimesNewRoman</option>
									</select>
								</div>
							</div>
							<div id="group-font-size" class="form-group">
								<label for="font-size" class="col-xs-5 control-label">Коэффициент размеров шрифтов</label>
								<div class="col-xs-6">
									<select name="font_size" id="font-size" class="form-control">
										<option @if ($widgetCustom->font_size=='0.8') selected @endif value="0.8">0.8</option>
										<option @if ($widgetCustom->font_size=='0.85') selected @endif value="0.85">0.85</option>
										<option @if ($widgetCustom->font_size=='0.9') selected @endif value="0.9">0.9</option>
										<option @if ($widgetCustom->font_size=='0.95') selected @endif value="0.95">0.95</option>
										<option @if ($widgetCustom->font_size=='1') selected @endif value="1">1</option>
										<option @if ($widgetCustom->font_size=='1.05') selected @endif value="1.05">1.05</option>
										<option @if ($widgetCustom->font_size=='1.1') selected @endif value="1.1">1.1</option>
										<option @if ($widgetCustom->font_size=='1.15') selected @endif value="1.15">1.15</option>
										<option @if ($widgetCustom->font_size=='1.2') selected @endif value="1.2">1.2</option>
										<option @if ($widgetCustom->font_size=='1.25') selected @endif value="1.25">1.25</option>
										<option @if ($widgetCustom->font_size=='1.3') selected @endif value="1.3">1.3</option>
										<option @if ($widgetCustom->font_size=='1.35') selected @endif value="1.35">1.35</option>
										<option @if ($widgetCustom->font_size=='1.4') selected @endif value="1.4">1.4</option>
										<option @if ($widgetCustom->font_size=='1.45') selected @endif value="1.4">1.45</option>
										<option @if ($widgetCustom->font_size=='1.5') selected @endif value="1.5">1.5</option>
									</select>
								</div>
							</div>
							<h4 class="text-center" style="font-weight: bold;">Цветовая гамма</h4>
								<div class="col-xs-12">
									<h5 class="text-center">Цветовые решения</h5>
									<div class="colors_div @if (stristr($widgetCustom->background_model, 'linear-gradient(to bottom, rgba(255, 255, 255, 0) 0, rgba(255, 208, 0, 1) 100%)')) color_active @endif" id="color_1">1</div>
									<div class="colors_div @if (stristr($widgetCustom->background_model, 'linear-gradient(to bottom, rgba(227,235,237,1) 0%,rgba(208,210,213,1) 100%)')) color_active @endif" id="color_2">2</div>
									<div class="colors_div @if (!stristr($widgetCustom->background_model, 'linear')) color_active @endif" style="float: right" id="color_s">С</div>
								</div>
							<input name="background_text" type="text" value="@if (stristr($widgetCustom->background_model, 'linear')) {{$widgetCustom->background}} @endif" class="form-control" id="background-color-textarea" style="display: none">
							<input name="border_color_text" type="text" value="@if (stristr($widgetCustom->background_model, 'linear')) {{$widgetCustom->border_color}} @endif" class="form-control" id="border-color-textarea" style="display: none">
							<input name="background_model_text" type="text" value="@if (stristr($widgetCustom->background_model, 'linear')) {{$widgetCustom->background_model}} @endif" class="form-control" id="background-model-textarea" style="display: none">
							<input name="background_model_hover_text" type="text" value="@if (stristr($widgetCustom->background_model, 'linear')) {{$widgetCustom->background_model_hover}} @endif" class="form-control" id="background-model-hover-textarea" style="display: none">
							<div id="area_color" @if (stristr($widgetCustom->background_model, 'linear')==true) style="display: none" @endif>
								<div id="group-background-color" class="form-group">
									<label for="background-color" class="col-xs-5 control-label">Цвет фона</label>
									<div class="col-xs-6">
										<input type="text" name="background" class="form-control" id="background-color-input" data-format="rgb" data-opacity="true" value="@if (!stristr($widgetCustom->background_model, 'linear')){{$widgetCustom->background or 'rgba(255,255,255,1)'}}@endif">
									</div>
								</div>
								<div id="group-border-color" class="form-group">
									<label for="border-color" class="col-xs-5 control-label">Цвет рамки</label>
									<div class="col-xs-6">
										<input type="text" name="border_color" class="form-control" id="border-color-input" data-format="rgb" data-opacity="true" value="@if (!stristr($widgetCustom->background_model, 'linear')){{$widgetCustom->border_color or 'rgba(0, 0, 0, .1)'}}@endif">
									</div>
								</div>
								<div id="group-background-model" class="form-group">
									<label id="background-model-label" for="background-model" class="col-xs-5 control-label">Цвет фона карточки товара</label>
									<div class="col-xs-6">
										<input type="text" name="background_model" class="form-control" id="background-model-input" data-format="rgb" data-opacity="true" value="@if (!stristr($widgetCustom->background_model, 'linear')){{$widgetCustom->background_model or 'rgba(255,255,255,1)'}}@endif">
									</div>
								</div>
								<div id="group-background-model-hover" class="form-group">
									<label id="background-model-hover-label" for="background-model-hover" class="col-xs-5 control-label">Цвет фона при наведении</label>
									<div class="col-xs-6">
										<input type="text" name="background_model_hover" class="form-control" id="background-model-hover-input" data-format="rgb" data-opacity="true" value="@if (!stristr($widgetCustom->background_model, 'linear')){{$widgetCustom->background_model_hover or 'rgba(255,255,255,1)'}}@endif">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default" id="acc_mobile" @if ($widgetCustom->mobile==1) style="display: block" @else style="display: none;" @endif>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseMobile">Внешний вид мобильного шаблона</a>
						</h4>
					</div>
					<div id="collapseMobile" class="panel-collapse collapse">
						<div class="panel-body">
							<div id="mobile-background-color" class="form-group">
								<label for="mobile-background" class="col-xs-5 control-label">Цвет фона</label>
								<div class="col-xs-6">
									<input type="text" name="mobile_background" class="form-control" id="mobile-background" data-format="rgb" data-opacity="true" value="{{ $widgetCustom->mobile_background or 'rgba(255,255,255,1)' }}">
								</div>
							</div>
							<div id="mobile-background-color-model" class="form-group">
								<label id="mobile-background-model-label" for="background-model" class="col-xs-5 control-label">Цвет фона карточки товара</label>
								<div class="col-xs-6">
									<input type="text" name="mobile_background_model" class="form-control" id="mobile-background-model" data-format="rgb" data-opacity="true" value="{{ $widgetCustom->mobile_background_model or 'rgba(255,255,255,1)' }}">
								</div>
							</div>
							<div id="mobile-background-model-hover" class="form-group">
								<label id="mobile-background-model-hover-label" for="background-model-hover" class="col-xs-5 control-label">Цвет фона при наведении</label>
								<div class="col-xs-6">
									<input type="text" name="mobile_background_model_hover" class="form-control" id="mobile-background-model-hover-input" data-format="rgb" data-opacity="true" value="{{ $widgetCustom->mobile_background_model_hover or 'rgba(255,255,255,1)' }}">
								</div>
							</div>
							<div id="mobile-font-family" class="form-group">
								<label for="mobile-font-family" class="col-xs-5 control-label">Шрифт</label>
								<div class="col-xs-6">
									<select name="mobile_font_family" id="mobile-font-family" class="form-control">
										<option @if ($widgetCustom->mobile_font_family=='ArialRegular') selected @endif value="ArialRegular">Arial</option>
										<option @if ($widgetCustom->mobile_font_family=='ArimoRegular') selected @endif value="ArimoRegular">Arimo</option>
										<option @if ($widgetCustom->mobile_font_family=='GeorgiaRegular') selected @endif value="GeorgiaRegular">Georgia</option>
										<option @if ($widgetCustom->mobile_font_family=='MyriadProRegular') selected @endif value="MyriadProRegular">MyriadPro</option>
										<option @if ($widgetCustom->mobile_font_family=='OpenSansRegular') selected @endif value="OpenSansRegular">OpenSans</option>
										<option @if ($widgetCustom->mobile_font_family=='OxygenRegular') selected @endif value="OxygenRegular">Oxygen</option>
										<option @if ($widgetCustom->mobile_font_family=='RobotoRegular') selected @endif value="RobotoRegular">Roboto</option>
										<option @if ($widgetCustom->mobile_font_family=='TimesNewRomanRegular') selected @endif value="TimesNewRomanRegular">TimesNewRoman</option>
									</select>
								</div>
							</div>
						
						</div>
					</div>
				</div>
				{{-- конец панели Внешний Вид --}}


				




			<button type="submit" class="btn btn-primary">
				Сохранить
			</button>
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
						Эстафета олимпийского огня по традиции была проведена в Греции 24 октября 2017 года. Первым факелоносцем стал греческий лыжник Апостолос Ангелис. Эстафету от грека сразу принял бывший футболист английского «Манчестер Юнайтед» и сборной Южной Кореи Пак Чжи Сун
. По территории Греции эстафета продлится семь дней, а 31 октября на афинском стадионе «Панатинаикос» олимпийский огонь будет передан представителям оргкомитета Игр в Пхенчхане и закончится уже Пхёнчхане 9 февраля 2018 года.

По территории Южной Кореи эстафета Олимпийского огня стартует 1 ноября — за 100 дней до церемонии открытия игр. Маршрут эстафеты, в которой примут участие 7,5 тысяч факелоносцев, составит 2018 километров и пройдёт по 17-ти городам и провинциям страны.
						<div id="preview_mobile_cut_cut">
						Олимпийские игры, которые пройдут в южнокорейском Пченчхане в феврале 2018 года.
						</div>
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
    <div class="modal fade" id="myModalCategory" role="dialog">
    <div class="modal-dialog">
       <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Выбрать категорию</h4>
        </div>
        <div class="modal-body">
		<div class="row">
		<div class="col-lg-9">  
		<input type="text" class = "microsearch_input">
		</div><div class="col-lg-3">
		<button type="button" class = "microsearch_button" class="btn btn-primary btn-sm">Поиск</button>
		</div>
		</div>
        <div class="row" id ="teletree">
		</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Сохранить</button>
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
	transform: rotate(0deg);
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

max-width: 96%;
    left: 5px!important;
    overflow: hidden;
    right: 5px!important;
}
#preview_mobile_cut_cut iframe{
	position: absolute;
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;
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
.posit-rel{
white-space: inherit!important;
}
</style>
@endpush
@push('cabinet_home_js')
<script src="/projects/widgeteditor.js?v=3"></script>
<script src="{{ asset('minicolors/jquery.minicolors.min.js') }}"></script>

<script>
    var kolokolchikGlag=0;
	
    function driverManipulation(){ //функция выковыривания яндекс адаптивного драйвера отовсюду 
	    if(kolokolchikGlag) return;
		kolokolchikGlag=1;
		var iddriver=$('#driver option:selected').val();
		if(!iddriver){
			iddriver={{$widgetCustom->driver}};
		}
		console.log(["----d",iddriver,idtype]);
		var idtype=$('#ttype option:selected').val();
		
		if(iddriver == 3 ){
			//var tpb = $('#template_block option:selected').val();

			$('#ttype').val(5).change();
			//if(!tpb)
			//	tpb = 'list';
			//$('#template_block option:selected').val(tpb).change();
			
			$('#ttype option').each(function(item){
				if($(this).val() !=5){
					$(this).hide();
				}else{
					$(this).show();
				}
			});
		}else{

			$('#ttype').val(1).change();
			$('#ttype option').each(function(item){
				if($(this).val() !=5){
					$(this).show();
				}else{
					$(this).hide();					
				}
			});
		}
				kolokolchikGlag=0;
	}
	$(document).ready(function() {
		console.log("DOM is ready если что");
		driverManipulation();
		var desctop=$('#desctop');
		var mobile=$('#mobile_area');
		
		console.log(desctop);
		
		var s=new WidgetEditor(desctop);
		var q=new WidgetEditor(mobile);
		function reloadyandex(){
			
	    alert(s);
		var dataApi = {
			 	"containerId": "marketWidget",
				"type": "offers",
				"params": {
					"clid": 2328050,
					"searchText": "Смартфон",
					"themeId": 2
				}

			};
		console.log(["dataApi = ",dataApi]);
		s.reloadYandexAPI(dataApi);
		 
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
		var h1=$('#main_yandex_text').val();
		var summa_from=0;
		var summa_to=0;
		var driver=$('#driver').val();
		
			var lis = document.querySelectorAll("#main_yandex_categories li");
			prevs=[];
			for (var i=0,j=lis.length;i<j;i++){
            var inp=lis[i].querySelector("input");
            if(inp){
            prevs.push(inp.value);
            }
            }
		
		
		var data = {
		name: $('#template_block option:selected').val(),
		width: width,
		height: height,
		cols: $('#cols').val(),
		row: $('#row').val(),
		count: $('#cols').val()*$('#row').val(),
		background: $('#background-color-input').val(),
		border_color: $('#border-color-input').val(),
		border_width: $('#border_width').val(),
		border_radius: $('#border_radius').val(),
		background_model: $('#background-model-input').val(),
		background_model_hover: $('#background-model-hover-input').val(),
		font_family: $('#font-family option:selected').val(),
		font_size: $('#font-size option:selected').val(),
		name_mobile: $('#mobile_block_template option:selected').val(),
		driver: $('#driver option:selected').val(),
		categories:prevs,
		h1:h1,
		driver:driver,
		summa_from: summa_from,
		summa_to: summa_to,
		};
		var dataMobil = {
		name: $('#mobile_block_template option:selected').val(),
		count: 3,
		name_mobile: $('#mobile_block_template option:selected').val(),
		mobile_background: $('#mobile-background').val(),
		background_model: $('#mobile-background-model').val(),
		background_model_hover: $('#mobile-background-model-hover-input').val(),
		mobile_font_family: $('#mobile-font-family option:selected').val(),
		driver: $('#driver option:selected').val(),
		categories:prevs,
		h1:h1,
		summa_from: summa_from,
		summa_to: summa_to,
		};
		 s.createIframe(data);
		 q.createIframe(dataMobil);
		//console.log(s);
		setTimeout(function(){
		reloaddata();
		mobilereloaddata();
		}, 2000);
		//reloaddata();
		
		 
		$('#form-custom-widget').keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
		
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
		  	  
		$('#background-model-input').minicolors({
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
		  
		$('#border-color-input').minicolors({
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
		  
		$('#background-model-hover-input').minicolors({
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
		  
		$('#mobile-background').minicolors({
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
			mobilereloaddata();
          },
		theme: 'bootstrap'
		  });
		  
		$('#mobile-background-model').minicolors({
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
			mobilereloaddata();
          },
		theme: 'bootstrap'
		  });
		  
		$('#mobile-background-model-hover-input').minicolors({
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
			mobilereloaddata();
          },
		theme: 'bootstrap'
		  });
		  
		  
		$('#color_1').on("click", function() {
			$('#color_2').removeClass('color_active');
			$('#color_s').removeClass('color_active');
			$('#color_1').addClass('color_active');
			$('#area_color').css('display', 'none');
			$('#background-color-input').val('');
			$('#border-color-input').val('');
			$('#background-model-input').val('');
			$('#background-model-hover-input').val('');
			
			$('#background-color-textarea').val('rgba(255,255,255,1)');
			$('#border-color-textarea').val('rgba(0,0,0,0.1)');
			$('#background-model-textarea').val('linear-gradient(to bottom, rgba(255, 255, 255, 0) 0, rgba(255, 208, 0, 1) 100%)');
			$('#background-model-hover-textarea').val('linear-gradient(to bottom, rgba(255, 255, 255, 0) 0, rgba(255, 119, 0, 1) 100%)');
			
			reloaddata();
		});
		
		$('#color_2').on("click", function() {
			$('#color_1').removeClass('color_active');
			$('#color_s').removeClass('color_active');
			$('#color_2').addClass('color_active');
			$('#area_color').css('display', 'none');
			$('#background-color-input').val('');
			$('#border-color-input').val('');
			$('#background-model-input').val('');
			$('#background-model-hover-input').val('');
			
			$('#background-color-textarea').val('rgba(255,255,255,1)');
			$('#border-color-textarea').val('rgba(0,0,0,0.1)');
			$('#background-model-textarea').val('linear-gradient(to bottom, rgba(227,235,237,1) 0%,rgba(208,210,213,1) 100%)');
			$('#background-model-hover-textarea').val('linear-gradient(to bottom, rgba(250,151,85,1) 0%,rgba(224,134,74,1) 100%)');
			
			reloaddata();
		});
		
		$('#color_s').on("click", function() {
			$('#color_1').removeClass('color_active');
			$('#color_2').removeClass('color_active');
			$('#color_s').addClass('color_active');
			$('#area_color').css('display', 'block');
			$('#background-color-input').val('rgba(255,255,255,1)');
			$('#border-color-input').val('rgba(255,255,255,1)');
			$('#background-model-input').val('rgba(255,255,255,1)');
			$('#background-model-hover-input').val('rgba(255,255,255,1)');
			
			$('#background-color-textarea').val('');
			$('#border-color-textarea').val('');
			$('#background-model-textarea').val('');
			$('#background-model-hover-textarea').val('');
			
			reloaddata();
		});
		
		
		
		$('#ttype').change(function(){
			if ($('#ttype option:selected').data('type')=='1'){
				jQuery ('#template_block').html(
					'@foreach ($widgetTemplates as $wTemplate)' +
						'@if ($wTemplate->type==1)' +
						'<option value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>' +
						'@endif' +
					'@endforeach'
				);
			}
			else if ($('#ttype option:selected').data('type')=='2'){
				jQuery ('#template_block').html(
					'@foreach ($widgetTemplates as $wTemplate)' +
						'@if ($wTemplate->type==2)' +
						'<option value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>' +
						'@endif' +
					'@endforeach'
				);
			}
			else if ($('#ttype option:selected').data('type')=='3'){
				jQuery ('#template_block').html(
					'@foreach ($widgetTemplates as $wTemplate)' +
						'@if ($wTemplate->type==3)' +
						'<option value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>' +
						'@endif' +
					'@endforeach'
				);
			}
			else if ($('#ttype option:selected').data('type')=='5'){
				jQuery ('#template_block').html(
					'@foreach ($widgetTemplates as $wTemplate)' +
						'@if ($wTemplate->type==5)' +
						'<option value="{{$wTemplate->name}}">{{$wTemplate->title}}</option>' +
						'@endif' +
					'@endforeach'
				);
			}
			reloaddata();
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
		
		$('#border_width').change(function(){
			$('#value_border').html($('#border_width').val());
			reloaddata();
		});
		
		$('#border_radius').change(function(){
			$('#radius_border').html($('#border_radius').val());
			reloaddata();
		});
		
		$('#font-family').change(function(){
			reloaddata();
		});
		
		$('#font-size').change(function(){
			reloaddata();
		});
		
		$('#driver').change(function(){
			reloaddata();
		});
		
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
		
		$('#width-template-full').on("click", function() {
			reloaddata();
		});
		
		$('#height-template-full').on("click", function() {
			reloaddata();
		});
		
		$('#template_block').change(function(){
			//kostil
			var iddriver=$('#driver option:selected').val();
		    if(!iddriver){
			iddriver={{$widgetCustom->driver}};
		    }
			
			if(iddriver==3){
				reloadyandex();
				/* делаем чота своё набазе готового говна*/
				
			}else{
			reloaddata();
		    }
		});
		
		function reloaddata(perevs,userext,summa_from,summa_to){
            driverManipulation();
			/* Проверки цветов */
			if (!$('#background-color-input').val()){
				var background_col=$('#background-color-textarea').val();
			}
			else{
				var background_col=$('#background-color-input').val();
			}
			if (!$('#border-color-input').val()){
				var border_col=$('#border-color-textarea').val();
			}
			else{
				var border_col=$('#border-color-input').val();
			}
			if (!$('#background-model-input').val()){
				var background_model_col=$('#background-model-textarea').val();
			}
			else{
				var background_model_col=$('#background-model-input').val();
			}
			if (!$('#background-model-hover-input').val()){
				var background_model_hover_col=$('#background-model-hover-textarea').val();
			}
			else{
				var background_model_hover_col=$('#background-model-hover-input').val();
			}
			
			
			

			/* Проверки по шаблона */
			if ($('#template_block option:selected').val()=="module-block-third-1"){
				$('#width-template').prop('min', 200*$('#cols').val());
				if ($('#width-template').val()<200*$('#cols').val()){
					$('#width-template').val(200*$('#cols').val());
					$('#width_compl').html('Мин. ширина 1 блока 200px');
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
			
			if ($('#template_block option:selected').val()=="module-block-third"){
				$('#width-template').prop('min', 200*$('#cols').val());
				if ($('#width-template').val()<200*$('#cols').val()){
					$('#width-template').val(200*$('#cols').val());
					$('#width_compl').html('Мин. ширина 1 блока 200px');
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
			
			if ($('#template_block option:selected').val()=="module-block-tetra"){
				$('#width-template').prop('min', 130*$('#cols').val());
				if ($('#width-template').val()<130*$('#cols').val()){
					$('#width-template').val(130*$('#cols').val());
					$('#width_compl').html('Мин. ширина 1 блока 130px');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').prop('min', 40+160*$('#row').val());
				if ($('#height-template').val()<40+160*$('#row').val()){
					$('#height-template').val(40+160*$('#row').val());
					$('#height_compl').html('Мин. высота 1 блока 160px + 40px подвал и шапка');
					$('#height_compl').css('display', 'block');
				}
				else{
					$('#height_compl').css('display', 'none');
				}
				$('#background-model-label').html('Цвет кнопки');
				$('#background-model-hover-label').html('Цвет кнопки при наведении');
				$('#group-background-model').css('display', 'block');
				$('#group-cols').css('display', 'block');
				$('#group-height').css('display', 'block');
				$('#group-height-full').css('display', 'block');
				$('#row').prop('min', '1');
			}
			
			if ($('#template_block option:selected').val()=="module-block-new_widget"){
				if ($('#cols').val()<=2){
					$('#width-template').prop('min', 260);
					if ($('#width-template').val()<260){
						$('#width-template').val(260);
						$('#width_compl').html('Мин. ширина 260px');
						$('#width_compl').css('display', 'block');
					}
					else{
						$('#width_compl').css('display', 'none');
					}
				}
				else{
					$('#width-template').prop('min', 260+130*($('#cols').val()-2));
					if ($('#width-template').val()<260+130*($('#cols').val()-2)){
						$('#width-template').val(260+130*($('#cols').val()-2));
						$('#width_compl').html('Мин. ширина 260 px + 130 за каждый блок после второго');
						$('#width_compl').css('display', 'block');
					}
					else{
						$('#width_compl').css('display', 'none');
					}
				}
				$('#height-template').prop('min', 62+158*$('#row').val());
				if ($('#height-template').val()<62+158*$('#row').val()){
					$('#height-template').val(62+158*$('#row').val());
					$('#height_compl').html('Мин. высота 1 блока 158px + 62px подвал');
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
			
			if ($('#template_block option:selected').val()=="module-block-yandex_left"){
				$('#width-template').prop('min', 320+80*$('#cols').val());
				if ($('#width-template').val()<320+80*$('#cols').val()){
					$('#width-template').val(320+80*$('#cols').val());
					$('#width_compl').html('Мин. ширина 320px + 80px за каждый блок');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').val(16+82*$('#row').val());
				$('#background-model-label').html('Цвет фона карточки товара');
				$('#background-model-hover-label').html('Цвет фона при наведении');
				$('#group-background-model').css('display', 'block');
				$('#group-cols').css('display', 'block');
				$('#group-height').css('display', 'none');
				$('#group-height-full').css('display', 'none');
				$('#row').prop('min', '1');
			}
			
			
			
			
			if ($('#template_block option:selected').val()=="table-mini"){
				$('#cols').val('1');
				$('#row').prop('min', '2');
				if ($('#row').val()<2){
					$('#row').val('2');
				}
				$('#group-cols').css('display', 'none');
				$('#width-template').prop('min', 500*$('#cols').val());
				if ($('#width-template').val()<500*$('#cols').val()){
					$('#width-template').val(500*$('#cols').val());
					$('#width_compl').html('Мин. ширина 500px');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').val(53+43*$('#row').val());
				$('#group-height').css('display', 'none');
				$('#group-height-full').css('display', 'none');
				$('#height-template-full').prop('checked', false);
				$('#background-model-label').html('Цвет кнопки');
				$('#background-model-hover-label').html('Цвет кнопки при наведении');
			}
			
			if ($('#template_block option:selected').val()=="table"){
				$('#cols').val('1');
				$('#row').prop('min', '2');
				if ($('#row').val()<2){
					$('#row').val('2');
				}
				$('#group-cols').css('display', 'none');
				$('#width-template').prop('min', 620*$('#cols').val());
				if ($('#width-template').val()<620*$('#cols').val()){
					$('#width-template').val(620*$('#cols').val());
					$('#width_compl').html('Мин. ширина 620px');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').prop('min', 25+88*$('#row').val());
				if ($('#height-template').val()<25+88*$('#row').val()){
					$('#height-template').val(25+88*$('#row').val());
					$('#height_compl').html('Мин. высота 25px + 88px за каждый блок');
					$('#height_compl').css('display', 'block');
				}
				else{
					$('#height_compl').css('display', 'none');
				}
				
				$('#group-height').css('display', 'block');
				$('#group-height-full').css('display', 'block');
				$('#background-model-label').html('Цвет кнопки');
				$('#background-model-hover-label').html('Цвет кнопки при наведении');
			}
			
			if ($('#template_block option:selected').val()=="table-no-foto"){
				$('#cols').val('1');
				$('#row').prop('min', '2');
				if ($('#row').val()<2){
					$('#row').val('2');
				}
				$('#group-cols').css('display', 'none');
				$('#width-template').prop('min', 510*$('#cols').val());
				if ($('#width-template').val()<510*$('#cols').val()){
					$('#width-template').val(510*$('#cols').val());
					$('#width_compl').html('Мин. ширина 510px');
					$('#width_compl').css('display', 'block');
				}
				else{
					$('#width_compl').css('display', 'none');
				}
				$('#height-template').prop('min', 25+87*$('#row').val());
				if ($('#height-template').val()<25+87*$('#row').val()){
					$('#height-template').val(25+87*$('#row').val());
					$('#height_compl').html('Мин. высота 25px + 87px за каждый блок');
					$('#height_compl').css('display', 'block');
				}
				else{
					$('#height_compl').css('display', 'none');
				}
				
				$('#group-height').css('display', 'block');
				$('#group-height-full').css('display', 'block');
				$('#background-model-label').html('Цвет кнопки');
				$('#background-model-hover-label').html('Цвет кнопки при наведении');
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
			if (!summa_from){
				summa_from=0;
			}
			if (!summa_to){
				summa_to=0;
			}
			    if(userext)
					var h1=userext;
					else
				var h1=$('#main_yandex_text').val();
				var driver=$('#driver').val();
				if(perevs){
			prevs=perevs;
				
			}else{
			var lis = document.querySelectorAll("#main_yandex_categories li");
			prevs=[];
			for (var i=0,j=lis.length;i<j;i++){
            var inp=lis[i].querySelector("input");
            if(inp){
            prevs.push(inp.value);
            }
            }
			}
			var data = {
				name: $('#template_block option:selected').val(),
				width: width,
				height: height,
				cols: $('#cols').val(),
				row: $('#row').val(),
				count: $('#cols').val()*$('#row').val(),
				background: background_col,
				border_color: border_col,
				border_width: $('#border_width').val(),
				border_radius: $('#border_radius').val(),
				background_model: background_model_col,
				background_model_hover: background_model_hover_col,
				font_family: $('#font-family option:selected').val(),
				font_size: $('#font-size option:selected').val(),
				name_mobile: $('#mobile_block_template option:selected').val(),
				driver: $('#driver option:selected').val(),
				categories:prevs,
				h1: h1,
				driver:driver,
				summa_from: summa_from,
				summa_to: summa_to,
				};
				console.log(["парамс у",data]);
			s.reloadIframe(data); 
			
			
		}
		window.reloaddata=reloaddata;
		function mobilereloaddata(perevs,usertext,summa_from,summa_to){
        driverManipulation();
			var h1=$('#main_yandex_text').val();
			var driver=$('#driver').val();
			if(perevs){
			prevs=perevs; 
			}else{
			var lis = document.querySelectorAll("#main_yandex_categories li");
			prevs=[];
			for (var i=0,j=lis.length;i<j;i++){
            var inp=lis[i].querySelector("input");
            if(inp){
            prevs.push(inp.value);
            }
            }
		    }
			if (!summa_from){
				summa_from=0;
			}
			if (!summa_to){
				summa_to=0;
			}
			var dataMobil = {
			name: $('#mobile_block_template option:selected').val(),
			count: 3,
			name_mobile: $('#mobile_block_template option:selected').val(),
			mobile_background: $('#mobile-background').val(),
			background_model: $('#mobile-background-model').val(),
			background_model_hover: $('#mobile-background-model-hover-input').val(),
			mobile_font_family: $('#mobile-font-family option:selected').val(),
			driver: $('#driver option:selected').val(),
			categories:prevs,
			h1: h1,
			driver:driver,
			summa_from: summa_from,
			summa_to: summa_to,
			}
			
			q.reloadIframe(dataMobil);
		}
		window.mobilereloaddata=mobilereloaddata;
		$('#mobile-font-family').change(function(){
			mobilereloaddata();
		});
		$('#mobile_block_template').change(function(){
			mobilereloaddata();
		});
		$('#resize_mobile').click(function(){
			if ($('#preview_mobile').hasClass("rotate")){
				$('#preview_mobile').removeClass("rotate");
				$('#preview_mobile_cut').css({'transform':'rotate(0deg)', 'width':'228px', 'height':'406px', 'top':'85px', 'left':'24px'});
				$('#preview_mobile_cut_cut').css({'width':'238px', 'height':'406px'});
			}
			else{
				$('#preview_mobile_cut').css({'transform':'rotate(90deg)', 'width':'406px', 'height':'228px', 'top':'174px', 'left':'-65px', 'padding-right': '10px'});
				$('#preview_mobile_cut_cut').css({'width':'416px', 'height':'228px'});
				$('#preview_mobile').addClass("rotate");
			}
			
		});
		$('#version').click(function(){
			if ($('#preview_mobile').css('background-image')=='url("https://widget.market-place.su/images/cabinet/cabinet-mobile.png")'){
				$('#preview_mobile').css('background-image', 'url("https://widget.market-place.su/images/cabinet/cabinet-mobile2.png")');
				$('#version').html('У меня iPhone');
			}
			else{
				$('#preview_mobile').css('background-image', 'url("https://widget.market-place.su/images/cabinet/cabinet-mobile.png")');
				$('#version').html('У меня Android');
			}
		});
		
	});
</script>
<script>
$('#myTabs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>
<script>
		jQuery('.plus').click(function(){
			jQuery('.information_json_plus').before(
			'<div class="JsSel cur_panel" style="margin: 5px auto; border-bottom:1px solid #d3e0e9">' + 
			'<div class ="insert category">'+
			'<div class="row">'+
			'<label for="driver" class="col-xs-3 control-label">Url:</label>'+
			'<div class="col-xs-6"><input type="text" style="width: 100%;" name="agrotopot[]"></div>'+
			'<div class="col-xs-3"><a class="btn btn-success" onclick="postReload(this.parentNode.parentNode.parentNode.querySelector(\'.btn.btn-info\'));">Поиск</a></div>'+
			'</div>'+
			'<div class="row">'+
			'<label for="driver" class="col-xs-3 control-label">Запрос:</label>'+
			'<input type="hidden" class="mask_category" name ="blink_categories[]" value="">'+ 
			'<div class="col-xs-6"><input type="text" class="firma" style="width: 100%;" name="agrofirma[]" onkeyup="OnKeyInputSearch(event)"></div>'+
			'<div class="col-xs-3"><a class="minus btn btn-danger">&ndash;</a></div>'+
			'</div>'+
			'<div class="row">'+
				'<label for="driver" class="col-xs-3 control-label">Цена:</label>'+
				'<input type="hidden" class="mask_category" name ="blink_categories[]" value="">'+
				'<div class="col-xs-6">'+
					'<input type="number" class="col-xs-5 summa_from" name="summa_from[]" style="padding-left:0; padding-right:0;" value="">'+
					'<span class="col-xs-2 text-center" style="padding: 0;">&#8212;</span><input type="number" class="col-xs-5 summa_to" style="float: right; padding-left:0; padding-right:0;" name="summa_to[]" value="">'+
				'</div>'+
			'</div>'+
			'<div class="row">'+
			'<div class="col-xs-6"><span data-id="" onclick="createMyTreeElenent(this,\'search\',\'Категории поиска\',1);" class="btn btn-info" style="margin: 5px 0;">Выбрать категории</span></div>'+
			'<div class="col-xs-6"><ul id="page_yandex_categories" class="list-inline"></ul></div>'+
			'</div>'+
			'</div>'+
			'</div>'
			);
		});
/*createMyTreeElenent(this,'search','Категории поиска');*/
		jQuery(document).on('click', '.minus', function(){
			jQuery( this ).parents('.JsSel').remove();
			
			//jQuery( this ).closest( 'div' ).remove(); // удаление строки с полями
		});
	</script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$( function() {
			$( "#sortable" ).sortable();
		});
	</script>
@endpush
 @push('cabinet_home_js')
<script src="https://widget.market-place.su/projects/categorytree.js?v=1.0"></script>
 <script>
 var KK=null;
  function attachBtnObject(obj){
	  var ww = obj.parentNode.parentNode.parentNode.querySelector(".btn.btn-info");
	  if(!ww){
		   var ww = obj.parentNode.parentNode.parentNode.querySelector(".btn.btn-success");
		  //alert(ww);
	  }
	  KK=ww;
	  return ww;
	  
  }	  
 function createMyTreeElenent(obj,cls,title,level){
  KK=obj;
   //KK=obj.parentNode.parentNode.querySelector(".last_diew");
  //alert(obj.parentNode.parentNode.innerHTML);
  //alert(KK);
  var resContainer=obj.parentNode.parentNode.querySelector("ul");
  var prevs=[];
  
  if(resContainer){
	  
	  
   var lis = resContainer.querySelectorAll("li");
  
   for (var i=0,j=lis.length;i<j;i++){
   var inp=lis[i].querySelector("input");
   if(inp){
   prevs.push(inp.value);
   }
   }
  }
 // window.reloaddata();
  $("#myModalCategory h4").html(title); 
  var tmp = new window.ContextCategory(obj,cls,$("#myModalCategory").get(0),resContainer,prevs,function(){
  },function(){
	//postReload(obj);
  },level);
   tmp.selfCloseNode=function(obj){
	
	   var dst=attachBtnObject(obj); obj.parentNode.removeChild(obj); postReload(dst); 
   
   }
  
  $("#myModalCategory").modal('toggle');
  }
  function postReload(dKK){
	       var drevs=null;
		   var usertext=null;
		   var summa_from=0;
		   var summa_to=0;
		   $(".cur_panel").each(function(){

				  //$(this).css("border","0");
			   });
	       if(dKK){
			   //alert();
			 
              KK=dKK;

			 //$(dKK).closest(".cur_panel").css("border","1px solid red");
			 var resBlick=dKK.parentNode.parentNode.parentNode.querySelector("input.mask_category");   
			 //alert(resBlick.value);  
			 var resText=dKK.parentNode.parentNode.parentNode.querySelector("input.firma");   
			 summa_from=dKK.parentNode.parentNode.parentNode.querySelector("input.summa_from").value;
			 summa_to=dKK.parentNode.parentNode.parentNode.querySelector("input.summa_to").value;
			 if(resText){
				 usertext=resText.value;
			//alert(resText.value);
		    var resContainer=dKK.parentNode.parentNode.querySelector("ul");
			var lis = resContainer.querySelectorAll("li");
			 var drevs=[];
			
			for (var i=0,j=lis.length;i<j;i++){
            var inp=lis[i].querySelector("input");
            if(inp){
				
             drevs.push(inp.value);
             }
             }
			 }
			 if(resBlick){
			 if(drevs)
			 resBlick.value = drevs.join(",");  
		     else 
			 resBlick.value="";
			 }
		   }
		  
		   window.reloaddata(drevs,usertext,summa_from,summa_to);
	       window.mobilereloaddata(drevs,usertext,summa_from,summa_to);  
		   	 window.setTimeout(function(){
			 window.reloaddata(drevs,usertext,summa_from,summa_to);
			 window.mobilereloaddata(drevs,usertext,summa_from,summa_to); 
		}, 2000);
  }
OnKeyInputSearch= function(event)
{
event = event || window.event;
if((event.keyCode == 13)) 
 {
 postReload(KK);
 return false;
 }  
};
   $("#myModalCategory").on("hidden.bs.modal", function() {
    postReload(KK)
   });
 </script>	
 @endpush