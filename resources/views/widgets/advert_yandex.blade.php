<div class="row">
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
<b>Тип предложений магазина</b>
</div>
<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
@foreach($collection as $yid=>$col)
<div><input type="checkbox" name ="yandexshir[{{$yid}}]" value="1" @if($col["checked"]) checked @endif> {{$col["name"]}} <span style="color:#DCDCDC">({{$col["uniq_name"]}})</span></div>
@endforeach
</div>
</div>