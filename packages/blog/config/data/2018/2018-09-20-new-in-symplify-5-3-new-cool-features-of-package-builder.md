---
id: 145
title: "New in Symplify 5: 3 New Cool Features of PackageBuilder"
perex: |
    [PackageBuilder](https://github.com/symplify/packagebuilder) was always sort of meta package with all **the cool and shiny features anyone can use**. After all, it's the most downloaded Symplify package hitting almost [1000 downloads a day](https://packagist.org/packages/symplify/package-builder/stats).
    <br>
    <br>
    In Symplify 5 now it allows you to **drop manual binds** from Symfony configs, separate files from directories **in one method** and merge nested YAML parameters **with 1&nbsp;service**.
tweet: "New post on my Blog: New in Symplify 5: 3 New Cool Features of PackageBuilder #symfony #autowiring #yaml"
---

You don't have this package installed yet?

```bash
composer require symplify/package-builder
```

Now enjoy the news ↓

## 1. Drop Manual Binds in Symfony configs

<a href="https://github.com/Symplify/Symplify/pull/998" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #998
</a>

You can add [parameter binding since Symfony 3.4](https://symfony.com/blog/new-in-symfony-3-4-local-service-binding):

```yaml
services:
    _defaults:
        bind:
            $meetupComApiKey: '%meetup_com_api_key%'
```

That's nice. But what if you have multiple configs that use multiple parameters?

```yaml
services:
    _defaults:
        bind:
            $meetupComApiKey: '%meetup_com_api_key%'
            $facebookApiKey: '%facebook_api_key%'
            $maxPostOnHomepage: '%max_post_on_homepage%'

    App\FirstPackage\:
        resource: ..
```

```yaml
services:
    _defaults:
        bind:
            $facebookApiKey: '%facebook_api_key%'
            $maxPostOnHomepage: '%max_post_on_homepage%'

    App\SecondPackage\:
        resource: ..
```

### Not for Lazy Programmer

This way you'll be writing more bindings than there are parameters. And there is more! When you remove autodiscovered service that depends on a bound parameter, you'll get this "nice" exception:

<blockquote class="blockquote">
    Unused binding "maxPostOnHomepage" in service "App\SomeUnterlatedService"
</blockquote>

You can solve this all by having a huge config with all parameters, binding and services. Even if the config would be shorter than 100 lines, you still have to maintain parameters, bindings and services and it teaches other programmers to *put-everything-to-one-place* instead of SOLID principles.

<br>

**Would you like to get rid of this all extra maintenance, and just code cleanly instead?** I would!

So, have you noticed the pattern?

- `$variableName` <=> `%parameter_name`
- `camelCase` <=> `underscore_case`

And exactly this can be automated! Just add `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass` compiler pass:

```diff
<?php

    // ...

 use Symfony\Component\DependencyInjection\ContainerBuilder;
 use Symfony\Component\HttpKernel\Kernel;
+use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;

 final class YourAppKernel extends Kernel
 {
     // ...

+    protected function build(ContainerBuilder $containerBuilder): void
+    {
+        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
+    }
 }
```

And if you keep this convention, you can keep yours configs clear and minimalistic:

```diff
 services:
-    _defaults:
-        bind:
-            $meetupComApiKey: '%meetup_com_api_key%'
-            $facebookApiKey: '%facebook_api_key%'
-            $maxPostOnHomepage: '%max_post_on_homepage%'
```

Of course you can bind your parameters manually:

```yaml
services:
    _defaults:
        bind:
            $anotherName: '%non_standard_parameter_naming%'

    SomeClass:
        arguments:
            - '%very_specfici_meetup_com_api_key%'
```

## 2. Separate Files from Directories

<a href="https://github.com/Symplify/Symplify/pull/963" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #963
</a>

Do you need to work multiple file/directories arguments?

```bash
vendor/bin/ecs check src tests/ThisFile.php
```

Just use `Symplify\PackageBuilder\FileSystem\FileSystem`:

```php
<?php

$sources = [
   __DIR__ . '/SomeDirectory',
   __DIR__ . '/SomeFile.php'
];

$symplifyFileSystem = new Symplify\PackageBuilder\FileSystem\FileSystem;
[$files, $directories] = $symplifyFileSystem->separateFilesAndDirectories($sources);

// ...
```

## 3. Merge Parameters without Leaving Any Behind

<a href="https://github.com/Symplify/Symplify/pull/989" class="btn btn-dark btn-sm">
    <em class="fab fa-github fa-fw"></em>
    See pull-request #989
</a>

At the moment, Symfony is unable to merge nested parameters [for historical and other reasons](https://github.com/symfony/symfony/issues/26713):

```yaml
# config.yml
imports:
    - { resource: 'imported-file.yml' }

parameters:
    festivals:
        - three
```

```yaml
# imported-file.yml
parameters:
    festivals:
        - one
        - two
```

This will end up with just 1 festival in classic Symfony Applicatoin. Do you want to use the **full power of YAML, glob and imports** and **still keep all the parameters**?

Use `Symplify\PackageBuilder\Yaml\ParameterMergingYamlLoader`:

```php
<?php

$parametersMergingYamlLoader = new Symplify\PackageBuilder\Yaml\ParameterMergingYamlLoader;
$parameterBag = $parametersMergingYamlLoader->loadParameterBagFromFile(
    __DIR__ . '/config.yml'
);

var_dump($parameterBag->get('festivals'));
// ['one', 'two', 'three']
```

<br>

And that's all folks!

<br>

Happy tuning of your code!
