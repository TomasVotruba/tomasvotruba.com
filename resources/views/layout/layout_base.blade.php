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

        <meta name="twitter:creator" content="@votrubaT"/>

        @if (\Illuminate\Support\Facades\View::hasSection('post_social_tags'))
            @yield('social_tags')
        @else
            {{-- default social --}}
            <meta name="twitter:card" content="summary"/>

            <meta property="og:image" content="{{ asset('assets/images/tomas_votruba.jpg') }}"/>
            <meta name="twitter:image" content="{{ asset('assets/images/tomas_votruba.jpg') }}"/>
       @endif

        <link rel="alternate" type="application/rss+xml" title="Tomas Votruba Blog RSS" href="{{ route(\TomasVotruba\Website\Enum\RouteName::RSS) }}">

        {{-- next attempts https://stackoverflow.com/a/64439406/1348344 --}}
        <link rel="stylesheet" rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:700&amp;display=swap" />

        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>

    <body>
        @include('_snippets/menu')

        <div class="container-fluid" id="content">
            @yield('content')
        </div>
    </body>

    <script>
        ga=function(){ ga.q.push(arguments) };
        ga.q=[];
        ga.l=+new Date;
        ga('create', 'UA-46082345-1', 'auto');
        ga('send','pageview');
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async defer></script>
</html>
