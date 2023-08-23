---
id: 392
title: "How to take Advantage of 3rd party Dependency Injection Container"
perex: |
    When Nuno sent me a Pest plugin for type coverage that runs [TomasVotruba/type-coverage](https://github.com/TomasVotruba/type-coverage), I looked for the PHPStan container use.

    Why? Because the type-coverage package is PHPStan rules that easily plugin into PHPStan. But what if you want to use them in a tool that has a different container?

    I've found the solution the hard way - so it might be useful to share it with you to save you the trouble.
---

In the example above, there was no PHPStan container. The PHPStan services are made from scratch, which is a painful process and can lead to [bugs in the next PHPStan patch version](https://github.com/pestphp/pest-plugin-type-coverage/pull/12).

<br>

But don't narrow your focus only on this specific situation. It can be anything from:

* using PHPStan services in the Rector container
* using Laravel container in a Symfony project
* using Blade compiler in PHPStan rule for [template static analysis](https://tomasvotruba.com/blog/introducing-bladestan-phpstan-analysis-of-blade-templates/)
* using php-cs-fixer and PHP CodeSniffer container (if they are compatible) in [EasyCodingStandard](https://github.com/symplify/easy-coding-standard/)
* using PHPStan container in a tool for [Smoke Twig template testing](/blog/twig-smoke-rendering-why-do-we-even-need-it/)

...and so on.

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/4fb5af38-99dd-4319-904e-59ceb3454b58" class="img-thumbnail" style="max-width: 35em">

<br>

It's not universal. You can't connect every PHP container into another container.

<br>

Some projects even don't have containers and are made manually with `new` instances.

<br>

## Minimal Requirements

Despite the messy complexity and wide variety of PHP containers, there are **compatibility requirements** to be able to connect 2 containers:

* they both use the PSR method called `get()` or a similar
* our main container support factories
* the external container can be created easily or with the help of the `ContainerFactory` object

Have you met these conditions? We're good to go.

<br>

From my experience, Laravel, Symfony, and Nette containers fit perfectly and are mutually reusable.

<br>

## Practical Use Case: We need PHPStan service in Rector

Let's look at a practical example - we need a PHPStan `PHPStan\Analyser\NodeScopeResolver` service inside a Rector container.

<br>

How does injecting from another container work?

* our service will require a PHPStan service in the constructor,
* our container will check if the service has already been created,
* if not, it creates a PHPStan container using *container factory*,
* then it asks the PHPStan container for the service,
* then our container will return, given the service

<br>

Rector uses Laravel container since 0.18, so we build the example using [illuminate/container](https://laravel.com/docs/10.x/container) package (same pseudo-code works for Symfony, too):

```php
new Illuminate\Container\Container;

use PHPStan\DependencyInjection\Container as PHPStanContainer;
use PHPStan\Analyser\NodeScopeResolver::class;

$container = new Container();

// we register a service from PHPStan that we want in our project
$container->singleton(NodeScopeResolver::class, function (Container $container) {
    // we ask for the PHPStan container
    $phpstanContainer = $container->make(PHPStanContainer::class);

    return $phpstanContainer->getByType(NodeScopeResolver::class);
});
```

<br>

We asked for `PHPStanContainer` service, but where does it come from?

Fortunately, PHPStan has a [`ContainerFactory`](https://github.com/phpstan/phpstan-src/blob/1.11.x/src/DependencyInjection/ContainerFactory.php), so we register it and create PHPStan container:

```php
use PHPStan\DependencyInjection\ContainerFactory as PHPStanContainerFactory;
use PHPStan\DependencyInjection\Container as PHPStanContainer;

// ...

$container->singleton(PHPStanContainer::class, function (Con) {
    $phpStanContainerFactory = new PHPStanContainerFactory();

    return $phpStanContainerFactory->create(
        '/tmp/our_phpstan',
        additionalConfigFiles: [],
        analysedPaths: []
    );
});
```

Now, when we ask for `PHPStanContainer`, it will be created once and stored in our container.

**Then PHPStan container will provide any service we need!**

<br>

For more inspiration, [check this pattern in Rector](https://github.com/rectorphp/rector-src/blob/main/packages/NodeTypeResolver/DependencyInjection/PHPStanServicesFactory.php).

<br>

## Make your Tools with Usability in Mind

However, this is not standard - not every tool ships with a container factory class. That means we have to manually dig in and construct the whole service and its tree with `new` instances.

PHPStan, ECS, Rector and [all](https://github.com/TomasVotruba/type-coverage) [the](https://github.com/TomasVotruba/lines) [tools](https://github.com/TomasVotruba/class-leak) [I make](https://github.com/TomasVotruba/bladestan) have a container factory as first class citizens.

You can require them in your project and build on top of their features quickly.

<br>

Next time you make a tool, consider **dropping in a container factory class** too. Even if it is bare `new` instances with a single `get()` method, it will allow your fellow PHP developers to use them easily.

<br>

Happy coding!
