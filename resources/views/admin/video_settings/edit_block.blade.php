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
		@include('admin.video_settings.top_menu')
	</div>
	<div class="row">
		<h4 class="text-center">Редактирование блока {{$block->name}}</h4>
		<div class="col-xs-12">
			<form class="form-horizontal" role="form" method="POST" action="{{ route('video_setting.block.update', ['id'=>$block->id]) }}">
			{{ csrf_field() }}
			<div class="form-group">
				<label for="type" class="col-xs-4 control-label">Тип</label>
					<div class="col-xs-6">
						<select name="type" class="form-control block_links">
									<option value="0">Не выбрано</option>
									<option value="1" @if($block->type==1) selected @endif>Васт ссылки</option>	
								</select>
					</div>
					</div>
				<div class="form-group">
					<label for="title" class="col-xs-4 control-label">Название</label>
					<div class="col-xs-6">
						<input id="title" type="text" class="form-control" name="title" value="{{$block->name}}" required autofocus>
					</div>
				</div>
				<div class="form-group">
					<label for="cheap_util" class="col-xs-4 control-label">Учитывать дешевые ссылки</label>
					<div class="col-xs-6" style="margin-top: 8px;">
						<input type="checkbox" id="cheap_util" name="cheap_util" @if($block->cheap_util==1) checked @endif  value="1">
					</div>
				</div>
				<div class="form-group">
					<label for="repeat" class="col-xs-4 control-label">Повтор</label>
					<div class="col-xs-6">
						<select class="form-control block_links" id="repeat" name="repeat">
							@if ($block->cheap_util==0)
								<option @if ($block->repeat==0) selected @endif value="0">Не повторять</option>
								<option @if ($block->repeat==3) selected @endif value="3">Повторить все</option>
							@else
								<option @if ($block->repeat==0) selected @endif value="0">Не повторять</option>
								<option @if ($block->repeat==1) selected @endif value="1">Повторить только дорогие ссылки</option>
								<option @if ($block->repeat==2) selected @endif value="2">Повторить только дешевые ссылки</option>
								<option @if ($block->repeat==3) selected @endif value="3">Повторить все</option>
							@endif
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="autosort" class="col-xs-4 control-label">Автоматический порядок</label>
					<div class="col-xs-6" style="margin-top: 8px;">
						<input type="checkbox" id="autosort" name="autosort" @if($block->autosort==1) checked @endif  value="1">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-6">
						<div style="width: 80%">
							<label for="link" class="col-xs-12 text-center">
								Порядок предлагаемый автоматически
							</label>
						</div>
							@if ($block->cheap_util!=1)
							<div class="parent_auto_sort">
							@foreach ($block->SourcesOnAuto as $selected) 

								@foreach($sources as $source)
									@if ($selected->id==$source->id)
									<div class="form-control block_links auto_sort"><b>{{$selected->pivot->autosort}} ({{$selected->id}})</b>) {{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</div>
									@endif
								@endforeach
							@endforeach
							</div>
							@else
							<div style="width: 80%">
								<label for="link" class="col-xs-12 text-center">
									Дорогой блок(играет первым)
								</label>
							</div>
							<div class="parent_auto_sort">
								@foreach ($block->SourcesOnAuto as $selected)
									@if ($selected->cheap!=1)
										@foreach($sources as $source)
											@if ($selected->id==$source->id)
											<div class="form-control block_links auto_sort"> {{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</div>
											@endif
										@endforeach
									@endif
								@endforeach
							</div>
							<div style="width: 80%">
								<label for="link" class="col-xs-12 text-center">
									Дешевый блок(играет вторым)
								</label>
							</div>
							<div class="parent_auto_sort">
								@foreach ($block->SourcesOnAuto as $selected)
									@if ($selected->cheap==1)
										@foreach($sources as $source)
											@if ($selected->id==$source->id)
											<div class="form-control block_links auto_sort"> {{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</div>
											@endif
										@endforeach
									@endif
								@endforeach
							</div>
							@endif
					</div>
					<div class="col-xs-6">
						<label for="link" class="col-xs-12 text-center">
							Действующий порядок ссылок
						</label>
						<div id="sortable">
							@if ($block->cheap_util!=1)
								@if ($block->autosort==1) 
									@foreach ($block->SourcesOnAutoPrioritet as $selected)
										<div class="JsSel">
											<select name="url[]" class="form-control block_links">
												<option value="0">Не выбрано</option>
												@foreach($sources as $source)
													<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
												@endforeach
											</select>
											<input type="text" class="form-control link_prioritet" name="prioritet[]" 
											value="{{\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->where('id_link', $selected->id)->first()->prioritet}}">
											<span class="btn btn-danger minus pull-right">–</span>
											<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
										</div>
									@endforeach
								@else
									@foreach ($block->Sources as $selected)
										<div class="JsSel">
											<select name="url[]" class="form-control block_links">
												<option value="0">Не выбрано</option>
												@foreach($sources as $source)
													<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
												@endforeach
											</select>
											<input type="text" class="form-control link_prioritet" name="prioritet[]" 
											value="{{\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->where('id_link', $selected->id)->first()->prioritet}}">
											<span class="btn btn-danger minus pull-right">–</span>
											<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
										</div>
									@endforeach
								@endif
							@else
								<div style="width: 80%">
									<label for="link" class="col-xs-12 text-center">
										Дорогой блок(играет первым)
									</label>
								</div>
								@if ($block->autosort==1) 
									@foreach ($block->SourcesOnAutoPrioritet as $selected)
										@if ($selected->cheap!=1)
											<div class="JsSel">
												<select name="url[]" class="form-control block_links">
													<option value="0">Не выбрано</option>
													@foreach($sources as $source)
														<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
													@endforeach
												</select>
												<input type="text" class="form-control link_prioritet" name="prioritet[]" 
												value="{{\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->where('id_link', $selected->id)->first()->prioritet}}">
												<span class="btn btn-danger minus pull-right">–</span>
												<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
											</div>
										@endif
									@endforeach
								@else
									@foreach ($block->Sources as $selected)
										@if ($selected->cheap!=1)
											<div class="JsSel">
												<select name="url[]" class="form-control block_links">
													<option value="0">Не выбрано</option>
													@foreach($sources as $source)
														<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
													@endforeach
												</select>
												<input type="text" class="form-control link_prioritet" name="prioritet[]" 
												value="{{\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->where('id_link', $selected->id)->first()->prioritet}}">
												<span class="btn btn-danger minus pull-right">–</span>
												<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
											</div>
										@endif
									@endforeach
								@endif
								<div style="width: 80%">
									<label for="link" class="col-xs-12 text-center">
										Дешевый блок(играет вторым)
									</label>
								</div>
								@if ($block->autosort==1) 
									@foreach ($block->SourcesOnAutoPrioritet as $selected)
										@if ($selected->cheap==1)
											<div class="JsSel">
												<select name="url[]" class="form-control block_links">
													<option value="0">Не выбрано</option>
													@foreach($sources as $source)
														<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
													@endforeach
												</select>
												<input type="text" class="form-control link_prioritet" name="prioritet[]" 
												value="{{\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->where('id_link', $selected->id)->first()->prioritet}}">
												<span class="btn btn-danger minus pull-right">–</span>
												<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
											</div>
										@endif
									@endforeach
								@else
									@foreach ($block->Sources as $selected)
										@if ($selected->cheap==1)
											<div class="JsSel">
												<select name="url[]" class="form-control block_links">
													<option value="0">Не выбрано</option>
													@foreach($sources as $source)
														<option @if ($selected->id==$source->id) selected @endif value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option>
													@endforeach
												</select>
												<input type="text" class="form-control link_prioritet" name="prioritet[]" 
												value="{{\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->where('id_link', $selected->id)->first()->prioritet}}">
												<span class="btn btn-danger minus pull-right">–</span>
												<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>
											</div>
										@endif
									@endforeach
								@endif
							@endif
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
		.parent_auto_sort .auto_sort{
			margin-top: 5px;
		}
		.parent_auto_sort .auto_sort:first-child{
			margin-top: 0!important;
		}
.link_prioritet{
display: inline-block; 
width: 23px;
padding: 6px;
}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {
			$('#cheap_util').on("click", function() {
				if($(this).is(":checked")){
					jQuery ('#repeat').html(
					'<option value="0">Не повторять</option>'+
					'<option value="1">Повторить только дорогие ссылки</option>'+
					'<option value="2">Повторить только дешевые ссылки</option>'+
					'<option value="3">Повторить Все</option>'
					);
				}
				else{
					jQuery ('#repeat').html(
					'<option value="0">Не повторять</option>'+
					'<option value="3">Повторить Все</option>'
					);
				}
			});
			jQuery('.plus').click(function(){
			jQuery('.information_json_plus').before(
			'<div class="JsSel">' + 
			'<select name="url[]" class="form-control block_links">' +
			'<option value="0">Не выбрано</option>@foreach($sources as $source) <option value="{{$source->id}}">{{$source->title}} @if($source->player) <strong>({{$source->player}}) </strong> @else <strong>(Прямая)</strong> @endif</option> @endforeach</select>' + 
			'<input  type="text" class="form-control link_prioritet" name="prioritet[]" value="0" style="margin-left: 3px;">'+
			'<span class="btn btn-danger minus pull-right">&ndash;</span>' + 
			'<span class="btn btn-primary sortable pull-right" style="padding: 0; margin-right: 5px;"><img src="/images/arrow.png" style="width: 33px; height: 36px;"></span>' +
			'</div>'
			);
		});

		jQuery(document).on('click', '.minus', function(){
			jQuery( this ).closest( 'div' ).remove(); // удаление строки с полями
		});
	});
	</script>
	<script>
		$( function() {
			$( "#sortable" ).sortable();
		});
	</script>
@endpush