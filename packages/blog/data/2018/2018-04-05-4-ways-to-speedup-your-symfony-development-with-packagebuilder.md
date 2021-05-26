---
id: 89
title: "4 Ways to Speedup Your Symfony Development with PackageBuilder"
perex: |
    Symplify 4 was released and with it also one package, that contains all the Symfony tweaks that Symplify packages use.
    <br><br>
    Throwable render? Test services without public violation? Load parameters with glob? We got you covered!
tweet: "New Post on My Blog: 4 Ways to Speedup Your Symfony Development with PackageBuilder"
tweet_image: "/assets/images/posts/2018/symplify-4-pb/error.png"
---

Here are 4 news that were added in Symplify 4 and that you can use in your application right away.

Just install it...

```bash
composer require symplify/package-builder
```

...and enjoy [more than one](https://github.com/symplify/package-builder) of these 4 new features:

## 1. Console-Like `-vvv`-Aware Renders for Exceptions and Errors

<a href="https://github.com/symplify/symplify/pull/732" class="btn btn-dark btn-sm mt-2 mb-3 pull-left">
    Check the PR #732
</a>

<a href="https://github.com/symplify/symplify/pull/720" class="btn btn-dark btn-sm mt-2 mb-3 ml-2">
    Check the PR #720
</a>

If you use Symfony Console you are probably familiar with these errors and with `-vvv` to get full exception trace:

<img src="/assets/images/posts/2018/symplify-4-pb/error-without-and-with-vvv.gif" class="img-thumbnail">

Also works with `Error` like `ParseError`. That is super handy, useful and universal.

**But what if you need to use it standalone error reporting. e.g before console build?**

```php
$containerFactory = new ContainerFactory();
$container = $containerFactory->createFromConfig('config-not-found.yml');

$application = $container->get(Application::class);
$application->run();
```

Well, you could use `SymfonyStyle`:

```php
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

try {
    $containerFactory = new ContainerFactory();
    $containerFactory->createFromConfig('config-with-parse-error.yml');

    $application = $container->get(Application::class);
    $application->run();
} catch (Throwable $throwable) {
    (new SymfonyStyle(new ArgvInput(), new ConsoleOutput()))->error($throwable);
}
```

And that will get you rather chaotic report:

```bash
 [ERROR] Symfony\Component\Yaml\Exception\ParseException: Unable to parse at line 9 (near "@# global templates
         variables"). in /var/www/tomasvotruba.com/vendor/symfony/yaml/Parser.php:415
         Stack trace:
         #0 /var/www/tomasvotruba.com/vendor/symfony/yaml/Parser.php(454): Symfony\Component\Yaml\Parser->doParse(' @#
         global temp...', 768)
         #1 /var/www/tomasvotruba.com/vendor/symfony/yaml/Parser.php(315): Symfony\Component\Yaml\Parser->parseBlock(8,
         '@# global templ...', 768)
         #2 /var/www/tomasvotruba.com/vendor/symfony/yaml/Parser.php(95): Symfony\Component\Yaml\Parser->doParse(Array,
         768)
         #3 /var/www/tomasvotruba.com/vendor/symfony/yaml/Parser.php(62):
         Symfony\Component\Yaml\Parser->parse('imports:\n    - ...', 768)
         #4 /var/www/tomasvotruba.com/vendor/symfony/dependency-injection/Loader/YamlFileLoader.php(621):
         Symfony\Component\Yaml\Parser->parseFile('/var/www/tomasv...', 768)
         #5
         /var/www/tomasvotruba.com/vendor/symplify/package-builder/src/Yaml/AbstractParameterMergingYamlFileLoader.php(52
         ): Symfony\Component\DependencyInjection\Loader\YamlFileLoader->loadFile('/var/www/tomasv...')
         #6 /var/www/tomasvotruba.com/vendor/symfony/config/Loader/DelegatingLoader.php(40):
         Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader->load('/var/www/tomasv...', NULL)
         #7 /var/www/tomasvotruba.com/vendor/symplify/statie/src/DependencyInjection/StatieKernel.php(43):
         Symfony\Component\Config\Loader\DelegatingLoader->load('/var/www/tomasv...')
         #8 /var/www/tomasvotruba.com/vendor/symfony/http-kernel/Kernel.php(614):
        ...
```

### How to Get Nice Error Reports Even out of Console Application Scope?

Do you need this to work on your CLI app? Thanks to [Ondra Machulda](https://github.com/ondram)'s motivation [issues](https://github.com/symplify/symplify/pull/716) I came with decoupled Symfony\Console Application logic.

It's named `Symplify\PackageBuilder\Console\ThrowableRenderer` and use it like this:

```php
use Symplify\PackageBuilder\Console\ThrowableRenderer;

try {
    $containerFactory = new ContainerFactory();
    $containerFactory->createFromConfig('config-not-found.yml');

    $application = $container->get(Application::class);
    $application->run();
} catch (Throwable $throwable) {
    (new ThrowableRenderer())->render($throwable);
}
```

**And you'll get always nice errors for any `Throwable` :). Work anywhere right away and also respects `-vvv` option.**

<br>

## 2. Drop Manual `public: true` for Every Service You Test

<a href="https://github.com/symplify/symplify/pull/680" class="btn btn-dark btn-sm mt-2 mb-3">
    Check the PR #680
</a>

If you need to test a service, this is the most common way to test it using DI:

```php
final class ChangelogLinkerTest extends AbstractContainerAwareTestCase
{
    protected function setUp(): void
    {
        $this->changelogLinker = $this->container->get(ChangelogLinker::class);
    }

    // ...
}
```

But if you call it like this, you're informed that it must be public.

To make that happen, developers will take one of 2 paths. Both with high maintainability:

### 1. Public for Every Tested Class

```yaml
services:
    SomeNamespace\:
        resource: '..'

    SomeNamespace\SomeClass:
        public: true
```

### 2. Custom Tests-only Configs

```yaml
# services-tests.yml
services:
    SomeNamespace\SomeClass:
        public: true
```

Both these configs rely on your manual updates. That!s not a way to go - programming should be easy, fun and without any triggers in our heads.

### How to Overcome This?

Just add `Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass`:

```php
final class AppKernel extends Kernel
{
    // ...

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
    }
}
```

It detects PHPUnit run and adds public to each service, so you don't have to add it for every new service your set.

Setup & forget.

<br>

## 3. Autowire Singly-Implemented Interfaces

<a href="https://github.com/symplify/symplify/pull/645" class="btn btn-dark btn-sm mt-2 mb-3">
    Check the PR #645
</a>

Autowiring works great in combination with PSR-4 autoloading since [Symfony 3.4](https://github.com/symfony/symfony/pull/25282). But what about **3-rd party services that have interfaces**?

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    Symfony\Component\Console\Input\ArgvInput: ~
    Symfony\Component\Console\Output\ConsoleOutput: ~
```

If you use `Symfony\Component\Console\Input\InputInterace`, you'll get error of missing implementation.

To solve it you need to use an alias for every class that implements an interface:

```yaml
# app/config/services.yml
services:
    _defaults:
        autowire: true

    Symfony\Component\Console\Input\ArgvInput: ~
    Symfony\Component\Console\Input\InputInterace:
        alias: Symfony\Component\Console\Input\ArgvInput

    Symfony\Component\Console\Output\ConsoleOutput: ~
    Symfony\Component\Console\Output\OutputInterace:
        alias: Symfony\Component\Console\Output\ConsoleOutput
```

This way, you're actually being punished for using clean code and separation of interfaces in your code, because using `Symfony\Component\Console\Input\ArgvInput` would be easier.
But is it really necessary to break SOLID principles just to comply with Symfony behaviors? I don't think that framework should enforce bad design to your application.

### How to fix this?

I got inspired by [Register singly-implemented interfaces when doing PSR-4 discovery](https://github.com/symfony/symfony/pull/25282) pull-request in Symfony and by Nette default behavior.

```php
namespace App;

use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;

final class AppKernel extends Kernel
{
    // ...
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
```

And then clean your configs the same way PSR-4 autodiscovery works:

```diff
 # app/config/services.yml
 services:
     _defaults:
         autowire: true

     Symfony\Component\Console\Input\ArgvInput: ~
-    Symfony\Component\Console\Input\InputInterace:
-        alias: Symfony\Component\Console\Input\ArgvInput

     Symfony\Component\Console\Output\ConsoleOutput: ~
-    Symfony\Component\Console\Output\OutputInterace:
-        alias: Symfony\Component\Console\Output\ConsoleOutput
```

<br>

## 4. How to Decouple Parameters to multiple files in Safe Way?

<a href="https://github.com/symplify/symplify/pull/745" class="btn btn-dark btn-sm mt-2 mb-3">
    Check the PR #745
</a>

Do you prefer to decouple long parameter list to multiple files and them with [Glob](https://symfony.com/blog/new-in-symfony-3-3-import-config-files-with-glob-patterns)?

```yaml
# app/config/config.yml
imports:
    - { resource: 'framework/*.yml' }
```

In `/framework` directory there 2 files:

```yaml
# app/config/framework/symfony.yml
parameters:
    framework:
        symfony:
            controller: '<?php "some Symfony code"'
```

and

```yaml
# app/config/framework/laravel.yml
parameters:
    framework:
        laravel:
            controller: '<?php "some Laravel code"'
```

How many items will `framework` parameter have? 2? 1? 0?

**One is correct**. And which one? `laravel` or `symfony`? Well, according the `YamlFileLoader`, that [*last wins* approach](https://github.com/symfony/symfony/blob/f77c1d0d0996cc4723bff0411c8b75fe6a575bc8/src/Symfony/Component/DependencyInjection/Loader/YamlFileLoader.php#L135) is used. So probably `symfony`... but it doesn't matter, because you need them all.

### How to Prefer Merging of Parameters?

The official statement is to [create `Extension`, `Configuration`, `Bundle` and merge class](https://github.com/symfony/symfony/issues/26713), which and then add a custom implementation of [parameter binding](https://symfony.com/blog/new-in-symfony-3-4-local-service-binding) and other Symfony parameters related features like composing of parameters, env variables and etc. I asked for this option to be allowed with no BC break in [the issue](https://github.com/symfony/symfony/issues/26713), but it seems it's not needed enough.

Symplify actually followed the suggested approach and **it was a lot of duplicated code from Symfony\DependencyInjection that barely worked**.

To save many duplicated classes and take advantage of all Symfony parameter features you could overload `YamlFileLoader`, where parameters are merged together:

```yaml
# app/config/framework/symfony.yml
parameters:
    framework:
        symfony:
            controller: '<?php "some Symfony code"'
        laravel:
            controller: '<?php "some Laravel code"'
```

Do you need this? Just use `Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader` in your `Kernel` class:

```php
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    // ...

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $kernelFileLocator = new FileLocator($this);

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($container, $kernelFileLocator),
            new class($container, $kernelFileLocator) extends AbstractParameterMergingYamlFileLoader {
            },
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
```

The class is `abstract`, so you can modify it **in any way you need**.

<br>

Happy package building!
