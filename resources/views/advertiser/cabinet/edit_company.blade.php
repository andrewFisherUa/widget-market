@extends('layouts.app')
@push ('cabinet_home_top')
<style>

</style>
@endpush
@section('content')
<div class="container">
@if(isset($id_user))
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',['id_user'=>$id_user])}}" class="btn btn-success">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',['id_user'=>$id_user])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$id_user,'shop_id'=>0])}}" class="btn btn-primary">Статистика всех рекламных компаний пользователя</a>
<a href="{{ route('admin.disco',['id_user'=>$id_user])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@else	
  @if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
 <div class="row"><a class="btn btn-primary" href="{{route('advertiser.add_admin')}}">Список рекламных компаний</a></div>
 @endif
 
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
        <form class="form-horizontal" method="post" action="{{ route('advertiser.save_company',['id'=>$model->id])}}" enctype="multipart/form-data">
			{{ csrf_field() }}
			<div class="panel-group">
			<input type="hidden" name="user_id" value="{{$userProf->user_id}}">
				<div class="form-group  @if ($errors->has('name')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Название компании</label>
					<div class="col-xs-6">
						<input type="text" name="name" class="form-control" value ="{{$model->name}}">
					</div>
					            @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
				</div>
				<div class="form-group  @if ($errors->has('url_host')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Сайт</label>
					<div class="col-xs-6">
						<input type="text" name="url_host" class="form-control" value ="{{$model->url_host}}">
					</div>
					        @if ($errors->has('url_host'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url_host') }}</strong>
                                    </span>
                                @endif
				</div>
				<div class="form-group  @if ($errors->has('description')) has-error  @endif">
					<label for="type" class="col-xs-5 control-label">Краткое описание</label>
					<div class="col-xs-6">
						 <textarea class="form-control" rows="5" id="description" name="description">{{$model->description}}</textarea>
					</div>
					        @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
				</div>
			
				<div class="form-group  @if ($errors->has('url')) has-error  @endif">
					<label for="url" class="col-xs-5 control-label">Ссылка на прайс</label>
					<div class="col-xs-6">
						<input type="text" name="url" class="form-control" value ="{{$model->url}}">
					</div>
					            @if ($errors->has('url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                @endif
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
				
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
				<div class="form-group">
					<label for="type" class="col-xs-5 control-label">Тип прайса</label>
					<div class="col-xs-6">
						<select class="form-control" name="type_price">
							<option value="0" @if($model->type_price==0) selected @endif>Не выбран</option>
							@foreach($formats as $form)
							<option value="{{$form->id}}" @if($model->type_price==$form->id) selected @endif>{{$form->name}}</option>
							@endforeach
							
							
						</select>
					</div>
				</div>
				@endif
			    @if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
				<div class="form-group">
					<label for="type" class="col-xs-5 control-label">Статус компании</label>
					<div class="col-xs-6">
						<select class="form-control" name="status">
						@foreach($statuses as $st)
							<option value="{{$st->id}}" @if($model->status==$st->id) selected @endif>{{$st->name}}</option>
					    @endforeach
						</select>
					</div>
				</div>
				@else
				<div class="form-group">
					<label for="type" class="col-xs-5 control-label">Статус компании</label>
					
					
						@foreach($statuses as $st)
						<div class="col-xs-6">
						@if($model->status==$st->id) {{$st->name}} @endif
						</div>	
					    @endforeach

					
				</div>
				@endif
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
				
					<div class="form-group  @if ($errors->has('persent')) has-error  @endif">
					<label for="persent" class="col-xs-5 control-label">Процентное соотношение</label>
					<div class="col-xs-6">
						<input type="text" name="persent" class="form-control" value ="{{$model->persent}}">
					</div>
					            @if ($errors->has('persent'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('persent') }}</strong>
                                    </span>
                                @endif
				</div>
                @else
					<input type="hidden" name="persent" value ="{{$model->persent}}">
			    @endif
				<div class="form-group  @if ($errors->has('limit_clicks')) has-error  @endif">
					<label for="limit_clicks" class="col-xs-5 control-label">Максимальное количество переходов в день</label>
					<div class="col-xs-6">
						<input type="text" name="limit_clicks" class="form-control" value ="{{$model->limit_clicks}}">
					</div>
					            @if ($errors->has('limit_clicks'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('limit_clicks') }}</strong>
                                    </span>
                                @endif
				</div>
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<div class="form-group  @if ($errors->has('persent')) has-error  @endif">
					<label for="persent" class="col-xs-5 control-label">Статусы юзеров</label>
					<div class="col-xs-6">
						<span class="col-xs-2 text-center">
							<img src="/images/smail/green.png" style="width: 20px; height: 20px; top: 0px; cursor: pointer;"><br>
							<input type="checkbox" name="user_st[]"
							@foreach ($stat as $st)
								@if ($st==0 or $st==1) checked @endif
							@endforeach
							value="1" style="width: 20px; height: 20px; margin-left: 1px;">
						</span>
						<span class="col-xs-2 text-center">
							<img src="/images/smail/yellow.png" style="width: 20px; height: 20px; top: 0px; cursor: pointer;"><br>
							<input type="checkbox" name="user_st[]" 
							@foreach ($stat as $st)
								@if ($st==0 or $st==2) checked @endif
							@endforeach
							value="2" style="width: 20px; height: 20px; margin-left: 1px;">
						</span>
						<span class="col-xs-2 text-center">
							<img src="/images/smail/red.png" style="width: 20px; height: 20px; top: 0px; cursor: pointer;"><br>
							<input type="checkbox" name="user_st[]"
							@foreach ($stat as $st)
								@if ($st==0 or $st==3) checked @endif
							@endforeach
							value="3" style="width: 20px; height: 20px; margin-left: 1px;">
						</span>
					</div>
					            @if ($errors->has('persent'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('persent') }}</strong>
                                    </span>
                                @endif
				</div>
				@else
				<input type="checkbox" name="user_st[]" @foreach ($stat as $st) @if ($st==0 or $st==1) checked @endif @endforeach value="1" hidden>
				<input type="checkbox" name="user_st[]" @foreach ($stat as $st) @if ($st==0 or $st==2) checked @endif @endforeach value="2" hidden>
				<input type="checkbox" name="user_st[]" @foreach ($stat as $st) @if ($st==0 or $st==3) checked @endif @endforeach value="3" hidden>
				@endif
				<div class="form-group">
                 <div class="row">
                 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"><b>Показывать аксессуары</b></div> 
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
							           <input type="checkbox" name ="use_accessories" @if($model->use_accessories)checked @else @endif >
   				            </div>
				      </div>				
				</div>				
				<div class="form-group">
					
						@widget('AdvertYandex',["id"=>$model->id])

				</div>
				<div class="form-group">
					
						@widget('Advert\Raspisanie',[])

				</div>
					<h2>Укажите цену за переход</h2>			
@widget('GeoRating',["id"=>$model->id,"noprice"=>$noprice])	
				<div class="form-group">
					<button type="submit" class="btn btn-primary">
					Сохранить
				</div>
			</button>
			</div>
		</form>
    </div>
</div>
@endsection
