---
id: 5
title: Modular Routing in Symfony
perex: "Modular routing in Symfony is bounded to <code>routing.yml</code>. Adding few lines for each new module can create large mess. Can we make it bit simpler? Sure we do and I will show you how."

deprecated_since: "June 2017"
deprecated_message: |
    I have deprecated this package, because of <a href="https://github.com/Symplify/Symplify/issues/181">feedback that it is not useful</a> and low download rates (under 2 000 in 2 years).
    <br><br>
    You can use <strong>annotation routing</strong> in combination with <strong><a href="/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#4-use-psr-4-based-service-autodiscovery-and-registration">PSR-4 controller autodiscovery</a></strong> since <strong>Symfony 3.3</strong> and with <a href="https://github.com/symfony/symfony/pull/23044">routing annotation loader enabled by default</a> since <strong>Symfony 3.4</strong>.
    <br><br>
    This package is still available <a href="https://github.com/DeprecatedPackages/SymfonyModularRouting">here for inspiration</a> though.

---

Let's say you have fairly standalone module or package and you want to add its routes as simple as:

```php
// app/AppKernel.php

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            new App\MeetupModule\AppMeetupModuleBundle(),
            // ...
        ];
    }
}
```

## So, what you can do?

- add your routes manually to `routing.yml` - **requires routing.yml modification**
- implement [custom Route Loader](https://symfony.com/doc/current/cookbook/routing/custom_route_loader.html) - **requires routing.yml modification**
- use [Symfony CMF RoutingBundle](https://github.com/symfony-cmf/RoutingBundle) and hook to [ChainRouter](https://symfony.com/doc/current/cmf/components/routing/chain.html) - **requires lots of reading and programming**

<br>

<div class="text-center">
    <img src="/assets/images/posts/2016/modular-router/mess.jpg" alt="Wow, so many options!">
    <br>
    <em>Wow, so many options!</em>
</div>

<br>

As [Matthias Noback](https://twitter.com/matthiasnoback) [wrote 4 years ago](http://php-and-symfony.matthiasnoback.nl/2012/01/symfony2-dynamically-add-routes/), in Symfony 1 you could use `routing.load_configuration` event to do this, but it was removed in Symfony 2. As a replacement, Matthias suggests creating custom Route Loader. It's the best solution so far I used before.

But I'm older and more lazy now so I tried to find a simpler way.

> Warning!<br>
> If you prefer **YAML, XML or PHP definition, good news - continue**.<br>
> In case you use `@Route` annotation, following solution won't help you.


## Load your routes in 1 method

Routes are usually in form of a simple array with `url` → `controller` records.

**What if loading of this simple array could be done via simple service? Something as simple as:**

```php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

public function getRouteCollection(): RouteCollection
{
    return $this->loadRouteCollectionFromFile(__DIR__ . '/routes.yml');

    // OR xml
    return $this->loadRouteCollectionFromFile(__DIR__ . '/routes.xml');

    // OR even multiple files
    return $this->loadRouteCollectionFromFiles([
        __DIR__ . '/front_routes.yml',
        __DIR__ . '/admin_routes.yml'
    ]);

    // OR pure PHP with some tweaks
    $routeCollection = new RouteCollection();
    $routeCollection->add('my_route', new Route('/hello'));

    return $routeCollection;
}
```

## Load your routes in modular way in 4 steps

All those options above are available in Symfony, thanks to [Symplify\ModularRouting package](https://github.com/Symplify/ModularRouting).

Let's try it together.

### 1. Install package

```bash
composer require symplify/modular-routing
```

### 2. Register bundles

```php
// app/AppKernel.php
final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Symplify\ModularRouting\SymplifyModularRoutingBundle(),
            // ...
        ];
    }
}
```

### 3. Create services to load your YAML file

```php
// src/SomeBundle/Routing/SomeRouteCollectionProvider.php
namespace SomeBundle\Routing;

use Symfony\Component\Routing\RouteCollection;
use Symplify\ModularRouting\Routing\AbstractRouteCollectionProvider;

final class SomeRouteCollectionProvider extends AbstractRouteCollectionProvider
{
    public function getRouteCollection(): RouteCollection
    {
        # routes.yml is the file, where all your routes are located
        return $this->loadRouteCollectionFromFile(__DIR__ . '/routes.yml');
    }
}
```

### 4. Register it as a service to your module

```yaml
# src/SomeBundle/Resources/config/services.yml
services:
    SomeBundle\Routing\SomeRouteCollectionProvider: ~
```

And that's it! Now all routes are loaded along with your bundle registration.


For further use, **just check [Readme for Symplify/ModularRouting](https://github.com/Symplify/ModularRouting)**.


<br>

<div class="text-center">
    <img src="/assets/images/posts/2016/modular-router/you-are-king.jpg">
</div>

<br>

## What have you learned today?

- that registering routes usually requires using `app/routing/routing.yml` - unfortunately :(
- that routes is basically array of `url` → `controller` records
- **that you can load them per module via service with [Symplify/ModularRouting](https://github.com/Symplify/ModularRouting)**


If you have some questions or tips for how to make loading of routes simpler, just let me know below.

Happy coding!

