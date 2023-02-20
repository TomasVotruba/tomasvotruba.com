@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <h1>Read about upgrades, static analysis, GPT, Symfony and Laravel</h1>

        {{-- display posts with as few divs as possible, for DOM performance on mobile --}}

        @include('_snippets/post/post_list', ['posts' => $posts])
    </div>
@endsection


@section('custom_footer')
    {{-- special footer just for blog page --}}
    <div id="footer">
        <div class="container-fluid text-center">
            Get next post first →

            <a href="https://twitter.com/votrubaT" target="blank">Twitter</a>
            <span class="pl-2 pr-2">•</span>

            <a href="{{ route(\TomasVotruba\Website\ValueObject\RouteName::RSS) }}" target="blank">RSS</a>
        </div>
    </div>
@endsection
