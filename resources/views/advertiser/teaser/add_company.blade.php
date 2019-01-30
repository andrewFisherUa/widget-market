@extends('layouts.app')

@section('content')
<div class="container">
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
        <form class="form-horizontal" method="post" action="{{route('advertiser.save_company_teaser')}}">
			{{ csrf_field() }}
			<input type="text" hidden name="user_id" value="{{$user->id}}">
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Название компании</label>
				<div class="col-xs-9">
					<input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Категории сайтов для показа</label>
				<div class="col-xs-9">
					@foreach ($categories as $cat)
						<div class="col-xs-4">
						<input type="checkbox" class="categories" 
						@if (old('categories'))
							@foreach (old('categories') as $categor)
								@if ($categor==$cat->id) checked @endif
							@endforeach
						@endif
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
						@if (old('browser'))
							@foreach (old('browser') as $br)
								@if ($br==$browser->id) checked @endif
							@endforeach
						@endif
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
						<input type="radio" name="sex" @if (old('sex')==1) checked @endif  value="1"> Мужской
					</div>
					<div class="col-xs-3">
						<input type="radio" name="sex" @if (old('sex')==2) checked @endif value="2"> Женский
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Лимиты компаний</label>
				<div class="col-xs-9">
					<div class="col-xs-3">
						<select class="form-control" name="kompany_limit_set">
							<option @if (old('kompany_limit_set')==0) selected @endif value="0">По кликам</option>
							<option @if (old('kompany_limit_set')==1) selected @endif value="1">По бюджету</option>
						</select>
					</div>
					<div class="col-xs-3">
						<input class="form-control" name="kompany_limit_value" type="nubmer" value="{{old('kompany_limit_value')}}">
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
						<input class="form-control" type="number" value="{{old('offer_limit_click')}}" name="offer_limit_click">
					</div>
					<div class="col-xs-3">
					За количество времени
					</div>
					<div class="col-xs-3">
						<select class="form-control" name="offer_limit_time">
							<option @if (old('offer_limit_time')==3600) selected @endif value="3600">1 час</option>
							<option @if (old('offer_limit_time')==7200) selected @endif value="7200">2 часа</option>
							<option @if (old('offer_limit_time')==10800) selected @endif value="10800">3 часа</option>
							<option @if (old('offer_limit_time')==21600) selected @endif value="21600">6 часов</option>
							<option @if (old('offer_limit_time')==43200) selected @endif value="43200">12 часов</option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Настройка гео</label>
				<div class="col-xs-9">
					@widget('GeoRatingTeaser',[])
				</div>
			</div>
			<div class="form-group">
			@widget('Advert\Raspisanie',[])
			{{--
				<label for="type" class="col-xs-3 control-label">Расписание</label>
				<div class="col-xs-9">
					<input type="checkbox" @if (old('shedule_status')==1) checked @endif value="1" name="shedule_status"> Запуск по расписанию (UTC+03:00)<br>
					<table class="table table-bordered shedule">
						<thead>
							<tr>
								<td class="table_check_all">Выбрать все</td>
								@for ($i=0; $i<24; $i++)
									<td class="time_td" data-set="{{$i}}">@if ($i<10) 0{{$i}} @else {{$i}} @endif</td>
								@endfor
							</tr>
						</thead>
						<tbody>
							<tr class="tr_day_1">
								<td class="td_day" data-set='1'>Понедельник</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==1)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[1][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>							
							<tr class="tr_day_2">
								<td class="td_day" data-set='2'>Вторник</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==2)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[2][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>
							<tr class="tr_day_3">
								<td class="td_day" data-set='3'>Среда</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==3)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[3][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>
							<tr class="tr_day_4">
								<td class="td_day" data-set='4'>Четверг</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==4)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[4][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>
							<tr class="tr_day_5">
								<td class="td_day" data-set='5'>Пятница</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==5)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[5][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>
							<tr class="tr_day_6">
								<td class="td_day" data-set='6'>Суббота</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==6)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[6][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>
							<tr class="tr_day_0">
								<td class="td_day" data-set='0'>Воскресение</td>
								@for ($i=0; $i<24; $i++)
									<td style="padding: 0;">
										<label style="margin: 0;">
											<input data-val="{{$i}}" 
											@if (old('shedule'))
												@foreach(old('shedule') as $key=>$shed)
													@if ($key==0)
														@foreach ($shed as $k=>$s)
															@if ($k==$i) checked @endif
														@endforeach
													@endif
												@endforeach
											@endif
											type="checkbox" name="shedule[0][{{$i}}]">
											<span></span>
										</label>
									</td>
								@endfor
							</tr>
						</tbody>
					</table>
				</div>
			--}}
			</div>
			<div class="form-group">
				<label for="type" class="col-xs-3 control-label">Цена клика перехода</label>
				<div class="col-xs-3">
				<input type="text"  @if (old('summa')) data-summa="{{old('summa')}}" value="{{old('summa')}}" @else data-summa="1.7" value="1.7"  @endif id="summa" name="summa" class="form-control" required>
				{{--<input type="number" step="0.01" @if (old('summa')) data-summa="{{old('summa')}}" value="{{old('summa')}}" @else data-summa="1.7" value="1.7"  @endif id="summa" name="summa" class="form-control" required>--}}
					<span class="help-block" style="margin: 0; color: rgb(181, 0, 0);">
						<strong class="help-strong">Минимальная цена клика 1.7 руб.</strong>
					</span>
				</div>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary">
					Добавить
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
		
		/*
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
		$('[name="summa"]').on("keyup", function(){
			var v=$(this).val();
			v=vreplace('.',',');
			$(this).val(v);
        });
		
		$('#summa').on("change", function(){
			if ($(this).val()<$(this).prop('min')){
				$(this).val($(this).prop('min'));
			}
		});
	});
	</script>
@endpush