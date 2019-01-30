@extends('layouts.app')
@push('cabinet_home')
<style>
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}

.img-tmp{
    width: 100%;
}
.img-upload{
    display: block;
    max-width: 100%;
	width: 40px;
    height: auto;
    margin-left: auto;
    margin-right: auto;
	vertical-align: middle;
}
</style>
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
	   <div class="row">
	   

	   
	   
	    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
        ----
        </div>
		
   	    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
		

<!------ Include the above in your HEAD tag ---------->
<div class="row">
<div class="col-md-3">
Заголовок
</div>
<div class="col-md-3">
Ссылка на страницу
</div>
<div class="col-md-3">
Краткое описание
</div>
<div class="col-md-3">
Картинка
</div>
</div>


<form method="POST" enctype="multipart/form-data">
   {!! csrf_field() !!}
   
   @if($collection)
	     @foreach($collection as $col)
<div class="row" style="position: relative;@if($col->status!=1)border:1px solid red;@endif">
<span class="btn btn-danger minus pull-right" style="position: absolute; width: 33px; height: 36px; left: -33px">–</span>
<div class="col-md-3">
    <div class="form-group">
	<input type="text" value="{{$col->title}}" class="form-control" name="title[]">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
	<input type="text" value="{{$col->src}}" class="form-control" name="link[]">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
	<input type="text" class="form-control" value="{{$col->descript}}" name="description[]">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group uploader_preview">
        <div class="col-md-3">
		<div class="input-group">
            <span class="input-group-btn">
                <span class="btn btn-default btn-file">
                    Browse… <input type="file" name="imgInp[]" class="imgInp">
                </span>
            </span>
        </div>
		 </div>
		<div class="col-md-9">
		<input name='status-old[]' type="hidden" value="{{$col->status}}"/>
		<input name='upld-old[]' type="hidden" value="{{$col->img}}"/>
        <img name='img-upload[]' class="img-upload" src="{{$col->img}}"/>
		 </div>
    </div>
</div>
</div>
@endforeach
@endif
<div class="information_json_plus text-center"></div>

						<div class="text-center">
							
						</div>
		
		<div class="form-group">
		@role(['admin','manager','super_manager'])
		<input type="checkbox" name="setstat"/> Допустить все
		@endrole
		<span class="btn btn-success plus">Добавить ссылку</span>
		                <button type="submit" class="btn btn-primary">
							Сохранить
						</button>
		</div>
</form>            
	    </div>

    </div>
</div>
@endsection

@push('cabinet_home_js')
<script>
$(document).ready( function() {
	
		    jQuery(document).on('click', '.minus', function(){
			    jQuery( this ).closest( 'div.row' ).remove(); // удаление строки с полями
		    });
			jQuery('.plus').click(function(){
			jQuery('.information_json_plus').before(
			'<div class="row">' + 
             '<span class="btn btn-danger minus pull-right" style="position: absolute; width: 33px; height: 36px; left: -33px">–</span>' +
			 '<div class="col-md-3">' +
             '<div class="form-group">' +
	         '<input type="text" class="form-control" name="title[]">' +
             '</div>' +
             '</div>' +
			 '<div class="col-md-3">' +
             '<div class="form-group">' +
	         '<input type="text" class="form-control" name="link[]">' +
             '</div>' +
             '</div>' +
			 '<div class="col-md-3">' +
             '<div class="form-group">' +
	         '<input type="text" class="form-control">' +
             '</div>' +
             '</div>' +
			 '<div class="col-md-3">' +
             '<div class="form-group uploader_preview">' +
             '<div class="col-md-3">' +
		     '<div class="input-group">' +
             '<span class="input-group-btn">' +
             '<span class="btn btn-default btn-file">' +
             ' Browse… <input type="file" name="imgInp[]" class="imgInp">' +
             '</span>' +
             '</span>' +
             '</div>' +
		     '</div>' +
		     '<div class="col-md-9">' +
             '<img name="img-upload[]" class="img-upload"/>' +
		     '</div>' +
             '</div>' +
             '</div>' +
			'</div>'
			);
			$(".imgInp").change(function(){
		    readURL(this);
		}); 	
		});
	
    	$(document).on('change', '.btn-file :file', function() {
			return;
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
		    
		    var input = $(this).parents('.input-group').find(':text'),
		        log = label;
		    
		    if( input.length ) {
		        input.val(log);
		    } else {
		        if( log ) alert(log);
		    }
	    
		});
		function readURL(input) {
			//alert($(input).closest('.uploader_preview').find('.img-upload').prop('name'));
			//return;
		    if (input.files && input.files[0]) {
		        var reader = new FileReader();
		        var that=$(input).closest('.uploader_preview').find('.img-upload');
		        reader.onload = function (e) {
				that.attr('src', e.target.result);
		            //$('#img-upload').attr('src', e.target.result);
		        }
		        
		        reader.readAsDataURL(input.files[0]);
		    }
		}

		$(".imgInp").change(function(){
		    readURL(this);
		}); 	
	});
</script>
@endpush