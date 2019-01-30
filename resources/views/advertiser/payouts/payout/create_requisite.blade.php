                    <div class="row">
						<div class="col-xs-6 col-xs-offset-0 personal_form" style="margin-left: 15px;">
							<form class="form-horizontal" role="form" method="post" action="{{route('advertiser.save.requisites')}}">
							{{ csrf_field() }}
		            <input type="text" name="user_id" value="{{$userProf->user_id}}" hidden style="display: none">
					<div class="form-group">
						<label for="summa" class="col-xs-5 control-label">Форма организации</label>
						<div class="col-xs-7">
							<div class="radio_buttons">
								<div>
									<input type="radio" name="form" id="radio1" @if(!old('form') || old('form')==1) checked  @endif value="1">
									<label for="radio1">ИП</label>
								</div>
								<div>
									<input type="radio" name="form" id="radio2" @if(old('form')==2) checked @endif  value="2">
									<label for="radio2">ООО</label>
								</div>
							</div>
						</div>
						@if ($errors->has('form'))
							<span class="help-block">
								<strong>{{ $errors->first('form') }}</strong>
							</span>
                        @endif
					</div>	
					<div  class="form-group"  id="indebeznds" style="dispaly:none !important;color:red">
					<label class="col-xs-5 control-label"></label>
					<div class="col-xs-7">
					ИП только без ндс</div></div>
					<div class="form-group" data-set="nds">
					    
						
						
						<label for="bik" class="col-xs-5 control-label">Способ оплаты</label>
						<div class="col-xs-7">
							<div class="radio_buttons1">
								<div>
									<input type="radio" name="nds" value="1" @if(!old('nds') || old('nds')==1) checked @endif id="pay_rad1">
									<label for="pay_rad1">без НДС</label>
								</div>
								<div>
									<input type="radio" name="nds" value="2" @if(old('nds')==2) checked @endif id="pay_rad2">
									<label for="pay_rad2">с НДС</label>
								</div>
							</div>
						</div>
						
					</div>			 		
					<div class="form-group" data-set="position">
						<label for="position" class="col-xs-5 control-label">Должность лица заключающего договор</label>
						<div class="col-xs-7">
							<select class="form-control" name="position" style="margin-top: 12px;">
                                @if(!old('form') || old('form')==1)
								<option value="Руководитель">Руководитель</option>
                                @else
                                <option value="Руководитель" @if(old('position')=="Руководитель")selected @endif>Руководитель</option>
                                <option value="Директор" @if(old('position')=="Директор")selected @endif>Директор</option>
                                <option value="Генеральный директор" @if(old('position')=="Генеральный директор")selected @endif>Генеральный директор</option> 
                                @endif 
							</select>
						</div>
					</div>
					<div class="form-group @if ($errors->has('name')) has-error  @endif" data-set="name">
						<label for="name" class="col-xs-5 control-label">ФИО</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" name="name" value="{{old('name')}}">
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
					<div class="form-group @if ($errors->has('firm_name')) has-error  @endif" data-set="firm_name">
						<label for="firm_name" class="col-xs-5 control-label"> @if(!old('form') || old('form')==1)ИП@elseПолное наименование организации@endif</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('firm_name')}}" name="firm_name" @if(!old('form') || old('form')==1) readonly @endif>
							@if ($errors->has('firm_name'))
								<span class="help-block">
									<strong>{{ $errors->first('firm_name') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group @if ($errors->has('legale_male')) has-error  @endif" data-set="legale_male">
						<label for="legale_male" class="col-xs-5 control-label">Юридический адрес</label>
						<div class="col-xs-7">
							<textarea class="form-control" name="legale_male" style="height: 80px; resize: none">{{old('legale_male')}}</textarea>
							@if ($errors->has('legale_male'))
								<span class="help-block">
									<strong>{{ $errors->first('legale_male') }}</strong>
								</span>
							@endif
						</div>
					</div>

                    <div class="form-group @if ($errors->has('fact_male')) has-error  @endif" data-set="fact_male">
						<label for="fact_male" class="col-xs-5 control-label">Почтовый адрес</label>
						<div class="col-xs-7">
							<textarea class="form-control" name="fact_male" style="height: 80px; resize: none">{{old('fact_male')}}</textarea>
							@if ($errors->has('fact_male'))
								<span class="help-block">
									<strong>{{ $errors->first('fact_male') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group @if ($errors->has('series_certificate')) has-error  @endif" data-set="series_certificate">
						<label for="series_certificate" class="col-xs-5 control-label">Серия свидетельства о постановке на учет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('series_certificate')}}" name="series_certificate" style="margin-top: 12px;">
							@if ($errors->has('series_certificate'))
								<span class="help-block">
									<strong>{{ $errors->first('series_certificate') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group @if ($errors->has('number_certificate')) has-error  @endif" data-set="number_certificate">
						<label for="number_certificate" class="col-xs-5 control-label">Номер свидетельства о постановке на учет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('number_certificate')}}" name="number_certificate" style="margin-top: 12px;">
							@if ($errors->has('number_certificate'))
								<span class="help-block">
									<strong>{{ $errors->first('number_certificate') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group @if ($errors->has('date_certificate')) has-error  @endif" data-set="date_certificate">
						<label for="date_certificate" class="col-xs-5 control-label">Дата свидетельства о постановке на учет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('date_certificate')}}" name="date_certificate" style="margin-top: 20px;">
							@if ($errors->has('date_certificate'))
								<span class="help-block">
									<strong>{{ $errors->first('date_certificate') }}</strong>
								</span>
							@endif
						</div>
					</div>		

                    <div class="form-group  @if ($errors->has('inn')) has-error  @endif" data-set="inn">
						<label for="inn" class="col-xs-5 control-label">ИНН</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('inn')}}" name="inn">
							@if ($errors->has('inn'))
								<span class="help-block">
									<strong>{{ $errors->first('inn') }}</strong>
								</span>
							@endif
						</div>
					</div>
                    <div class="form-group  @if ($errors->has('kpp')) has-error  @endif" data-set="kpp">
						 <label for="kpp" class="col-xs-5 control-label">КПП</label>
						 <div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('kpp')}}" name="kpp">
							@if ($errors->has('kpp'))
								<span class="help-block">
									<strong>{{ $errors->first('kpp') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group  @if ($errors->has('ogrn')) has-error  @endif" data-set="ogrn">
						<label for="ogrn" class="col-xs-5 control-label">ОГРНИП</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('ogrn')}}" name="ogrn">
							@if ($errors->has('ogrn'))
								<span class="help-block">
									<strong>{{ $errors->first('ogrn') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group @if ($errors->has('okved')) has-error  @endif" data-set="okved">
						<label for="okved" class="col-xs-5 control-label">ОКВЭД</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('okved')}}" name="okved">
							@if ($errors->has('okved'))
								<span class="help-block">
									<strong>{{ $errors->first('okved') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group @if ($errors->has('name_bank')) has-error  @endif" data-set="name_bank">
						<label for="name_bank" class="col-xs-5 control-label">Наименование банка</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('name_bank')}}" name="name_bank">
							@if ($errors->has('name_bank'))
								<span class="help-block">
									<strong>{{ $errors->first('name_bank') }}</strong>
								</span>
							@endif
						</div>
					</div>						
                    <div class="form-group @if ($errors->has('account')) has-error  @endif" data-set="account">
						<label for="account" class="col-xs-5 control-label">Расчетный счет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('account')}}" name="account">
							@if ($errors->has('account'))
								<span class="help-block">
									<strong>{{ $errors->first('account') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group @if ($errors->has('kor_account')) has-error  @endif" data-set="kor_account">
						<label for="kor_account" class="col-xs-5 control-label">Кор. счет</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('kor_account')}}" name="kor_account">
							@if ($errors->has('kor_account'))
								<span class="help-block">
									<strong>{{ $errors->first('kor_account') }}</strong>
								</span>
							@endif
						</div>
					</div>	
                    <div class="form-group @if ($errors->has('kor_account')) has-error  @endif" data-set="bik">
						<label for="bik" class="col-xs-5 control-label">БИК</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" value="{{old('bik')}}" name="bik">
							@if ($errors->has('bik'))
								<span class="help-block">
									<strong>{{ $errors->first('bik') }}</strong>
								</span>
							@endif
						</div>
					</div>					
					@if(isset($config['pref']) && $config['pref']=='admin.')
											<div class="form-group" data-set="chstatus">
						<label for="chstatus" class="col-xs-5 control-label">Договор №</label>
						<div class="col-xs-7">
							<input type="text" class="form-control" name="dogovor" value = "{{old('chstatus')}}">

						</div>
					</div>	
					<div class="form-group" data-set="chstatus">
						<label for="chstatus" class="col-xs-5 control-label">Подтверждение</label>
						<div class="col-xs-1">
							<input type="checkbox" class="form-control" name="chstatus" @if(old('chstatus')) checked @endif>

						</div>
					</div>
					@endif					
				
								<div class="form-group">
									<label for="submit" class="col-xs-6 control-label person-form"></label>
									<div class="col-xs-6">
										<button type="submit" class="btn btn-success">
										Сохранить
										</button>
									</div>
								</div>
							</form>
						</div>

					</div>
@include('advertiser.payouts.payout.jsform')