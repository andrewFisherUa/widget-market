@extends('layouts.app')
@push('cabinet_home')

 <link rel="stylesheet" type="text/css" href="https://widget.market-place.su/projects/styles/tree.css?v=1.0"/>
@endpush
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
		<div style="margin: 10px 0">
        <a href="{{route('advert_setting.advert_categories')}}" class="btn btn-primary">Вернутья в список категорий</a>

        </div>
	</div>
    <div class="row">
		<h4 class="text-center">Настройки категории <u>{{$category->name}}</u></h4>
       
    </div>		
	<form class="form-inline" role="form" method="post" action=" {{ route('advert_setting.advert_category',["id"=>$category->id]) }}">	
	{!! csrf_field() !!}
	<input type="hidden" name ="name" value ="{{$category->name}}"/>
	
	<div class="row">
    <div class="col-md-6 text-center">Синтаксис Sphinx</div>
	<div class="col-md-6 text-center">Яндекс категории</div>

	
    
	</div>
	<div class="row">
 
	<div  class="col-md-6 text-center">
	<textarea name ="templates" style="width:100%" rows ="10">{{$category->templates}}</textarea></div>
	
    <div class="col-md-6 text-center">
	<div class="col-md-1"><span data-id="1736" onclick="createMyTreeElenent(this,'search','Категории поиска');" class="glyphicon glyphicon-edit"></span>
	
	</div>
	<ul class="list-inline">
	@foreach($yandex_categories as $ya)
	<li onclick="this.parentNode.removeChild(this);"><div class="btn btn-default btn-sm posit-rel"><input name="cattuya[{{$category->id}}][{{$ya->id}}]" value="{{$ya->id}}" type="hidden">{{$ya->uniq_name}}</div></li> 
	@endforeach 
	
	
	</ul>
	</div>
	</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-primary">Применить</button>
				</div>
	</form>
</div>	

    <div class="modal fade" id="myModal" role="dialog">
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
  $("#myModal h4").html(title); 
  var tmp = new window.ContextCategory(obj,cls,$("#myModal").get(0),resContainer,prevs);
  $("#myModal").modal('toggle');
  }
 </script>	
 @endpush