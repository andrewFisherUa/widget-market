<div style="margin: 10px 0">
<a href="{{route('video_setting.source.add')}}" class="btn btn-primary">Новая ссылка</a>
<a href="{{route('video_setting.sources')}}" class="btn btn-primary">Все ссылки</a>
<a href="{{route('video_setting.block.create')}}" class="btn btn-primary">Новый блок</a>
<a href="{{route('video_setting.blocks.all')}}" class="btn btn-primary">Все блоки</a>
@if (\Auth::user()->hasRole('admin'))
	<a href="{{route('video_setting.default')}}" class="btn btn-primary">Дефолтовые настройки</a>
	<a href="{{route('video_setting.sources.defolte')}}" class="btn btn-primary">Дефолтовые цены ссылок</a>
@endif
</div>