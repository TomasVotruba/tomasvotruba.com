---
id: 387
title: "Experiment: How I replaced Symfony DI with Laravel&nbsp;Container in ECS"

perex: |
    This year I've been learning Laravel and quickly adapting to most of my tools. I've made 2 packages - [Punchcard](/blog/introducing-punchcard-object-configs-for-laravel) to handle configs, and [Bladestan](/blog/introducing-bladestan-phpstan-analysis-of-blade-templates) for static analysis of Blade templates using PHPStan.

    The component I wanted to put in tests was [Laravel Container](/blog/what-i-prefer-about-laravel-dependency-injection-over-symfony). Everything went well on small projects, but what about packages with 10 000 000+ downloads?

    This week, I gave it a try on ECS, and this is how it went.
---

I'm not much fan of "best practices", Tweets by authorities, or "this works for everyone" claims. What works for you doesn't have to work for me and vice versa. Instead, I prefer controller experiments where **I can see real numbers on a single real project**.

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">What are you working on today? 🤗<br><br>I&#39;m currently migrating the biggest <a href="https://twitter.com/symfony?ref_src=twsrc%5Etfw">@symfony</a> DI container to <a href="https://twitter.com/laravelphp?ref_src=twsrc%5Etfw">@laravelphp</a> so far... the Easy Coding Standard. <br><br>I&#39;m curious about results 🙏 <a href="https://t.co/kzrRpLHi3x">pic.twitter.com/kzrRpLHi3x</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1683456457633669122?ref_src=twsrc%5Etfw">July 24, 2023</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>


## The Scope

The projects I work with are typically CLI PHP applications. They use Symfony DI and Symfony Console. They are not web applications, so I don't need to care about HTTP requests, sessions, or cookies.

**Unfortunately, the `symfony/dependency-injection is tightly coupled with `symfony/http-kernel`** as I already wrote in "[What I prefer about Laravel Dependency Injection over Symfony](/blog/what-i-prefer-about-laravel-dependency-injection-over-symfony)". This and another complexity leads to **slow container compilation and unnecessary complexity we have to learn, counteract in case of parameter invalidation, downgrade and maintain**.

Also, CLI tools are stuck with Symfony 6.1 because Symfony 6.2 uses complex PHP features (some reflection + attributes combo, not sure exactly) that Rector fails to downgrade to PHP 7.2 without breaking it.

<br>

On the other hand, [Laravel container](https://github.com/illuminate/container/) contains **only 6 PHP files** &ndash; only 2 files contain some logic:

<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/cde74668-5724-49c8-b0dc-7b1165b247e9" class="img-thumbnail">

It has no external dependencies, except contracts packages:

```json
{
    "require": {
        "php": "^8.2",
        "illuminate/contracts": "^11.0",
        "psr/container": "^1.1.1|^2.0.1"
    }
}
```

This seems like a good candidate for a DI container, where all you need is to get a service with injected dependencies, right?

<br>

Today we'll focus on **practical drop-in replacement** of `symfony/dependency-injection with `illuminate/container` in [Easy Coding Standard](https://github.com/easy-coding-standard/easy-coding-standard), how I've done with the help of Chat GPT and what are the results.

Nothing more, nothing less. If this goes well, I want to try and measure a similar experiment on Rector or [legacy projects we upgrade](https://github.com/easy-coding-standard/easy-coding-standard).

<br>

## The Main Difference between Symfony and Laravel Container

One of the often-mentioned differences is that Symfony compiles container and Laravel creates services on the fly. But that was never a problem or benefit for me.

A more practical difference is that:

* Laravel tries to create every service for you without any configuration,
* Symfony only creates services you explicitly configure

But there is a catch - Laravel creates everything from scratch, so I you require a service 2 times, you'll get 2 different instances. To avoid that, you have to **explicitly register this service**.

I personally find **this very useful because it forces me to write clean stateless services** - once a service depends on a state, e.g., I have to set some configuration at a random point of time except the constructor, then it's not really a service design and should be refactored.

<br>

All clear? **Let's deep dive into the experiment**. I'll share the pull-request link at the end, so you can review all the changes step by step yourself.

<br>

## Step 1: Let's create a Laravel Container

**In Symfony**, we create Kernel, where we register service configs and compiler passes. Using container builder, we build a container that we fetch from the container:

```php
$kernel = new Kernel();
$kernel->addCompilerPass(new SomeCompilerPass());
$kernel->addConfig(__DIR__ . '/config/some_config.php');
$kernel->boot();

$container = $kernel->getContainer();

$application = $container->get(Application::class);
$application->run();
```

<br>

**In Laravel**, we only create `Container` and ask for a service:

```php
use Illuminate\Container\Container;
use Symfony\Component\Console\Application;

$container = new Container();

$application = $container->get(Application::class);
$application->run();
```

That's it!

<br>

This is typically part of the `bin/ecs` file, where we create a container and fetch a console application to run a console command, e.g., `bin/ecs check src`.

<br>

## Step 2: Let's Register Commands

**In Symfony**, we have to register commands in `services.php` explicitly or with PSR-4 autodiscovery, autowire and autoconfigure. Then we also depend on Kernel correctly injecting commands:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\AppBundle\\', __DIR__ . '/../src/AppBundle');
};
```

<br>

**In Laravel**, I've decided to avoid configs completely and require commands explicitly via constructor:

```php
use Symfony\Component\Console\Application;

final class EasyCodingStandardConsoleApplication extends Application
{
    public function __construct(
        CheckCommand $checkCommand,
        WorkerCommand $workerCommand,
    ) {
        parent::__construct('EasyCodingStandard', StaticVersionResolver::PACKAGE_VERSION);

        $this->add($checkCommand);
        $this->add($workerCommand);
    }
}
```

Thanks to automated service creation, I don't have to worry about registering `CheckCommand` and `WorkerCommand` in the config. Laravel handles this for me once at the start of the application.

<br>

## Step 3: Registering a Simple Service

In the previous step, we skipped an important part, how to register a simple service.

**In Symfony**:

```php
$services->set(Filesystem::class);
```

<br>

**In Laravel**... we don't have to do anything. Laravel container handles it for us.

<br>
<br>

How about a tagged service? **In Symfony**:

```php
$services->set(ConsoleFormatter::class)
    ->tag(FormatterInterface::class);
```

<br>

**In Laravel** we tag service in a standalone line:

```php
$container->singleton(ConsoleFormatter::class);
$container->tag(ConsoleFormatter::class, FormatterInterface::class);
```

<br>

## Step 4: A Service that Requires collection of other Services

A typical example of this is a `SniffFileProcessor` or `FixerFileProcessor` that collects all sniffers or fixers and runs them on a file. Both frameworks use tagged services, so we only collect them and pass them along.

**In Symfony**, we only set specific arguments with tagged services:

```php
$services->set(FileProcessor::class)
    ->arg('$sniffs', tagged_iterator(Sniff::class));
```

<br>

**In Laravel**, this is a bit more complicated, as there are no abstract definitions and we create services directly. As far as I know, we have to pass all other services too:

```php
$container->singleton(FileProcessor::class, function (Container $container) {
    return new FileProcessor(
        $container->make(Filesystem::class),
        $container->make(FileDiffFactory::class),
        $container->tagged(Sniff::class),
    );
});
```

(Do you know a better way how to handle this in Laravel? Please let me know; maybe I'm doing it long.)

<br>

## Step 5: From Compiler Pass to...?

So far, we only handled simple steps such as registration of services. Let's level up a bit.

In ECS, sometimes we want to skip a fixer/sniff completely because it doesn't fit our preference:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->skip([
        SomeStrictFixer::class,
    ]);
};
```

How do we remove a service from the container? **In Symfony**, we have compiler passes that run before the container is compiled:

```php
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        // resolve excluded class from skip() parameter
        $excludedClass = '...';

        $containerBuilder->removeDefinition($excludedClass);
    }
}
```

That's it! From now on, the `SomeStrictFixer` will not be anywhere in our application. It's like it never existed.

<br>

**In Laravel**, this became quite a challenge. Instead of adding a compiler pass, we run the `beforeResolving()` method. This method is run before every service is resolved, so we pick one of those that will get initialized at the start.

Removing service from the container is easy. But there is **a catch for tagged services** - if we don't remove it from tagged services, it will still get injected via `$container->tagged()`. Here is the solution I came up with (I'm sure there is a better way):

```php
use Illuminate\Container\Container;

$container->beforeResolving(
    FixerFileProcessor::class,
    static function ($object, $misc, Container $container): void {
        $this->removeServiceFromContainer($container);
    }
);

private function removeServiceFromContainer(Container $container): void
{
    // resolve excluded class from skip() parameter
    $excludedClass = '...';

    // remove instance
    $container->offsetUnset($excludedClass);

    $tags = PrivatesAccessorHelper::getPropertyValue($container, 'tags');
    foreach ($tags as $tag => $classes) {
        foreach ($classes as $key => $class) {
            if ($class !== $excludedClass) {
                continue;
            }

            // remove the tagged class
            unset($tags[$tag][$key]);
        }
    }

    // update value in the container
    PrivatesAccessorHelper::setPropertyValue($container, 'tags', $tags);
}
```

I feared the migration of compiler passes the most, but ChatGPT showed me [one more method: `afterResolving()`](https://github.com/illuminate/container/blob/7ebfc9acfd5d5c7dda1ff5975927c6569651857f/Container.php#L1184-L1202). With these 2 methods replacing compiler passed was easy.

*Fun fact: these 2 methods are not documented in official Laravel documentation.*

<br>

## Step 6: Add extra call or parameters to a Service

**In Symfony**, when we want to add a call or property, we use the `call()` or `property()` method:

```php
$definition = $services->set($checkerClass);

$definition->call('methodCall', [123]);
$definition->property('$publicProperty', ['hey']);
```

<br>

**In Laravel**, we can use an `extend()` method:

```php
$container->extend($checkerClass, function (CheckerClass $checkerClass) {
    $checkerClass->configure(123);
    $checkerClass->publicProperty = 'hey';

    return $checkerClass;
});
```

<br>

## Step 7: Import Configuration Files

Last but not least, sometimes we need to import external configuration. E.g., in ECS, we want to import a set of rules (services):

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([SetList::PSR_12]);
};
```

<br>

**In Symfony**, we use `import()` method in combination with PHP or YAML file loader:

```php
$containerBuilder->import($filePath);
```

<br>

What actually "importing a new file" does?

* We pass the container/container builder class somewhere,
* There is a closure that accepts it as a parameter
* it decorates the passed container with more services and parameters.

Saying that **in Laravel**, we pass this container to the included file closure:

```php
$closureFilePath = require $filePath;
$closureFilePath($container);
```

That's it!

<br>

## First Results: Developers Experience and Performance

What has changed? I really enjoy working with DI now. We don't have to include any configs nor configure directory to load services from. Everything default is created for us; everything **non-standard or weird is explicitly defined in the container**.

The speed is amazing, and it will get only better once we figure out Laravel container bottlenecks. Thanks to GPT and a neat tests suite that reported broken places, I was able to make the switch **under 6 hours**.

I really look for the next ideas once the dust settles.

<br>

I didn't expect this, but happy to see that tests run 3-4 times faster with Laravel container:

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">I&#39;m rethinking the way I approached <a href="https://twitter.com/laravelphp?ref_src=twsrc%5Etfw">@laravelphp</a> performance in past years 🤔 Why?<br><br>This is ECS test-suite:<br><br>1) On the left, Symfony 6 - 0,759 ms<br>2) On the right, Laravel 10 - 0,179 ms<br><br>That&#39;s 370 % faster 😲 😍 <a href="https://t.co/cbXTZ3MUVn">pic.twitter.com/cbXTZ3MUVn</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1683576139049058304?ref_src=twsrc%5Etfw">July 24, 2023</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

For more performance testing, I'll release a new ECS version and try it out in the wild. I also want to check how the `/vendor` size changed, as that's crucial in CLI tools that include downgraded and scoped `/vendor`.

<br>

You **can review these changes in detail yourself a [single pull-request in ECS repository](https://github.com/easy-coding-standard/easy-coding-standard/pull/105)**. Tests were passing 100 % before with Symfony DI, and they were passing 100 % after with the new Laravel Container.

<br>

I'm a Laravel-beginner, so if you see a much better way to achieve some goal, **let me know in the pull-request**. Thank you!

<br>


Happy coding!
