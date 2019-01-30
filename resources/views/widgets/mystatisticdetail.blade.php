{{ $collection->appends([])->links() }}
<table class="table">
 	<thead>
		<tr>
		    <th>{!! $sorts["shop"] !!}</th>

			<th>{!! $sorts["url"] !!}</th>
			<th>{!! $sorts["clicks"] !!}</th>
			<th>{!! $sorts["views"] !!}</th>
			<th>{!! $sorts["ctr"] !!}</th>
			<th>{!! $sorts["request"] !!}</th>
			<th>{!! $sorts["ipscount"] !!}</th>

		</tr>
	</thead>
	<tbody>
	@foreach($collection as $col)
		@php
	if(!$col->request)
		$col->request="--";
	$dop="";
	if($col->url)
		$dop='&dpu='.rawurlencode($col->url);
	$op='';
	if($col->jns)
		$op='&data='.rawurlencode($col->jns);
	$url='/adv_/testpage/api?name='.urlencode($col->request).'&count=10&pad='.$config["pad"].$dop.$op;
	@endphp
			<tr>
			<td>{{$col->shop}}</td>
		
			<td><a href="{{$col->url}}" target="_blank">{{$col->url}}</a></td>
			<td>{{$col->clicks}}</td>
			<td>{{$col->views}}</td>
			<td>{{$col->ctr}}</td>
			<td>
			 @if($col->url)
		     <a href="{{$col->url}}" target="_blank">{{$col->request}}</a>
	         @else
		    {{$col->request}}
		     @endif
			 <div class="panel">
		     <a href="{!! $url !!}&t=1" target="_newtestpage">только топадверт</a><br>
			 <a href="{!! $url !!}&t=2" target="_newtestpage">только прямые </a><br>
			 </div>
			</td>
			
			<td>{{$col->ipscount}}</td>

		</tr>
	@endforeach	
	</tbody>	
</table>	
{{ $collection->appends([])->links() }}