@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
	<div class="row">
		<a href="{{route('lbtc.yandex.robot.disabled.info')}}" class="btn btn-success">Выключение покупки</a>
	</div>
	<div class="row">
		<h3 class="text-center">Покупки за Yandex которые считаются в актуальный курс</h3>
		<div class="col-xs-12">
			<div class="col-xs-2">
				Дата создания
			</div>
			<div class="col-xs-2">
				Сумма в рублях
			</div>
			<div class="col-xs-2">
				Сумма в битках
			</div>
			<div class="col-xs-2">
				Осталось продать
			</div>
			<div class="col-xs-2">
				Курс
			</div>
			<div class="col-xs-2">
				Не учитывать
			</div>
		</div>
		<form class="form-horizontal" role="form" method="post">
		{{ csrf_field() }}
		@foreach ($yandexs as $yandex)
			<div class="col-xs-12" style="border-bottom: solid 1px #000;">
				<div class="col-xs-2">
					{{$yandex->created}}
				</div>
				<div class="col-xs-2">
					{{$yandex->amount}}
				</div>
				<div class="col-xs-2">
					{{$yandex->amount_btc}}
				</div>
				<div class="col-xs-2">
					{{$yandex->remainder}}
				</div>
				<div class="col-xs-2">
					{{$yandex->course_fact}}
				</div>
				<div class="col-xs-2">
					<input type="checkbox" name="status[]" value="{{$yandex->id}}">
				</div>
			</div>
		@endforeach
			<div class="col-xs-12 text-center" style="margin: 5px 0;">
				<button type="submit" class="btn btn-primary">Сохранить</button>
			</div>
		</form>
    </div>
</div>
@endsection
@push('cabinet_home')

@endpush
@push('cabinet_home_js')
	
@endpush

