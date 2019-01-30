@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
	<div class="row">
		<div class="col-xs-12">
			<form class="form-inline" role="form" method="get" action="">
				<div class="row" style="text-align:center">
					<div class="input-group col-xs-2 form-group">
						<span class="input-group-addon">Период:</span>
						<input type="text" class="form-control" value="{{$period}}" name="period">
					</div>
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div>{!! $graph->render() !!}</div>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<style>
	.morris-hover.morris-default-style{
		max-height: 300px;
		overflow: auto;
		width: 250px!important;
	}
	#white_links, #adult_links, #razv_links, #not_me_links, #all_links{
	height: 270px;
	overflow: hidden;
	}
	.graph_block{
	border: 1px solid #cacaca;
    background-image: url(/images/cabinet/background_block.png);
    background-color: rgba(199, 199, 199, 0.5);
    height: 300px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
    -moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
	}
	.checkbox_for_src{
		float: right;
		margin-right: 10px!important;
	}
	.title_for_src{
		width: calc(100% - 30px);
		display: inline-block;
	}
	</style>
	{!! Charts::assets() !!}
@endpush
@push('cabinet_home_js')

@endpush