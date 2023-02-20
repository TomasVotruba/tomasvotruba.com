---
id: 372
title: "How to release PHP 8.1 and 7.2 package in the Same Repository"
perex: |
    In a post from autumn, we looked at how to [develop packages in monorepo on PHP 8.1 and release a downgraded version on PHP 7.2](/blog/how-to-develop-sole-package-in-php81-and-downgrade-to-php72/).


    But having 2 repositories to work with still feels crappy. Which one should we use? Where do people contribute? Where do we report issues? Everyone needs clarification, and **time is wasted on explaining complexity**.
    <br>
    <br>
    I knew we could do better... we want **one repository**. Today I'll show you how to get there with 39 lines of GitHub Action workflow (including comments).
---

When I created minimal PHPStan packages with specialized rules, I wanted to use them on [every legacy project we upgrade](https://getrector.org/for-companies) we do with the Rector upgrade team. The most common lowest PHP for these projects is **PHP 7.2**, so it has to be downgraded.

<br>

A good package is a package that is:

* **simple to develop**
* and **easy to use**

<br>

Let's look at the package I published recently: [TomasVotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity)

## Prepare 2 Build Files

The build files are files that help us create the downgraded package.

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


The PHP 7.2 version is precisely the same, except it requires all the other PHP versions below:

```json
{
    // ...
     "require": {
        "php": "^7.2 || 8.0.*",
    }
}
```

The reason is to make package tags idempotent. There must be precisely one tag to choose from by the composer for any PHP version.

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

Check the files directly in the repository in the [`/build`](https://github.com/TomasVotruba/cognitive-complexity/tree/main/build) directory.

## Create GitHub Action Workflow

You already know how to downgrade the code Rector. The trick here is we **downgrade the code in the same repository**, just the `/src` directory, to be exact. Because this is the only code, people use when they get the package via composer.

<br>

Here is the 1st part of the workflow, which downgrades the code and applies the coding standard:

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

After GitHub runs this workflow, we have a PHP 7.2 downgraded code in the same repository. But how do **we release it so it is available via composer on PHP 7.2 as well**?

<br>

## 4 Lines of Gold

I've spent a few hours experimenting and consulting with amazing [Jan Kuchar](https://jankuchar.cz/), till we got the minimal script that handles the job. You're going to be surprised how simple this is:

```yaml
            # publish to the same repository with a new tag
            -
                name: "Tag Downgraded Code"
                run: |
                    git add --all
                    git commit -m "release PHP 7.2 downgraded version"
                    git tag "${GITHUB_REF#refs/tags/}.72"
                    git push origin "${GITHUB_REF#refs/tags/}.72"
```

<br>

### What each Lines does?

* 1st line - we add and commit PHP 7.2 files, the `--all` will handle the new files too
* 2nd line - we commit files (not pushed back yet)
* 3rd line - we create a new tag that adds a `.72` suffix to the original tag
* 4th line - we push this tag to the repository

<br>

That's it!

<br>

Check the [`workflows/downgraded_release.yaml`](https://github.com/TomasVotruba/cognitive-complexity/blob/main/.github/workflows/downgraded_release.yaml) in the GitHub repository.

### How does that look in Practise?

* I push tag 0.1.0. It is released on PHP 8.1 immediately
* The GitHub workflows notices, "oh, there is a new tag, I got work to do."
* The GitHub workflow downgrades the code to PHP 7.2, tags it, and pushes it back to the repository - just the tag
* Then 0.1.0.72 tag is released with PHP 7.2 code

## Install Anywhere

Now you can require the package on any of PHP 7.2-8.2 versions and get the correct code:

```bash
# php 7.2
composer require tomasvotruba/cognitive-complexity

# php 8.0
composer require tomasvotruba/cognitive-complexity

# php 8.1
composer require tomasvotruba/cognitive-complexity
```

For me, this is truly amazing! Few lines of code in a single Github Workflow file, and we've just made the package available to the people who need it the most.

<br>

I'm using this technique in 3 packages, and it works perfectly. Try it, too, to **get the most out of the newest PHP while not keeping any of your users behind**.

<br>

Happy coding!
