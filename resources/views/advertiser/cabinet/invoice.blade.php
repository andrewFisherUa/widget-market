@extends('layouts.app')
@push('cabinet_home_top')
<style>
.table > thead > tr > th {
	white-space: nowrap;
}
</style>
@endpush
@section('content')


<div class="container" >
@if($config['pref'] && isset($config["wparams"]["id_user"]))
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',$config['wparams'])}}" class="btn btn-primary">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',$config['wparams'])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.balance_history',$config['wparams'])}}" class="btn btn-primary">Взаиморасчёты пользователя</a>
<a href="{{ route('admin.statistic',$config['wparams'])}}" class="btn btn-primary">Статистика всех рекламных компаний пользователя</a>
<a href="{{ route('admin.disco',$config['wparams'])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@endif
	    <div class="row text-center">
		<button type="button" id="ras_print" class="btn btn-primary">Распечатать</button>
	           <button type="button" id="ras_download" class="btn btn-primary">Сохранить в pdf</button>
	    </div>
		
	    <div class="row" >
   	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <iframe name ="myFrame" src="{{route($config['pref'].'invoice_print',$config['wparams'])}}" style="width:300mm;height:220mm;border:none">
          </iframe>
		  
	    </div>

    </div>		
</div>
@php
var_dump($config['wparams']['id']);
@endphp
@endsection
@push('cabinet_home_js')
<script>		
$(document).ready(function() {
	$('#ras_print').click(function(){
		window.frames["myFrame"].postMessage("print",'*');

	});
	$('#ras_download').click(function(){
		window.open("/adv_/getpdf/{{$config['wparams']['id']}}");
		//alert("{{$config['wparams']['id']}}");
		//alert("передам скачивание");
	})
	
});
</script>
@endpush