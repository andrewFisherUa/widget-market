@extends('layouts.app')
@push('cabinet_home_top')
<style>
.table > thead > tr > th {
	white-space: nowrap;
}
</style>
@endpush
@section('content')
<div class="container">
	    <div class="row">
	    <div class="text-center"><h4 >{{$title}}</h4></div>
	    </div>
	    <div class="row">
   	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
		@php
         #$url=;
         #var_dump($url);
        @endphp
		
        {!! $error_url !!}
	    </div>

    </div>		
</div>
@endsection