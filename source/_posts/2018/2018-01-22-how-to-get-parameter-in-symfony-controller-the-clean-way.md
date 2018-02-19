---
id: 73
title: "How to Get Parameter in Symfony Controller the Clean Way"
perex: '''
    Services are already moving to Constructor Injection in Symfony.
    <br>
    Now it's time for parameters to follow.
'''
tweet: "New post on my blog: How to Get Parameter in Symfony Controller the Clean Way #symfony #php #di"
tested: true
test_slug: ParameterToSymfonyController
---


### The Easy Way

```php
final class LectureController extends SymfonyController
{
    public function registerAction()
    {
        $bankAccount = $this->container->getParameter('bankAccount');
    }
}
```

It works, but it breaks [SOLID encapsulation of dependencies](https://github.com/jupeter/clean-code-php#solid). Controller should not be aware of whole DI container and every service in it. **It should take only what it needs** as any other [delegator](/blog/2018/01/08/clean-and-decoupled-controllers-commands-and-event-subscribers-once-and-for-all-with-delegator-pattern/#delegator-pattern-to-the-strike-rescue-strike-prevention).

**What if we need a service** to pay a registration fee to our bank account?

Since [Symfony 2.8 with autowiring](https://symfony.com/blog/new-in-symfony-2-8-service-auto-wiring) we can go for constructor injection with no obstacles:

```php
<?php declare(strict_types=1);

final class LectureController extends SymfonyController
{
    /**
     * @var PaymentService
     */
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function registerAction(): void
    {
        $bankAccount = $this->container->getParameter('bankAccount');

        $this->paymentService->payAmountToAccount(1000, $bankAccount);
    }
}
```

This can go completely wrong, not because dependency injection is better than service locator, but **because code is now inconsistent**. It's not clear:

- When should we use constructor injection? For services?
- When should we use service locator? For parameters?

At that's what we think about when *we* refactored code and know about it's previous state.

When your colleague will extends this code 3 months later, he might [broke your window](https://blog.codinghorror.com/the-broken-window-theory/):

```diff
<?php declare(strict_types=1);

final class LectureController extends SymfonyController
{
    /**
     * @var PaymentService
     */
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function registerAction(): void
    {
        $bankAccount = $this->container->getParameter('bankAccount');

        $this->paymentService->payAmountToAccount(1000, $bankAccount);
    }
+
+    public function refundAction(): void
+    {
+        $refundService = $this->container->get(RefundService::class);
+        $refundService->refundToLoggedUser(1000);
+    }
}
```

## Consistency over Per Change Pattern

You understand your code = you know reasons why it's written this way and the boundaries. You know when to use dependency injection and when service (or pamater) locator.

**But that's you. Only you.** Other people don't have your experience and your memory. **They read the code and learn while reading**.

That's why it's important to use as less rules as possible to prevent [cognitive overload](https://chrismm.com/blog/writing-good-code-reduce-the-cognitive-load/). Which leads to poor understanding of the code and coding further in the same file but in own personal way, not related to original code much.

### DI is the Flow &ndash; Go With It

Symfony 3.3 and 3.4/4.0 brought [many new DI features](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/) and **with it an evolution to developer experience paradigm**. Thanks to [Nicolas Grekas](https://github.com/nicolas-grekas), and subsequently [Kévin Dunglas](https://github.com/dunglas) and [Martin Hasoň](https://github.com/hason).

## The Clean Way

Service is created in the container and passed via constructor where needed.
**Why not parameter, which is also loaded by the container?**

```php
<?php declare(strict_types=1);

final class LectureController extends SymfonyController
{
    /**
     * @var string
     */
    private $bankAccount;

    /**
     * @var PaymentService
     */
    private $paymentService;

    public function __construct(string $bankAccount, PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->bankAccount = $bankAccount;
    }

    public function registerAction(): void
    {
        $this->paymentService->payAmountToAccount(1000, $this->bankAccount);
    }
}
```

### Change the Config

We need to:

- register controller **manually** in every instance
- pass the parameter to constructor **manually** in every instance
- autowire the rest

It's lot of work, but it's worth it!

```yaml
# config.yml
parameters:
    bankAccount: '1093849023/2013'

services:
    _defaults:
        autowire: true

    App\Controller\LectureController:
        arguments:
            - '%bankAccount%'
```

Would you use this approach? 5 lines for 1 parameter in 1 service? Maybe.

What about 2, 3 or 40 controllers/services using it?

```yaml
services:
    autowire: true

    App\Controller\LectureController:
        arguments:
            - '%bankAccount%'

    App\Controller\ContactController:
        arguments:
            - '%bankAccount%'

    # and 40 more services with manual setup
    App\Model\PaymentService:
        arguments:
            # with care when used with another position then 1st one
            2: '%bankAccount%'
```

**Doh, so much work :(**

I find [the easy way](#the-easy-way) now much more likeable:

```php
$this->container->getParameter('bankAccount');
```

**Wait! No need to go easy and dirty. There *is* simpler way.**

Since Symfony 3.3 we can use [PSR-4 service loading](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#4-use-psr-4-based-service-autodiscovery-and-registration/) and since [Symfony 3.4/4.0 parameter binding](https://symfony.com/blog/new-in-symfony-3-4-local-service-binding).

How changed previous steps?

- <strike>register controller manually</strike> → use PSR4 **once for all services**
- <strike>pass the parameter to constructor</strike> → use binding **once for all services**
- autowire the rest

```yaml
services:
    _defaults:
        autowire: true
        bind:
            $bankAccount: '%bankAccount%'

    App\Controller\:
        resource: ..
```

**Now you can add 50 more services using `$bankAccount` as constructor dependency with no extra edit on config**. Win-win!

<br><br>

Happy Config Fit & Slimming!
