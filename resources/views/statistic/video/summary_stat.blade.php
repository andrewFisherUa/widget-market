@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				<form class="form-inline" role="form" method="get">
					<div class="row">
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">С:</span>
							<input type="text" class="form-control" value="{{$from}}" name="from">
						</div>
						<div class="input-group col-xs-2 form-group">
							<span class="input-group-addon">По:</span>
							<input type="text" class="form-control" value="{{$to}}" name="to">
						</div>
						<div class="input-group col-xs-2 form-group">
							<select name="number" class="form-control">
								<option @if ($number==5) selected @endif value="5">5</option>
								<option @if ($number==10) selected @endif value="10">10</option>
								<option @if ($number==15) selected @endif value="15">15</option>
								<option @if ($number==20) selected @endif value="20">20</option>
								<option @if ($number==30) selected @endif value="30">30</option>
								<option @if ($number==50) selected @endif value="50">50</option>
								<option @if ($number==100) selected @endif value="100">100</option>
							</select>
						</div>
						<div class="input-group col-xs-2 form-group">
							<select name='manager' class="form-control">
								<option value="0">Все</option>
								@if (\Auth::user()->hasRole('admin'))
									@foreach (\App\Role::whereIn('id', [3,4,5])->get() as $role)
										@foreach ($role->users as $user)
											<option @if ($manager==$user->id) selected @endif value="{{$user->id}}">{{$user->name}}</option>
										@endforeach
									@endforeach
								@else
									<option @if ($manager==\Auth::user()->id) selected @endif value="{{\Auth::user()->id}}">{{\Auth::user()->name}}</option>
								@endif
							</select>
						</div>
						<div class="input-group col-xs-1 form-group">
							Без adwise<input type="checkbox" @if ($ole==1) checked @endif name="ole" value="1">
						</div>
						<div class="col-xs-2 input-group form-group">
							<button type="submit" class="btn btn-primary">Применить</button>
						</div>
					</div>
				</form>
			</div>
		</div>
			<div class="row">
				<h4 class="text-center">Суммарная видео статистика в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
				<div class="col-xs-12">
					{!! $summarys->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
					<div>
						<ul class="nav nav-tabs nav-justified cust-tabs">
							<li class="heading text-left active"><a href="#summary_stat" data-toggle="tab">Общая статистика</a></li>
							<li class="heading text-left"><a href="#rus_stat" data-toggle="tab">Статистика по России</a></li>
							<li class="heading text-left"><a href="#cis_stat" data-toggle="tab">Статистика по СНГ</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="summary_stat">
								<ul class="nav nav-tabs nav-justified cust-tabs">
									<li class="heading text-left active"><a href="#summary_stat_all" data-toggle="tab">Общая статистика</a></li>
									<li class="heading text-left"><a href="#summary_stat_pc" data-toggle="tab">ПК</a></li>
									<li class="heading text-left"><a href="#summary_stat_mobile" data-toggle="tab">Мобильные</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="summary_stat_all">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$summary_all->loaded}}</td>
												<td>{{$summary_all->played}}</td>
												<td>{{$summary_all->calculate}}</td>
												<td>{{$summary_all->deep}}</td>
												<td>{{$summary_all->util}}</td>
												<td>{{$summary_all->dosm}}</td>
												<td>{{$summary_all->clicks}}</td>
												<td>{{$summary_all->ctr}}</td>
												<td>{{$summary_all->second}}</td>
												<td>{{$summary_all->second_all}}</td>
												<td>{{$summary_all->second_summa}}</td>
												<td>{{$summary_all->summa}}</td>
												<td>{{$summary_all->coef}}</td>
												<td>{{$summary_all->viewable}}</td>
											</tr>
											@foreach ($summarys as $summary)
												<tr>
													<td>{{$summary->day}}</td>
													<td>{{$summary->loaded}}</td>
													<td>{{$summary->played}}</td>
													<td>{{$summary->calculate}}</td>
													<td>{{$summary->deep}}</td>
													<td>{{$summary->util}}</td>
													<td>{{$summary->dosm}}</td>
													<td>{{$summary->clicks}}</td>
													<td>{{$summary->ctr}}</td>
													<td>{{$summary->second}}</td>
													<td>{{$summary->second_all}}</td>
													<td>{{$summary->second_summa}}</td>
													<td>{{$summary->summa}}</td>
													<td>{{$summary->coef}}</td>
													<td>{{$summary->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
									<div class="tab-pane" id="summary_stat_pc">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$summary_all_pc->loaded}}</td>
												<td>{{$summary_all_pc->played}}</td>
												<td>{{$summary_all_pc->calculate}}</td>
												<td>{{$summary_all_pc->deep}}</td>
												<td>{{$summary_all_pc->util}}</td>
												<td>{{$summary_all_pc->dosm}}</td>
												<td>{{$summary_all_pc->clicks}}</td>
												<td>{{$summary_all_pc->ctr}}</td>
												<td>{{$summary_all_pc->second}}</td>
												<td>{{$summary_all_pc->second_all}}</td>
												<td>{{$summary_all_pc->second_summa}}</td>
												<td>{{$summary_all_pc->summa}}</td>
												<td>{{$summary_all_pc->coef}}</td>
												<td>{{$summary_all_pc->viewable}}</td>
											</tr>
											@foreach ($summarys_pc as $summary_pc)
												<tr>
													<td>{{$summary_pc->day}}</td>
													<td>{{$summary_pc->loaded}}</td>
													<td>{{$summary_pc->played}}</td>
													<td>{{$summary_pc->calculate}}</td>
													<td>{{$summary_pc->deep}}</td>
													<td>{{$summary_pc->util}}</td>
													<td>{{$summary_pc->dosm}}</td>
													<td>{{$summary_pc->clicks}}</td>
													<td>{{$summary_pc->ctr}}</td>
													<td>{{$summary_pc->second}}</td>
													<td>{{$summary_pc->second_all}}</td>
													<td>{{$summary_pc->second_summa}}</td>
													<td>{{$summary_pc->summa}}</td>
													<td>{{$summary_pc->coef}}</td>
													<td>{{$summary_pc->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
									<div class="tab-pane" id="summary_stat_mobile">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$summary_all_mob->loaded}}</td>
												<td>{{$summary_all_mob->played}}</td>
												<td>{{$summary_all_mob->calculate}}</td>
												<td>{{$summary_all_mob->deep}}</td>
												<td>{{$summary_all_mob->util}}</td>
												<td>{{$summary_all_mob->dosm}}</td>
												<td>{{$summary_all_mob->clicks}}</td>
												<td>{{$summary_all_mob->ctr}}</td>
												<td>{{$summary_all_mob->second}}</td>
												<td>{{$summary_all_mob->second_all}}</td>
												<td>{{$summary_all_mob->second_summa}}</td>
												<td>{{$summary_all_mob->summa}}</td>
												<td>{{$summary_all_mob->coef}}</td>
												<td>{{$summary_all_mob->viewable}}</td>
											</tr>
											@foreach ($summarys_mob as $summary_mob)
												<tr>
													<td>{{$summary_mob->day}}</td>
													<td>{{$summary_mob->loaded}}</td>
													<td>{{$summary_mob->played}}</td>
													<td>{{$summary_mob->calculate}}</td>
													<td>{{$summary_mob->deep}}</td>
													<td>{{$summary_mob->util}}</td>
													<td>{{$summary_mob->dosm}}</td>
													<td>{{$summary_mob->clicks}}</td>
													<td>{{$summary_mob->ctr}}</td>
													<td>{{$summary_mob->second}}</td>
													<td>{{$summary_mob->second_all}}</td>
													<td>{{$summary_mob->second_summa}}</td>
													<td>{{$summary_mob->summa}}</td>
													<td>{{$summary_mob->coef}}</td>
													<td>{{$summary_mob->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
								</div>
							</div>
							
							<div class="tab-pane" id="rus_stat">
								<ul class="nav nav-tabs nav-justified cust-tabs">
									<li class="heading text-left active"><a href="#rus_stat_all" data-toggle="tab">Общая статистика</a></li>
									<li class="heading text-left"><a href="#rus_stat_pc" data-toggle="tab">ПК</a></li>
									<li class="heading text-left"><a href="#rus_stat_mobile" data-toggle="tab">Мобильные</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="rus_stat_all">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$ru_summary_all->loaded}}</td>
												<td>{{$ru_summary_all->played}}</td>
												<td>{{$ru_summary_all->calculate}}</td>
												<td>{{$ru_summary_all->deep}}</td>
												<td>{{$ru_summary_all->util}}</td>
												<td>{{$ru_summary_all->dosm}}</td>
												<td>{{$ru_summary_all->clicks}}</td>
												<td>{{$ru_summary_all->ctr}}</td>
												<td>{{$ru_summary_all->second}}</td>
												<td>{{$ru_summary_all->second_all}}</td>
												<td>{{$ru_summary_all->second_summa}}</td>
												<td>{{$ru_summary_all->summa}}</td>
												<td>{{$ru_summary_all->coef}}</td>
												<td>{{$ru_summary_all->viewable}}</td>
											</tr>
											@foreach ($ru_summarys as $ru_summary)
												<tr>
													<td>{{$ru_summary->day}}</td>
													<td>{{$ru_summary->loaded}}</td>
													<td>{{$ru_summary->played}}</td>
													<td>{{$ru_summary->calculate}}</td>
													<td>{{$ru_summary->deep}}</td>
													<td>{{$ru_summary->util}}</td>
													<td>{{$ru_summary->dosm}}</td>
													<td>{{$ru_summary->clicks}}</td>
													<td>{{$ru_summary->ctr}}</td>
													<td>{{$ru_summary->second}}</td>
													<td>{{$ru_summary->second_all}}</td>
													<td>{{$ru_summary->second_summa}}</td>
													<td>{{$ru_summary->summa}}</td>
													<td>{{$ru_summary->coef}}</td>
													<td>{{$ru_summary->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
									<div class="tab-pane" id="rus_stat_pc">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$ru_summary_all_pc->loaded}}</td>
												<td>{{$ru_summary_all_pc->played}}</td>
												<td>{{$ru_summary_all_pc->calculate}}</td>
												<td>{{$ru_summary_all_pc->deep}}</td>
												<td>{{$ru_summary_all_pc->util}}</td>
												<td>{{$ru_summary_all_pc->dosm}}</td>
												<td>{{$ru_summary_all_pc->clicks}}</td>
												<td>{{$ru_summary_all_pc->ctr}}</td>
												<td>{{$ru_summary_all_pc->second}}</td>
												<td>{{$ru_summary_all_pc->second_all}}</td>
												<td>{{$ru_summary_all_pc->second_summa}}</td>
												<td>{{$ru_summary_all_pc->summa}}</td>
												<td>{{$ru_summary_all_pc->coef}}</td>
												<td>{{$ru_summary_all_pc->viewable}}</td>
											</tr>
											@foreach ($ru_summarys_pc as $ru_summary_pc)
												<tr>
													<td>{{$ru_summary_pc->day}}</td>
													<td>{{$ru_summary_pc->loaded}}</td>
													<td>{{$ru_summary_pc->played}}</td>
													<td>{{$ru_summary_pc->calculate}}</td>
													<td>{{$ru_summary_pc->deep}}</td>
													<td>{{$ru_summary_pc->util}}</td>
													<td>{{$ru_summary_pc->dosm}}</td>
													<td>{{$ru_summary_pc->clicks}}</td>
													<td>{{$ru_summary_pc->ctr}}</td>
													<td>{{$ru_summary_pc->second}}</td>
													<td>{{$ru_summary_pc->second_all}}</td>
													<td>{{$ru_summary_pc->second_summa}}</td>
													<td>{{$ru_summary_pc->summa}}</td>
													<td>{{$ru_summary_pc->coef}}</td>
													<td>{{$ru_summary_pc->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
									<div class="tab-pane" id="rus_stat_mobile">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$ru_summary_all_mob->loaded}}</td>
												<td>{{$ru_summary_all_mob->played}}</td>
												<td>{{$ru_summary_all_mob->calculate}}</td>
												<td>{{$ru_summary_all_mob->deep}}</td>
												<td>{{$ru_summary_all_mob->util}}</td>
												<td>{{$ru_summary_all_mob->dosm}}</td>
												<td>{{$ru_summary_all_mob->clicks}}</td>
												<td>{{$ru_summary_all_mob->ctr}}</td>
												<td>{{$ru_summary_all_mob->second}}</td>
												<td>{{$ru_summary_all_mob->second_all}}</td>
												<td>{{$ru_summary_all_mob->second_summa}}</td>
												<td>{{$ru_summary_all_mob->summa}}</td>
												<td>{{$ru_summary_all_mob->coef}}</td>
												<td>{{$ru_summary_all_mob->viewable}}</td>
											</tr>
											@foreach ($ru_summarys_mob as $ru_summary_mob)
												<tr>
													<td>{{$ru_summary_mob->day}}</td>
													<td>{{$ru_summary_mob->loaded}}</td>
													<td>{{$ru_summary_mob->played}}</td>
													<td>{{$ru_summary_mob->calculate}}</td>
													<td>{{$ru_summary_mob->deep}}</td>
													<td>{{$ru_summary_mob->util}}</td>
													<td>{{$ru_summary_mob->dosm}}</td>
													<td>{{$ru_summary_mob->clicks}}</td>
													<td>{{$ru_summary_mob->ctr}}</td>
													<td>{{$ru_summary_mob->second}}</td>
													<td>{{$ru_summary_mob->second_all}}</td>
													<td>{{$ru_summary_mob->second_summa}}</td>
													<td>{{$ru_summary_mob->summa}}</td>
													<td>{{$ru_summary_mob->coef}}</td>
													<td>{{$ru_summary_mob->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
								</div>
							</div>
							
							<div class="tab-pane" id="cis_stat">
								<ul class="nav nav-tabs nav-justified cust-tabs">
									<li class="heading text-left active"><a href="#cis_stat_all" data-toggle="tab">Общая статистика</a></li>
									<li class="heading text-left"><a href="#cis_stat_pc" data-toggle="tab">ПК</a></li>
									<li class="heading text-left"><a href="#cis_stat_mobile" data-toggle="tab">Мобильные</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="cis_stat_all">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$cis_summary_all->loaded}}</td>
												<td>{{$cis_summary_all->played}}</td>
												<td>{{$cis_summary_all->calculate}}</td>
												<td>{{$cis_summary_all->deep}}</td>
												<td>{{$cis_summary_all->util}}</td>
												<td>{{$cis_summary_all->dosm}}</td>
												<td>{{$cis_summary_all->clicks}}</td>
												<td>{{$cis_summary_all->ctr}}</td>
												<td>{{$cis_summary_all->second}}</td>
												<td>{{$cis_summary_all->second_all}}</td>
												<td>{{$cis_summary_all->second_summa}}</td>
												<td>{{$cis_summary_all->summa}}</td>
												<td>{{$cis_summary_all->coef}}</td>
												<td>{{$cis_summary_all->viewable}}</td>
											</tr>
											@foreach ($cis_summarys as $cis_summary)
												<tr>
													<td>{{$cis_summary->day}}</td>
													<td>{{$cis_summary->loaded}}</td>
													<td>{{$cis_summary->played}}</td>
													<td>{{$cis_summary->calculate}}</td>
													<td>{{$cis_summary->deep}}</td>
													<td>{{$cis_summary->util}}</td>
													<td>{{$cis_summary->dosm}}</td>
													<td>{{$cis_summary->clicks}}</td>
													<td>{{$cis_summary->ctr}}</td>
													<td>{{$cis_summary->second}}</td>
													<td>{{$cis_summary->second_all}}</td>
													<td>{{$cis_summary->second_summa}}</td>
													<td>{{$cis_summary->summa}}</td>
													<td>{{$cis_summary->coef}}</td>
													<td>{{$cis_summary->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
									<div class="tab-pane active" id="cis_stat_pc">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$cis_summary_all_pc->loaded}}</td>
												<td>{{$cis_summary_all_pc->played}}</td>
												<td>{{$cis_summary_all_pc->calculate}}</td>
												<td>{{$cis_summary_all_pc->deep}}</td>
												<td>{{$cis_summary_all_pc->util}}</td>
												<td>{{$cis_summary_all_pc->dosm}}</td>
												<td>{{$cis_summary_all_pc->clicks}}</td>
												<td>{{$cis_summary_all_pc->ctr}}</td>
												<td>{{$cis_summary_all_pc->second}}</td>
												<td>{{$cis_summary_all_pc->second_all}}</td>
												<td>{{$cis_summary_all_pc->second_summa}}</td>
												<td>{{$cis_summary_all_pc->summa}}</td>
												<td>{{$cis_summary_all_pc->coef}}</td>
												<td>{{$cis_summary_all_pc->viewable}}</td>
											</tr>
											@foreach ($cis_summarys_pc as $cis_summary_pc)
												<tr>
													<td>{{$cis_summary_pc->day}}</td>
													<td>{{$cis_summary_pc->loaded}}</td>
													<td>{{$cis_summary_pc->played}}</td>
													<td>{{$cis_summary_pc->calculate}}</td>
													<td>{{$cis_summary_pc->deep}}</td>
													<td>{{$cis_summary_pc->util}}</td>
													<td>{{$cis_summary_pc->dosm}}</td>
													<td>{{$cis_summary_pc->clicks}}</td>
													<td>{{$cis_summary_pc->ctr}}</td>
													<td>{{$cis_summary_pc->second}}</td>
													<td>{{$cis_summary_pc->second_all}}</td>
													<td>{{$cis_summary_pc->second_summa}}</td>
													<td>{{$cis_summary_pc->summa}}</td>
													<td>{{$cis_summary_pc->coef}}</td>
													<td>{{$cis_summary_pc->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
									<div class="tab-pane active" id="cis_stat_mobile">
										<table class="table table-hover table-bordered" style="margin-top: 10px">
											<thead>
												<tr>
												@foreach($header as $k=>$row)
													<td>
														@if($row['index'])<a class="table_href" href="/{{$row['url']}}">{{$row['title']}}</a>@else {{$row['title']}} @endif
													</td>
												@endforeach
													<!--<td>Дата</td>
													<td>Загрузки</td>
													<td>Показы</td>
													<td>Зачтенные показы</td>
													<td>Глубина</td>
													<td>Утиль</td>
													<td>Досмотры</td>
													<td>Клики</td>
													<td>Ctr</td>
													<td>Доход</td>-->
												</tr>
											</thead>
											<tr style="background: black; color: white">
												<td>Всего</td>
												<td>{{$cis_summary_all_mob->loaded}}</td>
												<td>{{$cis_summary_all_mob->played}}</td>
												<td>{{$cis_summary_all_mob->calculate}}</td>
												<td>{{$cis_summary_all_mob->deep}}</td>
												<td>{{$cis_summary_all_mob->util}}</td>
												<td>{{$cis_summary_all_mob->dosm}}</td>
												<td>{{$cis_summary_all_mob->clicks}}</td>
												<td>{{$cis_summary_all_mob->ctr}}</td>
												<td>{{$cis_summary_all_mob->second}}</td>
												<td>{{$cis_summary_all_mob->second_all}}</td>
												<td>{{$cis_summary_all_mob->second_summa}}</td>
												<td>{{$cis_summary_all_mob->summa}}</td>
												<td>{{$cis_summary_all_mob->coef}}</td>
												<td>{{$cis_summary_all_mob->viewable}}</td>
											</tr>
											@foreach ($cis_summarys_mob as $cis_summary_mob)
												<tr>
													<td>{{$cis_summary_mob->day}}</td>
													<td>{{$cis_summary_mob->loaded}}</td>
													<td>{{$cis_summary_mob->played}}</td>
													<td>{{$cis_summary_mob->calculate}}</td>
													<td>{{$cis_summary_mob->deep}}</td>
													<td>{{$cis_summary_mob->util}}</td>
													<td>{{$cis_summary_mob->dosm}}</td>
													<td>{{$cis_summary_mob->clicks}}</td>
													<td>{{$cis_summary_mob->ctr}}</td>
													<td>{{$cis_summary_mob->second}}</td>
													<td>{{$cis_summary_mob->second_all}}</td>
													<td>{{$cis_summary_mob->second_summa}}</td>
													<td>{{$cis_summary_mob->summa}}</td>
													<td>{{$cis_summary_mob->coef}}</td>
													<td>{{$cis_summary_mob->viewable}}</td>
												</tr>
											@endforeach
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					{!! $summarys->appends(["from"=>$from, "to"=>$to, "number"=>$number, "order"=>$order, "direct"=>$direct])->render() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	<style>
		.table{
			text-align: center;
		}
		.table > thead > tr > th, .table > thead > tr > td, .table > tbody > tr > th, .table > tbody > tr > td, .table > tfoot > tr > th, .table > tfoot > tr > td{
			vertical-align: middle;
			border: 1px solid #ababab;
		}
		.body_sum{
			font-weight: bolder;
		}
		.celi_pok{
			display: inline-block!important;
			width: 10px;
			height: 10px;
		}
		.rur{
		font-style: normal;
		}
		.right_pok{
		display: inline-block;
		width: 200px;
		}
		.table_href{
		color: inherit;
		}
		.cust-tabs .active a{
			font-weight: 600;
			color: #3b4371!important;
		}
		.cust-tabs li a{
			color: #3b4371;
			letter-spacing: 1.1px;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
	<script>	
$(function() {
    $('input[name="from"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
	$('input[name="to"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
});	
</script>
@endpush