# Statie Tweet Publisher

Let Statie & Travis publish Tweets for your new posts fo you.

## Install

```bash
composer require tomasvotruba/statie-tweet-publisher:@dev --dev
```

## Configure

```yaml
# statie.yml
parameters:
    twitter_name: 'VotrubaT'
    source_directory: '%kernel.project_dir%/../../../source'
    minimal_gap_in_days: 1 # how many days to wait before publishing another Tweet
```

### Get Twitter Tokens

- @todo

### Setup Travis Online

- @todo

```bash
# .travis.yml
language: php

php:
    - 7.1

script:
    # tweets posts
    - if [[ $TRAVIS_BRANCH == "master" && $TRAVIS_PULL_REQUEST == "false" ]]; then packages/StatieTweetPublisher/bin/publish-new-tweet; fi
``` 
