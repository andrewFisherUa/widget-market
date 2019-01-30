@extends('layouts.app')

@section('content')
<div class="container">
	@include('lbtc.top_menu')
    <div class="row text-center">
		<!--@if ($month=="01" or $month=="03" or $month=="05" or $month=="07" or $month=="08" or $month=="10" or $month=="12")
			{{$l=31}}
			@elseif ($month=="04" or $month=="06" or $month=="09" or $month=="11")
			{{$l=30}}
			@elseif ($month=="02" and $year%4==0)
			{{$l=29}}
			@elseif ($month=="13")
			{{$l=12}}
			@else
			{{$l=28}}
			@endif
			@if ($month=="01")
				{{$mo="Январь"}}
			@elseif ($month=="02")
				{{$mo="Февраль"}}
			@elseif ($month=="03")
				{{$mo="Март"}}
			@elseif ($month=="04")
				{{$mo="Апрель"}}
			@elseif ($month=="05")
				{{$mo="Май"}}
			@elseif ($month=="06")
				{{$mo="Июнь"}}
			@elseif ($month=="07")
				{{$mo="Июль"}}
			@elseif ($month=="08")
				{{$mo="Август"}}
			@elseif ($month=="09")
				{{$mo="Сентябрь"}}
			@elseif ($month=="10")
				{{$mo="Октябрь"}}
			@elseif ($month=="11")
				{{$mo="Ноябрь"}}
			@elseif ($month=="12")
				{{$mo="Декабрь"}}
			@else
				{{$mo="весь год"}}
			@endif
		-->
		
		<h3>Детализация {{$valut->title}} за {{$mo}}</h3>
		<form class="form-inline text-left" role="form" method="get" style="margin:5px 0">
			<div class="input-group col-xs-3 form-group">
				<select name='month' class="form-control">
					<option @if ($month=="13") selected @endif value="13">За год</option>
					<option @if ($month=="01") selected @endif value="01">Январь</option>
					<option @if ($month=="02") selected @endif value="02">Февраль</option>
					<option @if ($month=="03") selected @endif value="03">Март</option>
					<option @if ($month=="04") selected @endif value="04">Апрель</option>
					<option @if ($month=="05") selected @endif value="05">Май</option>
					<option @if ($month=="06") selected @endif value="06">Июнь</option>
					<option @if ($month=="07") selected @endif value="07">Июль</option>
					<option @if ($month=="08") selected @endif value="08">Август</option>
					<option @if ($month=="09") selected @endif value="09">Сентябрь</option>
					<option @if ($month=="10") selected @endif value="10">Октябрь</option>
					<option @if ($month=="11") selected @endif value="11">Ноябрь</option>
					<option @if ($month=="12") selected @endif value="12">Декабрь</option>
				</select>
			</div>
			<div class="input-group col-xs-2 form-group">
				<input name='year' class="form-control" value="{{$year}}">
			</div>
			<div class="col-xs-2 input-group form-group">
				<button type="submit" class="btn btn-primary">Применить</button>
			</div>
		</form>
		@if ($month==13)
			@for ($i = 1; $i <= $l; $i++)
				<!--{{($stats=$valut->yearStat($valut->id, $i, $year))?"":""}}-->
					<!--@if ($i=="1")
						{{$mm="Январь"}}
					@elseif ($i=="2")
						{{$mm="Февраль"}}
					@elseif ($i=="3")
						{{$mm="Март"}}
					@elseif ($i=="4")
						{{$mm="Апрель"}}
					@elseif ($i=="5")
						{{$mm="Май"}}
					@elseif ($i=="06")
						{{$mm="Июнь"}}
					@elseif ($i=="07")
						{{$mm="Июль"}}
					@elseif ($i=="08")
						{{$mm="Август"}}
					@elseif ($i=="09")
						{{$mm="Сентябрь"}}
					@elseif ($i=="10")
						{{$mm="Октябрь"}}
					@elseif ($i=="11")
						{{$mm="Ноябрь"}}
					@elseif ($i=="12")
						{{$mm="Декабрь"}}
					@endif-->
				@if ($i==1 or $i==7)
					<div class="col-xs-2" style="border: solid 1px #000; padding: 0; margin: 2px 0;">
						<div class="col-xs-12" style="border-bottom: solid 1px #999"><strong>{{$mm}}</strong></div>
						<div class="col-xs-6" style="padding: 0; border-bottom: solid 1px #999" >+</div>
						<div class="col-xs-6" style="padding: 0; border-left: 1px solid #999; border-bottom: solid 1px #999;">-</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['plus']:0,2)>0) color: green; @endif">{{round($stats?$stats['plus']:0,2)}}</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['minus']:0,2)>0) color: red; @endif border-left: 1px solid #999;">{{round($stats?$stats['minus']:0,2)}}</div>
					</div>
				@else
					<div class="col-xs-2" style="border: solid 1px #000; border-left: 0; padding: 0; margin: 2px 0;">
						<div class="col-xs-12" style="border-bottom: solid 1px #999"><strong>{{$mm}}</strong></div>
						<div class="col-xs-6" style="padding: 0; border-bottom: solid 1px #999">+</div>
						<div class="col-xs-6" style="padding: 0; border-left: 1px solid #999; border-bottom: solid 1px #999">-</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['plus']:0,2)>0) color: green; @endif">{{round($stats?$stats['plus']:0,2)}}</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; border-left: 1px solid #999; @if(round($stats?$stats['minus']:0,2)>0) color: red; @endif">{{round($stats?$stats['minus']:0,2)}}</div>
					</div>
				@endif
			@endfor
		@else
			@for ($i = 1; $i <= $l; $i++)
				<!--@if ($i<10)
					{{$g='0' . $i . ''}}
				@else
					{{$g=$i}}
				@endif-->
				<!--
				{{$day=$year . '-' . $month . '-' . $g . ''}}
				-->
				<!--{{($stats=$valut->monthStat($valut->id, $i, $month, $year))?"":""}}-->
				@if ($i==1 or $i==7 or $i==13 or $i==19 or $i==25 or $i==31)
					<div class="col-xs-2" style="border: solid 1px #000; padding: 0; margin: 2px 0;">
						<div class="col-xs-12" style="border-bottom: solid 1px #999"><a href="{{route('lbtc.table.day', ['id'=>$valut->id, 'day'=>$day])}}"><strong>{{$i}}.{{$month}}</strong></a></div>
						<div class="col-xs-6" style="padding: 0; border-bottom: solid 1px #999" >+</div>
						<div class="col-xs-6" style="padding: 0; border-left: 1px solid #999; border-bottom: solid 1px #999;">-</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['plus']:0,2)>0) color: green; @endif">{{round($stats?$stats['plus']:0,2)}}</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['minus']:0,2)>0) color: red; @endif border-left: 1px solid #999;">{{round($stats?$stats['minus']:0,2)}}</div>
					</div>
				@else
					<div class="col-xs-2" style="border: solid 1px #000; border-left: 0; padding: 0; margin: 2px 0;">
						<div class="col-xs-12" style="border-bottom: solid 1px #999"><a href="{{route('lbtc.table.day', ['id'=>$valut->id, 'day'=>$day])}}"><strong>{{$i}}.{{$month}}</strong></a></div>
						<div class="col-xs-6" style="padding: 0; border-bottom: solid 1px #999">+</div>
						<div class="col-xs-6" style="padding: 0; border-left: 1px solid #999; border-bottom: solid 1px #999">-</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['plus']:0,2)>0) color: green; @endif">{{round($stats?$stats['plus']:0,2)}}</div>
						<div class="col-xs-6" style="padding: 0; font-weight: bold; @if(round($stats?$stats['minus']:0,2)>0) color: red; @endif border-left: 1px solid #999;">{{round($stats?$stats['minus']:0,2)}}</div>
					</div>
				@endif
			@endfor
		@endif
    </div>
</div>
@endsection
