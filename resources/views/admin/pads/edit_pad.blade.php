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
		<div class="col-xs-8 col-xs-offset-2 moder_pad">
			<h4 class="text-center"><a href="{{route('admin.home', ['id_user'=>$pad->user_id])}}" target="_blank">{{$pad->setUser($pad->user_id)->name}}</a></h4>
			<form class="form-horizontal" role="form" method="post" action="{{ route('pads.save', ['id'=>$pad->id]) }}">
			{!! csrf_field() !!}
				<div class="form-group">
					<label for="domain" class="col-xs-4 control-label"><a href="http://{{$pad->domain}}" target="_blank">Домен</a></label>
					<div class="col-xs-8">
						<input type="text" name="domain" class="form-control" value="{{$pad->domain}}" required>
					</div>
				</div>
				<div class="form-group">
					<label for="type" class="col-xs-4 control-label">Тип</label>
					<div class="col-xs-8">
						<div class="col-xs-4 text-center">
						Товарный<br>
						<input type="checkbox" id="tovar_type" class="" @if ($pad->type==-1 or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13) checked @endif name="type[]" value="1">
						</div>
						<div class="col-xs-4 text-center">
						Видео<br>
						<input type="checkbox" id="video_type" class="" @if ($pad->type==-1 or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14) checked @endif name="type[]" value="2">
						</div>
						<div class="col-xs-4 text-center">
						Тизерный<br>
						<input type="checkbox" id="teaser_type" class="" @if ($pad->type==-1 or $pad->type==4 or $pad->type==5 or $pad->type==6 or $pad->type==7 or $pad->type==12 or $pad->type==13 or $pad->type==14) checked @endif  name="type[]" value="4">
						</div>
						@if (\Auth::user()->hasRole('admin'))
						<div class="col-xs-4 text-center">
						Брендирование<br>
						<input type="checkbox" class="" @if ($pad->type==-1 or $pad->type==8 or $pad->type==9 or $pad->type==10 or $pad->type==11 or $pad->type==12 or $pad->type==13 or $pad->type==14) checked @endif name="type[]" value="8">
						</div>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="statistic" class="col-xs-4 control-label"><a href="{{$pad->stcurl}}" target="_blank">Статистика</a></label>
					<div class="col-xs-8">
						<input name="stcurl" type="text" placeholder="Ссылка" value="{{$pad->stcurl}}" class="form-control stat_url">
						<input name="stclogin" type="text" placeholder="Логин" value="{{$pad->stclogin}}" class="form-control stat_log">
						<input name="stcpassword" type="text" placeholder="Пароль" value="{{$pad->stcpassword}}" class="form-control stat_pas">
					</div>
				</div>
				<div class="form-group" id="id_categories" @if ($pad->type==-1 or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13) style="display: block;" @else style="display: none;" @endif>
					<label for="id_categories" class="col-md-4 control-label">Категории товаров</label>
					<div class="col-md-6 text-center">
					<div class="col-md-1"><span data-id="1736" onclick="createMyTreeElenent(this,'search','Категории поиска');" class="glyphicon glyphicon-edit"></span>
					
					</div>
					<ul class="list-inline">
					@foreach($categories as $ya)
					<li onclick="this.parentNode.removeChild(this);"><div class="btn btn-default btn-sm posit-rel"><input name="cattuya[0][{{$ya->id}}]" value="{{$ya->id}}" type="hidden">{{$ya->uniq_name}}</div></li> 
					@endforeach 
					
					
					</ul>
					</div>
				</div>
				<div class="form-group" id="id_teaser" @if ($pad->type==-1 or $pad->type==4 or $pad->type==5 or $pad->type==6 or $pad->type==7 or $pad->type==12 or $pad->type==13 or $pad->type==14) style="display: block;" @else style="display: none;" @endif>
					<label for="id_categories" class="col-md-4 control-label">Категории тизеров</label>
					<div class="col-xs-6">
					<input name="id_t_categories[-1]" id="toggle_category" type="checkbox" value="1" @if($k2) checked @endif > <b>Все</b><br>
					   @foreach($t_categories as $t_category)
					   @if($t_category->id>0)
						<input name="id_t_categories[{{$t_category->id}}]" class="category_check" @if($k2 || $t_category->category_id) checked @endif  type="checkbox" value="1" > {{$t_category->name}}<br>
					@endif	
						@endforeach
					</div>
					
				</div>
				<div class="form-group" id="driver">
					<label for="id_categories" class="col-md-4 control-label">Драйвер</label>
					<div class="col-xs-8">
						<select name="driver" class="form-control">
							<option value="0">Не выбран</option>
							<option @if ($pad->driver==1) selected @endif value="1">Адверт</option>
							<option @if ($pad->driver==2) selected @endif value="2">Яндекс</option>
							<option @if ($pad->driver==3) selected @endif style='color: blue; font-weight: bold;' value="3">Яндекс API</option>
							<option @if ($pad->driver==11) selected @endif value="11">Надави</option>
						</select>
					</div>
				</div>
				<div class="form-group" id="clids">
					<label for="id_categories" class="col-md-4 control-label">Клид</label>
					<div class="col-xs-8">
						<select name="clid" class="form-control">
							<option value='0'>Не выбрано</option>

							@foreach ($clids as $clid)
								<!-- {{ $clid_pad=\App\PartnerPad::where('clid', $clid->clid)->first() }} -->
								<option @if($clid->clid=='2291286') style="color: green; font-weight: bold;" @endif 
								{{-- Этот клид не использовать --}}	
								
								@if ($clid_pad and $clid->clid!='2291286') style='color: red; font-weight: bold;' @endif
								
								@if ($clid->type==1) style='color: blue; font-weight: bold;'
								@else style='color: black; font-weight: bold;'
								@endif
								
								value="{{$clid->clid}}" @if ($pad->clid==$clid->clid) selected @endif>{{$clid->clid}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group" id="video_categories" @if ($pad->type==-1 or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14) style="display: block;" @else style="display: none;" @endif>
					<label for="video_categories" class="col-xs-4 control-label">Категория видео</label>
					<div class="col-xs-8">
						<select name="video_categories" class="form-control">
							<option value="4">Не выбрано</option>
							<option @if ($pad->video_categories=='0') selected @endif value="0">Белая</option>
							<option @if ($pad->video_categories=='1') selected @endif value="1">Адалт</option>
							<option @if ($pad->video_categories=='2') selected @endif value="2">Развлекательная</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
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
@push('cabinet_home')
	<style>
		.moder_pad{
			border: 1px solid #cacaca;
			background-image: url(/images/cabinet/background_block.png);
			background-color: rgba(199, 199, 199, 0.5);
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
		}
		.stat_url{
			width: 34%;
			display: inline-block;
		}
		.stat_log, .stat_pas{
			width: 32%;
			display: inline-block;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
	$('#toggle_category').on("click", function() {
		//alert();
		for (i=0; i<$('.category_check').length; i++){
					$('.category_check')[i].checked=$(this).prop('checked');
				}
		
	});
	$('.category_check').on("click", function() { 
		 // alert($('#toggle_category').prop('checked'));
		  if(!$(this).prop('checked'))
					 $('#toggle_category').checked=false;

	});
	
		$('#tovar_type').on("click", function() {
			if($(this).is(":checked")){
				$('#id_categories').css('display', 'block');
			}
			else{
				$('#id_categories').css('display', 'none');
			}
		});
		$('#teaser_type').on("click", function() {
			if($(this).is(":checked")){
				$('#id_teaser').css('display', 'block');
			}
			else{
				$('#id_teaser').css('display', 'none');
			}
		});
		
		$('#video_type').on("click", function() {
			if($(this).is(":checked")){
				$('#video_categories').css('display', 'block');
			}
			else{
				$('#video_categories').css('display', 'none');
			}
		});
	</script>
@endpush
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