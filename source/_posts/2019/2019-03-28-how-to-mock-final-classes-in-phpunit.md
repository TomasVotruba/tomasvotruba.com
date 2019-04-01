---
id: 198
title: "How to Mock Final Classes in PHPUnit"
perex: |
    Do you prefer composition over inheritance? Yes, that's great. Why aren't your classes `final` then? Oh, you have tests and you mock your classes. **But why is that a problem?**

tweet: "New Post on #php 🐘 blog: How to Mock Final Classes in #PHPUnit"
tweet_image: "/assets/images/posts/2019/mocking-final/final-case.png"

tested: true
test_slug: FinalMock
---

Since I started using *`final` first* [I got rid of many problems](/blog/2019/01/24/how-to-kill-parents/). Most programmers I meet already know about the benefits of not having 6 classes extended in a row and that `final` remove this issue.

But many of those programmers are skilled and they write tests.

## How Would You Mock this Class?

...so it returns `20` on `getNumber()` instead:

```php
<?php

final class FinalClass
{
    public function getNumber(): int
    {
        return 10;
    }
}
```

We have few options out in the wild:

- [You can use `uopz` extension](https://stackoverflow.com/a/33095281/1348344) <em class="fas fa-fw fa-times text-danger fa-lg"></em>
- [You can use reflection](https://gist.github.com/DragonBe/24761f350984c35b73966809dd439135) <em class="fas fa-fw fa-times text-danger fa-lg"></em>

or...

## Extract an Interface

```diff
 <?php

-final class FinalClass
+final class FinalClass implements FinalClassInterface
 {
     public function getNumber(): int
     {
         return 10;
     }
 }
+
+interface FinalClassInterface
+{
+    public function getNumber(): int;
+}
```

Then use the interface instead of the class in your test:

```diff
 <?php

 use PHPUnit\Framework\TestCase;

 final class FinalClassTest extends TestCase
 {
     public function testSuccess(): void
     {
-        $finalClassMock = $this->createMock(FinalClass::class);
+        $finalClassMock = $this->createMock(FinalClassInterface::class);
         // ... it works! but at what cost...
     }
 }
```

This will work, but creates **huge debt you'll have to pay later** (usually at a time you would rather skip):

- for every new `public` method in the class, you have to update the interface
- "interface everything" approach will shift the meaning of interface from "something to be implemented for a reason" to "anything you want to test"
- do you have 100 classes? you have 200 PHP files now, you're welcome!

This is obviously annoying maintenance and it will lead you to one of 2 bad paths:

- **don't use `final`** at all
- or **do not test**

<em class="fas fa-fw fa-2x fa-times text-danger fa-lg"></em>

## By Pass Finals!

Nette packages also missed `final` in the code, so people could mock it. Until David came with [Bypass Finals](https://github.com/dg/bypass-finals) package. Some people think it's only for Nette\Tester, but I happily **use it in PHPUnit universe** as well.

We just install it:

```bash
composer require dg/bypass-finals --dev
```

And enable:

```php
DG\BypassFinals::enable();
```

<em class="fas fa-fw fa-lg fa-check text-success"></em>

<div class="alert alert-sm alert-warning mt-5 mb-5" role="alert">
    Do you want to know, <strong>how BypassFinals works?</strong> Read author's <a href="https://phpfashion.com/how-to-mock-final-classes">blog post</a> or check <a href="https://github.com/dg/bypass-finals/blob/8f0f7ab7a17a6b5c188dde1cf5edc6ceb06c70c1/src/BypassFinals.php#L217">this line on Github</a>.
    <br>
    I don't know much, but I think it loads file via stream and removes the <code>T_FINAL</code> token.
</div>

Hm, where should be put it?

### 1. `bootstrap.php` File?

```php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

DG\BypassFinals::enable();
```

Update path in `phpunit.xml`:

```diff
 <phpunit
-    bootstrap="vendor/autoload.php"
+    bootstrap="tests/bootstrap.php"
 >
```

Let's run the tests:

```bash
vendor/bin/phpunit

...

There were 19 warnings:

1) SomeClassTest::testSomeMethod
Class "SomeClass" is declared "final" and cannot be mocked.
```

Hm, most mocks work, but there are still some errors.

<em class="fas fa-fw fa-2x fa-times text-danger fa-lg"></em>

### 2. `setUp()` Method?

Let's put it into `setUp()` method. It seems like a good idea for these operations:

```diff
 <?php

+use DG\BypassFinals;
 use PHPUnit\Framework\TestCase;

 final class FinalClassTest extends TestCase
 {
+    public function setUp()
+    {
+        BypassFinals::enable();
+    }

     public function testFailInside(): void
     {
         $this->createMock(FinalClass::class);
     }
 }
```


And run tests again:

```bash
vendor/bin/phpunit

...

There were 7 warnings:

1) AnotherClassTest::testSomeMethod
Class "AnotherClass" is declared "final" and cannot be mocked.
```

Damn you, black magic! We're getting there, but there are still mocks in the `setUp()` method, and we've also added work to our future self - for every new test case, we [have to remember](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) to add `BypassFinals::enable();` manually.

<em class="fas fa-fw fa-2x fa-times text-danger fa-lg"></em>

<br>
<br>

Why it doesn't work. I was angry and frustrated. Honestly, I wanted to give up now and just pick "interface everything" or "final nothing" quick solution.  I think **that resolutions in emotions are not a good idea...** so I take a deep breath, pause and go to a toilet to get some fresh air.

<br>

Suddenly... I remember that... PHPUnit has some Listeners, right? What if we could use that?

### 3. Own TestListener?

Let's try all the methods of `TestListener`, enable bypass in each of them by trial-error and see what happens:

```php
<?php declare(strict_types=1);

use DG\BypassFinals;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

final class BypassFinalListener implements TestListener
{
    public function addError(Test $test, \Throwable $t, float $time): void
    {
    }

    public function addWarning(Test $test, Warning $e, float $time): void
    {
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
    }

    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
    }

    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
    }

    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
    }

    public function startTestSuite(TestSuite $suite): void
    {
    }

    public function endTestSuite(TestSuite $suite): void
    {
    }

    public function startTest(Test $test): void
    {
        BypassFinals::enable();
    }

    public function endTest(Test $test, float $time): void
    {
    }
}
```

In the end, it was just one method.

Then register listener it in `phpunit.xml`:

```xml
<phpunit bootstrap="vendor/autoload.php">
    <listeners>
        <listener class="Listener\BypassFinalListener"/>
    </listeners>
</phpunit>
```

And run tests again:

```bash
vendor/bin/phpunit

...

Success!
```

Great! **All our objects can be final and tests can mock them**.

Is it a good enough solution? Yes, **it works and it's a single place of origin** - use it, close this post and your code will thank you in 2 years later.

<em class="fas fa-fw fa-lg fa-check text-success"></em>

<br>

Are you a **curious hacker that is never satisfied with his or her solution**? Let's take it one step further.

What do you think about the Listener class? There is **10+ methods** and **only one is used**. It's very hard to read. To add more fire to the fuel, `TestListener` class is [deprecated since PHPUnit 8](https://github.com/sebastianbergmann/phpunit/issues/3388) and will be [removed in PHPUnit 9](https://github.com/sebastianbergmann/phpunit/issues/3389). Don't worry, [Rector already covers the migration path](https://github.com/rectorphp/rector/pull/1270).

After bit of Googling on PHPUnit Github and documentation I found something called *hooks*!

### 4. Single Hook

You can read about them in the [PHPUnit documentation](https://phpunit.readthedocs.io/en/8.0/extending-phpunit.html#extending-the-testrunner), but in short: they're the same as the listener, just **with 1 event**.

```php
<?php declare(strict_types=1);

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

final class BypassFinalHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
```

And again, register it in `phpunit.xml`:

```xml
<phpunit bootstrap="vendor/autoload.php">
    <extensions>
        <extension class="Hook\BypassFinalHook"/>
    </extensions>
</phpunit>
```

The final test, run all tests:

```bash
vendor/bin/phpunit

...

Success!
```

<em class="fas fa-fw fa-2x fa-check text-success"></em>
<em class="fas fa-fw fa-2x fa-check text-success"></em>
<em class="fas fa-fw fa-2x fa-check text-success"></em>

### Before

- we had to use interface for mocks
- or we had to remove `final`
- we had to pick between inheritance hell or poor tests

### After

- A **single solution, in single class**
- we use PHPUnit feature directly, no weird bending code
- we can **mock anything**
- **we can `final` anything**

<br>

Finally :)


<br>

Do you want to see solutions 2, 3 and 4 tested in real PHPUnit code? [They're here on Github](https://github.com/TomasVotruba/tomasvotruba.cz/tree/master/tests/Posts/Year2019/FinalMock)



<br>

Happy coding!
