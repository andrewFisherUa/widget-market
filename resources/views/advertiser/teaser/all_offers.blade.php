@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{!! session('message_success') !!}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{!! session('message_danger') !!}
		</div>
	@endif
		@foreach ($offers as $offer)
			<div class="col-xs-3" style="border: solid 1px #000; padding: 5px; text-align: center">
				<a href="{{route('advertiser.delete_offer_company', ['id'=>$offer->id])}}" style="clear: both">Удалить</a><br>
				<img style="height: 200px; width: 200px; clear: both;" src="https://storage.market-place.su/teaser_img/{{$offer->id_company}}/{{$offer->id}}.{{$offer->format}}">
				<p>{{$offer->name}}</p>
				<p>{{$offer->sub_name}}</p>
			</div>
		@endforeach
    </div>
</div>
@endsection
@push ('cabinet_home_top')
@endpush
@push ('cabinet_home_js')
@endpush