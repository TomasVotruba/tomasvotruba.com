---
id: 427
title: "Cost-effective Smoke Tests every Symfony Project should have"
perex: |
    Today, I'll share a trademark secret that allows us to move fast and make huge changes without fear.

    When we're starting a new [Symfony 2.8-7.2 upgrade project](/blog/off-the-beaten-path-to-upgrade-symfony-28-to-72), we cover it with a couple of tests first. These tests are not units, but smoke tests - with a couple of lines they cover a huge portion of the Symfony framework layer we use.

    With a couple of lines, we can cover complex container operations and avoid most dummy yet destructive bugs while working with services.
---

## Smoke tests without Complexity

First, we should define what we mean by "smoke tests." We talk about tests without databases, external connections, or mocks. We talk about purely exclusively DI container tests.

Tests extending `KernelTestCase` or `WebTestCase` often create a couple of services dependent on the database and require a more complex setup. We won't use them either. Want to run these tests:

* quickly,
* locally,
* using PHPUnit
* and put them on CI right now to cover our back instantly.

## Timeless Tests

The following tests must run on any of [Symfony 2.8-7.2](/blog/off-the-beaten-path-to-upgrade-symfony-28-to-72) versions. We don't want to depend on the framework version and maintain it once we upgrade Symfony. They should cover us even when we change the major Symfony version, so we know the upgrade is safe.

## Simple Container

Saying that we create a simple container test case. It boots Kernel, creates a container, and sets it up - just once, so we can reuse it in all tests with speed.

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

There was no need for this paragraph until Symfony 3.3. Unfortunately, [Symfony 3.4](https://dev.to/mainick/how-to-test-a-private-service-in-symfony-2m91) made all services `private` by default.

These calls stopped working, even in tests:

```php
self::$container->get('product_repository');
```

Not sure why it's not default Symfony behavior since private services were introduced, but we can only work around it now. In Symfony 4.1 they made [them `public` again](https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing), but only in `WebTestCase` or `KernelTestCase` child classes.

<br>

This mess made a lot of people:

* to avoid simple service testing and settle down for mock everything and test nothing,
* high-level and slow tests
 or no tests at all approaches.

<br>

Saying that every Symfony version has its weird way of making services testable again. But as we stated above, **we're writing version independent tests**. So we don't have to worry about breaks during major Symfony version upgrades.

## How to deal with it?

It's easy to make our own services public, but in smoke tests, we work exclusively with Symfony or Doctrine internal ones.

There are a couple of tricks like custom [`PublicForTestsCompilerPass`](https://stackoverflow.com/questions/46677535/running-symfony-dic-smoke-tests-in-sf3-4-and-above), but it's not a reliable way.

When we need service `public` we'll have to make it so. After couple dozen Symfony upgrades, the **cheapest maintenance costs** point to [vendor patches](/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates). We have roughly 5-10 per project, changing 1-2 lines. If the line moves, we simply regenerate the patch file.


## What to test?

Okay, now we know how to make services we need `public`. What should we test then?

We want to test the internal Symfony/Doctrine service that:

* collect many services of collected type - usually an interface, e. g. `\Symfony\Component\EventDispatcher\EventSubscriberInterface`
* has constructor array dependency of a single type
* can change in time
* collect type that is implemented by our services - we want to make sure our services are still picked up later


## Why?

This week 2 tests of this type saved us hard-to-find bugs on a page. These tests are never tested anywhere else, as they're a blend of Symfony glue and our internal logic. We kind of hope Symfony did work, but it doesn't have to always be the case.

@theroy



**Rule of thumb**:

@todo


```php
<?php

declare(strict_types=1);

namespace test\Smoke;

use In2plane\OperationsBundle\Event\OrderStatusChangedEvent;
use In2plane\OperationsBundle\Event\ServiceOrderAllocationChangedEvent;
use Nette\Utils\Strings;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\KernelEvents;
use test\AbstractContainerTestCase;

final class EventSubscribersTest extends AbstractContainerTestCase
{
    public function testInstance(): void
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::$container->get('event_dispatcher');
        $this->assertInstanceOf(EventDispatcherInterface::class, $eventDispatcher);
    }

    public function testKernelEventsCount(): void
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::$container->get('event_dispatcher');

        $this->assertCount(14, $eventDispatcher->getListeners(KernelEvents::RESPONSE));
        $this->assertCount(9, $eventDispatcher->getListeners(KernelEvents::CONTROLLER));
        $this->assertCount(27, $eventDispatcher->getListeners(KernelEvents::REQUEST));

        $this->assertCount(2, $eventDispatcher->getListeners(OrderStatusChangedEvent::STATUS_COMPLETED));
        $this->assertCount(1, $eventDispatcher->getListeners(ServiceOrderAllocationChangedEvent::ALLOCATION_CHANGED));
    }

    public function testCustomEventSubscribers(): void
    {
        $customEventSubscriberClasses = [];

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::$container->get('event_dispatcher');

        foreach ($eventDispatcher->getListeners() as $listeners) {
            foreach ($listeners as $listener) {
                /** @var string $listenerClass */
                $listenerClass = $listener[0]::class;

                // skip native ones
                if (Strings::match($listenerClass, '#^(Symfony|Sentry|Nelmio|Sensio|FOS)\\\\#')) {
                    continue;
                }

                $customEventSubscriberClasses[] = $listenerClass;
            }
        }

        $this->assertCount(42, $customEventSubscriberClasses);
    }

    public function testFormEventSubscribers(): void
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::$container->get('event_dispatcher');

        $this->assertCount(5, $eventDispatcher->getListeners(FormEvents::PRE_SUBMIT));
        $this->assertCount(2, $eventDispatcher->getListeners(FormEvents::POST_SUBMIT));
        $this->assertCount(4, $eventDispatcher->getListeners(FormEvents::PRE_SET_DATA));
        $this->assertCount(1, $eventDispatcher->getListeners(FormEvents::POST_SET_DATA));
    }
}
```

4) bugs and tags



5) what next?
