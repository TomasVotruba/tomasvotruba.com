---
id: 219
title: "How to Convert Listeners to Subscribers and Reduce your Configs"
perex: |
    I wrote [Don't Ever use Symfony Listeners](/blog/2019/05/16/don-t-ever-use-listeners/) 2 months ago (if you missed it, be sure to read it to better understand this 2nd part). It got many constructive comments, mostly focused on particular standalone sentences without context.
    <br>
    <br>
    To my surprise, **none of the comments shown that listener beats subscriber**.<br>
    But what can you do, if you'd like to try subscribers, but currently have over 100 listeners in your application?

tweet: "New Post on the #php 🐘 blog: How to Convert Listeners to Subscribers and Reduce your Configs          #symfony @rectorphp"

updated_since: "August 2020"
updated_message: |
    Updated Rector YAML to PHP configuration, as current standard.
---

## 2 Ways to do One Thing? = WTF! WHY TF?

Just a reminder, how hurtful is to teach people **2 very similar ways to do one thing**.

Google shows that people are confused since 2012, wow!

<img src="/assets/images/posts/2019/listen-to-sub/github.png" class="img-thumbnail">
<img src="/assets/images/posts/2019/listen-to-sub/quote.png" class="img-thumbnail">
<img src="/assets/images/posts/2019/listen-to-sub/so.png" class="img-thumbnail">

And this is not related only to big patterns as subscriber or listener. We do such decisions every day - while we code a new feature when we add a new package to `composer.json` when we integrate 4th API to verify payments.

Next time you'll be standing before 2 options, remember [the least common denominator](/blog/2019/07/01/5-workflow-tips-every-php-developer-should-know/#5-use-elementary-maths-to-become-master) and **make your code more durable** in time.

## Why Should we Re-Think Listeners in our Code?

If the readable and clear code is not good enough reason for you and you still think
you should stick with listeners at all cost, maybe the following steps will convince you.

Symfony 3.3 introduced [PSR-4 Autodiscovery](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/) of services. In short, it means we don't have register services manually, if they respect PSR-4 (class name ~= file location):

```diff
 services:
-    App\Controller\CoffeeController: ~
-    App\Controller\WifiController: ~
-    App\Controller\PlaneController: ~
-    # thousands more...

+    App\Controller\:
+        resource: '../src/Controller'
```

## How to Migrate Listeners to Subscribers?

It doesn't apply only to controllers, but to all services, like Event Subscribers:

```diff
 services:
     _defaults:
         # this helps load event subscribers to EventDistpatcher
         autoconfigure: true

-    App\EventSubscriber\CoffeeEventSubscriber: ~
-    App\EventSubscriber\WifiEventSubscriber: ~
-    App\EventSubscriber\PlaneEventSubscriber: ~
-    # thousands more...

+    App\EventSubscriber\:
+        resource: '../src/EventSubscriber'
```

Next time we create an event subscriber class, we don't have to [code in config](/blog/2019/02/14/why-config-coding-sucks/) anymore ✅

We've [reduced cognitive load](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/) → code is easier to work with → hiring is faster → we can focus on business and feature value.

### How can we Reduce configs with Listeners?

```yaml
services:
    App\EventListener\WifiEventListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }
            - { name: kernel.event_listener, event: kernel.view }
```

Well, what about...

```yaml
services:
    App\EventListener\:
        resource: '../src/EventListener'
```

Hm, how can be the listener called when it has now an information event?

That's one of legacy [code smells of tags](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/).

**We can't reduce configs**. We have to grow about configs together with code till the end of times ❌

<br>

If **you're paid or motivated by productivity** like me and not by produced lines of code or wasted time with no output, you care about this.

## Automated Instant Migration

It's very nice use case for [pattern refactoring](/blog/2019/04/15/pattern-refactoring/), from A - *Listener* to B - *Event Subscriber*.

### 1. Define Patterns

A. **Listener**

- it's a naked PHP class with a public method
- event information (event, method, priority) is in a config

B. **Event Subscriber**

- it's a PHP class that implements `Symfony\Component\EventDispatcher\EventSubscriberInterface`
- event information is in a static method `getSubscribedEvents` inside the class
- it is auto-discovered by PSR-4

### 2. Pattern Change In Code?

```php
<?php

class SomeListener
{
     public function methodToBeCalled()
     {
     }
}
```

```yaml
# in config.yaml
services:
    SomeListener:
        tags:
            - { name: kernel.event_listener, event: 'some_event', method: 'methodToBeCalled' }
```

↓

```php
<?php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SomeEventSubscriber implements EventSubscriberInterface
{
     /**
      * @return mixed[]
      */
     public static function getSubscribedEvents(): array
     {
         return ['some_event' => 'methodToBeCalled'];
     }

     public function methodToBeCalled()
     {
     }
}
```

Without any config.


### 3. Instant Upgrade with Rector

The latest [Rector v0.5.8 is shipped](https://twitter.com/rectorphp/status/1152862370630459393) with rule exactly for this kind of migration.

Just register the rule in your `rector.php` config to start migration:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\SymfonyCodeQuality\Rector\Class_\EventListenerToEventSubscriberRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(EventListenerToEventSubscriberRector::class);

    // optional, when something fails
    $parameters = $containerConfigurator->parameters();
    // use explicit Kernel, if not discovered by Rector
    $parameters->set('kernel_class', 'App\Kernel');
    // use explicit environment, if not found by Rector
    $parameters->set('kernel_environment', 'test');
};

Run it:

```bash
vendor/bin/rector process app src
```

And now all the listeners were migrated to event subscribers ✅

### 4. Update Configs

In the end, we have to remove all listeners + metadata from configs and add single autodiscovery for our EventSubscribers:

```diff
services:
-    App\EventListener\WifiEventListener:
-        tags:
-            - { name: kernel.event_listener, event: kernel.exception }
-            - { name: kernel.event_listener, event: kernel.view }

+    _defaults:
+        autoconfigure: true
+
+    App\EventSubscriber\:
+        resource: '../src/EventSubscriber'
```

That's it!

<br>

Happy coding!
