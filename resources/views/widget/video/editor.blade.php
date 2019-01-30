@extends('layouts.app')

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
	</div>
	<div class="row">
		@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
			<a href="{{route('admin.home', ['user_id'=>$user->id])}}" style="font-weight: bold">{{$user->name}}</a>
		@endif
		<h4 class="text-center">Редактирование виджета №{{$wid->id}} @if($widget->type==1) автоплей @elseif ($widget->type==2) оверлей @endif {{$wid->partnerPad->domain}}</h4>
		<form id="form-custom-widget" class="form-horizontal" method="post" novalidate action="{!! route('widget.video.save', ['id'=>$id_widget]) !!}">
		{{ csrf_field() }}
			<div class="col-xs-4">
				<h5 class="text-center" style="font-weight: bold">Настройки виджета</h5>
				<div class="form-group">
					<label for="width" class="col-xs-5 control-label">Ширина <span data-toggle="tooltip" data-placement="bottom" title="<b>Внимание!</b> Минимальная ширина 400px, это правило действует и при растягивании." class="glyphicon glyphicon-exclamation-sign" style="color: #ce0000; font-size: 16px; top: 3px; cursor: pointer"></span></label>
					<div class="col-xs-6">
						<input type="number" id="width-template" class="form-control" @if ($widget->width==0) readonly @endif name="width" min="400" value="{{$widget->width}}">
					</div>
				</div>
				
				<div id="group-width-full" class="form-group">
					<label for="width-template-full" class="col-xs-5 control-label">Растянуть по ширине</label>
					<div class="col-xs-6">
						<input name="width_full" id="width-template-full" style="margin-top: 12px;" @if ($widget->width==0) checked @endif type="checkbox" value="1">
					</div>
				</div>
				
				<div class="form-group">
					<label for="width" class="col-xs-5 control-label">Высота <span data-toggle="tooltip" data-placement="bottom" title="<b>Внимание!</b> Минимальная высота 300px, это правило действует и при растягивании." class="glyphicon glyphicon-exclamation-sign" style="color: #ce0000; font-size: 16px; top: 3px; cursor: pointer"></span></label>
					<div class="col-xs-6">
						<input type="number" id="height-template" class="form-control"  @if ($widget->height==0) readonly @endif name="height" min="300" value="{{$widget->height}}">
					</div>
				</div>
				
				<div id="group-height-full" class="form-group">
					<label for="height-template-full" class="col-xs-5 control-label">Растянуть по высоте</label>
					<div class="col-xs-6">
						<input name="height_full" id="height-template-full" style="margin-top: 12px;" @if ($widget->height==0) checked @endif type="checkbox" value="1">
					</div>
				</div>
				@if (\Auth::user()->hasRole(['admin','super_manager']))
				@php 
			#var_dump($widget);
			    @endphp
				<div class="form-group">
					<label for="muting" class="col-xs-5 control-label">Отключение звука</label>
					<div class="col-xs-6">
						<input name="muting" style="margin-top: 12px;" type="checkbox" @if ($widget->muting==1) checked  @endif value="1">
					</div>
				</div>
				<div class="form-group">
					<label for="autosort" class="col-xs-5 control-label">Индивидуальная сортировка</label>
					<div class="col-xs-6">
						<input name="autosort" style="margin-top: 12px;" type="checkbox" @if ($widget->autosort==1) checked  @endif value="1">
					</div>
				</div>
				<div class="form-group">
					<label for="validations" class="col-xs-5 control-label">Валидация</label>
					<div class="col-xs-6">
						<input name="validations" style="margin-top: 12px;" type="checkbox" @if ($widget->validation==1) checked  @endif value="1">
					</div>
				</div>
				@else
                                    @if ($widget->validation==1)
    		                        <input name="validations" type="hidden"  value="1">
    		                        @endif
									@if ($widget->autosort==1)
    		                        <input name="autosort" type="hidden"  value="1">
    		                        @endif
									@if ($widget->muting==1)
    		                        <input name="muting" type="hidden"  value="1">
    		                        @endif
				@endif
				<div class="form-group">
					<label for="on_rus" class="col-xs-5 control-label">Показывать по России</label>
					<div class="col-xs-6">
						<input name="on_rus" style="margin-top: 12px;" type="checkbox" @if ($widget->on_rus!='0') checked  @endif value="1">
					</div>
				</div>
				
				<div class="form-group">
					<label for="on_cis" class="col-xs-5 control-label">Показывать по СНГ</label>
					<div class="col-xs-6">
						<input name="on_cis" style="margin-top: 12px;" type="checkbox" @if ($widget->on_cis!='0') checked  @endif value="1">
					</div>
				</div>
				
				<div class="form-group">
					<label for="on_mobil" class="col-xs-5 control-label">Показывать на мобильных устройствах</label>
					<div class="col-xs-6">
						<input name="on_mobil" style="margin-top: 12px;" type="checkbox" @if ($widget->on_mobil!='0') checked  @endif value="1">
					</div>
				</div>

				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					@if (1==1 || $widget->type==2)

						<div class="form-group">
							<label for="adslimit" class="col-xs-5 control-label">Лимит роликов</label>
							<div class="col-xs-6">
								<input name="adslimit" class="form-control"  type="text" value="{{$widget->adslimit}}">
							</div>
						</div>
					@endif
				@endif
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<div class="form-group">
							 <label for="control_rus" class="col-xs-5 control-label">Контроль RU?</label>
					    <div class="col-xs-6">
						     <input name="control_rus" style="margin-top: 12px;" type="checkbox" @if ($widget->control_rus!='0') checked  @endif value="1">
					    </div>
					</div>
			        <div class="form-group">
							 <label for="control_cis" class="col-xs-5 control-label">Контроль СНГ?</label>
					    <div class="col-xs-6">
						     <input name="control_cis" style="margin-top: 12px;" type="checkbox" @if ($widget->control_cis!='0') checked  @endif value="1">
					    </div>
					</div>
				@endif
				@if (\Auth::user()->hasRole('admin'))
						<div class="form-group">
						<label for="deep_bonus_ru" class="col-xs-5 control-label">За глубину Россия</label>
						<div class="col-xs-6">
						
                         <input name="deep_bonus_ru" class="form-control"  type="text" value="{{$widget->deep_bonus_ru}}">						
						</div>
					</div>	
					<div class="form-group">
						<label for="deep_bonus_cis" class="col-xs-5 control-label">За глубину СНГ</label>
						<div class="col-xs-6">
						
                         <input name="deep_bonus_cis" class="form-control"  type="text" value="{{$widget->deep_bonus_cis}}">						
						</div>
					</div>	
				
					<div class="form-group">
						<label for="commission_rus" class="col-xs-5 control-label">Комиссия Россия</label>
						<div class="col-xs-6">
							<select name="commission_rus" class="form-control">
								@foreach ($commissions as $commission)
									<option @if ($widget->commission_rus == $commission->commissiongroupid) selected @endif value="{{$commission->commissiongroupid}}">{{$commission->label}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="commission_cis" class="col-xs-5 control-label">Комиссия СНГ</label>
						<div class="col-xs-6">
							<select name="commission_cis" class="form-control">
								@foreach ($commissions as $commission)
									<option @if ($widget->commission_cis == $commission->commissiongroupid) selected @endif value="{{$commission->commissiongroupid}}">{{$commission->label}}</option>
								@endforeach
							</select>
						</div>
					</div>
				@endif
				@if (\Auth::user()->hasRole(['admin','super_manager']))
					<div class="form-group">
						<label for="block_rus" class="col-xs-5 control-label">Блок для России</label>
						<div class="col-xs-6">
							<select name="block_rus" class="form-control">
								@foreach ($blocks as $block)
									<option @if ($widget->block_rus == $block->id) selected @endif value="{{$block->id}}">{{$block->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
				
					<div class="form-group">
						<label for="block_cis" class="col-xs-5 control-label">Блок для СНГ</label>
						<div class="col-xs-6">
							<select name="block_cis" class="form-control">
								@foreach ($blocks as $block)
									<option @if ($widget->block_cis == $block->id) selected @endif value="{{$block->id}}">{{$block->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
				
					<div class="form-group">
						<label for="block_mobil" class="col-xs-5 control-label">Блок для мобильных</label>
						<div class="col-xs-6">
							<select name="block_mobil" class="form-control">
								@foreach ($blocks as $block)
									<option @if ($widget->block_mobil == $block->id) selected @endif value="{{$block->id}}">{{$block->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
				@endif
				
				
				<div class="form-group text-center">
					<button type="submit" class="btn btn-primary">
						Сохранить
					</button>
				</div>
			@role(['admin','super_manager'])	
				<div class="row">
				@foreach($linkForPid as $lp)
                      	<div class="row">
                           <div class="col-xs-3">{{$lp->util}}
						   </div>					
						   <div class="col-xs-9">{{$lp->title}}
						   </div>					
                        </div>		
                                      @endforeach						
				</div>
		       @endrole
			</div>
			
			<div class="col-xs-4">
				<h5 class="text-center" style="font-weight: bold">Код для Вашего сайта</h5>
				<div class="form-group">
					<div class="col-xs-12">
						<textarea id="maincode" readonly="readonly" class="form-control" style="height: 250px; resize: none" class="give-code">{!! $kod !!}</textarea>
					</div>
				</div>
				<div class="text-center">
					<a class="btn btn-success copy-all" data-clipboard-target="#maincode" style="cursor:pointer; top: 3px; font-size: 18px;">
						Скопировать код						
					</a>
				</div>
				
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<h5 class="text-center" style="font-weight: bold">Комментарий</h5>
					<div class="form-group">
						<div class="col-xs-12">
							<textarea id="" name="coment" class="form-control" style="height: 150px; resize: none" >{{$wid->coment}}</textarea>
						</div>
					</div>
					
					<h5 class="text-center" style="font-weight: bold">Название</h5>
					<input name="WiName" class="form-control"  type="text" value="{{$wid->name}}">	
				@endif
			</div>
			@if (\Auth::user()->hasRole('admin') or \Auth::user()->id == 39)
			<div class="col-xs-4">
				<h5 class="text-center" style="font-weight: bold">Исключения</h5>
					@foreach ($exceptions as $link)
						<div class="from-group">
							<input name="exception[]" style="padding: 6px 0; height: auto; width: auto; display: inline-block" type="checkbox" value="{{$link->id}}"
								@if ($link->forbidden)
									checked
								@endif
							>
							{{$link->title}}
					
						</div>
					@endforeach
				<h5 class="text-center" style="font-weight: bold">Добавить ссылки</h5>
					@foreach ($links as $l)
						<div class="from-group">
							<input name="active[]" style="padding: 6px 0; height: auto; width: auto; display: inline-block" type="checkbox" value="{{$l->id}}"
								@if ($l->active)
									checked
								@endif
							>
							{{$l->title}}
						</div>
					@endforeach
			</div>
			@endif
		</form>
    </div>
</div>
@endsection
@push('cabinet_home')

@endpush
@push('cabinet_home_js')
<script src="https://cdn.rawgit.com/zenorocha/clipboard.js/master/dist/clipboard.min.js"></script>
	<script>
		new Clipboard('.copy-all');
	</script>
<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip({html: true});
		});
	</script>
<script>
	$(document).ready(function() {
		$('#width-template-full').change(function(){
			if ($('#width-template-full').prop('checked')==true){
				$('#width-template').val('0');
				$('#width-template').prop('readonly', true);
			}
			else{
				$('#width-template').prop('readonly', false);
				$('#width-template').val('400');
			}
		});
		$('#height-template-full').change(function(){
			if ($('#height-template-full').prop('checked')==true){
				$('#height-template').val('0');
				$('#height-template').prop('readonly', true);
			}
			else{
				$('#height-template').prop('readonly', false);
				$('#height-template').val('300');
			}
		});
		$('#width-template').change(function(){
			if ($('#width-template').val()<400){
				$('#width-template').val('400');
			}
		});
		$('#height-template').change(function(){
			if ($('#height-template').val()<300){
				$('#height-template').val('300');
			}
		});
	});
</script>
@endpush