@extends('layouts.app')
@push('cabinet_home')
 <link rel="stylesheet" type="text/css" href="https://widget.market-place.su/projects/styles/tree.css?v=1.0"/>
@endpush
@section('content')
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
    </div>
	<h2>Подстановка категории для товаров Магазина {{$shop->name}}</h2>

	<div class="row">
       <div class="col-sm-8">
	       <div class="row">
		   <div class="col-sm-5">Категория магазина</div>
	       <div class="col-md-7"><strong><u>{{$shop_category->name}}</u></strong></div>
		  
		   </div>
	   <br></br>
	   <div class="row">
	       <div class="col-sm-3">Товар</div>
	       <div class="col-md-9">
		   <div class="row">
		   <div class="col-sm-3"><img style="max-width:100px;max-height:100px" alt ="{!! \App\MPW\Widgets\Product::getPicture($offer->id,$offer->picture) !!}" src="{!! \App\MPW\Widgets\Product::getPicture($offer->id,$offer->picture) !!}"></div>
	       <div class="col-md-9"><a href="{{$offer->url}}" target="_blank"><strong>{{$offer->type_prefix}} {{$offer->name}}</strong></a></div>
		  
		   </div>
		   </div>
		   <h3>Другие товары этой категории</h3>
	   </div>
	   {!! $offers->appends([])->render() !!}	   
	   @foreach($offers as $o)
		   <div class="row">
		   <div class="col-sm-3"><img style="max-width:100px;max-height:100px" alt="{!! \App\MPW\Widgets\Product::getPicture($o->id,$o->picture) !!}" src="{!! \App\MPW\Widgets\Product::getPicture($o->id,$o->picture) !!}"></div>
	       <div class="col-md-9"><strong><a href="{{$o->url}}" target="_blank">{{$o->type_prefix}}  {{$o->name}}</strong></a></div>
		  
		   </div>
       @endforeach	 

	   </div>
       <div class="col-sm-4">
	   @if($shop_category && $shop_category->edited)
		    <div class="row">
		   <div class="col-sm-5">Исправлено</div>
	       <div class="col-md-7"><strong><u>{{$shop_category->edited}}</u></strong></div>
		  
		   </div>
		   <div class="row">
		  Изменения вступают в силу в течении часа или двух
		  
		   </div>
		   <br>
	   @endif	   
	   <form id="form-custom-widget" class="form-horizontal" method="post" novalidate action="{!! route('advertiser.check_company', ['id'=>$offer->id]) !!}">
			{{ csrf_field() }}
	   	<div class="text-center form-group">
								<div>
								    <span data-id="0" class="btn btn-success" onclick="createMyTreeElenent(this,'search','Категории поиска');" >Выбрать категории</span>
									
								</div>
								
								<div>
									<ul id="main_yandex_categories" class="list-inline">
									@if($yandex_category)
										<li onclick="this.parentNode.removeChild(this); postReload();">
											<div class="btn btn-default btn-sm posit-rel">
												<input name="cattuya[$offer->shop_category][{{$yandex_category->id}}]" value="{{$yandex_category->id}}" type="hidden">
														{{$yandex_category->uniq_name}}
											</div>
										</li> 
									@endif	
	                                  
										
									</ul>
								</div>
							</div>
			 <button type="submit" class="btn btn-primary">
				Сохранить
			</button>
			</form>
	   </div>
	</div>
</div>
    <div class="modal fade" id="myModalCategory" role="dialog">
    <div class="modal-dialog">
       <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Выбрать категорию</h4>
        </div>
        <div class="modal-body">
		<div class="row">
		<div class="col-lg-9">  
		<input type="text" class = "microsearch_input">
		</div><div class="col-lg-3">
		<button type="button" class = "microsearch_button" class="btn btn-primary btn-sm">Поиск</button>
		</div>
		</div>
        <div class="row" id ="teletree">
		</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Сохранить</button>
        </div>
      </div>
     </div>  
    </div>
@endsection	
@push('cabinet_home_js')
<script src="https://widget.market-place.su/projects/categorytree.js?v=1.0"></script>
 <script>
 function createMyTreeElenent(obj,cls,title){
  var resContainer=obj.parentNode.parentNode.querySelector("ul");
  var prevs=[];
  if(resContainer){
	  
	  
   var lis = resContainer.querySelectorAll("li");
  
   for (var i=0,j=lis.length;i<j;i++){
   var inp=lis[i].querySelector("input");
   if(inp){
   prevs.push(inp.value);
   }
   }
  }
 // window.reloaddata();
  $("#myModalCategory h4").html(title); 
  var tmp = new window.ContextCategory(obj,cls,$("#myModalCategory").get(0),resContainer,prevs,function(){
  },function(){
	postReload();
  });

  
  $("#myModalCategory").modal('toggle');
  }
  function postReload(){

  }
   $("#myModalCategory").on("hidden.bs.modal", function() {
    postReload()
   });
 </script>	
 @endpush
