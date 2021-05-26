---
id: 103
title: "Why You Should Combine Symfony Console and Dependency Injection"
perex: |
    I saw 2 links to Symfony\Console in [today's Week of Symfony](http://symfony.com/blog/a-week-of-symfony-592-30-april-6-may-2018) (what a time reference, huh?).
    There are plenty of such posts out there, even in Pehapkari community blog: [Best Practice for Symfony Console in Nette](https://pehapkari.cz/blog/2017/06/02/best-practice-for-symfony-console-in-nette) or [Symfony Console from the Scratch](/blog/2019/08/12/standalone-symfony-console-from-scratch/).
    <br>
    But nobody seems to write about **the greatest bottleneck of Console applications - static cancer**. Why is that?
tweet: "A new post on my blog: Why You Should Combine Symfony Console and Dependency Injection"
tweet_image: "/assets/images/posts/2018/cli-app-di/cli-app-di.png"
---

## 1. Current Status in PHP Console Applications

Your web application has an entry point in `www/index.php`, where it loads the DI Container, gets `Application` class and calls `run()` on it (with explicit or implicit `Request`):

```php
require __DIR__ . '/vendor/autoload.php';

// Kernel or Configurator
$container = $kernel->getContainer();
$application = $container->get(Application::class);
$application->run(Request::createFromGlobals());
```

Console Applications (further as *CLI Apps*) have very similar entry point. Not in `index.php`, but usually in `bin/something` file.

When we look at entry points of popular PHP Console Applications, like:

<br>

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/bin/phpcs)

```php
$runner = new PHP_CodeSniffer\Runner();
$runner->runPHPCS();
```

<br>

[PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/php-cs-fixer)

```php
$application = new PhpCsFixer\Console\Application();
$application->run();
```

<br>

[PHPStan](https://github.com/phpstan/phpstan/blob/master/bin/phpstan)

```php
$application = new Symfony\Component\Console\Application('PHPStan');
$application->add(new AnalyseCommand());
$application->run();
```

<br>

If we **mimic such approach in web apps**, how would our `www/index.php` look like?

```php
require __DIR__ . '/vendor/autoload.php';

$application = new Application;
$application->addController(new HomepageController);
$application->addController(new PostController);
$application->addController(new ContactController);
$application->addController(new ProducController);
// ...
$application->run();
```

How do you feel seeing such code? I feel a bit weird and [I don't get on well with static code](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/).


On the other hand, if we take the web app approach to cli apps:

```php
$container = $kernel->getContainer();

$application = $container->get(Application::class);
$application->run(new ArgInput);
```

### Why is That?

I wish I knew this answer :). In my opinion and experience with building cli apps, there might be few...

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- CLI apps almost always start with simple plain PHP code:

    ```php
    # bin/turn-tabs-to-spaces.php

    $input = $argv[1];

    // 1st PSR-2 rule: replace tabs with spaces
    return str_replace('\t', ' ', $input);
    ```

    No container, no dependency injection, sometimes not even dependencies. Just see [the PHP-CS-Fixer v0.00001](https://gist.github.com/fabpot/3f25555dce956accd4dd).

    When the proof of concepts works, the application grows.

- It's easy, quick and simple.
- Who would use container right from the start of 1 command, right?

### ❌ Disadvantages

- If you start a project with `new` static, it's difficult to migrate.
- The need of refactoring is clear much earlier before it really happens.
- When the application grows, new classes are added and you need to think more and more what class to pass by the constructor, which are singletons, which value objects/DTOs etc.

## 2. Container Inceptions

The container is slowly appearing not as the backbone of application as in web apps, but as part of commands.

E.g. [`AnalyseCommand`](https://github.com/phpstan/phpstan/blob/a991e94fca78b7fb7017a469b19834766787a04c/src/Command/AnalyseCommand.php#L152) in PHPStan:

```php
use Symfony\Component\Console\Command\Command;

class AnalyseCommand extends Command
{
    // ...

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->containerFactory->createFromConfig($input->getOption('config'));

        $someService = $container->get(SomeService::class);
        // ...
    }
}
```

Or in [`FixerFactory`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/0257ccae7ddcbdd5f7d51de3ae92ffac02d0e1c1/src/FixerFactory.php#L107) in PHP CS Fixer:

```php
# much simplified

class FixerFactory
{
    public function registerBuiltInFixers()
    {
        static $fixers = [];

        foreach (Finder::findAllFixerClasses() as $fixerClass) {
            $fixers[] = new $fixerClass;
        }
    }
}
```

### ❌ Disadvantages

- Well, ambiguous approach to creating service-like-classes.
- There is an inconsistent approach to services. How do you know where to put it? Is it a service or is it a class to be created manually?
- Should you inject dependency manually or let container (or any higher service) handle that?

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- It's better than no container at all.
- It gives at least some basis for future refactoring.
- Very useful for collecting of minimal basic classes: like rules in PHPStan, Fixers in PHP CS Fixer or Sniffs in PHP Code Sniffer.

<br>

**Imagine a code like this in your web application**:

```php
class ProductController
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connectoin;
    }

    public function detail($id);
    {
        $productRepository = new ProductRepository($this->connection);
        $product = $productRepository->get($id);

        // ...
    }
}
```

How do you feel about it?


### Injection Inception Problem

CLI apps authors often struggle with the question: *When should be the container created*?

- In a bin file?
- In a `Command`?
- And how to get any service container outside the `Command` scope?
- How to share services between 2 `Command`s?
- How to avoid creating container in every single `Command`?

And how to create container **when user provides config with services via `--config` option**?
The complexity of this question usually leads to choice 2 or 1.

I won't get into more details now, since I'll write about possible solutions in following posts.

<img src="/assets/images/posts/2018/cli-app-di/inject-inception.jpg" class="img-thumbnail">

This application cycle has these steps:

- call bin file
- create `Application` with `new`
- add commands with `$application->add(new SomeCommand)`
- run `Application`
- in called command, there are 2 approaches
    - 1. create a container
        - load it with few services
        - use these services in the scope of this command
    - 2. create other classes with `new`
        - sometimes add them to the container, so they can be used later
        - sometimes add use them in scope and re-create them again when needed

Compare it to a web application:

- call `www/index.php` file
- create dependency injection container
- get `Application` from it
- run it with the current request
- invoke controller and all other needed services in the scope of this controller

## 3. Symfony\Console meets Symfony\DependencyInjection

Why not inspire by web apps, where Controllers are lazy and dependency injection is the first-class citizen?
Moreover, Symfony 3.4 allows [Lazy Commands](https://symfony.com/blog/new-in-symfony-3-4-lazy-commands), that make application cycle more and more similar to web apps. Be careful - **there are few WTFs during migration to Lazy Commands**, [as Shopsys describes](https://blog.shopsys.com/5-5-steps-to-migrate-from-symfony-2-8-lts-to-symfony-3-4-lts-in-real-prs-50c98eb0e9f6).

```php
# bin/rector

// ...

$container = $kernel->getContainer();

$application = $container->get(Application::class);
$application->run();
```

### ❌ Disadvantages

- You need to rethink the [static `new` service approach](/blog/2018/04/26/how-i-got-into-static-trap-and-made-fool-of-myself/), if you're used to it.

### <em class="fas fa-fw fa-lg fa-check text-success"></em> Advantages

- Web apps = CLI apps, nothing extra to learn for new contributors, even though they contribute a CLI app for their first time.
- You can use all [Symfony 3.3+ super cool features](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/).
- It's much easier to scale architecture than with non-container apps.
- You can drop a lot of boilerplate code that sticks code together and simulates container, like singletons, `static::$vars` inside classes etc.
- You can avoid bugs caused by unexpected behavior - like object, that looks like services but is created in 2 different places and holds different variables.

## How To Migrate from 1 to 3?

I wish there was Rector for that like there is [for Doctrine Repositories as Services](/blog/2018/04/02/rectify-turn-repositories-to-services-in-symfony/), but it is a too complex task at the moment. Maybe one day.

In the meantime you can use few guides:

- [`ForbiddenStaticFunctionSniff`](https://github.com/symplify/coding-standard#use-services-and-constructor-injection-over-static-method)
- [`NoClassInstantiationSniff`](https://github.com/symplify/coding-standard#use-service-and-constructor-injection-rather-than-instantiation-with-new)
- [Stackoverflow: How to access the service in a custom console command?
](https://stackoverflow.com/questions/19321760/symfony2-how-to-access-the-service-in-a-custom-console-command/46007150#46007150)
- [5,5 Steps to Migrate from Symfony 2.8 LTS to Symfony 3.4 LTS in Real PRs](https://blog.shopsys.com/5-5-steps-to-migrate-from-symfony-2-8-lts-to-symfony-3-4-lts-in-real-prs-50c98eb0e9f6)

<br>

That's what works for me in CLI apps I've been working on. Look for yourself to get real code inspiration:

 - [Rector](https://github.com/rectorphp/rector),
 - [ApiGen](https://github.com/apigen/apigen),
 - and [EasyCodingStandard](https://github.com/symplify/easy-coding-standard).

<br>

**Which approach do you find the best in your own practice for the long-term code?**

<br><br>

Happy injecting!
