@extends('layouts.app')

@section('content')
<div class="container">
<h2>рекламная компания {{$model->name}}</h2>
  
</div>

<div><a href ="{{route('advertiser.create_shop',["id_company"=>$model->id])}}">Добавить Магазин</a></div>
@endsection
