---
id: 171
title: "How To Convert All Your Symfony Service Configs to Autodiscovery"
perex: |
    Do you use Symfony autodiscovery services registration everywhere and your configs have no extra lines?
    Skip this post and rather read another one.
    <br>
    <br>
    But if **you have many configs with manual service registration**, tagging, and autowiring, keep reading. I'll show you how you can convert them easily be new Symplify package.
tweet: "🐘 New Post on #php blog: How To Convert All Your #Symfony Service Configs to Autodiscovery"
tweet_image: "/assets/images/posts/2018/autodiscovery/demo.gif"
---

## tl;dr;

<img src="/assets/images/posts/2018/autodiscovery/demo.gif" class="img-thumbnail">

<br>

I've been consulting a few Symfony e-commerce projects recently that all have `service.yml`. Big configs with manual service registration:

```yaml
services:
    App\SomeService:
        autowire: true

    App\Controller\SomeController:
        autowire: true

    # 50 more lines...
    # 20 more files similar to this one
```

I already wrote [How to refactor to new Dependency Injection features in Symfony 3.3](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/), so you can read it. But you don't have to, since **this conversion can be automated**...

```yaml
services:
    _defaults:
        autowire: true
    App\:
        resource: /src
```

...with [Symplify\Autodiscovery](https://github.com/Symplify/Autodiscovery).

## 3 Steps to Your Minimalistic Configs

1. Install the package

    ```bash
    composer require symplify/autodiscovery
    ```

2. Convert Configs

    Run on `/src` directory:

    ```diff
    vendor/bin/autodiscovery convert-yaml /src
    ```

    It converts all `services.yml`, `config.yml`, `config.dev.yml` etc. configs that contain `services:` to autodiscovery format.

    `*.yaml` included.

3. See the changes:

    ```bash
    git diff
    ```

## What Can Go Wrong?

There are many reasons to **automate this work**, because **there are many gotchas** you have to be careful about. In each single services registration.

### 1. Tags

Name-only system tags can be removed thanks to `autoconfigure`:

```yaml
services:
    first_command:
        class: App\Command\FirstCommand
        tags:
            - { name: 'console.command' }
```

So can this:

```yaml
services:
    first_command:
        class: App\Command\FirstCommand
        tags: ['console.command']
```

But not this [lazy-loaded command](https://symfony.com/doc/current/console/commands_as_services.html#lazy-loading):

```yaml
services:
    first_command:
        class: App\Command\FirstCommand
        tags:
          - { name: 'console.command', command: 'first' }
```

And neither this:

```yaml
services:
    App\EventListener\ExceptionListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }
```

### 2. Single-class Names

Service name can be often dropped:

```diff
 services:
-    app.controller:
-        class: App\SomeController
+    App\SomeController: ~
```

Except for classes with no namespace:

```yaml
services:
    Single_Class_Name:
        class: Single_Class_Name
```

### 3. Vendor Autodiscovery

Configs are usually mixed of your code (`/app` or `/src`) and 3rd party code (`/vendor`):

```yaml
services:
    App\SomeService: ~
    App\AnotherService: ~

    Symplify\PackageBuilder\Parameter\ParameterProvider: ~
    Symplify\PackageBuilder\FileSystem\FileGuard: ~
```

Converter sees a namespace and tries to use autodiscovery:

```diff
 services:
-    App\SomeService: ~
-    App\AnotherService: ~
+    App\:
+        resource: ../src

-    Symplify\PackageBuilder\Parameter\ParameterProvider: ~
-    Symplify\PackageBuilder\FileSystem\FileGuard: ~
+    Symplify\PackageBuilder\: ~
+        resource: ../vendor/symplify/package-builder/src
```

Ops, **the last case should not be converted** - all 3rd party classes are better left explicit since we almost never register them all and they're handled by their own config/bundle:

```yaml
services:
    Symplify\PackageBuilder\Parameter\ParameterProvider: ~
    Symplify\PackageBuilder\FileSystem\FileGuard: ~
```

### 4. Exclude Obviously

When you try to autoload a class with a constructor, it's considered a service. But not all classes with constructors are services. Symfony doesn't know that unless you tell it, and it would fail with missing argument exception.

```diff
 services:
     App\:
        resource: ../src
+       exclude: ../src/{Entity,Exception,Contract}
```

The converter includes support for basic dirs to be excluded.

<br>

**Do you want minimalist configs for your application?** [Give Autodiscovery a try](#3-steps-to-your-minimalistic-configs).
