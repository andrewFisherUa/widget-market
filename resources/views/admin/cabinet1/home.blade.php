@extends('layouts.app1')

@section('content')
<div id="user_id" hidden>{{$user->id}}</div>
<div class="container">
	<div class="row">
		<div id="home_message">
			
		</div>
	</div>
    <div class="row">
		<!-- профиль -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block text-center">
				<div class="heading text-left">Мой профиль</div>
				<hr class="affilaite_hr">
				<div id="home_profile" class="home_block">
					<div class="loaded">
					
					</div>
				</div>
			</div>
		</div>
		<!-- Баланс -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мой баланс 
				@if ($user->hasRole('manager') or $user->hasRole('super_manager')) 
					<a href="{{route('managers.history', ['id'=>$user->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Детальная статистика" class="glyphicon glyphicon-question-sign color-blue manager_help" target="_blank"></a>
				@endif
				</div>
				<hr class="affilaite_hr">
				<div id="home_balance" class="home_block">
					<div class="loaded">
					
					</div>
				</div>			
			</div>
		</div>
		<!-- Уведомления -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Мои уведомления</div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
				<div id="home_notif" class="home_block">
					<div class="loaded">
					
					</div>
				</div>	
			</div>
		</div>
		<!-- Новости -->
		<div class="col-xs-3">
			<div class="affiliate_cabinet_block">
				<div class="heading text-left">Последние новости </div>
				<hr class="affilaite_hr" style="margin-bottom: 10px;">
				<div id="home_news" class="home_block">
					<div class="loaded">
					
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>

@endsection
@push('cabinet_home_top')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/news.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	<style>
		.home_block{
			height: 247px;
		}
		.loaded{
			height: 100%;
			width: 100%;
			background: url('/images/100x100_spinner.gif');
			background: url(/images/100x100_spinner.gif) no-repeat;
			background-size: 100px 100px;
			background-position: center center;
		}
	</style>
	<style>
		.manager_help{
			float: right;
			cursor: pointer;
			font-size: 21px;
			line-height: 14px;
			transition: 0.2s;
		}
		.manager_help:focus, .manager_help:active, .manager_help:hover{
			outline: none;
			 text-decoration: none;
		}
	</style>
	<style>
		#notif_accordion{
		    height: 247px;
			overflow: hidden;
			margin: 0 15px;
		}
		#notif_accordion .panel{
			margin-bottom: 13px;
		}
		.remove_notif{
			color: rgb(181, 0, 0);
			cursor: pointer;
		}
	</style>
@endpush
@push('cabinet_home_bottom')
<script type="text/javascript">
   $(document).ready(function() {
		
		function profile(){
			$.post('/home_profile',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html()}, function(response) {
				$('#home_profile').html(response.view);
				$('[data-toggle="popover"]').popover({html:true});
				$('[data-toggle="tooltip"]').tooltip();
			});
		};
		profile();
		
		function balance(){
			$.post('/home_balance',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html() }, function(response) {
				$('#home_balance').html(response.view);
				$('[data-toggle="popover"]').popover({html:true});
				$('[data-toggle="tooltip"]').tooltip();
				$('#auto_payment').on('click', '#auto_payment_submit', function(event) {
					event.preventDefault();
					if ($('#auto_payment').find('input[name=urgently]').is(":checked")){
						var urgently=1;
					}
					else{
						var urgently=0;
					}
					if ($('#auto_payment').find('input[name=auto_pay]').is(":checked")){
						var auto_pay=1;
					}
					else{
						var auto_pay=0;
					}
					$.post('/test_user_payout_auto',{ _token: $('meta[name=csrf-token]').attr('content'), 
					user_id: $('#auto_payment').find('input[name=user_id]').val(),
					urgently: urgently,
					auto_pay: auto_pay,
					day: $('#auto_payment').find('select[name=day]').val(),
					pay_option: $('#auto_payment').find('select[name=pay_option]').val()
					}, function(response) {
						$(".modal").modal("hide");
						$('body').removeClass('modal-open'); 
						$('.modal-backdrop').remove();
						if (response.ok){
							$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), text: response.message }, function(response) {
								$('#home_message').html(response.view);
							});
							balance();
						}
						else{
							$('#home_balance').html("<div class='no_manager text-center'>Возникла ошибка, пожалуйста обновите страницу.</duiv>");
						}
					});
				});
				$('#payment').on('click', '#payment_submit', function(event) {
					event.preventDefault();
					console.log(123);
				});
			});
		};
		balance();
	});
</script>
@endpush