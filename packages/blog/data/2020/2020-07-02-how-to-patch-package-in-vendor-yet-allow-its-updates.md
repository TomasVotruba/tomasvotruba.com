---
id: 266
title: "How to Patch a Package in Vendor, Yet Allow its Updates"
perex: |
    While working with legacy code upgrades, we often need to fix a line or two in 3rd party package in `/vendor`.
    <br>
    <br>
    You can fork it, but by that, you **take manual responsibility** for all the package updates.
    You can copy a package locally, which is faster, but **disables package updates**.
    <br><br>
    Or... you can use "composer patches"?

tweet: "New Post on #php 🐘 blog: How to Patch a Package in Vendor, Yet Allow its Updates"
---

I'm currently working on one Nette project as [a cleaning lady](/blog/2020/04/27/forget-complex-migrations-use-cleaning-lady-checklist/). After making code nice & shiny clean, we'll move from Nette 2.4 to Nette 3.0. During the cleaning process, we got into one delicate issue I'd love to share with you.

<br>

We used [inject properties](/blog/2020/06/01/inject-or-required-will-get-you-any-service-fast/):

```php
<?php

abstract class AbstactSomePresenter
{
    /**
     * @inject
     * @var SomeDependency
     */
    public $someDependency;
}
```

But since PHP 7.4, we could drop the `@var` annotation:

```php
<?php

abstract class AbstactSomePresenter
{
    /**
     * @inject
     */
    public SomeDependency $someDependency;
}
```

But... this doesn't work in Nette 2.4. Aww.

<br>

We tried to add this feature by replacing native `InjectExtension` with our own. But native extension **is statically hardcoded**, so there was no way to replace it.

**What else we can do?** We looked at GitHub for a commit that added this feature somewhere between Nette 2.4 and 3.0 (with git blame GitHub feature and look at specific lines).

**We were lucky.** It was [**just 2 lines**](https://github.com/nette/di/commit/24df5e6af0ecf18542dc6e721112598bc648082c#diff-e7f245a9be21411c36d839ed85a17457) that added this feature!

## How to Change 2 Lines of code in `/vendor`?

We needed the same commit in our codebase with Nette 2.4.

**How can we do that?**

- edit file in `/vendor/nette/di/*` manually
- fork the package at version 2.4, edit it, release it, maintain it

<em class="fas fa-fw fa-times text-danger fa-2x"></em>

These options are slow or will keep the code changed only on your local machine = your `composer install` would suck a long time.

<br>

Is there some automated way with all the benefits and almost zero maintenance?

- [composer patches](https://github.com/cweagans/composer-patches)

<em class="fas fa-check text-success margin-auto fa-2x"></em>

## Patching For Dummies

Idea behind [composer patches](https://github.com/cweagans/composer-patches) is great, but the user experience with making the patch not so much. It's classic pixel coding - you have to edit the patch file the one slash or dot char. If you do it wrong, or the whole process collides with "fatal error". That's why there is [over 10 comments under Czech post by Tomas Pilar](https://pehapkari.cz/blog/2017/01/20/jak-snadno-a-rychle-upravovat-soubory-ve-vendoru), asking about the pixel coding.

I don't want developers to be frustrated over pixel coding. I want developers to play and explore their abilities to their limits.

We made a Symplify package that adds UX layer **that handles the tedious maintenance for you**.

## 4 Steps to Generate Your First Patch

### 1. Install Packages

```bash
composer require cweagans/composer-patches symplify/vendor-patches --dev
```

### 2. Create a Copy of `/vendor` file you Want To Change with `*.old` Suffix

For example, if you edit:

```bash
vendor/nette/di/src/DI/Extensions/InjectExtension.php
```

The copied file would be:

```bash
vendor/nette/di/src/DI/Extensions/InjectExtension.php.old
```

### 3. Open the Original file and Change it

```diff
         if (DI\Helpers::parseAnnotation($rp, 'inject') !== null) {
-           if ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
+           if ($type = \Amateri\Reflection\Helper\StaticReflectionHelper::getPropertyType($rp)) {
+           } elseif ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
               $type = Reflection::expandClassName($type, Reflection::getPropertyDeclaringClass($rp));
```

Only `*.php` file is loaded, not the `*.php.old` one. This way, you can **be sure the new code** is working before you generate patches.

### 4. Run `generate` command to Create Patch File and Register it

```bash
vendor/bin/vendor-patches generate
```

The Symplify tool will generate patch files for all files created this way in `/patches` directory:

```bash
/patches/nette-di-di-extensions-injectextension.php.patch
```

The patch path is created from the original file path, so **the patch name is always unique**.

Also, the configuration for `cweagans/composer-patches` is added your `composer.json`:

```json
{
    "extra": {
        "patches": {
            "nette/di": [
                "patches/nette_di_di_extensions_injectextension.patch"
            ]
        }
    }
}
```

That's it!

<br>

Now all you need to do is run composer:

```bash
composer install
```

And your patches are applied to your code!

<br>

Happy coding!
