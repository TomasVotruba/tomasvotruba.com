---
id: 393
title: "From Symfony to Laravel - Can Laravel even Compiler&nbsp;Pass?"
perex: |
    Do you want to migrate your Symfony project to Laravel and not sure if it "handles it"? Switching containers is pretty [straighfowrad](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) for the most parts.

    But can Laravel handle as advanced features as compiler passes?
---

Let's take it step by step and list the Symfony container life-cycle. It has 3 separate steps that run one after another:

* 1. Register services from various configs
* 2. **Post-process the registered services**
* 3. Dump the compiled container to cached PHP files

<br>

The first step is the syntax sugar difference between Laravel and Symfony. The last step is absent and is not needed in Laravel.

**The middle step is what Symfony calls *compiler passes*.**

One example for all in Easy Coding Standard - some checkers from PHP CodeSniffer/php-cs-fixer exclude each other and would run in an infinity loop, e.g., turn all spaces to tabs and turn all tabs to space.

ECS has a compiler pass to avoid this loop:

```php
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AvoidCheckersLoopCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $checkerClasses = [];

        // here, we check all the registered services
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $serviceClass = $definition->getClass();

            if ($this->isSniffOrFixerClass($serviceClass)) {
                $checkerClasses[] = $serviceClass;
            }
        }

        $this->ensureNoMutuallyExcludingCheckers($checkerClasses);
    }
}
```

As we know, the compiler passes in Symfony are run *after every service is registered**.

<br>

## How to achieve the same in Laravel?

One month ago, I [migrated the ECS container from Symfony to Laravel](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs), and I had to solve this issue. When I googled "compiler pass in Laravel", I got Symfony docs, my post about compiler passes, and the third link goes to Blade template documentation.


GPT is more helpful and shows me the `beforeResolving()` method. How do we use it to achieve a similar check?

<br>

This method has 2 use cases. The first way is the following:

```php
use Illuminate\Container\Container;

$container = new Container();
$container->beforeResolving(SomeType::class, function () {
    // call this
});
```

The callback in 2nd argument will be run before resolving of `SomeType` service. This means when a project needs `SomeType`, it will invoke this closure. But **we don't want to wait for a specific type; we want to call closure before resolving *any type***.

<br>

That's where the other way comes to the rescue:

```php
use Illuminate\Container\Container;

$container = new Container();
$container->beforeResolving(function () {
    // call this
});
```

That's the missing piece. You can probably figure out the rest now. I'll share my way of making the most out of this feature.

<br>

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

<br>

## Call Closure Once

This works, but it's also running before any service is built. Do we have 500 services? This closure is run 500 times. That's not very environmentally friendly.

We add a helper variable to ensure the callback is run just once:

```php
use Illuminate\Container\Container;

$hasRun = false;

$container = new Container();
$container->beforeResolving(function (Container $container) use (&$hasRun) {
    if ($hasRun) {
        return;
    }

    // ...

    $hasRun = true;
});
```

That's it! As always, have you found a better way of achieving the same result? Let me know, I want to use it.

<br>

Happy coding!
