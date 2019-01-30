@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if ($user->hasRole('affiliate'))
			@include('affiliate.cabinet.home')
		@endif
		@if ($user->hasRole('advertiser'))
			@include('advertiser.cabinet.home')
		@endif
		@if ($user->hasRole('admin') or $user->hasRole('manager') or $user->hasRole('super_manager'))
			@include('admin.cabinet.home')
		@endif
    </div>
</div>
@endsection
