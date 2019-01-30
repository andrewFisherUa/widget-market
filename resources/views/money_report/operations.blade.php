@extends('layouts.app')

@section('content')
<div class="container">
	@include('money_report.menu')
	<div class="row">
		<h3 class="text-center">Операции {{$account->title}} по отчету № {{$rep->id}} от {{$rep->opened}}</h3>
		<div class="col-xs-12" style="margin: 5px 0;">
			<form class="form-inline" role="form" method="get">
				<div class="row">
					<div class="input-group col-xs-2 form-group">
						<select name="type" class="form-control">
							<option value="0">Все</option>
							<option @if ($type==1) selected @endif value="1">Входящие</option>
							<option @if ($type==2) selected @endif value="2">Исходящие</option>
						</select>
					</div>
					<div class="input-group col-xs-2 form-group">
						<select name="shortcode" class="form-control">
							<option value="0">Все</option>
							@foreach ($typesMenu as $menu)
								<option @if ($shortcode==$menu->shortcode) selected @endif value="{{$menu->shortcode}}">{{$menu->title}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
		<div class="col-xs-12">
			По состоянию курса на {{$spec_cources->date}} | USD={{$spec_cources->usd}} | BTC={{$spec_cources->btc}}
		</div>
		<div class="col-xs-6 text-left">
			Остатки на начало отчета по счету: @if ($balance) {{$balance->summa_opened}} руб / {{round($balance->summa_opened/$spec_cources->usd,2)}} usd / {{round($balance->summa_opened/$spec_cources->btc,8)}} btc @endif
		</div>
		<div class="col-xs-6 text-right">
			@if ($report->closed)
				Остатки на конец отчета по счету: @if ($balance) {{$balance->summa_closed}} руб / {{round($balance->summa_closed/$spec_cources->usd,2)}} usd / {{round($balance->summa_closed/$spec_cources->btc,8)}} btc @endif
			@else
				Остатки на данный момент по счету: @if ($balance) {{$balance->summa_closed}} руб / {{round($balance->summa_closed/$spec_cources->usd,2)}} usd / {{round($balance->summa_closed/$spec_cources->btc,8)}} btc @endif
			@endif
		</div>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td>Время</td>
					<td>Тип</td>
					<td>Сумма</td>
					<td>Комментарий</td>
				</tr>
			</thead>
			<tbody>
				<tr style="color: #fff; background: #000">
					<td>Всего</td>
					<td></td>
					<td>@if ($sum){{round($sum->summa,8)}}@endif</td>
					<td></td>
				</tr>
				@foreach ($operations as $operation)
					<tr style="@if ($operation->type==1) background: rgba(0, 204, 0, 0.15); @elseif ($operation->type==2) background: rgba(255, 0, 35, 0.15); @endif">
						<td>{{$operation->datetime}}</td>
						<td class="text-left">
							@foreach ($types as $type)
								@if($type->type==$operation->type and $type->shortcode==$operation->shortcode)
									@if($operation->obnal)<b>Обнал</b>@endif {{$type->title}}
								@endif
							@endforeach
						</td>
						<td>{{round($operation->summa,8)}} {{$operation->account->shortcode}}</td>
						<td>{{$operation->comment}}</td>
					</tr>
				@endforeach
				<tr style="color: #fff; background: #000">
					<td>Всего</td>
					<td></td>
					<td>@if ($sum){{round($sum->summa,8)}}@endif</td>
					<td></td>
				</tr>
			</tbody>
		</table>
    </div>
</div>
@endsection
@push('cabinet_home')
	<style>
		.table>thead>tr>td, .table>tbody>tr>td{
			vertical-align: middle;
		}
	</style>
@endpush
@push('cabinet_home_js')
	
@endpush
