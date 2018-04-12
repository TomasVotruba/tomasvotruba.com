# Statie Tweet Publisher

Let Statie & Travis publish Tweets for your new posts fo you.

## Install

```bash
composer require tomasvotruba/statie-tweet-publisher:@dev --dev
```

## Configure

```yaml
# statie.yml
imports:
    - { resource: 'vendor/tomasvotruba/statie-tweet-publisher/src/config/config.yml' }

parameters:
    twitter_name: 'VotrubaT'
    source_directory: '%kernel.project_dir%/../../../source'
    # set 0 for testing
    minimal_gap_in_days: 1 # how many days to wait before publishing another Tweet
```

### Get Twitter Tokens

- Go to [https://apps.twitter.com/app/new](https://apps.twitter.com/app/new)
- Login under account you want to publish in and create new Application
- Then go to "Keys and Access Tokens"
- In the bottom click to "Create my access token"

**Now the secret part, be careful about your keys!**

- Add `config/config.local.yml` to `.gitignore`
- Copy these 4 hashes you see in the page to `config/config.local.yml`

    ```yaml
    # config/config.local.yml
    parameters:
        # for tomasvotruba/statie-tweet-publisher package locally
        twitter_consumer_key: "..."
        twitter_consumer_secret: "..."
        twitter_oauth_access_token: "..."
        twitter_oauth_access_token_secret: "..."
    ```

- Import this file in `statie.yml` **under the package config**, so it has bigger priority and you can test it locally

    ```yaml
    # statie.yml
    imports:
        - { resource: 'vendor/tomasvotruba/statie-tweet-publisher/src/config/config.yml' }
        # enabled on localhost only
        - { resource: 'config/config.local.yml', ignore_errors: true }
    ```

- Add `tweet: "some tweet"` to headline of your post to test it
- Run `vendor/bin/publish-new-tweet` and check your Twitter account

Is it there? Good, it works and only few steps remain to fully automate this :)

### Setup Travis Online

Now we only put that logic on Travis and we're done.

- Open Travis for your repository, e.g. [https://travis-ci.org/TomasVotruba/tomasvotruba.cz](https://travis-ci.org/TomasVotruba/tomasvotruba.cz)
- Got to *More options* => *Settings*
- In *Environment Variables* add 4 variables with they values. They are hidden by default, so don't worry:
    - `TWITTER_CONSUMER_KEY`
    - `TWITTER_CONSUMER_SECRET`
    - `TWITTER_OAUTH_ACCESS_TOKEN`
    - `TWITTER_OAUTH_ACCESS_TOKEN_SECRET`
- Then setup cron, so posts are being published even if you don't write and have a break.
- Go to *Cron Jobs* → `master` branch → *Daily* → *Always run* → Add

That its!

- And let `.travis.yml` know, that he should publish it

    ```yaml
    # .travis.yml
    language: php

    matrix:
        include:
            - php: 7.1
              env: TWEET=1

    script:
        # tweets posts
        - if [[ $TRAVIS_BRANCH == "master" && $TRAVIS_PULL_REQUEST == "false" && $TWEET != "" ]]; then vendor/bin/publish-new-tweet; fi
    ```

Now you can [quit Twitter](https://www.tomasvotruba.cz/blog/2017/01/20/4-emotional-reasons-why-I-quit-my-twitter/) if you want and you posts will be still there :)
