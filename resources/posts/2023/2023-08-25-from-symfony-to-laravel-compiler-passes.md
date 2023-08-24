---
id: 393
title: "From Symfony to Laravel - Compiler&nbsp;Passes"
perex: |
    Do you plan to migrate your Symfony project to Laravel, and not sure if it "handle it"? Switching container is pretty [straighfowrad for the most parts](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs)...

    But how can Laravel as advanced feature as compiler passes?
---

Let's take it step by step, and list the Symfony container life-cycle. It has 3 separated steps that have to run in series:

1. register services from various configs
2. post-process the registered services
3. dump the compiled container to cached PHP files

<br>

First step is is syntax sugar differences of Laravel and Symfony share. Last step is not present and not needed in Laravel.

The middle step is what Symfony calls *compiler passes*.

One example for all in Easy Coding Standard - few checkers from PHP CodeSniffer and php-cs-fixer exclude each other and would run in infinity loop, e.g. turn all spaces to tabs and turn all tabs to space. ECS has a compiler pass that is responsibly for warning about these twins and stop the run:

```php

final MutualExcludingCheckerCompilerPass implements CompilerPass
{
    $checkerClasses = [];

    // here we check all the registered services
    foreach ($container->getDefinitions() as $definition) {
        $serviceClass = $definition->getClass();
        if ($this->isSniffOrFixerClass($serviceClass)) {
            $checkerClasses[] = $serviceClass;
        }
    }

    $this->ensureNoMutuallyExcludingCheckers($checkerClasses);
}
```

As we know, the compiler passes in Symfony are run *after every service is registered**.

<br>

## How to achieve the same in Laravel?

One month ago I [migrated ECS container from Symfony to Laravel](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) and I had to solve this issue. When I google "compiler pass in Laravel", I get Symfony docs, my post about compiler passes and third links goes to Blade template documentation.


GPT is more helpful and show me `beforeResolving()` method. How do we use it to achieve similar check?

This method can be used in 2 ways. The first way is following:

```php
use Illuminate\Container\Container;

$container = new Container();
$container->beforeResolving(SomeType::class, function () {
    // call this
});
```

The callback in 2nd argument will be run before resolving of `SomeType` service. This means when project needs `SomeType`, it will invoke this closure. But we don't want to wait for any specific type, we want to call closure before resolving *any type*.

<br>

That's where the other way comes to rescue:

```php
use Illuminate\Container\Container;

$container = new Container();
$container->beforeResolving(function () {
    // call this
});
```

That's the missing piece. You can probably figure out the rest now. I'll share my way of making the most out of this feature.

<br>

## Call Closure Once

I've added a closure that checks for conflicting checkers:

```php
use Illuminate\Container\Container;

$container = new Container();
$container->beforeResolving(function (Container $container): void {
    $sniffsIterator = $container->tagged(\PHP_CodeSniffer\Sniffs\Sniff::class);
    $fixersIterator = $container->tagged(\PhpCsFixer\Fixer\FixerInterface::class);

    $checkerClasses = [];
    foreach ($sniffxIterator as $sniff) {
        $checkerClasses[] = get_class($sniff);
    }

    foreach ($fixersIterator as $fixer) {
        $checkerClasses[] = get_class($fixer);
    }

    $this->ensureNoMutuallyExcludingCheckers($checkerClasses);
});
```

This works, but it's also running before any service is build. Do we have 500 services? This closure is run 500-times. That's not very environment friendly.

<br>

We add a helper variable, to ensure the callback is run just once:

```php
use Illuminate\Container\Container;

$hasRun = false;

$container = new Container();
$container->beforeResolving(function (Container $container) use (&$hasRun) void {
    if ($hasRun) {
        return;
    }

    // ...

    $hasRun = true;
});
```

That's it!

<br>

Happy coding!
