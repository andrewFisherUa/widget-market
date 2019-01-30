@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@include('admin.video_settings.top_menu')
	</div>
	<div class="row">
		<h4 class="text-center">Создание блока ссылок</h4>
		<div class="col-xs-12">
			<form class="form-horizontal" role="form" method="POST" action="{{ route('video_setting.block.save') }}">
			{{ csrf_field() }}
				<div class="form-group">
					<label for="type" class="col-xs-4 control-label">Тип</label>
					<div class="col-xs-6">
						<select name="type" class="form-control block_links">
									<option value="0">Не выбрано</option>
									<option value="1">Васт ссылки</option>	
								</select>
					</div>
					</div>
					<div class="form-group">
					<label for="title" class="col-xs-4 control-label">Название</label>
					<div class="col-xs-6">
						<input id="title" type="text" class="form-control" name="title" value="" required autofocus>
					</div>
				</div>
				<div class="form-group">
					<label for="link" class="col-xs-4 control-label">Ссылки</label>
					<div class="col-xs-6">
						<div id="sortable">
							<div class="JsSel">
								<select name="url[]" class="form-control block_links">
									<option value="0">Не выбрано</option>
										@foreach($sources as $source)
											<option value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
										@endforeach
								</select>
								<span class="btn btn-danger minus pull-right">–</span>
								<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
							</div>
							<div class="information_json_plus text-center"></div>
						</div>
						<div class="text-center">
							<span class="btn btn-success plus">Добавить ссылку</span>
						</div>
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
</div>
@endsection
@push('cabinet_home')
	<style>
		.block_links{
			width: 80%;
			display: inline-block;
		}
		.minus{
			display: inline-block;
		}
		.sortable{
			display: inline-block;
			height: 36px;
		}
		.JsSel{
			margin: 5px 0;
		}
		.JsSel:first-child{
			margin: 0;
		}
		.plus{
			margin-top: 10px;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script>
		jQuery('.plus').click(function(){
			jQuery('.information_json_plus').before(
			'<div class="JsSel">' + 
			'<select name="url[]" class="form-control block_links">' +
			'<option value="0">Не выбрано</option>@foreach($sources as $source) <option value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option> @endforeach</select>' + 
			'<span class="btn btn-danger minus pull-right">&ndash;</span>' + 
			'<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>' +
			'</div>'
			);
		});

		jQuery(document).on('click', '.minus', function(){
			jQuery( this ).closest( 'div' ).remove(); // удаление строки с полями
		});
	</script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$( function() {
			$( "#sortable" ).sortable();
		});
	</script>
@endpush