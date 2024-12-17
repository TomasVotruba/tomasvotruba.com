---
id: 421
title: "Off the Beaten Path to Upgrade Symfony 2.8 to 7.2"
perex: |
    There are two types of upgrades. One follows only `UPGRADE.md` files on every release, replacing what has been removed with new alternatives. It works, and we could say that the codebase will be "up-to-date."

    The other upgrade doesn't stop at the required minimum but **makes use of all modern features the framework provides**. It will be faster, easier to understand, and easier to upgrade to the next version. I [wrote a post](/blog/two-kinds-of-legacy-code-upgrade) that explains why the latter is better.

    There are no sources about Symfony upgrades spanning multiple major versions—time to fix that.
---

This was a reply to [question on Reddit](https://www.reddit.com/r/PHP/comments/1hfjn89/good_strategy_when_upgrading_php_symfony_apps/) that grew into a post. Today, we look at the less-spoken steps that bring the real value of modern Symfony. If you do them, a new Symfony developer who joins your team will have a good time onboarding and working with your code.

## Happy Path

For the happy path upgrade:

* see [Rector Symfony upgrade sets](https://getrector.com/find-rule?activeRectorSetGroup=symfony)
* See `UPGRADE.md` files in the Symfony Github repository

<br>

Here is a complete list of `UPGRADE.md` files: [2.8](https://github.com/symfony/symfony/blob/2.8/UPGRADE-2.8.md), [3.0](https://github.com/symfony/symfony/blob/3.0/UPGRADE-3.0.md),
[3.1](https://github.com/symfony/symfony/blob/3.1/UPGRADE-3.1.md),
[3.2](https://github.com/symfony/symfony/blob/3.2/UPGRADE-3.2.md),
[3.3](https://github.com/symfony/symfony/blob/3.3/UPGRADE-3.3.md),
[3.4](https://github.com/symfony/symfony/blob/3.4/UPGRADE-3.4.md),
[4.0](https://github.com/symfony/symfony/blob/4.0/UPGRADE-4.0.md),
[4.1](https://github.com/symfony/symfony/blob/4.1/UPGRADE-4.1.md),
[4.2](https://github.com/symfony/symfony/blob/4.2/UPGRADE-4.2.md),
[4.3](https://github.com/symfony/symfony/blob/4.3/UPGRADE-4.3.md),
[4.4](https://github.com/symfony/symfony/blob/4.4/UPGRADE-4.4.md),
[5.0](https://github.com/symfony/symfony/blob/5.0/UPGRADE-5.0.md),
[5.1](https://github.com/symfony/symfony/blob/5.1/UPGRADE-5.1.md),
[5.2](https://github.com/symfony/symfony/blob/5.2/UPGRADE-5.2.md),
[5.3](https://github.com/symfony/symfony/blob/5.3/UPGRADE-5.3.md),
[5.4](https://github.com/symfony/symfony/blob/5.4/UPGRADE-5.4.md),
[6.0](https://github.com/symfony/symfony/blob/6.0/UPGRADE-6.0.md),
[6.1](https://github.com/symfony/symfony/blob/6.1/UPGRADE-6.1.md),
[6.2](https://github.com/symfony/symfony/blob/6.2/UPGRADE-6.2.md),
[6.3](https://github.com/symfony/symfony/blob/6.3/UPGRADE-6.3.md),
[7.0](https://github.com/symfony/symfony/blob/7.0/UPGRADE-7.0.md),
[7.1](https://github.com/symfony/symfony/blob/7.1/UPGRADE-7.1.md),
[7.2](https://github.com/symfony/symfony/blob/7.2/UPGRADE-7.2.md)

<br>

## Can I skip the first steps when we're on Symfony 5?

If you're using a later version like Symfony 4, 5, or 6, **still check previous steps**. Some projects have Symfony 7 in their `composer.json`, but their syntax and architecture are stuck on Symfony 3 times. Imagine the surprise when they're hiring for a modern Symfony 7 codebase, but then the developers see a code fossil.

## PHP or Symfony first?

Should we upgrade first to PHP 8, then start with Symfony 3 to 4 to 5, or vise versa? This is a legit question and one path might lead you to serious turmoil if PHP bugs (don't always trust composer requirements in the past).

Here is a simple table of minimal PHP versions required by various Symfony versions:

<img src="/assets/images/posts/2024/symfony-upgrade-2.png">

Symfony 4 or 5 was not tested on PHP 8.0. It should work, but I've experienced a few bugs when it crashes on an invalid internal type or return value. Those are unfixable, as we'd have to change PHP itself.

In my experience, it's best to reach the highest Symfony version possible. If our PHP version blocks us from going further, then upgrade the PHP version by a single minor step.

For example, let's say we're upgrading the project from Symfony 4 to 7 and running PHP 7.4. First, we upgrade 4 to 5.0, then to 5.4. Why? Because Symfony 5.4 is the last version** that runs on PHP 7.4. Symfony 6.0 requires us to upgrade PHP to  8.0. Only then do we upgrade PHP 7.4 to 8.0.


## Symfony 3

### 1. Leaner Directory Structure

The main change in Symfony 3 was the directory structure. In short, everything used to be placed in the `/app` and `/Resources` directories. Now, everything is directly in the root directory.

<img src="/assets/images/posts/2024/symfony-upgrade-3.png" class="img-thumbnail">

Give the `/Resources` directory some love, as most of the templates, translations, tests, configs, etc., are nested there. It will make working with the Symfony project easier—not just for you but also for linters, static analyzers, and Rector.

<br>

### 2. From named Services to Constructor Injection

The 2nd important change is moving from a string-named service to and global container...

```php
$someService = $this->get('some_service');
```

...to a typed constructor injection:

```php
/**
 * @var SomeService
 */
private $someService;

public function __construct(SomeService $someService)
{
    $this->someService = $someService;
}
```

This is mainly spread in:

* controllers
* commands
* even subscribers
* anywhere where a container is available

This sole change consumes **~40 % of all upgrade time**, but the value increase is a hundredfold. Constructor injection is one of the best patterns for creating clean and adaptable architecture.

There is a [special Rector set](https://github.com/rectorphp/rector-symfony/blob/main/config/sets/symfony/symfony-constructor-injection.php) to help with the upgrade. Use one rule at a time, refactor your code, send a pull request, merge, and then add the next rule.

<br>

In short, the change is:

* to use constructor injection, the service must be explicitly registered in `services.yml`
* The controller class must extend the `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` class
* all the injected services must be explicitly registered in `services.yml`, too

### 3. From Explicit Service Arguments to Autowire

Also, you can clean your service configs:

```diff
 # services.yml
 services:
+    _defaults:
+        autowire: true

     SomeService:
-        arguments:
-            $someArgument: '@SomeType'
-            $someArgument: '@AnotherType'
```

I wrote a [dedicated post about configs upgrade](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/), so you won't miss any line you can remove.

### 4. Do the Monorepo Split

Is your project still using the following dependency in `composer.json`?

```json
{
    "require": {
        "symfony/symfony": "^3.0"
    }
}
```

Flip this to a monorepo split. Instead, require each package separately:

```json
{
    "require": {
        "symfony/http-kernel": "^3.0",
        "symfony/console": "^3.0",
        "symfony/finder": "^3.0",
        "symfony/dependency-injection": "^3.0",
        "symfony/config": "^3.0",
        "symfony/yaml": "^3.0",
        "symfony/translation": "^3.0",
    }
}
```

This is not just do add us more work and make our `composer.json` bigger. It will ease the upgrade:

* we only require what we need, not the whole Symfony - that can be half what full `symfony/symfony` download
* we can bump the easy packages first, then the harder ones later

```diff
 {
     "require": {
         "symfony/http-kernel": "^3.0",
-        "symfony/console": "^3.0",
+        "symfony/console": "^4.0",
-        "symfony/finder": "^3.0",
+        "symfony/finder": "^4.0",
         "symfony/dependency-injection": "^3.0",
-        "symfony/config": "^3.0",
+        "symfony/config": "^4.0",
-        "symfony/yaml": "^3.0",
+        "symfony/yaml": "^4.0",
-        "symfony/translation": "^3.0",
+        "symfony/translation": "^4.0",
    }
}
```

Packages that are hard to bump and should go the last:

* `symfony/framework-bundle`
* `symfony/dependency-injection`
* `symfony/http-kernel`

<br>

## Symfony 4

You can upgrade to Symfony 4 while handling named services upgrades, but Symfony 4.4 is the last one to allow it. Symfony 5 would crash.

### 5. PSR-4 Autodiscovery

Symfony 3.3 introduced [PSR-4-based service discovery](https://symfony.com/blog/new-in-symfony-3-3-psr-4-based-service-discovery). It was slightly buggy until Symfony 4.0, so I would first upgrade to Symfony 4 before using it.

What is it? Before, we had to register each service manually - one by one:

```php
// services.php
$services = $containerConfigurator->services();

$services->register(App\Repository\ProductRepository::class);
$services->register(App\Repository\CategoryRepository::class);
$services->register(App\Repository\CartRepository::class);
$services->register(App\Repository\OrderRepository::class);
```

Now we can load them all from 1 directory:

```php
$services = $containerConfigurator->services();

$services->load('App\\Repository\\', __DIR__ . '/../src/Repository')
```

If we add a new `*Repository` class, Symfony will automatically pick it up.


### 6. From YAML to PHP configs

You've noticed we're not using the YAML syntax anymore. Why? Symfony 3.4 has added a [PHP fluent syntax](https://symfony.com/blog/new-in-symfony-3-4-php-based-configuration-for-services-and-routes) for configs. Again, we better wait for Symfony 4 to make it reliable.

First, let me give you [10 reasons why PHP beats YAML](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/).

"But we have over 50 configs", you say.

No worries, **there is a [migration tool that automates 99 % of the process](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/)**.

<br>

Last, but not least, I wrote a [dedicated post about the benefits of modern PHP Symfony configs](https://getrector.com/blog/modernize-symfony-configs), how it works perfectly with PHPStan deprecation rules to **get warnings about deprecated config methods**. This is not possible with YAML.

<br>

### 7. Ultimate Config Goal

<blockquote class="blockquote">
"Perfection is achieved, not when there is nothing more to add,<br>
but when there is nothing left to take away."
</blockquote>

When we finish the config migration, service narrowing and remove every possible piece we don't need, what will the result be?

1 config per environment, that is:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
        ->bind('$environment', '%kernel.environment%');

    $services->load('App\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/App/Entity',
            __DIR__ . '/../src/App/Event',
            __DIR__ . '/../src/App/ValueObject',
        ]);
```

<br>

## Symfony 5

### 8. From Annotations to Attributes

Symfony 5.2 added [# [Route] and # [Required] attributes] (https://symfony.com/blog/new-in-symfony-5-2-php-8-attributes). As I've said above, we should first upgrade to Symfony 5.4 while still on PHP 7.4 and then to PHP 8.0.

You can prepare early within the Rector config:

```php
# rector.php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withAttributesSets()
```

The `->withAttributesSets()` method enables all relevant attribute sets in your project. But don't worry; it will only start upgrading once we're on PHP 8.0. Once we're on PHP 8.0, it will only upgrade those attributes that really exist.


<br>

### 9. Security Back and Forth - Skip the Guard

Symfony 3 introduced a new way to handle authentication - Guard. It got [further improved](https://symfony.com/blog/new-in-symfony-3-4-guard-authentication-improvements) and promoted. Then [deprecated in Symfony 5.3](https://symfony.com/blog/new-in-symfony-5-3-guard-component-deprecation) to be replaced with [new authentication system](https://symfony.com/blog/new-in-symfony-5-1-updated-security-system).

This is the most dynamic component in Symfony, changing with every major version. It's like the opposite of the Form component.

I've had the fortune to work with projects that were not coupled with Symfony security. If that was the case, we **slowly decoupled authentication logic from Symfony security to our own**. It made and will make our migration much easier.

Security upgrade depends on each specific project, but it's worth skipping the Security Guard completely. It's a dead end.

<br>

## Symfony 6 and 7

### 10. Attributes Everywhere

When we reach PHP 8.0 and Symfony 6.0, **95 % of the work is already behind us**. Symfony 6 and 7 are stabilizing and relaxing releases. They're mostly about syntax sugar and more attributes. I would not recommend using them all blindly just because it's PHP 8.0 syntax, though.

<br>

But there are a few worth mentioning that bring a value:

* [#[TaggedIterator]](https://symfony.com/blog/new-in-symfony-5-3-service-autowiring-with-attributes)

```php
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class HandlerCollection
{
    /** @var HandlerInterface[] */
    private $handlers;

    public function __construct(
        #[TaggedIterator(HandlerInterface::class)]
        private iterable $handlers
    ) {
    }
}
```

* [#[Autowire]](https://symfony.com/blog/new-in-symfony-6-1-service-autowiring-attributes)

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MyService
{
    public function __construct(
        #[Autowire(env: 'PROJECT_ENVIRONMENT')]
        private $environment,
    ) {}
}
```

They both allow to drop even more config blurbs and make them more leaner.

<br>

This would be the off-the-beaten path to upgrading Symfony 2.8 to 7.2. It's a lot of work, but with the right tools and mindset, it can be done in months. The final result is worth it! Good luck and have fun.

<br>

Happy coding!
