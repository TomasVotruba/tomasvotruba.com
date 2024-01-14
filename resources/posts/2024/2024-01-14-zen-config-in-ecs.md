---
id: 401
title: "Zen Config in ECS 12"
perex: |
    Easy Coding Standard focuses on easy run, setup, and use. From composer requirement through the automated setup to the config.

    The config was based on rather cumbersome Symfony closure service configs. But last year, I [switched the DI container to Laravel](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs).

    Eventually, this opened the door to further innovation that I'll introduce today.
---

In the previous ECS version, you could run the `vendor/bin/ecs` command at the very start. If there were no `ecs.php` config, it would generate it for you:

```php
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->rules([
        NoUnusedImportsFixer::class,
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::ARRAY,
    ]);
};
```

So this would be the contents of the generated `ecs.php`. As you can see, there is some closure, some `return` of the void, and some `SetList` enum that contains some constants.

There's quite a lot of code clutter, that makes it hard to see the actual configuration. Inspired by Amazon buy-with-single-click and Laravel 11 single-file config, I decided to simplify the config even more.

## Introducing `ECSConfig::configure()`

In the ECS 12.1 version, we're introducing a simple way to set configuration with the static method `ECSConfig::configure()`. Type
only what you need, and you're done:

```php
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRules([
        NoUnusedImportsFixer::class,
    ])
    ->withPreparedSets(psr12: true, arrays: true);
```

* no constant enum list
* no closure
* no void
* only what you want to configure

The new method `withPreparedSets()` contains **all prepared sets** that come with ECS. You can **enable them with autocomplete in your IDE**. No more copy-pasting from README or looking for the right `*Set` class.

## php-cs-fixer sets? We got you covered

One of the most popular questions we get in the issue tracker is: "How can I use sets from PHP-CS-Fixer in ECS?"

We've added `withPhpCsFixerSets()` that **contains all sets from PHP-CS-Fixer**. You can enable them with autocomplete in your IDE:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpCsFixerSets(perCS20: true, doctrineAnnotation: true);
```

No more searching documentation for the set names and copy-pasting them to the ECS config. Stay in the config file and enable sets you like.

We actually generate parameters for this method from PHP-CS-Fixer code, so it's always up to date.

## Include Root Files

What are the paths we apply coding standards to? Usually, it's the `/src` and `/tests` directories. But more often, we also need to apply them to the root files. Then we'll end up with such a long file list:

```php
return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
        __DIR__ . '/scoper.php',
        __DIR__ . '/composer-unused.php',
    ]);
```

When we add new PHP files to the root and forget... ECS forgets it, too.

<br>

In ECS 12, we've added a **way to automate this for the future and save you work**:

```diff
 return ECSConfig::configure()
     ->withPaths([
         __DIR__ . '/src',
         __DIR__ . '/tests',
-        __DIR__ . '/ecs.php',
-        __DIR__ . '/rector.php',
-        __DIR__ . '/scoper.php',
-        __DIR__ . '/composer-unused.php',
-    ]);
+    ])
+    ->withRootFiles();
```

## One more thing...

Do you use ECS across a variety of projects? Do you want to run the coding standard the same way in each project so you don't have to check which tool you're using?

Then you'll probably use [composer scripts](https://blog.martinhujer.cz/have-you-tried-composer-scripts/). Then, we can always run 2 same commands regardless of the tool or project:

```bash
composer check-cs
```

And to fix coding standards:

```bash
composer fix-cs
```

We make this simple with a new command that updates your `composer.json` for you - just run:

```bash
vendor/bin/ecs scripts
```

It will add these 2 scripts for you:

```diff
 {
+    "scripts": {
+        "check-cs": "vendor/bin/ecs check --ansi",
+        "fix-cs": "vendor/bin/ecs check --fix --ansi"
+    }
 }
```

Including `--ansi` so we can see pretty colors in CI.

<br>

That's it for today. I hope you've enjoyed the new ECS config features - update and try them today.

<br>

Happy coding!
