@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <h1>{{ \App\Enum\Design::BLOG_TITLE }} </h1>

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
            <span class="ps-2 pe-2">•</span>

            <a href="{{ action(\App\Http\Controllers\RssController::class) }}" target="blank">RSS</a>
        </div>
    </div>
@endsection
