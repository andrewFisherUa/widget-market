@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
			<div class="alert alert-success">
				{{ session('message_success') }}
			</div>
		@endif
		@if (Session::has('message_warning'))
			<div class="alert alert-warning">
				{{ session('message_warning') }}
			</div>
		@endif
	</div>
	<div class="row">
		<h4 class="text-center">Редактирование блока брендирования {{$block->title}}</h4>
			<form class="form-horizontal" enctype="multipart/form-data" method="post" action="">
			{!! csrf_field() !!}
				<div class="form-group @if ($errors->has('title')) has-error @endif">
					<label for="title" class="col-xs-4 control-label">Название</label>
					<div class="col-xs-6">
						<input type="text" name="title" value="{{$block->title}}" class="form-control" required>
						@if ($errors->has('title'))
							<span class="help-block">
								<strong>{{ $errors->first('title') }}</strong>
							</span>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="link" class="col-xs-4 control-label">Ссылки</label>
					<div class="col-xs-6">
						<div id="sortable">
							@foreach ($block->Sources as $selected)
										<div class="JsSel">
											<select name="url[]" class="form-control block_links">
												<option value="0">Не выбрано</option>
												@foreach($sources as $source)
													<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}}</option>
												@endforeach
											</select>
											<span class="btn btn-danger minus pull-right">–</span>
											<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
										</div>
									@endforeach
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
			'<option value="0">Не выбрано</option>@foreach($sources as $source) <option value="{{$source->id}}">{{$source->title}}</option> @endforeach</select>' + 
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
			$( "#sortable" ).disableSelection();
		});
	</script>
@endpush