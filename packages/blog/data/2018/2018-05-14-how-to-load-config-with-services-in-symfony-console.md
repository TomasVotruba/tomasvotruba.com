---
id: 105
title: "How to Load --config With Services in Symfony Console"
perex: |
    PHP CLI apps usually accept config, to setup their behavior. For PHPUnit it's `phpunit.xml`, for PHP CS Fixer it's `.php_cs`, for ECS it's `ecs.php`, for PHPStan it's `phpstan.neon` and so on.
    <br><br>
    In the first post about PHP CLI Apps I wrote about [poor DI support in PHP CLI projects](/blog/2018/05/07/why-you-should-combine-symfony-console-and-dependency-injection/).
    <br><br>
    Today we look on the first barrier that leads most people to prefer static over DI - **how to load config with services**.

tweet: "New Post on My Blog: How to Load --config With Services in #Symfony Console #di #config #egg #chicken"
tweet_image: "/assets/images/posts/2018/config-di-console/chicken-egg.jpg"

updated_since: "November 2020"
updated_message: |
    Updated config loading approach.
    Switched deprecated `--set` option to `esc.php` config.
    Switched **YAML** to **PHP** configuration.
---

```bash
vendor/bin/phpstan --configuration phpstan.neon
vendor/bin/ecs --config ecs.php
```

Can you spot the difference? Same CLI input, but:

- first has only [in-command container build for few classes](/blog/2018/05/07/why-you-should-combine-symfony-console-and-dependency-injection/#2-container-inceptions),

- second is [Application with full DI support](/blog/2018/05/07/why-you-should-combine-symfony-console-and-dependency-injection/#3-symfony-console-meets-symfony-dependencyinjection), like a Symfony App.

Today you'll learn **how** to get from first to second, knowing **why** and all the pros and cons.

## Who Comes First?

<img src="/assets/images/posts/2018/config-di-console/chicken-egg.jpg" class="img-thumbnail">

This addresses a problem (or rather mind-exercise) of [injection inception](/blog/2018/05/07/why-you-should-combine-symfony-console-and-dependency-injection/#injection-inception-problem) aka *chicken vs. egg*. Because this might be a little bit confusing, I try to describe it in 3 different forms:

### A. In Chicken vs. Egg Form

We need *an egg*, so we can create *a chicken*. We can get *an egg* thanks to *a chicken*.  With *this egg*, we can create *a chicken*. Then we need to get a chicken to "cluck".

### B. In Container vs. Config Form

We need a config to create a container. We can get a config thanks to `Symfony\Component\Console\Application`. With this value, we can create a container. Then we need to get a `Symfony\Component\Console\Application` service and call `run()` method on it.

### C. In Implementation Form

Are you lost? That's all right. Let's see it in a list:

- Get a user-provided config from CLI (e.g. `--config ecs.php`)
- Use `Symfony\Component\Console\Application` to get this value
- Create a DI container with this config
- Get `Application` service from the container
- Invoke the `Application` with provided config
- Get a user-provided config from CLI (e.g. `--config ecs.php`)
- ...

Very nice recursion, isn't it?

## Why this Problem Even Exists?

To get the main config in PHP App is easy. Symfony has [a common path in `Kernel`](https://github.com/symfony/demo/blob/v1.0.0/app/AppKernel.php#L59), Nette [in Configurator](https://github.com/nette/sandbox/blob/b3bd786d71bdecec441121cafc63086e58355130/app/bootstrap.php#L18) and other frameworks likewise.

It's usually **absolute path defined in PHP code**, usually `app/config/config.php` or `app/config/config.neon`. It doesn't change and every developer knows that. If we put the file to `app/config.php`, it won't be loaded. PHP Apps are nice and clear in this matter.

### PHP CLI Apps are Free

Users can configure the path to the main config, they can have multiple configs, `.dist` configs, config located in the root or nested in `/config` directory, it can be named `my-own-super-cool-config.php` and so on.

Legacy bound architecture design or static code is a price for the freedom we have to pay here. **So can we pay less?**

### 3 Possible "Solutions"

Imagine we call EasyCodingStandard with following `--config`:

```bash
vendor/bin/ecs --config some-config.php
```

## 1. The Mainstream: No Container

Use static approach, no services config, just list of items. Most spread solution so far.

### How if Fits?

✅ Ready in 2 minutes

❌ Well, static

## 2. DI for Poor People: Container in a Command

I'm used to container [thanks to great work of David Grudl](/blog/2017/06/01/thank-you-david/) and many posts [he wrote about dependency injection](https://phpfashion.com/co-je-dependency-injection), so this one is very counter-intuitive to me, but I still see it quite often in the wild.

**The easiest way to start** using Container in a static application is to **create it at the class we need it:**

```php
use Symfony\Component\Console\Command\Command;

class SomeCommand extends Command
{
    // ...

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addConfig($input->getOption('config'));
        $container = $containerBuilder->build();

        $someService = $container->get(SomeService::class);
        // ...
    }
}
```

### How if Fits?

✅ Ready in 10 minutes

❌ Only local scope, we need to re-create container everywhere we need it

❌ The Chicken vs. Egg problem still remains very clear

## 3. Kill the Egg: The bin File Tuning

When I worked on the first version of [nette/coding-standard](https://github.com/nette/coding-standard)
almost a year ago, David came with question: "how to use ECS with 2 different configs - one for PHP 5.6 and one for PHP 7.0"?

```bash
vendor/bin/ecs check src --config vendor/nette/cofing-standard/php56.php
vendor/bin/ecs check src --config vendor/nette/cofing-standard/php70.php
```

I had no idea. So I created [this issue at Symplify](https://github.com/symplify/symplify/issues/192) and praised the open-source Gods, because current version of `bin/ecs` was as simple as:

```php
# bin/ecs
require_once __DIR__ . '/../vendor/autoload.php';

$container = (new ContainerFactory)->create();

// ...

$application = $container->get(Symfony\Component\Console\Application::class);
$application->run();
```

<br>

So what now?

### `ArgvInput` to the Rescue

<a href="https://github.com/symplify/symplify/pull/198" class="btn btn-dark btn-sm">
    See pull-request #198
</a>

Do you know `ArgvInput` class? It's a Symfony\Console input helper around native PHP `$_SERVER['argv']`, that holds all the `arguments --options 1` passed via CLI.

Let's use it:

```php
$config = null;
$argvInput = new Symfony\Component\Console\Input\ArgvInput;
if ($argvInput->hasParameterOption('--config')) {
    $config = $argvInput->getParameterOption('--config');
}

if ($config) {
    $container = (new ContainerFactory)->createWithConfig($config);
} else {
    $container = (new ContainerFactory)->create();
}

$application = $container->get(Symfony\Component\Console\Application::class);
$application->run();
```

Run it:

```bash
bin/ecs check src --config custom-config.php
```

And see how all nicely works on the 1st run:

<img src="/assets/images/posts/2018/config-di-console/ups.png" class="img-thumbnail">

Or not. Oh, it looks like we need to add `config` option to the `CheckCommand` definition:

```diff
 final class CheckCommand extends Command
 {
     // ...

     protected function configure(): void
     {
         $this->setName('check');
         // ...
+        $this->addOption('config', null, InputOption::VALUE_REQUIRED, 'Config file.');
     }
 }
```

```bash
bin/ecs check src --config custom-config.php
```

<img src="/assets/images/posts/2018/config-di-console/yes.png" class="img-thumbnail">

### How if Fits?

❌ Ready in 15 minutes

✅ Setup & Forget

✅ Much more legacy-proof

✅ We can now use Dependency Injection **everywhere we need**

✅ And make use off Symfony, Nette or any other container features we're used to from Web Apps.

<br>

## Where to Go Next?

Do you have a CLI App and do you find DI approach useful? Do you have `--config` or `-c` or `--configuration` options and do you want to migrate them to this?

Then go [symplify/set-config-resolver](https://github.com/symplify/set-config-resolver). **Special package dedicated to this problem.**

<br>

Happy coding!
