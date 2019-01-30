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
				<form class="form-horizontal" role="form" method="post" action="{{route('advertiser.save.requisites')}}">
				{{ csrf_field() }}
					@if ($requisite)
					<input type="text" name="id" value="{{$requisite->id}}" hidden style="display: none">
					@endif
					<input type="text" name="user_id" value="{{$user->id}}" hidden style="display: none">
					<div class="form-group">
						<label for="summa" class="col-xs-5 control-label">Форма организации</label>
						<div class="col-xs-7">
							<div class="radio_buttons">
								<div>
									<input type="radio" name="form" id="radio1" checked value="1">
									<label for="radio1">ИП</label>
								</div>
								<div>
									<input type="radio" name="form" id="radio2" value="2">
									<label for="radio2">ООО</label>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group" data-set="nds">
						<label for="bik" class="col-xs-5 control-label">Способ оплаты</label>
						<div class="col-xs-7">
							<div class="radio_buttons1">
								<div>
									<input type="radio" name="nds" value="1" checked id="pay_rad1">
									<label for="pay_rad1">без НДС</label>
								</div>
								<div>
									<input type="radio" name="nds" value="2" id="pay_rad2">
									<label for="pay_rad2">с НДС</label>
								</div>
							</div>
						</div>
						@if ($errors->has('form'))
							<span class="help-block">
								<strong>{{ $errors->first('form') }}</strong>
							</span>
                        @endif
					</div>
					<div class="form-group" data-set="position">
						<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>
						<div class="col-xs-7">
							<select class="form-control" name="position" style="margin-top: 12px;">
								<option value="Руководитель">Руководитель</option>
							</select>
						</div>
					</div>
					<div class="form-group" data-set="name">
						<label for="name" class="col-xs-5 control-label">ФИО</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" name="name" required>
							<span class="help-block help-name"style="margin: 0; color: rgb(181, 0, 0); display: none;">
								<strong>Не верно задано ФИО</strong>
							</span>
							@if ($errors->has('name'))
								<span class="help-block">
									<strong>{{ $errors->first('name') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="firm_name">
						<label for="firm_name" class="col-xs-5 control-label">ИП</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="firm_name" readonly required>
							@if ($errors->has('firm_name'))
								<span class="help-block">
									<strong>{{ $errors->first('firm_name') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="legale_male">
						<label for="legale_male" class="col-xs-5 control-label">Юридический адрес</label>
						<div class="col-xs-7">
							<textarea class="form-control" name="legale_male" style="height: 80px; resize: none"></textarea>
							@if ($errors->has('legale_male'))
								<span class="help-block">
									<strong>{{ $errors->first('legale_male') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="fact_male">
						<label for="fact_male" class="col-xs-5 control-label">Почтовый адрес</label>
						<div class="col-xs-7">
							<textarea class="form-control" name="fact_male" style="height: 80px; resize: none"></textarea>
							@if ($errors->has('fact_male'))
								<span class="help-block">
									<strong>{{ $errors->first('fact_male') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="series_certificate">
						<label for="fact_male" class="col-xs-5 control-label">Серия свидетельства о постановке на учет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="series_certificate" style="margin-top: 12px;" required>
							@if ($errors->has('series_certificate'))
								<span class="help-block">
									<strong>{{ $errors->first('series_certificate') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="number_certificate">
						<label for="fact_male" class="col-xs-5 control-label">Номер свидетельства о постановке на учет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="number_certificate" style="margin-top: 12px;" required>
							@if ($errors->has('number_certificate'))
								<span class="help-block">
									<strong>{{ $errors->first('number_certificate') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="date_certificate">
						<label for="fact_male" class="col-xs-5 control-label">Дата свидетельства о постановке на учет (в формате день.месяц.год)</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="date_certificate" style="margin-top: 20px;" required>
							@if ($errors->has('date_certificate'))
								<span class="help-block">
									<strong>{{ $errors->first('date_certificate') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="inn">
						<label for="inn" class="col-xs-5 control-label">ИНН</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="inn" required>
							@if ($errors->has('inn'))
								<span class="help-block">
									<strong>{{ $errors->first('inn') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="kpp" style="margin-bottom: 0px;">
						
					</div>
					<div class="form-group" data-set="ogrn">
						<label for="ogrn" class="col-xs-5 control-label">ОГРНИП</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="ogrn" required>
							@if ($errors->has('ogrn'))
								<span class="help-block">
									<strong>{{ $errors->first('ogrn') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="okved">
						<label for="okved" class="col-xs-5 control-label">ОКВЭД</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="okved" required>
							@if ($errors->has('okved'))
								<span class="help-block">
									<strong>{{ $errors->first('okved') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="name_bank">
						<label for="name_bank" class="col-xs-5 control-label">Наименование банка</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="name_bank" required>
							@if ($errors->has('name_bank'))
								<span class="help-block">
									<strong>{{ $errors->first('name_bank') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="account">
						<label for="account" class="col-xs-5 control-label">Расчетный счет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="account" required>
							@if ($errors->has('account'))
								<span class="help-block">
									<strong>{{ $errors->first('account') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="kor_account">
						<label for="kor_account" class="col-xs-5 control-label">Кор. счет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="kor_account" required>
							@if ($errors->has('kor_account'))
								<span class="help-block">
									<strong>{{ $errors->first('kor_account') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="bik">
						<label for="bik" class="col-xs-5 control-label">БИК</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="" name="bik" required>
							@if ($errors->has('bik'))
								<span class="help-block">
									<strong>{{ $errors->first('bik') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group" data-set="submit">
						<div class="col-xs-7 col-xs-offset-5 text-center">
							<button type="submit" class="btn btn-primary">Сохранить</button>
						</div>
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
					var ip=$('input[name="name"]').val().split(' ');
					if (ip.length==3){
					var name=$('input[name="name"]').val();
					}
					else{
					var name="";
					}
					$('[data-set="position"]').html('<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>'+
						'<div class="col-xs-7">'+
							'<select class="form-control" name="position" style="margin-top: 12px;" >'+
								'<option value="Руководитель">Руководитель</option>'+
							'</select>'+
						'</div>'
					);
					$('[data-set="firm_name"]').html('<label for="firm_name" class="col-xs-5 control-label">ИП</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" value="'+name+'" name="firm_name" readonly required>'+
						'</div>'
					);
					$('[data-set="series_certificate"]').html('<label for="fact_male" class="col-xs-5 control-label">Серия свидетельства о постановке на учет</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" value="" name="series_certificate" style="margin-top: 12px;" required>'+
						'</div>'
					);
					$('[data-set="number_certificate"]').html('<label for="fact_male" class="col-xs-5 control-label">Номер свидетельства о постановке на учет</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" value="" name="number_certificate" style="margin-top: 12px;" required>'+
						'</div>'
					);
					$('[data-set="date_certificate"]').html('<label for="fact_male" class="col-xs-5 control-label">Дата свидетельства о постановке на учет (в формате день.месяц.год)</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" value="" name="date_certificate" style="margin-top: 20px;" required>'+
						'</div>'
					);
					$('[data-set="kpp"]').html('');
					$('[data-set="kpp"]').css('margin-bottom', '0');
					$('[data-set="series_certificate"]').css('margin-bttom', '15px');
					$('[data-set="number_certificate"]').css('margin-bttom', '15px');
					$('[data-set="date_certificate"]').css('margin-bttom', '15px');
					$('[data-set="ogrn"]').html('<label for="ogrn" class="col-xs-5 control-label">ОГРНИП</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="ogrn" required>'+
						'</div>'
					);
				}
				else{
					$('[data-set="position"]').html('<label for="bik" class="col-xs-5 control-label">Должность лица заключающего договор</label>'+
						'<div class="col-xs-7">'+
							'<select class="form-control" name="position" style="margin-top: 12px;" >'+
								'<option value="Руководитель">Руководитель</option>'+
								'<option value="Директор">Директор</option>'+
								'<option value="Генеральный директор">Генеральный директор</option>'+
							'</select>'+
						'</div>'
					);
					$('[data-set="firm_name"]').html('<label for="firm_name" class="col-xs-5 control-label">Полное наименование организации (без ООО)</label>'+
						'<div class="col-xs-7">'+
							'<input type="text" class="form-control" name="firm_name" style="margin-top: 12px;" required>'+
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
					$('[data-set="kpp"]').css('margin-bottom', '15px');
					$('[data-set="series_certificate"]').html('');
					$('[data-set="series_certificate"]').css('margin-bttom', 0);
					$('[data-set="number_certificate"]').html('');
					$('[data-set="number_certificate"]').css('margin-bttom', 0);
					$('[data-set="date_certificate"]').html('');
					$('[data-set="date_certificate"]').css('margin-bttom', 0);
				}
			});
			$('input[name="name"]').change(function(){
				if ($(this).val()!=""){
					var ip=$(this).val().split(' ');
					if (ip.length==3){
						$('.help-name').css('display', 'none');
						if ($('input[name="form"]:checked').val()==1){
							$('input[name="firm_name"]').val($(this).val());
						}
					}
					else{
						$('.help-name').css('display', 'block');
					}
				}
				else{
					$('.help-name').css('display', 'none');
				}
			});
		});
	</script>
@endpush