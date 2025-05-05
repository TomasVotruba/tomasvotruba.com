@php
    /** @var $post \App\Entity\Post */
@endphp

# {!! $post->getTitle() !!}

* published: {{ $post->getDateTime()->format('Y-m-d') }}

{!! $post->getPerex() !!}

{!! $post->getContent() !!}

        <br>

        @if ($post->hasTweets())
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        @endif

        <br>
    </div>
@endsection
