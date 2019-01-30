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
		<div class="row">
			<div class="col-xs-3">
				<div class="affiliate_cabinet_block text-center">
					<div class="heading text-left">Мой профиль</div>
					<hr class="affilaite_hr">
					<div class="home_avatar_block">
						@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
							@if (strtotime(date('Y-m-d h:i:s')) < strtotime($user->updated_at)+900)
								<span data-toggle="tooltip" data-placement="bottom" title="В сети" class="home_avatar_green"></span>
							@else
								<span data-toggle="tooltip" data-placement="bottom" title="Был в сети {{$user->updated_at}}" class="home_avatar_red"></span>
							@endif
						@endif
					@if ($user->profile->avatar)
						<img src="/images/avatars/{{$user->profile->avatar}}" class="img-circle cabinet_avatar">
					@else
						<img src="/images/cabinet/no_foto.png" class="img-circle cabinet_avatar">
					@endif
					</div>
					<div class="affiliate_name">{{$user->name}}</div>
					<div class="affiliate_role">@if ($user->hasRole('affiliate'))Вебмастер@elseif ($user->hasRole('advertiser'))Рекламодатель@elseif($user->hasRole('manager'))Менеджер@elseif($user->hasRole('super_manager'))Ст. менеджер@elseif($user->hasRole('admin'))Администратор@endif</div>
					<div class="affiliate_email">{{$user->email}}</div>
					<div class="cabinet_gliph">
						<a href="" data-toggle="tooltip" data-placement="bottom" title="Редактировать профиль"><span class="glyphicon glyphicon-user gliph_affiliate"></span></a>
						<a href="" data-toggle="tooltip" data-placement="bottom" title="Справка"><span class="glyphicon glyphicon-question-sign gliph_affiliate"></span></a>
						<a href="{{ route('news.all') }}" data-toggle="tooltip" data-placement="bottom" @if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))) title="Не прочитаных новостей: {{count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))}}" @else title="Новости" @endif class="home_news"><span class="glyphicon glyphicon-envelope gliph_affiliate"></span>@if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))) @if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))>9)<span class="count_news">9+</span> @else <span class="count_news">{{count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))}}</span> @endif @endif</a>
						<a href="{{ route('logout') }}" data-toggle="tooltip" data-placement="bottom" title="Выйти" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-log-out gliph_affiliate"></span></a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								{{ csrf_field() }}
							</form>
					</div>
				</div>
			</div>
			<div class="col-xs-3">
				<div class="affiliate_cabinet_block">
					<div class="heading text-left">Мой баланс</div>
					<hr class="affilaite_hr">
					<div class="affiliate_balance text-center">@if ($user->Profile->balance==0)0.00 @else{{$user->Profile->balance}}@endif <span class="rur">q</span></div>
					<p class="text-center"><a href="#" data-toggle="modal" data-target="#advertiser_payout" class="btn btn-primary" role="button">Пополнить баланс</a></p>
					<div class="affiliate_detal_balance"><span>Сегодня:</span><div class="right">0.00 <span class="rur">q</span></div></div>
					<div class="affiliate_detal_balance"><span>Вчера:</span><div class="right green">0.00 <span class="rur">q</span></div></div>
					<div class="affiliate_detal_balance"><span>Неделя:</span><div class="right green">0.00 <span class="rur">q</span></div></div>
					<div class="affiliate_detal_balance"><span>Месяц:</span><div class="right green">0.00 <span class="rur">q</span></div></div>			
					@include('advertiser.payouts.modal_payout')
				</div>
			</div>
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
			<div class="col-xs-3">
				<div class="affiliate_cabinet_block">
					<div class="heading text-left">Последние новости @if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager')) <a href="{{ route('news.add') }}" class="affiliate_add_domain" target="_blank"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Добавить новость"></span></a> @endif</div>
					<hr class="affilaite_hr" style="margin-bottom: 10px;">
					<div id="home_news" class="home_block">
						<div class="loaded">

						</div>
					</div>	
				</div>
			</div>
		</div>
		
		<div class="row" style="margin-top: 30px;">
			<div class="col-xs-12">
				<div class="affiliate_cabinet_block" style="height: auto; min-height: 300px;">
					<div class="heading text-left">Мои компании <a href="{{route('advertiser.create_company')}}" class="affiliate_add_domain" target="_blank"><span class="glyphicon glyphicon-plus-sign" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Добавить компанию"></span></a></div>
					<hr class="affilaite_hr" style="margin-bottom: 10px;">
					<div class="home_block">
						@widget('UserCompanies',["user"=>$user])
						<!--@widget('UserCompaniesTeaser',["user"=>$user])-->
					</div>	
				</div>
			</div>
		</div>
    </div>
</div>

@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')
	<script>
		$(document).ready(function() {
			$('#payout_sistem').change(function(){
				if ($('#payout_sistem option:selected').val()==1 || $('#payout_sistem option:selected').val()==2){
					$('#submit_payout').html(
						 '<button class="btn btn-primary">Далее</button>'
					);
				}
				else{
					$('#submit_payout').html('');
				}
			});
		});
	</script>
@endpush