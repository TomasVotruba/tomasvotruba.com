@foreach ($posts as $post)
    @php /** @var $post \TomasVotruba\Website\Entity\Post */ @endphp

    <div
            @class([
                'article-row',
                'border opacity-70 border-danger text-danger rounded shadow-danger pt-3 pb-4 pl-4 pr-4 mb-4' => $post->getDeprecatedAt(),
                'bg-success text-white pt-3 pb-4 pl-4 pr-4 mb-4 rounded shadow' => $post->isUpdated(),
            ])
    >
        <a href="{{ route(\TomasVotruba\Website\Enum\RouteName::POST_DETAIL, ['slug' => $post->getSlug()]) }}"
           class="post-list-title">
            {{ $post->getClearTitle() }}
        </a>

        @if ($post->getDeprecatedAt())
            <div class="text-danger">Deprecated {{ $post->getDeprecatedAt()->format('Y-m-d') }}</div>
        @elseif ($post->getUpdatedAt())
            <div>
                Updated {{ $post->getUpdatedAt()->format('Y-m-d') }}
            </div>
        @else
            <div class="text-muted">{{ $post->getDateTime()->format('Y-m-d') }}</div>
        @endif
    </div>
@endforeach
