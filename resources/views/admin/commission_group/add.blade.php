@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-xs-8 col-xs-offset-2 moder_pad">
			<form class="form-horizontal" role="form" method="post" action="">
				<div class="form-group">
					<label for="category" class="col-xs-4 control-label">Категория коммиссии</label>
					<div class="col-xs-8">
						<select name="category" class="form-control">
							<option>Коммиссия товарного виджета</option>
							<option>Коммиссия видео виджета</option>
							<option>Коммиссия менеджера</option>
							<option>Коммиссия реферальной программы</option>
						</select>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection