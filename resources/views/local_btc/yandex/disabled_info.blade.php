@extends('layouts.app')

@section('content')
<div class="container">
	@include('local_btc.top_menu')
	<div class="row">
		<h3 class="text-center">Отключенные покупки за Yandex</h3>
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
				Кто выключил
			</div>
		</div>
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
					{{$yandex->logs}}
				</div>
			</div>
		@endforeach
		</form>
    </div>
</div>
@endsection
@push('cabinet_home')

@endpush
@push('cabinet_home_js')
	
@endpush

