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

        <hr style="height: 1px; border: none; background-color: black" class="mt-5 mb-5">

        <br>

        <p>
            Do you learn from my contents or use open-souce packages like Rector every day?
            <br>

            <strong>
                <a href="https://github.com/sponsors/tomasvotruba">Consider supporting it on GitHub Sponsors</a>.
            </strong>

            I'd really appreciate it!
        </p>

        <br>

{{--        <a name="comments"></a>--}}

{{--        <div id="disqus_thread"></div>--}}

{{--        <script>--}}
{{--            (function () { // DON'T EDIT BELOW THIS LINE--}}
{{--                var d = document, s = d.createElement('script');--}}
{{--                s.src = 'https://itsworthsharing.disqus.com/embed.js';--}}
{{--                s.setAttribute('data-timestamp', +new Date());--}}
{{--                (d.head || d.body).appendChild(s);--}}
{{--            })();--}}
{{--        </script>--}}

{{--        <script id="dsq-count-scr" src="https://itsworthsharing/disqus.com/count.js" async defer></script>--}}
    </div>
@endsection
