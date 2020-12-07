---
id: 289
title: "How to get a Dynamic PHP Version Matrix in GitHub Actions"
perex: |
    Do you want to run your tests on each PHP version you support? PHP 7.3, 7.4 and 8.0?
    Instead of 3 workflows with copy-paste steps, you can define **just one with a matrix for PHP versions**.
    <br><br>
    But PHP is released every year. The version constraints are already defined in `composer.json`.
    Hm, how could we use this knowledge to provide list of PHP version for a dynamic matrix?

tweet: "New Post on #php 🐘 blog: How to make a Dynamic PHP Version Matrix in GitHub Actions"
---

Do you know [memory locks](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/)? An "if this then that" code smell. E.g. you have to always put keys into your left pocket, after you lock the door of your office.

If we have tests, we want **them run on all PHP versions** we support. Just to be sure there are no PHP 8 features running on PHP 7.4.

<br>

**When** new PHP version [once a year](/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/), **then** we have to:

- update `composer.json`
    <img src="/assets/images/posts/2020/php-json-composer.png" class="img-thumbnail">

- update test workflow
    <img src="/assets/images/posts/2020/php-json-useless.png" class="img-thumbnail">

**This is completely useless operation** = double the work and double the maintenance. No gain, except fear of control.

## Memory Lock is Expensive

Could you guess how much time it took to send both commits? 2 minutes? 10 minutes?

<img src="/assets/images/posts/2020/php-json-time.png" class="img-thumbnail">

Almost **2 hours**. I had to send a new commit based on feedback in code-review. That's hidden cost of memory lock code smell.
Hidden cost in attention, work and [slow feedback loop](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/).

## Trust `composer.json`

What PHP version should be tested? The information is already in `composer.json`:

```json
{
    "require": {
        "php": "^7.2|^8.0"
    }
}
```

You're right, it's this list:

- 7.2
- 7.3
- 7.4
- 8.0

So **why should we duplicate** this information in GitHub Actions workflow config?

We can do better with [dynamic matrix](/blog/2020/11/16/how-to-make-dynamic-matrix-in-github-actions/).

## EasyCI to the Rescue

Symplify 9 is coming with a brand new package called [EasyCI](https://github.com/symplify/easy-ci). This package helps you with CI maintenance, like in our case above:

```bash
composer require symplify/easy-ci --dev

vendor/bin/easy-ci php-json
# "[7.2, 7.3, 7.4, 8.0]"
```

Now we have a problem, the command to provide JSON dynamic data and GitHub Actions. Let's put them together.

## Workflow with Dynamic PHP Versions

There are **2 steps** in our unit tests workflow:

- first provides the list of PHP version
- the other creates a dynamic job run with each PHP version

The first step:

```yaml
jobs:
    # first step
    provide_php_versions_json:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@v2

            -   uses: shivammathur/setup-php@v2
                with:
                    # this is the only place we have to use PHP, to avoid lock to bash scripting
                    php-version: 8.0

            -   run: composer install --no-progress --ansi

            # to see the provided output, just to be sure
            -   run: vendor/bin/easy-ci php-versions-json

            # here we create the json, we need the "id:" so we can use it in "outputs" bellow
            -
                id: output_data
                run: echo "::set-output name=matrix::$(vendor/bin/easy-ci php-versions-json)"

        # here, we save the result of this 1st phase to the "outputs"
        outputs:
            matrix: ${{ steps.output_data.outputs.matrix }}
```

The second step:

```yaml
    # continue from above
    unit_tests:
        needs: provide_php_versions_json

        runs-on: ubuntu-latest

        strategy:
            fail-fast: false
            matrix:
                php: ${{ fromJson(needs.provide_php_versions_json.outputs.matrix) }}

        # continue with tests
        steps:
            # ...
            -   run: vendor/bin/phpunit
```

<br>

Here is full [`.github/workflows/unit_tests.yaml`](https://github.com/symplify/symplify/blob/aeb8e03dfb2948474f5a7d267ab05541ee00d90b/.github/workflows/unit_tests.yaml) to explore.

<br>

Happy coding!
