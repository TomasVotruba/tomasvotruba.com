---
id: 161
title: "How to Test Monorepo in 3 Layers"
perex: |
    Do you have a monorepo, with 2 packages at least, autoloaded with composer and splitting works?
    Good! Now you're about to set up testing and code quality tools.


    How to make testing so tight no bug can escape?


updated_since: "February 2021"
updated_message: "Changed from Travis to GitHub Actions, from post-split testing to local split testing."
---

There are 3 layers you test your monorepo in. Most projects have 2 of them max.:

- **Testing Monorepo** (Symfony, Sylius)
- **Testing Standalone Packages in Monorepo** (Symfony, Sylius)
- **After Split Testing**

I'm not sure why the last one is often skipped. Surprisingly, it's very easy to setup - a matter of single a new workflow file in `.github/workflows`.

Now we know the 3 testing layers. It's time to look **why each particular layer is important**.

## 1. Testing Monorepo

```bash
/.github/workflows/...
/packages
    /first-package
    /second-package
phpunit.xml
```

### Why is it Important?

Monorepo is more complex than the classic package. The developers who use it needs to study more nested directories, special rules and exceptions he didn't have to before. He's already exhausted by learning all this and he's barely some energy left to contribute.

That's why **your monorepo workflow has to be as simple as possible**.

Testing should be as easy as:

```bash
vendor/bin/phpunit
```

One run and I we can see test are passing or failing. Must have.

## 2. Testing Standalone Packages in Monorepo

```diff
 /.github/workflows/...
 /packages
     /first-package
+        phpunit.xml
     /second-package
+        phpunit.xml
 phpunit.xml
```

In this layer, each package has own PHPUnit setup. It still uses root `vendor/autoload.php`, but the testing scope is more similar to standalone package testing. If's *faking* split testing for poor people.

```bash
vendor/bin/phpunit packages/first-package
vendor/bin/phpunit packages/second-package
```

### Why is it Important?

PHPUnit **has own autoloading so it autoloads tests** without relying on your `composer.json`. It's for historical reasons and also the fact, it's not standard to autoload test files or even user PSR-4 naming in them.

<br>

So when we run e.g. `vendor/bin/phpunit packages`, we basically tell the PHPUnit *autoload `packages` directory*.

What happens, when:

 - `packages/first-package/tests/Fixture/SomeClass.php` is used in test in
 - `packages/second-package/tests/UnrelatedTest.php`?

❌

**It will silently pass**. Monorepo has many classes you work with and some test classes can be accidentally reused in another package. Your test run says it passes, even though it's broken.

You'll find out eventually when `second-package` is downloaded and break the code to somebody but isn't automated testing suppose to prevent that?

## 3. Split Testing

### Why is it Important?

This is like a double condom with birth control - the best quality testing we can get. It's **almost identical with real use when programmer downloads** a package by `packages/second-package`.

Our goal is to autoload:

- the second-package code in `packages/second-package/src`
- dependencies from `packages/second-package/composer.json` **ONLY**

<br>

You've figured out by now *the why* by seeing **ONLY**. You can't find this bug in layer 1 or 2.

Our first package uses Doctrine `packages/first-package/composer.json`

```json
{
    "name": "our-project/first-package",
    "require": {
        "php": "^7.2",
        "doctrine/orm": "^2.7"
    }
}
```

At the same time, `second-package` is using the Doctrine class:

```php
namespace OurProject\SecondPackage;

use Doctrine\ORM\EntityManagerInterface;

final class ProductController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
    }
}
```

But does **not require Doctrine** it in `composer.json`:

```json
{
    "name": "our-project/second-package",
    "require": {
        "php": "^7.2"
    }
}
```

How does the GitHub Action **workflow look like exactly**? Checkout [How to Test Monorepo After Split Before Actual Split](/blog/2020/02/10/how-to-test-monorepo-after-split-before-actual-split/).

That's why after split testing is so important. GitHub Action will tell us!

<br>

Happy coding!
