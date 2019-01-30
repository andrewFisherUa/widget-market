<div class="row" style="margin: 10px 0">
<a href="{{route('video_statistic.video_summary')}}" class="btn btn-primary">Видео суммарная статистика</a>
<a href="{{route('video_statistic.new_graph')}}" class="btn btn-primary">Видео график</a>
<a href="{{route('video_statistic.new_video_stat')}}" class="btn btn-primary">Видео статистика по ссылкам</a>
<a href="{{route('video_statistic.pads_video_summary')}}" class="btn btn-primary">Видео статистика по площадкам</a>
<a href="{{route('video_statistic.partner_video_summary')}}" class="btn btn-primary">Видео статистика по партнерам</a>
<a href="{{route('video_statistic.video_hour')}}" class="btn btn-primary">По часам</a>
</div>
<div class="row" style="margin: 10px 0">
<a href="{{route('video_statistic.new_video_stat_comparison')}}" class="btn btn-success">Сравнение статистики по ссылкам</a>
<a href="{{route('video_statistic.pads_video_summary_comparison')}}" class="btn btn-success">Сравнение статистики по площадкам</a>
<a href="{{route('video_statistic.partner_video_summary_comparison')}}" class="btn btn-success">Сравнение статистики по партнерам</a>
</div>
<div class="row" style="margin: 10px 0">
<a href="{{route('advert_statistic.pads_advert_summary_comparison')}}" class="btn btn-success">Сравнение товарной статистики по площадкам</a>
<a href="{{route('advert_statistic.partner_advert_summary_comparison')}}" class="btn btn-success">Сравнение товарной статистики по партнёрам</a>
</div>
<div class="row" style="margin: 10px 0">
<a href="{{route('teaser_statistic.pads_teaser_summary_comparison')}}" class="btn btn-success">Сравнение тизерной статистики по площадкам</a>
<a href="{{route('teaser_statistic.partner_teaser_summary_comparison')}}" class="btn btn-success">Сравнение тизерной статистики по партнёрам</a>
</div>
<div class="row" style="margin: 10px 0">

<a href="{{route('rekrut_product.nextstat')}}" class="btn btn-primary">Товарка суммарная статистика</a>

<!--<a href="{{route('product_statistic.product_graph')}}" class="btn btn-primary">Товарка график</a>-->
	{{--<a href="{{route('product_statistic.product_detail_pads')}}" class="btn btn-primary">Товарка статистика по площадкам</a>--}}
<a href="{{route('rekrut_product.nextstat.pads')}}" class="btn btn-primary">Товарка статистика по площадкам</a>
	{{--<a href="{{route('product_statistic.product_detail_users')}}" class="btn btn-primary">Товарка статистика по партнерам</a>--}}
<a href="{{route('rekrut_product.nextstat.part')}}" class="btn btn-primary">Товарка статистика по партнерам</a>
</div>
<div class="row" style="margin: 10px 0">
{{--<a href="{{route('teaser_statistic.teaser_summary')}}" class="btn btn-primary">Тизерка суммарная статистика</a>--}}
<a href="{{route('mpstatistica.summa_teaser')}}" class="btn btn-primary">Тизерка суммарная статистика</a>

	{{--<a href="{{route('teaser_statistic.teaser_detail_pads')}}" class="btn btn-primary">Тизерка статистика по площадкам</a>--}}
	<a href="{{route('mpstatistica.pads_teaser')}}" class="btn btn-primary">Тизерка статистика по площадкам</a>
		{{--<a href="{{route('teaser_statistic.teaser_detail_users')}}" class="btn btn-primary">Тизерка статистика по партнерам</a>--}}
    <a href="{{route('mpstatistica.partners_teaser')}}" class="btn btn-primary">Тизерка статистика по партнерам</a>		
</div>