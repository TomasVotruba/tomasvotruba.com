---
id: 152
title: "Brief History of Tools Watching and Changing Your PHP Code"
perex:
    From coding standard tools, over static analysis to instant upgrade tools. This post is going to be a geeky history trip.

    Which tool was first? How they **build on shoulders of each other**?

updated_since: "December 2020"
updated_message: "Removed deprecated and unused tools, to keep focus on relevant ones."
---

## 1. Coding Standard Tools

### [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) from Australia

<span class="badge badge-light">Tokens</span>
<span class="badge badge-success">Modifies Code</span>

The first tool that made it to the mainstream of coding standard tools was created around **2007** by Greg Sherwood from Australia. At least by date [of the oldest post I could found](http://gregsherwood.blogspot.com/2006/12/if-not-test-first-then-test-really-soon.html).

Greg has been maintaining the repository for last **11 years** (at least). I don't know anyone else who would take care of his project for such a long time without stepping back - **much respect**!

### [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) from Symfony

<span class="badge badge-light">Tokens</span>
<span class="badge badge-success">Modifies Code</span>

Reporting errors are helpful, but fixing them for you is even more helpful. More people got a similar need and around [Symfony 2.0 times and 2011](https://gist.github.com/fabpot/3f25555dce956accd4dd) next tools were born - PHP CS Fixer.

[The first version](https://gist.github.com/fabpot/3f25555dce956accd4dd) was created by Fabien Potentier, author and founder of Symfony, and it used mostly regular expressions.

The decision to **fix everything by default was the huge jump in history** of these tools. It was the first case of a tool that would be so bold to change your code for you. You had to believe it, you had to overcome the fear of modifying it the wrong way or even deleting. I mean, now we're used to it like to cars, but at one point of history, they were just riding bombs.

<br>

With [PHP CS Fixer 1.0 release in 2014](http://fabien.potencier.org/php-cs-fixer-finally-reaches-version-1-0.html) and rising popularity of **automated fixes** was **big motivation for PHP_CodeSniffer** to add similar feature - [*code beautifier*](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically) - to version 2.

It also helped with another issue. The development of PHP_CodeSniffer 2.8 and later almost froze to zero. I remember because I started working on [EasyCodingStandard](https://github.com/symplify/easy-coding-standard) right between
PHP_CodeSniffer 2.8 and 3.0 (depending on `3.0-dev`), which took 14 uncertain months.

**So PHP_CodeSniffer now holds <span class="badge badge-success">Modifies Code</span> as well.**

<br>

Both use `token_get_all()` that basically parses code to strings. Do you want to know [how they actually work](/blog/2017/07/31/how-php-coding-standard-tools-actually-work/)?

### [EasyCodingStandard](https://github.com/symplify/easy-coding-standard) from the Czech Republic

<span class="badge badge-light">Tokens</span>
<span class="badge badge-success">Modifies Code</span>

I saw many projects that use both tools, yet very poorly - because split attention divides the focus in the same ratio. The mission of this tool is **to help new generations to adopt coding standard with almost no effort**. So in **2016** the EasyCodingStandard was born.

## 2. Static Analysis Tools

### [nikic/php-parser](https://github.com/nikic/PHP-Parser)

<span class="badge badge-danger">AST</span>

This package is barely known for code analysis. But it provides the technology that all the other tool builds on - *abstract syntax tree* (known as *AST*).

It all started as a question on [StackOverflow](https://stackoverflow.com/questions/5586358/any-decent-php-parser-written-in-php) - *Any decent PHP parser written in PHP?* nikic answered himself with a [`php-parser`](https://github.com/nikic/PHP-Parser) less than a 6 months later.

I would not write this post and neither have my fuel for passion without this tool, so **huge thank you, Nikita, for creating it and maintaining it**.

<br>

All following tools use an **AST analyzer** - that's how they know what object is `$object` variable, like in this example:

```php
<?php

class SomeObject
{
    public function exist()
    {
    }
}

$object = new SomeObject; // AST remembers that "$object" is "SomeObject" type
$object->missing(); // here we know that "missing" does not exist in "SomeObject"
```

### [PHPStan](https://github.com/phpstan/phpstan) by Ondrej Mirtes

<span class="badge badge-danger">AST</span>
<span class="badge badge-info">
    <a href="https://github.com/phpstan/phpstan/releases/tag/0.1">* 2016</a>
</span>

If you don't use any static analysis tool, give PHPStan a try. I've made [minimalist intro that will help you with first steps ](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/). It's worth investing even day or two to set it up because **these tools will join toolkit of everyday use**, like Composer or PHPUnit.

### [vimeo/psalm](https://github.com/vimeo/psalm) by Matthew Brown

<span class="badge badge-danger">AST</span>
<span class="badge badge-info">
    <a href="https://github.com/vimeo/psalm/releases/tag/0.1">* 2016</a>
</span>
<span class="badge badge-success">Modifies Code</span>

Psalm is a very interesting tool that was born to fight Vimeo code complexity. It was the first tool from this group of 3, **that started [fixing the code](https://psalm.dev/docs/manipulating_code/fixing/)**.

## 3. Deprecation Detectors

This group is widely used in the framework-agnostic PHP community.

### [PHPCompatibility/PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibility)

<span class="badge badge-light">Tokens</span>

This tool checks for PHP cross-version compatibility. It allows you to analyze your code for compatibility with higher and lower versions of PHP.

<br>

It's useful to know what places are wrong, but you still have to fix them all manually.

## 4. Instant Upgrade Tools

### [umpirsky/Symfony-Upgrade-Fixer](https://github.com/umpirsky/Symfony-Upgrade-Fixer)

<span class="badge badge-light">Tokens</span>
<span class="badge badge-success">Modifies Code</span>
<span class="badge badge-secondary">Deprecated</span>

This tool was created in **2015** and it's revolutionary. Why? It wisely connects the token analysis of PHP CS Fixer with deprecations in Symfony 2 to 3. Imagine it like PHP CS Fixer + `sensiolabs-de/deprecation-detector` **working for you**.

Isn't that amazing? **You just sit, run this tool and send invoices. Genius!** The features are limited due to Tokens, but still, I love this.

### [Rector](https://github.com/rectorphp/rector)

<span class="badge badge-danger">AST</span>
<span class="badge badge-success">Modifies Code</span>

It's **2017** and Rector still had to wait many months to be born. For what?

Well, it's built on php-parser and as it modifies the code and prints it back, it **needed to keep spacing**. That's one of AST drawbacks - it doesn't care about all that coding standards spacings.

They say "history repeats", but I never trusted that. Untill I saw that similar need Fabien had while making the PHP CS Fixer in 2012 - [Optionally add nodes for whitespace](https://github.com/nikic/PHP-Parser/issues/41). More **people wanted AST-based coding standards**:

<blockquote class="twitter-tweet" data-lang="cs"><p lang="en" dir="ltr"><a href="https://twitter.com/fabpot?ref_src=twsrc%5Etfw">@fabpot</a> zendframework, though we are moving afay from php-cs-fixer because it is not AST-based</p>&mdash; Supervising Program (@Ocramius) <a href="https://twitter.com/Ocramius/status/532622405290971136?ref_src=twsrc%5Etfw">12. listopadu 2014</a></blockquote>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

php-parser `4.0-dev` already had this feature, so Rector ran on it since the very start. It was not until [**February 2018**](https://github.com/nikic/PHP-Parser/releases/tag/v4.0.0) when it was finally released.

Last giant Rector builds on is the one for type analysis - PHPStan. Thanks to that, it **doesn't reinvent the wheel and can focus on the refactoring part**.

<br>

And that's a brief history of big-brother tools that watch your code and modify it for you.
