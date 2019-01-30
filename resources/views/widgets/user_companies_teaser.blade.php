<table class="table">
    @if (count($companies)>0)
	<thead>
		<tr>
			<th>Название</th>
			<th>Тип</th>
			<th>Статус</th>
			<th>Последнее обновление</th>
			
			<th>Клики сегодня</th>
			<th>Показы сегодня</th>
			<th>Кол-во предложений</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach($companies as $company)
			<tr>
				<td>{{$company->name}}</td>
				<td>Тизерный</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><a href = "{{route("advertiser.add_offer_company",["id"=>$company->id]) }}"><span class="glyphicon glyphicon-cog color-green"></span></a></td>
				<td><a href = "{{route("advertiser.edit_company_teaser",["id"=>$company->id]) }}"><span class="glyphicon glyphicon-cog color-blue"></span></a></td>
				<td></td>
			</tr>
		@endforeach
	</tbody>
	@else
		Добавьте свою первую компанию
	@endif
</table>
