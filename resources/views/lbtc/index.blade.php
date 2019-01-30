@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom: 20px;">
		<form class="form-inline" role="form" method="post" action="{{route('lbtc.sms')}}">
		{{ csrf_field() }}
				<div class="input-group col-xs-4 form-group">
					<label for="sms" class="col-xs-5 control-label">SMS оповещения</label>
					<div class="col-xs-7">
						<select style="width: 100%; @if($sms->value==0) color: red; @else color: green; @endif" name="sms">
							<option value="0" @if($sms->value==0) selected @endif style="color: red">Выключены</option>
							<option value="1" @if($sms->value==1) selected @endif style="color: green">Включены</option>
						</select>
					</div>
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
		</form>
	</div>
	<div class="row" style="margin-bottom: 20px">
		<a href="{{route('lbtc.qiwi.robot.list')}}" class="btn btn-success" target="_blank">Обмены робота по Qiwi</a>
		<a href="{{route('lbtc.yandex.robot.list')}}" class="btn btn-success" target="_blank">Обмены робота по Яндекс</a>
		<a href="{{route('lbtc.birges')}}" class="btn btn-success" target="_blank">Курсы на биржах</a>
		
		<a href="{{route('lbtc.qiwi.robot.list.v3')}}" class="btn btn-success" target="_blank">Обмены робота по Qiwi по дням</a>
		<a href="{{route('lbtc.yandex.robot.list.v3')}}" class="btn btn-success" target="_blank">Обмены робота по Яндекс по дням</a>
	</div>
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
			@foreach ($ads as $ad)
				@if ($ad->id_ads==609849 or $ad->id_ads==617372 or $ad->id_ads==609305 or $ad->id_ads==609928 
				or $ad->id_ads==632297 or $ad->id_ads==635074 or $ad->id_ads==666680 or $ad->id_ads==666810)
				<tr>
					<td style="font-size: 40px; vertical-align: middle; text-align: center; padding: 0">{{$ad->id}}</td>
					<td style="@if ($ad->visible) font-weight: bold; color: green; @else font-weight: bold; color: red; @endif">{{$ad->id_ads}}</td>
					<td style="word-wrap: break-word;">
					<a href="https://localbitcoins.net/ads_edit/{{$ad->id_ads}}" target="_blank">{{$ad->name}}</a><br>
					@if ($ad->id_ads==617372)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609849)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
						<br>Актуальный курс: {{round($actual_sell_qiwi['course'])}}
					@elseif ($ad->id_ads==609849)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 617372)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum2-$sum1)/$sum2)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==609895)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609293)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum2-$sum1)/$sum2)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==609293)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609895)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==626956)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609895)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('banks_offers_two')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('banks_offers_two')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==626956)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609895)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('banks_offers_two')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('banks_offers_two')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==632297)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 635074)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==635074)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 632297)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum2-$sum1)/$sum2)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==666810)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 666680)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum2-$sum1)/$sum2)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==666680)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 666810)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==609928)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609305)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum2-$sum1)/$sum2)*100,4)}} %
						@endif
					@elseif ($ad->id_ads==609305)
						@if ($pr=\DB::connection('obmenneg')->table('all_ads')->where('id_ads', 609928)->first())
							<!--
							{{$sum1=1}}
							{{$sum2=1}}
							@if (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ads)->first() 
							and 
							\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ads)->first())
							{{$sum1=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $ad->id_ads)->first()->temp_price}}
							{{$sum2=\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', $pr->id_ads)->first()->temp_price}}
							@endif
							-->
							{{round((($sum1-$sum2)/$sum1)*100,4)}} %
						@endif
						<br>Актуальный курс: {{round($actual_sell_yandex['course'])}}
					@endif
					<!--@if ($ad->id_ads==609849 or $ad->id_ads==617372)
						<br><span style="font-weight: bold">Баланс</span>
						<br>{{$qiwibalance}}
					@endif
					@if ($ad->id_ads==609305 or $ad->id_ads==609928)
						<br><span style="font-weight: bold">Баланс</span>
						<br>{{$yandexbalance}}
					@endif-->
					</td>
					<td style="padding: 8px 3px;">{{$ad->temp_price}}<br>
						Процент: <br>
						@if (!$ad->parse_status)
							<form class="form-horizontal" method="post" action="{{ route('lbtc.edit.prosent', ['id'=>$ad->id_ads])}}">
								{{ csrf_field() }}
								<input type="text" name="prosent" class="form-control" value="{{$ad->prosent}}" style="margin-bottom: 2px;">
								<!--<div style="width: 45%; float: left;">
								<input type="text" name="min_amount" style="padding: 3px" class="form-control" value="{{$ad->min_amount}}">
								</div>
								<div style="width: 10%; float: left; text-align: center"> - </div>
								<div style="width: 45%; float: left;">
								<input type="text" name="max_amount" style="padding: 3px" class="form-control" value="{{$ad->max_amount}}">
								</div>-->
								<div class="col-xs-12 text-center" style="margin:2px 0;">
									<button type="submit" class="btn btn-primary">
										Сохранить
									</button>
								</div>
							</form>
						@else
							{{$ad->prosent}} %
						@endif
						<br>Лимиты:
						<br>{{$ad->min_amount}} - {{$ad->max_amount}}
						@if ($ad->id_ads==609849)
							<br><span style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->buy}}</span>
						@elseif ($ad->id_ads==617372)
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
							<br><span style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->shell}}</span>
						@elseif ($ad->id_ads==609305)
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
							<br><span style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '2')->first()->shell}}</span>
						@elseif ($ad->id_ads==609928)
							<br><span style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '2')->first()->buy}}</span>
						@endif
					</td>
					<td style="font-size: 10px;">
						@if ($ad->id_ads==609849)
							@foreach (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', '609849')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==617372)
							@foreach (\DB::connection('obmenneg')->table('qiwi_offers')->where('my_id_ad', '617372')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==609928)
							@foreach (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', '609928')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==609305)
							@foreach (\DB::connection('obmenneg')->table('yandex_offers')->where('my_id_ad', '609305')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==609293)
							@foreach (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', '609293')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==609895)
							@foreach (\DB::connection('obmenneg')->table('banks_offers')->where('my_id_ad', '609895')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==626956)
							@foreach (\DB::connection('obmenneg')->table('banks_offers_two')->where('my_id_ad', '626956')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==635074)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', '635074')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==632297)
							@foreach (\DB::connection('obmenneg')->table('wmr_offers')->where('my_id_ad', '632297')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==666810)
							@foreach (\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', '666810')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
						@if ($ad->id_ads==666680)
							@foreach (\DB::connection('obmenneg')->table('wmz_offers')->where('my_id_ad', '666680')->get() as $offer)
								Продавец: <b>{{$offer->username}}</b> Цена: <b>{{$offer->temp_price}}</b><span style="float: right; font-weight: bold">{{$offer->min_amount}} - {{$offer->max_amount}}</span><br>
								Ссылка: <a href="https://localbitcoins.net/ad/{{$offer->id_ads}}" target="_blank">{{$offer->name}}</a>
								<hr style="margin: 0; border-color: #000;">
							@endforeach
						@endif
					</td>
					<td>
					<form class="form-inline" method="post" action="{!! route('lbtc.edit.parse', ['id'=>$ad->id_ads]) !!}">
						{{ csrf_field() }}
						<div class="row">
							<div class="col-xs-12" style="margin: 2px; 0">
								<div class="col-xs-6">
									Позиция
									<input type="text" style="padding: 3px; width: 50px;" name="position" class="form-control" value="{{$ad->position}}">
								</div>
								<div class="col-xs-6">
									Шаг
									<input type="text" name="step" style="padding: 3px; width: 100px;" class="form-control" value="{{$ad->step}}">
								</div>
							</div>
							<div class="col-xs-12" style="margin: 2px; 0">
								<div class="col-xs-6">
									Мин. порог
									<input type="text" name="min" style="padding: 3px; width: 32px;" class="form-control" value="{{$ad->min}}">
								</div>
								<div class="col-xs-6">
									Статус
									<select class="form-control" @if ($ad->parse_status) style="color: green; padding: 3px; width: 84px;" @else style="color: red; padding: 3px; width: 84px;" @endif name="parse_status">
										<option value="0" style="color: red">Выключен</option>
										<option value="1" @if ($ad->parse_status) selected @endif style="color: green">Включен</option>
									</select>
								</div>
							</div>
							@if ($ad->id_ads==617372 or $ad->id_ads==609849 or $ad->id_ads==609928 or $ad->id_ads==609305)
							<div class="col-xs-12" style="margin: 2px; 0">
								<div class="col-xs-6">
									Робот
									<select class="form-control" @if ($ad->robot==1) style="color: green; padding: 3px; padding: 3px; width: 100px;" @else style="color: red; padding: 3px; padding: 3px; width: 100px;" @endif name="robot">
										<option value="0" style="color: red">Выключен</option>
										<option value="1" @if ($ad->robot==1) selected @endif style="color: green">Включен</option>
									</select>
								</div>
								@if ($ad->id_ads==617372 or $ad->id_ads==609305)
									<div class="col-xs-6">
										Учитывать актуал. курс
										<select class="form-control" @if ($ad->on_actual==1) style="color: green; padding: 3px; padding: 3px; width: 100px;" @else style="color: red; padding: 3px; padding: 3px; width: 100px;" @endif name="on_actual">
											<option value="0" style="color: red">Нет</option>
											<option value="1" @if ($ad->on_actual==1) selected @endif style="color: green">Да</option>
										</select>
									</div>
								@endif
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
				@endif
			@endforeach
			<!--@foreach ($locales as $locale)
				<tr>
					<td style="font-size: 40px; vertical-align: middle;">{{$locale->id}}</td>
					<td style="@if ($ad->visible) font-weight: bold; color: green; @else font-weight: bold; color: red; @endif">{{$locale->id_ads}}</td>
					<td><a href="https://localbitcoins.net/ads_edit/{{$locale->id_ads}}" target="_blank">{{$locale->name}}</a></td>
					<td>{{$locale->temp_price}}<br>
						Процент: <br>
						@if (!$locale->parse_status)
							<form class="form-horizontal" method="post" action="{{ route('lbtc.edit.prosent', ['id'=>$locale->id_ads])}}">
								{{ csrf_field() }}
								<input type="text" name="prosent" class="form-control" value="{{$locale->prosent}}">
								<div class="text-center" style="margin-top: 5px;">
									<button type="submit" class="btn btn-primary">
										Сохранить
									</button>
								</div>
							</form>
						@else
							{{$locale->prosent}} %
						@endif
					</td>
					<td>
						
					</td>
					<td>
					
					</td>
				</tr>
			@endforeach-->
		</table>
    </div>
</div>
@endsection
