<div class="row" style="margin: 0px 0px;">
<a href="{{route('advertiser.statistic',['shop_id'=>0])}}" class="btn btn-primary">Cуммарная статистика</a>
<a href="{{route('advertiser.statistic',['shop_id'=>0,'mod'=>'domain','order'=>'desc','sort'=>'clicks'])}}" class="btn btn-primary">Статистика по площадкам</a> 
<a href="{{route('advertiser.statistic',['shop_id'=>0,'mod'=>'company','order'=>'desc','sort'=>'clicks'])}}" class="btn btn-primary">Статистика по магазинам</a> 
<a href="{{route('advertiser.statistic',['shop_id'=>0,'mod'=>'category','order'=>'desc','sort'=>'clicks'])}}" class="btn btn-primary">Статистика по категориям</a> 
<a href="{{route('advertiser.statistic',['shop_id'=>0,'mod'=>'region','order'=>'desc','sort'=>'clicks'])}}" class="btn btn-primary">Статистика по регионам</a> 
</div>