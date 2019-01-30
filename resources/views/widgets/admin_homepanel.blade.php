@role(['admin','super_manager','manager'])
<div class="row" style="margin: 10px 0px;">
<a href="{{ route('admin.invoices_history',['id_user'=>$config['user']->id])}}" class="btn btn-primary">Счета пользователя</a>
<a href="{{ route('admin.balance_history',['id_user'=>$config['user']->id,'shop_id'=>0])}}" class="btn btn-primary">Взаиморасчёты пользователя</a>
<a href="{{ route('admin.statistic',['id_user'=>$config['user']->id,'shop_id'=>0])}}" class="btn btn-primary">Статистика магазинов пользователя</a>
<a href="{{ route('admin.disco',['id_user'=>$config['user']->id])}}" class="btn btn-primary">Файлы пользователя</a>
</div>
@endrole
<div class="row">
<div class="col-xs-3">
     <div class="affiliate_cabinet_block text-center"><div class="heading text-left">Мой профиль</div> 
	 
	      <hr class="affilaite_hr"> <div id="home_profile" class="home_block">
	      <div class="home_avatar_block">
		    <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" class="home_avatar_red" data-original-title="Был в сети 2018-02-01 22:39:10"></span>
	        <img src="/images/cabinet/no_foto.png" class="img-circle cabinet_avatar">
	      </div>
     <div class="affiliate_name">{{$config["user"]->name}}</div>
     <div class="affiliate_role">{{$profileName}}</div>
     <div class="affiliate_email">{{$config["user"]->email}}</div>
     <div class="cabinet_gliph">
	 
	   <a href="{{route($config['pref'].'profile.personal',$config['params'])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" target="_blank" data-original-title="Профиль"><span class="glyphicon glyphicon-user gliph_affiliate"></span></a>
	   
	  <a target="_blank" href="https://widget.market-place.su/news" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" class="home_news" data-original-title="Новости"><span class="glyphicon glyphicon-envelope gliph_affiliate"></span></a>
	  <a href="https://widget.market-place.su/logout" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-original-title="Выйти"><span class="glyphicon glyphicon-log-out gliph_affiliate"></span></a>
	  <form id="logout-form" action="https://widget.market-place.su/logout" method="POST" style="display: none;">
		<input name="_token" value="NVwZZcStWDHkQrsH3cgrkKt47K2r4kaSKVmxRTak" type="hidden">
	</form>
</div></div></div>
</div> 
@if($balancer)
<div class="col-xs-3"><div class="affiliate_cabinet_block"><div class="heading text-left">Мой баланс 
<a href="{{route('advertiser.balance_history',['shop_id'=>0])}}" class="affiliate_add_domain">
<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" class="glyphicon glyphicon-question-sign color-blue manager_help" data-original-title="Детальная статистика"></span></a>
</div> 


<hr class="affilaite_hr"> <div id="home_balance" class="home_block"><div class="affiliate_balance text-center">{{$balancer["balance"]}}<span class="rur">q</span>
	{{--
<span class="glyphicon glyphicon-exclamation-sign color-red" style="font-size: 20px; line-height: 1; position: relative; top: -6px;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" data-original-title="Включен автозаказ выплат"></span>
	--}}
</div>
{{--
<p class="text-center"><a href="#" data-toggle="modal" data-target="#payment" class="btn btn-primary" role="button">Заказать выплату</a></p>


<div class ="affiliate_detal_balance"><a href="#" data-toggle="modal" data-target="#auto_payment" class="btn btn-success" role="button">Автозаказ выплат</a></div>
--}}

<p class ="text-center" style ="margin-bottom:20px"><a href="#" data-toggle="modal" data-target="#advertiser_payout" class="btn btn-success" role="button">Пополнить баланс</a></p>

@include('advertiser.payouts.modal_payout')
<div class="affiliate_detal_balance"><span>Сегодня:</span><div class="right">{{$balancer["today"]}}<span class="rur">q</span></div></div>
<div class="affiliate_detal_balance"><span>Вчера:</span><div class="right">{{$balancer["yesturday"]}}<span class="rur">q</span></div></div>
<div class="affiliate_detal_balance"><span>Неделя:</span><div class="right">{{$balancer["week"]}}<span class="rur">q</span></div></div>
<div class="affiliate_detal_balance" style="border-bottom: 0;"><span>Месяц:</span><div class="right">{{$balancer["month"]}}<span class="rur">q</span></div></div>
{{--
<div id="payment" data-set="0" class="modal fade">
	<div class="modal-dialog">
	
		<div class="modal-content">
		
		<div class="affiliate_modal_header">Заказ выплаты<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Закрыть"></button></div>
		
		
		
			<hr class="modal_hr">
			<!--3-->
						<form class="form-horizontal" role="form" method="post" action="https://widget.market-place.su/add_payout">
				<input name="_token" value="NVwZZcStWDHkQrsH3cgrkKt47K2r4kaSKVmxRTak" type="hidden">
				<input name="user_id" value="6" type="hidden">
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Сумма</label>
					<div class="col-xs-6">
						<input name="summa" id="summa_for_pay" min="300" max="17470.62" step="0.01" value="17470" class="form-control" required="" type="number">
						<span class="help-block" style="margin: 0;" id="text_for_user_pay">
								<strong>Минимальная сумма выплаты 300 руб.</strong>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Платежная система</label>
					<div class="col-xs-6">
						<!-- [{&quot;id&quot;:1,&quot;name&quot;:&quot;\u042f\u043d\u0434\u0435\u043a\u0441 \u0434\u0435\u043d\u044c\u0433\u0438&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u043a\u043e\u0448\u0435\u043b\u044c\u043a\u0430&quot;,&quot;engname&quot;:&quot;yandex&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:38:32&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:38:32&quot;},{&quot;id&quot;:2,&quot;name&quot;:&quot;Qiwi \u043a\u043e\u0448\u0435\u043b\u0435\u043a&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u043a\u043e\u0448\u0435\u043b\u044c\u043a\u0430&quot;,&quot;engname&quot;:&quot;qiwi&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:39:28&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:39:28&quot;},{&quot;id&quot;:3,&quot;name&quot;:&quot;PayPal&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u0441\u0447\u0435\u0442\u0430&quot;,&quot;engname&quot;:&quot;paypal&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:40:03&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:40:03&quot;},{&quot;id&quot;:4,&quot;name&quot;:&quot;Web Money&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 WMR \u043a\u043e\u0448\u0435\u043b\u044c\u043a\u0430&quot;,&quot;engname&quot;:&quot;wm&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:40:41&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:40:41&quot;},{&quot;id&quot;:5,&quot;name&quot;:&quot;\u0421\u0447\u0435\u0442 \u0432 \u0431\u0430\u043d\u043a\u0435 (\u042e\u0420 \u043b\u0438\u0446\u043e)&quot;,&quot;label&quot;:&quot;\u2116 \u0440\u0430\u0441\u0447\u0435\u0442\u043d\u043e\u0433\u043e \u0441\u0447\u0435\u0442\u0430&quot;,&quot;engname&quot;:&quot;urface&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:42:01&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:42:01&quot;},{&quot;id&quot;:6,&quot;name&quot;:&quot;\u041f\u043b\u0430\u0441\u0442\u0438\u043a\u043e\u0432\u0430\u044f \u043a\u0430\u0440\u0442\u0430&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u043a\u0430\u0440\u0442\u044b&quot;,&quot;engname&quot;:&quot;card&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:42:32&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:42:32&quot;}] -->
						<select name="pay_option" class="form-control">
																																																																		<option value="2">Qiwi кошелек </option>
																																																																																																																			<option selected="" value="5">Счет в банке (ЮР лицо) </option>
																																																			<option value="6">Пластиковая карта  Комиссия 1% </option>
																														</select>
					</div>
				</div>
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Срочный вывод</label>
					<div class="col-xs-6">
						<input name="urgently" value="1" id="user_urgently_pay" style="margin-top: 12px;" type="checkbox">
												<span class="help-block" style="margin: 0; color: rgb(181, 0, 0);">
								<strong>Комиссия 6%</strong>
						</span>
											</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
					  <a id="payment_submit" class="btn btn-primary">Запросить</a>
					</div>
				</div>
			</form>
		
					</div>
					
	</div>
</div>


<div id="auto_payment" data-set="0" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="affiliate_modal_header">Настройка автозаказа выплат<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Закрыть"></button></div>
			<hr class="modal_hr">
			<!--3-->
						<form class="form-horizontal" role="form">
				<input name="_token" value="NVwZZcStWDHkQrsH3cgrkKt47K2r4kaSKVmxRTak" type="hidden">
				<input name="user_id" value="6" type="hidden">
				<div class="form-group">
					<label for="auto_pay" class="col-xs-4 control-label">Включить автозаказ</label>
					<div class="col-xs-6">
						<input name="auto_pay" value="1" checked="" style="margin-top: 12px;" type="checkbox">
					</div>
				</div>
				<!-- {&quot;id&quot;:42,&quot;user_id&quot;:6,&quot;payment_id&quot;:5,&quot;day&quot;:2,&quot;urgently&quot;:null,&quot;created_at&quot;:&quot;2017-11-04 19:11:54&quot;,&quot;updated_at&quot;:&quot;2017-11-04 19:11:54&quot;} -->
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Выберите день недели</label>
					<div class="col-xs-6">
						<select name="day" class="form-control">
							<option value="1">Понедельник</option>
							<option selected="" value="2">Вторник</option>
							<option value="3">Среда</option>
							<option value="4">Четверг</option>
							<option value="5">Пятница</option>
							<option value="6">Суббота</option>
							<option value="0">Воскресенье</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Срочный вывод</label>
					<div class="col-xs-6">
						<input name="urgently" value="1" style="margin-top: 12px;" type="checkbox">
												<span class="help-block" style="margin: 0; color: rgb(181, 0, 0);">
								<strong>С включенным срочным выводом комиссия 6%</strong>
						</span>
											</div>
				</div>
				<div class="form-group">
					<label for="summa" class="col-xs-4 control-label">Платежная система</label>
					<div class="col-xs-6">
						<!-- [{&quot;id&quot;:1,&quot;name&quot;:&quot;\u042f\u043d\u0434\u0435\u043a\u0441 \u0434\u0435\u043d\u044c\u0433\u0438&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u043a\u043e\u0448\u0435\u043b\u044c\u043a\u0430&quot;,&quot;engname&quot;:&quot;yandex&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:38:32&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:38:32&quot;},{&quot;id&quot;:2,&quot;name&quot;:&quot;Qiwi \u043a\u043e\u0448\u0435\u043b\u0435\u043a&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u043a\u043e\u0448\u0435\u043b\u044c\u043a\u0430&quot;,&quot;engname&quot;:&quot;qiwi&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:39:28&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:39:28&quot;},{&quot;id&quot;:3,&quot;name&quot;:&quot;PayPal&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u0441\u0447\u0435\u0442\u0430&quot;,&quot;engname&quot;:&quot;paypal&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:40:03&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:40:03&quot;},{&quot;id&quot;:4,&quot;name&quot;:&quot;Web Money&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 WMR \u043a\u043e\u0448\u0435\u043b\u044c\u043a\u0430&quot;,&quot;engname&quot;:&quot;wm&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:40:41&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:40:41&quot;},{&quot;id&quot;:5,&quot;name&quot;:&quot;\u0421\u0447\u0435\u0442 \u0432 \u0431\u0430\u043d\u043a\u0435 (\u042e\u0420 \u043b\u0438\u0446\u043e)&quot;,&quot;label&quot;:&quot;\u2116 \u0440\u0430\u0441\u0447\u0435\u0442\u043d\u043e\u0433\u043e \u0441\u0447\u0435\u0442\u0430&quot;,&quot;engname&quot;:&quot;urface&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:42:01&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:42:01&quot;},{&quot;id&quot;:6,&quot;name&quot;:&quot;\u041f\u043b\u0430\u0441\u0442\u0438\u043a\u043e\u0432\u0430\u044f \u043a\u0430\u0440\u0442\u0430&quot;,&quot;label&quot;:&quot;\u043d\u043e\u043c\u0435\u0440 \u043a\u0430\u0440\u0442\u044b&quot;,&quot;engname&quot;:&quot;card&quot;,&quot;profile&quot;:0,&quot;created_at&quot;:&quot;2017-09-25 16:42:32&quot;,&quot;updated_at&quot;:&quot;2017-09-25 16:42:32&quot;}] -->
						<select name="pay_option" class="form-control">
																																																																		<option value="2">Qiwi кошелек </option>
																																																																																																																			<option selected="" value="5">Счет в банке (ЮР лицо) </option>
																																																			<option value="6">Пластиковая карта  Комиссия 1% </option>
																														</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
					  <a id="auto_payment_submit" class="btn btn-primary">Сохранить</a>
					</div>
				</div>
			</form>
					</div>
	</div>
</div>
--}}
</div></div></div>
@endif
@if($manager)
 <div class="col-xs-3 col-xs-3">
 <div class="affiliate_cabinet_block text-center"><div class="heading text-left">Контакты</div> <hr class="affilaite_hr"> <div id="home_contacts" class="home_block"><div class="affiliate_manager">Ваш менеджер:</div>
			<div class="home_avatar_block">
							<span data-toggle="tooltip" data-placement="bottom" title="" class="home_avatar_red" data-original-title="Ваш менеджер offline"></span>
						<img class="img-circle manager_avatar" src="/images/cabinet/no_foto.png">
		</div>
		<div class="affiliate_name">{{$manager->name}}</div>			
	<div class="manager_skype"><b>Skype:</b> <a href="skype:asdima8?chat">{{$manager->Profile->skype}}</a></div>
	<div class="manager_skype"><b>Email:</b> <a href="mailto:manager@automediya.ru">{{$manager->email}}</a></div>
</div></div></div> 
@else 
@endif
  @widget(' AdvertNews',["user"=>$config["user"]])
 

@push('cabinet_home_top')
    <link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/news.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom_scroll/jquery.custom-scroll.css') }}" rel="stylesheet">
@endpush	
@push('cabinet_home_js')
<script src="/js/custom_scroll/jquery.custom-scroll.min.js"></script>
<script>
$( document ).ready(function() { 

$('#cabinet_news').customScroll({
				offsetTop: 32,
				offsetRight: 16,
				offsetBottom: -42,
				vertical: true,
				horizontal: false
			});
});		
</script>	
@endpush							