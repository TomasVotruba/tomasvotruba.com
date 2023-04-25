@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-9">
                <h1 class="text-start">
                    I help PHP Companies <br>
                    Change Fast&nbsp;and&nbsp;Safely
                </h1>
            </div>

            <div class="col-4 col-md-3">
                <a href="{{ action(\App\Http\Controllers\RssController::class) }}">
                    <img src="{{ asset('assets/images/tomas_votruba.jpg') }}" class="mt-auto rounded-circle shadow">
                </a>
            </div>
        </div>

        <h2 class="mb-5">
            What can your learn about?
        </h2>

        @include('_snippets/post/post_list', ['posts' => $last_posts])

        <a href="{{ action(\App\Http\Controllers\BlogController::class) }}" class="btn btn-warning pull-right mt-4">
            Discover more Posts
        </a>

        <br>
        <br>
        <br>
        <hr>
        <br>
        <br>

        {{-- my dad raised me with quotes, at first I didn't understand them,
        as I was older, I realized the Truth - it's a coding standard for Life --}}

        <blockquote class="blockquote text-center">
            "{!! $quote !!}"
        </blockquote>
    </div>
@endsection
