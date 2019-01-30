@extends('layouts.app')

@section('content')
<div class="container">

@if(isset($id_user) && $id_user)
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',['id_user'=>$id_user])}}" class="btn btn-success">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',['id_user'=>$id_user])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$id_user,'shop_id'=>0])}}" class="btn btn-primary">Статистика всех рекламных компаний</a>
<a href="{{ route('admin.disco',['id_user'=>$id_user])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@endif

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
        <form class="form-horizontal" method="post" action="{{ route('advertiser.save_company',[])}}">
			{{ csrf_field() }}
			<div class="panel-group">
			<input type="hidden" name="user_id" value="{{$userProf->user_id}}">
				<div class="form-group  @if ($errors->has('name')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Название компании</label>
					<div class="col-xs-6">
						<input type="text" name="name" value="{{old('name')}}" class="form-control" required>
						@if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif
					</div>
					        
				</div>
				<div class="form-group  @if ($errors->has('url_host')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Сайт</label>
					<div class="col-xs-6">
						<input type="text" name="url_host" value="{{old('url_host')}}" class="form-control" >
						@if ($errors->has('url_host'))
							<span class="help-block">
								<strong>{{ $errors->first('url_host') }}</strong>
							</span>
						@endif
					</div>
					        
				</div>
				<div class="form-group  @if ($errors->has('description')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Краткое описание</label>
					<div class="col-xs-6">
						 <textarea class="form-control" rows="5" id="description" style="resize: none" name="description" required>{{old('description')}}</textarea>
						 @if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('description') }}</strong>
							</span>
						@endif
					</div>
					       
				</div>
				<div class="form-group  @if ($errors->has('url')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Ссылка на прайс</label>
					<div class="col-xs-6">
						<input type="text" name="url" value="{{old('url')}}" class="form-control" required>
						@if ($errors->has('url'))
							<span class="help-block">
								<strong>{{ $errors->first('url') }}</strong>
							</span>
						@endif
					</div>
					            
				</div>
				<div class="form-group  @if ($errors->has('file_source')) has-error  @endif">
				<label for="file_source" class="col-xs-5 control-label">Закачать</label>
                <div class="col-xs-6">
				
                <div style="position:relative;">
                <a class='btn btn-primary' href='javascript:;'>
                Choose File...
                  <input type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="file_source" size="40"  onchange='$("#upload-file-info").html($(this).val());'>
                </a>
                 &nbsp;
                 <span class='label label-info' id="upload-file-info"></span>
                 </div>
					            @if ($errors->has('file_source'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('file_source') }}</strong>
                                    </span>
                                @endif
				</div>

				</div>
				
				<div class="form-group  @if ($errors->has('limit_clicks')) has-error  @endif">
					<label for="limit_clicks" class="col-xs-5 control-label">Максимальное количество переходов в день</label>
					<div class="col-xs-6">
						<input type="text" name="limit_clicks" value="{{old('limit_clicks')}}" class="form-control" value ="">
						@if ($errors->has('limit_clicks'))
							<span class="help-block">
								<strong>{{ $errors->first('limit_clicks') }}</strong>
							</span>
						@endif
					</div>
					            
				</div>
				 <div class="form-group">
                 <div class="row">
                 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"><b>Показывать аксессуары</b></div> 
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
							           <input type="checkbox" name ="use_accessories" >
   				            </div>
				      </div>				
				</div>			
				<div class="form-group">
					
						@widget('AdvertYandex',["id"=>0])

				</div>
				
				<div class="form-group">
					
						@widget('Advert\Raspisanie',[])

				</div>
				<div class="form-group">
					
						@widget('GeoRating',["noprice"=>1])

				</div>
		
				<div class="form-group">
					<button type="submit" class="btn btn-primary">
					Добавить
				</div>
			</button>
			</div>
		</form>
    </div>
</div>
@endsection
