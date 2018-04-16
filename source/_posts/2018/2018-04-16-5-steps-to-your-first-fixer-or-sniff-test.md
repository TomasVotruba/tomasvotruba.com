---
id: 92
title: "5 Steps to Your First Fixer or Sniff Test"
perex: |
    When [I wrote my first Sniff](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/) 4 years ago I wanted to test it. I expected testing class, that would register sniff, provide ugly code and compare it to fixed one. So I started to explore PHP_CodeSniffer looking for such feature. Found one class, second class, warnings, errors, uff and after 10th error I closed it.
    <br><br>
    When [I wrote my first Fixer](/blog/2017/07/24/how-to-write-custom-fixer-for-php-cs-fixer-24/), the story was a bit shorter, but very similar. No wonder people don't test when entry barrier is so huge.
    <br><br>
    **Since I use both of them and I want to motivate people to write own sniffs and fixers, I turned this barrier to just 5 short steps** for both of them.
tweet: "New post on my blog: Test Your First Fixer or Sniff Like a Lazy Pro"
related_items: [46, 47] # first fixer, sniff
---

Imagine you have a `LowerBoolConstantsFixer` that fixes all uppercase bool constants to lower ones:  

```diff
-$value = TRUE;
+$value = true;
```

And nothing more. How do we take this test case to PHPUnit? That what [ECS Tester](https://github.com/Symplify/EasyCodingStandardTester) package will help us with.

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

(*Checker* is group name for sniff and fixer, nothing more.)

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

- Try to keep this standard in every tests to lower the maintenance test, e.g. `__DIR__ . '/config.yml'`.
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

You can make us of 3 testing methods:

- `doTestCorrectFile($correctFile)` - the file should not be affected by this checker
- `doTestWrongToFixedFile($wrongFile, $fixedFile)` - classic before/after testing
- `doTestWrongFile($wrongFile)` - **only for sniff**, that doesn't fix, just reports

<br>

```php
<?php declare(strict_types=1);

namespace Your\CodingStandard\Tests\LowerBoolConstantsFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class LowerBoolConstantsFixerTest extends AbstractCheckerTestCase
{
    public function testCorrectCases(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

    public function testWrongToFixedCases(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
```

## 5. The Best For Last - Create the Code Snippets 

**This part you enjoy the most, because your job is to broke the checker**... well, at least verify it behaves as you want it to behave. 

What should it skip? Well, since `NULL` / `null` is not a bool value...

```php
// correct/correct.php.inc
$value = NULL;
$value = null;
```

I guess the before/after part you already know, so here it is in written form:

```php
// wrong/wrong.php.inc
$value = TRUE;
$value = FALSE;
```

```php
// fixed/fixed.php.inc
$value = true;
$value = false;
```

That's it!


Know you know all you need to be able test any fixer or sniff.
But if you want to know more, check [the ECS Tester README](https://github.com/Symplify/EasyCodingStandardTester).

<br><br>

Enjoy simple testing!
