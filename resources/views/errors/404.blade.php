@extends('layout/layout_base')

@php
    $title = __('Not Found');
@endphp

@section('content')
    <h1>We could not find this page...</h1>

    <p>
        You looked for: <code>{{ url()->full() }}</code>
    </p>
@endsection
