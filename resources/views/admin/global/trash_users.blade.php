@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row" style="margin: 10px 0">
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
			<form class="form-inline" role="form" method="get" action="">
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">С:</span>
					<input type="text" id="from_for_users" class="form-control" value="{{$from}}" name="from">
				</div>
				<div class="input-group col-xs-2 form-group">
					<span class="input-group-addon">По:</span>
					<input type="text" id="to_for_users" class="form-control" value="{{$to}}" name="to">
				</div>
				<div class="input-group col-xs-3 form-group">
					<span class="input-group-addon">Поиск:</span>
					<input type="text" class="form-control" value="{{$search}}" name="search">
				</div>
				@if (\Auth::user()->hasRole('admin'))
					<div class="input-group col-xs-2 form-group">
						<select name="manager_for_client" class="form-control">
							<option @if ($manager_for_client=='0') selected @endif value="0">Все менеджеры</option>
							<option @if ($manager_for_client=='no_manager') selected @endif value="no_manager">Без менеджера</option>
							<!--{{$all_managers=\App\User::whereHas('roles', function ($query) {
							$query->whereIn('id', ['3','4','5']);
							})->orderBy('name', 'asc')->get()}}-->
							@foreach ($all_managers as $manager)
								<option @if ($manager_for_client==$manager->id) selected @endif value="{{$manager->id}}">{{$manager->name}}</option>
							@endforeach
						</select>
					</div>
				@elseif (\Auth::user()->hasRole('super_manager'))
					<div class="input-group col-xs-2 form-group">
						<select name="manager_for_client" class="form-control">
							<option @if ($manager_for_client=='0') selected @endif value="0">Мои клиенты</option>
							<option @if ($manager_for_client=='no_manager') selected @endif value="no_manager">Без менеджера</option>
						</select>
					</div>
				@endif
					<div class="col-xs-2 input-group form-group">
						<button type="submit" class="btn btn-primary">Применить</button>
					</div>
			</form>
		</div>
	</div>
	<div class="row">
		{!! $allUsersActive->appends(["direct"=>$direct, "order"=>$order, "from"=>$from, "to"=>$to, "search"=>$search])->render() !!}
		<table class="table table-condensed table-hover widget-table" style="table-layout: fixed;">
			<thead>
				<colgroup>
					<col span="1" style="width: 41px">
					<col span="1" style="width: 270px">
					<col span="7" style="width: 90px">
					<col span="5" style="width: 32px">
				</colgroup>
				<tr style="border-bottom: 1px solid #8c8c8c;">
					<td></td>
					@foreach($header as $k=>$row)
						<td class="@if ($k!=0) text-center @endif" style="@if ($k==1) min-width: 90px; @endif">
							@if($row['index'])<a class="table_href" href="{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
						</td>
					@endforeach
					<td colspan='5'></td>
				</tr>
			</thead>
			<tbody>
				<tr style="background: #000; color: #fff">
					<td></td>
					<td>Всего</td>
					<td></td>
					<td class="text-center">{{$all_sum->loaded}}</td>
					<td class="text-center">{{$all_sum->calculate}}</td>
					<td class="text-center">{{$all_sum->clicks}}</td>
					<td class="text-center">{{$all_sum->util}}</td>
					<td class="text-center">{{$all_sum->ctr}}</td>
					<td class="text-center">{{$all_sum->summa}}</td>
					<td colspan='5'></td>
				</tr>
			</tbody>
			@foreach ($allUsersActive as $userActive)
				<tbody>
					<tr>
						<td>
							<a data-toggle="collapse" data-parent="#accordion" href="#us-{{$userActive->user_id}}">
								<span data-set="{{$userActive->user_id}}" class="glyphicon glyphicon-plus plus_us_bottom plus_all"></span>
							</a>
						</td>
						<td>
							<a href="{{route('admin.home', ['user_id'=>$userActive->user_id])}}" target="_blank" style="color: #636b6f;">{{$userActive->name}}</a>
						</td>
						<td></td>
						<td class="text-center">{{$userActive->loaded}}</td>
						<td class="text-center">{{$userActive->calculate}}</td>
						<td class="text-center">{{$userActive->clicks}}</td>
						<td class="text-center">{{$userActive->util}}</td>
						<td class="text-center">{{$userActive->ctr}}</td>
						<td class="text-center">{{$userActive->summa}}</td>
						<td colspan='5'>
							@if ($userActive->dop_status==1)
								<img src="/images/smail/green.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
							@elseif ($userActive->dop_status==2)
								<img src="/images/smail/yellow.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
							@elseif ($userActive->dop_status==3)
								<img src="/images/smail/red.png" data-toggle="tooltip" data-placement="bottom" title="{{$userActive->text_for_dop_status}}" style="width: 20px; height: 20px; display: inline-block; cursor: pointer; top: -4px; position: relative;">
							@endif
							<!-- {{$coms=\App\VideoDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
							<!-- {{$Productcoms=\App\ProductDefaultOnUser::where('user_id', $userActive->user_id)->get()}}-->
							@if (count($coms)>0 or count($Productcoms)>0)
								<span class="glyphicon glyphicon-exclamation-sign default_status" style="color: #ff6a00; font-size: 20px; top: 2px; cursor: pointer;"
									data-container="body" data-toggle="popover" tabindex="0" data-trigger="focus" data-placement="bottom" data-content="
									@foreach ($coms as $com)
										@if ($com->wid_type==1) Автоплей @elseif($com->wid_type==2) Оверлей @endif @if($com->pad_type==0) Белый @elseif ($com->pad_type==1) Адалт @else($com->pad_type==2) Развлек. @endif {{round($com->videoCommisssion($com->commission_rus),2)}} и {{round($com->videoCommisssion($com->commission_cis),2)}}<br>
									@endforeach
									@foreach ($Productcoms as $comm)
										Товарный @if ($comm->driver==1) (ТопАдверт) @elseif ($comm->driver==2) (Яндекс) @endif {{round($comm->commission,2)}}<br>
									@endforeach
									">
								</span>
							@endif
							<a href="{{route('admin.user_active', ['id_user'=>$userActive->user_id])}}" data-toggle="tooltip" data-placement="bottom" title="Отметить как активный клиент" style="float: right"><span class="glyphicon glyphicon-eye-open color-green"></span></a>
						</td>
					</tr>
				</tbody>
				<tbody id="us-{{$userActive->user_id}}" class="panel-collapse vlogen-tbody collapse">
				
				</tbody>
			@endforeach
		</table>
		@foreach ($allUsersActive as $userActive)
			@include('admin.cabinet.add_user_site')
			@include('admin.cabinet.add_user_widget')
			@include('admin.cabinet.add_user_dop_status')
			@if (\Auth::user()->hasRole('admin'))
				@include('admin.cabinet.add_video_default_on_users')
			@endif
		@endforeach
		{!! $allUsersActive->appends(["direct"=>$direct, "order"=>$order, "from"=>$from, "to"=>$to, "search"=>$search])->render() !!}
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<link href="{{ asset('css/news.css') }}" rel="stylesheet">
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	<style>
	.users_cabinet_block{
		height: 390px!important;
	}
	#usersActive, #usersNoActive{
		height: 350px;
		overflow: hidden;
	}
	one_user{
		height: 20px;
	}
	.users_name{
		float: left;
		margin-left: 10px;
		width: 200px;
		overflow: hidden;
		text-align: left;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: pre;
		text-overflow: ellipsis;
	.users_status{
		float: right;
		margin-right: 10px;
	}
	#notif_accordion{
	    height: 247px;
		overflow: hidden;
		margin: 0 15px;
	}
	notif_accordion .panel{
		margin-bottom: 13px;
	}
	.remove_notif{
		color: rgb(181, 0, 0);
	}
	.nav > li > a{
		padding: 4px 15px;
		border-radius: 0!important;
	}
	.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
		border-bottom:none!important;
	}
	#affiliate_all_widgets {
		height: 210px;
		overflow: hidden;
	}
	.affiliate_cabinet_bot{
	    border: 1px solid #cacaca;
		background-image: url(/images/cabinet/background_block.png);
		background-color: rgba(199, 199, 199, 0.5);
		box-shadow: 0 6px 12px rgba(0,0,0,.175);
		-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
		-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
	}
	.affiliate_cabinet_block > .heading {
		font-size: 11px;
		text-transform: uppercase;
		margin: 8px;
		padding: 0;
		height: 14px;
	}
	.plus_us_bottom{
	    font-size: 21px;
		margin: 0 5px;
	}
	.vlogen-tbody{
		background: rgba(255, 255, 255, 0.85);
	}
	.get_code:hover, .get_code:focus, .get_code:active{
	    outline: 0!important;
	}
	.default_status:focus, .default_status:active{
		outline: none!important;
	}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
			$('.default_status').popover({html : true});
		});
	</script>
	<script src="{{ asset('js/custom_scroll/jquery.custom-scroll.min.js') }}"></script>
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
	<script src="https://cdn.rawgit.com/zenorocha/clipboard.js/master/dist/clipboard.min.js"></script>
	<script>
		new Clipboard('.copy-all');
	</script>
	<script>
	$('#affiliate_all_pads').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -32,
  vertical: true,
  horizontal: false
});
$('#affiliate_all_widgets').customScroll({
  offsetTop: 78,
  offsetRight: 16,
  offsetBottom: -78,
  vertical: true,
  horizontal: false
});
	$('#cabinet_news').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
$('#usersActive').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
$('#usersNoActive').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
$('#notif_accordion').customScroll({
  offsetTop: 32,
  offsetRight: 16,
  offsetBottom: -42,
  vertical: true,
  horizontal: false
});
	</script>
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
$(document).ready(function() {
	$('.pad_for_widget').change(function(){
		var user_parent=$(this).parents('.user_add_widget');
		if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
		user_parent.find($('.type_for_widget')).css('display', 'block');
		user_parent.find($('.pod_type_for_widget')).html(
		'<option value="1">Товарка</option>'
		);
		user_parent.find($('.save_for_widget')).html(
		'<button type="submit" class="btn btn-primary">Сохранить</button>'
		);
		}
		else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
		user_parent.find($('.type_for_widget')).css('display', 'block');
		user_parent.find($('.pod_type_for_widget')).html(
		'<option value="2">Видео</option>'
		);
		user_parent.find($('.type_for_video')).css('display', 'block');
			user_parent.find($('.type_for_video_select')).html(
			'<option value="1">Автоплей</option>' +
			'<option value="2">Оверлей</option>'+
			'<option value="3">Васт ссылка</option>'
			);
		user_parent.find($('.save_for_widget')).html(
		'<button type="submit" class="btn btn-primary">Сохранить</button>'
		);
		}
		else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
		user_parent.find($('.type_for_widget')).css('display', 'block');
		user_parent.find($('.pod_type_for_widget')).html(
		'<option value="1">Товарка</option>' +
		'<option value="2">Видео</option>'+
		'<option value="3">Васт ссылка</option>'
		);
		user_parent.find($('.save_for_widget')).html(
		'<button type="submit" class="btn btn-primary">Сохранить</button>'
		);
		}
		else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
			user_parent.find($('.type_for_widget')).css('display', 'none');
			user_parent.find($('.pod_type_for_widget')).html('');
			user_parent.find($('.type_for_video')).css('display', 'none');
			user_parent.find($('.save_for_widget')).html(' ');
		}
	});
	$('.pod_type_for_widget').change(function(){
		var user_parent=$(this).parents('.user_add_widget');
		if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='1'){
			user_parent.find($('.type_for_video')).css('display', 'none');
			user_parent.find($('.type_for_video_select')).html('');
		}
		else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='2'){
			user_parent.find($('.type_for_video')).css('display', 'block');
			user_parent.find($('.type_for_video_select')).html(
			'<option value="1">Автоплей</option>' +
			'<option value="2">Оверлей</option>'+
			'<option value="3">Васт ссылка</option>'
			);
		}
	});
});
$(document).ready(function() {
	$('.type_for_commission').change(function(){
		var user_parent=$(this).parents('.default_commission');
		if (user_parent.find($('.type_for_commission option:selected')).val()>'0' && user_parent.find($('.type_for_commission option:selected')).val()<'7'){
			user_parent.find($('.video_commission_rus')).css('display', 'block');
			user_parent.find($('.video_commission_cis')).css('display', 'block');
			user_parent.find($('.product_commission')).css('display', 'none');
			user_parent.find($('.commission_save')).css('display', 'block');
		}
		if (user_parent.find($('.type_for_commission option:selected')).val()=='0'){
			user_parent.find($('.video_commission_rus')).css('display', 'none');
			user_parent.find($('.video_commission_cis')).css('display', 'none');
			user_parent.find($('.product_commission')).css('display', 'none');
			user_parent.find($('.commission_save')).css('display', 'none');
		}
		if (user_parent.find($('.type_for_commission option:selected')).val()>'6'){
			user_parent.find($('.video_commission_rus')).css('display', 'none');
			user_parent.find($('.video_commission_cis')).css('display', 'none');
			user_parent.find($('.product_commission')).css('display', 'block');
			user_parent.find($('.commission_save')).css('display', 'block');
		}
	});
});
</script>
<script type="text/javascript">
    $(function() {
        $(document).on('click', '.plus_all', function(event) {
            var id=$(this).data('set');
			event.preventDefault();
			if ($('#us-'+id).hasClass('in')){
				$('#us-'+id).html('');
			}
			else{
				$.post('/user_detail_widgets/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
						$('#us-'+response.id).html(response.view);
				});
			}
        });
	});
</script>
@endpush