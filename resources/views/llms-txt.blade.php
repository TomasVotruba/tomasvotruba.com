@php
    /** @var \App\Entity\Post[] $posts */
@endphp

# Tomas Votruba

Helping PHP companies modernize codebases, increase profits, and attract talent through automated refactoring and upgrades.

## About

I help PHP companies modernize their codebases, increase profits, and attract talent using Rector, an automated refactoring tool I created to handle instant upgrades and code improvements. We provide an [upgrade service](https://getrector.com/hire-team) to save you time and money.

Where to find me:

- [LinkedIn](https://www.linkedin.com/in/tomas-votruba/)
- [Twitter](https://x.com/votrubaT)
- [GitHub](https://github.com/tomasVotruba/)
- [Rector](https://getrector.com)


## Blog posts

@foreach ($posts as $post)
* [{{ $post->getTitle() }}]({{ action(\App\Http\Controllers\PostController::class, ['slug' => $post->getSlug()]) }})
* [markdown version]({{ action(\App\Http\Controllers\PostMdController::class, ['slug' => $post->getSlug()]) }})
* published on {{ $post->getDateTime()->format('Y-m-d') }}
* perex: {!! $post->getPerex() !!}

--------------------------------------------------

@endforeach
