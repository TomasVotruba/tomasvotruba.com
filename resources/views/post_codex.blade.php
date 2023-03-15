@php
    /** @var \App\Entity\Post[] $posts */
@endphp

<div class="container">
    @foreach ($posts as $post)

        <h1>{{ $post->getClearTitle() }}</h1>

        <p>
            <x-markdown highlightCode=false>
                {{ $post->getPerex() }}
            </x-markdown>
        </p>

        <p>
            <x-markdown highlightCode=false>
                {{ $post->getContent() }}
            </x-markdown>
        </p>

        <hr>

    @endforeach
</div>
