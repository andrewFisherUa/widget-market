@extends('layouts.app_for_graph')

@section('content')
{!! $graph->render() !!}

@endsection
@push('cabinet_home_top')

{!! Charts::assets() !!}
@endpush