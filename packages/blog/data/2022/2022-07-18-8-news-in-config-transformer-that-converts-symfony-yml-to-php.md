---
id: 367
title: "8 News in Config Transformer that Converts Symfony YML to PHP"
perex: |
    If you have not switched your [Symfony configs from YAML to PHP](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/), there no better time like present.


    This month we tested the ConfigTransformer to its limit. It had to deal with edge-cases from Symfony 2.8 all the way through Symfony 3.4. With features that were removed for years. As a result, we added default processing of `*.yaml` and `.yml` syntax at the same time and support for scalars like `::hi` and `'hello::'`.


    That's just the tip of the iceberg. **What are the 5 new features you'll be able to enjoy in the upcoming release**?

tweet_image: "/assets/images/posts/2022/config_trans_after.png"
---

## 1. New Support for Directory Loader

Since Symfony 2.8, it is possible to [load configs from a single directory](https://github.com/symfony/symfony/issues/11045). That means you can use the shorter syntax...

```diff
 imports:
-    - { resource: packages/doctrine.yml }
-    - { resource: packages/framework.yml }
-    - { resource: packages/security.yml }
+    - { resource: packages/ }
```

...with the same effect. Now you won't forget to include the config here. It's pretty rare with legacy projects, but still possible syntax.

<br>

In the new ConfigTransformer version, we **cover directory loading out of the box ✅**

<br>

## 2. New Defaults Extensions Autoloaded

The directory loading opened a complete feature list we can cover. E.g., now it's possible to load extensions from these imported configs:

```yaml
doctrine:
    key: value
```

<br>

We've added the list of 10 default extensions to cover - `'doctrine'`, `'framework'`, `'monolog'`, `'security'`, `'twig'` and more. Including the deprecated ones, e.g. `'assetic'`.

<br>

This allows us converting a much lower Symfony version than ever - **Symfony 2.8 ✅**

<br>

## 3. Added Support for `autowiring_types`

This option [was deprecated in Symfony 3.3](https://github.com/symfony/symfony/pull/21494). If any config contained it, it was skipped.

<br>

It was quite a complex challenge because the code is removed in Symfony 6, and ConfigTransformer is built on that version.

But in the end, we managed to add it:

```yaml
services:
    some_service:
        class: App\SomeClass
        autowiring_types: App\SomeInterface
```

↓

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use App\SomeClass;
use App\SomeInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('some_service', SomeClass::class)
        ->addAutowiringType(SomeInterface::class);
```

**Now it's covered as expected ✅**

<br>

## 4. Add Support for Unquoted Scalars

Unquoted scalars were deprecated [in Symfony 3.1](https://symfony.com/blog/new-in-symfony-3-1-yaml-deprecations#deprecated-starting-scalars-with-characters). If your config had it, the conversion crashed:

```yaml
framework:
    secret: %secret%
```

<br>

Are you using a Symfony version where this is still valid syntax? We have good news for you.

**We added automated pre-quoting of scalar so this can be parsed with ease ✅**

<br>

## 5. Skip PHP Configs Loading

Usually, we take many little steps when it comes to huge changes. We don't convert all the files at once, but rather one group at a time, e.g. first parameters, then services, then extensions.

That means in certain point of time, we have part configs in PHP and part in YAML:

```bash
/configs
    config.yml
    /parameters
        items.php
```

<br>

Before, the ConfigTransformer tried to load the PHP configs and invoked a bunch of class loading and config interpretation, that could crash.

**Now it will skip PHP and focuses solely on the YAML files ✅**

<br>

## 6. Informative Output with Beautiful Colored Diff

Do you want first to try it on a single file? That's completely normal. I often do that, too, to verify the output is the same as I expect.

At the moment, we can run a `--dry-run` so we see what changes:

<img src="/assets/images/posts/2022/config_trans_before.png" class="img-thumbnail mb-4" style="max-width: 35em">

We see some files are removed, and other files are added. Okay, but what about content? At this point, the `--dry-run` does not change anything, which is correct. But it also doesn't give us any indices to decide to run a command without it. We can do better.

<br>

That's why we've **redesigned the output to provide the following**:

* Information about the file rename
* full diff of what you expect

<img src="/assets/images/posts/2022/config_trans_after.png" class="img-thumbnail mt-3 mb-4" style="max-width: 35em">

**Beautiful and clear ✅**

<br>

## 7. Improved Support for `when@env`

Thanks to [Tofandel](https://github.com/Tofandel) you can enjoy this [impressive feature](https://github.com/symplify/symplify/pull/4247) that handles complex cases of conditional environments:

<br>

```yaml
when@local:
    web_profiler:
        toolbar: true
        intercept_redirects: false

    framework:
        profiler: true
```

↓

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ($containerConfigurator->env() === 'local') {
        $containerConfigurator->extension('web_profiler', [
            'toolbar' => true,
            'intercept_redirects' => false,
        ]);
        $containerConfigurator->extension('framework', [
            'profiler' => true
        ]);
    }
};
```

<br>

## 8. Refactored Constant Support

Last but not least is a feature that focuses on constant. Constants were one of the weakest parts of ConfigTransformer, with a lot of hacking. It was prone to error on non-existing or non-autoloaded constants, which should not happen as the scope of this tool is entirely static.

Again, big thanks to Tofandel, who [refactorted it into a smooth solution](https://github.com/symplify/symplify/pull/4246). It now handles any constant possible:

```yaml
parameters:
    class_constant: !php/const App\SomeConst::TEST
    class: !php/const App\SomeConst::class
    unexisting_constant: !php/const App\MissingClass::NOT_HERE
    another_key: '%env(string:default::CODE_EDITOR)%'
```

↓

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('class_constant', App\SomeConst::TEST);
    $parameters->set('class', App\SomeConst::class);
    $parameters->set('unexisting_constant', App\MissingClass::NOT_HERE);
    $parameters->set('another_key', '%env(string:default::CODE_EDITOR)%');
};
```

As a bonus, it cut down the converting speed on one project from 3 seconds to 200 ms. That's **1500 % faster**.

Thank you ✅

<br>

## Start Now to Save Work

I'm proud of this leap forward and better support for older Symfony versions. It allows to convert configs **to a broader range of projects and start using PHP in earlier stages**. That brings Rector, ECS, and PHPStan to help as well, as they cover any PHP file, even configs.

Now it's the best time to switch - [here is how](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/)

<br>

Happy coding!
