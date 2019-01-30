{{ $collection->appends([])->links() }}
<table class="table">
 	<thead>
		<tr>
		    <th>{!! $sorts["day"] !!}</th>
			<th>{!! $sorts["name"] !!}</th>
            <th>{!! $sorts["request"] !!}</th>
	        <th>{!! $sorts["first_found"] !!}</th>
			<th>{!! $sorts["cnt"] !!}</th>
			<th>{!! $sorts["clicks"] !!}</th>
			<th>{!! $sorts["ctr"] !!}</th>
			<th>{!! $sorts["last_visit"] !!}</th>
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
		
		
	$url='/adv_/testpage/api?name='.urlencode($col->request).'&count=10&pad='.$col->pad.$dop.$op;
	@endphp
	<tr> 
	    <td>{{$col->day}}
		</td>
		 <td>
		  {{$col->name}}
		</td>
		 <td>
		 @if($col->url)
		 <a href="{{$col->url}}" target="_blank">{{$col->request}}</a>
	     @else
		{{$col->request}}
	     @endif
		 <div class="panel">
		 <a href="{!! $url !!}&t=1" target="_blank">только топадверт</a><br>
		 <a href="{!! $url !!}&t=2" target="_blank">только прямые </a><br>
		 <a href="{!! $url !!}&t=3" target="_blank">только вместе</a><br>
		 <a href="{!! $url !!}&t=4" target="_blank">только yandex</a>
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
	</tr>
	@endforeach	
	</tbody>	
</table>	
{{ $collection->appends([])->links() }}
