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
<div class="row" style="margin-top: 0px;">
    @widget('AdminHomepanel',["user"=>$user])
</div>
	<div class="row" style="margin-top: 30px;">
	
	    @if(isset($admin))
		{{--@widget('ClientCompanies',["user"=>$user,"admin"=>1])--}}

			@widget('UserCompanies',["user"=>$user,"admin"=>1])
    @else
	@php
    if(1==0 && $_SERVER["REMOTE_ADDR"]=="176.213.140.214"){
    @endphp
    @widget('ClientCompanies',["user"=>$user])
	@php
	}else{
    @endphp
	@widget('UserCompanies',["user"=>$user])
	@php
	}
	@endphp

@endif
</div>	
</div>	
</div>	
@endsection
@push('cabinet_home')
    <style>
	.customright {margin-bottom:4px;}
	.panel-left-padding{
		padding-left:17px;
		margin-bottom:10px;
	}
	</style>
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/news.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush

@push('cabinet_home_js')
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
			$('.default_status').popover({html : true});
		});
	</script>
@endpush

