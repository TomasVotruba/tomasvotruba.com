# Statie Tweet Publisher

[![Build Status](https://img.shields.io/travis/TomasVotruba/StatieTweetPublisher/master.svg?style=flat-square)](https://travis-ci.org/TomasVotruba/StatieTweetPublisher)

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

### Setup Travis

- @todo
