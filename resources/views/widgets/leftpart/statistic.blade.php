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