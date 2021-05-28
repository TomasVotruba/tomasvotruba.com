---
id: 92
title: "5 Steps to Your First Fixer or Sniff Test"
perex: |
    When [I wrote my first Sniff](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/) 4 years ago I wanted to test it. I expected testing class, that would register sniff, provide ugly code and compare it to fixed one. So I started to explore PHP_CodeSniffer looking for such feature. Found one class, second class, warnings, errors, uff and after 10th error, I closed it.
    <br><br>
    When [I wrote my first Fixer](/blog/2017/07/24/how-to-write-custom-fixer-for-php-cs-fixer-24/), the story was a bit shorter but very similar. No wonder people don't test when the entry barrier is so huge.
    <br><br>
    **Since I use both of them and I want to motivate people to write their own sniffs and fixers, I turned this barrier to just 5 short steps** for both of them.
tweet: "New post on my blog: 5 Steps to Your First Fixer or Sniff Test #php #codingstandard #phpcodesniffer #phpcsfixer #phpunit"
tweet_image: "/assets/images/posts/2018/ecs-tester/test-case.png"
---

Imagine you have a `LowerBoolConstantsFixer` that fixes all uppercase bool constants to lowercase ones:

```diff
-$value = TRUE;
+$value = true;
```

And nothing more. How do we take this test case to PHPUnit? That is what [ECS Tester](https://github.com/symplify/easy-coding-standard-tester) package will help us with.

## 1. Install the package

```bash
composer require symplify/easy-coding-standard-tester --dev
```

## 2. Create a config with checker(s) you want to test


```yaml
# /tests/Fixer/LowerBoolConstantsFixer/config.yml
services:
    Your\CodingStandard\LowerBoolConstantsFixer: ~
```

(*Checker* is a group name for sniff and fixer, nothing more.)

## 3. Create a Test Case and Provide the Config

Create a test case that extends `Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase` class.

```php
<?php declare(strict_types=1);

namespace Your\CodingStandard\Tests\LowerBoolConstantsFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class LowerBoolConstantsFixerTest extends AbstractCheckerTestCase
{
    // ...
}
```

And provide the config above in `provideConfig()` method.

- Try to keep this standard in every test to reduce the maintenance of the test, e.g. `__DIR__ . '/config.yml'`.
- You can also make configured test of the same checker, `__DIR__ . '/configured-config.yml'`.

```diff
 <?php declare(strict_types=1);

 namespace Your\CodingStandard\Tests\LowerBoolConstantsFixer;

 use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

 final class LowerBoolConstantsFixerTest extends AbstractCheckerTestCase
 {
     // ...

+    protected function provideConfig(): string
+    {
+        return __DIR__ . '/config.yml';
+    }
 }
```

## 4. Test The Checker Behavior

You can make use of 3 testing methods:

- `doTestCorrectFile($correctFile)` - the file should not be affected by this checker
- `doTestWrongToFixedFile($wrongFile, $fixedFile)` - classic before/after testing
- `doTestWrongFile($wrongFile)` - **only for sniff** - it doesn't fix, just reports

<br>

```php
<?php declare(strict_types=1);

namespace Your\CodingStandard\Tests\LowerBoolConstantsFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class LowerBoolConstantsFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');

        $this->doTestWrongToFixedFile(
            __DIR__ . '/wrong/wrong.php.inc',
            __DIR__ . '/fixed/fixed.php.inc'
        );
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
```

## 5. The Best For Last - Create the Code Snippets

**This part you enjoy the most because your job is to break the checker**... well, at least verify it behaves as you want it to behave.

What should it skip? Well, since `NULL` / `null` is not a bool value...

```php
// correct/correct.php.inc
$value = NULL;
$value = null;
```

<br>

I guess you already know the before/after part:

```php
// wrong/wrong.php.inc
$value = TRUE;
$value = FALSE;
```

<br>

```php
// fixed/fixed.php.inc
$value = true;
$value = false;
```

That's it!


Now you know all you need to be able to test any fixer or sniff. But if you want to know more, check [the ECS Tester README](https://github.com/symplify/easy-coding-standardTester).

<br><br>

Enjoy simple testing!
