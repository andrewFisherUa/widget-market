@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		@if (Session::has('message_warning'))
			<div class="alert alert-warning">
				{{ session('message_warning') }}
			</div>
		@endif

	</div>
			<div style="margin: 10px 0">
        <a href="{{route('advert_setting.add_category')}}" class="btn btn-primary">Создать новую категорию</a>

        </div>
    <div class="row">
		<h4 class="text-center">Настройка категорий по товарному виджету</h4>
		@foreach($categories  as $category)
		<div class="row">{{$category->name}}
		<a href="{{route('advert_setting.advert_category',["id"=>$category->id])}}"><span class="glyphicon glyphicon-pencil news-gliph-all color-blue"></span></a>
			{{--	<form  method="post" action ="{{route('advert_setting.delete_category',["id"=>$category->id])}}" style="display:inline;margin:0px;padding:0px;">
<button type="submit" class="btn btn-danger" style ="height:22px;font-size:12px;padding-top:0">Удалить</button>
{!! csrf_field() !!}


</form> --}}
			
</div>	
		@endforeach
    </div>		
</div>	
	
@endsection