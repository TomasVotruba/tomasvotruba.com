---
id: 382
title: "Introducing Bladestan - PHPStan analysis of Blade templates"

perex: |
    This Tuesday I was a guest in [2nd podcast of PHP Portugal](https://twitter.com/VotrubaT/status/1639241043248836610) folks. It was fun as always and apart GPT questions, I got asked about the Laravel open-source packages like [Punchcard](https://github.com/tomasVotruba/punchcard).

    I promised to put the 2nd package this week, so here it is.
---

## Short history

First a short history of full circle. A year and half ago I wrote about [Twig static analysis](/blog/stamp-static-analysis-of-templates/). Last year [Canvural](https://github.com/canvural) turned the idea into the real project for Blade templates. I wanted to use this package with few extras and upgrade on Laravel 10.

The package seemed abandoned and crashed on few templates, so I ported most of it parts and inlined the Symplify package for PHPStan compilation.

## The result?

I've been running **the Bladestan package since February on all my Laravel projects** to detect bugs without waiting production to fail on render. It's perfect helping hand and I don't have to worry about various code changes.

## What does the package do?

This package find all `view()` calls and looks if rendered variables have valid behavior in the provided template.t

Let's look at example of this blog controller:

```php
final class BlogController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): View
    {
        return \view('blog', [
            'title' => 'Blog',
            'posts' => $this->postRepository->fetchAll(),
        ]);
    }
}
```

* The package looks for a `resources/views/blog.blade.php` template file path
* It detect the `$title` and `$posts` variables types, `string` and `Post[]` respectively
* It renders the template file using those variables to PHPStan-compatible PHP
* It runs PHPStan on this JIT-rendered PHP and checks it for any violation using your normal `phpstan.neon` setup

## Where it helps with?

Let's say we make a typo and call non-existing method in our Blade template:

```php
@foreach($posts as $post)
    {{ $post->getConten() }}
@endforeach
```

<br>

We get a report:

```bash
 blog.blade.php:17
 rendered in: app/Http/Controllers/BlogController.php:20
 --------------------------------------------------------------
  - '#Call to an undefined method App\\Entity\\Post\:\:getContet\(\)#'
 ```


Or we call a non-existing class:

```php
<a href="{{ route(\TomasVotruba\Enum\RouteName::HOMEPAGE) }}">Homepage</a>
```

We get a report:

```bash
 blog.blade.php:18
 rendered in: app/Http/Controllers/BlogController.php:20
 --------------------------------------------------------------
  - '#Access to constant INVALID on an unknown class TomasVotruba\\Enum\\RouteName#'
```

<br>

We could also discovered these error using our browser and going through all possible if/else template paths. The strongest power of Bladestan is that **everything happens without any Blade compilation** - in static analysis made by PHPStan.

You add the package and let CI handle the rest.


## 2 steps to install package

1. require it via `composer.json`

```bash
composer require tomasvotruba/bladestan --dev
```

<br>

2. Setup template paths in `phpstan.neon`

Note: If you use only `resources/views`, you can skip this step. It's a default one.

```yaml
# phpstan.neon
parameters:
    bladestan:
        template_paths:
            # default
            - resources/views
```

That's it!

Now you just run PHPStan and see how well done are your templates:

```bash
vendor/bin/phpstan
```

<br>


This is my 2nd package in Laravel ecosystem, so I'm eager to get your feedback. Do you have some? Go to GitHub repository [tomasvotruba/bladestan](https://github.com/tomasVotruba/bladestan) and make an issue of improvement.


<br>

Happy coding!

