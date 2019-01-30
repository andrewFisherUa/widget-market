@push('cabinet_home') 
 <style>
	.customright {margin-bottom:4px;}
	.panel-left-padding{
		padding-left:17px;
		margin-bottom:10px;
	}
	.active_l{
		background-color:#f5f5f5;
		border:1px solid #636b6f;
	}
	</style>	   
@endpush	


@if($config["key"]!=4)

	@if($superstat)
        <div class="well well-sm customright">Мои компании</div>
		<div class ="panel-left-padding">
			<a href="#" data-toggle="modal" data-target="#modal_add_company">Добавить компанию</a>
		</div>
    @endif	
		<div class="well well-sm customright">Статистика</div>
		<div class ="panel-left-padding">
		@if($id_user)
		<div @if($mod=="data")class="active_l" @endif><a href="{{route('admin.statistic',["id"=>$shop_id,"id_user"=>$id_user,"mod"=>"data","from"=>$from,"to"=>$to])}}">По дате</a></div>  
	    <div @if($mod=="region")class="active_l" @endif><a href="{{route('admin.statistic',["id"=>$shop_id,"id_user"=>$id_user,"mod"=>"region","from"=>$from,"to"=>$to])}}">По регионам</a></div>  
		<div @if($mod=="category")class="active_l" @endif><a href="{{route('admin.statistic',["id"=>$shop_id,"id_user"=>$id_user,"mod"=>"category","from"=>$from,"to"=>$to])}}">По категориям</a></div> 
        <div @if($mod=="domain")class="active_l" @endif><a href="{{route('admin.statistic',["id"=>$shop_id,"id_user"=>$id_user,"mod"=>"domain","from"=>$from,"to"=>$to])}}">По доменам</a></div>  					
		@if(!$shop_id)
			<div @if($mod=="company")class="active_l" @endif><a href="{{route('admin.statistic',["id"=>$shop_id,"id_user"=>$id_user,"mod"=>"company","from"=>$from,"to"=>$to])}}">По доменам</a></div>  					
		@endif	
			{{--<div @if($mod=="company")class="active_l" @endif><a href="{{route('advertiser.statistic',["id"=>$shop_id,"mod"=>"company","from"=>$from,"to"=>$to])}}">По магазинам</a></div>--}}
		@else 
		<div @if($mod=="data")class="active_l" @endif><a href="{{route('advertiser.statistic',["id"=>$shop_id,"mod"=>"data","from"=>$from,"to"=>$to])}}">По дате</a></div>  
	    <div @if($mod=="region")class="active_l" @endif><a href="{{route('advertiser.statistic',["id"=>$shop_id,"mod"=>"region","from"=>$from,"to"=>$to])}}">По регионам</a></div>  
		<div @if($mod=="category")class="active_l" @endif><a href="{{route('advertiser.statistic',["id"=>$shop_id,"mod"=>"category","from"=>$from,"to"=>$to])}}">По категориям</a></div> 
        <div @if($mod=="domain")class="active_l" @endif><a href="{{route('advertiser.statistic',["id"=>$shop_id,"mod"=>"domain","from"=>$from,"to"=>$to])}}">По доменам</a></div>  		
        @if(!$shop_id)
			<div @if($mod=="company")class="active_l" @endif><a href="{{route('advertiser.statistic',["id"=>$shop_id,"mod"=>"company","from"=>$from,"to"=>$to])}}">По магазинам</a></div>
		@endif		
		@endif	
		</div>  
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
		{{--
		<div class ="panel-left-padding">
		   <div class ="affiliate_detal_balance"><span>Общий баланс</span> <div class="right">{{$superstat["balance"]}}</div></div>  
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
			   <div class ="affiliate_detal_balance"><a href="{{$invsLink}}">Счета</a></div>  
		   @endif
		   
     	</div>  
		@endif
		
@else		
	
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
		   <div class ="affiliate_detal_balance"><span>Сегодня</span> <div class="right">{{$superstat['today']}}</div></div>  
		   <div class ="affiliate_detal_balance"><span>Вчера</span><div class="right">{{$superstat['yesturday']}}</div></div>  
		   <div class ="affiliate_detal_balance"><span>Неделя</span><div class="right">{{$superstat['week']}}</div></div>  
          @if($id_user)
		  <div class ="affiliate_detal_balance"><a href="{{route('admin.balance_history',$superstat['params'])}}">Подробнее</a></div>  
	      @else 
          <div class ="affiliate_detal_balance"><a href="{{route('advertiser.balance_history',$superstat['params'])}}">Подробнее</a></div>  		  			   
		  	  
	      @endif
		
		   <div class ="affiliate_detal_balance"><a href="#" data-toggle="modal" data-target="#advertiser_payout">Пополнить баланс</a></div>  
		   
		   @if($invsLink)
			   <div class ="affiliate_detal_balance"><a href="{{$invsLink}}">Счета</a></div>  
		   @endif
		   
     	</div>  
		@endif		
@endif


		
@include('advertiser.payouts.modal_payout')
		@include('advertiser.cabinet.modal_add_company')
	    