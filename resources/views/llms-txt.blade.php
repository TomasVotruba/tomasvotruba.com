@php
    /** @var \App\Entity\Post[] $posts */
@endphp

[site]
name: Tomas Votruba's Blog
url: https://tomasvotruba.com
description: Personal blog and professional site of Tomas Votruba, focusing on modernizing PHP codebases, Symfony, automated refactoring with Rector, and static analysis with PHPStan.

[self]
name: Tomas Votruba
bio: I help PHP companies modernize their codebases, increase profits, and attract talent using Rector, an automated refactoring tool I created to handle instant upgrades and code improvements. We provide an [upgrade service](https://getrector.com/hire-team) to save you time and money.
links:
- [LinkedIn](https://www.linkedin.com/in/tomas-votruba/)
- [Twitter](https://x.com/votrubaT)
- [GitHub](https://github.com/tomasVotruba/)
- [Rector](https://getrector.com)


[content]
# Blog posts

@foreach ($posts as $post)
## {{ $post->getTitle() }}

* [link]({{ action(\App\Http\Controllers\PostController::class, ['slug' => $post->getSlug()]) }})
* published on {{ $post->getDateTime()->format('Y-m-d') }}

{!! $post->getPerex() !!}

{!! $post->getContent() !!}


-----------------------------------------------


@endforeach
