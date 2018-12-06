---
id: 105
title: "How to Load --config With Services in Symfony Console"
perex: |
    PHP CLI apps usually accept config, to setup their behavior. For PHPUnit it's `phpunit.xml`, for PHP CS Fixer it's `.php_cs`, for EasyCodingStandard it's `ecs.yml`, for PHPStan it's `phpstan.neon` and so on.
    <br><br>
    In the first post about PHP CLI Apps I wrote about [poor DI support in PHP CLI projects](/blog/2018/05/07/why-you-should-combine-symfony-console-and-dependency-injection/).
    <br><br>
    Today we look on the first barrier that leads most people to prefer static over DI - **how to load config with services**.
tweet: "New Post on My Blog: How to Load --config With Services in #Symfony Console #di #config #egg #chicken"
tweet_image: "/assets/images/posts/2018/config-di-console/chicken-egg.jpg"
---

```php
vendor/bin/phpstan --configuration phpstan.neon
vendor/bin/ecs --config ecs.yml
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

- Get a user-provided config from CLI (e.g. `--config ecs.yml`)
- Use `Symfony\Component\Console\Application` to get this value
- Create a DI container with this config
- Get `Application` service from the container
- Invoke the `Application` with provided config
- Get a user-provided config from CLI (e.g. `--config ecs.yml`)
- ...

Very nice recursion, isn't it?

## Why this Problem Even Exists?

To get the main config in PHP App is easy. Symfony has [a common path in `Kernel`](https://github.com/symfony/demo/blob/v1.0.0/app/AppKernel.php#L59), Nette [in Configurator](https://github.com/nette/sandbox/blob/b3bd786d71bdecec441121cafc63086e58355130/app/bootstrap.php#L18) and other frameworks likewise.

It's usually **absolute path defined in PHP code**, usually `app/config/config.yml` or `app/config/config.neon`. It doesn't change and every developer knows that. If we put the file to `app/config.yml`, it won't be loaded. PHP Apps are nice and clear in this matter.

### PHP CLI Apps are Free

Users can configure the path to the main config, they can have multiple configs, `.dist` configs, config located in the root or nested in `/config` directory, it can be named `my-own-super-cool-config.yml` and so on.

Legacy bound architecture design or static code is a price for the freedom we have to pay here. **So can we pay less?**

### 3 Possible "Solutions"

Imagine we call EasyCodingStandard with following `--config`:

```bash
vendor/bin/ecs --config some-config.yml
```

## 1. The Mainstream: No Container

Use static approach, no services config, just list of items. Most spread solution so far.

### How if Fits?

<em class="fas fa-fw fa-lg fa-check text-success"></em> Ready in 2 minutes

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Well, static

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

<em class="fas fa-fw fa-lg fa-check text-success"></em> Ready in 10 minutes

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Only local scope, we need to re-create container everywhere we need it

<em class="fas fa-fw fa-lg fa-times text-danger"></em> The Chicken vs. Egg problem still remains very clear

## 3. Kill the Egg: The bin File Tuning

When I worked on the first version of [nette/coding-standard](https://github.com/nette/coding-standard)
almost a year ago, David came with question: "how to use ECS with 2 different configs - one for PHP 5.6 and one for PHP 7.0"?

```php
vendor/bin/ecs check src --config vendor/nette/cofing-standard/php56.yml
vendor/bin/ecs check src --config vendor/nette/cofing-standard/php70.yml
```

I had no idea. So I created [this issue at Symplify](https://github.com/Symplify/Symplify/issues/192) and praised the open-source Gods, because current version of `bin/ecs` was as simple as:

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

<a href="https://github.com/Symplify/Symplify/pull/198" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
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
bin/ecs check src --config custom-config.yml
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

So far so good.

<br>

Later, a `ShowCommand` was added:

```bash
bin/ecs show --config custom-config.yml
```

And we see our old friend again:

<img src="/assets/images/posts/2018/config-di-console/ups.png" class="img-thumbnail">

What now? Add the `config` option to the `ShowCommand` definition?

```diff
 final class ShowCommand extends Command
 {
     // ...

     protected function configure(): void
     {
         $this->setName('show');
         // ...
+        $this->addOption('config', null, InputOption::VALUE_REQUIRED, 'Config file.');
     }
 }
```

Wait a bit... Can you smell it?

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Duplicated code

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Not-lazy

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Creates **memory lock** to 1 command → 1 extra operation. If we forget this, it will break.

<br>

What else can we do to **solve this once and for all** and release our memory to work on more important things?

### Common Options to all Commands

Let me think... I've seen something like this before:

```bash
vendor/bin/ecs check /src -v
vendor/bin/ecs show -v
```

Where is this `-v` option from and how does it work in every command even though we don't define it there?

After a bit of in-PHPStorm-Googling I found [`this`](https://github.com/symfony/symfony/blob/784a7accf55dbca0f2363457a066d6bcf03d065f/src/Symfony/Component/Console/Application.php#L943-L956):

```php
namespace Symfony\Component\Console;

class Application
{
    // ...

    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message'),
            new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Do not output any message'),
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version'),
            new InputOption('--ansi', '', InputOption::VALUE_NONE, 'Force ANSI output'),
            new InputOption('--no-ansi', '', InputOption::VALUE_NONE, 'Disable ANSI output'),
            new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question'),
        ));
    }
}
```

So maybe we could extend this class and our `--config` option:

```php
namespace Symplify\EasyCodingStandard\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputOption;

final class Application extends SymfonyApplication
{
    protected function getDefaultInputDefinition()
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "--config" option
        $inputDefinition->addOption(new InputOption('config', null, InputOption::VALUE_REQUIRED, 'Config file.'));

        return $inputDefinition;
    }
}
```

(Btw, [`addOption()`](https://github.com/symfony/symfony/blob/784a7accf55dbca0f2363457a066d6bcf03d065f/src/Symfony/Component/Console/Command/Command.php#L386-L391) is just a helper method for `new InputOption()`.)

And use our `Application` in `bin/rector`:

```diff
 // ...

-$application = $container->get(Symfony\Component\Console\Application::class);
+$application = $container->get(Symplify\EasyCodingStandard\Console\Application::class);
 $application->run();
```

Don't foget to update `services.yml`:

```diff
 services:
     _defaults:
         autowire: true

-    Symfony\Component\Console\Application:
+    Symplify\ChangelogLinker\Console\Application:
         public: true # for bin file
```

And let's try this again:

```bash
bin/ecs check src --config custom-config.yml
```

<img src="/assets/images/posts/2018/config-di-console/yes.png" class="img-thumbnail">

### How if Fits?

<em class="fas fa-fw fa-lg fa-times text-danger"></em> Ready in 15 minutes

<em class="fas fa-fw fa-lg fa-check text-success"></em> Setup & Forget

<em class="fas fa-fw fa-lg fa-check text-success"></em> Much more legacy-proof

<em class="fas fa-fw fa-lg fa-check text-success"></em> We can now use Dependency Injection **everywhere we need**

<em class="fas fa-fw fa-lg fa-check text-success"></em> And make use off Symfony, Nette or any other container features we're used to from Web Apps.

<br>

## Where to Go Next?

Do you have a CLI App and do you find DI approach useful? Do you have `--config` or `-c` or `--configuratoin` options and do you want to migrate them to this? Or maybe you have `--level` option, that shortens the config path in some way:

```php
vendor/bin/ecs check src --level clean-code
```

Yes? Then go check:

- [`bin/container.php`](https://github.com/Symplify/EasyCodingStandard/blob/master/bin/container.php) - a file that handles container creation

- [`Symplify\PackageBuilder\Configuration\ConfigFileFinder`](https://github.com/symplify/packagebuilder#4-load-a-config-for-cli-application)

- [`Symplify\PackageBuilder\Configuration\LevelFileFinder`](https://github.com/symplify/packagebuilder#6-load-config-via---level-option-in-your-console-application)

...and stay tuned for next post about CLI Apps in PHP.

<br><br>

Happy CLInjecting!
