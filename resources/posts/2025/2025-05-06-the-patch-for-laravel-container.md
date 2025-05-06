---
id: 433
title: "The Patch for Laravel Container"
perex: |
    [I switched this website from Symfony to Laravel](/blog/why-I-migrated-this-website-from-symfony-to-laravel) 2,5 years ago, and I [love Laravel Container](/blog/what-i-prefer-about-laravel-dependency-injection-over-symfony) ever since.

    Symfony and Laravel containers are very similar - read this [compare post](/blog/experiment-how-i-replaced-symfony-di-with-laravel-container-in-ecs) if you know one and want to understand the other.

    Yet, I **always patch Laravel Container in `/vendor`** for all projects I use.

    Single line change.

    Why and where?
---


Imagine this situation: we're using an open-source package and we **want slightly different behavior** from one file. We have 3 options:

## 1. Fork it!


We fork the package, add our code, and maintain it forever. A plan that sounds too good to be true.
In 5 years, this will lead up to a huge mess that [someone else](https://getrector.com/hire-team) will have to clean up.

## 2. Create a pull request!

We can also create a pull request, contribute to the open-source project, and wait for the release of your feature... it might take time, but if others will benefit from it, it's worth it.


## 3. Patch it!

We patch the file locally in your `/vendor`. This doesn't mean copying `/vendor/some/package` to `/my-vendor/some/package`, basically a local fork.

Instead, we use the native git patching feature, which allows us to [add only the patch file itself](/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates/). This way **we still get updates in the future release, while keeping our feature intact**. Win, win!

<br>

There is just one catch: in this case, it's not a slightly different feature, but the complete opposite.


## Default Behavior for Unregistered Service

Let's say we have a huge project like Rector and we use Laravel Container (`illuminate/container` package) as a foundation.
What happens when we try to resolve a service that is not registered in the container?

```php
use Illuminate\Container\Container;

$container = new Container();
$setListProvider = $container->get(SetListProvider::class);
```

The container will create it using reflection. Nice!

<br>

But what happens if we ask for the same service again?

```php
use Illuminate\Container\Container;

$container = new Container();
$setListProvider = $container->get(SetListProvider::class);

// 100 files and 1000 lines later
$setListProvider = $container->get(SetListProvider::class);
```

Those are 2 different instances.

<br>

If we have a project with fewer than 100 services, we most likely won't hit a bottleneck here. Creating duplicated services for the same job doesn't make sense, but we're good performance-wise.

<br>

Now, imagine we have 50 services running, each of which has 5-10 dependencies. That's 50 instances of the very same 5-10 services, **250-500 waste instances of the same type**, including their constructor dependencies.

Creating service by type via constructor reflection is performance-heavy, and this might hit us hard.

## Container ~= Factory

That means our **container behaves like a factory**, not like a service locator. Why?

* The factory creates a new object every single time.
* Service locator returns the very same object every single time (singleton).

<br>

You're right, we could use a `$container->singleton(...)` call to define all of those services explicitly. But at that point, the framework stopped working for us, and **we started working for the framework**.

I want a zero-line-config, not 500+ lines I have to maintain across dozens of repositories.


## Container as a Service Locator

Instead, we want to use the container as a service locator. We ask for a service, and Laravel will handle it for us. If we want a new instance, we use the `new` keyword.

This is the default behavior of `illuminate/container` we want to change. With a patch.

<br>

Fortunately, Laravel Container has a very clean and simple architecture. Only 2 files.

There is a single line that decides: **is it a singleton or should we create a new service**?

<br>

Here: [https://github.com/illuminate/container/Container.php#L903](https://github.com/illuminate/container/blob/22635d3fb61bbb3db235aa7eb90ac1fac6901095/Container.php#L903)

```php
// If the requested type is registered as a singleton, we'll want to cache off
// the instances in "memory" so we can return it later without creating an
// entirely new instance of an object on each subsequent request for it.
if ($this->isShared($abstract) && ! $needsContextualBuild) {
    $this->instances[$abstract] = $object;
}
```

How do we teach Laravel container to always consider service "shared" (= singleton)?

We change the left condition to always `true`:

```diff
--- /dev/null
+++ ../Container.php
@@ -800,7 +800,7 @@
         // If the requested type is registered as a singleton, we'll want to cache off
         // the instances in "memory" so we can return it later without creating an
         // entirely new instance of an object on each subsequent request for it.
-        if ($this->isShared($abstract) && ! $needsContextualBuild) {
+        if (! $needsContextualBuild) {
             $this->instances[$abstract] = $object;
         }
```

This way, all our **services are created only once**. This keeps our `ServiceProvider` to a minimum size. We only need them, if we do something more complicated.


## Like it? Try it

We use this patch in many places, so it would be a waste of time to create a new patch for each project. Instead, we store patches in an [open-source repository](https://github.com/rectorphp/vendor-patches/blob/main/patches/illuminate-container-container-php.patch) on GitHub.

That way, you can try it in 2 lines:

### 1. Install the patching package

```bash
composer require symplify/vendor-patches --dev
```

<br>

### 2. Add the patch file to your `composer.json`

```json
{
    "extra": {
        "patches": {
            "illuminate/container": [
                "https://raw.githubusercontent.com/rectorphp/vendor-patches/main/patches/illuminate-container-container-php.patch"
            ]
        },
        "composer-exit-on-patch-failure": true,
        "enable-patching": true
    },
    "config": {
        "allow-plugins": {
            "cweagans/composer-patches": true
        }
    }
}
```

### 3. Run `composer install`

This will trigger all patch files and apply them to your `/vendor`.

<br>

If you update to Laravel 13, the patch will be applied automatically (unless the container is completely rewritten, but we keep patching up to date since Laravel 9).

<br>

Happy coding!
