@php use TomasVotruba\Website\ValueObject\RouteName; @endphp

<!DOCTYPE html>
<html lang="en">
<head>
    @include('_snippets/head')

    @php /** @var $post \TomasVotruba\Blog\ValueObject\Post */ @endphp

    {{--  social tags based on https://www.phpied.com/minimum-viable-sharing-meta-tags/ --}}
    <meta name="description" property="og:description" content="{{ $post->getPerex() }}"/>

    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:site" content="votrubaT"/>
    <meta name="twitter:creator" content="votrubaT"/>
    <meta name="twitter:title" content="{{ $post->getClearTitle() }}"/>

    <meta property="og:image" content="{{ route(RouteName::POST_IMAGE, ['title' => $post->getClearTitle()]) }}"/>
    <meta name="twitter:image" content="{{ route(RouteName::POST_IMAGE, ['title' => $post->getClearTitle()]) }}"/>
</head>

<body>
    <!-- post_id: {{ $post->getId() }} -->
    @include('_snippets/menu')

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
                        {{ $post->getUpdatedMessage() }}
                    </div>
                @endif
            </div>

            <br>
        @endif

        <div class="card card-bigger mb-5">
            <div class="card-body pb-2">
                <x-markdown>{{ $post->getPerex() }}</x-markdown>
            </div>
        </div>

        {!! $post->getHtmlContent() !!}

        <br>

        <br>
        <br>

        <a name="comments"></a>

        @include('_snippets/disqus_comments')
    </div>

    <script id="dsq-count-scr" src="https://itsworthsharing/disqus.com/count.js" async defer></script>

    <link href="{{ asset('assets/prism/prism.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('assets/prism/prism.js') }}"></script>

    @include('_snippets/google_analytics')
    </body>
</html>
