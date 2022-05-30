---
id: 356
title: "How to Make Configs like RectorConfig or ECSConfig for your Symfony Project"
perex: |
    We've introduced a new way to configure PHP tools on Symfony DI. A way that is decoupled brings new methods with autocomplete and validates your input. I talk about [RectorConfig](https://getrector.org/blog/new-in-rector-012-introducing-rector-config-with-autocomplete) and [ECSConfig](https://tomasvotruba.com/blog/new-in-ecs-simpler-config/).
    <br><br>
    At first, I thought it was not possible. Symfony is very strict about this and does not allow any extensions. After a few days of hacking Symfony, I found a space to squash `<x>Config` class. After meeting with [Sebastian Schreiber](https://twitter.com/schreiberten) last week, we found an even better generic solution.
    <br><br>
    Are you interested in a better developer experience for your Symfony project? Keep reading.

tweet_image: "/assets/images/posts/2022/custom_config_symfony.png"
---

This is `ECSConfig` in action:

<img src="/assets/images/posts/2022/ecs_config.gif" class="img-thumbnail">

<br>

## What is Good For?

### 1. Isolate from Framework

This is very important for projects like [Rector](https://getrector.org/) or [ECS](https://github.com/symplify/easy-coding-standard/), as you'll use them to analyze or refactor code that uses Symfony.

<br>

You can have 2 classes with the same `Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator` name but different return types or event methods:

* an ECS class in Symfony 6.0
* your project class in Symfony 5.0

The autoload will get confused, and your project [will crash](https://github.com/rectorphp/rector/issues/6698). Config class in **your namespace prevents this**.

### 2. Add own Config Methods with Instant Validation

This is a significant side effect from a developer experience point of view. We can still add parameters and services with the bare `ContainerConfigurator` class. But **what about domain specifics**? E.g., In Rector, we want to register rules and run them on specific paths:

```php
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
    ]);

    $rectorConfig->rule(ClosureToArrowFunctionRector::class);
};
```

<br>

Here we have **2 domain-specific** methods tailored to our developer needs:

* We wrote a `paths()` method that validates provided directory exists. That way, we know about typos or incorrect nesting right in the first second.

* As for the `rule()` method, we can validate the class exists and that its type is `RectorInterface`. Such validation can prevent passing non-existing classes that Symfony silently skips.

### 3. Narrow Context to Narrow Focus

If we explore all Symfony config features like `autowire()`, `parameters()`, or autodiscovery, we'll slowly drift away. When we're driving a car, we should not try to check what cylinder has gasoline and which is exploding. Too much complexity will make our driving distracted and make it easier to crash.

We should provide **focus by design**. Does your tool generate a static blog? Add `sourcePath()` and `outputPath()` methods. Do you work with real-time stats of cryptocurrencies? Create `watchCurrencies([...])` method to list currencies to watch, etc.

With the exact 3 methods of simple scalar arguments, **your users know what to do**.

<br>

## How to teach Symfony to work with our Custom Config?

Let's try to implement such a config class from scratch, e.g., for a cryptocurrency watcher. We use the `ContainerConfigurator` as a base-building stone:

```php
namespace CryptoWatch\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator

final class CryptoWatchConfig extends ContainerConfigurator
{
    /**
     * @param string[] $currencies
     */
    public function watchCurrencies(array $currencies): void
    {
        // you can validate $currencies to contain only currencies you handle

        // set parameter (or create service), depending on your domain
        $parameters = $this->parameters();
        $parameters->set('currencies', $currencies);
    }
}
```

<br>

The config uses public API methods from `ContainerConfigurator` to work with parameters and services.
But the user of our package will be saved from those implementation details.

They will **simply use it like this**:

```php
// config/config.php
namespace CryptoWatch\Config\CryptoWatchConfig;

return function (CryptoWatchConfig $cryptoWatchConfig): void {
    $cryptoWatchConfig->watchCurrencies(['btc', 'eth']);
};
```

That's it! How simple implementation, right?

Here we expect the Symfony to take the `CryptoWatchConfig` type from the closure, create an instance, and pass it to the dependency injection container.
That way, our project will have parameters of key `currencies` with value `['btc', 'eth']`. That's at least how [config builders like `SecurityConfig`](https://symfony.com/blog/new-in-symfony-5-3-config-builder-classes) in Symfony 5.3 should work.

<br>

That's how we *wish* the Symfony to work, but the reality is slightly... unexpected.

<br>

## How to Teach Symfony to Accept Custom Configs

When we run the project with the config above, it will crash with the following message:

```bash
Could not resolve argument "CryptoWatchConfig $cryptoWatchConfig"
```

### 1. New Instance

But why? The config builders **only work for extensions**, and methods are automatically generated in a temp directory. They're not "use your own config class" friendly.

<br>

The `PhpFileLoader` class is responsible for creating configs. When we explore it, [there is a line](https://github.com/symfony/symfony/blob/a10071bd657c350bb8f995361643072a97ff5819/src/Symfony/Component/DependencyInjection/Loader/PhpFileLoader.php#L67) responsible for creating `ContainerConfigurator`:

```php
$this->executeCallback($callback, new ContainerConfigurator(...), $path);
```

It only creates exactly `ContainerConfigurator`. Nothing else.

<br>

### What can we Improve Here?

We need to create the same type, as we put in the closure param type. This closure:

```php
return function (CryptoWatchConfig $cryptoWatchConfig): void {
    // ...
}
```

<br>

Should result into:

```php
$containerBuilder = new CryptoWatchConfig(...);
```

<br>

In other words, we need to change `PhpFileLoader` code into generic solution that takes param type and creates a class from it:

```php
$reflectionFunction = new \ReflectionFunction($callback);
$firstParameterReflection = $reflectionFunction->getParameters()[0];
$containerConfiguratorClass = $firstParameterReflection->getType()->getName();

// the $containerConfiguratorClass is `CryptoWatchConfig` or any config we provide

$this->executeCallback($callback, new $containerConfiguratorClass(...), $path);
```

👍️

### 2. Open Param Type

But there is 2nd problem. The type is resolved from [param reflection here](https://github.com/symfony/symfony/blob/a10071bd657c350bb8f995361643072a97ff5819/src/Symfony/Component/DependencyInjection/Loader/PhpFileLoader.php#L121-L137):

```php
switch ($type) {
    case ContainerConfigurator::class:
        $arguments[] = $containerConfigurator;
        break;

    // ...
    default:
        throw new \InvalidArgumentException(sprintf(
            'Could not resolve argument "%s"', $type
        ));
```

<br>

What can be improved here? `Switch` for type detection is generally a terrible idea, and it only matches a single exact type:

```php
// exclusive 1 entrance :(
get_class($class) === Controller::class

// children on-board :)
$class instanceof Controller
```

<br>

In other words, we change the code to this:

```php
if ($type instanceof ContainerConfigurator) {
    $arguments[] = $containerConfigurator;
} else {
    // ... fallback to switch
}
```

👍️

That's it!

<br>

We had to change that to make `ECSConfig`, `RectorConfig`, `MBConfig` `EasyCIConfig`, and the rest work with Symfony. It's a single change to allow all of them, including your custom config that extends `ContainerConfigurator`.

Do you think this should be part of Symfony core? Let me know in the comments.

<br>

**tl;dr;** Add [the patch file](https://github.com/symplify/vendor-patch-files/blob/main/patches/generic-php-config-loader.patch) and create your config today.

<br>

Happy coding!
