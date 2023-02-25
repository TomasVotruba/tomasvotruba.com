---
id: 261
title: "How to Speedup Test&nbsp;Coverage on Travis by&nbsp;95&nbsp;%"
perex: |
    It was late in the night. He was looking at CI builds to make sure everything is ready for a morning presentation.

    **The build took over 45 minutes**. What was wrong? He was scared, took a deep breath, and looked at Travis build detail anyway.

    "What? Code coverage? All the stress for this?"
    <br>
    **"We should remove it,"** he thought, "CI should give fast feedback... or is there another way?"

tweet_image: "/assets/images/posts/2019/faster-coverage/coverage_fast.png"
---

Do you find this story resembling your daily job? We had the same problem. We tolerated for 2 years, but in 2020 we looked for a better way.

<img src="/assets/images/posts/2019/faster-coverage/coverage_slow.png">

## Status Quo: Xdebug

The most common way in the open-source nowadays is Xdebug with Coveralls. [Coveralls.io](http://coveralls.io) is an open-source, free service, that consumes your PHPUnit coverage data, and turns them into one significant number.

That's how can have sexy coverage badge in your repository:

<img src="https://img.shields.io/coveralls/symplify/symplify/master.svg?style=flat-square">

How do we make it happen on Travis?

```yaml
script:
    - vendor/bin/phpunit --coverage-clover coverage.xml
```

In the job context:

```yaml
# .travis.yml
jobs:
    include:
        -
            stage: coverage
            php: 7.3
            name: Test Coverage
            script:
                - vendor/bin/phpunit --coverage-clover coverage.xml
                # Coveralls.io
                - wget https://github.com/php-coveralls/php-coveralls/releases/download/v2.1.0/php-coveralls.phar
                - php php-coveralls.phar --verbose
```

## 2. Faster with phpdbg

I've learned about phpdbg from this [short and clear post by KIZU 514](https://kizu514.com/blog/phpdbg-is-much-faster-than-xdebug-for-code-coverage).

One-line, no-install setup:

```yaml
script:
    - phpdbg -qrr -d memory_limit=-1 vendor/bin/phpunit --coverage-clover coverage.xml
```

In full job:

```yaml
# .travis.yml
jobs:
    include:
        -
            stage: coverage
            php: 7.3
            name: Test Coverage
            script:
                - phpdbg -qrr -d memory_limit=-1 vendor/bin/phpunit --coverage-clover coverage.xml
                # Coveralls.io
                - wget https://github.com/php-coveralls/php-coveralls/releases/download/v2.1.0/php-coveralls.phar
                - php php-coveralls.phar --verbose
```

**Mind the `-d memory_limit=-1`.** The memory was exhausted very soon. We would care if this was a production code. But CI is build, run and throw away container, so allowing unlimited memory is ok.


## 3. Even Faster with PCOV

It's better to have PHPUnit 8+, but what if [don't have it yet](/blog/2019/11/04/still-on-phpunit-4-come-to-phpunit-8-together-in-a-day/)? You can [read about PCOV here](https://kizu514.com/blog/pcov-is-better-than-phpdbg-and-xdebug-for-code-coverage), we'll get right to the business.

2-lines run with setup:

```yaml
script:
    - pecl install pcov
    - vendor/bin/phpunit --coverage-clover coverage.xml
```

In jobs context:

```yaml
# .travis.yml
jobs:
    include:
        -
            stage: coverage
            php: 7.3
            name: Test Coverage
            script:
                - pecl install pcov
                - vendor/bin/phpunit --coverage-clover coverage.xml
                # Coveralls.io
                - wget https://github.com/php-coveralls/php-coveralls/releases/download/v2.1.0/php-coveralls.phar
                - php php-coveralls.phar --verbose
```

**PCOV took only 1,5 minutes**, that's great!

The coverage number changed from 77 % to 73 %. However, [PCOV provides the higher accuracy than phpdbg](https://github.com/krakjoe/pcov#differences-in-reporting) which cannot correctly detect implicit return paths.


## Final Results

- Xdebug - 37 minutes, 77,5 % code coverage
- phpdbg - 3 minutes, 77,1 % code coverage
- pcov - 1,5 minutes, 73 % code coverage

...and the winner is:

<br>

**PCOV** 🎉

<br>

It was the fastest one, while also providing code analysis similar to the mainstream Xdebug.

<br>

But that was *our specific* codebase. Be sure to try option 2. and 3. on your code, in one PR, to see **what suits you**.

## The Future?

Derrick, the Xdebug author, [wrote about Xdebug 2.9](https://derickrethans.nl/crafty-code-coverage.html) that should speed up 22 mins build into **1.2 min**.

It [might take some time to get it on Travis](https://travis-ci.community/t/new-faster-xdebug-2-9-is-out/6372), which has nov Xdebug 2.7.

We'll see :)

<br>

Happy coding!
