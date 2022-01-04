---
id: 338
title: "Decomposing Symfony Kernel: What does Minimal Symfony Bundle Do"
perex: |
    In previous post, we looked at [When Symfony Http Kernel is a Too Big Hammer to Use](/blog/when-symfony-http-kernel-is-too-big-hammer-to-use). We talked about enormous content this package provides, but we don't really need.
    <br><br>
    Today we'll have a little self-reflecting pause in the middle of the 4-post journey. We'll look at main glue in Symfony Kernel - the bundle. **Can we find a way to decompose it and use it without Kernel?**

tweet: "New Post on the 🐘 blog: Decomposing Symfony Kernel: What does Minimal Symfony Bundle Do"
---

In previous post we described the problem: the Symfony Kernel requires lot of http-related code **that we don't need in command line applications**. We still have to maintain it, require dev dependencies in production and handle their downgrade.

When there was a pandemic hitting our world in 2020, we didn't think about our studies, where would we go for vacation or what car we'll buy. We cared about our family, what do we eat for dinner and where can we buy everyday life essentials. When we find ourselves in time of troubles we try to look at bare essentials.

## What are the Kernel Essentials?

How can we use this approach in "my application kernel is way too big" situation?

<br>

**What do we need from Kernel?**
* To build our dependency injection container from provided PHP config.

<br>

**How can Kernel do that?**
* It takes the main config, few bundles and loads them together.

<br>

**What is the bundle class actually doing?**
* It collects one PHP config, sometimes extension or compiler passes and passes it to kernel to process further.

<br>

**Which do we need from those 3?**
* The config, because it defines service autodiscovery, parameters and sometimes manual arguments.
* The extension mostly refer the config, so we don't need that.
* We need compiler passes, because they decorate service definitions and interact with each other.

<br>

## 1. The Bundle

Let's look at one of Symplify bundles to demonstrate on real code. This is `AstralBundle`, that allows other kernels to register `symplify/astral` service and use them:

```php
namespace Symplify\Astral\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\Astral\DependencyInjection\Extension\AstralExtension;

final class AstralBundle extends Bundle
{
    protected function createContainerExtension(): AstralExtension
    {
        return new AstralExtension();
    }
}
```

### 2. The Extension

The Bundle class contains `createContainerExtension()` that further creates `AstralExtension`. What is that one doing?

```php
namespace Symplify\Astral\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class AstralExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../../config')
        );

        $phpFileLoader->load('config.php');
    }
}
```

<br>

It seems, it loads the `../../config/config.php`. There we define services and parameters.

So we need:
* a `*Bundle` class,
* an `*Extension` class,
* and register the `*Bundle` class in `*Kernel.php` in the `registerBundles()` method.

```php
$fileLoader->load(__DIR__ . '/../../config.php');
```

## Where Else can we Load Configs?

Does this look familiar? There at least 2 places that handle the same operation. Nothing special, they're quite common - you can see both in code of this website.

One of them is `MicroKernelTrait::configureContainer()` method:

```php
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class TomasVotrubaKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureContainer(
        ContainerBuilder $containerBuilder,
        LoaderInterface $loader
    ): void {
        $loader->load(__DIR__ . '/../../vendor/symplify/astral/config/config.php');
    }
}
```

<br>

The other is any `config/config.php` that we already load in our Kernel:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(
        __DIR__ . '/../../vendor/symplify/astral/config/config.php'
    );
};
```

As you can see, we can replace Bundle + Extension classes with single-line import either in Kernel or in `config.php`. That's **2 classes and one method we don't have to worry about anymore**.

<p class="text-success pt-3 pb-3">
    ✅
</p>

<br>

~~Extension~~ is gone and we can import config elsewhere. What about the compiler passes? We can [add them in the Kernel](https://symfony.com/doc/current/service_container/compiler_passes.html) in `build()` method:

```php
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new SomeCompilerPass());
    }
}
```

We've managed to tidy up the Kernel a bit. With less code to work with, we might see the solution more easily... or **have we just moved the same problem with somewhere else**?

We'll see about that next time.

<br>

Happy coding!
