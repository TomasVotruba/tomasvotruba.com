---
layout: post
title: Forget "autowire" and just use it
perex: '''
    Autowiring is a great feature that was added in Symfony 2.8. It moves Dependency Injection pattern to the next level.
    If you want to use it to its full potential, you still have to add 1 extra line to every service configuration.
    Today I will show you, how to get rid of that line.
'''
lang: "en"
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

- No => add `autowiring_types` for specific name to required service, [which is pretty difficult at the moment](https://github.com/symfony/symfony/issues/17783)
- Yes => autowire

*4) Has the constructor changed during development?*

- Start from point 1.

+ And some more for edge cases.


## Seems like function... Could this be automated?

You are right! **It can be automated.**

This is exactly what [Symplify/DefaultAutowire](https://github.com/Symplify/DefaultAutowire) bundle does.

Apart handling feature above for you, it will turn this...

```yaml
# app/config/config.yml
services:
    price_calculator:
        class: PriceCalculator
        autowire: true

    product_repository:
        class: ProductRepository
        autowire: true

    user_factory:
        class: UserFactory
        autowire: true
```

...into this:

```yaml
# app/config/config.yml
services:
    price_calculator:
        class: PriceCalculator

    product_repository:
        class: ProductRepository

    user_factory:
        class: UserFactory
```

## Get It Done in 2 steps

### 1. Install package

```yaml
composer require symplify/default-autowire
```

### 2. Register bundle

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
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
