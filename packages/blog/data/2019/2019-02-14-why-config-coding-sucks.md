---
id: 186
title: "Why Config Coding Sucks"
perex: |
    Rector and static analysis help us to work with code better, but it also helps us spot new weak-points of our PHP code.


    One of the biggest evils is *config coding*. **How it can hurt you and how get rid of it**?
tweet: 'New Post on my  🐘 #php blog: Why Config Coding Sucks  #symfony #nettefw #laravel'
tweet_image: '/assets/images/posts/2019/config-evil/rename.gif'
---

Many frameworks propagate config coding over PHP code. It's cool, it's easy to type, short and we have a feeling we learned something new.

One of the good examples is Laravel with its [`config/app.php`](https://laravel.com/docs/5.7/configuration) - really good work!

Since [Symfony 3.3+ service autodiscovery](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/) feature, there is almost no reason to use code in config.




By coding in the config I mean anything more complex than:

- named services

    ```yaml
    services:
        someService: SomeService
        AnotherService:
            arguments:
                - '@someService'
    ```

- `_defaults`

    ```yaml
    services:
        _defaults:
            autowire: true
            autoconfigure: true
    ```

- and PSR-4 autodiscovery

    ```yaml
    services:
        App\:
            resource: '../src'
    ```

## The Dark Side

Less discussed side of config coding is that in exchange for sweet syntax sugar we lose:

- static analysis,
- PHPStorm refactoring
- and instant upgrades and refactoring by Rector.

PHP code written in config format has the same value to these tools as a screen-shot of code... with scrollbar:

<img src="/assets/images/posts/2019/config-evil/useless.png" alt="" class="img-thumbnail">


Here are **3 problems you invite to your code** with config coding and how to get rid of them with PHP.

## 1. Crappy Code Refactoring Automation

```yaml
services:
    - FirstService(@secondService::someMethod())
```

### What if...

- ...we **change** method name `someMethod`?
- ...we **change** class name `FirstService`?
- ...we **change** service name `secondService`?

With PHPStorm these changes are pretty easy:

<img src="/assets/images/posts/2019/config-evil/rename.gif" alt="" class="img-thumbnail">

But the config has to be changed manually - everywhere where Symfony/Nette/... plugins cannot reach.

*How can we do this better?*

### In PHP ✅

```php
<?php

final class FirstServiceFactory
{
    /**
     * @var SecondService
     */
    private $secondService;

    public function __construct(SecondService $secondService)
    {
        $this->secondService = $secondService;
    }

    public function createFirstService(): FirstService
    {
        return new FirstService($this->secondService);
    }
}
```

```diff
 services:
-   - FirstService(@secondService::someMethod())
+   - FirstServiceFactory
```

## 2. Learn the Neon/YAML Syntax by Heart

When you start to use more and more "cool" syntax of your favorite markup language, you'll have to remember the spacing, chars and key names:

```yaml
services:
    FirstService:
        setup:
        # or was is?
        calls:
            - 'setLogger', '@logger']
            # or was it?
            - ['setLogger', ['@logger']]
            # or was it?
            setLogger: '@logger'
```

You can also say goodbye to *rename method* refactoring.

We don't want to pollute our brains with these syntax details, **we want to code** with light and clear mind.

*How can we do this better?*

### In PHP ✅

```php
<?php

namespace App;

final class FirstServiceFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create(): FirstService
    {
        $someService = new FirstService();
        $someService->setLogger($this->logger);

        return $someService;
    }
}
````

The service is autowired by return type declaration `public function create(): FirstService`.

## 3. Service Re-use With Bugs

```yaml
services:
    - EmailCodeCleaner(HTMLPurifier(HTMLPurifier_Config::create({
        Attr.EnableID: true
    })))
```

Later that year somebody wants to use `HTMLPurifier`:

```php
<?php

final class MicrositeHtmlCodeCleaner
{
    /**
     * @var HTMLPurifier
     */
    private $htmlPurifier;

    public function __construct(HTMLPurifier $htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    // ...
}
```

Let's run the code:

```bash
Service 'HTMLPurifier' was not found. Register it in the config.
```

Ups! It looks like it's the first use of this service. It requires `HTMLPurifier_Config` class, so we have to create it too:

```yaml
services:
    - HTMLPurifier(HTMLPurifier_Config::create())
```

Done!

A few months later, you have an email campaign with a link to a microsite. Both with the same content. But a weird bug is reported - the microsite and email have different HTML outputs with the same content.

You already know, that's because `create()` had different arguments.

*How can we remove this potential bug?*

### In PHP ✅

```php
<?php

namespace App;

use HTMLPurifier;

final class HTMLPurifierFactory
{
    public function create(): HTMLPurifier
    {
        return new HTMLPurifier(HTMLPurifier_Config::create());
    }
}
```

We just returned the benefits of PHP code:

- the **autowiring works**
- there is only one instance of `HTMLPurifier` in your container
- if you register more by accident, the container tells you

<br>

Instead of config coding, use factories and [autowired parameters](/blog/2018/11/05/do-you-autowire-services-in-symfony-you-can-autowire-parameters-too/). You can also remove factory from configs with [`AutoReturnFactoryCompilerPass`](https://github.com/symplify/package-builder#do-not-repeat-simple-factories).

```diff
 services:
     App\:
        resource: ../src
-
-    SomeClass:
-         factory: ['@SomeClassFactory', 'create']
```

That's it!

<br>

Happy coding!
