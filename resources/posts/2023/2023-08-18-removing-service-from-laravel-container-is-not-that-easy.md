---
id: 390
title: "Removing Service from Laravel Container is not that Easy"

perex: |
    Last month I successfully [switched the Symfony container for Laravel one](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) in Easy Coding Standard.

    The tiny container is a joy to work with - it consists of 2 files I can read and understand all its features. **I wanted to put this package into pressure test**, so I migrated the project I work on daily - [Rector](https://github.com/rectorphp/rector).
---

*Disclaimer: I have more experience with fixing leaking pipes in my flat than with Laravel, so if there is a better way of doing anything in this post, please let me know. I want to learn and write meaningful content. Thank you!*

<br>

I'll write about particular details in other posts, but today I'd like to focus on a feature that cost me the most energy and time to figure out. In ECS and Rector, there is a *skip* feature.

Let's say you register whole `NAMING` set to help you with variable/property namings but don't like the `RenamePropertyToMatchTypeRector` one.

```php
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SetList::NAMING,
    ]);

    $rectorConfig->skip([
        RenamePropertyToMatchTypeRector::class,
    ]);
};
```

The set already registers many services (rules), and we want to use all of them but one. That means we have to remove the service from the container - that way, it doesn't run in the file processor and doesn't change the code.

## How would You Remove Service?

The `RectorConfig` class simply extends the `Illuminate\Container\Container` class, so we have access to Laravel container logic.

Let's try the obvious one:

```php
$rectorConfig->forgetInstance(RenamePropertyToMatchTypeRector::class);
```

Now, the container should [forget about the instance](https://github.com/illuminate/container/blob/7ebfc9acfd5d5c7dda1ff5975927c6569651857f/Container.php#L1368-L1371). *It kind of does* - if we try to fetch the service from container, it will be created from fresh start (I think), but... everything else remains.

I've also found a more powerful [method `offsetUnset()`](https://github.com/illuminate/container/blob/7ebfc9acfd5d5c7dda1ff5975927c6569651857f/Container.php#L1469-L1478) that removed service from bindings and resolved as well, but still with no effect.

<br>

Could you guess the possible issues? It took me 2-3 hours to figure out because, in previous containers like Symfony and Nette, this removed services from the whole container. I thought I was misusing the Laravel container, so I paid attention exclusively to my code instead of debugging Laravel internals.

<br>

## What was the bug?

The single service was removed, but the file is still changed.

I'll give you a clue. The service that changes PHP files is registered like this:

```php
$rectorConfig->when(RectorNodeTraverser::class)
    ->needs('$rectors')
    ->giveTagged(RectorInterface::class);
```

How is the Rector rule registered?

```php
$rectorConfig->singleton($rectorClass);
$rectorConfig->tag($rectorClass, RectorInterface::class);
```

The `RectorNodeTraverser` still contains all the removed rules. But why?

<br>

If this bug happened to you, the answer just popped, or your frontal lobe.

<br>

What happens when we try to **remove a tagged service**?

* **It stays tagged**. Once we pass the tagged iterator to another service, it will get all the service references, including the "removed ones".

## How to Remove Tagged Service

We need to remove the `RenamePropertyToMatchTypeRector` services from our service, so it will not change the code. What can we do?

1. add some special filter to all tagged iterators - overriding the framework's functions means we have to maintain any internal changes too

2. check the skipped classes manually in the `RectorNodeTraverser` constructor - that would [memory lock](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock) us to duplicate it every other services we pass the tagged services to

3. remove the service from tagged services too

<br>

I like the last one because that **the behavior I expect when I remove any service from the container** - all its tags, calls, and references should be gone too. Like it never existed.

<br>

So what does [the `tag()` method](https://github.com/illuminate/container/blob/7ebfc9acfd5d5c7dda1ff5975927c6569651857f/Container.php#L525) do?

```php
$rectorConfig->tag($rectorClass, RectorInterface::class);
```

↓

```php
$this->tags[RectorInterface::class][] = $rectorClass;
```

It adds a reference to a `$tags` property. To remove the reference:

* we iterate through all the tags
* remove all cases where `$rectorClass` equals our class we want to remove

<br>

## Let's get Technical

It's like fixing a leak in water pipes - once we find the leaking weak spot, the fix is just an implementation detail.

So, the `$tags` property [is `protected`](https://github.com/illuminate/container/blob/7ebfc9acfd5d5c7dda1ff5975927c6569651857f/Container.php#L83-L88), so we can extend the behavior in child class and override it or use reflection and separate the remove.

<br>

I prefer the latter one, as it's easier to test and less coupled to the framework:

```php
use Illuminate\Container\Container;

function forgetInstance(Container $container, string $typeToForget): void
{
    $tagsReflectionProperty = new ReflectionProperty($container, 'tags');
    $tags = $tagsReflectionProperty->getValue($container);

    // here we iterate all tags
    foreach ($tags as $tagName => $taggedClasses) {
        foreach ($taggedClasses as $key => $taggedClass) {
            //Is it a match?
            if (is_a($taggedClass, $typeToForget, true)) {
                //let's remove it!
                unset($tags[$tagName][$key]);
            }
        }
    }

    $tagsReflectionProperty->setValue($container, 'tags', $tags);
}
```

Not pretty, but it gets the job done.

<br>

If we had a direct accessor or public `$tags` property, it would look much cleaner:

```php
use Illuminate\Container\Container;

function forgetInstance(Container $container, string $typeToForget): void
{
    foreach ($container->getTags() as $tagName => $taggedClasses) {
        foreach ($taggedClasses as $key => $taggedClass) {
            if (is_a($taggedClass, $typeToForget, true)) {
                unset($tags[$tagName][$key]);
            }
        }
    }

    $container->updateTags($tags);
}
```

<br>

We have the script. Now the time comes to try it in the wild - will it work, or will it fail?

```php
forgetInstance($rectorConfig, RenamePropertyToMatchTypeRector::class);
```

<br>

I dump the `RectorNodeTraverser` constructor with the `$rectors` collection... and there is one less rule! Yay!

<br>

That's it for today. I hope you've learned something, or at least I've used some weird obstructions that made you laugh. As always, let me know if you see a better way of doing things. Thanks!

<br>

Happy coding!
