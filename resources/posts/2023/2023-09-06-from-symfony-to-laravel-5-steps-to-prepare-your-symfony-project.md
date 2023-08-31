---
id: 394
title: "From Symfony to Laravel - 5 Steps to Prepare your Symfony Project"
perex: |
    Framework migration challange few choose take - yet in some cases, it makes sense for business, for project health and pure joy from coding. Once you know [the recipe](https://getrector.com/blog/how-to-migrate-legacy-php-applications-without-stopping-development-of-new-features), it clear the switch [is doable](https://getrector.com/blog/success-story-of-automated-framework-migration-from-fuelphp-to-laravel-of-400k-lines-application).

    Today we look on steps that will prepare your Symfony project for future Laravel migration.
---

<blockquote class="blockquote mt-4 mb-4 text-center">
    "Luck favors the prepared... and brave."
</blockquote>


We start with steps that makes Symfony project easier to maintain in general, than move to more Symfony-Laravel bridge topics.

<br>

## 1. Make sure your Configs are *.php

At first, we make sure our `/config` directory contains only PHP files. This will help tools like ECS, PHPStan and Rector to see the PHP configs, check them for erros like missing class and automate any migration.

Nowadays we can **migrate YAML configs to PHP effortlessly** with single CLI command run - using [symplify/config-transformer](https://github.com/symplify/config-transformer).

```bash
composer require symplify/config-transformer --dev
vendor/bin/config-transformer switch-format config
```

Don't forget to [update your Kernel loader](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/) to seek PHP files and your done.

<br>

## 2. Prepare custom script for TWIG to Blade conversion

I've learned this trick from [Pragmatic Programmer](https://www.amazon.com/Pragmatic-Programmer-Journeyman-Master/dp/020161622X). Sometimes we need process high volume of data, in reliable and verifiable way. We could rewrite templates manually - in fact, if our project has 10 TWIG files, that's the saint way.

For higher volume of data, we should take a different path - **create a one time script to do one job and then perish.**

<br>

Does it sound tedious and long-term work? Reality is pretty straightforward. For my projects it took a weekend train trip [from Cascais to Lisbon and back](https://twitter.com/VotrubaT/status/1627277318254100482)

<br>

Here is [the script I used](https://github.com/TomasVotruba/tomasvotruba.com/pull/1356/files#diff-ca5932af66e0307e206f9fd6fdd5242085113ee66b127ff40502ca8e358a70e9) - feel free to steal it.

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/236f5291-d9f5-466e-ac40-2927ad006323" class="img-thumbnail">

<br>

## 3. Understand differences between Symfony and Laravel container

I wrote about this topic before, so I refer posts in here to learn from. There **are 2 important difference** between these 2 containers:

**First is Symfony container is compiled** to huge PHP file and then dumped for caching purposes. This allows performance heavy operations like service decoration, autowired setters and compiler passes. **The Laravel container is build on the fly** - it's lighter and faster. I've never noticed any difference in developer experience in this point to be honest.

<br>

The 2nd difference I've actually noticed is:

* that **Symfony registers every service in the config** and nothing else,
* **Laravel registers everything automatically as a singleton, unless config defines it otherwise**

You can read about upsides, downsides and how to deal with them in [What I prefer about Laravel Dependency Injection over Symfony](/blog/what-i-prefer-about-laravel-dependency-injection-over-symfony)

<br>

I was worried about lack of compiler passes the most. In Symfony, they allows post-build operations around every service. But Laravel docs doesn't mention them at all. I've learned the reason is [different naming](/blog/from-symfony-to-laravel-can-laravel-even-compiler-pass) - **Laravel is compiler passes-ready**.

<br>

## 4. Create parallel Laravel container

Would you try to cross this bridge to the other side?

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/ac1de6e6-2205-4709-a84e-3ee6e9ac6a14" alt="img-thumbnail">

<br>

Success of every greater migration is based on stability. That's why:

* we should never drop the old version unless we run stable on a the next one
* we should have a fallback in case something goes wrong,
* and stable testing environment to be sure our project keeps running flawlessly

<br>

First, we make sure our project has Symfony container factory in place. This will make flipping container later on possible with single line:

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

Notice the use of PSR container contract.

This Symfony container factory is used in entrypoint level like `bin/console`, `public/index.php` or abstract test case.

<br>

Then we create `LaravelContainerFactory` to handle services for Laravel context. We create this container **in parallel** to Symfony container and **keep the Symfony untouched**:

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

We register every **non-standard service to the `LaravelContainerFactory::create()` method**. This way we can easily see what services are not registered in Laravel container and add them later on.

<br>

Now we have 2 containers and one of them is not used. One could call [that a dead-code](https://github.com/TomasVotruba/unused-public), right?

Not really, I'll show you how to use the other container in a next step.

<br>

## 5. Try Laravel container in your tests

This is where fast feedback loop kicks-in. We'll be able to:

* **debug Laravel migration on the fly and quickly**,
* keep the production codebase running on original Symfony container.

Fastly and safe.

I've used this technique to migrate this website, getrector.com, all my open-source packages and CLI tools, [ECS](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) and finally [Rector](/blog/rector-018-from-symfony-container-to-laravel-and-how-to-upgrade-your-extensions). Starting with few weeks of real Laravel experience and learning mostly from error messages. That means if I can do it, you can do it too.

<br>

Again, we should have separated `AbstractSymfonyTestCase` that uses Symfony container (or one based on `KernelTestCase`) and our container-based tests depends on it. We keep this file untouched:

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

Our goal is to replace the parent test from Symfony to Laravel one case and make test passing at the same time.

<br>


First, we create new abstract test case, e.g. `AbtractLaravelContainerTestCase` where we'll use `LaravelContainerFactory`:

```php
use PHPUnit\Framework\TestCase;

abstract class AbstractLaravelTestCase extends TestCase
{
    protected function setUp(): void
    {
        $laravelContainerFactory = new LaraveContainerFactory();

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

We will not change any other line, because that **would turn our migration** to a refactoring. **The original test code must remain untouched and work for both containers.**

<br>

We run the tests, see what fails, fix it, iterate feedback loop and create pull-request for our single test. Merge and repeat. Again, we should not touch the original source code, only the `LaraveContainerFactory`.

That's it!

<br>

Thanks to this technique, we made [Rector tests 7x faster](/blog/rector-018-how-we-made-tests-seven-times-faster) before we made the container switch. To give you perspective, the whole migration was finished withing a week.

<br>

Happy coding!
