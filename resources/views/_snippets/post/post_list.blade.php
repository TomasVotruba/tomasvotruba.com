@foreach ($posts as $post)
    @php /** @var $post \App\Entity\Post */ @endphp

    <div
            @class([
                'article-row',
                'bg-success text-white pt-3 pb-4 ps-4 pe-4 mb-4 rounded shadow' => $post->isUpdated(),
            ])
    >
        <a href="{{ action(\App\Http\Controllers\PostController::class, ['slug' => $post->getSlug()]) }}"
           class="post-list-title">
            {{ $post->getClearTitle() }}
        </a>

        @if ($post->getUpdatedAt())
            <div>
                Updated {{ $post->getUpdatedAt()->format('Y-m-d') }}
            </div>
        @else
            <div class="text-muted">{{ $post->getDateTime()->format('Y-m-d') }}</div>
        @endif
    </div>
@endforeach
