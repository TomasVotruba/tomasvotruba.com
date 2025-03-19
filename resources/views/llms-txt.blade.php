@php
    /** @var \App\Entity\Post[] $posts */
@endphp


# Tomas Votruba

I help PHP companies to modernize their codebase,
increase profits and attract new talent.

To make this happen faster and at scale, I made Rector - software that handles instant upgrades and automated refactoring.

We provide an upgrade service to save you time and money.



# Blog posts

@foreach ($posts as $post)
## {{ $post->getTitle() }}

{!! $post->getContent() !!}

@endforeach
