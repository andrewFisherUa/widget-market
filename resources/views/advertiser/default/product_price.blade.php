@extends('layouts.app')

@section('content')
<div class="container">
<a  href="{{ route('advertiser.add_admin') }}" >Список рекламных компаний</a>
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{!! session('message_success') !!}
		</div>
	@endif
	@if (Session::has('message_war'))
		<div class="alert alert-warning">
			{!! session('message_war') !!}
		</div>
	@endif
        <form class="form-horizontal" method="post" action="{{ route('advert_setting.save_default_product')}}">
			{{ csrf_field() }}
			<input type="hidden" name="id" value="{{$id}}">
			<div class="panel-group">			
	<h2>Укажите цену за переход {{$id}}</h2>			
@widget('GeoRating',["id"=>$id,"noprice"=>0])				
				<div class="form-group">
					<button type="submit" class="btn btn-primary">
					Сохранить
				</div>
			</button>
			</div>
		</form>
    </div>
</div>
</div>
@endsection
