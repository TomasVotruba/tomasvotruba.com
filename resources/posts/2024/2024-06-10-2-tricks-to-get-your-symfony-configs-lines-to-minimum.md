---
id: 413
title: "2 Tricks to get your Symfony configs&nbsp;lines&nbsp;to&nbsp;minimum"
perex: |
    I believe that every Symfony app can fit service config under 5 lines.

    Configs are among the most underestimated parts of Symfony projects and deserve to be done right. Like a healthy tree trunk, which brings power to the branches and leaves, clear configs keep the design architecture clear and easy to grow.
---

But before we start with renovation, we need stable building foundations. That means we have configs [in PHP](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify), we use [Generated Configs](https://getrector.com/blog/modernize-symfony-configs) and `load()` to register services.

<blockquote class="blockquote text-center">
Any Symfony service config<br>
can be narrowed down under 5 lines.
</blockquote>

Do you still have configs with more than 10 lines? Or more than 100 or 200... lines? We can do better. I'll share 2 techniques I've been using for the past couple of years to achieve the best architecture with the fewest lines.

<img src="/assets/images/posts/2024/narrow-car.jpg" alt="Tesla car narrow down pieces from 171 to 2" class="img-thumbnail">

<em>When Tesla narrowed down 171 pieces to 2, it made their whole build process extremely simpler.<br>
The same way we narrow down config lines, so we can focus on the code itself.</em>

<br>

## 1. From named Services to Unique Types

In Symfony 2.8 times, it was expected to make up string names for services to pass them as arguments to other services:

```php
$services->set('app.data_analyser', DataAnalyser::class);

$services->set('app.homepage_controller', HomepageController::class)
    ->arg('$dataAnalyser', 'app.data_analyser');
```

From Symfony 3.0, there is no real need, as every service is either unique, e.g., our `DataAnalyser`, or a collected type, e.g., event subscribers.

<br>

Remove service names, so their references are autowired by unique type:

```diff
-$services->set('app.data_analyser', DataAnalyser::class);
+$services->set(DataAnalyser::class);

-$services->set('app.homepage_controller', HomepageController::class)
-    ->arg('$dataAnalyser', 'app.data_analyser');
+$services->set(HomepageController::class);
```

This way, you can clear most of the useless code in our configs. It's a clear path for a single unique type.

<br>

But what if we have **multiple instances of the same type**?
```php
$services->set('app.data_analyser', DataAnalyser::class)
    ->arg('$scope', 'production');

$services->set('app.dev_data_analyser', DataAnalyser::class)
    ->arg('$scope', 'dev');
```

We have to use string names so we can pass these services to different locations:

```php
$services->set(HomepageController::class);
    ->arg('$dataAnalyser', 'app.data_analyser');

$services->set(AnalyseCommand::class);
    ->arg('$dataAnalyser', 'app.dev_data_analyser');
```

But do we? This is a typical factory pattern. We create multiple instances of the same type but with different values in the `__construct()`.

## Avoid Factory coding in Configs

Configs are not the place to use a factory pattern. The `HomepageController` will always accept the same to `DataAnalyser` instance, and the `AnalyseCommand` will always accept a different `DataAnalyser` instance.

What can we see reading the config?

* there is a constant amount of instances of the same service - in our case 2 services of `DataAnalyser` type
* they need a made-up string name to be unique
* they're always used as arg explicitly in the same places
* there are no other services with the same parent type passed to the constructor
* it hides the project architecture from the PHP class itself to the config

I see it as a misapplication of the factory pattern for [config coding](/blog/2019/02/14/why-config-coding-sucks).

<br>

What is the way out?

**Make `DataAnalyser` abstract** and create **unique child types**:

```php
final class AppDataAnalyser extends DataAnalyser
{
    public function __construct()
    {
        parent::__construct('production');
    }
}

final class DevDataAnalyser extends DataAnalyser
{
    public function __construct()
    {
        parent::__construct('dev');
    }
}
```

Now we have 2 unique instances, defined in PHP code, outside the config. These services are now:

* independent on the config
* autowireable by the exact type
* easier to reuse across other frameworks

<br>

We can typehint them in the controllers:

```diff
 final class HomepageController
 {
     public function __construct(
-        private DataAnalyser $dataAnalyser,
+        private AppDataAnalyser $dataAnalyser,
     ) {
     }
 }
```

And we can clear up our configs from all the code we've used:

```diff
-$services->set('app.data_analyser', DataAnalyser::class)
-    ->arg('$scope', 'production');
-
-$services->set('app.dev_data_analyser', DataAnalyser::class)
-    ->arg('$scope', 'dev');
-
-$services->set(HomepageController::class);
-    ->arg('$dataAnalyser', 'app.dev_data_analyser');
-
-$services->set(AnalyseCommand::class);
-    ->arg('$dataAnalyser', 'app.data_analyser');
```

This approach is highly effective in making configs tight and application design robust.

<br>

## 2. From manual binding to `#[Autowire]` attribute

Autowiring by type is quite familiar to you. But sometimes, we need to pass a scalar value, like a route name or API key:

```php
$services = $containerConfigurator->services();

$services->set(DataAnalyser::class)
    ->arg('$environment', '%kernel.environment%')
    ->arg('$secret', '%env(LOGGER_SECRET)%');
```

For every single scalar line, our config is 1 line longer. 1 dangerous line that depends on vague argument order or name.

## PHP 8.0 Attributes to the Rescue

Following feature, is a recent addition at [Symfony 6.1](https://symfony.com/blog/new-in-symfony-6-1-service-autowiring-attributes). At first, I was hesitant to move the logic to the service itself. The configuration should be in the config file, right?

After few experiments, I changed my mind. Services now clearly define their dependencies; we don't have to jump back and forth to the config file to learn about them.

To autowire a param in your service, add `Autowire` and pass `param` or `env` named argument value:

```diff
+use \Symfony\Component\DependencyInjection\Attribute\Autowire;

 final readonly class DataAnalyser
 {
     public __construct(
+        #[Autowire(param: 'kernel.environment')]
         private $environment,
+        #[Autowire(env: 'LOGGER_SECRET')]
         private $loggerSecret,
     ) {
     }
 }
```

Once we autowire params in the services, we can eliminate manual registrations. We can go even further and drop the services line itself, as we already register all services via `load()`:

```diff
 $services = $containerConfigurator->services();

-$services->set(DataAnalyser::class)
-    ->arg('$environment', '%kernel.environment%')
-    ->arg('$secret', '%env(LOGGER_SECRET)%');
```

That's it! As a bonus, such attribute-based code can be also analysed by static analysis. We can create a PHPStan rule, that checks if parameter/env is defined and warns us early in the CI.

<br>


Happy coding!
