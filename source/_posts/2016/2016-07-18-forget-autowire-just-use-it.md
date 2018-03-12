---
id: 10
title: Forget "autowire" and just use it
perex: |
    Autowiring is a great feature that was added in Symfony 2.8. It moves Dependency Injection pattern to the next level.
    If you want to use it to its full potential, you still have to add 1 extra line to every service configuration.
    Today I will show you, how to get rid of that line.

deprecated: true
deprecated_since: "May 2017"
deprecated_message: |
    You can set autowiring via <a href="https://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration#default-service-configuration">new <code>_defaults</code> per config file feature</a>
    since <strong>Symfony 3.3</strong>.
    <br><br>
    It does pretty much the same thing, with <strong>advantage of having it under control and explicitly defined</strong>.
    <br><br>
    I recommend using it instead!
---


## When to autowire?

If you use autowiring daily, you might came across this thinking process before you place `autowired: true` to your config:

*1) Has this service constructor dependency?*

- No => skip
- Yes => go on

*2) Is it object?*

- No => skip
- Yes => go on

*3) Is it unique service type?*

- No => add [`alias` for specific name to required service](https://github.com/symfony/symfony/pull/21494)
- Yes => autowire

*4) Has the constructor changed during development?*

- Start from point 1.

Plus some more logic for edge cases like parameters and factories.


## Seems like function... Could this be automated?

You are right! **It can be automated.**

This is exactly what [Symplify/DefaultAutowire](https://github.com/Symplify/DefaultAutowire) bundle does.

Apart handling feature above for you, it will turn this...

```yaml
# app/config/config.yml
services:
    PriceCalculator:
        autowire: true

    ProductRepository:
        autowire: true

    UserFactory:
        autowire: true
```

...into this:

```yaml
# app/config/config.yml
services:
    PriceCalculator: ~

    ProductRepository: ~

    UserFactory: ~
```

## Get It Done in 2 steps

### 1. Install package

```yaml
composer require symplify/default-autowire
```

### 2. Register bundle

```php
// app/AppKernel.php

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            new Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle(),
            // ...
        ];
    }
}
```

And that's it!

For further use, **just check Readme for [Symplify/DefaultAutowire](https://github.com/Symplify/DefaultAutowire).**
