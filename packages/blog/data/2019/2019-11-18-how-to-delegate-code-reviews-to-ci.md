---
id: 226
title: "How to Delegate Code&nbsp;Reviews&nbsp;to&nbsp;CI"
perex: |
    Are you doing code reviews? No? Yes?
    <br>
    <br>
    **In both cases, you won't have too**. Just add a couple of YAML lines to your CI.

tweet: "New Post on #php 🐘 blog: How to Delegate Code Reviews to CI"
tweet_image: "/assets/images/posts/2019/rector-ci-code-review/result.png"

updated_since: "December 2020"
updated_message: |
    The Bitbucket was dropped, as unused services and the demo repository is not maintained.
---

I'm very grateful that Rector is getting traction lately. More and more PHP developers save dozens of hours by running simple CLI commands on their codebases.

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Just upgraded <a href="https://twitter.com/phpunit?ref_src=twsrc%5Etfw">@phpunit</a> from 4.0 to 8.4 with <a href="https://twitter.com/rectorphp?ref_src=twsrc%5Etfw">@rectorphp</a> in 3 seconds with one command. It&#39;s worth knowing this tool. Thanks <a href="https://twitter.com/VotrubaT?ref_src=twsrc%5Etfw">@VotrubaT</a> 👏 <a href="https://t.co/o3ESvJRsJ7">pic.twitter.com/o3ESvJRsJ7</a></p>&mdash; Arkadiusz Kondas (@ArkadiuszKondas) <a href="https://twitter.com/ArkadiuszKondas/status/1196349896690950144?ref_src=twsrc%5Etfw">November 18, 2019</a></blockquote>

<br>

It's a lot. But you want more laziness, right?

<br>

**Rector can do much more without you even running it**.

## What do you Review in Code?

If you do code-review, what do you mostly do?

1. **Look for patterns**, explain them, report them everywhere or just somewhere and hope the other person will fix them all.

2. **Discuss the design** and why and what should be done about it.

Well, the 2nd cannot be automated, unless you're able to put it in clear step-by-step, e.g SOLID laws in procedural code. So you still have to do that, sorry.

## Dead Fat Code

But 1st step can be easily automated. How? Well, let's take a dead code for example.
In the last [project I've helped to improve with Rector](/blog/2019/07/29/how-we-completed-thousands-of-missing-var-annotations-in-a-day/), there were 2 years of dead-code piled up. A dead that you have to:

- test
- maintain
- debug if broken
- review if changed anything related to it

**So many human-hours wasted**. In the end, we **removed over 12 % of "dead fat code"**. Wouldn't it be better if that fat would never be got it?

## Add Rector in CI in 3 Steps

1. Install Rector

```bash
composer require rector/rector --dev
```

2. Create `rector-ci.php` config just for code-reviews

```php
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [SetList::DEAD_CODE]);
};
```

3. And add to your CI bash

```bash
vendor/bin/rector process src --config rector-ci.php --dry-run
```

## How to add Rector CI to your Favorite CI?

I've prepared a demo with PHP code and a testing pipeline for all widely used CI services.

- [GitHub Actions](#1-github-actions)
- [GitHub + Travis CI](#2-github-travis-ci)
- [Gitlab CI](#3-gitlab-ci)

<br>

There you'll find all the configuration you need to **let your CI do code-reviews**.

### 1. GitHub Actions

<br>

<a href="https://github.com/tomasvotruba/rector-ci-demo" class="btn btn-info">Repository</a>

```yaml
# .github/workflows/php.yml
name: Rector Code Review

on: [push]

jobs:

    build:
        runs-on: ubuntu-latest

        steps:
        - uses: actions/checkout@v1

        - name: Validate composer.json and composer.lock
          run: composer validate

        - name: Install dependencies
          run: composer install --prefer-dist --no-progress --no-suggest

        - name: Code Review
          run: ./vendor/bin/rector process --config rector-ci.php --dry-run
```

### 2. GitHub + Travis CI

<br>

<a href="https://github.com/tomasvotruba/rector-ci-demo" class="btn btn-info">Repository</a>
<a href="https://travis-ci.com/TomasVotruba/rector-ci-demo/jobs/258286278#L320"  class="btn btn-success ml-3">CI Feedback</a>

```yaml
# .travis.yml
os: linux
language: php

php:
    - '7.2'

install:
    # install composer dependencies for all stages
    - composer install --prefer-dist --no-ansi --no-interaction --no-progress

jobs:
    include:
        -
            stage: test
            name: "Tests"
            script:
                - vendor/bin/phpunit

        -
            stage: test
            name: "Code Review"
            script:
                - vendor/bin/rector process --config rector-ci.php --dry-run
```

<br>

### 3. Gitlab CI

```yaml
# .gitlab-ci.yml
# inspired from https://docs.gitlab.com/ee/ci/examples/php.html

# see https://github.com/RobBrazier/docker-php-composer
image: robbrazier/php:7.2

before_script:
    # install composer dependencies for all stages
    - composer install --prefer-dist --no-ansi --no-interaction --no-progress

stages:
    - test

tests:
    stage: test
    script:
        - vendor/bin/phpunit

code-review:
    stage: test
    script:
        - vendor/bin/rector process --config rector-ci.php --dry-run
```

## What sets to Start With?

Here are my favorite sets I apply first:

```php
<?php

// rector.php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [
        SetList::CODING_STYLE,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::NETTE_UTILS_CODE_QUALITY,
    ]);
};
```

But you don't have to stop there. Pick **any of [100+ sets](https://github.com/rectorphp/rector/tree/master/config/set)** that Rector provides.

<br>
<br>

That's it!

Oh... one more thing. You don't have to resolve all the Rector reported flaws manually, **just remove the `--dry-run` option** and run it locally before pushing:

```bash
vendor/bin/rector process src --config rector-ci.php
```

<br>

Enjoy your coffee!

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
