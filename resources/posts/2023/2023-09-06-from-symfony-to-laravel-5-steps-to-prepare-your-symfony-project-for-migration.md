---
id: 394
title: "From Symfony to Laravel - 5 Steps to Prepare your Symfony Project for Migration"
perex: |
    Framework migration is a challenge few choose to take - yet in some cases, it makes sense for business, project health, and pure joy from coding.

    Once you know [the recipe](https://getrector.com/blog/how-to-migrate-legacy-php-applications-without-stopping-development-of-new-features), it clear the switch [is doable](https://getrector.com/blog/success-story-of-automated-framework-migration-from-fuelphp-to-laravel-of-400k-lines-application).

    Today, we'll look at the steps to prepare your Symfony project for future Laravel migration.
---

<blockquote class="blockquote mt-4 mb-4 text-center">
    "Luck favors the prepared... and brave."
</blockquote>


We start with steps that make the Symfony project easier to maintain, then move to more Symfony-Laravel bridge topics.

<br>

## 1. Make sure your Configs are *.php

At first, we make sure our `/config` directory contains only PHP files. This will help tools like ECS, PHPStan, and Rector to see the PHP configs, check them for errors like missing classes, and automate any migration.

We can **migrate YAML configs to PHP effortlessly** with a single CLI command run - using [symplify/config-transformer](https://github.com/symplify/config-transformer).

```bash
composer require symplify/config-transformer --dev
vendor/bin/config-transformer switch-format config
```

Don't forget to [update your Kernel loader](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/) to seek PHP files and you're done.

<br>

## 2. Prepare a custom script for TWIG to Blade conversion

I've learned this trick from [Pragmatic Programmer](https://www.amazon.com/Pragmatic-Programmer-Journeyman-Master/dp/020161622X). What if we need to process a high volume of data in a reliable way?

We could rewrite templates manually - in fact, if our project has 10 TWIG files, that's the saint way.

For a higher volume of data, we should take a different path - **create a one-time script to do one job and then perish.**

<br>

Does it sound tedious and long-term work? Reality is pretty straightforward. For my projects, it took a weekend train trip [from Cascais to Lisbon and back](https://twitter.com/VotrubaT/status/1627277318254100482)

<br>

Here is [the script I used](https://github.com/TomasVotruba/tomasvotruba.com/pull/1356/files#diff-ca5932af66e0307e206f9fd6fdd5242085113ee66b127ff40502ca8e358a70e9) - feel free to steal it.

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/236f5291-d9f5-466e-ac40-2927ad006323" class="img-thumbnail">

<br>

## 3. Understand the differences between Symfony and Laravel container

There **are 2 important difference** between these 2 containers:

First - the Symfony container is compiled into a huge PHP file and then dumped for caching purposes. This allows performance-heavy operations like service decoration, autowired setters, and compiler passes. **The Laravel container is built on the fly** - it's lighter and faster.

I've never noticed any difference in developer experience regarding this point.

<br>

The 2nd difference that I consider much more important is:

* that **Symfony registers every service in the config** and nothing else,
* **Laravel registers everything automatically as a singleton unless config defines it otherwise**.

You can read about the upsides, downsides and how to deal with them in [What I prefer about Laravel Dependency Injection over Symfony](/blog/what-i-prefer-about-laravel-dependency-injection-over-symfony)

<br>

I was most worried about the lack of Symfony [compiler passes](https://symfony.com/doc/current/service_container/compiler_passes.html). Laravel docs don't mention them at all. Why not?

Once you know it, the reason is simple - [different naming](/blog/from-symfony-to-laravel-can-laravel-even-compiler-pass) - **Laravel is compiler passes-ready**.

<br>

## 4. Create a parallel Laravel container

Would you cross this bridge to the other side?

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/ac1de6e6-2205-4709-a84e-3ee6e9ac6a14" alt="img-thumbnail">

<br>

<blockquote class="blockquote mt-5 mb-5">
	The success of every more significant migration is based on stability.
</blockquote>

That's why:

* We should never drop the old version unless we run stable on the next one
* We should have a fallback in case something goes wrong,
* and stable testing environment to be sure our project keeps running flawlessly

<br>

First, we ensure our project has a Symfony container factory in place. It will make flipping containers later on possible with a single line:

```php
use Psr\Container\ContainerInterface;

final class SymfonyContainerFactory
{
    public function create(): ContainerInterface
    {
        $kernel = new AppKernel();
        $kernel->boot();

        return $kernel->getContainer();
    }
}
```

Notice the use of the PSR container contract.

We use this Symfony container factory at entry-point levels like `bin/console`, `public/index.php` or abstract test cases.

<br>

Then, we create `LaravelContainerFactory` to handle services for the Laravel context. We create this container **in parallel** to the Symfony container and **keep the Symfony untouched**:

```php
use Psr\Container\ContainerInterface;
new Illuminate\Container\Container;

final class LaravelContainerFactory
{
    public function create(): ContainerInterface
    {
        $container = new Container();
        $container->singleton(SomeType::class);

        return $container;
    }
}
```

We register every **non-standard service to the `LaravelContainerFactory::create()` method**. This way, we can easily see what services are not registered in the Laravel container and add them later.

<br>

Now we have 2 containers - yay! But one of them is not used. One could call [that's a dead code](https://github.com/TomasVotruba/unused-public), right?

I'll show you how to use the other container in the next step.

<br>

## 5. Try the Laravel container in your tests

This is where **the fast feedback loop kicks in**. We'll be able to:

* Debug Laravel **migration on the fly, safely and quickly**,
* Keep the production codebase running on the original Symfony container.

Fast and safe.

I've used this technique to migrate this website, getrector.com, all my open-source packages and CLI tools, [ECS](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) and finally [Rector](/blog/rector-018-from-symfony-container-to-laravel-and-how-to-upgrade-your-extensions).

I was starting with a few weeks of real Laravel experience and learning mostly from error messages. That means if I can do it, you can do it too.

<br>

Again, we should have separated `AbstractSymfonyTestCase` that uses the Symfony container (or one based on `KernelTestCase`), and our container-based tests depend on it. We keep this file untouched:

```php
use PHPUnit\Framework\TestCase;

abstract class AbstractSymfonyTestCase extends TestCase
{
    protected function setUp(): void
    {
        $symfonyContainerFactory = new SymfonyContainerFactory();

        // add static caching if needed
        $this->container = $symfonyContainerFactory->create();
    }
}
```

Then our tests use it like:

```php
final class SomeTest extends AbstractSymfonyTestCase
{
    public function test(): void
    {
        $someType = $this->container->get(SomeType::class);

        $result = $someType->callForResult();
        $this->assertNotEmpty($result);
    }
}
```

<br>

Our goal is to replace the parent test from Symfony to Laravel one case and make the test pass simultaneously.

<br>


First, we create a new abstract test case, e.g., `AbtractLaravelContainerTestCase` where we'll use `LaravelContainerFactory`:

```php
use PHPUnit\Framework\TestCase;

abstract class AbstractLaravelTestCase extends TestCase
{
    protected function setUp(): void
    {
        $laravelContainerFactory = new LaravelContainerFactory();

        // add static caching if needed
        $this->container = $laravelContainerFactory->create();
    }
}
```

The final step is to **try to replace** the parent class and re-run the test:

```diff
-final class SomeTest extends AbstractSymfonyTestCase
+final class SomeTest extends AbstractLaravelTestCase
 {
     public function test(): void
     {
         $someType = $this->container->get(SomeType::class);

         $result = $someType->callForResult();
         $this->assertNotEmpty($result);
     }
 }
```

We will not change any other line because that **would turn our migration** into a refactoring. **The original test code must remain untouched and work for both containers.**

<br>

We run the tests, see what fails, fix it, iterate the feedback loop, and create pull-request for our single test. Merge and repeat. Again, we should not touch the original source code, only the `LaravelContainerFactory`.

That's it!

<br>

Thanks to this technique, we made [Rector tests 7x faster](https://getrector.com/blog/rector-018-how-we-made-tests-seven-times-faster) before we made the container switch. To give you perspective, the whole migration was finished within a week.

<br>

Happy coding!
