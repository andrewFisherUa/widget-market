<div class="row">

<div class="col-xs-10 form-group">  
<form class="form-inline" role="form" method="get">
		<div class="row">
			<div class="col-xs-3 form-group">  
				 <select  class="form-control" name ="adv_status">
				 <option value="-1" @if($adv_status==-1) selected @endif>Все статусы</option>
				 @foreach($adv_statuses as $a_st)
				 <option value="{{$a_st->id}}" @if($adv_status==$a_st->id) selected @endif>{{$a_st->name}} </option>
				 @endforeach
				 </select>
			</div>
		    <div class="input-group col-xs-4 form-group">
			
				<span class="input-group-addon">Поиск:</span>
				<input type="text" class="form-control" value="{{Request('name')}}" name="name">
			</div>
			<div class="col-xs-1 input-group form-group">
				<button type="submit" class="btn btn-primary">Применить</button>
			</div>
		</div>
	</form>
	</div>
	        <div class="col-xs-2">
				<a href="#" data-toggle="modal" data-target="#modal_add_company" class="btn btn-success">
					Добавить компанию
				</a>
			</div>
		</div>		 
	@if($da)
	    
	{{ $da->appends([])->links() }}
	@endif
	
<div class="affiliate_cabinet_block" style="height: 550px;"><div class="heading text-left">Мои Магазины 

<a href="#" data-toggle="modal" data-target="#modal_add_company" class="affiliate_add_domain">
<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="" class="glyphicon glyphicon-plus-sign" data-original-title="Создать компанию"></span>
</a>

</div> 

<hr class="affilaite_hr"> 
<div id="home_widgets" class="home_block">
<div class="row" style="margin: 10px 0">


<div class="affiliate_cabinet_bot" style="margin-top: 10px;">
		<table class="table table-condensed table-hover widget-table">
		<thead>
			<tr class="widget-table-header">
				<td style="width:25px"></td>
				<td>{!!$sorts['name']!!}</td>
				
				
				@if(\Auth::user()->hasRole(['admin','super_manager','manager']))
			<th>{!!$sorts['username']!!}</th>
			<th>Допуски</th>
		    <th>{!!$sorts['status']!!}</th>
			<th>{!!$sorts["balance"]!!}</th>
			@else
				<td>{!!$sorts['status']!!}</td>
				@endif
			<td>{!!$sorts["day_clicks"]!!}</td>
			    <td style="width:25px"></td>
			    <td style="width:25px"></td>
				<td style="width:25px"></td>
				<td style="width:25px"></td>
			</tr>
		</thead>
		<tbody>
			@foreach($da as $d)
			<tr>
			    <td>
				    @if($d->type==1)
						<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Товарная компания" style="cursor:pointer" class="glyphicon glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green widget-gl"></span>
					@elseif($d->type==2)
						<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Видео компания" style="cursor:pointer" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green widget-gl"></span>
					@elseif($d->type==3)
						<span data-toggle="tooltip" data-placement="bottom" title="Тизерная компания" style="cursor:pointer" class="glyphicon glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green widget-gl"></span>
					@endif
				</td>
			    <td>{{$d->name}}</td>	
				@if(\Auth::user()->hasRole(['admin','super_manager','manager']))
					
				<td>
				@if($d->user_id)
				<a href="{{route('admin.home',['id_user'=>$d->user_id])}}">
				{{$d->username}}</a>
				@else
				{{$d->username}}
				@endif
				</td>
				<td>
				@if ($d->site_permissions&1)
				<img src="/images/smail/green.png" style="width: 10px; height: 10px; top: 0px; cursor: pointer; display: inline-block;">
				@endif
				@if ($d->site_permissions&2)
				<img src="/images/smail/yellow.png" style="width: 10px; height: 10px; top: 0px; cursor: pointer; display: inline-block;">
				@endif
				@if ($d->site_permissions&4)
				<img src="/images/smail/red.png" style="width: 10px; height: 10px; top: 0px; cursor: pointer; display: inline-block;">
				@endif
			    </td>
				
				<td>
				@if ($d->status==1 or $d->status==4)
					<select class="play_stop" data-set="{{$d->id}}">
						<option @if ($d->status==1) selected @endif value="1">В работе</option>
						<option @if ($d->status==4) selected @endif value="4">Приостановлен</option>
					</select>
				@else
					{{$adv_statuses[$d->status]->name}}
				@endif
				</td>	
				<td>{{$d->balance}}</td>
				@else
				<td>
				@if ($d->status==1 or $d->status==4)
					<select class="play_stop" data-set="{{$d->id}}">
						<option @if ($d->status==1) selected @endif value="1">В работе</option>
						<option @if ($d->status==4) selected @endif value="4">Приостановлен</option>
					</select>
				@else
					{{$adv_statuses[$d->status]->name}}
				@endif
				</td>	
			
					
				@endif	
				<td>{{$d->day_clicks}} / @if($d->limit_clicks) {{$d->limit_clicks}} @else ~ @endif</td>
				
				<td>@if ($d->type==1)
				<a href="" data-toggle="modal" data-target="#get_code_{{$d->id}}" class="get_code"><span  data-toggle="tooltip" data-placement="bottom" title="Получить код отслеживания покупок"  class="green" style="font-weight: bold; display: block;">&lt;/&gt;</span></a>
				@endif
			    </td>
                <td>
				<a href="{{route('advertiser.statistic',['shop_id'=>$d->id])}}"  data-trigger="hover" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Детальная статистика"><span class="glyphicon glyphicon-stats color-purple"></span></a>
				</td>				
				<td>
				@if ($d->type==1)
					
					 @if(\Auth::user()->hasRole(['admin','super_manager','manager']))
						@if($some_soobr)
							<a href = "{{  route("advertiser.edit_company",["id"=>$d->id]) }}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Редактировать компанию"><span class="glyphicon glyphicon-pencil color-blue"></span></a>
						@else
							<a href = "{{  route("admin.edit_company",["id_user"=>$config["user"]->id,"id"=>$d->id]) }}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Редактировать компанию"><span class="glyphicon glyphicon-pencil color-blue"></span></a>				
						@endif	
					@else
					<a href = "{{  route("advertiser.edit_company",["id"=>$d->id]) }}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Редактировать компанию"><span class="glyphicon glyphicon-pencil color-blue"></span></a>
					@endif
				@elseif ($d->type==3)
				
				    @if(!isset($config["wparams"]["id_user"]))
					<a href = "{{  route("advertiser.edit_company_teaser",["id"=>$d->id]) }}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Редактировать компанию"><span class="glyphicon glyphicon-pencil color-blue"></span></a>
				    @else
				     <a href = "{{  route("admin.edit_company_teaser",["id_user"=>$config["user"]->id,"id"=>$d->id]) }}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Редактировать компанию"><span class="glyphicon glyphicon-pencil color-blue"></span></a>				
			        @endif
				
				@endif
				</td>
				<td>
				@if ($d->type==1)
				<a href="{{route('advertiser.company.exceptions', ['id'=>$d->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Исключить площадки">
					<span class="glyphicon glyphicon-cog color-green"></span>
				</a>
				@elseif($d->type==3)
				
				
				
				<span style="white-space:nowrap">
				@if(1==1 || \Auth::user()->hasRole(['admin','super_manager','manager']))
				<a href="{{route('mpteaser.offers', ['id'=>$d->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Все предложения">
					<span class="glyphicon glyphicon-th color-green"></span>
				</a>
				@else
				<a href="{{route('advertiser.add_offer_company', ['id'=>$d->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Добавить предложения">
					<span class="glyphicon glyphicon-plus color-green"></span>
				</a>
				@endif
				{{--
				<a href="{{route('advertiser.all_offer_company', ['id'=>$d->id])}}" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Все предложения">
					<span class="glyphicon glyphicon-th color-green"></span>
				</a>
				--}}
				</span>
				@endif
				</td>
				<td>
				@if (1==1 || $d->type==1)
			    <form method="post" action = "{{route('advertiser.delete_advertise',['id'=>$d->id]) }}" >{{ csrf_field() }}
				<button type="submit" style="border:0">
                <span class="glyphicon glyphicon-trash color-red"></span>
                </button></form>
				@endif
				</td>
				</tr>
				<tr><td colspan="14" style="padding: 0; border: 0;">@include('affiliate.cabinet.get_cpa_code')</td></tr>
			@endforeach	
		<tbody>		
		</table>
</div>	

</div>	
</div>	
	
</div>	
@include('advertiser.cabinet.modal_add_company',['id_user'=>$config["user"]->id])
@push('cabinet_home_js')
	<script>
		$(document).ready(function(){
			$('.play_stop').change(function(){
				var val=$('option:selected',this).val();
				$.post('/adv_/company_status_post/'+$(this).data('set'),{ _token: $('meta[name=csrf-token]').attr('content'), 
					status: val}, function(response) {
						console.log(response);
					});
			});
		});
	</script>
@endpush		
