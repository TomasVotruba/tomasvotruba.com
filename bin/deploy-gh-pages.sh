#!/usr/bin/env bash

# Build settings
REPOSITORY=${REPOSITORY:-"https://${GH_TOKEN}@github.com/TomasVotruba/tomasvotruba.cz.git"}

# Git identity
GIT_AUTHOR_NAME=${GIT_AUTHOR_NAME:-"Travis"}
GIT_AUTHOR_EMAIL=${GIT_AUTHOR_EMAIL:-"travis@travis-ci.org"}

# Generate API
./vendor/bin/sculpin generate --env=prod

# Commit & push
cd "./output_prod"
git init
git commit -am "Regenerated output"
git push --force --quiet "${REPOSITORY}" master:gh-pages > /dev/null 2>&1
