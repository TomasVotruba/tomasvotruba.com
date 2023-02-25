---
id: 214
title: "7 News and Changes in Symplify 6"
perex: |
    Do you use Easy Coding Standard, Package Builder or Statie? Do you need to upgrade safely? **Do you want to benefit from new features?**

    This post shows 7 news and changes, that might affect you (in a good way).

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
    Updated ECS YAML to PHP configuration since **ECS 8**.
---

What is **new**?

## EasyCodingStandard

### 1. Run Checker only on Specific Path

<a href="https://github.com/symplify/symplify/pull/1537" class="btn btn-dark btn-sm pull-right mt-2 mb-2">
    &nbsp;
    See PR #1357
</a>

**I really love this feature, because it makes a lot of custom boiler code go away.**

In old *Symplify 5*, when you needed to run sniff only on `/tests`, you had to create own config, e.g. `ecs-only-for-tests.php` and run it separately.

```bash
vendor/bin/ecs check src --config ecs.php
vendor/bin/ecs check tests --config tests-only-ecs.php
```

That was way too complicated, right?

<br>

In new *Symplify 8*, you can use just one config with `only` option instead:

```php
// ecs.php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // all rules must be registered
    $services->set(BasicSniff::class);
    $services->set(AnotherSniff::class);

    $parameters = $containerConfigurator->parameters();
    // here you can configure, what rules should only check particular paths
    $parameters->set(Option::ONLY, [
        AnotherSniff::class => [
            __DIR__ . '/tests/'
        ]
    ]);
};
```

```yaml
vendor/bin/ecs check src tests
```

It's basically an inversion of `skip` parameter.

<br>
<br>
<br>

What has **changed**?

### 2. `*.yaml` → `*.php`

As Symfony is [moving to *.php](https://github.com/symfony/symfony/issues/37186) configuration, Symplify does too.

## EasyCodingStandard

### 3. Sets are Now In defined in `SetList` Constants

Why? The sets are only string references, so its useless for human to remember them. Why not let IDE help us?

```diff
 <?php

 // ecs.php

 declare(strict_types=1);

 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
+use Symplify\EasyCodingStandard\ValueObject\Option;
+use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

 return function (ContainerConfigurator $containerConfigurator): void {
-    $containerConfigurator->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/php71.php');
+    $containerConfigurator->import(SetList::PHP_71);
 };
```

### 4. ~~exclude_checkers~~ → `skip`

People confused this options and created *WTF* issues. That's why the `exclude_checkers` is now merged in `skip`, so you have less option names to remember:

```diff
 // ecs.php
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
+use Symplify\EasyCodingStandard\ValueObject\Option;

 return function (ContainerConfigurator $containerConfigurator): void {
     $parameters = $containerConfigurator->parameters();
-    $parameters->set('exclude_checkers', [
+    $parameters->set(Option::SKIP, [
-        SomeFixer::class
+        SomeFixer::class => null,
     ]);
 };
```

## PackageBuilder

### 5. Introducing `FinderSanitizer`

Do you like `SplFileInfo` that is 100 % sure the file exists? In that case, you use `Symplify\PackageBuilder\FileSystem\SmartFileInfo` instead of `SplFileInfo`. The easiest way to use it is via `FinderSanitizer` that is now available via `symplify/package-builder` package:

```diff
 <?php

 // ecs.php

 declare(strict_types=1);

 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
+use Symplify\EasyCodingStandard\ValueObject\Option;

 return function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();
-    $services->set(Symplify\EasyCodingStandard\Finder\FinderSanitizer);
+    $services->set(Symplify\PackageBuilder\FileSystem\FinderSanitizer);
 };
```

### 6. ~~ConfigurableCollectorCompilerPass~~ → `AutowireArrayParameterCompilerPass`

If you know collectors, you're using `ConfigurableCollectorCompilerPass`. It saves you so much time with configuration. The problem with that compiler pass, you still had to go to config to set it up, for no real advantage. And extra work for [no benefit sucks](/blog/2019/02/14/why-config-coding-sucks/). Also, there is big change someone will [forget it](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) and create a bug.

So instead, *Symplify 6* adds better system to pass collected services of certain type to single service - **[autowired arrays](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette/)**:

```diff
<?php declare(strict_types=1);

 use Symfony\Component\DependencyInjection\ContainerInterface;
-use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;
+use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

 final class AppKernel extends Kernel
 {
     protected function build(ContainerBuilder $containerBuilder): void
     {
-        $containerBuilder->addCompilerPass(new ConfigurableCollectorCompilerPass());
+        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
```

[This post](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette/) explains how to use it without ever touching config again.

## CodingStandard

### 7. ~~RemoveUselessDocBlockFixer~~ → `NoSuperfluousPhpdocTagsFixer`

`RemoveUselessDocBlockFixer` was removed, since [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) now provides `NoSuperfluousPhpdocTagsFixer` with similar features:

```diff
 // ecs.php
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

 return function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();

-    $services->set(Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer::class);
+    $services->set(PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class);
 };
```

That's all. It was easy, right?

<br>

Happy coding!
