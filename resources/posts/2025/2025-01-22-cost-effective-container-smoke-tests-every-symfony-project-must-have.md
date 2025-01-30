---
id: 427
title: "Cost-effective Container Smoke Tests every Symfony Project must have"
perex: |
    Today, I'll share a trademark secret that allows us to move fast and make huge changes without fear.

    When we're starting a new [Symfony 2.8-7.2 upgrade project](/blog/off-the-beaten-path-to-upgrade-symfony-28-to-72), we cover it with a couple of tests first. These tests are not units, but smoke tests - with a couple of lines they cover a huge portion of the Symfony framework layer we use.

    With a couple of lines, we can cover complex container operations and avoid most dummy yet destructive bugs while working with services.
---

## Why container smoke tests?

Just this week container smoke tests saved me from 2 hard-to-find bugs. Every unit, integration, and e2e test was passing, but this one did not. These bugs are impossible to spot because they're not the exclusive business logic we typically test. They're a **blend of business logic and Symfony glue**. We kind of hope Symfony did work, but it doesn't have to always be the case.

* These tests also **increase confidence in our project**.
* They give us a robust notion: if we move something to the other side or repository, it will still work.
* We can add them all a in **single working day** and they work for us for the rest of the project life.

## Smoke tests without Complexity

First, we should define what we mean by "smoke tests." We talk about tests without databases, external connections, or mocks. We talk about purely exclusively DI container tests.

Tests extending `KernelTestCase` or `WebTestCase` often create a couple of services dependent on the database and require a more complex setup. We won't use them either. Want to run these tests:

* quickly,
* locally,
* using bare PHPUnit
* and put them on CI right now to cover our back instantly.

## Timeless Tests

The following tests must run on any of [Symfony 2.8-7.2](/blog/off-the-beaten-path-to-upgrade-symfony-28-to-72) versions. We don't want to depend on the framework version and maintain it once we upgrade Symfony. They should cover us even when we change the major Symfony version, so we know the upgrade is safe.

## Simple Container over Kernel/Web test cases

Saying that we create **a simple container test case**.

Why not re-use `KernelTestCase` or `WebTestCase`? They lead to heavy loading and are very slow. Moreover, they change a lot in Symfony 3-5 and take even more maintenance costs we want to avoid. We **want timeless reliable tests working for us**.

Simple container test case boots Kernel creates a container, and sets it up - just once, so we can reuse it in all tests with speed:

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Smoke;

use AppKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class AbstractContainerTestCase extends TestCase
{
    protected static ?Container $container = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$container instance of Container) {
            $appKernel = new AppKernel('dev', true);
            $appKernel->boot();

            /** @var Container $container */
            $container = $appKernel->getContainer();
            self::$container = $container;
        }
    }
}
```

Now we can access the container easily via `self::$container`.

## Public Services

This is where the fun begins. To test services easily, it must be `public`:

```php
$productRepository = self::$container->get('product_repository');
// or better
$productRepository = self::$container->get(ProductRepository::class);
```

There was no need for this paragraph til Symfony 3.3. Unfortunately, [Symfony 3.4](https://dev.to/mainick/how-to-test-a-private-service-in-symfony-2m91) made all services `private` by default to improve migration to `__construct()` in services. As an unexpected side effect, all test calls stopped working too:

```php
self::$container->get('product_repository');
```

Not sure why it's not default Symfony behavior since private services were introduced, but we can only work around it now. In Symfony 4.1 they made [them `public` again](https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing), but only in `WebTestCase` or `KernelTestCase` child classes.

<br>

This mess made a lot of developers:

* to avoid simple service testing and settle down for *mock everything and test nothing* approach,
* to use high-level and slow tests
* to write no tests at all

<br>

This makes me deeply sad, as testing services in Symfony was otherwise very easy, fast, and fun.

<br>

As a reaction, various Symfony version have their weird temporary ways of making services testable:

* [custom compiler pass](https://stackoverflow.com/questions/46677535/running-symfony-dic-smoke-tests-in-sf3-4-and-above), but it's not a reliable way
 * [public aliases](https://github.com/symfony/symfony-docs/issues/8097)

But again, these change across the major versions. We'd have to worry about breaks during upgrades, instead of working on the upgrade itself.

## How to make internal services `public` again?

It's easy to make our own services public, but in smoke tests, we work exclusively with Symfony or Doctrine internal ones.

After couple dozen Symfony upgrades, **the most cost-effective path** points to [vendor patches](/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates). Don't worry, we'll not rewrite `/vendor` configs with huge files. We usually create 5-7 patches per project, each changing a single line. If the line moves, we simply regenerate the patch file.

## What is ideal smoke-test candidate?

Okay, now we know how to make services we need `public` and allow testing them. What should we test then?

<br>

First, we define **generic description** of a service to smoke test:

* it's an internal Symfony/Doctrine service
* it collects many services of a specific type - usually an interface, e. g. `Symfony\Component\EventDispatcher\EventSubscriberInterface`
* this specific type is implemented by our services (e.g. event subscriber) - we make sure our services are picked up

<br>

Here is a list of services we test, so you have a better idea:

* the Symfony `EventDispatcher` picks up all [event subscribers](/blog/2019/05/16/don-t-ever-use-listeners)
* the container picks up all our `*Controller` services
* all our `*Controller` services are instantiable and autowirable
* the container picks up all our configs and registers all services - important for [YAML configs to PHP upgrade](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/)
* the router loads all Controller annotations/attributes - the route prefixes are correct, and the controller paths and correct - great use for JSON snapshot testing
* the Doctrine ORM finds and loads all our entities
* all Doctrine entities have valid mappings
* the Doctrine `EventManager` picks up all listeners
* the Doctrine fixture loader picks up all our Doctrine Fixtures and Alice Fixtures

<br>

It depends on the way *you use* Symfony and Doctrine, but the first 4 items test everywhere.

<br>

Behind each of these points is a smoke test case. They're so similar across various Symfony projects, you could almost copy-paste (we do apart the namespace name).

This article explains the idea behind container smoke testing and would not fit 10 PHP files. By now you already know what your project needs to smoke test.

To give you a head start, we show you 2 typical smoke tests - **Event Subscribers** and **Controller instantiation**.

## A. Event Subscribers Smoke Test

The test has 3 steps:

* first, we fetch the main service - event dispatcher here
* then we add dummy **count-picked-services** test - here **we count all our custom event subscribers** - we skip internal Symfony/Sensio ones, as they can change across versions and we want to test only our code
* last but not least, if we want to be sure and improve code feedback later, we add narrow counter tests - e.g. how many listeners are on `KernelEvents::RESPONSE` or `KernelEvents::REQUEST`

```php
namespace Test\Unit\Smoke;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class EventSubscribersTest extends AbstractContainerTestCase
{
    private EventDispatcherInterface $eventDispatcher;

    public function setUp(): void
    {
        $this->eventDispatcher = self::$container->get('event_dispatcher');
    }

    public function testCount(): void
    {
        $eventSubscriberClasses = [];

        foreach ($this->eventDispatcher->getListeners() as $listeners) {
            foreach ($listeners as $listener) {
                /** @var string $listenerClass */
                $listenerClass = $listener[0]::class;

                // skip native ones
                if (
                    str_starts_with($listenerClass, 'Symfony')
                    || str_starts_with($listenerClass, 'Sensio')
                ) {
                    continue;
                }

                $eventSubscriberClasses[] = $listenerClass;
            }
        }

        $this->assertCount(42, $eventSubscriberClasses);
    }

    public function testNarrowCounts(): void
    {
        $this->assertCount(
            4, $this->eventDispatcher->getListeners(KernelEvents::RESPONSE)
        );

        $this->assertCount(
            20, $this->eventDispatcher->getListeners(KernelEvents::REQUEST)
        );
    }
}
```

That's it!

<br>

## B. Controllers loading Smoke Test

This test is the most generic one, we can put in every Symfony project.

It simply loads all controllers and checks if they are still instantiable. We also check their absolute count, so we know we didn't miss any of them.

How many times did it happen, that we moved the controller outside PSR-4 load() paths or we used the wrong parent class - this test saved us instantly.

```php
namespace Test\Unit\Smoke;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EventSubscribersTest extends AbstractContainerTestCase
{
    public function testControllerBuild(): void
    {
        $controllerCount = 0;

        foreach (self::$container->getServiceIds() as $serviceId) {
            if (! str_ends_with($serviceId, 'Controller')) {
                continue;
            }

            // check only our controllers
            if (! str_starts_with($serviceId, 'App')) {
                continue;
            }

            // make sure all controllers are still instantiated
            $controller = self::$container->get($serviceId);
            $this->assertInstanceOf(AbstractController::class, $controller);

            ++$controllerCount;
        }

        $this->assertSame(420, $controllerCount);
    }
}
```

That's it! Just 2 simple tests and we know that:

* event subscribers are loaded - in the correct amount, hooked to correct events
* autoconfigure works as expected
* controllers are loaded - in the correct amount
* all controllers are instantiable - the constructor is correctly autowired and parameters are passed

<br>

What else do you smoke test in our Symfony projects? Let me know on socials.

<br>

Happy coding!
