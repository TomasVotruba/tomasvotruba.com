@extends('layout.layout_base')

@php
    use TomasVotruba\Website\Enum\RouteName;

    /** type declarations */
    /** @var $post \TomasVotruba\Website\Entity\Post */
@endphp

@section('post_social_tags')
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
    <meta name="twitter:title" content="{{ $post->getClearTitle() }}"/>
    <meta name="twitter:image" content="{{ route(RouteName::POST_IMAGE, ['title' => $post->getClearTitle()]) }}"/>
    <meta name="twitter:description" content="{{ $post->getPerex() }}"/>

    <!-- post_id: {{ $post->getId() }} -->
@endsection

@section('content')
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

    <x-markdown>{!! $post->getContent() !!}</x-markdown>

    <br>

    <br>
    <br>

    <a name="comments"></a>

    @include('_snippets.disqus_comments')

    <script id="dsq-count-scr" src="https://itsworthsharing/disqus.com/count.js" async defer></script>
@endsection
