---
id: 55
title: "4 Simple Checkers for Coding Standard Haters but Clean Code Lovers"
perex: |
    Do you find coding standards too **annoying in telling you where to put that bracket**?
    Is that the reason you haven't tried them yet?
    <br><br>
    Great! This post is for you. There are [other ways to use coding standard](/blog/2017/07/31/how-php-coding-standard-tools-actually-work/#write-1-checke-save-hundreds-hours-of-work) and **clean code** is one of them.
tweet: "Do you hate Coding Standards, but love #cleancode? Check these 4 helpful rules #php"
tweet_image: "/assets/images/posts/2017/clean-checkers/dependency-drop.png"
related_items: [51, 48]

updated_since: "December 2018"
updated_message: |
    Updated with <strong>EasyCodingStandard 5</strong>, Neon to YAML migration and <code>checkers</code> to <code>services</code> migration.
---

There are some checkers in coding standard world, that don't check spaces, tabs, commas nor brackets. They **actually do code-review for you**.


I use a set of 4 checkers to **check open-source packages to help them keeping their code clean**.

In Sylius they [removed 500 lines of unused code](https://github.com/Sylius/Sylius/pull/8557) just few days ago.

Among others it **removed dead constructor dependencies**.

<img src="/assets/images/posts/2017/clean-checkers/dependency-drop.png" class="img-thumbnail">

It will not only make your code cleaner, but also can **speed up you container build** as a side effect.



## 4 Simple Checkers


```yaml
# ecs.yml
services:
    # use short array []
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short

    # drop dead code
    SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff: ~

    # drop dead use namespaces
    PhpCsFixer\Fixer\Import\NoUnusedImportsFixer: ~

    # and sort them A → Z
    PhpCsFixer\Fixer\Import\OrderedImportsFixer: ~
```

## 4 Steps to Make Your Code Cleaner

1. Install it

    ```bash
    composer require symplify/easy-coding-standard --dev
    ```

2. Add checkers to `ecs.yml` file

3. Check your code

    ```bash
    vendor/bin/ecs check src
    ```

4. Fix the code

    ```bash
    vendor/bin/ecs check src --fix
    ```

Happy coding!
