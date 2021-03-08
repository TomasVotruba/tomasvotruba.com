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

updated_since: "August 2020"
updated_message: |
    Updated with **ECS 5**, Neon to YAML migration and `checkers` to `services` migration.<br>
    Updated ECS YAML to PHP configuration since **ECS 8**.
---

There are some checkers in coding standard world, that don't check spaces, tabs, commas nor brackets. They **actually do code-review for you**.

I use a set of 4 checkers to **check open-source packages to help them keeping their code clean**.

In Sylius they [removed 500 lines of unused code](https://github.com/Sylius/Sylius/pull/8557) just few days ago.

Among others it **removed dead constructor dependencies**.

<img src="/assets/images/posts/2017/clean-checkers/dependency-drop.png" class="img-thumbnail">

It will not only make your code cleaner, but also can **speed up you container build** as a side effect.

## 4 Simple Checkers

```php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // use short array []
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [['syntax' => 'short']]);

    // drop dead code
    $services->set(UnusedPrivateElementsSniff::class);

    // drop dead use namespaces
    $services->set(NoUnusedImportsFixer::class);

    // and sort them A → Z
    $services->set(OrderedImportsFixer::class);
};
```

## 4 Steps to Make Your Code Cleaner

1. Install it

    ```bash
    composer require symplify/easy-coding-standard --dev
    ```

2. Add checkers to `ecs.php` file

3. Check your code

    ```bash
    vendor/bin/ecs check src
    ```

4. Fix the code

    ```bash
    vendor/bin/ecs check src --fix
    ```

Happy coding!
