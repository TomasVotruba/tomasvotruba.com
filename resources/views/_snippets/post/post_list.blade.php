@foreach ($posts as $post)
    @php /** @var $post \App\Entity\Post */ @endphp

    @if ($latestYear !== $post->getYear())
        <h3 class="mt-5">{{ $post->getYear() }}</h3>
        @php $latestYear = $post->getYear() @endphp
    @endif

    <div class="article-row d-flex">

        @if ($post->getUpdatedAt())
            <div class="highlight-date bg-success text-white">
                {{ $post->getUpdatedAt()->format('m-d') }}
            </div>
        @else
            <div class="highlight-date">{{ $post->getDateTime()->format('m-d') }}</div>
        @endif

        <a href="{{ action(\App\Http\Controllers\PostController::class, ['slug' => $post->getSlug()]) }}"
           class="post-list-title">
            {!! $post->getClearTitle() !!}
        </a>
    </div>
@endforeach
