---
id: 271
title: "How to Switch from YAML/XML Configs to PHP Today with Symplify"
perex: |
    In [previous post](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/), we looked 10 reasons to switch from YAML to PHP configs. Still asking *why*? I dare you to [disagree with 1 reason there](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/).


    If you have 1 config file, you already are on PHP side now. Close this post and enjoy life.


    But what if you have 10 or even 100 YAML/XML configs? Are you doing to close down for a weekend to switch your code base?

    Or maybe... **5 minute job**?

tweet_image: "/assets/images/posts/2020/yaml_to_php.png"

updated_since: "June 2021"
updated_message: |
    Updated Rector YAML to PHP configuration, as current standard. With automated input types.
---

## Break-Even Automation

With legacy migrations, we have to handle a lot of processes that are automated. If you work on 1 project for many years, it's a matter of habit to handle it manually. But **handling 3 projects a month makes you think, how to automated any repeated work**. It's cheaper, faster, and more reliable. The same way the writing tests are.

<br>

We made [symplify/config-transformer](https://github.com/symplify/config-transformer) to handle this work for us.

<img src="/assets/images/posts/2020/yaml_to_php.png" class="img-thumbnail">


<br>

**Credit** for inspiration first reported bugs, feature feedback, and exceptional test cases **goes to *archeoprog* and [Ryan Weaver](https://github.com/weaverryan)**. Their input helped get the Symplify package to high quality and covered Symfony features I didn't even know. Thank you, guys!

<br>

## 1. Install symplify/config-transformer

```bash
composer require symplify/config-transformer --dev
```

## 2. Run `switch-format` Command

This command takes 1 argument - paths to files or directories to convert:

```bash
vendor/bin/config-transformer switch-format app/config
```

<br>

This is how my **real workflow** looks like: from low hanging fruit of 1 file to the main config, to all packages.
Each followed by a separated commit, so it's easier to review and fix in case of regression.

```bash
vendor/bin/config-transformer switch-format ecs.yaml
# commit

vendor/bin/config-transformer switch-format rector.yaml
# commit

vendor/bin/config-transformer switch-format app/packages
# commit

vendor/bin/config-transformer switch-format app/config/config.yaml
# commit
```

## 3. Upgrade paths to PHP in Extensions and Kernel

The config format switch is one part; the next is to update loaders in PHP code.
Again, it's valid to handle it manually with search & replace in PHPStorm.

```diff
 use Symfony\Component\Config\FileLocator;
 use Symfony\Component\DependencyInjection\ContainerBuilder;
-use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
+use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
 use Symfony\Component\HttpKernel\DependencyInjection\Extension;

 final class SomeExtension extends Extension
 {
     public function load(array $configs, ContainerBuilder $container)
     {
-        $loader = new YamlFileLoader($container, new FileLocator());
+        $loader = new PhpFileLoader($container, new FileLocator());
-        $loader->load(__DIR__ . '/../Resources/config/controller.yaml');
+        $loader->load(__DIR__ . '/../Resources/config/controller.php');
-        $loader->load(__DIR__ . '/../Resources/config/events.yaml');
+        $loader->load(__DIR__ . '/../Resources/config/events.php');
     }
 }
```

But in case your code is not standard and can't be bothered with correct regular expressions, Rector got you covered:

```bash
composer require rector/rector --dev
```

Setup `rector.php`:

```php
use Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ChangeFileLoaderInExtensionAndKernelRector::class, [
        ChangeFileLoaderInExtensionAndKernelRector::FROM => 'yaml',
        ChangeFileLoaderInExtensionAndKernelRector::TO => 'php',
    ]);
};
```

And let Rector handle the boring work:

```bash
vendor/bin/rector process app src
```

<br>

That's it! One little tool for you, one big leap for a PHP programmer-kind.

## Supported Features

- imports
- services
- parameters
- autodiscovery
- instance of
- extensions, e.g. `framework`, `doctrine` or `twig`
- routing

## Let Us Know, Help You Grow

**Is something broken? Have you found a space for improvement?**

Create [an issue](https://github.com/symplify/symplify/issues/new) and let us know. We'd love to hear it.

<br>

Happy coding!
