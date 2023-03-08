---
id: 380
title: "Introducing Punchcard - Object Configs for Laravel"

perex: |
    What would you like to ride on a higway: a city bike or a Tesla car? **To move fast, we have to feel safe**.

    Last month I've [made a head jump to to Laravel](/blog/why-I-migrated-this-website-from-symfony-to-laravel) ecosystem.  The migration itself went very well, as most of the code is inutitive. There was just one clear bottle-neck: the array configs.
---

Sometimes, I remove config that were not needed. But sometimes a crucial line was missing and Laravel throws error about missing service.

<br>

Let's take the `config/view.php` as an example:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
```

## Problems with array configs

* There is too much comments to read the code between lines.

* It's not clear what is **a parameter name** and what **is used-defined values**. Both of them look like strings.

* The `env()` hides the type of the value. Is it a string? Is it an array or a boolean? We have to open the file and read the comment to know.

* What other options we can use in this config?

The rest of framework is quite polished for developer experience, so these arrays stand out of the crowd.

<br>

## What we need for config speed?

* strict-typed configs
* autocomplete with IDE
* class-based arguments so PHPStan and Rector can help
* e.g. services providers are not just `string[]`, but

```php
/**
 * @param array<class-string<\Illuminate\Support\ServiceProvider>> $providers
 */
```

<br>

I had clear idea for fluent config builder, that would be generated based on [`/config` directory](https://github.com/laravel/laravel/tree/10.x/config)
 from Laravel skeleton. I asked on Twitter to avoid the wheel invention: "has someone build this?"

I got a tip for **must-read [What about config builders?](https://stitcher.io/blog/what-about-config-builders) post** by Brent Roose.

<br>

Wow, that's it! Where can I download it?

But when I reached Brent, I got reply: "it's just an idea, no package".

<br>

Yet I was very happy to see that established Laravel developer has similar opinion on this topic. It's time to build it!


## Introducing Punchcard

1. Install package to your project:

```bash
composer require tomasvotruba/punchcard
```

<br>

2. Use it instead of arrays in your `/config` directory

Following code provides the same configuration as above. We use it on this very website for over a week.

```php
// config/view.php
use TomasVotruba\PunchCard\ViewConfig;

return ViewConfig::make()
    ->paths([__DIR__ . '/../resources/views'])
    ->compiled(__DIR__ . '/../storage/framework/views')
    ->toArray();
```

<br>

The `ViewConfig` is very simple class, but very powerful thanks to all the type declarations:

```php
<?php

namespace TomasVotruba\PunchCard;

final class ViewConfig
{
    /**
     * @var string[]
     */
    private array $paths = [];

    private ?string $compiled = null;

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param string[] $paths
     */
    public function paths(array $paths): self
    {
        $this->paths = $paths;
        return $this;
    }

    public function compiled(string $compiled): self
    {
        $this->compiled = $compiled;
        return $this;
    }

    /**
     * @return array<string, mixed[]>
     */
    public function toArray(): array
    {
        return [
            'paths' => $this->paths,
            'compiled' => $this->compiled,
        ];
    }
}
```

The configs are geneated on package release, so they're always up-to-date with Laravel and available to your autoload and IDE. No magic or fake `@methods` just pure PHP.

## Looking for feedback

This is my first Laravel package, so I want to hear your feedback: How can I make it better? More practical with less code? More strict when it comes to nested object?

The repository is here: [TomasVotruba/punchcard](https://github.com/TomasVotruba/punchcard)

<br>

Thank you and happy coding!
