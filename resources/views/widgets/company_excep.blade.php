{{ $collection->appends([])->links() }}
<form method ="POST">
{{ csrf_field() }}
<table class="table">
 	<thead>
		<tr>
			<th>{!! $sorts["domain"] !!}</th>
			<th>{!! $sorts["except"] !!}</th>
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
			<td>{{$col->domain}}</td>
			<td>
			<input type="checkbox" name ="excepts[{{$col->pad}}]" @if($col->exc1) checked @endif/>
			<input type="hidden" name ="showcheck[{{$col->pad}}]" value="3"/>
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
<button type="submit" class="btn btn-primary">Сохранить исключения</button>
</form>	
{{ $collection->appends([])->links() }}