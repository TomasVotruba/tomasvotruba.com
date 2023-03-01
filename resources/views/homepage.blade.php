@extends('layout/layout_base')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-9">
                <h1 class="text-start">
                    I help PHP Companies <br>
                    Change Fast&nbsp;and&nbsp;Safely
                </h1>
            </div>

            <div class="col-4 col-md-3">
                <a href="{{ route(\App\Enum\RouteName::ABOUT) }}">
                    <img src="{{ asset('assets/images/tomas_votruba.jpg') }}" class="mt-auto rounded-circle shadow">
                </a>
            </div>
        </div>

        <br>

        <div class="clearfix"></div>

        <h2 class="mb-4">
            What can your learn about?
        </h2>

        <div class="text-bigger">
            @foreach ($last_posts as $current_post)
                @php /** @var $current_post \App\Entity\Post */ @endphp
                <div class="mb-4 row">
                    <div class="col-12">
                        <a href="{{ route(\App\Enum\RouteName::POST_DETAIL, ['slug' =>  $current_post->getSlug()]) }}"
                           class="pt-3 pr-3">
                            {{ $current_post->getClearTitle() }}
                        </a>
                    </div>

                    <div class="small text-secondary col-12 pt-2">
                        {{ $current_post->getDateTime()->format("Y-m-d") }}
                    </div>
                </div>
            @endforeach

            <a href="{{ route(\App\Enum\RouteName::BLOG) }}"
               class="btn btn-warning pull-right mt-4">Discover more Posts </a>
        </div>

        <br>
        <br>
        <br>
        <hr>
        <br>
        <br>

        {{-- my dad raised me with quotes, at first I didn't understand them,
        as I was older, I realized the Truth - it's a coding standard for Life --}}

        <blockquote class="blockquote text-center">
            "{!! $quote !!}"
        </blockquote>
    </div>
@endsection
