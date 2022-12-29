---
id: 372
title: "How to release PHP 8.1 and 7.2 package in the Same Repository"
perex: |
    In a post from autumn, we looked at how to [develop packages in monorepo on PHP 8.1 and release downgraded version on PHP 7.2](/blog/how-to-develop-sole-package-in-php81-and-downgrade-to-php72/).
    <br><br>
    But having 2 repository to work with still feels crappy. Which one should we use? Where do people contribute? Where do report issues? Everyone is confused and **time is wasted on explaining complexity**.
    <br>
    <br>
    I knew we can do better... we want **one repository**. Today I'll show you, how to get there with 39 lines of GitHub Action workflow (including comments).
---

When I created a minimal PHPStan packages with specialized rules, I wanted to use it on [every legacy project we upgrade](https://getrector.org/for-companies) we do with Rector upgrade team. The most common lowest PHP for these projects is **PHP 7.2**, so it has to be downgraded.

<br>

A good package is a package that is:

* **simple to develop**
* and **easy to use**

<br>

Let's look at the package I published recently: [TomasVotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity)

## Prepare 2 Build Files

The build files are files, that help use create the downgraded package.

1. The PHP 7.2 `composer.json`

The main `composer.json` requires PHP 8.1, so we can develop with the newest features:

```json
{
    // ...
     "require": {
        "php": "^8.1"
    }
}
```


The PHP 7.2 version is exactly the same, except it requires all the other PHP versions bellow:

```json
{
    // ...
     "require": {
        "php": "^7.2 || 8.0.*",
    }
}
```

The reason is to make package tags idempotent. There must be exactly one tag to choose by composer for any PHP versions.

<br>

2. The `rector.php` with PHP 7.2 downgrade rules:

```php
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_72]);
};
```

<br>

Check the files directly in repository in the [`/build`](https://github.com/TomasVotruba/cognitive-complexity/tree/main/build) directory.

## Create GitHub Action Workflow

You already know, how to downgraded the code Rector. The trick here is, we **downgrade the code in the same repository**, just the `/src` directory, to be exact. Because this is the only code people use, when they get the package via composer.

Here is the 1st part of workflow, that downgrades the code and applies the coding standard:

```yaml
name: Downgraded Release

# this workflow is triggered only on a new tag
on:
    push:
        tags:
            - '*'

jobs:
    downgrade_release:
        runs-on: ubuntu-latest

        steps:
            -   uses: "actions/checkout@v2"

            -
                uses: "shivammathur/setup-php@v2"
                with:
                    php-version: 8.1
                    coverage: none

            -   uses: "ramsey/composer-install@v2"

            # downgrade /src to PHP 7.2
            -   run: vendor/bin/rector process src --config build/rector-downgrade-php-72.php --ansi
            -   run: vendor/bin/ecs check src --fix --ansi

            # copy PHP 7.2 composer
            -   run: cp build/composer-php-72.json composer.json

            # clear the dev files
            -   run: rm -rf build .github tests stubs ecs.php phpstan.neon phpunit.xml
```

<br>

After this workflow is run, we have in the same repository a PHP 7.2 downgraded code. But how do **we release it, so it is available via composer on PHP 7.2 as well**?

<br>

## 3 Lines of Gold

I've done few hours of experimenting and consulting with amazing [Jan Kuchar](https://jankuchar.cz/), till we get the sweet 3 lines, that handle all the job. You're gonna be surprised, how simple this is:

```yaml
            # publish to the same repository with a new tag
            -
                name: "Tag Downgraded Code"
                run: |
                    git commit -a -m "release PHP 7.2 downgraded ${GITHUB_REF#refs/tags/}"
                    git tag "${GITHUB_REF#refs/tags/}.72"
                    git push origin "${GITHUB_REF#refs/tags/}.72"
```

* 1st line - we add and commit PHP 7.2 downgraded files; its still only local in the workflow container, not pushed back to the GitHub repository
* 2nd line - we create a new that, that adds `.72` suffix to the original tag
* 3rd line - we push this tag to the repository

That's it!

<br>

Check the [`workflows/downgraded_release.yaml`](https://github.com/TomasVotruba/cognitive-complexity/blob/main/.github/workflows/downgraded_release.yaml) in the GitHub repository.

### How does that look in Practise?

* I push tag 0.1.0, it is released on PHP 8.1 immediately
* The GitHub workflows notices, "oh there is a new tag, I got work to do"
* The GitHub workflow downgrades the code to PHP 7.2, tags it and pushed it back to the repository - just the tag
* Then 0.1.0.72 tag is released with PHP 7.2 code

## Install Anywhere

Now you can require the package on any of PHP 7.2-8.2 versions and get the right code:

```bash
# php 7.2
composer require tomasvotruba/congnitive-complexity

# php 8.0
composer require tomasvotruba/congnitive-complexity

# php 8.1
composer require tomasvotruba/congnitive-complexity
```

For me, this is truly amazing! Few lines of code in a single Github Workflow file, and we've just made package available to the people, who need it the most.

<br>

I'm using this technique in 3 packages and it works perfectly so far. Try it to too, to **get the most ouf ot newest PHP while not keeping any of your users behind**.

<br>

Happy coding!
