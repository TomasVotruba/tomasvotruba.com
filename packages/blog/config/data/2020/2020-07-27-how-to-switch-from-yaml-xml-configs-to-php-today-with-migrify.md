---
id: 271
title: "How to Switch from YAML/XML Configs to PHP Today with Migrify"
perex: |
    In [previous post](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/), we looked 10 reasons to switch from YAML to PHP configs. Still asking *why*? I dare you to [disagree with 1 reason there](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/).
    <br><br>
    If you have 1 config file, you already are on PHP side now. Close this post and enjoy life.
    <br><br>
    But what if you have 10 or even 100 YAML/XML configs? Are you doing to close down for a weekend to switch your code base?
    <br>
    <br>
    Or maybe... **5 minute job**?

tweet: "New Post on #php 🐘 blog: How to Switch from YAML/XML Configs to PHP Today with Migrify"
tweet_image: "/assets/images/posts/2020/yaml_to_php.png"

updated_since: "August 2020"
updated_message: |
    Updated Rector YAML to PHP configuration, as current standard.
---

## Break-Even Automation

With legacy migrations, we have to handle a lot of processes that are automated. If you work on 1 project for many years, it's a matter of habit to handle it manually. But **handling 3 projects a month makes you think, how to automated any repeated work**. It's cheaper, faster, and more reliable. The same way the writing tests are.

<br>

We made [migrify/config-transformer](https://github.com/migrify/config-transformer) to handle this work for us.

<img src="/assets/images/posts/2020/yaml_to_php.png" class="img-thumbnail">


<br>

**Credit** for inspiration first reported bugs, feature feedback, and exceptional test cases **goes to [archeoprog](https://github.com/archeoprog) and [Ryan Weaver](https://github.com/weaverryan)**. Their input helped get the migrify package to high quality and covered Symfony features I didn't even know. Thank you, guys!

<br>

## 1. Install migrify/config-transformer

```bash
composer require migrify/config-transformer --dev
```

## 2. Run `switch-format` Command

This command has 2 requires options and 1 argument:

- `--input-format` - the format of the config you want to convert
- `--output-format` - desired output format (PHP is the default, YAML is also supported, useful for legacy projects with XML and but old Symfony)
- the argument is paths to file/s or directories you want to convert

```bash
vendor/bin/config-transformer switch-format --input-format yaml app/config
vendor/bin/config-transformer switch-format --input-format xml app/config
```

Are you lazy like me? Use shortcut:

```bash
vendor/bin/config-transformer switch-format -i yaml -o php app/config
vendor/bin/config-transformer switch-format -i xml -o php app/config
```


## Why am I Putting the Path Argument as the Last one?

You've noticed, the path argument is the last in the command line. That's rather confusing, right?

```bash
# conventional
vendor/bin/config-transformer PATH switch-format -i yaml -o php

# convenient
vendor/bin/config-transformer switch-format -i yaml -o php PATH
```

Do you need to switch multiple paths in separated commits or apply them on multiple projects?
<br>
Re-use previous command and **change the last part only**.

This is how my **real workflow** looks like: from low hanging fruit of 1 file to the main config, to all packages.
Each followed by a separated commit, so it's easier to review and fix in case of regression.

```bash
vendor/bin/config-transformer switch-format -i yaml -o php ecs.yaml
# commit

vendor/bin/config-transformer switch-format -i yaml -o php rector.yaml
# commit

vendor/bin/config-transformer switch-format -i yaml -o php app/packages
# commit

vendor/bin/config-transformer switch-format -i yaml -o php app/config/config.yaml
# commit
```

## 3. Upgrade paths to PHP in Extensions and Kernel

The config format switch is one part; the next is to update loaders in PHP code.
Again, it's valid to handle it manually with search & replace in PHPStorm.

```diff
 use Symfony\Component\Config\FileLocator;
 use Symfony\Component\DependencyInjection\ContainerBuilder;
-use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
+use Symfony\Component\DependencyInjection\Loader\PhplFileLoader;
 use Symfony\Component\HttpKernel\DependencyInjection\Extension;

 final class SomeExtension extends Extension
 {
     public function load(array $configs, ContainerBuilder $container)
     {
-        $loader = new YamlFileLoader($container, new FileLocator());
+        $loader = new PhplFileLoader($container, new FileLocator());
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
<?php

use Migrify\ConfigTransformer\FormatSwitcher\ValueObject\Format;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Rector\Symfony\Rector\Class_\ChangeFileLoaderInExtensionAndKernelRector;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ChangeFileLoaderInExtensionAndKernelRector::class)
        ->call('configure', [[
            ChangeFileLoaderInExtensionAndKernelRector::FROM => Format::YAML,
            ChangeFileLoaderInExtensionAndKernelRector::TO => Format::PHP,
        ]]);
};
```

And let Rector handle the boring work:

```bash
vendor/bin/rector p app src
```

<br>

And that's it!

One little tool for you, one big leap for a PHP programmer-kind.

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

Create [an issue](https://github.com/migrify/migrify/issues/new) and let us know. We'd love to hear it.

<br>

Happy coding!
