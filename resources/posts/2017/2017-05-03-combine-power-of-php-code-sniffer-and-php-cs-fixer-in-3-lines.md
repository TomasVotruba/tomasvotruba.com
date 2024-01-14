---
id: 37
title: "Combine power of PHP_CodeSniffer and PHP CS Fixer in 3 lines"
perex: |
    At time of publishing this article, PHP_CodeSniffer has over **5 381 stars** on Github and **210 default sniffs**,
    PHP CS Fixer with **6 467 stars** brings you **160 fixers**.

    Both powerful tools dealing with coding standards with **huge communities behind them**.
    Can you imagine using them both and actually enjoy it? Today I will show you how.

updated_since: "January 2023"
updated_message: |
    Updated with ECS 12 and `ECSConfig::configure()` simple way to work with configs.
---

<div class="text-center">
    <img src="/assets/images/posts/2017/easy-coding-standard-intro/together.png" class="img-thumbnail">
</div>

### Right to The Answer

Let's say we want to check arrays. We add first *checker* that requires short PHP 5.4 `[]` syntax:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;

return ECSConfig::configure()
    ->withRules([
        DisallowLongArraySyntaxSniff::class,
    ]);
```

Great start. Then we want to check for trailing commas, so every line has them.

So add one more checker:

```php
// ecs.php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;

return ECSConfig::configure()
    ->withRules([
        DisallowLongArraySyntaxSniff::class,
        TrailingCommaInMultilineArrayFixer::class,
    ]);
```

Great job! **You have just combined PHP_CodeSniffer and PHP CS Fixer in 3 lines.**

With a help of [ECS](https://github.com/symplify/easy-coding-standard). Now, when title promise is fulfilled, I will show how to install it, run it and how nice and clear reports it generates.

## How to add ECS in 3 steps

### 1. Install Package

```bash
composer require symplify/easy-coding-standard --dev
```

### 2. Configure

Create a `ecs.php` file in your project and desired checkers as above.

You can add a comment to groups, so everyone can easily orientate when there are more checkers.

### Be Lazy with PHP

Do you use PHPStorm? Just use PHP to autocomplete everything as you're used to since ECS 8.

**No more looking to documentation**, what string matches what sniff or fixer, if there are any checkers for arrays or debugging typos.

### 3. Run it

```bash
vendor/bin/ecs check src
```

### 4. Fix it


```bash
vendor/bin/ecs check src --fix
```

<div class="text-center">
    <img src="/assets/images/posts/2017/easy-coding-standard-intro/run-and-fix.gif" class="img-thumbnail">
</div>

That's all for short ECS intro.

<br>

Do you want to know more? Learn [how to write own sniff](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/) or [even better - a fixer](/blog/2017/07/24/how-to-write-custom-fixer-for-php-cs-fixer-24/).

Happy coding!
