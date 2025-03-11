@extends('layout.layout_base')

@php
    /** type declarations */
    /** @var $post \App\Entity\Post */
@endphp

@section('post_social_tags')
    {{--  social tags based on https://www.phpied.com/minimum-viable-sharing-meta-tags/ --}}
    <meta name="description" property="og:description" content="{{ $post->getPerex() }}"/>

    <meta property="og:title" content="{{ $post->getClearTitle() }}"/>
    <meta property="og:description" content="{{ $post->getPerex() }}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:image"
          content="{{ action(\App\Http\Controllers\ThumbnailController::class, ['title' => $post->getClearTitle()]) }}"/>

    <meta
        property="og:url"
        content="{{ action(\App\Http\Controllers\PostController::class, ['slug' => $post->getSlug()]) }}"
    />

    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="{{ $post->getClearTitle() }}"/>
    <meta name="twitter:image"
          content="{{ action(\App\Http\Controllers\ThumbnailController::class, ['title' => $post->getClearTitle()]) }}"/>
    <meta name="twitter:description" content="{{ $post->getPerex() }}"/>

    <!-- post_id: {{ $post->getId() }} -->
@endsection

@section('content')
    <div id="post">
        <h1>{!! $post->getTitle() !!}</h1>

        <time datetime="{{ $post->getDateTime()->format('Y-m-D') }}" class="text-muted">
            {{ $post->getDateTime()->format('Y-m-d') }}
        </time>

        @if ($post->getUpdatedAt())
            <div class="card border-success mt-4">
                <div class="card-header text-white bg-success">
                    This post was updated at {{ $post->getUpdatedAt()->format("F Y") }} with fresh know-how.
                    <br>
                    <strong>What is new?</strong>
                </div>
                @if ($post->getUpdatedMessage())
                    <div class="card-body pb-2">
                        {!! markdown($post->getUpdatedMessage()) !!}
                    </div>
                @endif
            </div>

            <br>
        @endif

        <div class="card card-bigger mb-5">
            <div class="card-body p-4">
                {!! markdown($post->getPerex()) !!}
            </div>
        </div>

        {!! markdown($post->getContent()) !!}

        <br>

        @if ($post->hasTweets())
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        @endif

        <br>
    </div>
@endsection
