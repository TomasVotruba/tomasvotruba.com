---
id: 392
title: "How take Advantage of 3rd party Dependency Injection Container
perex: |
    When Nuno send me a Pest plugin for type coverage that simply runs [TomasVotruba/type-coverage](https://github.com/TomasVotruba/type-coverage), I looked for the PHPStan container use.

    Why? Because the type-coverage package are PHPStan rules that easily plugin into PHPStan. But what if you want to use them in a tool that has a different container?

    I've found the solution the hard way - so I though it might be useful to share it with you to save you the troubles.
---

In the example above, there was no PHPStan container. The PHPStan services are made of scratch, which is painful process and can lead to [bugs in next PHPStan patch version](https://github.com/pestphp/pest-plugin-type-coverage/pull/12).

But don't narrow your focus only on this specific situation. It can be anything from:

* using PHPStan services in Rector container
* using Laravel container in a Symfony project
* using Blade compiler in PHPStan rule for [template static analysis](https://tomasvotruba.com/blog/introducing-bladestan-phpstan-analysis-of-blade-templates/)
* using php-cs-fixer and PHP CodeSniffer container (if they compatible one) in [EasyCodingStandard](https://github.com/symplify/easy-coding-standard/)
* using PHPStan container in tool for [Smoke Twig template testing](/blog/twig-smoke-rendering-why-do-we-even-need-it/)

...and so on.

<br>

It's not universal. You can't connect every PHP container into another container. Some project even don't have container and are made manually with `new` instances.

<br>

## Minimal Requirements

Despite messy complexity and wide variety of PHP containers, there are **compatibility requirements** to be able to connect 2 containers:

* they both use PSR method) called `get()` or similar
* your main container support factories
* the external container can be created easily or with help of `ContainerFactory` object

Have you met these conditions? We're good to go.

<br>

From my experience, Laravel, Symfony and Nette containers fit perfectly and are mutually reusable.

<br>

## Practical use Case

Let's look at practical example - we need a PHPStan `PHPStan\Analyser\NodeScopeResolver` service inside a Rector container.

<br>

How does injecting from another container works?

* our service will require a PHPStan service in constructor
* our container will check, if it's already created
* if not, it create PHPStan container using *container factory*
* then it asks PHPStan container for the service
* then our container will return given service

<br>

Rector uses Laravel container since 0.18 (but the same pseudo-code works for Symfony too):

```php
new Illuminate\Container\Container;

use PHPStan\DependencyInjection\Container as PHPStanContainer;
use PHPStan\Analyser\NodeScopeResolver::class;

$container = new Container();

// we register a service from PHPStan that we want in our project
$container->singleton(NodeScopeResolver::class, function (Container $container) {
    // we ask for PHPStan container
    $phpstanContainer = $container->make(PHPStanContainer::class);

    return $phpstanContainer->getByType(NodeScopeResolver::class);
});
```

<br>

The asked for `PHPStanContainer`, but how do we make it? Fortunately, PHPStan has its own [`ContainerFactory`](https://github.com/phpstan/phpstan-src/blob/1.11.x/src/DependencyInjection/ContainerFactory.php):

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

Now when we ask for `PHPStanContainer` it will be created just once and stored in our container.

Then PHPStan container will provide any service we need!

<br>

For more inspiration, [check this pattern in Rector](https://github.com/rectorphp/rector-src/blob/main/packages/NodeTypeResolver/DependencyInjection/PHPStanServicesFactory.php).

<br>

## Make your Tools with Usability in Mind

This is not standard though - not every tool ships with container factory. That means we have to dig in and construct the whole service and its tree manually with `new` instances.

PHPStan, ECS, Rector and [all](https://github.com/TomasVotruba/type-coverage) [the](https://github.com/TomasVotruba/lines) [tools](https://github.com/TomasVotruba/class-leak) [I make](https://github.com/TomasVotruba/bladestan) have a container factory as first class citizens.

You can require them in your project and build on top of their features easily.

Next time you'll be making a tool, consider **dropping in a container factory class** too. Even it if will be bare `new` instances with single `get()` method, it will give your fellow PHP developers option to use them easily.

<br>

Happy coding!
