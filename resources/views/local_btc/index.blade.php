@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
	<div class="row">
		<table class="table table-hover table-bordered" style="table-layout: fixed">
			<colgroup>
				<col span="1" style="width: 47px;">
				<col span="1" style="width: 67px;">
				<col span="1" style="width: 120px;">
				<col span="1" style="width: 180px;">
				<col span="1" style="width: 420px;">
			</colgroup>
			<thead>
				<tr>
					<td>ID</td>
					<td>Id lbtc</td>
					<td>Название</td>
					<td>Прайс</td>
					<td>Первые 5 позиций</td>
					<td>Статус парсера</td>
				</tr>
			</thead>
			<tbody>
				@foreach($ads as $ad)
					<tr @if ($ad->id_ad==705600 or $ad->id_ad==705602 or $ad->id_ad==712778 or $ad->id_ad==712783) style="background: rgba(249, 186, 186, 0.18)" @endif>
						<td style="text-align: center; vertical-align: middle; font-size: 26px; font-weight: bold;">{{$ad->id}}</td>
						<td style="@if($ad->visible==1) color: green; @else color: red; @endif font-weight: bold;">{{$ad->id_ad}}</td>
						<td><a style="color: blue; font-weight: bold;" href="https://localbitcoins.net/ads_edit/{{$ad->id_ad}}" target="_blank">{{$ad->name}}</a>
						@if ($ad->id_ad=='617372' or $ad->id_ad=='609305')
						<br><span style="font-weight: bold;">Актуальный курс продажи:</span><span  style="font-weight: bold; color: #02198e;"> {{round($ad->actual_price)}}({{round($ad->actual_price*(1+($ad->pr_actual_price/100)))}})</span>
						@endif
						@if ($ad->id_ad=='609928' or $ad->id_ad=='609849')
						<br><span style="font-weight: bold;">Актуальный курс покупки:</span><span  style="font-weight: bold; color: #02198e;"> {{round($ad->actual_price)}}({{round($ad->actual_price*(1+($ad->pr_actual_price/100)))}})</span>
						@endif
						@if ($ad->id_ad==617372)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 609849)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum1-$sum2)/$sum1)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==609849)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 617372)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum2-$sum1)/$sum2)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==609928)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 609305)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum2-$sum1)/$sum2)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==609305)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 609928)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum1-$sum2)/$sum1)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==705602)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 705600)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum2-$sum1)/$sum2)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==705600)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 705602)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum1-$sum2)/$sum1)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==712783)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 712778)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum2-$sum1)/$sum2)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==712778)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 712783)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum1-$sum2)/$sum1)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==632297)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 635074)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum2-$sum1)/$sum2)*100,4)}} %
							@endif
						@elseif ($ad->id_ad==635074)
							@if ($pr=\DB::connection('obmenneg')->table('local_ads')->where('id_ad', 632297)->first())
								<!--
								{{$sum1=1}}
								{{$sum2=1}}
								@if (\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $ad->id_ad)->first() 
								and 
								\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $pr->id_ad)->first())
								{{$sum1=\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $ad->id_ad)->first()->temp_price}}
								{{$sum2=\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', $pr->id_ad)->first()->temp_price}}
								@endif
								-->
								<br>{{round((($sum1-$sum2)/$sum1)*100,4)}} %
							@endif
						@endif
						</td>
						<td>
						{{$ad->temp_price}}
						<br><span style="font-weight: bold">{{round($ad->prosent,2)}} %</span>
						<br>Лимиты: {{$ad->min_amount}} - {{$ad->max_amount}}
						@if ($ad->balance)
						<br><span style="font-weight: bold;">Баланс:</span><span  style="font-weight: bold; color: #02198e;"> {{round($ad->balance)}}</span>
						@endif
						@if ($ad->id_ad==617372)
							<div class="col-xs-12" style="padding: 0">
								<form class="form-horizontal" method="post" action="{{route('lbtc.limite')}}">
									{{ csrf_field() }}
									<div class="col-xs-6 text-center" style="margin:2px 0; padding: 0">
										<input type="text" name="id" value="1" style="display: none" hidden>
										<input type="text" name="limite" class="form-control" value="{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->limite}}" style="margin-bottom: 2px;">
									</div>
									<div class="col-xs-6 text-center" style="margin:2px 0; padding: 0">
										<button type="submit" class="btn btn-primary">
											Ok
										</button>
									</div>
								</form>
							</div>
						@elseif ($ad->id_ad==609305)
							<div class="col-xs-12" style="padding: 0">
								<form class="form-horizontal" method="post" action="{{route('lbtc.limite')}}">
									{{ csrf_field() }}
									<div class="col-xs-6 text-center" style="margin:2px 0; padding: 0">
										<input type="text" name="id" value="2" style="display: none" hidden>
										<input type="text" name="limite" class="form-control" value="{{\DB::connection('obmenneg')->table('limites')->where('id', '2')->first()->limite}}" style="margin-bottom: 2px;">
									</div>
									<div class="col-xs-6 text-center" style="margin:2px 0; padding: 0">
										<button type="submit" class="btn btn-primary">
											Ok
										</button>
									</div>
								</form>
							</div>
						@endif
						</td>
						<td style="font-size: 10px;">
						@if ($ad->id_ad==609849)
							@foreach (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', '609849')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==617372)
							@foreach (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', '617372')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==609928)
							@foreach (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', '609928')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==609305)
							@foreach (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', '609305')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==705602)
							@foreach (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', '705602')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==705600)
							@foreach (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', '705600')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==712778)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', '712778')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==712783)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', '712783')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==635074)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', '635074')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==632297)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', '632297')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ad==666810)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers_2')->where('my_id_ad', '666810')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						</td>
						<td>
							<form class="form-inline" method="post" action="{!! route('lbtc.edit.parse.v2', ['id'=>$ad->id_ad]) !!}">
								{{ csrf_field() }}
								<div class="row">
									<div class="col-xs-12" style="margin: 2px; 0; padding: 0;">
										<div class="col-xs-6">
											<span style="margin-top: 7px; display: inline-block;">Парсер</span>
											<select class="form-control" @if ($ad->parser) style="color: green; padding: 3px; width: 84px; float: right;" @else style="color: red; padding: 3px; width: 84px; float: right;" @endif name="parser">
												<option value="0" style="color: red">Выключен</option>
												<option value="1" @if ($ad->parser) selected @endif style="color: green">Включен</option>
											</select>
										</div>
										<div class="col-xs-6">
											<span style="margin-top: 7px; display: inline-block;">Позиция</span>
											<input type="text" style="padding: 3px; width: 50px; float: right;" name="position" class="form-control" value="{{$ad->position}}">
										</div>
									</div>
									<div class="col-xs-12" style="margin: 2px; 0; padding: 0;">
										<div class="col-xs-6">
											<span style="margin-top: 7px; display: inline-block;">Шаг</span>
											<input type="text" name="step" style="padding: 3px; width: 84px; float: right;" class="form-control" value="{{$ad->step}}">
										</div>
										<div class="col-xs-6">
											<span style="margin-top: 7px; display: inline-block;">Порог проц.</span>
											<input type="text" name="min" style="padding: 3px; width: 50px; float: right;" class="form-control" value="{{$ad->min}}">
										</div>
									</div>
									<div class="col-xs-12" style="margin: 2px; 0; padding: 0;">
										<div class="col-xs-12">
											<span style="margin-top: 7px; display: inline-block;">Не учитывать объем меньше</span>
											<input type="text" name="min_max_amount" style="padding: 3px; width: 100px; float: right;" class="form-control" value="{{$ad->min_max_amount}}">
										</div>
									</div>
									
									@if ($ad->id_ad=='617372' or $ad->id_ad=='609305' or $ad->id_ad=='609849' or $ad->id_ad=='609928')
									<div class="col-xs-12" style="margin: 2px; 0; padding: 0;">
										<div class="col-xs-6">
											<span style="margin-top: 7px; display: inline-block;">Робот</span>
											<select class="form-control" @if ($ad->robot==1) style="color: green; padding: 3px; width: 84px; float: right;" @else style="color: red; padding: 3px; width: 84px; float: right;" @endif name="robot">
												<option value="0" style="color: red">Выключен</option>
												<option value="1" @if ($ad->robot==1) selected @endif style="color: green">Включен</option>
											</select>
										</div>
									</div>
									<div class="col-xs-12" style="margin: 2px; 0; padding: 0;">
											<div class="col-xs-6">
												<span style="margin-top: 7px; display: inline-block;">Акт.Курс</span>
												<select class="form-control" @if ($ad->actual==1) style="color: green; padding: 3px; width: 84px; float: right;" @else style="color: red; padding: 3px; width: 84px; float: right;" @endif name="actual">
													<option value="0" style="color: red">Выключен</option>
													<option value="1" @if ($ad->actual==1) selected @endif style="color: green">Включен</option>
												</select>
											</div>
											<div class="col-xs-6">
												<span style="margin-top: 7px; display: inline-block;">Проц. Акт.Курс</span>
												<input type="text" name="pr_actual_price" style="padding: 3px; width: 50px; float: right;" class="form-control" value="{{$ad->pr_actual_price}}">
											</div>
										</div>
									@endif
									
									<div class="text-center" style="margin-top: 5px;">
										<button type="submit" class="btn btn-primary">
											Сохранить
										</button>
									</div>
								</div>
							</form>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
    </div>
</div>
@endsection
