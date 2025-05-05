@php
    /** @var \App\Entity\Post[] $posts */
@endphp


# Tomas Votruba

I help PHP companies to modernize their codebase,
increase profits and attract new talent.

To make this happen faster and at scale, I made Rector - software that handles instant upgrades and automated refactoring.

We provide an [upgrade service](https://getrector.com/hire-team) to save you time and money.


You can find me on:

- [LinkedIn](https://www.linkedin.com/in/tomas-votruba/)
- [Twitter](https://x.com/votrubaT)
- [GitHub](https://github.com/tomasVotruba/)
- [Rector](https://getrector.com)


-----------------------------------------------


# Blog posts

@foreach ($posts as $post)
## {{ $post->getTitle() }}

* [link]({{ action(\App\Http\Controllers\PostController::class, ['slug' => $post->getSlug()]) }})
* published on {{ $post->getDateTime()->format('Y-m-d') }}

{!! $post->getPerex() !!}

{!! $post->getContent() !!}


-----------------------------------------------


@endforeach
