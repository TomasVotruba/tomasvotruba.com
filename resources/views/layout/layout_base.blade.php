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

    <meta name="twitter:site" content="votrubaT"/>
    <meta name="twitter:creator" content="votrubaT"/>

    @hasSection('post_social_tags')
        @yield('post_social_tags')
    @else
        {{-- default social --}}
        <meta name="twitter:card" content="summary"/>

        <meta property="og:image" content="{{ asset('assets/images/tomas_votruba.jpg') }}"/>
        <meta name="twitter:image" content="{{ asset('assets/images/tomas_votruba.jpg') }}"/>
    @endif

    <link rel="alternate" type="application/rss+xml" title="Tomas Votruba Blog RSS"
          href="{{ action(\App\Http\Controllers\RssController::class) }}">

    {{-- next attempts https://stackoverflow.com/a/64439406/1348344 --}}
    <link rel="stylesheet" rel="preload" as="style"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:700&amp;display=swap"/>

    @vite(['resources/css/app.scss'])

    {{-- code highligh posts --}}
    {{-- pick from https://highlightjs.org/demo --}}
    {{-- see ChatGPT https://chat.openai.com/share/af70716e-067c-481c-ad61-fc40de2f4dc3 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/base16/atlas.min.css">
    <style>
        pre code.hljs {
            border-radius: .6em;
            line-height: 1.6em;
            margin: 1.2em 0;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/yaml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/html.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/diff.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code.language-php, pre code.language-html, pre code.language-yaml, pre code.language-bash, pre code.language-diff, pre code.language-xml, pre code.language-json, pre code.language-javascript' ).forEach((block) => {
                hljs.highlightBlock(block);
            });
        });
    </script>
</head>

<body>
    @include('_snippets/menu')

    @hasSection('wide_content')
        @yield('wide_content')
    @else
        <div class="container">
            @yield('content')
        </div>
   @endif

</body>

    <!-- 100% privacy-first analytics -->
    <script async defer src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
    <noscript><img src="https://queue.simpleanalyticscdn.com/noscript.gif" alt="" referrerpolicy="no-referrer-when-downgrade" /></noscript>
</html>
