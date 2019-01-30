@if($found)
{{ $collection->appends([])->links() }}
<table class="table" >
 	<thead>
		<tr>
		    <th>{!! $sorts["day"] !!}</th>
			<th>{!! $sorts["action"] !!}</th>
			
		    <th>{!! $sorts["summa"] !!}</th>
		    
		</tr>
	</thead>
	<tbody>
	@foreach($collection as $col)
		<tr>
		    <td>{{$col->day}}</td>
			<td>{{$col->action}}</td>
		    <td  @if($col->summa>=0) style="color:#009900" @else style="color:#990000" @endif>{{$col->summa}}</td>

		</tr>
	@endforeach
	<tbody>
</table>	
@endif	