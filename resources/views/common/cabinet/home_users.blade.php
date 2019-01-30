<div class="row" style="margin: 10px 0">
	<div class="col-xs-12">
		<!--{{$all_managers=\App\User::whereHas('roles', function ($query) {
			$query->whereIn('id', ['3','4','5']);
			})->orderBy('name', 'asc')->get()}}-->
		<form class="form-inline" id="form_for_users" role="form" method="get" action="">
			<div class="input-group col-xs-2 form-group">
				<span class="input-group-addon">С:</span>
				<input type="text" id="from_for_users" class="form-control" value="{{$from}}" name="from">
			</div>
			<div class="input-group col-xs-2 form-group">
				<span class="input-group-addon">По:</span>
				<input type="text" id="to_for_users" class="form-control" value="{{$to}}" name="to">
			</div>
			<div class="input-group col-xs-2 form-group">
				<span class="input-group-addon">Поиск:</span>
				<input type="text" class="form-control" id="search_clent" value="{{$search}}" name="search">
			</div>
			@if (\Auth::user()->hasRole('admin'))
				<div class="input-group col-xs-2 form-group">
					<select name="manager" id="manager_for_client" class="form-control">
						<option @if ($manager=='0') selected @endif value="0">Все менеджеры</option>
						<option @if ($manager=='no_manager') selected @endif value="no_manager">Без менеджера</option>
						@foreach ($all_managers as $one_manager)
							<option @if ($manager==$one_manager->id) selected @endif value="{{$one_manager->id}}">{{$one_manager->name}}</option>
						@endforeach
					</select>
				</div>
			@elseif (\Auth::user()->hasRole('super_manager'))
				<div class="input-group col-xs-2 form-group">
					<select name="manager" id="manager_for_client" class="form-control">
						<option @if ($manager=='0') selected @endif value="0">Мои клиенты</option>
						<option @if ($manager=='no_manager') selected @endif value="no_manager">Без менеджера</option>
					</select>
				</div>
			@endif
			<div class="input-group col-xs-1 form-group">
				<select name="number" id="number_for_client" class="form-control">
					<option @if ($number==5) selected @endif value="5">5</option>
					<option @if ($number==10) selected @endif value="10">10</option>
					<option @if ($number==15) selected @endif value="15">15</option>
					<option @if ($number==20) selected @endif value="20">20</option>
					<option @if ($number==30) selected @endif value="30">30</option>
					<option @if ($number==50) selected @endif value="50">50</option>
					<option @if ($number==100) selected @endif value="100">100</option>
				</select>
			</div>
			<div class="input-group col-xs-1 form-group">
				<select name="sorty" id="sorty" class="form-control">
					<option value="0">Все</option>
					<option value="1">Автоплей</option>
					<option value="2">Оверлей</option>
					<option value="3">Vast</option>
					<option value="7">InPage</option>
					<option value="4">Топадверт</option>
					<option value="5">Яндекс</option>
					<option value="6">Тизерка</option>
					<option value="8">Autoplay muted</option>
				</select>
			</div>
			<div class="col-xs-1 input-group form-group">
				<a id="submit" class="btn btn-primary">Применить</a>
			</div>
		</form>
	</div>
</div>
<div class="row" style="margin-top: 10px; margin-left: 0px;">
	<div class="col-xs-12">
		<a href="{{route('global.trash_users')}}" target="_blank" class="btn btn-primary">Не активные юзеры</a>
		@if (\Auth::user()->id==16)
		<a href="{{route('cabinet_blocks.secret_alex')}}" target="_blank" class="btn btn-primary">Secret Alex Page</a>
		@endif
	</div>
</div>
<div class="affiliate_cabinet_bot" style="margin-top: 10px;">
	<ul class="nav nav-tabs nav-justified cust-tabs" role="tablist" id="myTabs">
		<li role="presentation" class="active"><a href="#all_stat" aria-controls="all_stat" role="tab" data-toggle="tab">Суммарная</a></li>
		<li role="presentation"><a id="get_users_video_widgets" href="#video_stat" aria-controls="video_stat" role="tab" data-toggle="tab">Видео</a></li>
		<li role="presentation"><a id="get_users_product_widgets" href="#product_stat" aria-controls="product_stat" role="tab" data-toggle="tab">Товарный</a></li>
		<li role="presentation"><a id="get_users_teaser_widgets" href="#teaser_stat" aria-controls="teaser_stat" role="tab" data-toggle="tab">Тизерный</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="all_stat">
			<div class="affiliate_cabinet_block" style="margin-top: 10px;">
				<div class="loaded">
							
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="video_stat">
			<div class="affiliate_cabinet_block" style="margin-top: 10px;">
				<div class="loaded">
							
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="product_stat">
			<div class="affiliate_cabinet_block" style="margin-top: 10px;">
				<div class="loaded">
							
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="teaser_stat">
			<div class="affiliate_cabinet_block" style="margin-top: 10px;">
				<div class="loaded">
							
				</div>
			</div>
		</div>
	</div>
</div>