---
id: 169
title: "Introducing Symfony Flex Loader"
perex: |
    Symfony 4 and Flex is heading in direction of zero-setup configuration - no bundles, no extensions, no configuration. You already know how to get rid of [Configuration](/blog/2018/11/29/how-to-manage-configuration-in-symfony-without-bundle-extension-and-configuraiton/). Flex now loads services instead of Extension class.
    <br>
    <br>
     But it has an extra price, a lot of new boilerplate code in Kernel. Today you'll learn **how to keep your Kernel Flex-ready and clean at the same time**.
tweet: "Learn new hack on my  🐘 #php blog: Introducing #Symfony Flex Loader"
tweet_image: "/assets/images/posts/2018/flex-loader/flex-loader.gif"
---

Can you spot, what extra directory is this Kernel loading...

```php
<?php declare(strict_types=1);

// ...

final class AppKernel extends Kernel
{
    // ...

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment.self::CONFIG_EXTS, 'glob');
        $loader->load(__DIR__ . '/optional/custom/path' . self::CONFIG_EXTS, 'glob');
    }

    // ...
}
```

...in 2 seconds?

If you found `__DIR__ . '/optional/custom/path'`, good job!

<br>

What directory does *this* Kernel load?

```php
<?php declare(strict_types=1);

// ...

final class AppKernel extends Kernel
{
    // ...

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $this->flexLoader->loadConfigs($container, $loader, [
            __DIR__ . '/another/custom/path'
        ]);

        $container->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    // ...
}
```

Do you prefer to read about extra compiler passes that modify your application or all the directories and suffixes your configs might be loaded from?

Programmers are lazy and they ** don't want to read any letter more then they have to**. Unless they're paid by for each read letter :).

## Make Kernel Small Again

Symplify is introducing a small handy package - [FlexLoader](https://github.com/symplify/flexloader). It handles **Flex service and route loading with 3 lines** (methods) and makes your Kernel to be nice and readable - like they used to the old times.

<br>

How does FlexLoader look in practice?

<img class="gifplayer" src="/assets/images/posts/2018/flex-loader/flex-loader.png" data-gif="/assets/images/posts/2018/flex-loader/flex-loader.gif" data-playon="click">

<br>

**That's all!** Now you can focus on important things, like writing [cool compiler passes](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette/).

<br>
<br>

Happy weight loss, so you're fit and slim for Christmas :)
