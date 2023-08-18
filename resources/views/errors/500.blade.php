@extends('layout/layout_base')

@php
    $title = 'Server Error';
@endphp

@section('content')
    <h1>Something went wrong...</h1>

    <p>
        You looked for: <code>{{ url()->full() }}</code>
    </p>
@endsection
