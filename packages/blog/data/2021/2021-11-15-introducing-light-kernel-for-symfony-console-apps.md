---
id: 347
title: "Introducing Light Kernel for Symfony&nbsp;Console&nbsp;Apps"
perex: |
    In the first post of this miniseries, we look at Symfony Http Kernel with a critical eye on [how it causes project overweight](/blog/when-symfony-http-kernel-is-too-big-hammer-to-use).


    In the second post, we [looked at bundles](/blog/decomposing-symfony-kernel-what-does-minimal-symfony-bundle-do) from a very raw point of view - what do we need from them?


    In the spirit of [thesis, antithesis, and synthesis](https://link.springer.com/referenceworkentry/10.1007%2F978-1-4020-8265-8_200183) philosophy, today, we'll combine both parts. We'll look for a solution to the original question: **How can we build Kernel in Console Application without the Http burden?**

---

<div class="card border-warning mt-4">
    <div class="card-header text-black bg-warning shadow">
        <strong>Proof over theory?</strong>
        ECS, Monorepo Builder, EasyCI, Config Transformer and Rector are using this method since 1st November 2021. ECS is now <a href="https://github.com/symplify/easy-coding-standard/commit/278d4d52958c1ca01c21219cb6e14ca4493914ad">40 000 lines lighter</a>, while keeping all the features running.
        <img src="/assets/images/posts/2021/ecs_light_commit.png" class="img-thumbnail mt-4">
    </div>
</div>


<blockquote class="blockquote">
    "Perfection is achieved, not when there is nothing more to add,<br>
    but when there is nothing left to take away."
    <footer class="blockquote-footer text-right">Antoine de Saint-Exupery</footer>
</blockquote>

## Striving for Simplicity

In previous posts, we defined requirements that we want from Symfony Kernel from Console Applications:

* build dependency injection container
* use only `symfony/console` and `symfony/dependency-injection` packages
* use as simple API as possible, ideally Kernel with 1 method that loads configs
* avoid the *http* part of Symfony Kernel, as we don't use it in CLI

## What do we Have?

Currently, we have old projects that use `symfony/http-kernel` with a bunch of bundles. But when we look closer at Symfony bundles, we'll see [they only add configs and compiler passes](/blog/decomposing-symfony-kernel-what-does-minimal-symfony-bundle-do). So we can drop bundles and extensions altogether.

## What do we Want?

Drop dependency on `symfony/http-kernel`, but make the project work as before.

<img src="/assets/images/posts/2021/light_remove_http_kernel.png" class="img-thumbnail" style="max-width: 25em">

<br>

In an ideal world, we want a container factory class that loads provided configs:

```php
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContainerBuilderFactory
{
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(
        array $configFiles,
        array $compilerPasses,
        array $extensions
    ): ContainerBuilder {
        $containerBuilder = new ContainerBuilder();

        foreach ($extensions as $extension) {
            $containerBuilder->registerExtension($extension);
        }

        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }

        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }

        return $containerBuilder;
    }
}
```

<br>

Then we could use it directly in any command-line application Kernel:

```php
use Psr\Container\ContainerInterface;
use Symplify\SymplifyKernel\ContainerBuilderFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;

final class MonorepoBuilderKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        // provide local config here
        $configFiles[] = __DIR__ . '/../../config/config.php';

        // external configs
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;

        $containerBuilderFactory = new ContainerBuilderFactory();

        $containerBuilder = $containerBuilderFactory->create($configFiles, [], []);

        // build the container
        $containerBuilder->compile();

        return $containerBuilder;
    }
}
```

<br>

How would it meet our requirements?

* single method <span class="text-success pt-3 pb-3">✅</span>
* only `symfony/dependency-injection` <span class="text-success pt-3 pb-3">✅</span>
* no http <span class="text-success pt-3 pb-3">✅</span>

## How to use ContainerBuilderFactory in 3 Steps

For a couple of years, the [Symplify](https://github.com/symplify/symplify) uses own Symfony Kernel wrapper package - the `symplify/symplify-kernel`. It abstracts repeated methods and eases testing. What better place we could use for adding a dependency container factory?

<br>

**1. Install Symplify Kernel**

```bash
composer require symplify/symplify-kernel
```

<br>

**2. Extend `AbstractSymplifyKernel` and provide config files**

This the full kernel for the [EasyCI package](/blog/5-commands-from-easy-ci-that-makes-your-ci-stronger/) looks like:

```php
namespace Symplify\EasyCI\Kernel;

use Psr\Container\ContainerInterface;
use Symplify\Astral\ValueObject\AstralConfig;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyCIKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;
        $configFiles[] = AstralConfig::FILE_PATH;

        return $this->create([], [], $configFiles);
    }
}
```

<br>

**3. Boot Kernel in your bin file and enjoy the Symfony DI**

```php
$easyCIKernel = new EasyCIKernel();
$easyCIKernel->createFromConfigs([__DIR__ . '/config/config.php']);

$container = $easyCIKernel->getContainer();

/** @var Application $application */
$application = $container->get(Application::class);
exit($application->run());
```

That's it! In the end, the setup is very simple.

It allowed us to drop the following files from 4 Simplify CLI packages:

<img src="/assets/images/posts/2021/light_kernel_vendor_diff.gif" class="img-thumbnail">

Less code to transfer, faster CI pipelines, and environment-friendly code.

<br>

What would be even better? If Symfony core would provide a similar factory out of the box. Maybe one day, it will.

<br>

Happy coding!
