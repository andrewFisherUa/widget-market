@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom: 20px;">
		<div class="panel-group" id="accordion">
			<table class="table text-center" style="table-layout: fixed">
				<colgroup>
					<col span="1" style="width: 55px;">
					<col span="1" style="width: 320px;">
					<col span="2" style="width: 80px;">
					<col span="1" style="width: 110px;">
					<col span="1" style="width: 80px;">
					<col span="2" style="width: 65px;">
					<col span="2" style="width: 80px;">
				</colgroup>
				<tbody>
					<tr>
						<td>Id Lbtc</td>
						<td>Название</td>
						<td>Прайс</td>
						<td>Процент</td>
						<td>Лимиты</td>
						<td>Разница</td>
						<td>Позиция</td>
						<td>Порог</td>
						<td>Парсер</td>
						<td>Робот</td>
					</tr>
				</tbody>
			</table>
			@foreach ($ads as $ad)
				<div class="panel panel-default" style="position: relative;">
					<a data-toggle="collapse" class="heading-href" href="#collapse-{{$ad->id_ads}}">
						<div class="panel-heading">
							<table class="table" style="table-layout: fixed">
								<colgroup>
									<col span="1" style="width: 55px;">
									<col span="1" style="width: 320px;">
									<col span="2" style="width: 80px;">
									<col span="1" style="width: 110px;">
									<col span="1" style="width: 80px;">
									<col span="2" style="width: 65px;">
									<col span="2" style="width: 80px;">
								</colgroup>
								<tbody>
									<tr>
										<td style="font-weight: bold; @if ($ad->visible) color: green; @else color: red; @endif">{{$ad->id_ads}}</td>
										<td>{{$ad->name}}</td>
										<td class="text-center">{{$ad->temp_price}}</td>
										<td class="text-center">{{round($ad->prosent,4)}}</td>
										<td class="text-center">{{$ad->min_amount}} - {{$ad->max_amount}}</td>
										<td class="text-center">
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
											
											@endif
										</td>
										<td class="text-center">{{$ad->position}}</td>
										<td class="text-center">{{$ad->min}} @if ($ad->min) % @endif</td>
										<td class="text-center" style="font-weight: bold; @if ($ad->parse_status) color: green; @else color: red; @endif">@if ($ad->parse_status) Включен @elseif ($ad->id_ads==669280 or $ad->id_ads==688318 or $ad->id_ads==689947) @else Выключен @endif</td></td>
										<td class="text-center" style="font-weight: bold; @if ($ad->robot==1) color: green; @else color: red; @endif">@if ($ad->robot==1) Включен @elseif ($ad->id_ads!=609849 and $ad->id_ads!=617372 and $ad->id_ads!=609928 and $ad->id_ads!=609305) @else Выключен @endif</td>
									</tr>
								</tbody>
							</table>
						</div>
					</a>
					<div id="collapse-{{$ad->id_ads}}" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-4" id="predlog-{{$ad->id_ads}}" style="font-size: 12px;">
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
								</div>
								<div class="col-xs-4 text-center">
									<a href="https://localbitcoins.net/ads_edit/{{$ad->id_ads}}" target="_blank" style="font-weight: bold;">Редактор на ЛокалБиткоине</a>
									@if (!$ad->parse_status)
										<form class="form-inline" method="post" action="{{ route('lbtc.edit.prosent', ['id'=>$ad->id_ads])}}">
											{{ csrf_field() }}
											<div class="col-xs-4">
												Процент
											</div>
											<div class="col-xs-4">
												<input type="text" name="prosent" class="form-control" value="{{$ad->prosent}}" style="margin-bottom: 2px; width: 100%">
											</div>
											<div class="col-xs-4">
												<button type="submit" class="btn btn-primary btn-sm">
													Сохранить
												</button>
											</div>
										</form>
									@endif
									@if ($ad->id_ads==609849)
										<br><div class="col-xs-4">Лимит:</div>
										<div class="col-xs-4" style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->buy}}</div>
									@elseif ($ad->id_ads==617372)
										<br><div class="col-xs-4">Лимит:</div>
										<div class="col-xs-4" style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->shell}}</div>
										<div class="col-xs-12" style="padding: 0">
											<form class="form-inline" method="post" action="{{route('lbtc.limite')}}">
												{{ csrf_field() }}
												<div class="col-xs-4">Назначить лимит:</div>
												<div class="col-xs-4" style="margin:2px 0; padding: 0">
													<input type="text" name="id" value="1" style="display: none" hidden>
													<input type="text" name="limite" class="form-control" value="{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->limite}}" style="margin-bottom: 2px; width: 100%;">
												</div>
												<div class="col-xs-4" style="margin:2px 0; padding: 0">
													<button type="submit" class="btn btn-primary btn-sm">
														Ok
													</button>
												</div>
											</form>
										</div>
									@elseif ($ad->id_ads==609305)
										<br><div class="col-xs-4">Лимит:</div>
										<div class="col-xs-4" style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '2')->first()->shell}}</div>
										<div class="col-xs-12" style="padding: 0">
											<form class="form-horizontal" method="post" action="{{route('lbtc.limite')}}">
												{{ csrf_field() }}
												<div class="col-xs-4" style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '1')->first()->shell}}</div>
												<div class="col-xs-4 text-center" style="margin:2px 0; padding: 0">
													<input type="text" name="id" value="2" style="display: none" hidden>
													<input type="text" name="limite" class="form-control" value="{{\DB::connection('obmenneg')->table('limites')->where('id', '2')->first()->limite}}" style="margin-bottom: 2px; width: 100%;">
												</div>
												<div class="col-xs-6 text-center" style="margin:2px 0; padding: 0">
													<button type="submit" class="btn btn-primary btn-sm">
														Ok
													</button>
												</div>
											</form>
										</div>
									@elseif ($ad->id_ads==609928)
										<br><div class="col-xs-4">Лимит:</div>
										<div class="col-xs-4" style="font-weight: bold; color: red;">{{\DB::connection('obmenneg')->table('limites')->where('id', '2')->first()->buy}}</div>
									@endif
								</div>
								<div class="col-xs-4">
									@if ($ad->id_ads!=669280 and $ad->id_ads!=688318 and $ad->id_ads!=689947)
										<form class="form-horizontal" method="post" action="{!! route('lbtc.edit.parse', ['id'=>$ad->id_ads]) !!}">
											{{ csrf_field() }}
												<div class="form-group">
													<label for="position" class="col-xs-4 control-label">Позиция</label>
													<div class="col-xs-8">
														<input type="text" class="form-control" name="position" value="{{$ad->position}}">
													</div>
												</div>
												<div class="form-group">
													<label for="step" class="col-xs-4 control-label">Шаг</label>
													<div class="col-xs-8">
														<input type="text" class="form-control" name="step" value="{{$ad->step}}">
													</div>
												</div>
												<div class="form-group">
													<label for="min" class="col-xs-4 control-label">Мин. порог</label>
													<div class="col-xs-8">
														<input type="text" class="form-control" name="min" value="{{$ad->min}}">
													</div>
												</div>
												<div class="form-group">
													<label for="parse_status" class="col-xs-4 control-label">Парсер</label>
													<div class="col-xs-8">
														<select class="form-control" @if ($ad->parse_status) style="color: green;" @else style="color: red;" @endif name="parse_status">
															<option value="0" style="color: red">Выключен</option>
															<option value="1" @if ($ad->parse_status) selected @endif style="color: green">Включен</option>
														</select>
													</div>
												</div>
												@if ($ad->id_ads==617372 or $ad->id_ads==609849 or $ad->id_ads==609928 or $ad->id_ads==609305)
												<div class="form-group">
													<label for="robot" class="col-xs-4 control-label">Робот</label>
													<div class="col-xs-8">
														<select class="form-control" @if ($ad->robot==1) style="color: green;" @else style="color: red;" @endif name="robot">
															<option value="0" style="color: red">Выключен</option>
															<option value="1" @if ($ad->robot==1) selected @endif style="color: green">Включен</option>
														</select>
													</div>
												</div>
												@endif
												<div class="form-group">
													<div class="col-xs-8 col-xs-offset-4 text-center">
														<a data-id="{{$ad->id_ads}}" type="submit" class="get_parser btn btn-primary btn-sm">
															Сохранить
														</a>
													</div>
												</div>
										</form>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div id="l-{{$ad->id_ads}}" class="preload"></div>
				</div>
			@endforeach
		</div>
    </div>
</div>
@endsection
@push('cabinet_home_top')
	<style>
		.table{
			margin: 0;
		}
		.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
			padding: 0;
			border-top: 0;
		}
		.heading-href{
			text-decoration: none;
			color: #636b6f;
		}
		.heading-href:hover, .heading-href:active, .heading-href:focus{
			text-decoration: none;
			color: #636b6f;
		}
		.panel-heading{
			padding: 10px 5px;
		}
		.form-group{
			margin-bottom: 3px;
		}
		.form-control{
			height: 30px;
			padding: 4px 12px;
		}
		.preload{
			position: absolute;
			width: 40px;
			height: 40px;
			top: 0;
			right: -45px;
			background-size: 100% 100%;
		}
	</style>
@endpush
@push('cabinet_home_js')
<script>
	$('.heading-href').click(function(){
		if ($(this).parent().find('.panel-collapse ').hasClass('in')){
			$(this).parent().css('background', '#fff');
		}
		else{
			$(this).parent().css('background', '#e8e8e8');
		}
	});
	
	$('.get_parser').click(function(){
		var form=$(this).parents('.form-horizontal');
		var position=form.find('input[name=position]').val();
		var step=form.find('input[name=step]').val();
		var min=form.find('input[name=min]').val();
		var parse_status=form.find('select[name=parse_status]').val();
		var robot=form.find('select[name=robot]').val();
		var id=$(this).data('id');
		$('#l-'+id).css('background-image', 'url("https://widget.market-place.su/images/loading.gif")');
		$.get('/lbtc/new_parse/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), 
		position: position, step: step, min: min, parse_status, robot: robot}, function(response) {
			if (response.ok){
				$('#l-'+id).css('background-image', 'url("https://widget.market-place.su/images/ok.png")');
			}
			else{
				$('#l-'+id).css('background-image', 'url("https://widget.market-place.su/images/done.png")');
			}
		});
	});
</script>
@endpush


