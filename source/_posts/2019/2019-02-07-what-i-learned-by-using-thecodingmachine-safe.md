---
id: 184
title: "What I Learned by Using thecodingmachine/safe"
perex: |
      [*Safe*](https://thecodingmachine.io/introducing-safe-php) replaces PHP native functions like `file_get_contents` with `Safe\file_get_contents`. Native functions return `false` on fail, but *Safe* throw exception instead.
      <br><br>
      "Good idea", I though, so I tried the package myself in Symplify and Rector.

tweet: "New Post on #php 🐘 blog: What I Learned by Using thecodingmachine/safe #php #phpgems #nettefw #symfony"
---

I'm a big fan of [instant personal experience](/blog/2018/12/06/dont-learn-to-code/) over over-thinking. I didn't know if this package would be useful for me, so I tried it. My code is different from yours, so your experience might be different.

<blockquote class="blockquote text-center">
    If you don't know, just try it.
</blockquote>


**This is my experience after 2 months of using Safe**.

## What I like 😍

- Less potential errors. I don't have to think about `false` verification. Mainly on Windows, the paths can fail, because of the `\` vs `/` problem, absolute paths not starting with `/` and different end of lines.

- Less PHPStan reporting with [Safe rule](https://github.com/thecodingmachine/phpstan-safe-rule). Not sure if the package is useful without it since there is no CI control to tell you where you missed the *Safe* version of a function.

## What I didn't like 🙁

### Function Autoloading Sucks in PHP

Function autoloading has much worse support than PSR-4 class autoloading. 3rd party tool is not ready for it, because it's very rare out in the wild. I personally don't know about any other function-based package.

I got stuck with building prefixed `rector.phar` for a week. See [humbug/box#352](https://github.com/humbug/box/issues/352) for more.

### The API Changes Fast

In 0.11.1 there was added a new function, that caused ci to fail due to PHPStan rule that required to be used for all new functions. I added it to make CI pass. Then 0.11.2 it was removed - PHPStan passed, but function was removed and the code was broken.

BC breaks on patch versions caused Symplify packages to break down. This is allowed on the 0.x version (see [Semver point 4](https://semver.org/)), so it really resulted from my over-trust.

If this was the only issue, **I could solve it with patch-lock** in `composer.json`:

```diff
 {
    "require": {
-       "thecodingmachine/safe": "^0.1.13"
+       "thecodingmachine/safe": "0.1.13"
    }
 }
```

But the combination of PHPStan rule CI fail or code fails forced to upgrade. **Optional use of functions** would be probably better, so I'd drop the PHPStan rule next time. But who would check the need for a *Safe* alternative? Chicken vs egg problem.

### Memory Lock on Every Native Function

This leads me to [memory lock](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) problem.

When I add new native function `array_filter`, should I use the *Safe* version or not? I have to:

- run PHPStan to see the answer
- fix it manually or [better with Rector](https://github.com/thecodingmachine/safe#automated-refactoring)
- import namespace to `Safe\array_filter` with Easy Coding Standard

Before? I just typed `array_filter` <em class="fas fa-fw fa-check text-success fa-lg"></em>

<br>

In the end, I feel it didn't solve any real problems for me, but add huge maintenance cost to my daily workflow. All this leads me to a conclusion:

<blockquote class="blockquote text-center">
    A problem that doesn't exist, doesn't need a solution.
</blockquote>

Hence, I [removed the Safe package](https://github.com/Symplify/Symplify/pull/1409/files) from my workflow.

## What are Real Issues with Native Functions?

### `sprintf`

During the code cleanup, I found this:

```php
<?php

throw new ShouldNotHappenException(
    sprintf('The is problem "%s"', $message, __METHOD__)
);
```

Can you spot the problem? [See code on 3v4l.org](https://3v4l.org/5bmvp).

It reports the `$message`, **but the location of the error - `__METHOD__` is skipped silently**.

I'd expect Safe function to help me exactly with this because this is a real problem. The code doesn't work as supposed to.

### `realpath`

Another real problem I have is `realpath` (it clear from the function name, right? :)):

```php
<?php

$filePath = 'missing_file';

$realFilePath = realpath($filePath);

$fileInfo = new SplFileInfo($realFilePath);

var_dump($fileInfo->getRealPath());
```

Here PHP creates `$fileInfo` object, that might be a file... but is it?

[See code on 3v4l.org](https://3v4l.org/Xflr4).

### `preg_*`

David Grudl wrote about this issue [many years ago](https://phpfashion.com/zradne-regularni-vyrazy-v-php). How to make `preg_*` really safe? He suggests the following:

```php
<?php

function safeReplaceCallback($pattern, $callback, $subject)
{
    // verify callback
    if (! is_callable($callback)) {
        throw new Exception('Invalic callback.');
    }

    // test on empty string
    if (preg_match($pattern, '') === false) { // compilation error?
        $error = error_get_last();
        throw new Exception($error['message']);
    }

    // call PCRE
    $result = preg_replace_callback($pattern, $callback, $subject);

    // execution error?
    if ($result === null && preg_last_error()) {
        throw new Exception('Error during regular execution.', preg_last_error());
    }

    return $result;
}
```

So...

## Is there a Better Way?

Have you read *Hidden Gems of PHP Packages*?

- [Hidden Gems of PHP Packages: Symfony\Finder and SplFileInfo](/blog/2018/08/13/hidden-gems-of-php-packages-symfony-finder-and-spl-file-info/)
- [Hidden Gems of PHP Packages: Nette\Utils](/blog/2018/07/30/hidden-gems-of-php-packages-nette-utils/)

The point is simple - replace native functions with classes methods or objects that:

- handle all possible errors **that specific function**
- **throws tailored informative exception** so you understand what exactly is wrong
- if created, you can be 100% sure it has the value you need (e.g. `SplFileInfo` existing file)

Here are few examples I use in my code:

```diff
-file_get_contents($somePath);
+Nette\Utils\FileSystem::read($somePath);
```

```diff
-preg_match('#Hi (.*?)#', $content);
+Nette\Utils\Strings::match($somePath, '#Hi (.*?)#');
```

```diff
-// 50 % chance the file doesn't exist
-$fileInfo = new SplFileInfo($somePath);
+// throw exception on non-existing file
+$fileInfo = new Symplify\PackageBuilder\FileSystem\SmartFileInfo($somePath);
```

They also make PHPStan happy, because they return `string`, `array`... or throw an exception <em class="fas fa-fw fa-check text-success fa-lg"></em>

<br>

I love [Nette\Utils](https://github.com/nette/utils) and there are more packages like this in the PHP universe. Packages **that use objects you can rely on**.

And if not, just create your own object, that does the job you want like I did with `Symplify\PackageBuilder\FileSystem\SmartFileInfo`. If you know about `Sprintf` object, let me know :).

<br>

<blockquote class="blockquote text-center">
    But remember: <strong>only solve problems that you already have</strong>.
</blockquote>

<br>

Safe coding!
