@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@include('admin.video_settings.top_menu')
	</div>
	<div class="row">
		<h4 class="text-center">Дефолтовые настройки {{$default->name}}</h4>
		<form class="form-horizontal" role="form" method="POST" action="{{ route('video_setting.default.save') }}">
		{{ csrf_field() }}
			<input type="text" name="id" value="{{$default->id}}" style="display: none" hidden>
			<div class="form-group">
				<label for="name" class="col-xs-4 control-label">Название</label>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="name" value="{{$default->name}}" readonly>
				</div>
			</div>
			<div class="form-group">
				<label for="block_rus" class="col-xs-4 control-label">Блок Россия</label>
				<div class="col-xs-6">
					<select class="form-control" name="block_rus">
						@foreach ($blocks as $block)
							<option @if ($default->block_rus==$block->id) selected @endif value="{{$block->id}}">{{$block->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="block_mobile" class="col-xs-4 control-label">Блок Мобильный</label>
				<div class="col-xs-6">
					<select class="form-control" name="block_mobile">
						@foreach ($blocks as $block)
							<option @if ($default->block_mobile==$block->id) selected @endif value="{{$block->id}}">{{$block->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="block_cis" class="col-xs-4 control-label">Блок СНГ</label>
				<div class="col-xs-6">
					<select class="form-control" name="block_cis">
						@foreach ($blocks as $block)
							<option @if ($default->block_cis==$block->id) selected @endif value="{{$block->id}}">{{$block->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="commission_rus" class="col-xs-4 control-label">Группа коммиссий Россия</label>
				<div class="col-xs-6">
					<select class="form-control" name="commission_rus">
						@foreach ($commissions as $commission)
							<option @if ($default->commission_rus==$commission->commissiongroupid) selected @endif value="{{$commission->commissiongroupid}}">{{$commission->label}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="commission_cis" class="col-xs-4 control-label">Группа коммиссий СНГ</label>
				<div class="col-xs-6">
					<select class="form-control" name="commission_cis">
						@foreach ($commissions as $commission)
							<option @if ($default->commission_cis==$commission->commissiongroupid) selected @endif value="{{$commission->commissiongroupid}}">{{$commission->label}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-6 col-xs-offset-4 text-center">
					<button type="submit" class="btn btn-primary">
						Сохранить
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection