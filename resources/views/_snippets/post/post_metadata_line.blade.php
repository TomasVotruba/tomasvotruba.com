<div>
    @php /** @var $post \App\Entity\Post */ @endphp

    <time datetime="{{ $post->getDateTime()|date('Y-m-D') }}">
        @if ($post->getUpdatedAt())
            {{ $post->getDateTime()->format('Y-m-d') }}
        @else
            Updated {{ $post->getUpdatedAt()->format('Y-m-d') }}
        @endif
    </time>
</div>
