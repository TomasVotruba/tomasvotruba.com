---
id: 152
title: "Brief History of Tools Watching and Changing Your PHP Code"
perex:
    From coding standard tools, over static analysis to instant upgrade tools. This post is going to be a geeky history trip.
    <br>
    <br>
    Which tool was first? How they **build on shoulders of each other**?
tweet: "New Post on my Blog: Brief History of Tools Watching and Changing PHP Code #phpcsfixer #phpcodesniffer @phpstan #psalm #phan @sensiolabsde #symfony #rector @cakephp"
---

## 1. Coding Standard Tools

### PHP_CodeSniffer

<span class="badge badge-light">Tokens</span>
<span class="badge badge-warning">Checks Code</span>

The first tool that made it to the mainstream of coding standard tools was created around **2007** by Greg Sherwood from Australia. At least by date [of the oldest post I could found](http://gregsherwood.blogspot.com/2006/12/if-not-test-first-then-test-really-soon.html).

Greg has been maintaining the repository for last **11 years** (at least). I don't know anyone else who would take care for his project for such a long time without stepping back - **much respect**!

### PHP CS Fixer

<span class="badge badge-light">Tokens</span>
<span class="badge badge-warning">Checks Code</span>
<span class="badge badge-success">Modifies Code</span>

Reporting errors are helpful, but fixing them for you is even more helpful. More people got a similar need and around [Symfony 2.0 times and 2011](https://gist.github.com/fabpot/3f25555dce956accd4dd) next tools were born - PHP CS Fixer.

[The first version](https://gist.github.com/fabpot/3f25555dce956accd4dd) was created by Fabien Potentier, author and founder of Symfony, and it used mostly regular expressions.

The decision to **fix everything by default was the huge jump in history** of these tools. It was the first case of a tool that would be so bold to change your code for you. You had to believe it, you had to overcome the fear of modifying it the wrong way or even deleting. I mean, now we're used to it like to cars, but at one point of history, they were just riding bombs.

<br>

With [PHP CS Fixer 1.0 release in 2014](http://fabien.potencier.org/php-cs-fixer-finally-reaches-version-1-0.html) and rising popularity of **automated fixes** was **big motivation for PHP_CodeSniffer** to add similar feature - [*code beautifier*](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically) - to version 2.

It also helped with another issue. The development of PHP_CodeSniffer 2.8 and later almost froze to zero. I remember because I started working on [EasyCodingStandard](https://github.com/symplify/easyCodingStandard/) right between
PHP_CodeSniffer 2.8 and 3.0 (depending on `3.0-dev`), which took 14 uncertain months.

**So PHP_CodeSniffer now holds <span class="badge badge-success">Modifies Code</span> as well.**

<br>

Both use `token_get_all()` that basically parses code to strings. Do you want to know [how they actually work](/blog/2017/07/31/how-php-coding-standard-tools-actually-work/)?

### EasyCodingStandard

<span class="badge badge-light">Tokens</span>
<span class="badge badge-warning">Checks Code</span>

I saw many projects that use both tools, yet very poorly - because split attention divides the focus in the same ratio. The mission of this tool is **to help new generations to adopt coding standard with almost no effort**. So in **2016** the EasyCodingStandard was born.

## 2. Static Analysis Tools

### nikic/php-parser

<span class="badge badge-danger">AST</span>

This is a package that is barely known for direct code analysis. But all following depend on it thanks to *abstract syntax tree* (known as *AST*; it's much simpler than it sounds).

It all started as a question on [StackOverflow](https://stackoverflow.com/questions/5586358/any-decent-php-parser-written-in-php) - *Any decent PHP parser written in PHP?* nikic answered himself with a [`php-parser`](https://github.com/nikic/PHP-Parser) less than a half year later.

I would not write this post and neither have my fuel for passion without this tool, so **huge thank you, Nikita, for creating it and maintaining it**.

### PHPStan, Phan, Psalm

<span class="badge badge-danger">AST</span>
<span class="badge badge-warning">Checks Code</span>

- [PHPStan](https://github.com/phpstan/phpstan) by Ondrej Mirtes
- [Phan](https://github.com/phan/phan) by Rasmus Lerdorf
- [Psalm](https://github.com/vimeo/psalm) by Matthew Brown

In my knowledge and according to [Github](https://github.com/phan/phan/releases/tag/0.1), [0.1](https://github.com/phpstan/phpstan/tree/0.1) [tags](https://github.com/vimeo/psalm/releases/tag/0.1), all have been published around **2015/2016**.

All these tools use an **AST analyzer** - that's how they know you're calling `$object->undefined()`. Apart `php-parser`, there is [Microsoft/tolerant-php-parser](https://github.com/Microsoft/tolerant-php-parser). His big advantage is that it can work with incomplete code. Thanks to such feature it's great for IDE autocompletion, e.g. [phpactor](https://github.com/phpactor/phpactor) for VIM.

**The downloads speaks for themselves**:

- [66 000 000](https://packagist.org/packages/nikic/php-parser/stats) vs
- [159 000](https://packagist.org/packages/microsoft/tolerant-php-parser/stats) in favor of php-parser.

If you don't use any of these, give at least PHPStan a try. I've made [minimalist intro just for you](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/). The overhead is worth it to overcome since **these tools will become more natural to use**, like `composer` is now today.

### Deprecation Detectors

<span class="badge badge-danger">AST</span>
<span class="badge badge-warning">Checks Code</span>

Last group that is widely used mainly in general PHP community (not-framework-bounded):

- Do you want to **upgrade your PHP code**? What are deprecations in it? [PHP 7 Compatibility Checker](https://github.com/sstalle/php7cc) tells you
- Do you **migrate from Symfony 2 to 3**? [sensiolabs-de/deprecation-detector](https://github.com/sensiolabs-de/deprecation-detector)

It's useful to know what places are wrong, but it still only reports these changes.

## 3. Instant Upgrade Tools

### umpirsky/Symfony-Upgrade-Fixer

<span class="badge badge-light">Tokens</span>
<span class="badge badge-success">Modifies Code</span>

This tool was created in **2015** and it's revolutionary. Why? It wisely connects token analysis of PHP CS Fixer with deprecations in Symfony 2 to 3. Imagine it like PHP CS Fixer + `sensiolabs-de/deprecation-detector` **working for you**.

Isn't that amazing? **You just sit, run this tool and send invoices. Genius!** The features are limited due to Tokens, but still, I love this.

### cakephp/upgrade

<span class="badge badge-success">Modifies Code</span>

It's completely natural, that frameworks that evolve fast need a tool that helps their users to migrate.
CakePHP is jumping faster and faster recent years, they made their own tool. It's based on regular expressions.

### silverstripe-upgrader

<span class="badge badge-danger">AST</span>
<span class="badge badge-success">Modifies Code</span>

SilverStripe is Australian CMS written in PHP. Its logic is very similar to Rector and I like it. It's also quite small, so the code is easy to understand. Be sure to check it!

### Rector

<span class="badge badge-danger">AST</span>
<span class="badge badge-success">Modifies Code</span>

It's **2017** and Rector had to wait to be born. For what?

Well, it's built on php-parser and as it modifies the code and prints it back, it **needed to keep spacing**. That's one of AST drawbacks - it doesn't care about all that coding standards spacings.

They say "history repeats", but I never trusted that. Untill I saw that similar need Fabien had while making the PHP CS Fixer in 2012 - [Optionally add nodes for whitespace](https://github.com/nikic/PHP-Parser/issues/41). More **people wanted AST-based coding standards**:

<blockquote class="twitter-tweet" data-lang="cs"><p lang="en" dir="ltr"><a href="https://twitter.com/fabpot?ref_src=twsrc%5Etfw">@fabpot</a> zendframework, though we are moving afay from php-cs-fixer because it is not AST-based</p>&mdash; Supervising Program (@Ocramius) <a href="https://twitter.com/Ocramius/status/532622405290971136?ref_src=twsrc%5Etfw">12. listopadu 2014</a></blockquote>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

php-parser `4.0-dev` already had this feature, so Rector ran on it since the very start. It was not until [**February 2018**](https://github.com/nikic/PHP-Parser/releases/tag/v4.0.0) when it was finally released.

Last giant Rector builds on is the one for type analysis - PHPStan. Thanks to that, it **doesn't reinvent the wheel and can focus on the refactoring part**.

<br>

And that's a brief history of big-brother tools that watch your code and modify it for you.
