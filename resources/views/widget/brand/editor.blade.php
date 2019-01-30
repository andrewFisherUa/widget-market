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
		<h4 class="text-center">Редактирование брендирования № {{$widget->id}} {{$widget->partnerPad->domain}}</h4>
			<form id="form-custom-widget" class="form-horizontal" method="post" novalidate action="{{route('widget.brand.save', ['id'=>$widget->id])}}">
				{{ csrf_field() }}
				<div class="col-xs-4">
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
						<h5 class="text-center" style="font-weight: bold">Настройки виджета</h5>
						<div class="form-group">
							<label for="on_rus" class="col-xs-5 control-label">Показывать по России</label>
							<div class="col-xs-6">
								<input name="on_rus" style="margin-top: 12px;" type="checkbox" @if ($widgetCustom->on_rus!='0') checked  @endif value="1">
							</div>
						</div>
						
						<div class="form-group">
							<label for="on_cis" class="col-xs-5 control-label">Показывать по СНГ</label>
							<div class="col-xs-6">
								<input name="on_cis" style="margin-top: 12px;" type="checkbox" @if ($widgetCustom->on_cis!='0') checked  @endif value="1">
							</div>
						</div>
						
						<div class="form-group">
							<label for="on_mobil" class="col-xs-5 control-label">Показывать на мобильных устройствах</label>
							<div class="col-xs-6">
								<input name="on_mobil" style="margin-top: 12px;" type="checkbox" @if ($widgetCustom->on_mobil!='0') checked  @endif value="1">
							</div>
						</div>
						<div class="form-group">
							<label for="width" class="col-xs-5 control-label">Блок Россия</label>
							<div class="col-xs-6">
								<select name="block_rus" class="form-control">
									@foreach ($blocks as $block)
										<option value="{{$block->id}}"  @if ($widgetCustom->block_rus==$block->id) selected @endif >{{$block->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="width" class="col-xs-5 control-label">Блок СНГ</label>
							<div class="col-xs-6">
								<select name="block_cis" class="form-control">
									@foreach ($blocks as $block)
										<option value="{{$block->id}}"  @if ($widgetCustom->block_cis==$block->id) selected @endif >{{$block->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="width" class="col-xs-5 control-label">Блок Мобильный</label>
							<div class="col-xs-6">
								<select name="block_mobil" class="form-control">
									@foreach ($blocks as $block)
										<option value="{{$block->id}}"  @if ($widgetCustom->block_mobil==$block->id) selected @endif >{{$block->title}}</option>
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
				</div>
				<div class="col-xs-4">
					@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
						<h5 class="text-center" style="font-weight: bold">Исключения</h5>
						@foreach ($sources as $link)
							<div class="from-group">
							<input name="exception[]" style="padding: 6px 0; height: auto; width: auto; display: inline-block" type="checkbox" value="{{$link->id}}"
								@if ($link->forbidden)
									checked
								@endif
							>
							{{$link->title}}
							</div>
						@endforeach
					@endif
				</div>
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
				$('#width-template').val('550');
			}
		});
		$('#height-template-full').change(function(){
			if ($('#height-template-full').prop('checked')==true){
				$('#height-template').val('0');
				$('#height-template').prop('readonly', true);
			}
			else{
				$('#height-template').prop('readonly', false);
				$('#height-template').val('340');
			}
		});
		$('#width-template').change(function(){
			if ($('#width-template').val()<550){
				$('#width-template').val('550');
			}
		});
		$('#height-template').change(function(){
			if ($('#height-template').val()<340){
				$('#height-template').val('550');
			}
		});
	});
</script>
@endpush