@php
    /** @var $post \App\Entity\Post */
@endphp

# {!! $post->getTitle() !!}

* published: {{ $post->getDateTime()->format('Y-m-d') }}

{!! $post->getPerex() !!}

{!! $post->getContent() !!}
