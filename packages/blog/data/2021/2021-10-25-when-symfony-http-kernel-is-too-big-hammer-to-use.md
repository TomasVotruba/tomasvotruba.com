---
id: 337
title: "When Symfony&nbsp;Http&nbsp;Kernel is a Too&nbsp;Big&nbsp;Hammer to&nbsp;Use"
perex: |
    I've been a big fan of Symfony components for ages. I use them as core bricks of my projects [migrate other frameworks to it](/blog/2019/08/26/how-we-migrated-54-357-lines-of-code-nette-to-symfony-in-2-people-under-80-hours), and every 6 months, I'm excited about what new features are coming in the next minor release.
    <br><br>
    But, one tough spot has been bothering me for the last 4 years. I tried to find my way out of it, hack around it or accept it. In March 2021, we [downgrade Rector 0.10 from PHP 8 to 7.1](https://getrector.org/blog/2021/03/22/rector-010-released-with-php71-support#rector-on-php-7-1-and-7-2-without-docker), and the issue became visible more than ever.
    <br><br>
    I knew there was a time for a change.

tweet: "New Post on the 🐘 blog: When Symfony Http Kernel is a Too Big Hammer to Use"
tweet_image: "/assets/images/posts/2021/symfony_kernel/composer_require.gif"
---

## Http Kernel and Console Applications

The command-line application is anything we run from CLI - PHPUnit, PHPStan, Rector, Composer, or ECS. At the start of building such an application, you stand in front of an important decision:

* manual construction of every service, aka "manual dependency injection",

```php
use Symfony\Component\Console\Application;

$application = new Application();
$fileAnalyzer = new FileAnalyzer();
$application->addCommand(new ProcessCommand($fileAnalyzer));
$application->run();
```

* or re-use of existing DI container

```php
use Symfony\Component\Console\Application;

$application = $container->get(Application::class);
$application->run();
```

I grew up on DI containers, so I'm spoiled by the automatic injection and service management this pattern handle for me. That's why [I picked `symfony/http-kernel` to build console application](/blog/2018/05/28/build-your-first-symfony-console-application-with-dependency-injection-under-4-files/).

## What about `symfony/dependency-injection`?

The name seems quite fitting, right? We could provide a single config file, and that's it. Unfortunately, the name is a bit misleading. It does not handle compiler passes, extensions, service autodiscovery, container cache, and container build. You'll find most of the building bricks there, **but the glue is missing**.

There is no "container factory" class in `symfony/dependency-injection`, that would handle even the simplest use case:

```php
use Symfony\Component\DependencyInjection\ContainerFactory;
use Symfony\Component\Console\Application;

$containerFactory = new ContainerFactory();
$container = $containerFactory->createFromConfigs([__DIR__ . '/config/config.php']);

$application = $container->get(Application::class);
```

<br>

On the other hand, there is an ["optional dependency"](https://matthiasnoback.nl/2014/04/theres-no-such-thing-as-an-optional-dependency/) on `symfony/http-kernel` package. Some Symfony components are decoupled well, but some are [mutually dependent even if you don't need them](https://paul-m-jones.com/post/2013/01/02/symfony-components-sometimes-decoupled-sometimes-not/):

<img src="/assets/images/posts/2021/symfony_kernel/symfony_paul_m_jones.png" class="img-thumbnail">

And we're forced to use Kernel with Http.

<br>

Btw, both articles by [Matthias Noback](https://matthiasnoback.nl/2014/04/theres-no-such-thing-as-an-optional-dependency/) and [Paul M. Jones](https://paul-m-jones.com/post/2013/01/02/symfony-components-sometimes-decoupled-sometimes-not/) are not just critics of tight coupling, but great specific tips about how to create a future proof and solid architecture design. If you haven't seen them, make sure you grasp the main ideas, and they will serve you in the future.

## There is no "Http" in CLI

The command-line application run in a command line. There is no HTTP request, no browser, no routes, no session, no-cache.

Instead of URL with parameters, **we run command-line arguments and options**:

```bash
composer require symfony/http-kernel --dev
```

## The 4 Components you don't Need, but Must Have

If there few more "http" classes we never use, we can live with that.

But what do we get when we actually run the composer command above?

<img src="/assets/images/posts/2021/symfony_kernel/composer_require.gif" class="img-thumbnail">

<br>

There are some packages related to http that's expected. But why is there debugging package in our production tool?

<img src="/assets/images/posts/2021/symfony_kernel/symfony_dependent_var_dumper.gif" class="img-thumbnail">

<br>

So when we require `symfony/http-kernel`, we also get 4 more packages we don't use:

- `symfony/error-handler` - for debug?
- `symfony/event-dispatcher` - in case we *might use* events some day?
- [`symfony/http-foundation`](https://github.com/symfony/http-foundation) - over 40 classes related exclusively to http request and response
- `symfony/var-dumper` - for debug?

<br>

Maybe we could say:

<blockquote class="blockquote text-center">
That's "optional dependency" in case of<br>
"dev" environment to report bugs.
</blockquote>

That's completely ok for [request and response](https://en.wikipedia.org/wiki/Request%E2%80%93response) http workflow, but **one more package we'll never use, but have to maintain**.

Now we understand why most CLI app developers decided not to use any container but [create their services manually](https://github.com/composer/composer/blob/e6cfc924f24089bc02cf8f4d27367b283247610e/src/Composer/Console/Application.php#L490-L519).

## The Downgrade Covariance of Symfony Config

You might think:

<blockquote class="blockquote text-center">
What do you mean by 'maintain'? It's few dependencies<br>
that we'll never use and just take little space on a disk.
<br>
What's the big deal?
</blockquote>

Let's get back to the start. In April 2021, we started to develop Rector on PHP 8 and [release PHP 7.1 downgraded version](https://getrector.org/blog/2021/03/22/rector-010-released-with-php71-support#rector-on-php-7-1-and-7-2-without-docker). Downgraded and scoped version means fully downgraded and scoped `/vendor`. Yes, including all Symfony components we use.

The downgrade PHP market is still quite a niche, but **the community is interested more and more in this field**. In December 2021 alone, there have been over 20 brand new downgrade rules contributed from Rector users.

## Try to Downgrade Invalid Code with Invalid Types

When we started downgrading Rector, we often came to incompatible types in `FileLoader` classes. The [covariant parameters added in PHP 7.4](https://wiki.php.net/rfc/covariant-returns-and-contravariant-parameters) started to cause problems on PHP 7.3 and bellow.

The most problematic was a downgrade of `import()` methods.

<br>

In [config `FileLoader.php`](https://github.com/symfony/symfony/blob/54015cccf0236bbdb38cd5abae5b2bdc89aa8ac2/src/Symfony/Component/Config/Loader/FileLoader.php#L71) they require `bool` parameter:

```php
public function import($resource, $type = null, bool $ignoreErrors = false) {}
```

But in a [dependency injection `FileLoader.php`](https://github.com/symfony/symfony/blob/54015cccf0236bbdb38cd5abae5b2bdc89aa8ac2/src/Symfony/Component/DependencyInjection/Loader/FileLoader.php#L55) that inherits former class, it is `string|bool`:

```php
public function import($resource, $type = null, bool|string $ignoreErrors = false) {}
```

<br>

We worked on a downgrade fix for 2 months before figuring our rules were correct. The problem is in contracts of Symfony method, which is invalid on PHP 7.3 and below.

## Accidental Complexity?

The problem is not about invalid contracts, and anyone could make a mistake in those. The problem is in **maintaining code we don't need nor use**.

## Need for Really Decoupled Container Factory

I wish there were a more straightforward container factory service that we could add to the project:

```bash
symfony/dependency-container-factory

Installing...
* symfony/dependency-container-factory
* symfony/dependency-injection
```

<br>

That would allow us only to create the container. With no extra dependencies, with a focus on service tree construction:

```php
use Symfony\Component\DependencyInjection\ContainerFactory;
use Symfony\Component\Console\Application;

$compilerPasses = [...];
$extensions = [...];

$containerFactory = new ContainerFactory($compilerPasses, $extensions);
$container = $containerFactory->createFromConfigs([__DIR__ . '/config/config.php']);

$application = $container->get(Application::class);
```

<p class="text-success pt-3 pb-3">✅</p>

<br>

It would make CLI apps much cleaner and easier to use Symfony in them.

<br>

What do you think? **What is your approach to creating service in your CLI applications?**

<br>

Happy coding!
