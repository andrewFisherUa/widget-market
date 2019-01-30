{{ $collection->appends([])->links() }}
<table class="table">
 	<thead>
		<tr>
		    <th>{!! $sorts["datetime"] !!}</th>
			<th>{!! $sorts["new"] !!}</th>
			<th>{!! $sorts["ltd"] !!}</th>
			<th>{!! $sorts["message"] !!}</th>
			<th>{!! $sorts["cnt"] !!}</th>
			<th>{!! $sorts["ip"] !!}</th>
		</tr>
	</thead>
	<tbody>
	@foreach($collection as $col)
	@php
	#$funkargs["pad"]=$col->pad;
	$url = preg_replace('/^.+(.{25,25})$/', "...\\1", $col->url);
	
	@endphp
	<tr> 
	    <td>{{$col->datetime}}
		</td>
		<td>
		@if($col->new )<span class="badge">новое</span>@else нет @endif
		
		</td>
		 <td>
		 <a href="{{$col->url}}" target="_blank">{{$col->ltd}}</a>
		 <br>
		 <a href ="{{route('pads.edit',['id'=>$col->pad])}}" target="_blank">площадка</a>
		 <br>
		 <a href ="{{route('widget.edit',['id'=>$col->pid])}}" target="_blank">виджет</a>
		</td>
		<td>{{$col->message}}
		</td>
		<td>{{$col->cnt}}
		</td>
		<td>{{$col->ip}}
		</td>		
	</tr>
	@endforeach	
	</tbody>	
</table>	
{{ $collection->appends([])->links() }}