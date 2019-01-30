@if($stata && isset($stata[0]))
<div class ="panel">
<table class="table">
<tr>
<td>
площадок в работе
</td>
<th>
{{$stata[0]->cnt}}
</th>
</tr>
<tr>
<td>
Показы
</td>
<th>
{{$stata[0]->views}}
</th>
</tr>
<tr>
<td>
Клики
</td>
<th>
{{$stata[0]->clicks}}
</th>
</tr>
<tr>
<td>
Цтр
</td>
<th>
{{$stata[0]->ctr}}
</th>
</tr>
<tr>
<td>
Общая сумма заработка
</td>
<th>
{{$stata[0]->summa}}
</th>
</tr>
<tr>
<td>
Клики на прямых рекламодателей
</td>
<th>
{{$stata[0]->sclicks}}
</th>
</tr>
{{--
<tr>
<td>
Цтр прямых рекламодателей
</td>
<th>
0
</th>
</tr>
--}}
<tr>
<td>
Сумма от прямых рекламодателей
</td>
<th>
{{$stata[0]->ssumma}}
</th>
</tr>
<tr>
<td>
Выплата вебмастерам
</td>
<th>
{{$stata[0]->psumma}}
</th>
</tr>
</table>
</div>
@endif
{{ $collection->appends([])->links() }}
<table class="table">
 	<thead>
		<tr>
		    <th>{!! $sorts["day"] !!}</th>
			<th>{!! $sorts["name"] !!}</th>
            <th>{!! $sorts["views"] !!}</th>
	        <th>{!! $sorts["clicks"] !!}</th>
			<th>{!! $sorts["ctr"] !!}</th>
			<th>{!! $sorts["summa"] !!}</th>
			<th>{!! $sorts["sclicks"] !!}</th>
			<th>{!! $sorts["sctr"] !!}</th>
			<th>{!! $sorts["ssumma"] !!}</th>
		</tr>
	</thead>
	<tbody>
	@foreach($collection as $col)
	@php
	$funkargs["pad"]=$col->pad;
	@endphp
	<tr> 
	    <td>{{$col->day}}
		</td>
		 <td>
		 <a href="{{route('advertiser.site_statistic_pad',$funkargs)}}">{{$col->name}}</a>
		</td>
		 <td>
		 {{$col->views}}
		</td>
		 <td>
		 {{$col->clicks}}
		</td>
		
		 <td>
		 {{$col->ctr}}
		</td>
		<td>
		  {{$col->summa}}
		</td>
		<td>
		  {{$col->sclicks}}
		</td>
		 <td>
		 {{$col->psumma}}
		</td>

		<td>
		  {{$col->ssumma}}
		</td>
	</tr>
	@endforeach	
	</tbody>	
</table>	
{{ $collection->appends([])->links() }}
