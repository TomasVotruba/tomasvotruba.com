---
id: 57
title: "EasyCodingStandard and PHPStan meet 3&nbsp;Symfony E-Commerce Projects"
perex: |
    [In the last post](/blog/2017/08/28/shopsys-spriker-and-sylius-under-static-analysis/), we looked at the static analysis of 3 Symfony E-Commerce projects.

    **Lines of code, Duplicated code, Cyclomatic complexity or Method length**. These metrics are very rarely used in practise (even though there is a [sniff for that](https://github.com/Symplify/Symplify/blob/bf802422b9528946a8bd7e7f0331d858a9bf5740/easy-coding-standard.neon#L27-L28)).

    Today, I am going to show you how you can check them with tools that can help you keep your code better on daily basis - [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) and [PHPStan](https://github.com/phpstan/phpstan).
tweet: "When EasyCodingStandard and @phpstan meet 3 #symfony e-commerce projects #numbers #php"
tweet_image: "/assets/images/posts/2017/shopsys-static-anal-2/phpstan-relative.png"
related_items: [52]

updated: true
updated_since: "April 2018"
updated_message: |
    Updated with <a href="https://github.com/Symplify/Symplify/blob/master/CHANGELOG.md#v400---2018-04-02">ECS 4.0</a>, Neon to YAML migration and <code>checkers</code> to <code>services</code> migration.
---

## Try It Yourself

I've updated [the repository on Github](https://github.com/TomasVotruba/shopsys-spryker-and-sylius-analysis) with both tools, so you can verify all the results in this post locally.

<br>

<div class="col-6 mb-3">
    <a href="https://www.shopsys.com/">
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


*(Tip: logos lead to projects' landing pages. Be sure to explore.)*



## Why These Tools?

When a project uses coding standards - moreover CI-checkers - it is very easy to contribute to it. **I don't have to be afraid a PR gets rejected or at least postponed due to a wrong bracket position** (yea, that happens).

Second, [PHPStan](https://github.com/phpstan/phpstan) is the best tool when it comes to **passing type validations** (arrays of objects, like `Type[]`), **incorrect namespaces**, **calling non-existing methods** [and more](https://medium.com/@ondrejmirtes/phpstan-2939cd0ad0e3#b18f).

To get the idea how it improves your code in practice, just [check this commit](https://github.com/ApiGen/ApiGen/commit/9ab5d1f94e95ac91a6cf2d0edd1d0c384f6299d7) in [ApiGen](/blog/2017/09/04/how-apigen-survived-its-own-death/).



## Coding Style Violations with EasyCodingStandard

### PSR2

**PSR-2 is the most spread coding standard in PHP**, described in [PHP-FIG guide](https://www.php-fig.org/psr/psr-2/). Both PHP_CodeSniffer and PHP CS Fixer have a set of ~30 rules, so why not to [combine them](https://github.com/Symplify/EasyCodingStandard/blob/master/config/psr2.yml) and use them on our projects?


<br>


<table class="table table-bordered table-responsive">
    <thead class="thead-inverse">
        <tr>
            <th>Shopsys</th>
            <th>Spryker</th>
            <th>Sylius</th>
        </tr>
    </thead>
    <tr>
        <td>0 errors</td>
        <td>9 723 errors</td>
        <td>133 errors</td>
    </tr>
</table>

<br>

### What Would Be the Takeaways?

If you’re used to PSR2 style, you probably use PHPStorm to check it and **contributing to Sylius would mean no extra work for you**. [Sylius' ruleset](https://github.com/Sylius/Sylius/blob/b5f6a4e4383fcbf5b1b9730094d1e1aa756de7a2/etc/phpcs/.php_cs) contains just handful of checkers though.

Spryker has [own package just for coding-standard](https://github.com/spryker/code-sniffer) build on PHP_CodeSnifer, but they don't comply with some rules of PHP CS Fixer. **This might be issue if you're in the Symfony world** and you use that tool in your projects.

Shopsys **has its own [coding-standard package](https://github.com/shopsys/coding-standards) build on both tools** - PHP CS Fixer and PHP_CodeSniffer - compatible with PSR-2. I'd say that's the best solution, because you have **the least responsibility as a contributor**. CI tools will handle the code style for you.



## The Four Cleaners

This is another [small and easy-to-understand set](/blog/2017/09/18/4-simple-checkers-for-coding-standard-haters-but-clean-code-lovers/) I use to keep the code of open-source packages clean. In Sylius [it removed 500 lines of unused code](https://github.com/Sylius/Sylius/pull/8557) just few days ago as of writing this article.

The full set looks like this:


```yaml
services:
    # use short array []
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short

    # drop dead code
    SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff: ~

    # drop dead use namespaces
    PhpCsFixer\Fixer\Import\NoUnusedImportsFixer: ~

    # and sort them A→Z
    PhpCsFixer\Fixer\Import\OrderedImportsFixer: ~
```

<br>

And results for our 3 projects?

<br>

<table class="table table-bordered table-responsive">
    <thead class="thead-inverse">
        <tr>
            <th>Shopsys</th>
            <th>Spryker</th>
            <th>Sylius</th>
        </tr>
    </thead>
    <tr>
        <td>39 errors</td>
        <td>97 errors</td>
        <td>7 errors</td>
    </tr>
</table>

<br>


## Code Violations with PHPStan

**There are 8 levels in PHPStan to this day** - from 0 to 7. Level 0 being *the starter* and 7 *the Mission Impossible* for most projects.

If you never used this tools before, **you probably don’t know how strict PHPStan is**.
Doctrine2 has now level 1 and successfully passes it, thanks to [Majkl578](https://github.com/Majkl578). In order to get the idea of what had to be done, [see the PR](https://github.com/doctrine/doctrine2/pull/6535/files).

Do you want to add PHPStan to your project? [Read this short intro](/blog/2017/01/28/why-I-switched-scrutinizer-for-phpstan-and-you-should-too/).


<div class="text-center">
    <img src="/assets/images/posts/2017/shopsys-static-anal-2/phpstan.png" class="img-thumbnail">
</div>

<br>

To have an idea about real numbers, **I picked results for lvl 0 and lvl 7**:


<br>

<table class="table table-bordered table-responsive table-striped">
    <tr>
        <thead class="thead-inverse">
            <th>PHPStan levels \ Projects</th>
            <th>Shopsys</th>
            <th>Spryker</th>
            <th>Sylius</th>
        </thead>
    </tr>
    <tr>
        <th>0</td>
        <td>1 406 errors</td>
        <td>7 905 errors</td>
        <td>1 404 errors</td>
    </tr>
    <tr>
        <th>7</td>
        <td>1 413 errors</td>
        <td>12 977 errors</td>
        <td>3 715 errors</td>
    </tr>
</table>

<br>



As you can see, both **Shopsys and Sylius are doing great**. Sylius is just falling bit behind in higher levels.

As we have seen in [the previous article](/blog/2017/08/28/shopsys-spriker-and-sylius-under-static-analysis/), **not all the projects are of the same size**. It might not be fair to Spryker to count only PHPStan violations as its codebase is considerably larger. **If we count violations relative to the size of the project** (measured in lines of code) the graph changes significantly:

<br>

<div class="text-center">
    <img src="/assets/images/posts/2017/shopsys-static-anal-2/phpstan-relative.png" class="img-thumbnail">
</div>

<br>

**Results for lvl 0 and lvl 7**:

<br>

<table class="table table-bordered table-responsive table-striped">
    <tr>
        <thead class="thead-inverse">
            <th>Relative to LOC</th>
            <th>Shopsys</th>
            <th>Spryker</th>
            <th>Sylius</th>
        </thead>
    </tr>
    <tr>
        <th>0</td>
        <td>0.014</td>
        <td>0.021</td>
        <td>0.012</td>
    </tr>
    <tr>
        <th>7</td>
        <td>0.014</td>
        <td>0.035</td>
        <td>0.033</td>
    </tr>
</table>

<br>

This point of view shows that, on the less strict levels of analysis, **Sylius and Shopsys are ahead of Spryker in terms of PHPStan violations**. During the second level of analysis this changes and number of problems found notably rises, only **Shopsys stands out** with less than a half of the issues of both other frameworks. This trend does not change even while moving to the higher levels of scrutiny from mr. PHPStan.

Shopsys has long way to passing level 7 with 0 errors, but it has still [some time till the release](https://blog.shopsys.com/we-have-started-with-our-regular-releases-public-beta-coming-soon-c1f879657bd4) to improve this.

Happy coding!
