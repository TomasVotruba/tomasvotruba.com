---
id: 283
title: "How to Split Test Monorepo with Composer 2"
perex: |
    [Composer 2 was released](https://twitter.com/packagist/status/1319945203797708800) this week. It brings **massive `composer install/update` performance** improvement of [150-200 %](https://blog.packagist.com/composer-2-0-is-now-available).
    <br>
    <br>
    That means [faster feedback](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/) from CI and faster monorepo testing.
    <br>
    <br>
    Today, we'll look on how to use Composer in Github Actions with monorepo split testing and what to avoid.
tweet: "New Post on #php 🐘 blog: How to Split Test Monorepo with #composer v2"
---

*Note: following feature will be available in Symplify 9, that will be released [along with Symfony 5.2](/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/).*

Monorepo split testing is made easier by [`symplify/monorepo-builder` package](https://github.com/symplify/monorepo-builder). This is how it ["works" for Composer 1](/blog/2020/02/10/how-to-test-monorepo-after-split-before-actual-split/). Why "works"? There are 2 problems, that we fix during upgrade to Composer 2.

<br>

Let's say we have a `symplify/symplify` monorepo, where we develop all `symplify` package. We need to test all packages together **with their local version**, even in pull-request.

## 1. Installing Packages that needs Local version Package

We want to test `symplify/coding-standard` package:

- the `symplify/coding-standard` package needs `symplify/autowire-array-parameter` package
    - the `symplify/autowire-array-parameter` needs the `symplify/package-builder` package

In short:

- we're testing *a*
- *a* needs *b*
    - *b* needs *c*

<br>

This the result of localized package `composer install` on GitHub Action with Composer 1:

- Package *a* and *b* and used in local version <em class="fas fa-fw fa-check text-success fa-lg"></em>
- Package *c* is used from packagist <em class="fas fa-fw fa-times text-danger fa-lg"></em>

<img src="/assets/images/posts/2020/test_split_composer_2_require_3rd_package_fail.png" class="img-thumbnail">

What happens with changes of `symplify/package-builder` in this pull-request? **They're ignored**, and last stable version is used instead. <em class="fas fa-fw fa-times text-danger fa-lg"></em>

<br>

This is the same process, with **Composer 2**:

- Package *a* and *b* and used in local version <em class="fas fa-fw fa-check text-success fa-lg"></em>
- Package *c* is used in local version <em class="fas fa-fw fa-check text-success fa-lg"></em>

<img src="/assets/images/posts/2020/test_split_composer_2_require_3rd_package_fixed.png" class="img-thumbnail">


## 2. State Local packages as `dev-master`

So we upgrade to Composer 2 with Symplify 9 and that's it? No.

Local and GitHub Action development are different. Locally, Composer can see them as `dev-master` branch, which works with using `branch-alias`. But on GitHub Actoin (or your any favorite CI, the pull-request branch is `dev-<commit-hash>`).

<br>

The `composer install` will lead to a conflict with these 2 version:

<img src="/assets/images/posts/2020/test_split_composer_2_require_3rd_package_mess.png" class="img-thumbnail">

What now?

I asked on Composer repository and after less than hour of work got [a working solution with proper explanation](https://github.com/composer/composer/issues/9368#issuecomment-718198161). Thank you Jordi!

Trick is using `COMPOSER_ROOT_VERSION=dev-master` env, that will explicitly make version `dev-master` for all environments.

<br>

This is the final working GitHub Action (here you can see [it the action](https://github.com/symplify/symplify/blob/40dbc8005754254aee31316b9082826f30b51577/.github/workflows/split_tests.yaml)):

```yaml
name: Split Tests

on:
    pull_request: null

jobs:
    split_testing:
        runs-on: ubuntu-latest

        strategy:
            fail-fast: false
            matrix:
                package_name:
                    - coding-standard
                    # and all the other packages...

        name: Split Tests of ${{ matrix.package_name }}

        steps:
            -   uses: actions/checkout@v2

            -   uses: shivammathur/setup-php@v1
                with:
                    php-version: 7.4
                    coverage: none
                    tools: composer:v2

            -   run: composer install --no-progress --ansi

            # tell composer to use local package version
            -   run: vendor/bin/monorepo-builder localize-composer-paths packages/${{ matrix.package_name }}/composer.json --ansi

            -
                working-directory: packages/${{ matrix.package_name }}
                run: composer update --no-progress --ansi
                env:
                    # see https://github.com/composer/composer/issues/9368#issuecomment-718112361
                    COMPOSER_ROOT_VERSION: "dev-master"

            -
                working-directory: packages/${{ matrix.package_name }}
                run: vendor/bin/phpunit
```

<br>

Do you want to know more about this topic?

- [Issue on Composer](https://github.com/composer/composer/issues/9368)
- [Canonical Repositories](https://getcomposer.org/doc/articles/repository-priorities.md#canonical-repositories) and their [change in Composer 2](https://getcomposer.org/doc/articles/repository-priorities.md#default-behavior)

<br>

Happy coding!
