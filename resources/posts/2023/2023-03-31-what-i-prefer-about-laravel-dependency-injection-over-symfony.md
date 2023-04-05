---
id: 383
title: "What I prefer about Laravel Dependency&nbsp;Injection over Symfony"

perex: |
    Regarding Laravel, one of the biggest "no"s in 2022 was how it handles passing services around the project. I'm not talking about the facades or active records but **the static reflection container**.

    At first, it raised my blood pressure - we have a dependency injection, and everything must be passed via the constructor, right?

    Then I remembered a wise saying: "There are no best solutions, only trade-offs."
---

Let's start with what most of my readers and I know well - Symfony.

<br>

## How do we work with services in Symfony?

To get a service in a Symfony project, we have to:

1. register it explicitly or via PSR-4 autodiscovery

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

From over 50+ Symfony project upgrades, what is **the most costly and repeated step**?

The configs:

* you autowire by type; every service has to be explicitly registered
* psr-4 autodiscovery is done by directory → it requires specific directory structure
* YAML/PHP/XML mix leads to [various opinionated camps](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify)
* the whole upgrade is about merging 50 configs into one

I call this approach an *opt-out*:

* We have to define every service in the config first,
* only then can we use it in the project.

This [memory-lock](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock) forces us to think about another more place when we want to use the service. This lead to distraction and unfocused programming.

## How do we get service in Laravel?

Laravel is one less step easier. Do you need a service? Ask for it:

```php
final class SomeClass
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }
}
```

## Opt-in

With this approach, it seems we have 50 % less work. It's much more, as we only focus on one point at a time and are more with deep work.

* Do you need service in code? Ask for it in the constructor
* Do you need service in tests? Ask for it via `make()` or `$this->get()`

I have very limited Laravel DI knowledge, so bear with me: when we ask for a service, and it's not created yet, the container will create one, inject the dependencies and provide it. I thought, "This uses reflection, and reflection is bad". But that's as dogmatic as "all drugs are bad, and you should never visit a professional psychiatrist".

## Simplicity teaches best practices

We only modify the config file (service provider) when we need to do something extra, unique, or weird. I've noticed this **motivates me to use as many clean services as possible**, without any scalar parameters, magic injections, or multiple instances of one type.

This naturally leads to cleaner architecture, simpler code, and a smaller set of coding patterns. This leads to code that another developer finds easier to read and understand and requires less cognitive load to work with effectively.

<br>

**I'm delighted this is a positive externality that works on a subconscious level and makes my code cleaner.**

What is your experience with Laravel or Symfony dependency injection?

<br>

Happy coding!
