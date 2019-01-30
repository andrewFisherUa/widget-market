@if($superstat)
		<div class="well well-sm customright">Настройки</div> 
		<div class ="panel-left-padding">
		<div>
		<a href="{{route($config['pref'].'profile.personal',$config['wparams'])}}#payments" >Реквизиты</a>
		</div>  
	    <div>
		<a href="{{route($config['pref'].'profile.personal',$config['wparams'])}}#password" >Сменить пароль</a>
		</div>  
		</div>  
		<div class="well well-sm customright">Взаиморасчёты {!! $dptitle !!}</div>
		@endif
		@if($superstat)
		
		<div class ="panel-left-padding">
		 <div class ="affiliate_detal_balance"><span>Общий баланс</span> <div class="right">{{$superstat["balance"]}}</div></div>  
		{{--
		  
		   <div class ="affiliate_detal_balance"><span>Сегодня</span> <div class="right">{{$superstat['today']}}</div></div>  
		   <div class ="affiliate_detal_balance"><span>Вчера</span><div class="right">{{$superstat['yesturday']}}</div></div>  
		   <div class ="affiliate_detal_balance"><span>Неделя</span><div class="right">{{$superstat['week']}}</div></div>  
          @if($id_user)
		  <div class ="affiliate_detal_balance"><a href="{{route('admin.balance_history',$superstat['params'])}}">Подробнее</a></div>  
	      @else 
          <div class ="affiliate_detal_balance"><a href="{{route('advertiser.balance_history',$superstat['params'])}}">Подробнее</a></div>  		  			   
		  	  
	      @endif
		--}}
		   <div class ="affiliate_detal_balance"><a href="#" data-toggle="modal" data-target="#advertiser_payout">Пополнить баланс</a></div>  
		   
		   @if($invsLink)
			   @role(['advertiser'])
		       <div class ="affiliate_detal_balance"><a href="{{$invsLink}}">Счета</a></div>
		       @endrole
		   @endif
		   
     	</div>  
		@endif	