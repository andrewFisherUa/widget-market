{{ $collection->appends([])->links() }}
<table class="table">
 	<thead>
		<tr>
		    <th>{!! $sorts["day"] !!}</th>
				{{--<th>{!! $sorts["name"] !!}</th>--}}
            <th>{!! $sorts["request"] !!}</th>
	        <th>{!! $sorts["first_found"] !!}</th>
			<th>{!! $sorts["cnt"] !!}</th>
			<th>{!! $sorts["clicks"] !!}</th>
			<th>{!! $sorts["ctr"] !!}</th>
			<th>{!! $sorts["last_visit"] !!}</th>
			<th title="клики на магазины пямых рекламодателей">клики прямых рекл</th>
			<th title="сумма за переходы со страницы на магазин">сумма</th>
		</tr>
	</thead>
	<tbody>
		@foreach($collection as $col)
	@php
	$funkargs["pad"]=$col->pad;
	if(!$col->request)
		$col->request="--";
	$dop="";
	if($col->url)
		$dop='&dpu='.rawurlencode($col->url);
	$op='';
	if($col->jns)
		$op='&data='.rawurlencode($col->jns);
		
		
	$url='http://product.market-place.su/api?name='.urlencode($col->request).'&count=10&pid='.$col->pid.$dop.$op;
	$url='/adv_/testpage/api?name='.urlencode($col->request).'&count=10&pid='.$col->pid.$dop.$op;
	@endphp
	<tr> 
	    <td>{{$col->day}}
		</td>
		{{-- <td> {{$col->name}}</td>--}}
		<td>
		 @if($col->url)
		 <a href="{{$col->url}}" target="newrightshop">{{$col->request}}</a>
	     @else
		{{$col->request}}
	     @endif
		  <div class="panel panel-primary">
		 <a href="{!! $url !!}&t=3" target="newtests">поиск маркет</a><br>
		 <a href="{!! $url !!}&t=4" target="newtests">поиск yandex</a>
		 </div>
		</td>
		 <td>
		 {{$col->first_found}}
		</td>
		 <td>
		 {{$col->cnt}}
		</td>
		<td>
		 {{$col->clicks}}
		</td>
		<td>
		 {{$col->ctr}}
		</td>
		<td class="text-nowrap">
		 {{$col->last_visit}}
		</td>	
		<td class="text-nowrap">
		 0
		</td>	
		<td class="text-nowrap">
		 0
		</td>			
	</tr>
	@endforeach	
    </tbody>
</table>	
{{ $collection->appends([])->links() }}
