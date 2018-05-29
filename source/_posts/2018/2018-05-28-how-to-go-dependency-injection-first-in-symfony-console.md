---
id: 109
title: "Build Your First Symfony Console Application with Dependency Injection Under 4 Files"
perex: |
    Series about PHP Cli Apps continues with 3rd part about writing Symfony Console Application with Depenendency Injection in the first place. Not last, not second, **but the first**.
    <br>
    Luckily, is easy to start using it and very difficult to
tweet: "New Post on My Blog: ..."
tested: true
test_slug: ConsoleDI
---

## Symfony Evolution

[7 years ago it was total nightmare to use Controllers as services](http://richardmiller.co.uk/2011/04/15/symfony2-controller-as-service/). Luckily, Symfony evolved a lot in this matter and using Symfony 4.0 packages in brand new application is much more simpler than it was in Symfony 2.8 or even 3.2. The very same evolutoin allowed to enter Dependency Injection to Symfony Console-based PHP Cli App.

### Commands as Services

I already wrote about [why is this important](/blog/2018/05/07/why-you-should-combine-symfony-console-and-dependency-injection/#3-symfony-console-meets-symfony-dependencyinjection), today we look on **how to actually do it**. To be clear, how to do it without need of bloated FrameworkBundle, that is official but [rather bad-practise solution](https://matthiasnoback.nl/2013/10/symfony2-console-commands-as-services-why/).

## 3 Steps to First Command as a Service

We need 3 elements:
 
- `service.yml` file with PSR-4 autodiscovery, 
- classic Kernel
- and the bin file - entry point to our application.

The simplest things first.

### 1. `services.yml`

Create `config/services.yml` with classic [PSR-4 autodiscovery/autowire setup](https://github.com/symfony/symfony/pull/21289#issue-101559374) and register `Symfony\Component\Console\Application` as well. We will use this class later.

```yml
# config/services.yml
services:
    _defaults:
        autowire: true

    App\:
        resource: '../app'
        
    Symfony\Component\Console\Application:
        # why public? so we can get it from container in bin file via "$container->get(Application::class)"
        public: true
```

### 2. Kernel

The basic stone of all Symfony Applications. Nothing extra here, we just load the `config/services.yml` from the previous step:

```php
<?php

# app/Kernel.php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AppKernel extends Kernel
{
    /**
     * In more complex app, add bundles here
     */
    public function registerBundles(): array
    {
        return [];
    }

    /**
     * Load all services
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
    }
}
```

There is one more thing. We'll

### 3. The bin file

Last but not least the entry point to our application - `bin/some-app`. That's basically twin-brother of [`public/index.php`](https://github.com/symfony/demo/blob/beb3aa8e988527f16ac50f792eede240fafbfdfc/public/index.php#L35-L39), just for CLI Apps.

```php
# bin/some-app

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$kernel = new AppKernel;
$kernel->boot();

$container = $kernel->getContainer();
$application = $container->get(Application::class)
$application->run();
```

So let's say we have a `App\Command\SomeCommand` with `some` name and we want to run it:

```bash
bin/some-app some
```

But we get:

```bash
Command "some" is not defined.
```

Why? We're sure that:
 
- the `App\Command\SomeCommand` class exists
- it's located in `app/Command/SomeCommand.php` file
- the `config/services.yml` loads it
- `composer.json` section `autoload` is correctly configured
- composer was dumped with `composer dump`... 

What are we missing? Oh, we forgot to **load commands** to the `Application` sdervice.

### How to All Service of Type A to Services of Type B

With FrameworkBundle, we could `autoconfigure` option in config that works with tags, but here we need to use clean PHP.
[Tags magic that is often overused in wrong places](/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/), so this extra works is actually a good thing. 

This is the place to use [famous collector pattern](/blog/2018/03/08/why-is-collector-pattern-so-awesome/#drop-that-expression-language-magic) via `CompilerPass`:

```php
# app/DependencyInjection/CompilerPass/CollectCommandsToApplicationCompilerPass.php

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CollectCommandsToApplicationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $applicationDefinition = $containerBuilder->getDefinition(Application::class);

        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            if (is_a($definition->getClass(), Command::class, true)) {
                $applicationDefinition->addMethodCall('add', [new Reference($name)]);
            }
        }
    }
}
```

And make our `Kernel` aware of it:

```php
# app/Kernel.php

// ...

use App\DependencyInjection\CompilerPass\CollectCommandsToApplicationCompilerPass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

// ...

{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CommandsToApplicationCompilerPass);
    }

    // ...
}
```

This will compile to container to something like this:

```php
function createSomeCommand()
{   
    return new SomeCommand();
}

function createApplication()
{
    $application = new Application;
    $application->add(createSomeCommand());

    return $application;
}
```

Now let's try it again:

```bash
bin/some-app some
```

It works! And that's it. I told you it'll be easy - how can we not love Symfony :).

**Do you still struggle with some parts?** Don't worry, this post is tested by PHPUnit, so you can find all the code mentioned here - just click on "Tested" in the top of the post to see it.

<br><br>

Happy coding!
