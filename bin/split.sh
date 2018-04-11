#!/usr/bin/env bash
git subsplit init .git

LAST_TAG="$(git tag -l  --sort=committerdate | tail -n1)"
HEADS="$(git branch | grep \* | cut -d ' ' -f2)"

git subsplit publish --heads=$HEADS --tags=$LAST_TAG packages/StatieTweetPublisher:git@github.com:tomasvotruba/statie-tweet-publisher.git

rm -rf .subsplit

# inspired by laravel: https://github.com/laravel/framework/blob/5.4/build/illuminate-split-full.sh
# they use SensioLabs now though: https://github.com/laravel/framework/pull/17048#issuecomment-269915319
