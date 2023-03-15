@php
    /** @var \App\Entity\Post[] $posts */
@endphp

<div class="container">
    @foreach ($posts as $post)

        <h1>{{ $post->getClearTitle() }}</h1>

        <p>
            {!! fast_markdown($post->getPerex()) !!}
        </p>

        <p>
            {!! fast_markdown($post->getContent()) !!}
        </p>

        <br>
        <br>
        <br>
        <br>

        <hr>

        <br>
        <br>
        <br>
        <br>

    @endforeach
</div>
