<div class="row main-alert">
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{!! __(session('message_danger')) !!}
		</div>
	@endif
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{!! __(session('message_success')) !!}
		</div>
	@endif
	@if (Session::has('message_info'))
		<div class="alert alert-info">
			{!! __(session('message_info')) !!}
		</div>
	@endif
	@if (Session::has('message_warning'))
		<div class="alert alert-warning">
			{!! __(session('message_warning')) !!}
		</div>
	@endif
</div>
<div class="row">
	<a href="{{route('money.index')}}" class="btn btn-primary btn-xs" target="_blank">Главная</a>
	<button data-toggle="modal" data-target="#add_accounte" data-backdrop="static" class="btn btn-primary btn-xs" target="_blank">Добавить систему/счет</button>
	<button data-toggle="modal" data-target="#add_valut_type" data-backdrop="static" class="btn btn-primary btn-xs" target="_blank">Добавить валюту</button>
	<a href="{{route('money.reports')}}" class="btn btn-primary btn-xs" target="_blank">Отчеты</a>
	<a href="{{route('money.report.time')}}" class="btn btn-primary btn-xs" target="_blank">По датам</a>
</div>
@php
//var_dump($cources);
@endphp
<div class="row">
	<p>Действующий отсчет от {{$report->opened}}<a href="{{route('money.report.closed', ['id'=>$report->id])}}" class="btn btn-danger btn-xs">закрыть отчет</a>
		<span style="float: right">
		@if ($report->summa_closed)
			{{$report->summa_closed}} руб / {{round($report->summa_closed/$cources->usd,2)}} usd / {{round($report->summa_closed/$cources->btc,8)}} btc
			@else
			{{$report->summa_opened}} руб / {{$cources->usd}}-{{$cources->btc}} ->{{round($report->summa_opened/$cources->usd,2)}} --> {round($report->summa_opened/$cources->btc,8)}} 

			@endif
		@if(1==0)
			@if ($report->summa_closed)
			{{$report->summa_closed}} руб / {{round($report->summa_closed/$cources->usd,2)}} usd / {{round($report->summa_closed/$cources->btc,8)}} btc
			@else
			{{$report->summa_opened}} руб / {{round($report->summa_opened/$cources->usd,2)}} usd / {{round($report->summa_opened/$cources->btc,8)}} btc
			@endif
		@endif	
		</span>

	        {{--
		<span style="float: right">
			@if ($report->summa_closed)
			{{$report->summa_closed}} руб / {{round($report->summa_closed/$cources->usd,2)}} usd / {{round($report->summa_closed/$cources->btc,8)}} btc
			@else
			{{$report->summa_opened}} руб / {{round($report->summa_opened/$cources->usd,2)}} usd / {{round($report->summa_opened/$cources->btc,8)}} btc
			@endif
		
		</span>
--}}
	</p>
</div>

<div id="add_accounte" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="affiliate_modal_header">Добавление системы/счета<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{route('money.add.account')}}">
				{!! csrf_field() !!}
				<div class="form-group">
					<label for="valut" class="col-xs-4 control-label">Название системы/счета</label>
					<div class="col-xs-6">
						<input name="title" type="text" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="account_balance" class="col-xs-4 control-label">Остаток на счете</label>
					<div class="col-xs-6">
						<input name="summa" type="text" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="account_balance" class="col-xs-4 control-label">Выберите валюту</label>
					<div class="col-xs-6">
						<!--{{$valutes=App\MoneyReport\Valute::all()}}-->
						<select name="valut" class="form-control" required>
							<option value="no">Выберите валюту</option>
							@foreach ($valutes as $valute)
								<option value="{{$valute->shortcode}}">{{$valute->title}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="account_balance" class="col-xs-4 control-label">Отметить как карта</label>
					<div class="col-xs-6" style="margin-top: 8px;">
						<input name="card" type="checkbox">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="add_valut_type" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="affiliate_modal_header">Добавление валюты<button class="modal_exit glyphicon glyphicon-remove-sign" type="button" data-dismiss="modal" data-toggle="tooltip" data-placement="bottom" title="Закрыть"></button></div>
			<hr class="modal_hr">
			<form class="form-horizontal" role="form" method="post" action="{{route('money.add.valute')}}">
				{!! csrf_field() !!}
				<div class="form-group">
					<label for="valut" class="col-xs-4 control-label">Название валюты</label>
					<div class="col-xs-6">
						<input name="title" type="text" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="account_balance" class="col-xs-4 control-label">Шорткод</label>
					<div class="col-xs-6">
						<input name="shortcode" type="text" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-1 col-xs-10 text-center">
						<button type="submit" class="btn btn-primary">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@push('cabinet_home')
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush