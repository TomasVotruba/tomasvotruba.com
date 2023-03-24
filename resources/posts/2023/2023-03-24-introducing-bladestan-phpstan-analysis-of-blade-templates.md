---
id: 382
title: "Introducing Bladestan - PHPStan analysis of Blade templates"

perex: |
    This Tuesday, I was a guest in [2nd podcast of PHP Portugal](https://twitter.com/VotrubaT/status/1639241043248836610) folks. It was fun as always, and apart from GPT questions, I got asked about the Laravel open-source packages like [Punchcard](https://github.com/tomasVotruba/punchcard).

    I promised to put the 2nd package this week, so here it is.
---

## Short History

I wrote about [Twig static analysis](/blog/stamp-static-analysis-of-templates/) a year and a half ago. Last year [Canvural](https://github.com/canvural) turned the idea into an actual project for Blade templates. I wanted to use this package with a few extras and upgrade to Laravel 10.

The package seemed abandoned and crashed on a few templates, so I ported most parts and inlined the Symplify package for the PHPStan compilation. This is what I love about open-source. One person shares an idea, another takes it into a prototype, and another improves it further.

## The result?

I've been running **the Bladestan package since February on all my Laravel projects** to detect bugs without waiting for production to fail on render. It's the perfect helping hand, and I don't have to worry about various code changes.

## What does the package do?

This package finds all `view()` calls and looks if rendered variables have valid behavior in the provided template.

Let's look at an example of this blog controller:

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
* It detects the `$title` and `$posts` variables types, `string` and `Post[]` respectively
* It renders the template file using those variables to PHPStan-compatible PHP
* It runs PHPStan on this rendered PHP and checks it for any violation using your normal `phpstan.neon` setup

## Where it helps with?

Let's say we make a typo and call a **non-existing method** in our Blade template:

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

<br>

Or we call a **non-existing class**:

```php
<a href="{{ route(\TomasVotruba\RouteName::HOMEPAGE) }}">Homepage</a>
```

We get a report:

```bash
blog.blade.php:18
rendered in: app/Http/Controllers/BlogController.php:20
--------------------------------------------------------------
 - '#Access to constant HOMEPAGE on an unknown class TomasVotruba\\RouteName#'
```

<br>

We could also discover these errors using our browser and go through all possible if/else template paths.

The most fantastic feature of Bladestan is that **everything happens without any Blade compilation** - in the static analysis made by PHPStan. It also uses your full PHPStan setup, including Larastan and all extensions.

<br>

You just add the package and let CI handle the rest.


## 2 steps to install Bladestan

1. Require it via composer

```bash
composer require tomasvotruba/bladestan --dev
```

<br>

2. Setup template paths in `phpstan.neon`

Note: You can skip this step using only `resources/views` - it's a default value.

```yaml
# phpstan.neon
parameters:
    bladestan:
        template_paths:
            # default
            - resources/views
```

That's it!

<br>

Now you just run PHPStan and see how well done your templates are:

```bash
vendor/bin/phpstan
```

<br>


The Bladestan is my 2nd package in the Laravel ecosystem, **so I'm eager to get your feedback**. Do you have some? Go to the GitHub repository [tomasvotruba/bladestan](https://github.com/tomasVotruba/bladestan) and make an issue of improvement.


<br>

Happy coding!

