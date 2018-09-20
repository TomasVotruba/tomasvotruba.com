---
id: 37
title: "Combine power of PHP_CodeSniffer and PHP CS Fixer in 3 lines"
perex: |
    PHP_CodeSniffer has over **5 381 stars** on Github and **210 default sniffs**,
    PHP CS Fixer with **6 467 stars** brings you **160 fixers**.
    <br><br>
    Both powerful tools dealing with coding standards with **huge communities behind them**.
    Can you imagine using them both and actually enjoy it? Today I will show you how.
related_items: [46, 47, 48]
tweet: "#ecs - tool to use both #phpCodeSniffer and #phpCsFixer in 3 lines #php #codingstandard"

updated: true
updated_since: "September 2018"
updated_message: |
    Updated with <a href="https://github.com/Symplify/Symplify/blob/master/CHANGELOG.md#v400---2018-04-02">ECS 4.0</a>, Neon to YAML migration and <code>checkers</code> to <code>services</code> migration.
---

<div class="text-center">
    <img src="/assets/images/posts/2017/easy-coding-standard-intro/together.png" class="img-thumbnail">
</div>

### Right to The Answer

Let's say we want to check arrays. We add first *checker* that requires short PHP 5.4 `[]` syntax:

```yaml
# ecs.yml
services:
    PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff: ~
```


Great start. Then we want to check for trailing commas, so every line has them.

So add one more checker:

```yaml
# ecs.yml
services:
    PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff: ~
    PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer: ~
```

Great job! **You have just combined PHP_CodeSniffer and PHP CS Fixer in 3 lines.**

With a help of [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard). Now, when title promise is fulfilled, I will show how to install it, run it and how nice and clear reports it generates.

## How to add EasyCodingStandard in 3 steps

### 1. Install Package

```bash
composer require symplify/easy-coding-standard --dev
```

### 2. Configure

Create a `ecs.yml` file in your project and desired checkers.

```yaml
services:
    # arrays
    PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff: ~
    PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer: ~
```

You can add a comment to groups, so everyone can easily orientate when there are more checkers.

### Be Lazy with YAML

Do you use PHPStorm? If so, you can use [Symfony Plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin). It allows you one amazing thing:

<div class="text-center">
    <img src="https://github.com/Symplify/EasyCodingStandard/raw/master/docs/yaml-autocomplete.gif" class="img-thumbnail">
</div>

**It autocompletes class names!**

No more looking to documentation, what string matches what sniff or fixer, if there are any checkers for arrays or debugging typos.

### 3. Run it & Fix it

```bash
vendor/bin/ecs check src

# ...

vendor/bin/ecs check src --fix
```

<div class="text-center">
    <img src="/assets/images/posts/2017/easy-coding-standard-intro/run-and-fix.gif" class="img-thumbnail">
</div>

That's all!


Well, unless you like videos...

## Watch 🕑 11 min Intro Talks from Dresden PHP Meetup

I spoke about ECS last week in Dresden. **If you have 11 minutes and you want to know more about it, [go watch it here it here](https://www.facebook.com/pehapkari/videos/vl.1877987242460289/1321227224593751/?type=1).**

That's all for short intro of this tool. I'll post more articles about how to use it, about fast caching or how to write own checkers - both sniffs and fixers.

**Thank you for any feedback.** Here or in the issues.
