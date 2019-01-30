@extends('layouts.app')

@section('content')
<div class="container">
	@include('money_report.menu')
	<div class="row">
		<h3 class="text-center">Операции по отчету № {{$rep->id}} от {{$rep->opened}}</h3>
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
							<option @if ($shortcode=='rbtobm') selected @endif value="rbtobm">Робот и обменник</option>
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
			Остатки на начало отчета по счету: {{$rep->summa_opened}} руб / {{round($rep->summa_opened/$spec_cources->usd,2)}} usd / {{round($rep->summa_opened/$spec_cources->btc,8)}} btc
		</div>
		<div class="col-xs-6 text-right">
			@if ($report->closed)
				Остатки на конец отчета по счету: {{$rep->summa_closed}} руб / {{round($rep->summa_closed/$spec_cources->usd,2)}} usd / {{round($rep->summa_closed/$spec_cources->btc,8)}} btc
			@else
				Остатки на данный момент по счету: {{$rep->summa_closed}} руб / {{round($rep->summa_closed/$spec_cources->usd,2)}} usd / {{round($rep->summa_closed/$spec_cources->btc,8)}} btc
			@endif
		</div>
		<table class="table table-hover table-bordered text-center">
			<thead>
				<tr>
					<td>Тип</td>
					<td>Сумма</td>
					<td>Детально</td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($sum->summa)}}</td>
					<td></td>
				</tr>
				@foreach ($operations as $operation)
					<tr style="@if ($operation->type==1) background: rgba(0, 204, 0, 0.15); @elseif ($operation->type==2) background: rgba(255, 0, 35, 0.15); @endif">
						<td class="text-left">
							@foreach ($types as $type)
								@if($type->type==$operation->type and $type->shortcode==$operation->shortcode)
									<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="
										<table>
											
										@foreach ($cachs as $cach)
											@if ($cach->shortcode==$operation->shortcode and $cach->type==$operation->type)
												<tr style='border-bottom: 1px solid #fff'>
												<td>{{$cach->title}} {{$cach->valut}}</td>
												<td>@if ($cach->type==1)+@else-@endif{{$cach->summa}}</td>
												</tr>
											@endif
										@endforeach
											</tr>
										</table>
									">
									{{$type->title}}
									</span>
								@endif
							@endforeach
						</td>
						<td>{{round($operation->summa)}}</td>
						<td>
						@if ($operation->shortcode=='obm' or $operation->shortcode=='rbt')
							<a href="{{route('money.report.operation.accounts', ['id'=>$rep->id, 'shortcode'=>'rbtobm'])}}">Детально</a>
						@else
							<a href="{{route('money.report.operation.accounts', ['id'=>$rep->id, 'shortcode'=>$operation->shortcode])}}">Детально</a>
						@endif
						</td>
					</tr>
				@endforeach
				<tr style="background: #000; color: #fff">
					<td>Всего</td>
					<td>{{round($sum->summa)}}</td>
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
	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip({html:true})
		})
	</script>
@endpush
