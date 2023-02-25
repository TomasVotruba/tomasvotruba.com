@php use TomasVotruba\Website\ValueObject\RouteName; @endphp

    <!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title }} | Tomas Votruba</title>
    <meta charset="utf-8">
    <meta name="robots" content="index, follow">

    {{-- mobile --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @php /** @var $post \TomasVotruba\Blog\ValueObject\Post */ @endphp

    {{--  social tags based on https://www.phpied.com/minimum-viable-sharing-meta-tags/ --}}
    <meta name="description" property="og:description" content="{{ $post->getPerex() }}"/>

    <meta property="og:title" content="{{ $post->getClearTitle() }}"/>
    <meta property="og:description" content="{{ $post->getPerex() }}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:image" content="{{ route(RouteName::POST_IMAGE, ['title' => $post->getClearTitle()]) }}"/>

    <meta
        property="og:url"
        content="{{ route(RouteName::POST_DETAIL, ['slug' => $post->getSlug()]) }}"
    />

    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:site" content="votrubaT"/>
    <meta name="twitter:creator" content="votrubaT"/>
    <meta name="twitter:title" content="{{ $post->getClearTitle() }}"/>
    <meta name="twitter:image" content="{{ route(RouteName::POST_IMAGE, ['title' => $post->getClearTitle()]) }}"/>
    <meta name="twitter:description" content="{{ $post->getPerex() }}"/>

    <link rel="alternate" type="application/rss+xml" title="Tomas Votruba Blog RSS"
          href="{{ route(\TomasVotruba\Website\ValueObject\RouteName::RSS) }}">

    {{-- !!! Twitter Bootstrap - keep the local copy css classes autocomplete --}}
    {{-- to speed-up delivery https://stackoverflow.com/a/46142270/1348344 --}}

    {{-- next attempts https://stackoverflow.com/a/64439406/1348344 --}}
    <link rel="stylesheet" rel="preload" as="style"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:700&amp;display=swap"/>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous"
          media="print" onload="this.media='all'">

    {{-- this is the last, so prism can be overriden here --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css"/>
</head>

<body>
<!-- post_id: {{ $post->getId() }} -->
@include('_snippets.menu')

<div class="container-fluid post" id="content">
    <h1>{!! $post->getTitle() !!}</h1>

    <time datetime="{{ $post->getDateTime()->format('Y-m-D') }}" class="text-muted">
        {{ $post->getDateTime()->format('Y-m-d') }}
    </time>

    @if ($post->getDeprecatedAt())
        <div class="card border-danger mt-4">
            <div class="card-header text-white bg-danger">
                This post is deprecated since {{ $post->getDeprecatedAt()->format("F Y") }}. Its knowledge is old and
                should not be used.
                <br>
                <strong>Why?</strong>
            </div>
            <div class="card-body pb-2">
                <x-markdown>
                    {{ $post->getDeprecatedMessage() }}
                </x-markdown>
            </div>
        </div>

        <br>
    @endif

    @if ($post->getUpdatedAt())
        <div class="card border-success mt-4">
            <div class="card-header text-white bg-success">
                This post was updated at {{ $post->getUpdatedAt()->format("F Y") }} with fresh know-how.
                <br>
                <strong>What is new?</strong>
            </div>
            @if ($post->getUpdatedMessage())
                <div class="card-body pb-2">
                    <x-markdown>
                        {{ $post->getUpdatedMessage() }}
                    </x-markdown>
                </div>
            @endif
        </div>

        <br>
    @endif

    <div class="card card-bigger mb-5">
        <div class="card-body pb-2">
            <x-markdown>
                {{ $post->getPerex() }}
            </x-markdown>
        </div>
    </div>

    {!! $post->getHtmlContent() !!}

    <br>

    <br>
    <br>

    <a name="comments"></a>

    @include('_snippets.disqus_comments')
</div>

<script id="dsq-count-scr" src="https://itsworthsharing/disqus.com/count.js" async defer></script>

<link href="{{ asset('assets/prism/prism.css') }}" rel="stylesheet" type="text/css"/>
<script src="{{ asset('assets/prism/prism.js') }}"></script>

@include('_snippets.google_analytics')
</body>
</html>
