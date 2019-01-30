
@if($id_user)
	
@else
<div style="margin-bottom:10px">
@foreach($links as $link)
<a href="{{$link["url"]}}">{{$link["title"]}}</a>&nbsp;&nbsp;&nbsp;
@endforeach
</div>
@endif