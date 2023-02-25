---
id: 163
title: "How to Manage Configuration in Symfony without Bundle, Extension, and Configuration?"
perex: |
    Symfony Flex is moving towards of bundle-less applications. That doesn't mean you should create a monolith code in `/src` as fast as possible, but rather control everything via `.yaml` and `.env` files. It's takes [few steps](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/#refactor-service-config-in-5-steps) to **remove extension and move to import** of `services.yaml`.

    But how would you approach a simple task **as *setup an account number* parameter**?
tweet_image: "/assets/images/posts/2018/bundle-less/bundle-less.png"

---

If you hear about the **trend of "no-bundle" application** for the first time, is very nicely summarized in 10 points in [SymfonyCasts](https://symfonycasts.com/blog/AppBundle). Go check it, I'll wait here.

<img src="/assets/images/posts/2018/bundle-less/bundle-less.png" class="img-thumbnail" style="max-width:400px">

## 1. How this Affected Service Registration?

Before you need 3 classes to get services to the application:

```bash
/app
    AppKernel.php
/packages
    /accountant
        /src
            /DependencyInjection
                AccountantExtension.php
            AccountantBundle.php
        /config
            services.yaml
```

```php
<?php declare(strict_types=1);

namespace App;

use Project\Accountant\AccountantBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class AppKernel extends Kernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new AccountantBundle];
    }

    // ...
}
```

```php
<?php declare(strict_types=1);

namespace Project\Accountant;

use Project\Accountant\DependencyInjection\AccountantExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AccountantBundle extends Bundle
{
 public function getContainerExtension()
    {
        return new AccountantExtension();
    }
}
```

```php
<?php declare(strict_types=1);

namespace Project\Accountant\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

final class AccountantExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }
}
```

```yaml
# packages/accountant/config/services.yaml
services:
    Project\Accountant\:
        resource: "../src"
```

Now we can drop all of the PHP [magic code](https://matthiasnoback.nl/2013/10/symfony2-some-things-i-dont-like-about-bundles) down:

```diff
 /app
     AppKernel.php
 /packages
     /accountant
         /src
-            /DependencyInjection
-                AccountantExtension.php
-            AccountantBundle.php
         /config
             services.yaml
```

...and load services in local config:

```yaml
# app/config.yaml
imports:
   - { resource: "packages/accountant/config/services.yaml" }
```

Or we can set this up just once for all [local packages](/blog/2017/12/25/composer-local-packages-for-dummies/) with [glob](https://symfony.com/blog/new-in-symfony-3-3-import-config-files-with-glob-patterns):

```yaml
# app/config.yaml
imports:
   - { resource: "packages/*/config/services.yaml" }
```

**We deleted all PHP files and add 2 lines to config** - that's what a good trade, right? Much less code can go wrong and the result is easy to read even for a programmer who was just hired today.

<br>

I think most of you already know this configuration shift and use it for months, right? Now the harder part, that many people still struggle with.

## 2. How this Affected Configuration?

In the "accountant" package we have a service that sends money... no ordinary money, Bitcoins! And we need to **set an account number parameter** to it:

```php
<?php declare(strict_types=1);

namespace Project\Accountant;

final class BitcoinSender
{
    /**
     * @param string
     */
    private $accountNumber;

    public function __construct(string $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public function donateTo(float $amount, string $targetAccountNumber)
    {
        // move $amount
        // from $this->accountNumber
        // to $targetAccountNumber
    }
}
```

The configuration of `$accountNumber` value in [*bundle-school* paradigm](https://stovepipe.systems/post/creating-bundle-configuration) looks like this:

```diff
 # packages/accountant/config/services.yaml
+accountant:
+     account_number: "123_secret_hash"
+
 services:
     Project\Accountant\:
         resource: "../src"
```

```php
<?php declare(strict_types=1);

namespace Project\Accountant;

use Project\Accountant\DependencyInjection\AccountantExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AccountantBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new AccountantExtension();
    }
}
```

```php
<?php declare(strict_types=1);

namespace Project\Accountant\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class AccountantExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new AccountantConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        // for bitcoin sender
        $container->getDefinition('Project\Accountant\BitcoinSender')
            ->setArgument('accountNumber', $config['account_number']);

        // for further use (optional)
        // $container->setParameter('account_number', $config['account_number']);
    }
}
```

```php
<?php declare(strict_types=1);

namespace Project\Accountant\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class AccountantConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('accountant');
        $rootNode
            ->children()
                ->scalarNode('account_number')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
```

All this fuss just to **load single parameter**? Not anymore:

```diff
 /app
     AppKernel.php
 /packages
     /accountant
         /src
-            /DependencyInjection
-                AccountantExtension.php
-                AccountantConfiguration.php
-            AccountantBundle.php
         /config
             services.yaml
```

All cleaned up. We run the app and...

*ERROR: "$accountNumber" argument was not set*

Damn! What now?

## What Options do We Have?

### 1. Keep the Extension

```php
<?php

$container->getDefinition('Project\Accountant\BitcoinSender')
    ->setArgument('accountNumber', $config['account_number']);
```

❌

We want to get rid of this code, not to maintain it.

### 2. Set Parameter Manually in the Config

```diff
 # packages/accountant/config/services.yaml
-accountant:
+parameters:
     account_number: "123_secret_hash"

 services:
     Project\Accountant\:
         resource: "../src"

+    Project\Accountant\BitcoinSender:
+        arguments:
+            $accountNumber: "%account_number%"
```




We want config to use PSR-4 autodiscovery to it's fullest potential, not go back to manual service definitions.

### 3. Bind the parameter

Good idea! Since [Symfony 3.4](https://symfony.com/blog/new-in-symfony-3-4-local-service-binding) we can do this:

```diff
 # packages/accountant/config/services.yaml
-accountant:
+parameters:
     account_number: "123_secret_hash"

 services:
+    _defaults:
+        bind:
+            $accountNumber: "%account_number%"
+
     Project\Accountant\:
         resource: "../src"
```

✅

### 4. Autowire the Parameter

```php
<?php declare(strict_types=1);

namespace App;

use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
```

You set up this only once, but then you can enjoy short and clear configs:

```diff
 # packages/accountant/config/services.yaml
-accountant:
+parameters:
     account_number: "123_secret_hash"

 services:
     Project\Accountant\:
         resource: "../src"
```

✅

This compiler autowires parameters by convention:

- `%parameter_name%` => `$parameterName`

You can [read more about it here](/blog/2018/11/05/do-you-autowire-services-in-symfony-you-can-autowire-parameters-too/).

## Final Results

So how does our bundle-less application looks like in the end?

- we got rid of the `Configuration` class - no more tree fluent builds for a bunch of parameters
- we got rid of the `Extension` class - no more relative paths
- we got rid of the `Bundle` class - no more `createExtension()`, `getExtension()` typos
- we gain parameter binding/autowiring

**We work with configs that clearly state all we parameters and services we use**. Explicit, clear, in one place.

<br>

How do you approach parameter for your packages (previously bundles) in Symfony 4 applications?
