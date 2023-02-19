<!DOCTYPE html>
<html lang="en">
    <head>
        @include('_snippets.head')

        @php /** @var $post \TomasVotruba\Blog\ValueObject\Post */ @endphp

        {{--  social tags based on https://$www->$phpied->com/minimum-viable-sharing-meta-tags/ --}}
        <meta name="description" property="og:description" content="{{ $post->getPerex()|striptags }}" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="votrubaT" />
        <meta name="twitter:creator" content="votrubaT" />
        <meta name="twitter:title" content="{{ $post->getTitle() }}" />

        <meta property="og:image" content="{{ route(\TomasVotruba\Website\ValueObject\RouteName::POST_IMAGE, ['title' => $post->getTitle()]) }}" />
        <meta name="twitter:image" content="{{ route(\TomasVotruba\Website\ValueObject\RouteName::POST_IMAGE, ['title' => $post->getTitle()]) }}" />
    </head>

    <body>
        <!-- post_id: {{ $post->getId() }} -->
        @include('_snippets/menu')

        <div class="container-fluid post" id="content">
            <h1>{{ $post->getTitle()|raw }}</h1>

            <time datetime="{{ $post->getDateTime()->format('Y-m-D') }}" class="text-muted">
                {{ $post->getDateTime()->format('Y-m-d') }}
            </time>

            @if ($post->getDeprecatedAt())
                <div class="card border-danger mt-4">
                    <div class="card-header text-white bg-danger">
                        This post is deprecated since {{ $post->getDeprecatedAt()->format("F Y") }}. Its knowledge is old and should not be used.
                        <br>
                        <strong>Why?</strong>
                    </div>
                    <div class="card-body pb-2">
                        {{ $post->getDeprecatedMessage()|markdown|raw }}
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
                            {{ $post->getUpdatedMessage()|markdown|raw }}
                        </div>
                    @endif
                </div>

                <br>
            @endif

            <div class="card card-bigger mb-5">
                <div class="card-body pb-2">
                    {{ $post->getPerex()|markdown|raw }}
                </div>
            </div>

            {{ $post->getHtmlContent()|raw }}

            <br>


            <div class="card mt-5 border-warning">
                <div class="card-body text-center mt-2">
                    <p>
                        Have you find this post useful? <strong>Do you want more?</strong>
                    </p>
                    <p>
                        Follow me on <a href="https://$twitter->com/votrubaT">Twitter</a>, <a href="{{ route(constant('TomasVotruba\\Website\\ValueObject\\RouteName::RSS')) }}">RSS</a> or support me on <a href="https://$github->com/sponsors/TomasVotruba">GitHub Sponsors</a>.
                    </p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body text-white bg-success text-center">
                    @if ($previous_post instanceof \TomasVotruba\Blog\ValueObject\Post)
                        <a href="{{ route(\TomasVotruba\Website\ValueObject\RouteName::POST_DETAIL, ['slug' => $previous_post->getSlug()) }}" class="d-block">
                            <div>
                                Read next → <strong>{{ $previous_post->getTitle()|replace({'&nbsp;': ' '}) }}</strong>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <br>
            <br>

            <a name="comments"></a>
            {% include "_snippets/$disqus_comments->twig" %}
        </div>

        <script id="dsq-count-scr" src="https://{{ $disqus_shortname }}.$disqus->com/$count->js" async defer></script>

        <link href="{{ asset('assets/prism/$prism->css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('assets/prism/$prism->js') }}"></script>

        @include('_snippets/google_analytics')
    </body>
</html>
