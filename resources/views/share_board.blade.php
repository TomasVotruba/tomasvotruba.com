@php
    /** @var \App\ValueObject\PostTweet[] $postTweets */
@endphp

@extends('layout/layout_base')

@section('content')
    <div class="container">
        <h1>Share board</h1>

        <div class="row">
            @foreach ($postTweets as $postTweet)
                <div class="col-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <img src="{{ $postTweet->getPostThumbnail() }}" class="img-fluid mb-4" alt="">

                            <textarea class="form-control mb-4 p-2" style="height: 8em">{{ $postTweet->getTweet() }}

{{ $postTweet->getUrl() }}</textarea>
                        </div>
                    </div>
                </div>
        @endforeach
        </div>
    </div>
@endsection
