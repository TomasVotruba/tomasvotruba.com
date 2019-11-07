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

2. Create `rector-ci.yaml` config just for code-reviews

```yaml
# rector-ci.yaml
parameters:
    sets:
        - "dead-code"
```

3. And add to your CI bash

```bash
vendor/bin/rector process src --config rector-ci.yaml --dry-run
```

## How to add Rector CI to your Favorite CI?

I've prepared a demo with PHP code and a testing pipeline for all widely used CI services.

- [Github + Travis CI](#1-github-travis-ci)
- [Gitlab CI](#2-gitlab-ci)
- [BitBucket](#3-bitbucket)

<br>

- ~~Github Actions~~
*Do you know how to work with Github Actions? Please let me know how would script look like, so we can complete the list.*

<br>

There you'll find all the configuration you need to **let your CI do code-reviews**.

### 1. Github + Travis CI

<br>

<a href="http://github.com/tomasvotruba/rector-ci-demo" class="btn btn-info">Repository</a>
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
                - vendor/bin/rector process --config rector-ci.yaml --dry-run
```

### 2. Gitlab CI

<br>

<a href="https://gitlab.com/TomasVotruba/rector-ci-demo" class="btn btn-info">Repository</a>
<a href="https://gitlab.com/TomasVotruba/rector-ci-demo/-/jobs/355280534#L197" class="btn btn-success ml-3">CI Feedback</a>

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
        - vendor/bin/rector process --config rector-ci.yaml --dry-run
```

### 3. Bitbucket

<br>

<a href="https://bitbucket.org/tomas-votruba/rector-ci-demo/src/master/" class="btn btn-info">Repository</a>
<a href="https://bitbucket.org/tomas-votruba/rector-ci-demo/addon/pipelines/home#!/results/3" class="btn btn-success ml-3">CI Feedback</a>

```yaml
# bitbucket-pipelines.yml
# see https://github.com/RobBrazier/docker-php-composer
image: robbrazier/php:7.2

pipelines:
    default:
        - step:
              name: "Build"
              script:
                  - composer install --prefer-dist --no-ansi --no-interaction --no-progress

              artifacts:
                  - build/**
                  - vendor/**
                  - composer.json # beacause of scripts
                  - composer

        # @see https://confluence.atlassian.com/bitbucket/parallel-steps-946606807.html
        - parallel:
              - step:
                    name: "Tests"
                    caches:
                        - composer
                    script:
                        - vendor/bin/phpunit

              # Run Rector CI
              - step:
                    name: "Code Review"
                    caches:
                        - composer
                    script:
                        - vendor/bin/rector process src --config rector-ci.yaml
```

## What sets to Start With?

Here are my favorite sets I apply first:

```yaml
# rector.yaml
parameters:
    sets:
        - 'coding-style'
        - 'code-quality'
        - 'dead-code'
        - 'nette-utils-code-quality'
```

But you don't have to stop there. Pick **any of [103 sets](https://github.com/rectorphp/rector/tree/master/config/set)** that Rector provides. E.g.:

```yaml
# rector.yaml
parameters:
    sets:
        - 'php70'
        - 'php71'
        - 'php72'
        - 'php73'
        - 'php74'
```

<br>
<br>

That's it!

Oh... one more thing. You don't have to resolve all the Rector reported flaws manually, **just remove the `--dry-run` option** and run it locally before pushing:

```bash
vendor/bin/rector process src --config rector-ci.yaml
```

<br>

Enjoy your coffee!


<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
