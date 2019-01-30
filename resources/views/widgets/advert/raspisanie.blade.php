			<div class="form-group" id="realnoe_raspisanie">
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
											@if ($wsh)
												@foreach($wsh as $key=>$shed)
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
			</div>
@push ('cabinet_home_top')
 <style>
   #realnoe_raspisanie label {
    height: 26px;
    display: block;
    position: relative;
   }
   #realnoe_raspisanie input[type="checkbox"] + span {
    position: absolute;
    left: 0; top: 0;
    width: 100%;
    height: 100%;
    background: #f5f8fa;
    cursor: pointer;
   }
   #realnoe_raspisanie input[type="checkbox"]:checked + span {
    background: rgb(59, 67, 113); 
   }
   #realnoe_raspisanie .table_check_all, .td_day, .time_td{
		cursor: pointer;
   }
   #realnoe_raspisanie .td_day{
		padding: 0 5px!important;
   }
   #realnoe_raspisanie .table_check_all{
		font-size: 12px;
		font-weight: bold;
	}
  </style>
@endpush
@push ('cabinet_home_js')
	<script>
	
	$(document).ready(function(){

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
	});
	
	</script>
@endpush			