---
id: 161
title: "How to Test Monorepo in 3 Layers"
perex: |
    You already have a monorepo, you have at least 2 packages, autoloaded with composer and splitting works.
     Now you're about to set up testing and code quality tools.
    <br><br>
    How to make testing so tight no bug can escape?
tweet: "New Post on My Blog: How to Test #Monorepo in 3 Layers"
---

*Is this your first time with monorepo? Check [gomonorepo.org](https://gomonorepo.org/) to get into this topic fast.*

There are 3 layers you test your monorepo in. Often projects go just a few of them:

- **Testing Monorepo** (Symfony, Sylius)
- **Testing Standalone Packages in Monorepo** (Symfony, Sylius)
- **After Split Testing**

I'm not sure why the last one is often skipped. Surprisingly, it's very easy to setup - add `.travis.yml` and enable the repository testing on Travis.

Now you know, what testing layers there are. It's time to look **why each layer is important**.

## 1. Testing Monorepo

```bash
/packages
    /first-package
    /second-package
.travis.yml
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
 /packages
     /first-package
+        phpunit.xml
     /second-package
+        phpunit.xml
 .travis.yml
 phpunit.xml
```

In this layer, each package has own PHPUnit setup. It still uses root `vendor/autoload.php`, but the testing scope is more similar to standalone package testing. If's *faking* after split testing for poor people.

```bash
vendor/bin/phpunit packages/first-package
vendor/bin/phpunit packages/second-package
```

### Why is it Important?

PHPUnit **has own autoloading so it autoloads tests** without relying on your `composer.json`. It's for historical reasons and also the fact, it's not standard to autoload test files or even user PSR-4 naming in them.

<br>

**Use PSR-4 in your tests**:

- `/packages/first-package/src/SomeClass.php` → `FirstPackage\SomeClass`
- `/packages/first-package/tests/SomeClassTests.php` → `FirstPackage\Tests\SomeClassTest`

PHPStan and Rector are already forcing you to do it because they need to know the exact class type of every element to works correctly.

Thank you!

<br>

Back to testing...

So when you run e.g. `vendor/bin/phpunit packages`, you basically tell the PHPUnit *autoload `packages` directory*.

What happens, when:

 - `packages/first-package/tests/Fixture/SomeClass.php` is used in test
 - in `packages/second-package/tests/UnrelatedTest.php`?

<em class="fas fa-3x fa-times text-danger"></em>

**It will silently pass**. Monorepo has many classes you work with and some test classes can be accidentally reused in another package. Your test run says it passes, even though it's broken.

You'll find out eventually when `second-package` is downloaded and break the code to somebody but isn't automated testing suppose to prevent that?

## 3. After Split testing

```diff
 /packages
     /first-package
         phpunit.xml
+        .travis.yml
     /second-package
         phpunit.xml
+        .travis.yml
 .travis.yml
 phpunit.xml
```

In each `.travis.yml` you put script to run tests:

```yaml
script:
    - vendor/bin/phpunit
```

It will trigger standalone Travis for each package after splitting the monorepo:

- `our-project/our-project` - monorepo running...
- `our-project/first-package` - Travis running...
- `our-project/second-package` - Travis running...

### Why is it Important?

This is like a double condom with birth control - the best quality testing you can get. It's **almost identical with real use when programmer downloads** a package by `our-project/second-package`.

It will download:

- the second-package code in `/src`
- dependencies from `composer.json` **ONLY** of that package

<br>

I think you've figured out by now the why by seeing **ONLY**. You can't find this bug in layer 1 or 2.


Our first package uses Doctrine `/packages/first-package/composer.json`

```json
{
    "name": "our-project/first-package",
    "require": {
        "php": "^7.2",
        "doctrine/orm": "^2.7"
    }
}
```

At the same time, `second-package` has this class:

```php
<?php

namespace OurProject\SecondPackage;

use Doctrine\ORM\EntityManagerInterface;

class ProductController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
    }
}
```

And this `composer.json`:

```json
{
    "name": "our-project/second-package",
    "require": {
        "php": "^7.2"
    }
}
```

What happens, when we run one all previous layers?

```yaml
vendor/bin/phpunit
vendor/bin/phpunit packages/first-package
vendor/bin/phpunit packages/second-package
```

<em class="fas fa-3x fa-times text-danger"></em>

**It will silently pass**, because our monorepo has `doctrine/orm` installed, thanks to dependency in `first-package` (it's actually propagated by [tools](/blog/2018/10/08/new-in-symplify-5-create-merge-and-split-monorepo-with-1-command/#3-merge-code-composer-json-code) to root `composer.json`).

This is **the most common error while developing with monorepo first year**. You add dependencies here and there, you add a couple of new packages and code grows and grows.

That's why after split testing is so important. Travis will tell you this instantly.


## The Better Your Test Are, The More You Focus on Coding

Of course, you can manage these mutual dependencies by manual testing, in code-reviews, have a tool that will scan the code and composer it to `composer.json` requirements and so on. Their options are very stressful for developers because they need to automated work manually - imagine you'd check each space on each line instead of using Easy Coding Standard.

**So instead of focusing on machines work, just add `.travis.yml` to each of your packages and let Travis handle that.**

Travis has a purpose and you can focus on what you enjoy the most - coding.

Win-win :)
