{{ $collection->appends([])->links() }}
<table class="table">
 	<thead>
		<tr>
			<th>{!! $sorts["domain"] !!}</th>
			<th>{!! $sorts["clicks"] !!}</th>
			<th>{!! $sorts["cpc"] !!}</th>
			<th>{!! $sorts["inv"] !!}</th>
			
			<th>{!! $sorts["views"] !!}</th>
			<th>{!! $sorts["ctr"] !!}</th>
			<th>{!! $sorts["summa"] !!}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	@foreach($collection as $col)
	@php
	$args["vid"]=$col->pad
	@endphp
			<tr>
			<td>
			@if($myadmin)<a href="{{ route($func,$args)}}">{{$col->domain}}</a>
			
			@else
				{{$col->domain}}
			@endif
			</td>
			<td>{{$col->clicks}}</td>
			<td>{{$col->cpc}}</td>
			<td>{{$col->inv}}</td>
			
			<td>{{$col->views}}</td>
			<td>{{$col->ctr}}</td>
			<td>{{$col->summa}}</td>
			<td></td>
		</tr>
	@endforeach	
	</tbody>	
</table>	
{{ $collection->appends([])->links() }}