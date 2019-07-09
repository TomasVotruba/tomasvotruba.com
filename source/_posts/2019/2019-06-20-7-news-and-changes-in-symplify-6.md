---
id: 214
title: "7 News and Changes in Symplify 6"
perex: |
    Do you use Easy Coding Standard, Package Builder or Statie? Do you need to upgrade safely? **Do you want to benefit from new features?**
    <br>
    <br>
    This post shows 7 news and changes, that might affect you (in a good way).
tweet: "New Post on #php 🐘 blog: 7 News and Changes in #symplify 6  #ecs"
---

What is **new**?

## EasyCodingStandard

### 1. Run Checker only on Specific Path

<a href="https://github.com/Symplify/Symplify/pull/1537" class="btn btn-dark btn-sm pull-right mt-2 mb-2">
    <em class="fab fa-github fa-fw"></em>
    &nbsp;
    See PR #1357
</a>

**I really love this feature, because it makes a lot of custom boiler code go away.**

In old *Symplify 5*, when you needed to run sniff only on `/tests`, you had to create own ruleset and run it separately:

```yaml
# ecs.yaml
services:
    BasicSniff: ~
```

```yaml
# tests-only-ecs.yaml
services:
    AnotherSniff: ~
```

```bash
vendor/bin/ecs check src tests # --config ecs.yaml by default
vendor/bin/ecs check tests --config tests-only-ecs.yaml
```

That was way too complicated, right?

<br>

In new *Symplify 6*, you can use just one config with `only` option instead:

```yaml
# ecs.yaml
services:
    BasicSniff: ~
    AnotherSniff: ~

parameters:
    only:
        AnotherSniff:
            - '*/tests/*'
```

```yaml
vendor/bin/ecs check src tests
```

It's basically an inversion of `skip` parameter.

<br>
<br>
<br>

What has **changed**?

### 2. `*.yml` → `*.yaml`

As Symfony is [moving to *.yaml](https://github.com/symfony/demo/tree/master/config) suffixes, Symplify does too.

## EasyCodingStandard

### 3. Sets are Now In `/set` Directory

Why? The `/config` directory contains `services.yaml` in Symfony application. It got cluttered with all the prepared sets.

```diff
 # ecs.yaml
 imports:
-    - { resource: 'vendor/symplify/easy-coding-standard/config/php71.yml' }
-    - { resource: 'vendor/symplify/easy-coding-standard/config/common.yml' }
-    - { resource: 'vendor/symplify/easy-coding-standard/config/clean-code.yml' }
+    - { resource: 'vendor/symplify/easy-coding-standard/config/set/php71.yaml' }
+    - { resource: 'vendor/symplify/easy-coding-standard/config/set/common.yaml' }
+    - { resource: 'vendor/symplify/easy-coding-standard/config/set/clean-code.yaml' }
```


### 4. ~~exclude_checkers~~ → `skip`

People confused this options and created *WTF* issues. That's why the `exclude_checkers` is now merged in `skip`, so you have less option names to remember:

```diff
 parameters:
-    exclude_checkers:
-        - 'PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer'
+    skip:
+        PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer: ~
```

## PackageBuilder

### 5. Introducing `FinderSanitizer`

Do you like `SplFileInfo` that is 100 % sure the file exists? In that case, you use `Symplify\PackageBuilder\FileSystem\SmartFileInfo` instead of `SplFileInfo`. The easiest way to use it is via `FinderSanitizer` that is now available via `symplify/package-builder` package:

```diff
 services:
-    Symplify\EasyCodingStandard\Finder\FinderSanitizer: ~
+    Symplify\PackageBuilder\FileSystem\FinderSanitizer: ~
```

### 6. ~~ConfigurableCollectorCompilerPass~~ → `AutowireArrayParameterCompilerPass`

If you know [collectors](/clusters/#collector-pattern-the-shortcut-hack-to-solid-code), you're using `ConfigurableCollectorCompilerPass`. It saves you so much time with configuration. The problem with that compiler pass, you still had to go to config to set it up, for no real advantage. And extra work for [no benefit sucks](/blog/2019/02/14/why-config-coding-sucks/). Also, there is big change someone will [forget it](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) and create a bug.

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

`RemoveUselessDocBlockFixer` was removed, because PHP CS Fixer now provides `NoSuperfluousPhpdocTagsFixer` with similar features:

```diff
 # ecs.yaml
 services:
-    Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer: ~
+    PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer: ~
```


That's all. It was easy, right?

<br>

Happy coding!