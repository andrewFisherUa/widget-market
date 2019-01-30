@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
	@if (Session::has('message_success'))
		<div class="alert alert-success">
			{{ session('message_success') }}
		</div>
	@endif
	@if (Session::has('message_danger'))
		<div class="alert alert-danger">
			{{ session('message_danger') }}
		</div>
	@endif
		<div class="row">
			<div class="col-xs-6">
				здесь счета
			</div>
			<div class="col-xs-6">
				@if (!$entity)
					ты не заключил договор
				@else
					<a href="{{route('advertiser.entity.dogovor', ['id'=>$entity->id])}}" target="_blank" class="btn btn-primary">Ознакомиться с договором</a>
					<table class="table table-hover" style="margin: 10px 0">
						<tr>
							<td>Должность</td>
							<td>{{$entity->position}}</td>
						</tr>
						<tr>
							<td>ФИО</td>
							<td>{{$entity->name}}</td>
						</tr>
						<tr>
							<td>Организация</td>
							<td>@if ($entity->form==1) ИП {{$entity->firm_name}}@elseif ($entity->form==2) ООО "{{$entity->firm_name}}" @endif</td>
						</tr>
						<tr>
							<td>Способ расчета</td>
							<td>@if ($entity->type_payout==1) Без НДС @elseif ($entity->type_payout==2) С НДС @endif</td>
						</tr>
						<tr>
							<td>Юридический адрес</td>
							<td>{{$entity->legale_male}}</td>
						</tr>
						<tr>
							<td>Почтовый адрес</td>
							<td>{{$entity->fact_male}}</td>
						</tr>
						<tr>
							<td>ИНН</td>
							<td>{{$entity->inn}}</td>
						</tr>
						@if ($entity->form==2)
						<tr>
							<td>КПП</td>
							<td>{{$entity->kpp}}</td>
						</tr>
						@endif
						<tr>
							<td>@if ($entity->form==1) ОГРНИП @elseif ($entity->form==2) ОГРН @endif</td>
							<td>{{$entity->ogrn}}</td>
						</tr>
						<tr>
							<td>ОКВЭД</td>
							<td>{{$entity->okved}}</td>
						</tr>
						<tr>
							<th colspan="2" class="text-center">Банковские реквизиты</th>
						</tr>
						<tr>
							<td>Наименование банка</td>
							<td>{{$entity->name_bank}}</td>
						</tr>
						<tr>
							<td>Расчетный счет</td>
							<td>{{$entity->account}}</td>
						</tr>
						<tr>
							<td>Кор. счет</td>
							<td>{{$entity->kor_account}}</td>
						</tr>
						<tr>
							<td>БИК</td>
							<td>{{$entity->bik}}</td>
						</tr>
					</table>
				@endif
			</div>
		</div>
    </div>
</div>

@endsection
@push('cabinet_home')
	<link href="{{ asset('css/cabinet/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/rouble.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modal.css') }}" rel="stylesheet">
@endpush
@push('cabinet_home_js')
	
@endpush