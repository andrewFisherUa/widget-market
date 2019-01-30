@extends('layouts.app')
@section('content')
<div class="container">

@widget('AdvertTop',[])
@role(['admin','super_manager','manager'])

<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',['id_user'=>$user->id])}}" class="btn btn-success">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',['id_user'=>$user->id])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$user->id,'shop_id'=>0])}}" class="btn btn-primary">Статистика всех рекламных компаний пользователя</a>
<a href="{{ route('admin.balance_history',['id_user'=>$user->id,'shop_id'=>0])}}" class="btn btn-primary">Взаиморасчёты пользователя</a>


@endrole

    <div class="row">
	<div class="text-center"><h4>Файлы пользователя {{$user->name}}</h4></div>
	</div>
	   <div class="row">
       <iframe src="/home/{{$user->id}}/laravel-filemanager?type=Files"  style="width: 100%; height: 500px; overflow: hidden; border: none;"></iframe>
	   </div>

</div>
@endsection