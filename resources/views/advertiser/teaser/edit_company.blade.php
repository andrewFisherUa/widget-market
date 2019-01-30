@extends('layouts.app')

@section('content')
<div class="container">
@if(isset($id_user) && $id_user)
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.home',['id_user'=>$id_user])}}" class="btn btn-success">Страница пользователя</a>
<a href="{{ route('admin.invoices_history',['id_user'=>$id_user])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$id_user,'shop_id'=>0])}}" class="btn btn-primary">Статистика магазинов пользователя</a>
<a href="{{ route('admin.disco',['id_user'=>$id_user])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@endif

    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{!! session('message_success') !!}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{!! session('message_danger') !!}
		</div>
	@endif
        <form class="form-horizontal" method="post" action="{{route('advertiser.save_company_teaser', ['id'=>$model->id])}}">
			{{ csrf_field() }}
			<input type="text" hidden name="user_id" value="{{$user->id}}">
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Название компании</label>
				<div class="col-xs-9">
					<input type="text" name="name" value="{{$model->name}}" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Категории сайтов для показа</label>
				<div class="col-xs-9">
					@foreach ($categories as $cat)
						<div class="col-xs-4">
						<input type="checkbox" class="categories" 
							@foreach ($cats as $categor)
								@if ($categor==$cat->id or $categor==0) checked @endif
							@endforeach
						name="categories[]" data-categories="{{$cat->id}}" value="{{$cat->id}}"> {{$cat->name}}
						</div>
					@endforeach
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Браузеры</label>
				<div class="col-xs-9">
					@foreach ($browsers as $browser)
						<div class="col-xs-3">
						<input type="checkbox" 
							@foreach ($brows as $br)
								@if ($br==$browser->id or $br==0) checked @endif
							@endforeach
						name="browser[]" value="{{$browser->id}}"> {{$browser->name}}
						</div>
					@endforeach
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Пол</label>
				<div class="col-xs-9">
					<div class="col-xs-3">
						<input type="radio" name="sex" checked value="0"> Все
					</div>
					<div class="col-xs-3">
						<input type="radio" name="sex" @if ($model->gender==1) checked @endif  value="1"> Мужской
					</div>
					<div class="col-xs-3">
						<input type="radio" name="sex" @if ($model->gender==2) checked @endif value="2"> Женский
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Лимиты компаний</label>
				<div class="col-xs-9">
					<div class="col-xs-3">
						<select class="form-control" name="kompany_limit_set">
							<option @if ($model->kompany_limit_set==0) selected @endif value="0">По кликам</option>
							<option @if ($model->kompany_limit_set==1) selected @endif value="1">По бюджету</option>
						</select>
					</div>
					<div class="col-xs-3">
						<input class="form-control" name="kompany_limit_value" type="nubmer" value="{{$model->kompany_limit_value}}">
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Лимиты объявлений</label>
				<div class="col-xs-9">
					<div class="col-xs-3">
					Уникальных кликов
					</div>
					<div class="col-xs-3">
						<input class="form-control" type="number" value="{{$model->offer_limit_click}}" name="offer_limit_click">
					</div>
					<div class="col-xs-3">
					За количество времени
					</div>
					<div class="col-xs-3">
						<select class="form-control" name="offer_limit_time">
							<option @if ($model->offer_limit_time==3600) selected @endif value="3600">1 час</option>
							<option @if ($model->offer_limit_time==7200) selected @endif value="7200">2 часа</option>
							<option @if ($model->offer_limit_time==10800) selected @endif value="10800">3 часа</option>
							<option @if ($model->offer_limit_time==21600) selected @endif value="21600">6 часов</option>
							<option @if ($model->offer_limit_time==43200) selected @endif value="43200">12 часов</option>
						</select>
					</div>
				</div>
			</div>			
			 @if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
				<div class="form-group">
					<label for="type" class="col-xs-3 control-label">Статус компании</label>
					<div class="col-xs-9">
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
				
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Настройка гео</label>
				<div class="col-xs-9">
					@widget('GeoRatingTeaser',['id'=>$model->id])
				</div>
			</div>
			<div class="form-group">
			@widget('Advert\Raspisanie',[])
			</div>
			@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
					<div class="form-group  @if ($errors->has('persent')) has-error  @endif">
					<label for="persent" class="col-xs-3 control-label">Статусы юзеров</label>
					<div class="col-xs-9">
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
				<label for="type" class="col-xs-3 control-label">Цена клика перехода</label>
				<div class="col-xs-3">
				<input type="text"  @if ($model->myteaser_perclick) data-summa="{{$model->myteaser_perclick}}" value="{{$model->myteaser_perclick}}" @else data-summa="1.7" value="1.7"  @endif id="summa" name="summa" class="form-control" required>
					{{--<input type="number"  step="0.01" @if ($model->myteaser_perclick) data-summa="{{$model->myteaser_perclick}}" value="{{$model->myteaser_perclick}}" @else data-summa="1.7" value="1.7"  @endif id="summa" name="summa" class="form-control" required>--}}
					<span class="help-block" style="margin: 0; color: rgb(181, 0, 0);">
						<strong class="help-strong">Минимальная цена клика 1.7 руб.</strong>
					</span>
				</div>
			</div>
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
			<div class="form-group">
				<button type="submit" class="btn btn-primary">
					Сохранить изменения
				</button>
			</div>
		</form>
		
    </div>
</div>
@endsection
@push ('cabinet_home_top')
 <style>
   label {
    height: 26px;
    display: block;
    position: relative;
   }
   input[type="checkbox"] + span {
    position: absolute;
    left: 0; top: 0;
    width: 100%;
    height: 100%;
    background: #f5f8fa;
    cursor: pointer;
   }
   input[type="checkbox"]:checked + span {
    background: rgb(59, 67, 113); 
   }
   /*
   .table_check_all, .td_day, .time_td{
		cursor: pointer;
   }
   .td_day{
		padding: 0 5px!important;
   }
    .table_check_all{
		font-size: 12px;
		font-weight: bold;
	}
	*/
  </style>
@endpush
@push ('cabinet_home_js')
	<script>
	$(document).ready(function(){
		var dat=$('[name="summa"]').data('summa');
		var min=1.7;
		var categories=0.36;
		var geo=0.3;
		var summa_categories=0;
		var summa_geo=0;
		if ($('[data-categories=5]').prop('checked') || $('[data-categories=9]').prop('checked') || $('[data-categories=15]').prop('checked')){
			summa_categories=min*categories;
		}
		if ($("[data-geo=1]").prop('checked') || $("[data-geo=2]").prop('checked') || $('#Tree1').find('input').prop('checked')){
			summa_geo=min*geo;
		}
		
		var itogo=min+summa_categories+summa_geo;
		
		
		if (itogo<dat){
			$('[name="summa"]').val(dat);
		}
		else{
			$('[name="summa"]').val(itogo);
		}
		$('[name="summa"]').prop('min', itogo);
		$('.help-strong').html('Минимальная цена за переход составляет: '+itogo)
		$('.td_day').on("click", function(){
			var set=$(this).data('set');
			var checkbox=$('.tr_day_'+set).find('input');
			if (checkbox.prop('checked')){
				checkbox.prop('checked', false);
			}
			else{
				checkbox.prop('checked', true);
			}
		});
		/*
		$('.time_td').on("click", function(){
			var set=$(this).data('set');
			var checkbox=$('.shedule').find('[data-val='+set+']');
			if (checkbox.prop('checked')){
				checkbox.prop('checked', false);
			}
			else{
				checkbox.prop('checked', true);
			}
		})
		$('.table_check_all').on("click", function(){
			if ($('.shedule').find('input').prop('checked')){
				$(this).html('Выбрать все');
				$('.shedule').find('input').prop('checked', false);
			}
			else{
				$(this).html('Отменить все');
				$('.shedule').find('input').prop('checked', true);
			}
		});
		*/
		$('.categories').on("click", function(){
			if ($('[data-categories=5]').prop('checked') || $('[data-categories=9]').prop('checked') || $('[data-categories=15]').prop('checked')){
				summa_categories=min*categories;
			}
			else{
				summa_categories=0;
			}
			if ($("[data-geo=1]").prop('checked') || $("[data-geo=2]").prop('checked') || $('#Tree1').find('input').prop('checked')){
				summa_geo=min*geo;
			}
			else{
				summa_geo=0;
			}
			var itogo=min+summa_categories+summa_geo;
			if ($('[name="summa"]').val()<itogo){
			$('[name="summa"]').val(itogo);
			}
			$('[name="summa"]').prop('min', itogo);
			$('.help-strong').html('Минимальная цена за переход составляет: '+itogo);
		});
		$('#Tree1').find('input').on("click", function(){
			
			if ($("[data-geo=1]").prop('checked') || $("[data-geo=2]").prop('checked') || $('#Tree1').find('input').prop('checked')){
				summa_geo=min*geo;
			}
			else{
				summa_geo=0;
			}
			if ($('[data-categories=5]').prop('checked') || $('[data-categories=9]').prop('checked') || $('[data-categories=15]').prop('checked')){
				summa_categories=min*categories;
			}
			else{
				summa_categories=0;
			}
			var itogo=min+summa_categories+summa_geo;
			if ($('[name="summa"]').val()<itogo){
				$('[name="summa"]').val(itogo);
			}
			$('[name="summa"]').prop('min', itogo);
			$('.help-strong').html('Минимальная цена за переход составляет: '+itogo);
		});
		$('[name="summa"]').on("keydown", function(e){
			//var v=$(this).get(0).value;
			//var v=$(this).val();
			//console.log(['1---',v]);
			//v= v.replace('.',',');
			
			//$(this).val(v); 
        });
		
		$('#summa').on("change", function(){
			//var v=$(this).get(0).value;
			//console.log(['1---',v]);
			if ($(this).val()<$(this).prop('min')){
				$(this).val($(this).prop('min'));
			}
		});
		
	});
	</script>
@endpush