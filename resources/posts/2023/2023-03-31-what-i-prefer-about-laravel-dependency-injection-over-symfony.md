---
id: 383
title: "What I prefer about Laravel Dependency&nbsp;Injection over Symfony"

perex: |
    When it comes to Laravel, one of the biggest "no"s in 2022 was the way it handles passing services around the project. I'm not talking about the facades or active records, but **the static reflection container**.

    At first, it raised my blood pressure - we have a dependency injection and everything must be passed via constuctor, right?

    Then I remembered wise saying: "There are no best solutions, only trade-offs."
---

Let's start with what I and most of my readers know well - Symfony.

<br>

## How do we work with services in Symfony?

To get a service in a Symfony project, we have to:

1. register it explicitly or with via PSR-4 autodiscovery

```php
return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()->autowire();

    $services->load('App\\', __DIR__ . '/../src/App');
}
```

2. then require it in a constructor

```php
final class SomeClass
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }
}
```

<br>

## Opt-Out

I call this approach is an *opt-out*: We have to define every service in the config first, only then we can use it in the project.

From over 50+ Symfony project upgrades, what is **the most costly and repeated step**? The configs:

* autowire by type, explicit registration of every service
* psr-4 autodiscovery done by directory = requires specific structure
* YAML/PHP/XML mix leads to [various opinionated camps](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify)
* the whole upgrade is about merging 50 configs to one


## How do we get service in Laravel?

@todo


```php
/** @var PhpContentsValidator $phpContentsValidator */
$phpContentsValidator = make(PhpContentsValidator::class);
```


## Opt-in vs Opt-out



Laravel configs

* need a services in code? get it
* do you need a service in tests? get it - the same way!
* do you need special configuration - **opt-int**
* you onl modify config files (service provider) when you need to do something extra, special or weird = I've noticed this motivates me to use as much clean services as possible, without any scalar parameters, magic injections = **cleaner code**; I'm very happy this is a positive externality that works on subconsious level


## Do's

* prefer constuctor over magic methods
* it's clear what is a services and what is a value object


## Great power comes with great responbility

@don'ts

* calling services in Blade

* calling services models
  * once you allow leak single services, then everyone coming to your project knows that this is allowed, so you'll end up calling controller that calls a form process in your model


@do's


## Weird places

* livewire and queue handler - the DI here is not done via constructor, but rather via one of autowirable  methods
