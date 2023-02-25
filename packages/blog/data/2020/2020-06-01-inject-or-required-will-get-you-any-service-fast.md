---
id: 254
title: "@inject or @required will Get You Any Services Fast"
perex: |
    It is official. Symfony 5.1 [adds property injection to public properties](https://symfony.com/blog/new-in-symfony-5-1-autowire-public-typed-properties).
    Now, `@inject` or `@required` annotation above property or setter method is the fastest way to get any dependency on any service in your Symfony or Nette project.


    Use it everywhere you can... or not?

tweet_image: "/assets/images/posts/2020/inject_required_fail.png"
---

This post is about one of the practices that give me work as a legacy cleaning lady. Read carefully - you can decide if your project will be the next client or you'll build reliable code that is fun to work with.

<br>

Do you **like long repeated code with the same meaning over and over again**?

<br>

Why write long and tedious constructors in 25 lines...

```php
<?php

use Twig\Environment;
use Latte\Engine;

final class TemplateFactory
{
    /**
     * Symfony way
     * @var Environment
     */
    private $environment;

    /**
     * Nette way
     * @var Engine
     */
    private $engine;

    public function __construct(Environment $environment, Engine $engine)
    {
        $this->environment = $environment;
        $this->engine = $engine;
    }
}
```

...when you can do property injection with only **17 lines** and same effect:

```php
<?php

use Twig\Environment;
use Latte\Engine;

final class TemplateFactory
{
    /**
     * @required
     */
    public Environment $environment;

    /**
     * @var Engine
     * @inject
     */
    public $engine;
}
```

Let me show you what you are inviting to your code by the second choice.

## 1. Public Property - Service Override?

At the start of the project, it's very easy to see what is right and what is wrong. But as time goes by, **people will start to use everything they can to add a feature or fix the bug as fast possible**. I do that all the time.

```php
<?php

use Twig\Environment;

final class EmailSender
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var Environment
     */
    private $emailOnlyEnvironment;

    public function __construct(TemplateFactory $templateFactory, Environment $emailOnlyEnvironment)
    {
        $this->templateFactory = $templateFactory;
        $this->emailOnlyEnvironment;
    }

    public function sendInvoiceEmail(string $emailTo)
    {
        // hm... we need that custom Twig service here because it has extra macros/filters that are used only in emails

        // how can we do that?

        // ah, this will do
        $this->templateFactory->engine = $this->emailOnlyEnvironment;

        $template = $this->templateFactory->create();
        // ...
    }
}
```

### Would You Accept this Code in Code-Review?

- What service is in the `$this->templateFactory->engine` property?
- How many weeks will it take to forget this ~~trick~~ hack?
- How many will be the `TemplateFactory` used for emails, and how many for web templates?

### Practise Makes Perfect

Do you think this is a good practice? If so, I dare you: add such **service override feature** to your code and code as nothing happened. After 30 days, get back here and let me know in the comments how did your colleagues liked it.


## 2. Circular Reference for Blind People?

Let's have a simple contest. Who will be first?

This example is oversimplified - 2 classes are easy to debug. Recent projects I work with have 1000-2000 classes... so for real-life use case imagine this is 1000x longer example.

```php
<?php

use Twig\Environment;

final class EmailSender
{
    /**
     * @inject
     * @var TemplateFactory
     */
    public $templateFactory;
}
```

We need to send an email to admin if template engine rendering fails.

```php
<?php

use Twig\Environment;

final class CustomEnvironment extends Environment
{
    /**
     * @inject
     * @var EmailSender
     */
    public $emailSender;

    public function renderTemplate(string $template)
    {
        try {
            // ...
        } catch (Throwable) { // new PHP 8.0 syntax ^^
            $this->emailSender->sendRenderFailedMessageToAdmin();
        }
    }
}
```

### Would You Accept this Code in Code-Review?

- How do you know what service is where when?
- What could happen in the case of race-condition?
- What stops you from using `StaticMethods::everywhere()`?
- How does the dependency tree look like now?

Some frameworks container will tell you there is circular dependency and fail with an exception. Some would let it silently slip.

Either way, your code is now opened to issue, when at **2 different times, there are 2 different values in one property**. Similar issue to previous one, just more *fun* to debug.

## Why we Have `@inject/@required` anyway and When to Use it?

Nette is trying to limit this by suggestion, that `@inject` should be [**used only in presenters**](https://doc.nette.org/en/3.0/di-usage#toc-which-way-should-i-choose). No surprise, that project I work with now have it in almost every dependency of every presenter.

Also, it takes about 20 lines of PHP code to enable this in every service. It might still be in one of the top 5 e-commerce projects in The Czech Republic.

[Symfony has similar feature](https://symfony.com/doc/current/service_container/injection_types.html#immutable-setter-injection), but without any scope limitation, as far as I know

<br>

What can you do about it?

Well, in complicated times of circular dependencies, public property override and service juggling, it helps to get back to the basics: **what is the best use case for `@required`/`@inject`?**

- Getting a dependency? **No**
- [Eliminating visual dept](https://ocramius.github.io/blog/eliminating-visual-debt)? **No**
- Using the *my-favorite* framework the fullest? **No**

Why add such a feature, if there is no reason to use it?

<br>

The main reason for this feature is to prevent **constructor injection hell**.

David Grudl [wrote about it 8 years ago](https://phpfashion.com/di-a-predavani-zavislosti) (in Czech):

```php
<?php

class Barbar extends Foobar
{
   private $logger;

   function __construct(HttpRequest $httpRequest, Router $router, Session $session, Logger $logger)
   {
      parent::__construct($httpRequest, $router, $session);
      $this->logger = $logger;
   }
}
```


You can also find other [sources in English](https://www.rhyous.com/2016/09/27/constructor-injection-hell).

But programmers don't know about constructor injection hell. Why? Simply because there [was no exception in the code, when they used `@inject`](https://blog.codinghorror.com/the-just-in-time-theory). We just use features that were given to us by the framework.

<blockquote class="blockquote text-center">
    "Everything which is not forbidden,
     <br>
     is allowed"
</blockquote>

<br>

## Rule of Thumb: Abstract Parent Only

Saying that the place `@inject`/`@required` is designed for is dependency in `abstract` class. But not every `abstract` class! Just those that have children with more dependencies, that would require to put `parent::__construct()` repeated in every child.

```php
<?php

abstract class AbstractRepository
{
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @required
     */
    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
```

```php
<?php

final class ProductRepository extends AbstractRepository
{
    protected ProductEntityFactory $productEntityFactory;

    public function __construct(ProductEntityFactory $productEntityFactory)
    {
        $this->productEntityFactory = $productEntityFactory;
        // no parent::__construct() in every repository - yay!
    }
}
```

✅

*Note: prefer "inject" method over public property to lower risk of 2 bugs mentioned in the start of the post.*


That's it! And soon, hopefully, I'll be out of work.

<br>

Now, all we need is **to create a PHPStan rule**, that allows `@inject`/`@required` (and setter method alternatives) in `abstract` classes. Then you can forget this post and be safe for eternity.

<br>

Happy coding!
