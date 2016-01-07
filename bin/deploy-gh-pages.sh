#!/usr/bin/env bash

# Build settings
REPOSITORY=${REPOSITORY:-"https://${GH_TOKEN}@github.com/TomasVotruba/tomasvotruba.cz.git"}
BRANCH=${BRANCH:-"gh-pages"}
BUILD_DIR=${BUILD_DIR:-"build"}

# Git identity
GIT_AUTHOR_NAME=${GIT_AUTHOR_NAME:-"Travis"}
GIT_AUTHOR_EMAIL=${GIT_AUTHOR_EMAIL:-"travis@travis-ci.org"}

# Generate API
#git clone "${REPOSITORY}" "${BUILD_DIR}" --branch "${BRANCH}" --depth 1
./vendor/bin/sculpin generate --env=prod

## Commit & push
cd "${BUILD_DIR}/output_prod"
git init
git add .
git commit -m "Regenerated output"
git push origin "${BRANCH}" -f
