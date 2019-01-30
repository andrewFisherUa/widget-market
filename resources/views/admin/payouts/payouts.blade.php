@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
		@endif
		@if (Session::has('message_danger'))
			<div class="alert alert-danger">
				{{ session('message_danger') }}
			</div>
		@endif
		<div class="col-xs-12">
			<a href="{{route('payments.payouts.report')}}" class="btn btn-primary">По датам</a>
		</div>
		<div class="col-xs-6">
			<form class="form-inline" role="form" method="get">
				<div class="row">
					<div class="input-group col-xs-3 form-group">
						<select name="status" class="form-control">
							<option @if ($status=='all') selected @endif value="all">Все</option>
							<option @if ($status=='0') selected @endif value="0">Ожидающие</option>
							<option @if ($status=='1') selected @endif value="1">Оплаченные</option>
							<option @if ($status=='2') selected @endif value="2">Отказаные</option>
							<option @if ($status=='3') selected @endif value="3">Отмененные пользователем</option>
						</select>
					</div>
					<div class="input-group col-xs-3 form-group">
						<select name="cnt" class="form-control">
							<option @if ($cnt=='10') selected @endif value="10">10</option>
							<option @if ($cnt=='20') selected @endif value="20">20</option>
							<option @if ($cnt=='50') selected @endif value="50">50</option>
							<option @if ($cnt=='100') selected @endif value="100">100</option>
						</select>
					</div>
					<div class="col-xs-4 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
				</div>
			</form>
		</div>
		@if (\Auth::user()->hasRole('admin'))
		<div class="col-xs-6 text-right">
			<form id="payout_for_wmr" class="form-inline" role="form" action="{{route('payments.wmr_xml')}}" method="POST">
				{{ csrf_field() }}
				<div class="input-group col-xs-4 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-4 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="col-xs-2 input-group form-group">
					<button type="submit" class="btn btn-success">WebMoney XML</button>
				</div>
			</form>
		</div>
		@endif
	</div>
	
	<div class="row">
	
		{!! $payouts->appends(["status"=>$status,'order'=>$order, 'direct'=>$direct,'cnt'=>$cnt, 'from'=>$from, 'to'=>$to])->render() !!}
		<table class="table table-hover table-bordered" style="margin-top: 10px; table-layout: fixed">
			<colgroup>
				<col span="3" style="width: 95px;">
				<col span="1" style="width: 245px;">
				<col span="1" style="width: 150px;">
				<col span="1" style="width: 150px;">
				<col span="2" style="width: 90px;">
				<col span="2" style="width: 120px;">
			</colgroup>
			<thead>
				<tr class="text-center">
					@foreach($header as $k=>$row)
						<td>
							@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
						</td>
					@endforeach
					<td>ID, ФИО</td>
					<td colspan="2">Реквизиты</td>
					<td>Сумма</td>
					<td>Статус</td>
					<td colspan="2">Действие</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($payouts as $payout) 
					<tr class="tr_for_payout" @if ($payout->status==1) style="background: rgba(76, 255, 76, 0.25);" @elseif ($payout->status==2 or $payout->status==3) style="background: rgba(245, 100, 100, 0.25);" @endif>
						<td class="text-center">
						{{$payout->indicative_payment}}
						<br>
						@if ($payout->urgently==1)
							<span style="color: rgb(181, 0, 0); font-weight: bold">Срочная</span>
						@endif
						</td>
						
						
						<td class="text-center">{{$payout->time_payout}}</td>
						<td>{{$payout->exit_time_payout}}</td>
						<!--{{ $user=\App\UserProfile::where('user_id', $payout->user_id)->first() }}-->
						<td>
						{{$payout->id}}<br>
						<a href="{{route('admin.home', ['id_user'=>$user->user_id])}}" target="_blank">{{$user->name}}</a><br>
						@if ($payout->urgently==1)
							<span style="color: rgb(181, 0, 0); font-weight: bold">Срочная</span>
						@endif</td>
						<td>
						<select style="width:130px" class="user_pay_option" data-set="{{$payout->payout}}" data-vip="{{$user->vip}}" @if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager')) data-manager="1" @else data-manager="0" @endif data-urgently="{{$payout->urgently}}">
						@foreach ($pay_options as $pay_option)
							<option @if($payout->pay_option==$pay_option->id) selected @endif value="{{$pay_option->id}}">{{$pay_option->name}}</option>
						@endforeach
						</select>
						</td>
						<td style="word-wrap: break-word;">
						<select style="width:130px" class="user_pay_option_1" data-set="{{$payout->payout}}" data-vip="{{$user->vip}}" @if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager')) data-manager="1" @else data-manager="0" @endif data-urgently="{{$payout->urgently}}">
							@foreach ($pay_options as $pay_option)
								@if (\DB::table('users_payment_options')->where('user_id', $payout->user_id)->where('payment_id', $pay_option->id)->first())
									@if (\DB::table('users_payment_options')->where('user_id', $payout->user_id)->where('payment_id', $pay_option->id)->first()->value)
											<option @if($payout->pay_option==$pay_option->id) selected @endif value="{{$pay_option->id}}">{{$pay_option->name}}</option>
									@endif
								@endif
							@endforeach
						</select>
						@foreach ($pay_options as $pay_option)		
							@if (\DB::table('users_payment_options')->where('user_id', $payout->user_id)->where('payment_id', $pay_option->id)->first())
								@if (\DB::table('users_payment_options')->where('user_id', $payout->user_id)->where('payment_id', $pay_option->id)->first()->value)
									@if ($payout->pay_option==$pay_option->id)
									<b>
									@endif
									{{$pay_option->name}}:<br>{{\DB::table('users_payment_options')->where('user_id', $payout->user_id)->where('payment_id', $pay_option->id)->first()->value}}<br>
									@if ($payout->pay_option==$pay_option->id)
									</b>
									@endif
								@endif
							@endif
						@endforeach
						</td>
						<td>
						<span>Заказано: {{$payout->payout}} р.</span><br>
						<span style="color: #2700ff;; font-weight: bold;" class="oplata_for_user">Выплатить: 
						@if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager'))
							{{$payout->payout}}
						@elseif ($payout->urgently==1 and $payout->pay_option!=6 and $user->vip!=1)
							{{$payout->payout*0.94}}
						@elseif ($payout->urgently==1 and $payout->pay_option==6 and $user->vip!=1)
							{{$payout->payout*0.93}}
						@elseif ($payout->urgently==1 and $payout->pay_option!=6 and $user->vip==1)
							{{$payout->payout}}
						@elseif ($payout->urgently==1 and $payout->pay_option==6 and $user->vip==1)
							{{$payout->payout*0.99}}
						@elseif ($payout->urgently==1 and $payout->pay_option==6 and $user->vip!=1)
							{{$payout->payout*0.99}}
						
						@elseif ($payout->urgently!=1 and $payout->pay_option!=6)
							{{$payout->payout}}
						@elseif ($payout->urgently!=1 and $payout->pay_option==6)
							{{$payout->payout*0.99}}
						@endif
						р.
						</span>
						<br>
						<span>Остаток: {{$payout->balance}} р.</span></td>
						<td style="word-break: break-all;">
						@if ($payout->status==0)
							Ожидает
						@elseif ($payout->status==1)
							Оплачено
						@elseif ($payout->status==2)
							Отказано
						@elseif ($payout->status==3)
							Отменено пользователем
						@endif
						</td>
						<td>
						<form class="form-inline" role="form" method="post" action="{{route('payments.action_payouts')}}">
							{!! csrf_field() !!}
							<input type="text" name="id" value="{{$payout->id}}" style="display: none">
							<input type="text" name="status" value="2" style="display: none">
							<input type="text" class="pay_danger_option" name="pay_option" value="{{$payout->pay_option}}" style="display: none">
							<div class="col-xs-12 input-group form-group text-center">
								<button type="submit" class="btn btn-primary">Отказать</button>
							</div>
						</form>
						</td>
						<td>
						<form class="form-inline" role="form" method="post" action="{{route('payments.action_payouts')}}">
							{!! csrf_field() !!}
							<input type="text" name="id" value="{{$payout->id}}" style="display: none">
							<input type="text" name="status" value="1" style="display: none">
							<input type="text" class="pay_success_option" name="pay_option" value="{{$payout->pay_option}}" style="display: none">
							<input type="text" class="payout_fact_" name="payout_fact" hidden
						value="@if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager'))
							{{$payout->payout}}
						@elseif ($payout->urgently==1 and $payout->pay_option!=6 and $user->vip!=1)
							{{$payout->payout*0.94}}
						@elseif ($payout->urgently==1 and $payout->pay_option==6 and $user->vip!=1)
							{{$payout->payout*0.93}}
						@elseif ($payout->urgently==1 and $payout->pay_option!=6 and $user->vip==1)
							{{$payout->payout}}
						@elseif ($payout->urgently==1 and $payout->pay_option==6 and $user->vip==1)
							{{$payout->payout*0.99}}
						@elseif ($payout->urgently==1 and $payout->pay_option==6 and $user->vip!=1)
							{{$payout->payout*0.93}}
						
						@elseif ($payout->urgently!=1 and $payout->pay_option!=6)
							{{$payout->payout}}
						@elseif ($payout->urgently!=1 and $payout->pay_option==6)
							{{$payout->payout*0.99}}
						@endif"
						>	
							<div class="col-xs-12 input-group form-group text-center">
								<button type="submit" class="btn btn-primary">Оплатить</button>
							</div>
						</form>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		{!! $payouts->appends(["status"=>$status])->render() !!}
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')
	<script>
		$(document).ready(function() {
			$('.user_pay_option').change(function(){
				var parent=$(this).parents('.tr_for_payout');
				parent.find($('.pay_success_option')).val($(this).val());
				parent.find($('.pay_danger_option')).val($(this).val());
				
			});
			$('.user_pay_option_1').change(function(){
				var parent=$(this).parents('.tr_for_payout');
				var sum=$(this).data('set');
				var vip=$(this).data('vip');
				var manager=$(this).data('manager');
				var urgently=$(this).data('urgently');
				if (manager=='1'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum+' р.');
					parent.find($('.payout_fact_')).val(sum);
				}
				else if (urgently=='1' && $(this).val()!='6' && vip!='1'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum*0.94+' р.');
					parent.find($('.payout_fact_')).val(sum*0.94);
				}
				else if (urgently=='1' && $(this).val()=='6' && vip!='1'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum*0.93+' р.');
					parent.find($('.payout_fact_')).val(sum*0.93);
				}
				else if (urgently=='1' && $(this).val()!='6' && vip=='1'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum+' р.');
					parent.find($('.payout_fact_')).val(sum);
				}
				else if (urgently=='1' && $(this).val()=='6' && vip=='1'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum*0.99+' р.');
					parent.find($('.payout_fact_')).val(sum*0.99);
				}
				else if (urgently=='1' && $(this).val()!='6' && vip!='1'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum*0.99+' р.');
					parent.find($('.payout_fact_')).val(sum*0.99);
				}
				else if (urgently!='1' && $(this).val()!='6'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum+' р.');
					parent.find($('.payout_fact_')).val(sum);
				}
				else if (urgently!='1' && $(this).val()=='6'){
					parent.find($('.oplata_for_user')).html('Выплатить:<br>'+sum*0.99+' р.');
					parent.find($('.payout_fact_')).val(sum*0.99);
				}
			});
		});
	</script>
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
	
	<script>
		
$(function() {
    $('input[name="from"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
	$('input[name="to"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
});

	</script>
@endpush