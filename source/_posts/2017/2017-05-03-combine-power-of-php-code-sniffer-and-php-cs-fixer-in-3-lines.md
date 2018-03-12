---
id: 37
title: "Combine power of PHP_CodeSniffer and PHP CS Fixer in 3 lines"
perex: |
    PHP_CodeSniffer has over <strong>3 400 stars</strong> on Github and <strong>210 default sniffs</strong>,
    PHP CS Fixer with <strong>4 423 stars</strong> brings you <strong>142 fixers</strong>.
    <br><br>
    Both powerful tools dealing with coding standards with <strong>huge communities behind them</strong>.
    Can you imagine using them both and actually enjoy it? Today I will show you how.
related_items: [46, 47, 48]
tweet: "#ecs - tool to use both #phpCodeSniffer and #phpCsFixer in 3 lines #php #codingstandard"
---

<div class="text-center">
    <img src="/assets/images/posts/2017/easy-coding-standard-intro/together.png" class="img-thumbnail">
</div>


### Right to The Answer

Let's say we want to check arrays.

We add first *checker* that requires long `array()` syntax:

```yaml
checkers:
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff
```


Great start. Then we want to check for trailing commas, so every line has them.

So add one more checker:

```yaml
checkers:
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff
    - PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer
```

Great job! **You have just combined PHP_CodeSniffer and PHP CS Fixer in 3 lines.**

With a help of [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard). Now, when title promise is fulfilled,
I will show how to install it, run it and how nice and clear reports it generates.

## How to add EasyCodingStandard in 3 steps

### 1. Install Package

```bash
composer require --dev symplify/easy-coding-standard
```

### 2. Add `easy-coding-standard.neon` and Configure

Create a `easy-coding-standard.neon` file in your project and desired checkers.

```yaml
checkers:
    # arrays
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff
    - PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer
```

You can add a comment to groups, so everyone can easily orientate when there are more checkers.


### Be Lazy with NEON

Do you use PHPStorm? If so, you can use [NEON Plugin](https://plugins.jetbrains.com/plugin/7060-neon-support). It allows you one amazing thing:

<div class="text-center">
    <img src="/assets/images/posts/2017/easy-coding-standard-intro/neon-autocomplete.gif" class="img-thumbnail">
</div>


**It autocompletes class names!**

No more looking to documentation, what string matches what sniff or fixer, if there are any checkers for arrays or debugging typos.

[NEON file format](https://ne-on.org/) is very similar to YAML.

*To install NEON PHPStorm plugin just: find everywhere → type "Plugins" → pick "Browse repositories..." in the bottom → type "NEON" and install it.*

### 3. Run it & Fix it

```bash
vendor/bin/easy-coding-standard check src

# ...

vendor/bin/easy-coding-standard check src --fix
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
