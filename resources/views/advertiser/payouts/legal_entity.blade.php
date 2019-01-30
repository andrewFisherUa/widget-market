@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{{ session('message_danger') }}
		</div>
	@endif
		<div class="row">
			<div class="col-xs-6 col-xs-offset-3">
				<h3 class="text-center">Для составления договора заполните реквизиты</h3>
				<form class="form-horizontal" role="form" method="post" action="{{route('advertiser.entity.payout')}}">
				{{ csrf_field() }}
					<input type="text" name="user_id" value="{{$user_id}}" hidden style="display: none">
					<div class="form-group">
						<label for="summa" class="col-xs-5 control-label">Форма организации</label>
						<div class="col-xs-7">
							<div class="radio_buttons">
								<div>
									<input type="radio" name="form" value="0" id="radio" checked>
									<label for="radio">Не выбрано</label>
								</div>
								<div>
									<input type="radio" name="form" id="radio1" @if (old('form')==1) checked @endif value="1">
									<label for="radio1">ИП</label>
								</div>
								<div>
									<input type="radio" name="form" id="radio2" @if (old('form')==2) checked @endif value="2">
									<label for="radio2">ООО</label>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group{{ $errors->has('nds') ? ' has-error' : '' }}" data-set="nds">
						@if (old('form')==1 or old('form')==2)
							<label for="bik" class="col-xs-5 control-label">Способ оплаты</label>
							<div class="col-xs-7">
								<div class="radio_buttons1">
									<div>
										<input type="radio" name="nds" value="0" id="pay_rad" checked>
										<label for="pay_rad">Не выбрано</label>
									</div>
									<div>
										<input type="radio" name="nds" value="1" @if (old('nds')==1) checked @endif id="pay_rad1">
										<label for="pay_rad1">без НДС</label>
									</div>
									<div>
										<input type="radio" name="nds" value="2" @if (old('nds')==2) checked @endif id="pay_rad2">
										<label for="pay_rad2">с НДС</label>
									</div>
								</div>
							</div>
							@if ($errors->has('form'))
								<span class="help-block">
									<strong>{{ $errors->first('form') }}</strong>
								</span>
                            @endif
						@endif
					</div>
					<div class="form-group{{ $errors->has('nds') ? ' has-error' : '' }}" data-set="position">
						@if (old('form')==1)
							<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>
							<div class="col-xs-7">
								<select class="form-control" name="position">
									<option value="Руководитель">Руководитель</option>
								</select>
							</div>
						@elseif (old('form')==2)
							<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>
							<div class="col-xs-7">
								<select class="form-control" name="position">
									<option @if (old('position')=='Руководитель') selected @endif value="Руководитель">Руководитель</option>
									<option @if (old('position')=='Директор') selected @endif value="Директор">Директор</option>
									<option @if (old('position')=='Генеральный директор') selected @endif value="Генеральный директор">Генеральный директор</option>
								</select>
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}" data-set="name">
						@if (old('form')==1 or old('form')==2)
							<label for="name" class="col-xs-5 control-label">ФИО</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" name="name" required>
								<span class="help-block help-name" value="{{old('name')}}" style="margin: 0; color: rgb(181, 0, 0); display: none;">
									<strong>Не верно задано ФИО</strong>
								</span>
								@if ($errors->has('name'))
									<span class="help-block">
										<strong>{{ $errors->first('name') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('firm_name') ? ' has-error' : '' }}" data-set="firm_name">
						@if (old('form')==1)
							<label for="firm_name" class="col-xs-5 control-label">Полное наименование</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('firm_name')}}" name="firm_name" readonly required>
								@if ($errors->has('firm_name'))
									<span class="help-block">
										<strong>{{ $errors->first('firm_name') }}</strong>
									</span>
								@endif
							</div>
						@elseif(old('form')==2)
							<label for="firm_name" class="col-xs-5 control-label">Полное наименование организации (без ООО)</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('firm_name')}}" name="firm_name" style="margin-top: 12px;" required>
								@if ($errors->has('firm_name'))
									<span class="help-block">
										<strong>{{ $errors->first('firm_name') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('legale_male') ? ' has-error' : '' }}" data-set="legale_male">
						@if (old('form')==1 or old('form')==2)
							<label for="legale_male" class="col-xs-5 control-label">Юридический адрес</label>
							<div class="col-xs-7">
								<textarea class="form-control" name="legale_male" style="height: 80px; resize: none">{{old('legale_male')}}</textarea>
								@if ($errors->has('legale_male'))
									<span class="help-block">
										<strong>{{ $errors->first('legale_male') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('fact_male') ? ' has-error' : '' }}" data-set="fact_male">
						@if (old('form')==1 or old('form')==2)
							<label for="fact_male" class="col-xs-5 control-label">Почтовый адрес</label>
							<div class="col-xs-7">
								<textarea class="form-control" name="fact_male" style="height: 80px; resize: none">{{old('fact_male')}}</textarea>
								@if ($errors->has('fact_male'))
									<span class="help-block">
										<strong>{{ $errors->first('fact_male') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('inn') ? ' has-error' : '' }}" data-set="inn">
						@if (old('form')==1 or old('form')==2)
						<label for="inn" class="col-xs-5 control-label">ИНН</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('inn')}}" name="inn" required>
							@if ($errors->has('inn'))
								<span class="help-block">
									<strong>{{ $errors->first('inn') }}</strong>
								</span>
							@endif
						</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('kpp') ? ' has-error' : '' }}" data-set="kpp">
						@if (old('form')==2)
							<label for="kpp" class="col-xs-5 control-label">КПП</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('kpp')}}" name="kpp" required>
								@if ($errors->has('inn'))
									<span class="help-block">
										<strong>{{ $errors->first('inn') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('ogrn') ? ' has-error' : '' }}" data-set="ogrn">
						@if (old('form')==1 or old('form')==2)
						<label for="ogrn" class="col-xs-5 control-label">@if (old('form')==1) ОГРНИП @else ОГРН @endif</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('ogrn')}}" name="ogrn" required>
							@if ($errors->has('ogrn'))
								<span class="help-block">
									<strong>{{ $errors->first('ogrn') }}</strong>
								</span>
							@endif
						</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('okved') ? ' has-error' : '' }}" data-set="okved">
						@if (old('form')==1 or old('form')==2)
							<label for="okved" class="col-xs-5 control-label">ОКВЭД</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('okved')}}" name="okved" required>
								@if ($errors->has('okved'))
									<span class="help-block">
										<strong>{{ $errors->first('okved') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('name_bank') ? ' has-error' : '' }}" data-set="name_bank">
						@if (old('form')==1 or old('form')==2)
						<label for="name_bank" class="col-xs-5 control-label">Наименование банка</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('name_bank')}}" name="name_bank" required>
							@if ($errors->has('name_bank'))
								<span class="help-block">
									<strong>{{ $errors->first('name_bank') }}</strong>
								</span>
							@endif
						</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('account') ? ' has-error' : '' }}" data-set="account">
						@if (old('form')==1 or old('form')==2)
							<label for="account" class="col-xs-5 control-label">Расчетный счет</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('account')}}" name="account" required>
								@if ($errors->has('account'))
									<span class="help-block">
										<strong>{{ $errors->first('account') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('kor_account') ? ' has-error' : '' }}" data-set="kor_account">
						@if (old('form')==1 or old('form')==2)
							<label for="kor_account" class="col-xs-5 control-label">Кор. счет</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('kor_account')}}" name="kor_account" required>
								@if ($errors->has('kor_account'))
									<span class="help-block">
										<strong>{{ $errors->first('kor_account') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group{{ $errors->has('bik') ? ' has-error' : '' }}" data-set="bik">
						@if (old('form')==1 or old('form')==2)
							<label for="bik" class="col-xs-5 control-label">БИК</label>
							<div class="col-xs-7">
								<input type="text" class="form-control" value="{{old('bik')}}" name="bik" required>
								@if ($errors->has('bik'))
									<span class="help-block">
										<strong>{{ $errors->first('bik') }}</strong>
									</span>
								@endif
							</div>
						@endif
					</div>
					<div class="form-group" data-set="submit">
						@if (old('form')==1 or old('form')==2)
							<div class="col-xs-7 col-xs-offset-5 text-center">
								<button type="submit" class="btn btn-primary">Продолжить</button>
							</div>
						@endif
					</div>
				</form>
			</div>
		</div>
    </div>
</div>

@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	<style>
	.radio_buttons {
    font-size: 14px
	}
	.radio_buttons div {
		float: left;
	}
	.radio_buttons input {
		position: absolute;
		left: -9999px;
	}
	.radio_buttons label {
		display: block;
		margin: 0px;
		padding: 8px 10px;
		border: 1px solid transparent;
		color: #333;
		background-color: #ebebeb;
		border-color: #1f648b;
		cursor: pointer;
	}
	.radio_buttons input:checked + label {
		color: #fff;
		background-color: #3097D1;
		border-color: #1f648b;
	}
	.radio_buttons div:first-child label {
		margin-left: 0;
		border-top-left-radius: 4px;
		border-bottom-left-radius: 4px;
	}
	.radio_buttons div:last-child label {
		border-top-right-radius: 4px;
		border-bottom-right-radius: 4px;
	}
	</style>
	<style>
	.radio_buttons1 {
    font-size: 14px
	}
	.radio_buttons1 div {
		float: left;
	}
	.radio_buttons1 input {
		position: absolute;
		left: -9999px;
	}
	.radio_buttons1 label {
		display: block;
		margin: 0px;
		padding: 8px 10px;
		border: 1px solid transparent;
		color: #333;
		background-color: #ebebeb;
		border-color: #1f648b;
		cursor: pointer;
	}
	.radio_buttons1 input:checked + label {
		color: #fff;
		background-color: #3097D1;
		border-color: #1f648b;
	}
	.radio_buttons1 div:first-child label {
		margin-left: 0;
		border-top-left-radius: 4px;
		border-bottom-left-radius: 4px;
	}
	.radio_buttons1 div:last-child label {
		border-top-right-radius: 4px;
		border-bottom-right-radius: 4px;
	}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		$(document).ready(function() {
			$('input[name="form"]').change(function(){
				if ($(this).val()==1){
					$('[data-set="name"]').html('<label for="name" class="col-xs-5 control-label">ФИО</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="name" required>'+
							'<span class="help-block help-name" style="margin: 0; color: rgb(181, 0, 0); display: none;">'+
								'<strong>Не верно задано ФИО</strong>'+
							'</span>'+
						'</div>'
					);
					$('[data-set="firm_name"]').html('<label for="firm_name" class="col-xs-5 control-label">Полное наименование</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="firm_name" readonly required>'+
						'</div>'
					);
					$('input[name="name"]').change(function(){
						var ip=$(this).val().split(' ');
						if (ip.length==3){
							$('input[name="firm_name"]').val($(this).val());
							$('.help-name').css('display', 'none');
						}
						else{
							$('input[name="firm_name"]').val('');
							$('.help-name').css('display', 'block');
						}
					});
					$('[data-set="legale_male"]').html('<label for="legale_male" class="col-xs-5 control-label">Юридический адрес</label>'+
						'<div class="col-xs-7">'+
							'<textarea class="form-control" name="legale_male" style="height: 80px; resize: none"></textarea>'+
						'</div>'
					);
					$('[data-set="fact_male"]').html('<label for="fact_male" class="col-xs-5 control-label">Почтовый адрес</label>'+
						'<div class="col-xs-7">'+
							'<textarea class="form-control" name="fact_male" style="height: 80px; resize: none"></textarea>'+
						'</div>'
					);
					$('[data-set="inn"]').html('<label for="inn" class="col-xs-5 control-label">ИНН</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="inn" required>'+
						'</div>'
					);
					$('[data-set="kpp"]').html('');
					$('[data-set="ogrn"]').html('<label for="ogrn" class="col-xs-5 control-label">ОГРНИП</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="ogrn" required>'+
						'</div>'
					);
					$('[data-set="okved"]').html('<label for="okved" class="col-xs-5 control-label">ОКВЭД</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="okved" required>'+
						'</div>'
					);
					$('[data-set="name_bank"]').html('<label for="name_bank" class="col-xs-5 control-label">Наименование банка</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="name_bank" required>'+
						'</div>'
					);
					$('[data-set="account"]').html('<label for="account" class="col-xs-5 control-label">Расчетный счет</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="account" required>'+
						'</div>'
					);
					$('[data-set="kor_account"]').html('<label for="kor_account" class="col-xs-5 control-label">Кор. счет</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="kor_account" required>'+
						'</div>'
					);
					$('[data-set="bik"]').html('<label for="bik" class="col-xs-5 control-label">БИК</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="bik" required>'+
						'</div>'
					);
					$('[data-set="position"]').html('<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>'+
						'<div class="col-xs-7">'+
							'<select class="form-control" name="position" style="margin-top: 12px;" >'+
								'<option value="Руководитель">Руководитель</option>'+
							'</select>'+
						'</div>'
					);
					$('[data-set="nds"]').html('<label for="bik" class="col-xs-5 control-label">Способ оплаты</label>'+
						'<div class="col-xs-7">'+
							'<div class="radio_buttons1">'+
								'<div>'+
									'<input type="radio" name="nds" value="0" id="pay_rad" checked>'+
									'<label for="pay_rad">Не выбрано</label>'+
								'</div>'+
								'<div>'+
									'<input type="radio" name="nds" value="1" id="pay_rad1">'+
									'<label for="pay_rad1">без НДС</label>'+
								'</div>'+
								'<div>'+
									'<input type="radio" name="nds" value="2" id="pay_rad2">'+
									'<label for="pay_rad2">с НДС</label>'+
								'</div>'+
							'</div>'+
						'</div>'
					);
					$('[data-set="submit"]').html('<div class="col-xs-7 col-xs-offset-5 text-center">'+
							'<button type="submit" class="btn btn-primary">Продолжить</button>'+
						'</div>'
					);
				}
				else if ($(this).val()==2){
					$('[data-set="name"]').html('<label for="name" class="col-xs-5 control-label">ФИО руководителя (либо директора, либо ген. директора)</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="name" style="margin-top: 12px;" required>'+
							'<span class="help-block help-name" style="margin: 0; color: rgb(181, 0, 0); display: none;">'+
								'<strong>Не верно задано ФИО</strong>'+
							'</span>'+
						'</div>');
					$('input[name="name"]').change(function(){
						var ip=$(this).val().split(' ');
						if (ip.length==3){
							$('.help-name').css('display', 'none');
						}
						else{
							$('.help-name').css('display', 'block');
						}
					});
					$('[data-set="firm_name"]').html('<label for="firm_name" class="col-xs-5 control-label">Полное наименование организации (без ООО)</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="firm_name" style="margin-top: 12px;" required>'+
						'</div>'
					);
					$('[data-set="legale_male"]').html('<label for="legale_male" class="col-xs-5 control-label">Юридический адрес</label>'+
						'<div class="col-xs-7">'+
							'<textarea class="form-control" name="legale_male" style="height: 80px; resize: none"></textarea>'+
						'</div>'
					);
					$('[data-set="fact_male"]').html('<label for="fact_male" class="col-xs-5 control-label">Почтовый адрес</label>'+
						'<div class="col-xs-7">'+
							'<textarea class="form-control" name="fact_male" style="height: 80px; resize: none"></textarea>'+
						'</div>'
					);
					$('[data-set="inn"]').html('<label for="inn" class="col-xs-5 control-label">ИНН</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="inn" required>'+
						'</div>'
					);
					$('[data-set="kpp"]').html('<label for="kpp" class="col-xs-5 control-label">КПП</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="kpp" required>'+
						'</div>'
					);
					$('[data-set="ogrn"]').html('<label for="ogrn" class="col-xs-5 control-label">ОГРН</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="ogrn" required>'+
						'</div>'
					);
					$('[data-set="okved"]').html('<label for="okved" class="col-xs-5 control-label">ОКВЭД</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="okved" required>'+
						'</div>'
					);
					$('[data-set="name_bank"]').html('<label for="name_bank" class="col-xs-5 control-label">Наименование банка</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="name_bank" required>'+
						'</div>'
					);
					$('[data-set="account"]').html('<label for="account" class="col-xs-5 control-label">Расчетный счет</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="account" required>'+
						'</div>'
					);
					$('[data-set="kor_account"]').html('<label for="kor_account" class="col-xs-5 control-label">Кор. счет</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="kor_account" required>'+
						'</div>'
					);
					$('[data-set="bik"]').html('<label for="bik" class="col-xs-5 control-label">БИК</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="bik" required>'+
						'</div>'
					);
					$('[data-set="position"]').html('<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>'+
						'<div class="col-xs-7">'+
							'<select class="form-control" name="position" style="margin-top: 12px;" >'+
								'<option value="Руководитель">Руководитель</option>'+
								'<option value="Директор">Директор</option>'+
								'<option value="Генеральный директор">Генеральный директор</option>'+
							'</select>'+
						'</div>'
					);
					$('[data-set="nds"]').html('<label for="bik" class="col-xs-5 control-label">Способ оплаты</label>'+
						'<div class="col-xs-7">'+
							'<div class="radio_buttons1">'+
								'<div>'+
									'<input type="radio" name="nds" value="0" id="pay_rad" checked>'+
									'<label for="pay_rad">Не выбрано</label>'+
								'</div>'+
								'<div>'+
									'<input type="radio" name="nds" value="1" id="pay_rad1">'+
									'<label for="pay_rad1">без НДС</label>'+
								'</div>'+
								'<div>'+
									'<input type="radio" name="nds" value="2" id="pay_rad2">'+
									'<label for="pay_rad2">с НДС</label>'+
								'</div>'+
							'</div>'+
						'</div>'
					);
					$('[data-set="submit"]').html('<div class="col-xs-7 col-xs-offset-5 text-center">'+
							'<button type="submit" class="btn btn-primary">Продолжить</button>'+
						'</div>'
					);
				}
				else{
					$('[data-set="name"]').html('');
					$('[data-set="firm_name"]').html('');
					$('[data-set="legale_male"]').html('');
					$('[data-set="fact_male"]').html('');
					$('[data-set="inn"]').html('');
					$('[data-set="kpp"]').html('');
					$('[data-set="ogrn"]').html('');
					$('[data-set="okved"]').html('');
					$('[data-set="name_bank"]').html('');
					$('[data-set="account"]').html('');
					$('[data-set="kor_account"]').html('');
					$('[data-set="bik"]').html('');
					$('[data-set="nds"]').html('');
					$('[data-set="submit"]').html('');
					$('[data-set="position"]').html('');
				}
			});
		});
	</script>
@endpush