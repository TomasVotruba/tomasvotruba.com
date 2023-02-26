---
id: 154
title: "7 Tips to Get the Most out of Travis CI"
perex:
    Travis CI is the most spread CI in checking open-source projects.


    Do you want to know how to use it **3x faster**?
    <br>
    How to make Travis **generate code for you**?
    <br>
    And how to make your **tokens safe**?
---

## 1. Skip x-debug

Xdebug makes everything much slower in exchange for deep analysis. It's useful**only for code coverage** in CI.

Turn it off to get faster:

```yaml
before_script:
    - phpenv config-rm xdebug.ini || return 0
```

Do you use coverage? Just use condition:

```yaml
before_script:
    # disable xdebug if not coverage
    - if [[ $COVERAGE == "" ]]; then phpenv config-rm xdebug.ini; fi
```

## 2. Deliver PR Checks Fast

The speed of feedback loop in PRs has the same effect as page load time. If the page is loading **more than 4 seconds**, [most people leaves thinking it's broken](https://www.hobo-web.co.uk/your-website-design-should-load-in-4-seconds).

What is **must-have** in PR check?

- tests
- static analysis
- coding style

What can be **checked later**?

- code coverage
- deploy
- documentation build

When tests, static analysis, and coding style can finish in 3 minutes including `composer install`, the code coverage and deploy could prolong waiting to **9 minutes**. For no added value, because it should be performed *on merge*.

Would you contribute to PR where you **wait 9 minutes or 3 to know it passed**?

For these reasons, there is `$TRAVIS_BRANCH` ENV var:

```yaml
after_script:
    - |
      if [[ $COVERAGE == true && $TRAVIS_BRANCH == "master" ]]; then
        vendor/bin/phpunit --coverage-clover coverage.xml
        wget https://github.com/php-coveralls/php-coveralls/releases/download/v2.1.0/php-coveralls.phar
        php php-coveralls.phar --verbose
      fi
```

This way coverage is run just once the PR merged. Imagine the safe process time and human nerves in PR where were additional 10 commits after code-review.

## 3. Use ENV vars

ENV vars is a standard approach to set secure values, pass options to containers and PHP application. It's a trend Symfony pushes every version, recently with this [Symfony 4.2 deprecation](https://symfony.com/blog/new-in-symfony-4-2-important-deprecations):

```diff
-php bin/console command_name --env=test --no-debug
+APP_ENV=test APP_DEBUG=0 php bin/console command_name
```

How to use them?

```yaml
language: php

matrix:
    include:
      - php: 7.2
        env: STATIC_ANALYSIS=true
      - php: 7.2

script:
    - vendor/bin/phpunit

    - |
      if [[ $STATIC_ANALYSIS == true ]]; then
        vendor/bin/ecs check src
        vendor/bin/phpstan analyze src --level max
      fi
```

Travis also allows [Stages](https://docs.travis-ci.com/user/build-stages) oppose ENV variables. I've tried that and it has much more complicated YAML syntax, than just `VAR=value`. Also due to ENV trends lead by Docker, containers in general and Symfony, I'd stick with ENV vars for open-source projects.

**For private projects stages are great** since you need to deploy migrations and the code itself. But for private projects, I think Gitlab CI is a much more valuable option. Ask [Jan Mikeš](https://janmikes.cz) about that.

## 4. Use Travis to do More Just Watching

Most projects use Travis to check tests and analyze code. But did you know you can also use it for **open-source deploy**? And even more:

- **running validations**, e.g. if all links are still responding with 200
- **crawl websites** and storing data to YAML
- **Tweet**!

```yaml
script:
    # make sure there are no duplicates
    - bin/console validate-groups

    # import data and genearte YAML
    - bin/console import

    # generate website to "/output" directory
    - vendor/bin/statie generate source
```

Since filesystem exists as long as the container is running, you can download, dump and work with almost any data in it.

One more thing: **Travis is super fast.** What on my laptop takes 10 minutes, it can solve in 2.

## 5. Rebuild your GitHub Pages Daily

One of the sexy features of Travis are [Cron Jobs](https://docs.travis-ci.com/user/cron-jobs) in combination with Github Pages and `deploy`:

It is this easy to [deploy Statie website](https://www.statie.org/docs/github-pages/#configure-travis) to Github Pages:

```yaml
language: php

php: 7.2

install:
    - composer install

script:
    - vendor/bin/statie generate source

deploy:
    provider: pages
    skip_cleanup: true
    github_token: $GITHUB_TOKEN
    local_dir: output
    on:
        branch: master
```

Check real life `.travis.yml` to getter better idea:

- [pehapkari.cz](https://github.com/pehapkari/pehapkari.cz/blob/7cb58f17cedffc8222d063d632b7d353c7728342/.travis.yml#L35-L41)
- [friendsofphp.org](https://github.com/TomasVotruba/friendsofphp.org/blob/76f64d0fa48633abcd4e256eb575f8d99ba1d78b/.travis.yml#L24-L40)

## 6. Stay Secure

When I said you can **Tweet** with your Travis, I mean it. That's what me lazy bastard [does](https://github.com/TomasVotruba/tomasvotruba.com/tree/master/packages/StatieTweetPublisher) on this blog:

```yaml
after_deploy:
    - |
      if [[ $TRAVIS_BRANCH == "master" && $TRAVIS_PULL_REQUEST == "false" ]]; then
        packages/StatieTweetPublisher/bin/publish-new-tweet
      fi
```

You may think "that not secure, bro", and you're right! To be able to tweet Travis needs to know the auth tokens that Twitter generates for you:

```yaml
language: php

env:
    - TWITTER_CONSUMER_KEY="asd0830GA709GA"
    - TWITTER_CONSUMER_SECRET="asd0830GA709GA"
    - TWITTER_OAUTH_ACCESS_TOKEN="asd0830GA709GA"
    - TWITTER_OAUTH_ACCESS_TOKEN_SECRET="asd0830GA709GA"
    # they're all fake, don't even try it!
```

This could work, but then everyone would see it.

**How to do it in secret?**

You can actually **enter them manually** in Travis administration of your repository - [here is the tutorial](https://github.com/TomasVotruba/tomasvotruba.com/tree/master/packages/StatieTweetPublisher#setup-travis-online).

But still, what if someone will send you following PR:

```yaml
script:
    - echo $TWITTER_CONSUMER_KEY
    - echo $TWITTER_CONSUMER_SECRET
    - echo $TWITTER_OAUTH_ACCESS_TOKEN
    - echo $TWITTER_OAUTH_ACCESS_TOKEN_SECRET
```

That's not nice and you should not do that to your friends! No, really, it a good way of thinking how to hack - good job.

Travis thought about this:

- first, they will be displayed as `**secret**` everywhere in the code

But what if you try:

```php
$secret = getenv('TWITTER_CONSUMER_KEY');
for ($i = 0; $i < strlen($secret); ++$i) {
    echo $secret[$i] . PHP_EOL;
}
```

Well, that still won't work (I tried that), since these variable **are available only for the repository owner**.

But if you as an owner do echo it like this PHP script, you're screwed :).

## 7. Make use of Composer Scripts

Did you know you can define your own composer scripts and run them with `composer <name>`? If not, check [Have you tried Composer Scripts? You may not need Phing](https://blog.martinhujer.cz/have-you-tried-composer-scripts) that explains it in a very practical way.

So instead of writing it manually in Travis and locally and just waiting for a typo or miss use of invalid config:

```yaml
script:
    # static analysis
    - vendor/bin/ecs check bin src test packages
    - vendor/bin/phpstan analyze packages bin src tests packages --level max
```

you can actually use these in Travis as well:

```yaml
script:
    # static analysis
    - composer check-cs
    - composer phpstan
```

Anyone who needs to debug and know what's behind these shortcuts can just open `composer.json`:

```json
{
    "scripts": {
        "check-cs": "vendor/bin/ecs check bin src tests packages",
        "phpstan": "vendor/bin/phpstan analyse bin src tests packages --level max"
    }
}
```

<br>

Did I miss some tip you use every day or do you know a better one? **Share in comments!**
