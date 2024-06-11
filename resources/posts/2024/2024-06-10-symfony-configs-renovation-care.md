---
id: 413
title: "2 Tricks to get your Symfony configs lines close minimum"
perex: |
    I believe that every Symfony app can fit service config under 5 lines.

    Configs is one of underestimated parts of Symfony projects that truly deserves to be done right. Why? Like a healthy tree trunk brings power to the branches and leaves, clear configs keep design architecture clear and easy to grow.
---

But before we start with renovation, we need stable buildings grounds. That means we have configs [in PHP](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify), we use [Generated Configs](https://getrector.com/blog/modernize-symfony-configs) and `load()` to register services.

Do you still have configs more than 10 lines? Or more than 100, 200... lines? We can do better. I'll share 3 technique I've been using to achieve best architecture with the least lines.

<img src="https://private-user-images.githubusercontent.com/924196/279690004-14e46986-656c-4891-8c90-1d5df0a68144.jpeg?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3MTgxMDY2MDQsIm5iZiI6MTcxODEwNjMwNCwicGF0aCI6Ii85MjQxOTYvMjc5NjkwMDA0LTE0ZTQ2OTg2LTY1NmMtNDg5MS04YzkwLTFkNWRmMGE2ODE0NC5qcGVnP1gtQW16LUFsZ29yaXRobT1BV1M0LUhNQUMtU0hBMjU2JlgtQW16LUNyZWRlbnRpYWw9QUtJQVZDT0RZTFNBNTNQUUs0WkElMkYyMDI0MDYxMSUyRnVzLWVhc3QtMSUyRnMzJTJGYXdzNF9yZXF1ZXN0JlgtQW16LURhdGU9MjAyNDA2MTFUMTE0NTA0WiZYLUFtei1FeHBpcmVzPTMwMCZYLUFtei1TaWduYXR1cmU9OTAyNTlhM2FhODcwZTA5OGJjNDdkYTRmMzIxZjczOGEyNzk0MjdhMzY4ZGU2ZDBmOTc3NGZjMGZkNTM3NDgwOCZYLUFtei1TaWduZWRIZWFkZXJzPWhvc3QmYWN0b3JfaWQ9MCZrZXlfaWQ9MCZyZXBvX2lkPTAifQ.jP1D85c6B4QRkbXNiPBAlQOrewF7rrtyXNI1v8VePQY" class="img-thumbnail">

<span>Our goal is to achieve same architecture with minimum config lines. Lets move the architecture design back to the PHP code.</p>

<br>

## 1. From named Services, to Unique Types

In Symfony 2.8 times, it was common to make up string names for services, to pass them as arguments to other services:

```php
$services->set('app.data_analyser', DataAnalyser::class);

$services->set('app.homepage_controller', HomepageController::class)
    ->arg('$dataAnalyser', 'app.data_analyser');
```

From Symfony 3.0, there is no real need, as every service is either unique, e.g. our `DataAnalyser`, or a collected type, e.g. event subscribers. We can remove service names and their references are autowire by single unique type.

```diff
-$services->set('app.data_analyser', DataAnalyser::class);
+$services->set(DataAnalyser::class);

+$services->set('app.homepage_controller', HomepageController::class)
-$services->set(HomepageController::class);
-    ->arg('$dataAnalyser', 'app.data_analyser');
```

This way you can clear most of the useless code in our configs. It's clear path for **a single unique type**.

<br>

But what is we have multiple instance of the same type?
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

But do we? This is typical a factory pattern. We create multiple instance of the same type, but with different values in the `__construct()`.

<br>

Code smell is lurking. It's not the right place to use the factory pattern.

The `HomepageController` will always accept the same to`DataAnalyser` instance, and the `AnalyseCommand` will always accept a different `DataAnalyser` instance.

What can see reading the config?

* there is exact amount of instances of same service - in our case 2 services of `DataAnalyser`
* they need a made-up string name to be unique
* they're always used as arg explicitly in the same places
* there is no other services with same parent type passed to the constructor
* it hides the project architecture from PHP class itself to the config

I see it as miss use of factory pattern for [config coding](/blog/2019/02/14/why-config-coding-sucks).

<br>

What is the way out? Use **unique types** instead:

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

Now we have 2 unique instances, created in PHP code, outside the config. These services are now:

* independent on the config
* autowirable by exact type
* easier to reuse across other framework

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

This is super effective to make configs tight and application design robust.

<br>

<blockquote class="blockquote text-center">
I believe that every Symfony app<br>
can fit service config under 5 lines.
</blockquote>

## 2. From manual binding to `#[Autowire]` attribute

Autowiring by type is quite familiar to you. But sometimes we need to pass a scalar value, like a route name or API key:

```php
$services = $containerConfigurator->services();

$services->set(DataAnalyser::class)
    ->arg('$environment', '%kernel.environment%')
    ->arg('$secret', '%env(LOGGER_SECRET)%');
```

For every single scalar line, our config is 1 line longer.

This is a recent addition in Symfony 6.1 version. At first, I was hesitant to move the logic to the service itself. The configuration should be config, right? After few experiments I changed my mind. Services now clearly define their dependencies, and we don't have to jump back and forth to the config file to learn about them.

To autowire a param in your service, just add `Autowire` and pass `param` or `env` named argument value:

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

Once we autowire params in the services itself, we can get rid of manual registrations. We can go even further and drop the services line itself, as we already register all services via `load()`:

```diff
 $services = $containerConfigurator->services();

-$services->set(DataAnalyser::class)
-    ->arg('$environment', '%kernel.environment%')
-    ->arg('$secret', '%env(LOGGER_SECRET)%');
```




<br>


Happy coding!
