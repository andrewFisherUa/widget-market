<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
	</head>
<body>
		<table class="table table-bordered" style="table-layout: fixed; margin-top: 20px">
			<thead>
				<tr>
					<td rowspan="2" style="width: 140px">Остаток на счете</td>
					<td rowspan="2" style="width: 140px">Система/Счет</td>
					<td rowspan="2" style="width: 630px">Объем сутки</td>
					<td colspan="2" style="width: 160px">Объем месяц</td>
				</tr>
				<tr>
					<td>+</td>
					<td>-</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($valuts as $valut)
					<tr>
						<td>
						</td>
						<td>{{$valut->title}}</td>
					</tr>
				@endforeach
				<tr>
					<td>1000000</td>
					<td>Яндекс деньги (онлайн)</td>
					<td style="padding: 0; dispay: block; overflow: auto;">
						<table class="table-bordered">
							<tr>
								<td colspan="2">01.12</td>
								<td colspan="2">02.12</td>
								<td colspan="2">03.12</td>
								<td colspan="2">04.12</td>
								<td colspan="2">05.12</td>
								<td colspan="2">06.12</td>
								<td colspan="2">07.12</td>
								<td colspan="2">08.12</td>
								<td colspan="2">09.12</td>
								<td colspan="2">10.12</td>
							</tr>
							<tr>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
							</tr>
							<tr>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
							</tr>
						</table>
					</td>
					<td>1000000</td>
					<td>1000000</td>
				</tr>
				<tr>
					<td>1000000</td>
					<td>Яндекс деньги (онлайн)</td>
					<td style="padding: 0; dispay: block; overflow: auto;">
						<table class="table-bordered">
							<tr>
								<td colspan="2">01.12</td>
								<td colspan="2">02.12</td>
								<td colspan="2">03.12</td>
								<td colspan="2">04.12</td>
								<td colspan="2">05.12</td>
								<td colspan="2">06.12</td>
								<td colspan="2">07.12</td>
								<td colspan="2">08.12</td>
								<td colspan="2">09.12</td>
								<td colspan="2">10.12</td>
							</tr>
							<tr>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
								<td style="min-width: 80px">+</td>
								<td style="min-width: 80px">-</td>
							</tr>
							<tr>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
								<td style="min-width: 80px">1000000</td>
							</tr>
						</table>
					</td>
					<td>1000000</td>
					<td>1000000</td>
				</tr>
			</tbody>
		</table>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>