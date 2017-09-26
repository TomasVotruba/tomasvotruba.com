---
id: 57
layout: post
title: "EasyCodingStandard and PHPStan meets 3 Symfony E-Commerce Projects"
perex: '''
    [In the last post](/blog/2017/08/28/shopsys-spriker-and-sylius-under-static-analysis/), we looked at the static analysis of 3 Symfony E-Commerce projects.

    **Lines of code, Duplicated code, Cyclomatic complexity or Method length**. These metrics are very rarely used in practise (even though there is a [sniff for that](https://github.com/Symplify/Symplify/blob/bf802422b9528946a8bd7e7f0331d858a9bf5740/easy-coding-standard.neon#L27-L28)).

    Today, I am going to show you how you can check them with tools that can help you keep your code better on daily basis - EasyCodingStandard and PHPStan.
'''
tweet_: "..."
related_posts: [52]
---


## Try It Yourself

I've updated [the repository on Github](https://github.com/TomasVotruba/shopsys-spryker-and-sylius-analysis) with both tools, so you can verify all the results in this post locally.

<br>

<div class="col-6 mb-3">
    <a href="https://www.shopsys-framework.com/">
        <img src="/assets/images/posts/2017/shopsys-static-anal/shopsys.png">
    </a>
</div>

<div class="col-5">
    <a href="http://sylius.org/">
        <img src="/assets/images/posts/2017/shopsys-static-anal/sylius.png">
    </a>
</div>

<div class="col-5">
    <a href="https://spryker.com/">
        <img src="/assets/images/posts/2017/shopsys-static-anal/spryker.png">
    </a>
</div>


## Why These Tools?

When a project uses coding standards - moreover CI-checkers - it is very easy to contribute to it. **I don't have to be afraid a PR gets rejected or at least postponed due to a wrong bracket position** (yea, that happens). 

Second, [PHPStan](https://github.com/phpstan/phpstan) is the best tool when it comes to **passing type validations** (arrays of objects, like `Type[]`), **incorrect namespaces**, **calling non-existing methods** [and more](https://medium.com/@ondrejmirtes/phpstan-2939cd0ad0e3#b18f).

To get the idea how it improves your code in practice, just [check this commit](https://github.com/ApiGen/ApiGen/commit/9ab5d1f94e95ac91a6cf2d0edd1d0c384f6299d7) in [ApiGen](/blog/2017/09/04/how-apigen-survived-its-own-death/).



## Coding Style Violations with EasyCodingStandard

### PSR2

**PSR-2 is the most spread coding standard in PHP**, described in [PHP-FIG guide](http://www.php-fig.org/psr/psr-2/). Both PHP_CodeSniffer and PHP-CS-Fixer have a set of ~30 rules, so why not to [combine them](https://github.com/Symplify/EasyCodingStandard/blob/master/config/psr2-checkers.neon) and use them on our projects?


**Shopys**

- 0 errors


**Spryker**

- 9723 errors - all fixable

 
**Sylius**

- 133 errors - all are fixable


### What Would Be the Takeaways?

If you’re used to PSR2 style, you probably use PHPStorm to check it and **contributing to Sylius would mean no extra work for you**. [Sylius' ruleset](https://github.com/Sylius/Sylius/blob/b5f6a4e4383fcbf5b1b9730094d1e1aa756de7a2/etc/phpcs/.php_cs) contains just handful of checkers though.

Spryker has [own package just for coding-standard](https://github.com/spryker/code-sniffer) build on PHP_CodeSnifer, but they don't comply with some rules of PHP-CS-Fixer. **This might be issue if you're in the Symfony world** and you use that tool in your projects.

Shopsys might have more errors than Sylius, **but it also has own [coding-standard package](https://github.com/shopsys/coding-standards) build on both tools** - PHP-CS-Fixer and PHP_CodeSniffer. I'd say that's the best solution, because you have **the least responsibility as a contributor**. CI tools will handle the code style for you.



## The Four Cleaners

This is another [small and easy-to-understand set](/blog/2017/09/18/4-simple-checkers-for-coding-standard-haters-but-clean-code-lovers/) I use to keep the code of open-source packages clean. In Sylius [it removed 500 lines of unused code](https://github.com/Sylius/Sylius/pull/8557) just few days ago as of writing this article.

The full set looks like this:


```yaml
checkers:
    # use short array []
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer: 
        syntax: short
    # drop dead code
    - SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff 
    # drop dead use namespaces
    - PhpCsFixer\Fixer\Import\NoUnusedImportsFixer 
    # and sort them A→Z
    - PhpCsFixer\Fixer\Import\OrderedImportsFixer 
```

And results for our 3 projects?

**Shopsys**

- 39 errors - 1 is fixable


**Spryker**

- 97 errors - 89 are fixable


**Sylius**

- 7 errors - 4 are fixable



## Code Violations with PHPStan

**There are 8 levels in PHPStan to this day** - from 0 to 7. Level 0 being *the starter* and 7 *the Mission Impossible* for most projects.

If you never used this tools before, **you probably don’t know how strict PHPStan is**.
Doctrine2 has now level 1 and successfully passes it, thanks to [Majkl578](https://github.com/Majkl578). In order to get the idea of what had to be done, [see the PR](https://github.com/doctrine/doctrine2/pull/6535/files.

Do you want to add PHPStan to your project? [Read this short intro](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/).


@todo image

To have an idea about real numbers, **I picked results for lvl 0 and lvl 7**:

@todo table


As you can see, both **Shopsys and Sylius are doing great**. Sylius is just falling bit behind in higher levels.

As we have seen in [the previous article](/blog/2017/08/28/shopsys-spriker-and-sylius-under-static-analysis/), **not all the projects are of the same size**. It might not be fair to Spryker to count only PHPStan violations as its codebase is considerably larger. **If count violations relative to the size of the project** (measured in lines of code) the graph changes significantly:

@todo another image


**Results for lvl 0 and lvl 7**:


@todo table


This point of view shows that, on the less strict levels of analysis, **Sylius and Shopsys are ahead of Spryker in terms of PHPStan violations**. During the second level of analysis this changes and number of problems found notably rises, only **Shopsys stands out** with less than a half of the issues of both other frameworks. This trend does not change even while moving tothe higher levels of scrutiny from mr. PHPStan.

Shopsys has long way to passing level 7 with 0 errors, but it has still [some time till the release](https://blog.shopsys.com/we-have-started-with-our-regular-releases-public-beta-coming-soon-c1f879657bd4) to improve this.

Happy coding!
